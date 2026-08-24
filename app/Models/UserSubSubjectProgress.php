<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubSubjectProgress extends Model
{
    protected $table = 'tien_do';

    protected $fillable = [
        'nguoi_dung_id',
        'chuong_id',
        'tong_da_lam',
        'phan_tram_thanh_thao',
    ];

    protected $casts = [
        'phan_tram_thanh_thao' => 'decimal:2',
    ];

    public function nguoiDung(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function chuong(): BelongsTo
    {
        return $this->belongsTo(SubSubject::class, 'chuong_id');
    }
}
