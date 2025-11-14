<?php

namespace App\Filament\Resources\BpjsResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\Alignment;
use App\Filament\Resources\BpjsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBpjs extends CreateRecord
{
    protected static string $resource = BpjsResource::class;

    protected static bool $canCreateAnother = false;

    public function getFormActionsAlignment(): Alignment
    {
        return Alignment::Center;
    }
}
