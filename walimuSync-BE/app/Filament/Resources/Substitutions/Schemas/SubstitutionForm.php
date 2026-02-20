<?php

namespace App\Filament\Resources\Substitutions\Schemas;

use App\Models\TimetableSlot;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class SubstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('timetable_slot_id')
                    ->label('Lesson')
                    ->options(
                        TimetableSlot::query()
                            ->with(['schoolClass', 'subject', 'teacher'])
                            ->get()
                            ->mapWithKeys(fn (TimetableSlot $slot): array => [
                                $slot->id => "{$slot->schoolClass->name} - {$slot->subject->name} ({$slot->teacher->name}, {$slot->day_of_week})",
                            ])
                    )
                    ->searchable()
                    ->required(),
                Select::make('substitute_teacher_id')
                    ->relationship('substituteTeacher', 'name')
                    ->label('Cover Teacher')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('date')
                    ->label('Date')
                    ->required(),
            ]);
    }
}
