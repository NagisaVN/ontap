<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpacedRepetitionSchedule extends Model
{
    protected $table = 'lich_on_tap';

    protected $fillable = [
        'nguoi_dung_id',
        'cau_hoi_id',
        'interval_days',
        'repetitions',
        'ease_factor',
        'next_review_at',
        'last_reviewed_at',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'ease_factor' => 'float',
            'next_review_at' => 'datetime',
            'last_reviewed_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'cau_hoi_id');
    }

    public function scopeDue(Builder $query, $at = null): Builder
    {
        return $query->where('next_review_at', '<=', $at ?? now());
    }
}
