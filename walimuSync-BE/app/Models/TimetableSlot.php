<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimetableSlot extends Model
{
    /** @use HasFactory<\Database\Factories\TimetableSlotFactory> */
    use HasFactory;

    protected $fillable = [
        'school_class_id',
        'subject_id',
        'teacher_id',
        'term_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function substitutions(): HasMany
    {
        return $this->hasMany(Substitution::class);
    }
}
