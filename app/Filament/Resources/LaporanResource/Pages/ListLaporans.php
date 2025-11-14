<?php

namespace App\Filament\Resources\LaporanResource\Pages;

use App\Filament\Resources\LaporanResource;
use App\Models\Poli;
use App\Models\Pegawai;
use Filament\Actions\Action; // Ini untuk Header Action
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\ListRecords;
use App\Http\Controllers\LaporanController;

// --- Ini adalah 2 'use' statement yang penting ---
use Filament\Notifications\Notification;
// Kita beri alias 'NotificationAction' agar tidak bentrok dengan 'Filament\Actions\Action' di atas
use Filament\Notifications\Actions\Action as NotificationAction;

class ListLaporans extends ListRecords
{
    protected static string $resource = LaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // --- TOMBOL 1: REKAP HARIAN (PIVOT) ---
            Action::make('cetakRekapHarian')
                ->label('Cetak Laporan Rekap Harian')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->modalWidth('4xl')
                ->modalFooterActionsAlignment('right')
                ->modalAutofocus(false)
                ->form([
                    // ... (Form Anda)
                    Section::make('Periode Laporan')->schema([
                        Grid::make(3)->schema([
                            DatePicker::make('bulan')
                                ->label('Pilih Bulan dan Tahun')
                                ->native(false)
                                ->displayFormat('F')
                                ->closeOnDateSelection()
                                ->required()
                                ->default(now()),
                            Select::make('ttd_kiri_id')->label('Mengetahui (Kepala UPT)')->options(Pegawai::pluck('nama_pegawai', 'id'))->searchable()->required(),
                            Select::make('ttd_kanan_id')->label('Bendahara Penerimaan')->options(Pegawai::pluck('nama_pegawai', 'id'))->searchable()->required(),
                        ]),
                    ]),
                ])
                ->modalSubmitActionLabel('Cetak')
                ->action(function (array $data) {
                    $printUrl = route('print.rekap.harian', [
                        'bulan' => $data['bulan'],
                        'ttd_kiri' => $data['ttd_kiri_id'],
                        'ttd_kanan' => $data['ttd_kanan_id'],
                    ]);

                    // --- MENGGUNAKAN SINTAKS ANDA YANG BENAR ---
                    Notification::make()
                        ->title('Laporan Siap Dicetak')
                        ->icon('heroicon-o-hand-thumb-up')
                        ->body('Klik tombol di bawah untuk membuka.')
                        ->success()
                        ->persistent()
                        ->actions([ // <-- Plural
                            NotificationAction::make('bukaTab') // <-- Gunakan alias
                                ->label('Lihat Hasil Cetak')
                                ->color('info')
                                ->icon('heroicon-o-printer')
                                ->button() // Membuatnya terlihat seperti tombol
                                ->url($printUrl, shouldOpenInNewTab: true)
                        ])
                        ->send();
                    // --- BATAS PERBAIKAN ---
                }),
            
            // --- TOMBOL 2: LAPORAN TRANSAKSI (BARU) ---
            Action::make('cetakLaporanTransaksi')
                ->label('Cetak Laporan Transaksi')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->modalWidth('4xl')
                ->modalFooterActionsAlignment('right')
                ->modalAutofocus(false)
                ->form([
                    // ... (Form Anda)
                    Section::make('Periode Laporan')->schema([
                        Grid::make(3)->schema([
                            DatePicker::make('tanggal_mulai')
                                ->label('Tanggal Mulai')
                                ->native(false)
                                ->displayFormat('d F Y')
                                ->closeOnDateSelection()
                                ->required()
                                ->default(now()),
                            DatePicker::make('tanggal_selesai')
                                ->label('Tanggal Selesai')
                                ->native(false)
                                ->displayFormat('d F Y')
                                ->closeOnDateSelection()
                                ->required()
                                ->default(now()),
                            Select::make('poli_id')
                                ->label('Pilih UPT/Poli')
                                ->options(function (): array {
                                    $polis = Poli::pluck('nama_poli', 'id')->all();
                                    return ['semua' => 'Puskesmas'] + $polis;
                                })
                                ->searchable()
                                ->required(),
                        ]),
                    ]),
                    Section::make('Filter dan Penandatangan')->schema([
                        Grid::make(2)->schema([
                            Select::make('ttd_kiri_id')
                                ->label('Mengetahui (Kepala UPT)')
                                ->options(Pegawai::pluck('nama_pegawai', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('ttd_kanan_id')
                                ->label('Bendahara Penerimaan')
                                ->options(Pegawai::pluck('nama_pegawai', 'id'))
                                ->searchable()
                                ->required(),
                        ]),
                    ]),
                ])
                ->modalSubmitActionLabel('Cetak')
                ->action(function (array $data) {
                    $printUrl = route('print.laporan.transaksi', [
                        'tanggal_mulai' => $data['tanggal_mulai'],
                        'tanggal_selesai' => $data['tanggal_selesai'],
                        'poli_id' => $data['poli_id'],
                        'ttd_kiri' => $data['ttd_kiri_id'],
                        'ttd_kanan' => $data['ttd_kanan_id'],
                    ]);

                    // --- MENGGUNAKAN SINTAKS ANDA YANG BENAR ---
                    Notification::make()
                        ->title('Laporan Siap Dicetak')
                        ->icon('heroicon-o-hand-thumb-up')
                        ->body('Klik tombol di bawah untuk membuka.')
                        ->success()
                        ->persistent()
                        ->actions([ // <-- Plural
                            NotificationAction::make('bukaTab') // <-- Gunakan alias
                                ->label('Lihat Hasil Cetak')
                                ->color('info')
                                ->icon('heroicon-o-printer')
                                ->button() // Membuatnya terlihat seperti tombol
                                ->url($printUrl, shouldOpenInNewTab: true)
                        ])
                        ->send();
                    // --- BATAS PERBAIKAN ---
                }),
        ];
    }
}