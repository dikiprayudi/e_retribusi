<?php

namespace App\Filament\Resources\PoliResource\Pages;

use App\Filament\Resources\PoliResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPolis extends ListRecords
{
    protected static string $resource = PoliResource::class;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->icon('heroicon-o-plus')
            ->createAnother(false)
            ->modalFooterActionsAlignment('right')
        ];
    }
}
