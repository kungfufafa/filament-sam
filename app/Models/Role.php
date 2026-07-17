<?php

namespace App\Models;

use App\Enums\OrganizationalScopeLevel;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'parent_role_id',
        'organizational_scope_level',
        'can_access_web',
        'can_access_mobile',
    ];

    protected $attributes = [
        'organizational_scope_level' => OrganizationalScopeLevel::Cluster->value,
        'can_access_web' => true,
        'can_access_mobile' => true,
    ];

    protected function casts(): array
    {
        return [
            'organizational_scope_level' => OrganizationalScopeLevel::class,
            'can_access_web' => 'boolean',
            'can_access_mobile' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Role, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_role_id');
    }

    /**
     * @return HasMany<Role, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_role_id');
    }
}
