<?php

namespace App\Filament\Resources\TimetableSlots\Pages;

use App\Filament\Resources\TimetableSlots\TimetableSlotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTimetableSlot extends EditRecord
{
    protected static string $resource = TimetableSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
