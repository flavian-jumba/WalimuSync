<?php

namespace App\Filament\Resources\TeacherAbsences;

use App\Filament\Resources\TeacherAbsences\Pages\CreateTeacherAbsence;
use App\Filament\Resources\TeacherAbsences\Pages\EditTeacherAbsence;
use App\Filament\Resources\TeacherAbsences\Pages\ListTeacherAbsences;
use App\Filament\Resources\TeacherAbsences\Schemas\TeacherAbsenceForm;
use App\Filament\Resources\TeacherAbsences\Tables\TeacherAbsencesTable;
use App\Models\TeacherAbsence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TeacherAbsenceResource extends Resource
{
    protected static ?string $model = TeacherAbsence::class;

    protected static ?string $navigationLabel = 'Absences';

    protected static ?string $modelLabel = 'Absence';

    protected static ?string $pluralModelLabel = 'Absences';

    protected static string|UnitEnum|null $navigationGroup = 'Staff Management';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserMinus;

    public static function form(Schema $schema): Schema
    {
        return TeacherAbsenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeacherAbsencesTable::configure($table);
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
            'index' => ListTeacherAbsences::route('/'),
            'create' => CreateTeacherAbsence::route('/create'),
            'edit' => EditTeacherAbsence::route('/{record}/edit'),
        ];
    }
}
