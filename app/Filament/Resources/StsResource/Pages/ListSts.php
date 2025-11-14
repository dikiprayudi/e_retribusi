<?php

namespace App\Filament\Resources\StsResource\Pages;

use App\Filament\Resources\StsResource;
use App\Models\Penerimaan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSts extends ListRecords
{
    protected static string $resource = StsResource::class;
    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('4xl')
                ->icon('heroicon-o-plus')
                ->createAnother(false)
                ->modalFooterActionsAlignment('right')
                // --- UBAH LOGIKA MUTASI ---
                ->mutateFormDataUsing(function (array $data): array {
                    // Kita tetap hitung total di server-side untuk keamanan
                    // Form sudah menghitungnya, tapi ini validasi ulang
                    $startDate = $data['periode_awal'];
                    $endDate = $data['periode_akhir'];
            
                    $grandTotal = Penerimaan::whereBetween('tanggal', [$startDate, $endDate])
                                    ->sum('total_tarif');
            
                    // Suntikkan total ke field yang benar
                    $data['total_disetor'] = $grandTotal; 
                    
                    // Hapus logika 'rincian'
                    // unset($data['rincian']); // Tidak perlu unset jika tidak ada

                    return $data;
                }),
        ];
    }
}