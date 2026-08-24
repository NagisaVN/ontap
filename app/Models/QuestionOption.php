<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    protected $table = 'lua_chon';

    protected $fillable = ['cau_hoi_id', 'noi_dung', 'la_dap_an', 'thu_tu'];

    protected $casts = [
        'la_dap_an' => 'boolean',
    ];

    public function cauHoi(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'cau_hoi_id');
    }
}
