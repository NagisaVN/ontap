<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $table = 'luot_thi';

    protected $fillable = [
        'nguoi_dung_id',
        'bai_thi_id',
        'diem_so',
        'so_cau_dung',
        'thoi_gian_lam',
        'trang_thai',
        'bat_dau_luc',
        'ket_thuc_luc',
    ];

    protected $casts = [
        'diem_so'     => 'decimal:2',
        'trang_thai'  => \App\Enums\AttemptStatus::class,
        'bat_dau_luc' => 'datetime',
        'ket_thuc_luc'=> 'datetime',
    ];

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function baiThi(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'bai_thi_id');
    }

    public function ketQua(): HasMany
    {
        return $this->hasMany(ExamAttemptAnswer::class, 'luot_thi_id');
    }
}
