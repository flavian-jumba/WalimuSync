<?php

namespace App\Filament\Resources\TeacherAbsences\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeacherAbsenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->label('Teacher')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('date')
                    ->label('Absence Date')
                    ->required(),
                Textarea::make('reason')
                    ->label('Reason')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
