<?php

namespace App\Filament\Resources\ExamResults\Tables;

use App\Models\SchoolClass;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExamResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('student.schoolClass.name')
                    ->label('Class')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('term.name')
                    ->label('Term')
                    ->sortable(),
                TextColumn::make('exam_type')
                    ->label('Exam')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cat' => 'info',
                        'midterm' => 'warning',
                        'endterm' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cat' => 'CAT',
                        'midterm' => 'Mid-Term',
                        'endterm' => 'End-Term',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable()
                    ->suffix(' / 100'),
                TextColumn::make('grade')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D' => 'danger',
                        'E' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('recorder.name')
                    ->label('Recorded By')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('exam_type')
                    ->label('Exam Type')
                    ->options([
                        'cat' => 'CAT',
                        'midterm' => 'Mid-Term',
                        'endterm' => 'End-Term',
                    ]),
                SelectFilter::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('term_id')
                    ->relationship('term', 'name')
                    ->label('Term')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('grade')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                        'E' => 'E',
                    ]),
                SelectFilter::make('school_class')
                    ->label('Class')
                    ->options(fn () => SchoolClass::pluck('name', 'id'))
                    ->query(fn ($query, array $data) => $query->when(
                        $data['value'],
                        fn ($q, $classId) => $q->whereHas('student', fn ($sq) => $sq->where('school_class_id', $classId))
                    ))
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
