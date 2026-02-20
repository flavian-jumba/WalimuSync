<?php

namespace App\Filament\Resources\Substitutions;

use App\Filament\Resources\Substitutions\Pages\CreateSubstitution;
use App\Filament\Resources\Substitutions\Pages\EditSubstitution;
use App\Filament\Resources\Substitutions\Pages\ListSubstitutions;
use App\Filament\Resources\Substitutions\Schemas\SubstitutionForm;
use App\Filament\Resources\Substitutions\Tables\SubstitutionsTable;
use App\Models\Substitution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SubstitutionResource extends Resource
{
    protected static ?string $model = Substitution::class;

    protected static ?string $navigationLabel = 'Cover Lessons';

    protected static ?string $modelLabel = 'Cover Lesson';

    protected static ?string $pluralModelLabel = 'Cover Lessons';

    protected static string|UnitEnum|null $navigationGroup = 'Timetable';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    public static function form(Schema $schema): Schema
    {
        return SubstitutionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubstitutionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubstitutions::route('/'),
            'create' => CreateSubstitution::route('/create'),
            'edit' => EditSubstitution::route('/{record}/edit'),
        ];
    }
}
