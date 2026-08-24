<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $table = 'mon_hoc';

    protected $fillable = ['nganh_id', 'ten', 'ma_mon', 'mo_ta'];

    public function nganh(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'nganh_id');
    }

    public function chuong(): HasMany
    {
        return $this->hasMany(SubSubject::class, 'mon_hoc_id')->orderBy('thu_tu');
    }

    public function baiThi(): HasMany
    {
        return $this->hasMany(Exam::class, 'mon_hoc_id');
    }
}
