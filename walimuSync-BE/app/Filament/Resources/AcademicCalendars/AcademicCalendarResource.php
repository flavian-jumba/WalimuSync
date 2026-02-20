<?php

namespace App\Filament\Resources\AcademicCalendars;

use App\Filament\Resources\AcademicCalendars\Pages\CreateAcademicCalendar;
use App\Filament\Resources\AcademicCalendars\Pages\EditAcademicCalendar;
use App\Filament\Resources\AcademicCalendars\Pages\ListAcademicCalendars;
use App\Filament\Resources\AcademicCalendars\Schemas\AcademicCalendarForm;
use App\Filament\Resources\AcademicCalendars\Tables\AcademicCalendarsTable;
use App\Models\AcademicCalendar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AcademicCalendarResource extends Resource
{
    protected static ?string $model = AcademicCalendar::class;

    protected static ?string $navigationLabel = 'School Calendar';

    protected static ?string $modelLabel = 'Calendar Event';

    protected static ?string $pluralModelLabel = 'School Calendar';

    protected static string|UnitEnum|null $navigationGroup = 'School Setup';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    public static function form(Schema $schema): Schema
    {
        return AcademicCalendarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicCalendarsTable::configure($table);
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
            'index' => ListAcademicCalendars::route('/'),
            'create' => CreateAcademicCalendar::route('/create'),
            'edit' => EditAcademicCalendar::route('/{record}/edit'),
        ];
    }
}
