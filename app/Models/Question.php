<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $table = 'cau_hoi';

    protected $fillable = [
        'chuong_id',
        'noi_dung',
        'hinh_anh',
        'do_kho',
        'giai_thich',
        'do_ai_tao',
        'cau_hoi_goc_id',
        'trang_thai',
        'nguon',
    ];

    protected $casts = [
        'do_ai_tao'   => 'boolean',
        'do_kho'      => \App\Enums\DifficultyLevel::class,
        'trang_thai'  => \App\Enums\QuestionStatus::class,
        'nguon'       => \App\Enums\QuestionSource::class,
    ];

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    /** ID hiển thị động theo mã môn học: VD "TIN01-120", "CSDL-34" */
    protected function maDinhDanh(): Attribute
    {
        return Attribute::make(
            get: function () {
                $maMon = $this->chuong?->monHoc?->ma_mon;
                $prefix = $maMon ? strtoupper($maMon) : 'Q';
                return "{$prefix}-{$this->id}";
            }
        );
    }

    /** Chỉ lấy câu hỏi đã được duyệt — dùng trong MatrixGenerationService */
    public function scopeDaDuyet($query)
    {
        return $query->where('trang_thai', 'da_duyet');
    }

    /** Lấy câu hỏi đang chờ duyệt — dùng trong Teacher Dashboard */
    public function scopeChoDuyet($query)
    {
        return $query->where('trang_thai', 'cho_duyet');
    }

    public function chuong(): BelongsTo
    {
        return $this->belongsTo(SubSubject::class, 'chuong_id');
    }

    public function luaChon(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'cau_hoi_id')->orderBy('thu_tu');
    }

    public function dapAnDung(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'cau_hoi_id')->where('la_dap_an', true);
    }

    /** Câu hỏi gốc (nếu đây là biến thể do AI tạo) */
    public function cauHoiGoc(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'cau_hoi_goc_id');
    }

    /** Các biến thể AI được sinh ra từ câu hỏi này */
    public function bienThe(): HasMany
    {
        return $this->hasMany(Question::class, 'cau_hoi_goc_id');
    }

    public function thongKe(): HasMany
    {
        return $this->hasMany(UserQuestionStat::class, 'cau_hoi_id');
    }

    public function baiThi(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'bai_thi_cau_hoi', 'cau_hoi_id', 'bai_thi_id')
                    ->withPivot('thu_tu')
                    ->withTimestamps();
    }
}
