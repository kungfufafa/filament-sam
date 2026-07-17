<?php

namespace App\Models;

use App\Enums\ScheduleScope;
use Database\Factories\PlanVisitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanVisit extends Model
{
    /** @use HasFactory<PlanVisitFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $attributes = [
        'schedule_scope' => ScheduleScope::Daily->value,
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'period_start' => 'date',
            'period_end' => 'date',
            'realized_at' => 'datetime',
            'schedule_week' => 'integer',
            'schedule_year' => 'integer',
            'schedule_scope' => ScheduleScope::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Visit, $this>
     */
    public function realizedVisit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'realized_visit_id');
    }
}
