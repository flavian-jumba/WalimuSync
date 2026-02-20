<?php

namespace App\Filament\Resources\Terms\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Term Name')
                    ->placeholder('e.g. Term 1 2026')
                    ->required(),
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('End Date')
                    ->after('start_date')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Currently Active'),
            ]);
    }
}
