<?php

namespace App\Filament\Resources\AcademicCalendars\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AcademicCalendarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'holiday' => 'danger',
                        'exam' => 'warning',
                        'event' => 'info',
                        'meeting' => 'primary',
                        'break' => 'danger',
                        'closure' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'holiday' => 'Holiday',
                        'exam' => 'Exam',
                        'event' => 'School Event',
                        'meeting' => 'Staff Meeting',
                        'break' => 'School Break',
                        'closure' => 'School Closure',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Time')
                    ->formatStateUsing(function ($record) {
                        if ($record->is_all_day) {
                            return 'All Day';
                        }

                        return ($record->start_time?->format('H:i') ?? '').' - '.($record->end_time?->format('H:i') ?? '');
                    }),
                IconColumn::make('suppresses_notifications')
                    ->label('Suppresses Reminders')
                    ->boolean()
                    ->trueIcon('heroicon-o-bell-slash')
                    ->falseIcon('heroicon-o-bell')
                    ->trueColor('danger')
                    ->falseColor('gray'),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Event Type')
                    ->options([
                        'holiday' => 'Holiday',
                        'exam' => 'Exam',
                        'event' => 'School Event',
                        'meeting' => 'Staff Meeting',
                        'break' => 'School Break',
                        'closure' => 'School Closure',
                        'other' => 'Other',
                    ]),
                TernaryFilter::make('suppresses_notifications')
                    ->label('Suppresses Reminders'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
