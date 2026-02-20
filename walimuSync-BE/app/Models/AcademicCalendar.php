<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    protected $fillable = [
        'date',
        'type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
