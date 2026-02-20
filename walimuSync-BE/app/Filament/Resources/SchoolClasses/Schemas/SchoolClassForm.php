<?php

namespace App\Filament\Resources\SchoolClasses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SchoolClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Class Name')
                    ->placeholder('e.g. Form 1')
                    ->required(),
                TextInput::make('stream')
                    ->label('Stream')
                    ->placeholder('e.g. East'),
                TextInput::make('academic_year')
                    ->label('Academic Year')
                    ->placeholder('e.g. 2026')
                    ->required(),
            ]);
    }
}
