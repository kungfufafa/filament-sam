<?php

namespace App\Support;

use App\Enums\OrganizationalScopeLevel;
use App\Models\BusinessEntity;
use App\Models\Cluster;
use App\Models\Division;
use App\Models\Outlet;
use App\Models\OutletChangeArchive;
use App\Models\OutletRegistration;
use App\Models\PlanVisit;
use App\Models\Region;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;

class OrganizationalDataScope
{
    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function apply(Builder $query, User $user): Builder
    {
        $level = $this->resolveLevel($user);

        if ($level === OrganizationalScopeLevel::All) {
            return $query;
        }

        if ($level === null) {
            return $query->whereRaw('1 = 0');
        }

        $coordinates = $this->coordinates($user, $level);

        if ($coordinates[$level->value] === []) {
            return $query->whereRaw('1 = 0');
        }

        return $this->applyCoordinates($query, $level, $coordinates);
    }

    public function resolveLevel(User $user): ?OrganizationalScopeLevel
    {
        return $user->roles()
            ->get(['organizational_scope_level'])
            ->pluck('organizational_scope_level')
            ->filter(fn (mixed $level): bool => $level instanceof OrganizationalScopeLevel)
            ->sortBy(fn (OrganizationalScopeLevel $level): int => $level->priority())
            ->first();
    }

    /**
     * @return array{business_entity: list<int>, division: list<int>, region: list<int>, cluster: list<int>}
     */
    private function coordinates(User $user, OrganizationalScopeLevel $level): array
    {
        $coordinates = [
            'business_entity' => [],
            'division' => [],
            'region' => [],
            'cluster' => [],
        ];

        if ($level === OrganizationalScopeLevel::BusinessEntity) {
            $coordinates['business_entity'] = $user->businessEntities()->pluck('business_entities.id')->all();
        }

        if ($level === OrganizationalScopeLevel::Divisi) {
            $divisions = $user->divisions()->get(['divisions.id', 'business_entity_id']);
            $coordinates['business_entity'] = $divisions->pluck('business_entity_id')->unique()->values()->all();
            $coordinates['division'] = $divisions->pluck('id')->all();
        }

        if ($level === OrganizationalScopeLevel::Region) {
            $regions = $user->regions()->get(['regions.id', 'business_entity_id', 'division_id']);
            $coordinates['business_entity'] = $regions->pluck('business_entity_id')->unique()->values()->all();
            $coordinates['division'] = $regions->pluck('division_id')->unique()->values()->all();
            $coordinates['region'] = $regions->pluck('id')->all();
        }

        if ($level === OrganizationalScopeLevel::Cluster) {
            $clusters = $user->clusters()->get(['clusters.id', 'business_entity_id', 'division_id', 'region_id']);
            $coordinates['business_entity'] = $clusters->pluck('business_entity_id')->unique()->values()->all();
            $coordinates['division'] = $clusters->pluck('division_id')->unique()->values()->all();
            $coordinates['region'] = $clusters->pluck('region_id')->filter()->unique()->values()->all();
            $coordinates['cluster'] = $clusters->pluck('id')->all();
        }

        return $coordinates;
    }

    /**
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  Builder<TModel>  $query
     * @param  array{business_entity: list<int>, division: list<int>, region: list<int>, cluster: list<int>}  $coordinates
     * @return Builder<TModel>
     */
    private function applyCoordinates(
        Builder $query,
        OrganizationalScopeLevel $level,
        array $coordinates,
    ): Builder {
        $modelClass = $query->getModel()::class;
        $column = $this->columnForLevel($level);
        $allowedIds = $coordinates[$level->value];

        return match ($modelClass) {
            BusinessEntity::class => $query->whereKey($coordinates['business_entity']),
            Division::class => $query->whereIn(
                $level === OrganizationalScopeLevel::BusinessEntity ? 'business_entity_id' : 'id',
                $level === OrganizationalScopeLevel::BusinessEntity ? $coordinates['business_entity'] : $coordinates['division'],
            ),
            Region::class => $query->whereIn(
                match ($level) {
                    OrganizationalScopeLevel::BusinessEntity => 'business_entity_id',
                    OrganizationalScopeLevel::Divisi => 'division_id',
                    default => 'id',
                },
                match ($level) {
                    OrganizationalScopeLevel::BusinessEntity => $coordinates['business_entity'],
                    OrganizationalScopeLevel::Divisi => $coordinates['division'],
                    default => $coordinates['region'],
                },
            ),
            Cluster::class => $query->whereIn($column, $allowedIds),
            Outlet::class, OutletRegistration::class => $query->whereIn($column, $allowedIds),
            User::class => $query->whereHas($this->userRelationshipForLevel($level), function (Builder $anchorQuery) use ($allowedIds): void {
                $anchorQuery->whereKey($allowedIds);
            }),
            Visit::class, PlanVisit::class => $query->whereHasMorph(
                'visitable',
                [Outlet::class, OutletRegistration::class],
                function (Builder $visitableQuery) use ($column, $allowedIds): void {
                    $visitableQuery->whereIn($column, $allowedIds);
                },
            ),
            OutletChangeArchive::class => $query->whereHas(
                'outlet',
                fn (Builder $outletQuery): Builder => $outletQuery->whereIn($column, $allowedIds),
            ),
            SystemSetting::class => $query->where(function (Builder $settingsQuery) use ($level, $allowedIds): void {
                $settingsQuery
                    ->where('scope_level', 'global')
                    ->orWhere(function (Builder $levelQuery) use ($level, $allowedIds): void {
                        $levelQuery
                            ->where('scope_level', $level->value === 'division' ? 'division' : $level->value)
                            ->whereIn($this->systemSettingColumnForLevel($level), $allowedIds);
                    });
            }),
            default => $query->whereRaw('1 = 0'),
        };
    }

    private function columnForLevel(OrganizationalScopeLevel $level): string
    {
        return match ($level) {
            OrganizationalScopeLevel::BusinessEntity => 'business_entity_id',
            OrganizationalScopeLevel::Divisi => 'division_id',
            OrganizationalScopeLevel::Region => 'region_id',
            OrganizationalScopeLevel::Cluster => 'cluster_id',
            OrganizationalScopeLevel::All => 'id',
        };
    }

    private function systemSettingColumnForLevel(OrganizationalScopeLevel $level): string
    {
        return $level === OrganizationalScopeLevel::Divisi
            ? 'division_id'
            : $this->columnForLevel($level);
    }

    private function userRelationshipForLevel(OrganizationalScopeLevel $level): string
    {
        return match ($level) {
            OrganizationalScopeLevel::BusinessEntity => 'businessEntities',
            OrganizationalScopeLevel::Divisi => 'divisions',
            OrganizationalScopeLevel::Region => 'regions',
            OrganizationalScopeLevel::Cluster => 'clusters',
            OrganizationalScopeLevel::All => 'roles',
        };
    }
}
