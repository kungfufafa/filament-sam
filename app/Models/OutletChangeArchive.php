<?php

namespace App\Models;

use Database\Factories\OutletChangeArchiveFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutletChangeArchive extends Model
{
    /** @use HasFactory<OutletChangeArchiveFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'changed_fields' => 'array',
            'request_meta' => 'array',
            'restored_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Outlet, $this>
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function actorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * @return BelongsTo<OutletChangeArchive, $this>
     */
    public function restoredFrom(): BelongsTo
    {
        return $this->belongsTo(OutletChangeArchive::class, 'restored_from_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function restoredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restored_by_user_id');
    }
}
