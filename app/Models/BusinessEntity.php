<?php

namespace App\Models;

use Database\Factories\BusinessEntityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessEntity extends Model
{
    /** @use HasFactory<BusinessEntityFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * @return HasMany<Division, $this>
     */
    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class, 'business_entity_id');
    }

    /**
     * @return HasMany<Region, $this>
     */
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class, 'business_entity_id');
    }

    /**
     * @return HasMany<Cluster, $this>
     */
    public function clusters(): HasMany
    {
        return $this->hasMany(Cluster::class, 'business_entity_id');
    }

    /**
     * @return HasMany<Outlet, $this>
     */
    public function outlets(): HasMany
    {
        return $this->hasMany(Outlet::class, 'business_entity_id');
    }

    /**
     * @return HasMany<OutletRegistration, $this>
     */
    public function outlet_registrations(): HasMany
    {
        return $this->hasMany(OutletRegistration::class, 'business_entity_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_entity_user', 'business_entity_id', 'user_id')->withTimestamps();
    }
}
