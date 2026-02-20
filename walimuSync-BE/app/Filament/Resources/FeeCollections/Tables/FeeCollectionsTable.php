<?php

namespace App\Filament\Resources\FeeCollections\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeeCollectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'remedial' => 'info',
                        'lunch' => 'warning',
                        'exam' => 'danger',
                        'trip' => 'success',
                        'uniform' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('KES')
                    ->sortable(),
                TextColumn::make('schoolClass.name')
                    ->label('Class')
                    ->placeholder('All Classes')
                    ->sortable(),
                TextColumn::make('term.name')
                    ->label('Term')
                    ->sortable(),
                TextColumn::make('assignedTeacher.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'remedial' => 'Remedial',
                        'lunch' => 'Lunch',
                        'exam' => 'Exam Fee',
                        'trip' => 'Trip',
                        'uniform' => 'Uniform',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ]),
                SelectFilter::make('term_id')
                    ->relationship('term', 'name')
                    ->label('Term')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Class')
                    ->searchable()
                    ->preload(),
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
