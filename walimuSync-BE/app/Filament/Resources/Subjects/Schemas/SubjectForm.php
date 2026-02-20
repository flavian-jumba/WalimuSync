<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Subject Name')
                    ->placeholder('e.g. Mathematics')
                    ->required(),
                TextInput::make('code')
                    ->label('Subject Code')
                    ->placeholder('e.g. MATH'),
            ]);
    }
}
