<?php

namespace App\Filament\Resources\PenerimaanResource\Pages;

use App\Filament\Resources\PenerimaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenerimaans extends ListRecords
{
    protected static string $resource = PenerimaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // PERBAIKAN: CreateAction::make() sekarang otomatis
            // mengarah ke halaman 'create' baru.
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }
}