<?php

namespace App\Filament\Resources\FeePayments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class FeePaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('fee_collection_id')
                    ->relationship('feeCollection', 'title')
                    ->label('Collection')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->label('Student')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount_paid')
                    ->label('Amount Paid')
                    ->required()
                    ->numeric()
                    ->prefix('KES'),
                Hidden::make('collected_by')
                    ->default(fn () => Auth::id()),
                DatePicker::make('payment_date')
                    ->label('Payment Date')
                    ->required()
                    ->default(now()),
                TextInput::make('receipt_number')
                    ->label('Receipt No.'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
