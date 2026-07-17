<?php

namespace Tests\Feature;

use App\Enums\OrganizationalScopeLevel;
use App\Enums\OutletStatus;
use App\Models\BusinessEntity;
use App\Models\Cluster;
use App\Models\Division;
use App\Models\Outlet;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use App\Support\OrganizationalDataScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationalDataScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_cluster_scope_only_returns_outlets_in_assigned_clusters(): void
    {
        [$firstOutlet, $firstCluster] = $this->createOutletHierarchy('A');
        [$secondOutlet] = $this->createOutletHierarchy('B');
        $user = $this->userWithScope(OrganizationalScopeLevel::Cluster);
        $user->clusters()->attach($firstCluster);

        $visibleOutletIds = app(OrganizationalDataScope::class)
            ->apply(Outlet::query(), $user)
            ->pluck('id');

        $this->assertTrue($visibleOutletIds->contains($firstOutlet->id));
        $this->assertFalse($visibleOutletIds->contains($secondOutlet->id));
    }

    public function test_division_scope_returns_all_descendants_of_assigned_division(): void
    {
        [$firstOutlet, , $firstDivision] = $this->createOutletHierarchy('A');
        [$secondOutlet, , $secondDivision] = $this->createOutletHierarchy('B');
        $user = $this->userWithScope(OrganizationalScopeLevel::Divisi);
        $user->divisions()->attach($firstDivision);

        $visibleOutletIds = app(OrganizationalDataScope::class)
            ->apply(Outlet::query(), $user)
            ->pluck('id');

        $this->assertTrue($visibleOutletIds->contains($firstOutlet->id));
        $this->assertFalse($visibleOutletIds->contains($secondOutlet->id));
        $this->assertNotSame($firstDivision->id, $secondDivision->id);
    }

    public function test_all_scope_does_not_require_an_organizational_anchor(): void
    {
        [$firstOutlet] = $this->createOutletHierarchy('A');
        [$secondOutlet] = $this->createOutletHierarchy('B');
        $user = $this->userWithScope(OrganizationalScopeLevel::All);

        $visibleOutletIds = app(OrganizationalDataScope::class)
            ->apply(Outlet::query(), $user)
            ->pluck('id');

        $this->assertEqualsCanonicalizing(
            [$firstOutlet->id, $secondOutlet->id],
            $visibleOutletIds->all(),
        );
    }

    /**
     * @return array{Outlet, Cluster, Division}
     */
    private function createOutletHierarchy(string $suffix): array
    {
        $businessEntity = BusinessEntity::factory()->create([
            'code' => "BU-{$suffix}",
            'name' => "Badan Usaha {$suffix}",
        ]);
        $division = Division::factory()->create([
            'code' => "DIV-{$suffix}",
            'name' => "Divisi {$suffix}",
            'business_entity_id' => $businessEntity->id,
        ]);
        $region = Region::factory()->create([
            'code' => "REG-{$suffix}",
            'name' => "Region {$suffix}",
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
        ]);
        $cluster = Cluster::factory()->create([
            'code' => "CLS-{$suffix}",
            'name' => "Cluster {$suffix}",
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
            'region_id' => $region->id,
        ]);
        $outlet = Outlet::factory()->create([
            'name' => "Outlet {$suffix}",
            'address' => "Alamat {$suffix}",
            'owner_name' => "Pemilik {$suffix}",
            'phone_number' => "080000000{$suffix}",
            'business_entity_id' => $businessEntity->id,
            'division_id' => $division->id,
            'region_id' => $region->id,
            'cluster_id' => $cluster->id,
            'status' => OutletStatus::Maintain,
        ]);

        return [$outlet, $cluster, $division];
    }

    private function userWithScope(OrganizationalScopeLevel $level): User
    {
        $role = Role::factory()->create([
            'organizational_scope_level' => $level,
        ]);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
