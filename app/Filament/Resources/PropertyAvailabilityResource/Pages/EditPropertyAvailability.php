<?php

namespace App\Filament\Resources\PropertyAvailabilityResource\Pages;

use App\Filament\Resources\PropertyAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyAvailability extends EditRecord
{
    protected static string $resource = PropertyAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
