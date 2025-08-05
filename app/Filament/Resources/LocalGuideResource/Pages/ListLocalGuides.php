<?php

namespace App\Filament\Resources\LocalGuideResource\Pages;

use App\Filament\Resources\LocalGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocalGuides extends ListRecords
{
    protected static string $resource = LocalGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
