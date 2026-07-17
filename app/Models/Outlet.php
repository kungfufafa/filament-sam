<?php

namespace App\Models;

use App\Enums\OutletStatus;
use Database\Factories\OutletFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    /** @use HasFactory<OutletFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => OutletStatus::class,
        ];
    }

    /**
     * @return BelongsTo<BusinessEntity, $this>
     */
    public function businessEntity(): BelongsTo
    {
        return $this->belongsTo(BusinessEntity::class, 'business_entity_id');
    }

    /**
     * @return BelongsTo<Division, $this>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * @return BelongsTo<Cluster, $this>
     */
    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }

    /**
     * @return BelongsTo<OutletRegistration, $this>
     */
    public function outletRegistration(): BelongsTo
    {
        return $this->belongsTo(OutletRegistration::class, 'outlet_registration_id');
    }

    /**
     * @return MorphMany<Visit, $this>
     */
    public function visits(): MorphMany
    {
        return $this->morphMany(Visit::class, 'visitable');
    }

    /**
     * @return MorphMany<PlanVisit, $this>
     */
    public function planVisits(): MorphMany
    {
        return $this->morphMany(PlanVisit::class, 'visitable');
    }

    /**
     * @return HasMany<OutletGeotag, $this>
     */
    public function geotags(): HasMany
    {
        return $this->hasMany(OutletGeotag::class);
    }

    /**
     * @return HasMany<OutletChangeArchive, $this>
     */
    public function archives(): HasMany
    {
        return $this->hasMany(OutletChangeArchive::class, 'outlet_id');
    }
}
