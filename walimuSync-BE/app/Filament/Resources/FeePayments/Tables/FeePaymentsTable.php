<?php

namespace App\Filament\Resources\FeePayments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeePaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date', 'desc')
            ->columns([
                TextColumn::make('feeCollection.title')
                    ->label('Collection')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->label('Amount')
                    ->money('KES')
                    ->sortable(),
                TextColumn::make('collector.name')
                    ->label('Collected By')
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('receipt_number')
                    ->label('Receipt No.')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('fee_collection_id')
                    ->relationship('feeCollection', 'title')
                    ->label('Collection')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('student_id')
                    ->relationship('student', 'name')
                    ->label('Student')
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
