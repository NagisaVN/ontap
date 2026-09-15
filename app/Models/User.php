<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * Spatie Activitylog: configure what gets auto-logged on model events.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active'])  // Only log these field changes
            ->logOnlyDirty()                            // Skip log if nothing actually changed
            ->dontSubmitEmptyLogs()                     // Never write empty log entries
            ->setDescriptionForEvent(fn (string $event) => match ($event) {
                'created' => 'User account was created',
                'updated' => 'User profile was updated',
                'deleted' => 'User account was deleted',
                default => "User was {$event}",
            });
    }

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

    public function lichOnTap(): HasMany
    {
        return $this->hasMany(SpacedRepetitionSchedule::class, 'nguoi_dung_id');
    }
}
