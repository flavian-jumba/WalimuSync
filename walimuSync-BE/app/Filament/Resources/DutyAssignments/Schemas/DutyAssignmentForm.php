<?php

namespace App\Filament\Resources\DutyAssignments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class DutyAssignmentForm
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
                DatePicker::make('start_date')
                    ->label('From')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('To')
                    ->after('start_date')
                    ->required(),
            ]);
    }
}
