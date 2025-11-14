<?php

namespace App\Filament\Resources\StsResource\Pages;

use App\Filament\Resources\StsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSts extends EditRecord
{
    protected static string $resource = StsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
