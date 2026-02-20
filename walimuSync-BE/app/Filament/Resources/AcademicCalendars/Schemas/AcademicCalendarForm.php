<?php

namespace App\Filament\Resources\AcademicCalendars\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AcademicCalendarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Date')
                    ->required(),
                Select::make('type')
                    ->label('Event Type')
                    ->options([
                        'holiday' => 'Holiday',
                        'exam' => 'Exam',
                        'event' => 'School Event',
                        'meeting' => 'Staff Meeting',
                        'break' => 'School Break',
                        'other' => 'Other',
                    ])
                    ->required(),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
