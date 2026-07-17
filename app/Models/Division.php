<?php

namespace App\Models;

use Database\Factories\DivisionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    /** @use HasFactory<DivisionFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo<BusinessEntity, $this>
     */
    public function businessEntity(): BelongsTo
    {
        return $this->belongsTo(BusinessEntity::class, 'business_entity_id');
    }

    /**
     * @return HasMany<Region, $this>
     */
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class, 'division_id');
    }

    /**
     * @return HasMany<Cluster, $this>
     */
    public function clusters(): HasMany
    {
        return $this->hasMany(Cluster::class, 'division_id');
    }

    /**
     * @return HasMany<Outlet, $this>
     */
    public function outlets(): HasMany
    {
        return $this->hasMany(Outlet::class, 'division_id');
    }

    /**
     * @return HasMany<OutletRegistration, $this>
     */
    public function outlet_registrations(): HasMany
    {
        return $this->hasMany(OutletRegistration::class, 'division_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'division_user', 'division_id', 'user_id')->withTimestamps();
    }
}
