<?php

namespace App\Filament\Resources\Substitutions\Pages;

use App\Filament\Resources\Substitutions\SubstitutionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubstitution extends EditRecord
{
    protected static string $resource = SubstitutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
