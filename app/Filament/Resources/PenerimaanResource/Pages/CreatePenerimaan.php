<?php

namespace App\Filament\Resources\PenerimaanResource\Pages;

use App\Filament\Resources\PenerimaanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Collection;
use Filament\Support\Enums\Alignment;

class CreatePenerimaan extends CreateRecord
{
    protected static string $resource = PenerimaanResource::class;

    protected static bool $canCreateAnother = false;
    
    // PERBAIKAN: Memindahkan logika kalkulasi dari file lama ke sini
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tindakan = $data['tindakan'] ?? [];
        $subtotal = collect($tindakan)->sum(function ($item) {
            return (float) ($item['biaya'] ?? 0);
        });
        $diskon = (float) ($data['diskon'] ?? 0);
        $grandTotal = $subtotal - $diskon;
        $data['total_tarif'] = $grandTotal;
        $data['diskon'] = $diskon;
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getFormActionsAlignment(): Alignment
    {
        return Alignment::Center;
    }
}