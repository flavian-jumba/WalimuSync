<?php

namespace App\Filament\Resources\TeacherAbsences\Pages;

use App\Filament\Resources\TeacherAbsences\TeacherAbsenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeacherAbsences extends ListRecords
{
    protected static string $resource = TeacherAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
