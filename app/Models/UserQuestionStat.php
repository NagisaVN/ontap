<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuestionStat extends Model
{
    protected $table = 'thong_ke_cau_hoi';

    protected $fillable = [
        'nguoi_dung_id',
        'cau_hoi_id',
        'so_lan_sai',
        'so_lan_dung',
        'lan_cuoi_lam',
    ];

    protected $casts = [
        'lan_cuoi_lam' => 'datetime',
    ];

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'cau_hoi_id');
    }
}
