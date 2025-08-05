<?php

namespace App\Filament\Resources\PropertyAvailabilityResource\Pages;

use App\Filament\Resources\PropertyAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyAvailabilities extends ListRecords
{
    protected static string $resource = PropertyAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
