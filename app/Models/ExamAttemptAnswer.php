<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttemptAnswer extends Model
{
    protected $table = 'ket_qua';

    protected $fillable = [
        'luot_thi_id',
        'cau_hoi_id',
        'lua_chon_id',
        'dung_sai',
        'giai_thich_ai',
    ];

    protected $casts = [
        'dung_sai' => 'boolean',
    ];

    public function luotThi(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'luot_thi_id');
    }

    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'cau_hoi_id');
    }

    public function luaChonDaChon(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'lua_chon_id');
    }
}
