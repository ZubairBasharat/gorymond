<?php

namespace App\Filament\Resources\ToneResource\Pages;

use App\Filament\Resources\ToneResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTone extends EditRecord
{
    protected static string $resource = ToneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
