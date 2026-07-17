<?php

namespace App\Models;

use Database\Factories\OutletGeotagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutletGeotag extends Model
{
    /** @use HasFactory<OutletGeotagFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'radius' => 'integer',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (OutletGeotag $geotag): void {
            if ($geotag->is_primary && $geotag->outlet_id) {
                static::query()
                    ->where('outlet_id', $geotag->outlet_id)
                    ->whereKeyNot($geotag->getKey())
                    ->update(['is_primary' => false]);
            }
        });

    }

    /**
     * @return BelongsTo<Outlet, $this>
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
