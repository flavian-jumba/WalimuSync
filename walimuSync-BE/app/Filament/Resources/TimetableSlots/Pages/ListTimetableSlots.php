<?php

namespace App\Filament\Resources\TimetableSlots\Pages;

use App\Filament\Resources\TimetableSlots\TimetableSlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTimetableSlots extends ListRecords
{
    protected static string $resource = TimetableSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
