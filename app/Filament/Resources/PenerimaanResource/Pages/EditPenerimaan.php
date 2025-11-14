<?php

namespace App\Filament\Resources\PenerimaanResource\Pages;

use App\Filament\Resources\PenerimaanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditPenerimaan extends EditRecord
{
    protected static string $resource = PenerimaanResource::class;

    // PERBAIKAN: Memindahkan logika kalkulasi dari file Resource ke sini
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $tindakan = $data['tindakan'] ?? [];
        $subtotal = collect($tindakan)->sum(function($item) {
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