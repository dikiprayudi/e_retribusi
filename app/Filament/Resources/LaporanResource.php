<?php

namespace App\Filament\Resources;

use App\Models\Poli;
use App\Models\Pegawai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
// --- IMPORT BARU UNTUK TOMBOL CETAK ---
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\LaporanResource\Pages\ListLaporans;
use App\Models\Penerimaan; // <-- 1. GANTI MODEL KE PENERIMAAN
use Filament\Notifications\Actions\Action as NotificationAction;

class LaporanResource extends Resource
{
    // 1. Arahkan ke Model Penerimaan
    protected static ?string $model = Penerimaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    // 2. Ganti label agar lebih deskriptif
    protected static ?string $modelLabel = 'Rekapitulasi Penerimaan';
    protected static ?string $pluralModelLabel = 'Rekapitulasi Penerimaan Tunai';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Laporan';

    // 3. Form kita kosongkan karena tidak ada 'Create' atau 'Edit'
    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Tampilkan data penerimaan seperti biasa
            ->striped()
            ->columns([
                TextColumn::make('no')->label('No.')->rowIndex(),
                TextColumn::make('tanggal')->date('d F Y')->sortable(),
                TextColumn::make('no_rm')->label('No. RM')->searchable(),
                TextColumn::make('nama_pasien')->label('Nama Pasien')->searchable(),
                TextColumn::make('total_tarif')
                    ->label('Total (Rp)')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format($state, 0, ',', '.')),
                TextColumn::make('poli.nama_poli')->label('Poli'), // Asumsi ada relasi 'poli'
            ])
            ->filters([
                // (Anda bisa tambahkan filter tahun di sini jika mau)
            ])
            // 4. Hapus semua record action
            ->actions([
                // Kosong
            ])
            // 5. Hapus semua bulk action
            ->bulkActions([
                // Kosong
            ]);
    }

    // 6. Tentukan halaman mana yang digunakan
    public static function getPages(): array
    {
        return [
            'index' => ListLaporans::route('/'),
        ];
    }

    // 7. Cegah tombol "Create" bawaan
    public static function canCreate(): bool
    {
        return false;
    }
}