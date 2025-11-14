<?php

namespace App\Filament\Widgets;

// --- TAMBAHKAN 'USE' STATEMENT INI ---
use App\Models\Penerimaan;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon; // <-- Tambahkan Carbon

class LatestOrders extends TableWidget
{
    // --- TAMBAHKAN PROPERTI INI ---
    // Atur judul widget
    protected static ?string $heading = 'Penerimaan Terakhir (Hari Ini)';

    // Atur agar widget memakan lebar penuh
    protected static ?int $sort = 2; // Urutan di dashboard
    protected int | string | array $columnSpan = 'full';
    // --- BATAS TAMBAHAN ---

    public function table(Table $table): Table
    {
        return $table
            // --- UBAH QUERY INI ---
            ->query(
                Penerimaan::query()
                    ->whereDate('tanggal', Carbon::today()) // Hanya hari ini
                    ->orderBy('id', 'desc') // Ambil yang terbaru
                    ->limit(5) // Batasi 5
            )
            // --- BATAS PERUBAHAN ---
            ->columns([
                TextColumn::make('nama_pasien')
                    ->label('Nama Pasien')
                    ->searchable(),
                TextColumn::make('no_rm')
                    ->label('No. RM')
                    ->searchable(),
                TextColumn::make('poli.nama_poli') 
                    ->label('Poli Tujuan'),
                TextColumn::make('total_tarif')
                    ->label('Total (Rp)')
                    ->alignRight()
                    ->formatStateUsing(fn ($state): string => number_format($state, 0, ',', '.')),
            ])
            // --- TAMBAHAN: Sembunyikan pagination ---
            ->paginated(false);
    }
}