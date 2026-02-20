<?php

namespace App\Filament\Resources\TeacherAbsences\Pages;

use App\Filament\Resources\TeacherAbsences\TeacherAbsenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacherAbsence extends EditRecord
{
    protected static string $resource = TeacherAbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
