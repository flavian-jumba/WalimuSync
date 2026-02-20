<?php

namespace App\Filament\Resources\TimetableSlots\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class TimetableSlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Class')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->label('Teacher')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('term_id')
                    ->relationship('term', 'name')
                    ->label('Term')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('day_of_week')
                    ->label('Day')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->required(),
                TimePicker::make('start_time')
                    ->label('Start Time')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('end_time')
                    ->label('End Time')
                    ->seconds(false)
                    ->after('start_time')
                    ->required(),
            ]);
    }
}
