<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'firebase_uid',
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

    public function timetableSlots(): HasMany
    {
        return $this->hasMany(TimetableSlot::class, 'teacher_id');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(TeacherAbsence::class, 'teacher_id');
    }

    public function substitutionsGiven(): HasMany
    {
        return $this->hasMany(Substitution::class, 'substitute_teacher_id');
    }

    public function dutyAssignments(): HasMany
    {
        return $this->hasMany(DutyAssignment::class, 'teacher_id');
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function assignedCollections(): HasMany
    {
        return $this->hasMany(FeeCollection::class, 'assigned_teacher_id');
    }

    public function collectedPayments(): HasMany
    {
        return $this->hasMany(FeePayment::class, 'collected_by');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'posted_by');
    }

    public function recordedResults(): HasMany
    {
        return $this->hasMany(ExamResult::class, 'recorded_by');
    }

    public function routeNotificationForFcm(): array
    {
        return $this->deviceTokens()
            ->pluck('fcm_token')
            ->filter()
            ->values()
            ->all();
    }
}
