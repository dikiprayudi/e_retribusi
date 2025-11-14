<?php

namespace App\Filament\Resources\BpjsResource\Pages;

use App\Filament\Resources\BpjsResource;
use App\Models\Pegawai;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

class ListBpjs extends ListRecords
{
    protected static string $resource = BpjsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus')
                ->createAnother(false)
                ->modalAutofocus(false)
                ->modalFooterActionsAlignment('right'),

            Action::make('cetak')
                ->label('Cetak Laporan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalWidth('3xl')
                ->modalFooterActionsAlignment('right')
                ->form([
                    // --- Form Anda (ini sudah benar) ---
                    Select::make('jenis_laporan')
                        ->label('Pilih Jenis Laporan')
                        ->options([
                            'bulanan' => 'Laporan Bulanan',
                            'tahunan' => 'Laporan Tahunan',
                            'harian' => 'Laporan Harian (Rentang Tgl)',
                        ])
                        ->required()
                        ->live(),

                    DatePicker::make('bulan')
                        ->label('Pilih Bulan dan Tahun')
                        ->displayFormat('F Y')
                        ->closeOnDateSelection()
                        ->native(false)
                        ->required(fn ($get) => $get('jenis_laporan') === 'bulanan')
                        ->visible(fn ($get) => $get('jenis_laporan') === 'bulanan'),

                    DatePicker::make('tahun')
                        ->label('Pilih Tahun')
                        ->displayFormat('Y')
                        ->closeOnDateSelection()
                        ->native(false)
                        ->required(fn ($get) => $get('jenis_laporan') === 'tahunan')
                        ->visible(fn ($get) => $get('jenis_laporan') === 'tahunan'),
                    
                    DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->displayFormat('d F Y')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->required(fn ($get) => $get('jenis_laporan') === 'harian')
                        ->visible(fn ($get) => $get('jenis_laporan') === 'harian'),
                    DatePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai')
                        ->displayFormat('d F Y')
                        ->native(false)
                        ->closeOnDateSelection()
                        ->required(fn ($get) => $get('jenis_laporan') === 'harian')
                        ->visible(fn ($get) => $get('jenis_laporan') === 'harian'),
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
                // --- PERBAIKAN LOGIKA ADA DI SINI ---
                
                // 1. Ganti label tombol submit modal
                ->modalSubmitActionLabel('Cetak')
                
                // --- 2. GANTI LOGIKA ACTION DENGAN NOTIFIKASI ---
                // Hapus type-hint 'RedirectResponse'
                ->action(function (array $data) { 
                    
                    // Buat URL (sesuaikan dengan route BPJS)
                    $url = route('laporan.cetak-bpjs', $data);

                    // Kirim notifikasi
                    Notification::make()
                        ->title('Laporan Siap Dicetak')
                        ->icon('heroicon-o-hand-thumb-up')
                        ->body('Klik tombol di bawah untuk membuka.')
                        ->success()
                        ->persistent() // <-- Buat notifikasi tetap tampil
                        ->actions([
                            // Gunakan alias 'NotificationAction'
                            NotificationAction::make('bukaTab') 
                                ->label('Lihat Hasil Cetak')
                                ->color('info')
                                ->icon('heroicon-o-printer')
                                ->button()
                                ->url($url, shouldOpenInNewTab: true)
                        ])
                        ->send();
                    
                    // Tidak ada 'return'
                }),
            
            // --- TAMBAHKAN TOMBOL BARU DI BAWAH INI ---
            Action::make('cetakBku')
                ->label('Cetak BKU Penerimaan')
                ->icon('heroicon-o-book-open') // Ikon baru
                ->color('info') // Warna baru
                ->modalWidth('3xl')
                ->form([
                    Section::make('Periode Laporan')
                        ->schema([
                            Grid::make(2)->schema([
                            // BKU biasanya pakai rentang tanggal
                            DatePicker::make('tanggal_mulai')
                                ->label('Tanggal Mulai')
                                ->native(false)
                                ->displayFormat('d F Y')
                                ->closeOnDateSelection()
                                ->required(),
                            DatePicker::make('tanggal_selesai')
                                ->label('Tanggal Selesai')
                                ->native(false)
                                ->displayFormat('d F Y')
                                ->closeOnDateSelection()
                                ->required(),
                            ])
                        ]),
                    Section::make('Penandatangan Laporan')
                        ->schema([
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
                            ])
                        ]),
                ])
                ->modalSubmitActionLabel('Cetak')
                ->action(function (array $data) { 
                    // Kita akan buat route baru 'laporan.cetak-bku'
                    $url = route('laporan.cetak-bku', $data);

                    Notification::make()
                        ->title('Laporan BKU Siap Dicetak')
                        ->icon('heroicon-o-hand-thumb-up')
                        ->body('Klik tombol di bawah untuk membuka.')
                        ->success()
                        ->persistent()
                        ->actions([
                            NotificationAction::make('bukaTab') 
                                ->label('Lihat Hasil Cetak')
                                ->color('info')
                                ->icon('heroicon-o-printer')
                                ->button()
                                ->url($url, shouldOpenInNewTab: true)
                        ])
                        ->send();
                }),
        ];
    }
}