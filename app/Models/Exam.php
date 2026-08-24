<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $table = 'bai_thi';

    protected $fillable = [
        'nguoi_dung_id',
        'mon_hoc_id',
        'ten_bai_thi',
        'che_do',
        'cau_hinh_ma_tran',
        'so_cau_hoi',
        'thoi_gian_phut',
    ];

    protected $casts = [
        'cau_hinh_ma_tran' => 'array',
        'che_do'           => \App\Enums\ExamMode::class,
    ];

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'mon_hoc_id');
    }

    public function cauHoi(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'bai_thi_cau_hoi', 'bai_thi_id', 'cau_hoi_id')
                    ->withPivot('thu_tu')
                    ->orderByPivot('thu_tu')
                    ->withTimestamps();
    }

    public function luotThi(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'bai_thi_id');
    }
}
