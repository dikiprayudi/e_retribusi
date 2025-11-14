<?php

namespace App\Filament\Resources\BpjsResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\Alignment;
use App\Filament\Resources\BpjsResource;
use Filament\Resources\Pages\EditRecord;

class EditBpjs extends EditRecord
{
    protected static string $resource = BpjsResource::class;

    public function getFormActionsAlignment(): Alignment
    {
        return Alignment::Center;
    }
}
