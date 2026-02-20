<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                Select::make('audience')
                    ->options([
                        'all' => 'Everyone',
                        'teachers' => 'Teachers Only',
                        'class' => 'Specific Class',
                    ])
                    ->required()
                    ->live(),
                Select::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Class')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('audience') === 'class')
                    ->required(fn ($get) => $get('audience') === 'class'),
                Hidden::make('posted_by')
                    ->default(fn () => Auth::id()),
                DateTimePicker::make('published_at')
                    ->label('Publish At')
                    ->default(now()),
                Toggle::make('is_pinned')
                    ->label('Pin this notice'),
            ]);
    }
}
