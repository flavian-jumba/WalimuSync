<?php

namespace App\Filament\Resources\ExamResults\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ExamResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->label('Student')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->label('Subject')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('term_id')
                    ->relationship('term', 'name')
                    ->label('Term')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('exam_type')
                    ->options([
                        'cat' => 'CAT',
                        'midterm' => 'Mid-Term',
                        'endterm' => 'End-Term',
                    ])
                    ->required(),
                TextInput::make('score')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('/ 100'),
                Select::make('grade')
                    ->options([
                        'A' => 'A (80-100)',
                        'B' => 'B (60-79)',
                        'C' => 'C (40-59)',
                        'D' => 'D (20-39)',
                        'E' => 'E (0-19)',
                    ])
                    ->helperText('Auto-calculated if left empty'),
                Textarea::make('remarks')
                    ->columnSpanFull(),
                Hidden::make('recorded_by')
                    ->default(fn () => Auth::id()),
            ]);
    }
}
