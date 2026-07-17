<?php

namespace App\Models;

use App\Enums\SystemSettingScopeLevel;
use Database\Factories\SystemSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    /** @use HasFactory<SystemSettingFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $attributes = [
        'scope_level' => SystemSettingScopeLevel::Global->value,
    ];

    protected function casts(): array
    {
        return [
            'scope_level' => SystemSettingScopeLevel::class,
            'allow_outlet_registration_visits' => 'boolean',
            'default_outlet_registration_radius' => 'integer',
            'plan_visit_min_days' => 'integer',
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
}
