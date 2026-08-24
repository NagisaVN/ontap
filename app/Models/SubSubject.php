<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubSubject extends Model
{
    protected $table = 'chuong';

    protected $fillable = ['mon_hoc_id', 'ten', 'thu_tu'];

    public function monHoc(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'mon_hoc_id');
    }

    public function cauHoi(): HasMany
    {
        return $this->hasMany(Question::class, 'chuong_id');
    }

    public function tienDo(): HasMany
    {
        return $this->hasMany(UserSubSubjectProgress::class, 'chuong_id');
    }
}
