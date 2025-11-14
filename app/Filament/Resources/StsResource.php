<?php

namespace App\Filament\Resources;

use App\Models\Sts;
use Filament\Tables;
use App\Models\Pegawai;
use Filament\Forms\Form;
use App\Models\Penerimaan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
// Hapus 'use Hidden' jika ada, kita tidak membutuhkannya lagi
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Resources\StsResource\Pages\ListSts;

class StsResource extends Resource
{
    protected static ?string $model = Sts::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $recordTitleAttribute = 'no_sts';
    protected static ?string $modelLabel = 'Register STS';
    protected static ?string $navigationLabel = 'STS';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Laporan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        // --- Fungsi ini sudah benar, hanya ganti nama field ---
        $recalculateTotal = function ($set, $get) {
            $tanggalMulai = $get('periode_awal');
            $tanggalSelesai = $get('periode_akhir');

            if (!$tanggalMulai || !$tanggalSelesai) {
                $set('total_disetor', 0); // <-- UBAH KE total_disetor
                return;
            }

            $total = Penerimaan::whereBetween('tanggal', [
                $tanggalMulai,
                $tanggalSelesai
            ])->sum('total_tarif');
            
            $set('total_disetor', $total); // <-- UBAH KE total_disetor
        };
        // ---
        return $form
            ->schema([
                Section::make('Periode Penerimaan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('periode_awal')
                                    ->displayFormat('d F Y')
                                    ->native(false)
                                    ->label('Periode Mulai')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated($recalculateTotal),
                                DatePicker::make('periode_akhir')
                                    ->displayFormat('d F Y')
                                    ->native(false)
                                    ->label('Periode Selesai')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated($recalculateTotal),
                            ]),
                         Grid::make(2)
                            ->schema([
                                TextInput::make('no_sts')
                                    ->label('No STS')
                                    ->required(),
                                DatePicker::make('tanggal_setor')
                                    ->displayFormat('d F Y')
                                    ->native(false)
                                    ->label('Tanggal Penyetoran')
                                    ->required(),
                            ]),
                            Grid::make(2)
                            ->schema([
                                TextInput::make('bank')
                                    ->label('Nama Bank')
                                    ->default('BJB')
                                    ->required(),
                                TextInput::make('no_rekening')
                                    ->label('No. Rekening')
                                    ->default('0059355228001')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan('full'),
                 Grid::make(2)->schema([
                    Grid::make(2)->schema([
                        Select::make('pengguna_anggaran_id')
                            ->label('Nama Pengguna Anggaran')
                            ->options(Pegawai::pluck('nama_pegawai', 'id'))
                            ->relationship('penggunaAnggaran', 'nama_pegawai')
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(
                                function($set, $state) {
                                    $pegawai = Pegawai::find($state);
                                    $set('pengguna_anggaran_nip', $pegawai?->nip ?? '');
                                    // --- HAPUS BARIS 'pangkat' ---
                                }
                            ),
                        TextInput::make('pengguna_anggaran_nip')
                            ->label('NIP')
                            ->required()
                            ->readOnly(),
                        // --- HAPUS 'Hidden::make(pangkat)' ---
                    ]),
                    Grid::make(2)->schema([
                        Select::make('bendahara_id')
                            ->label('Nama Bendahara Penerimaan')
                            ->options(Pegawai::pluck('nama_pegawai', 'id'))
                            ->relationship('bendahara', 'nama_pegawai')
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(
                                function($set, $state) {
                                    $pegawai = Pegawai::find($state);
                                    $set('bendahara_nip', $pegawai?->nip ?? '');
                                    // --- HAPUS BARIS 'pangkat' ---
                                }
                            ),
                        TextInput::make('bendahara_nip')
                            ->label('NIP')
                            ->required()
                            ->readOnly(),
                        // --- HAPUS 'Hidden::make(pangkat)' ---
                    ]),
                ])
                ->columnSpan('full'),
                 Grid::make(1)->schema([
                        TextInput::make('total_disetor') // <-- UBAH KE total_disetor
                        ->numeric()
                        ->prefix('Rp')
                        ->readOnly()
                        ->required()
                        ->default(0),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('no_sts')
            ->striped()
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('no_sts')
                    ->label('No. STS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_setor')
                    ->label('Tanggal Setor')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('bank')
                    ->label('Bank')
                    ->searchable(),
                TextColumn::make('no_rekening')
                    ->label('No. Rekening')
                    ->searchable(),
                TextColumn::make('total_disetor') // <-- PASTIKAN INI total_disetor
                    ->label('Total Penyetoran (Rp)')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn($state): string => number_format($state, 0, ',', '.')),
                TextColumn::make('periode_awal')
                    ->label('Awal Periode')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('periode_akhir')
                    ->label('Akhir Periode')
                    ->date('d F Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
                Tables\Actions\Action::make('cetakSts')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->iconButton()
                    ->url(fn(Sts $record) => route('print.sts.register', $record), shouldOpenInNewTab: true),
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
            'index' => ListSts::route('/'),
        ];
    }
}