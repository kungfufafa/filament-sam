<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'whatsapp_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFilamentName(): string
    {
        return (string) ($this->name ?? $this->username);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (blank($this->profile_photo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->profile_photo_path);
    }

    public function routeNotificationForOneSignal(): string
    {
        return "user:{$this->getKey()}";
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function tm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tm_id');
    }

    /**
     * @return HasMany<User, $this>
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'tm_id');
    }

    /**
     * @return BelongsToMany<BusinessEntity, $this>
     */
    public function businessEntities(): BelongsToMany
    {
        return $this->belongsToMany(BusinessEntity::class, 'business_entity_user', 'user_id', 'business_entity_id')->withTimestamps();
    }

    /**
     * @return BelongsToMany<Division, $this>
     */
    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class, 'division_user', 'user_id', 'division_id')->withTimestamps();
    }

    /**
     * @return BelongsToMany<Region, $this>
     */
    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'region_user', 'user_id', 'region_id')->withTimestamps();
    }

    /**
     * @return BelongsToMany<Cluster, $this>
     */
    public function clusters(): BelongsToMany
    {
        return $this->belongsToMany(Cluster::class, 'cluster_user', 'user_id', 'cluster_id')->withTimestamps();
    }

    /**
     * @return HasMany<Visit, $this>
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * @return HasMany<PlanVisit, $this>
     */
    public function planVisits(): HasMany
    {
        return $this->hasMany(PlanVisit::class);
    }

    /**
     * @return HasMany<OneSignalSubscription, $this>
     */
    public function oneSignalSubscriptions(): HasMany
    {
        return $this->hasMany(OneSignalSubscription::class);
    }

    public function canImpersonate(): bool
    {
        return $this->can('ImpersonateUser');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('super_admin');
    }
}
