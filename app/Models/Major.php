<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    protected $table = 'nganh';

    protected $fillable = ['ten', 'mo_ta'];

    public function monHoc(): HasMany
    {
        return $this->hasMany(Subject::class, 'nganh_id');
    }
}
