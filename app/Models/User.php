<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function baiThi(): HasMany
    {
        return $this->hasMany(Exam::class, 'nguoi_dung_id');
    }

    public function luotThi(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'nguoi_dung_id');
    }

    public function thongKeCauHoi(): HasMany
    {
        return $this->hasMany(UserQuestionStat::class, 'nguoi_dung_id');
    }

    public function tienDo(): HasMany
    {
        return $this->hasMany(UserSubSubjectProgress::class, 'nguoi_dung_id');
    }
}
