<?php

namespace App\Filament\Resources\FeeCollections\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FeeCollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('type')
                    ->options([
                        'remedial' => 'Remedial',
                        'lunch' => 'Lunch',
                        'exam' => 'Exam Fee',
                        'trip' => 'Trip',
                        'uniform' => 'Uniform',
                        'other' => 'Other',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('KES'),
                Select::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Class (optional)')
                    ->searchable()
                    ->preload()
                    ->helperText('Leave empty to apply to all classes'),
                Select::make('term_id')
                    ->relationship('term', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('assigned_teacher_id')
                    ->relationship('assignedTeacher', 'name')
                    ->label('Assigned Teacher')
                    ->searchable()
                    ->preload()
                    ->helperText('Teacher responsible for collecting'),
                DatePicker::make('due_date'),
                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ])
                    ->default('open')
                    ->required(),
            ]);
    }
}
