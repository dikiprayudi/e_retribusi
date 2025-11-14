<?php

namespace App\Filament\Resources;

// --- PERBAIKAN: Mengelompokkan dan Memperbaiki 'use' statements ---
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pelayanan;
use App\Models\Penerimaan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\PenerimaanResource\Pages\EditPenerimaan;
use App\Filament\Resources\PenerimaanResource\Pages\ViewPenerimaan;
use App\Filament\Resources\PenerimaanResource\Pages\ListPenerimaans;
use App\Filament\Resources\PenerimaanResource\Pages\CreatePenerimaan;
// --- BATAS PERBAIKAN ---

class PenerimaanResource extends Resource
{
    protected static ?string $model = Penerimaan::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $recordTitleAttribute = 'penerimaan';
    protected static ?string $modelLabel = 'Penerimaan Tunai';
    protected static ?string $navigationLabel = 'Tunai';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Retribusi';

    public static function form(Form $form): Form
    {
        $updateBiaya = function ($set, $get) {
            $qty = (float) $get('qty') ?? 1;
            $tarif = (float) $get('tarif_satuan') ?? 0;
            $set('biaya', $qty * $tarif);
        };

        return $form
            ->schema([
                Section::make('Data Pasien')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                DatePicker::make('tanggal')
                                    ->displayFormat('d F Y')
                                    ->native(false)
                                    ->label('Tanggal Pelayanan')
                                    ->placeholder('-- Pilih Tanggal Disini --')
                                    ->required(),
                                TextInput::make('no_rm')
                                    ->label('No. Rekam Medik')
                                    ->placeholder('-- Masukan No. Rekam Medik --')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('nama_pasien')
                                    ->label('Nama Pasien')
                                    ->placeholder('-- Masukan Nama Lengkap --')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->placeholder('-- Pilih Jenis Kelamin --')
                                    ->options([
                                        'L' => 'L',
                                        'P' => 'P',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                        Grid::make(4)
                            ->schema([
                                Select::make('alamat')
                                    ->label('Alamat (Desa)')
                                    ->placeholder('-- Pilih Alamat/Desa --')
                                    ->options([
                                        'Cikelet' => 'Cikelet',
                                        'Cijambe' => 'Cijambe',
                                        'Pamalayan' => 'Pamalayan',
                                        'Linggamanik' => 'Linggamanik',
                                        'Karangsari' => 'Karangsari',
                                        'Ciroyom' => 'Ciroyom',
                                        'Tipar' => 'Tipar',
                                        'Awassagara' => 'Awassagara',
                                        'Luar Wilayah' => 'Luar Wilayah',
                                    ])
                                    ->required()
                                    ->native(false),
                                Select::make('jenis_kunjungan')
                                    ->label('Jenis Kunjungan')
                                    ->placeholder('-- Pilih Jenis Kunjungan --')
                                    ->options([
                                        'Rawat Jalan' => 'Rawat Jalan',
                                        'Rawat Inap' => 'Rawat Inap',
                                    ])
                                    ->required()
                                    ->native(false),
                                Select::make('status_pasien')
                                    ->label('Status Pasien')
                                    ->placeholder('-- Pilih Status Pasien --')
                                    ->options([
                                        'Umum' => 'Umum',
                                        'BPJS' => 'BPJS',
                                    ])
                                    ->required()
                                    ->native(false),
                                Select::make('poli_id')
                                    ->label('Poli Tujuan')
                                    ->placeholder('-- Pilih Poli Tujuan --')
                                    ->relationship('poli', 'nama_poli')
                                    ->searchable()
                                    ->preload() // <-- PERBAIKAN: Dihapus untuk menghilangkan "delay" modal
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($set) => $set('tindakan', []))
                            ]),
                    ])
                    ->columnSpan('full'),

                Section::make('Data Tindakan')
                ->schema([
                    Repeater::make('tindakan')
                        ->label('Daftar Tindakan')
                        ->schema([
                            Select::make('tindakan_id')
                                ->label('Nama Tindakan / Pelayanan')
                                ->options(function ($get): array {
                                    $poliId = $get('../../poli_id');
                                    if (!$poliId) {
                                        return [];
                                    }
                                    return \App\Models\Pelayanan::where('poli_id', $poliId)
                                        ->pluck('jenis_pemeriksaan', 'id')
                                        ->all();
                                })
                                ->searchable()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($set, $state) {
                                    $pelayanan = \App\Models\Pelayanan::find($state);
                                    $tarif = $pelayanan ? $pelayanan->tarif_layanan : 0;
                                    $set('tarif_satuan', $tarif);
                                    $set('qty', 1);
                                    $set('biaya', $tarif * 1);
                                    $set('nama_pelayanan', $pelayanan ? $pelayanan->jenis_pemeriksaan : '');
                                    $set('kode_rekening', $pelayanan ? $pelayanan->kode_rekening : '');
                                })
                                ->columnSpan(2),
                            TextInput::make('qty')
                                ->label('Qty')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->reactive()
                                ->live(onBlur: true)
                                ->afterStateUpdated($updateBiaya)
                                ->columnSpan(1),
                            TextInput::make('biaya')
                                ->label('Biaya')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->readOnly()
                                ->columnSpan(1),
                            Hidden::make('tarif_satuan'),
                            Hidden::make('nama_pelayanan'),
                            Hidden::make('kode_rekening'),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->addActionLabel('+ Tambah Tindakan')
                        ->reactive()
                        ->columnSpan('full'),

                    Grid::make(3)
                        ->schema([
                            Placeholder::make('total_tarif')
                                ->inlineLabel()
                                ->live(true)
                                ->label('Sub Total Penerimaan :')
                                ->content(function ($get): string {
                                    $data = $get('tindakan');
                                    if (!$data) {
                                        return 'Rp 0';
                                    }
                                    $total = collect($data)->sum(function($item) {
                                        return (float) ($item['biaya'] ?? 0);
                                    });
                                    return 'Rp ' . number_format($total, 0, ',', '.');
                                })
                                ->extraAttributes([
                                    'class' => 'text-xl font-bold text-left text-gray-700 dark:text-gray-200'
                                ]),
                            TextInput::make('diskon')
                                ->label('Diskon ( jika diberikan )')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->reactive()
                                ->extraAttributes([
                                    'class' => 'text-lg text-right'
                                ]),
                            Placeholder::make('grand_total')
                                // ->alignCenter() // <-- PERBAIKAN: Dihapus (Sintaks v2)
                                ->label('Grand Total :')
                                ->inlineLabel()
                                ->live(true)
                                ->content(function ($get): string {
                                    $tindakan = $get('tindakan');
                                    $subtotal = 0;
                                    if ($tindakan) {
                                        $subtotal = collect($tindakan)->sum(function($item) {
                                            return (float) ($item['biaya'] ?? 0);
                                        });
                                    }
                                    $diskon = (float) $get('diskon') ?? 0;
                                    $grandTotal = $subtotal - $diskon;
                                    return 'Rp ' . number_format($grandTotal, 0, ',', '.');
                                })
                                ->extraAttributes([
                                    'class' => 'text-xl font-bold text-left text-gray-700 dark:text-gray-200'
                                ]),
                        ]),
                ])
                ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('penerimaan')
            ->striped()
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('no')
                ->label('No.')
                ->rowIndex(),
                TextColumn::make('tanggal')
                ->label('Tanggal')
                ->date('d F Y')
                ->searchable()
                ->sortable(),
                TextColumn::make('nama_pasien')
                ->label('Nama Pasien')
                ->searchable(),
                TextColumn::make('no_rm')
                ->label('No. RM')
                ->alignCenter()
                ->searchable(),
                TextColumn::make('alamat')
                ->label('Alamat')
                ->searchable()
                ->sortable(),
                TextColumn::make('jenis_kelamin')
                ->label('JK')
                ->alignCenter()
                ->searchable()
                ->sortable(),
                TextColumn::make('jenis_kunjungan')
                ->label('Kunjungan')
                ->searchable(),
                TextColumn::make('status_pasien')
                ->label('Status')
                ->alignCenter()
                ->searchable(),
                TextColumn::make('poli.nama_poli')
                    ->label('Poli Tujuan')
                    ->searchable()
                    ->alignCenter()
                    ->sortable()
                    ->placeholder('Belum Ditentukan'),
                BadgeColumn::make('tindakan_count')
                    ->label('Tindakan Diberikan')
                    // 1. Logika untuk menghitung (count)
                    ->getStateUsing(fn ($record): int => count($record->tindakan ?? []))
                    
                    // 2. Tambahkan warna agar terlihat seperti badge
                    ->color('primary') 

                    // 3. Jadikan link ke halaman 'view'
                    // MENJADI INI
                    ->url(fn ($record): string => \App\Filament\Resources\PenerimaanResource::getUrl('view', ['record' => $record]))
                    // --- TAMBAHKAN KODE INI ---
                    ->tooltip(function ($record): string {
                        // Ambil daftar nama pelayanan dari array
                        $tindakanList = collect($record->tindakan)
                                        ->pluck('nama_pelayanan')
                                        ->all();
                        
                        // Gabungkan menjadi string, dipisah dengan baris baru
                        // agar rapi di tooltip
                        return implode("\n", $tindakanList);
                    })
                    // --- BATAS TAMBAHAN ---
                    
                    ->alignCenter(),
                TextColumn::make('total_tarif')
                    ->label('Total Tarif')
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->searchable()
                    ->alignRight(),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari Tanggal')
                            ->displayFormat('d F Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->required(),
                        DatePicker::make('created_until')
                            ->label('Sampai Tanggal')
                            ->displayFormat('d F Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->required(),
                    ])
                    ->query(function ($query, array $data): void {
                        $query
                            ->when(
                                $data['created_from'],
                                fn ($query) => $query->where('tanggal', '>=', $data['created_from'])
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query) => $query->where('tanggal', '<=', $data['created_until'])
                            );
                    }),
                ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // --- PERBAIKAN: Ganti 'DeleteAction' menjadi 'DeleteBulkAction' ---
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenerimaans::route('/'),
            'create' => CreatePenerimaan::route('/create'),
            'view' => ViewPenerimaan::route('/{record}'),
            'edit' => EditPenerimaan::route('/{record}/edit'),
        ];
    }
}