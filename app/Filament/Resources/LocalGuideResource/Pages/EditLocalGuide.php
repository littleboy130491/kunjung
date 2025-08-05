<?php

namespace App\Filament\Resources\LocalGuideResource\Pages;

use App\Filament\Resources\LocalGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLocalGuide extends EditRecord
{
    protected static string $resource = LocalGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
