<?php

namespace App\Filament\Resources\AcademicCalendars\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AcademicCalendarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Event Title')
                    ->placeholder('e.g. Staff Meeting, Madaraka Day')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('Event Type')
                    ->options([
                        'holiday' => 'Holiday',
                        'exam' => 'Exam',
                        'event' => 'School Event',
                        'meeting' => 'Staff Meeting',
                        'break' => 'School Break',
                        'closure' => 'School Closure',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->live(),
                Grid::make(2)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Start Date')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->helperText('Leave empty for single-day events')
                            ->afterOrEqual('date'),
                    ]),
                Toggle::make('is_all_day')
                    ->label('All Day Event')
                    ->default(true)
                    ->live()
                    ->helperText('Turn off for events with specific start/end times (e.g. meetings)'),
                Grid::make(2)
                    ->schema([
                        TimePicker::make('start_time')
                            ->label('Start Time')
                            ->seconds(false)
                            ->requiredUnless('is_all_day', true),
                        TimePicker::make('end_time')
                            ->label('End Time')
                            ->seconds(false)
                            ->after('start_time')
                            ->requiredUnless('is_all_day', true),
                    ])
                    ->visible(fn ($get) => ! $get('is_all_day')),
                Toggle::make('suppresses_notifications')
                    ->label('Suppress Lesson Reminders')
                    ->helperText('When enabled, teachers will not receive class reminders during this event. For partial-day events (meetings), reminders resume after the end time.')
                    ->default(fn ($get) => in_array($get('type'), ['holiday', 'break', 'closure'])),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
