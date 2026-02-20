<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required(),
                TextInput::make('admission_number')
                    ->label('Admission No.')
                    ->unique(ignoreRecord: true)
                    ->required(),
                Select::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Class')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('parent_name')
                    ->label('Parent/Guardian Name'),
                TextInput::make('parent_phone')
                    ->label('Parent Phone')
                    ->tel()
                    ->placeholder('07XXXXXXXX'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
