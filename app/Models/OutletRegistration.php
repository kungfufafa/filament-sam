<?php

namespace App\Models;

use App\Enums\OutletRegistrationStatus;
use App\Enums\OutletRegistrationType;
use Database\Factories\OutletRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutletRegistration extends Model
{
    /** @use HasFactory<OutletRegistrationFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $attributes = [
        'type' => OutletRegistrationType::Noo->value,
        'status' => OutletRegistrationStatus::Pending->value,
    ];

    protected function casts(): array
    {
        return [
            'type' => OutletRegistrationType::class,
            'status' => OutletRegistrationStatus::class,
            'rejected_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'approved_at' => 'datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function tm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tm_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
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
}
