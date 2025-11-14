<?php

namespace App\Filament\Resources;

use Filament\Forms\Form;
use App\Models\Pelayanan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\PelayananResource\Pages\ListPelayanans;

class PelayananResource extends Resource
{
    protected static ?string $model = Pelayanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $recordTitleAttribute = 'pelayanan';
    protected static ?string $modelLabel = 'Manajemen Pelayanan';
    protected static ?string $navigationLabel = 'Pemeriksaan';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Pelayanan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pelayanan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('poli_id')
                                    ->label('Poli')
                                    ->relationship('poli', 'nama_poli')
                                    ->required(),
                                TextInput::make('jenis_pelayanan')
                                    ->label('Jenis Pelayanan')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('jenis_pemeriksaan')
                                    ->label('Jenis Pemeriksaan')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('kode_rekening')
                                    ->label('Kode Rekening')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('tarif_layanan')
                                    ->label('Tarif Pelayanan')
                                    ->integer()
                                    ->prefix('Rp.')
                                    ->required()
                                    ->maxLength(255),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pelayanan')
            ->striped()
            ->columns([
                TextColumn::make('poli.nama_poli')
                    ->label('Poli') // Mengubah judul kolom
                    ->searchable() // Agar bisa dicari berdasarkan nama penanggung jawab
                    ->placeholder('Belum Ditentukan'),
                TextColumn::make('jenis_pelayanan')
                    ->label('Jenis Pelayanan')
                    ->searchable(),
                TextColumn::make('jenis_pemeriksaan')
                    ->label('Jenis Pemeriksaan')
                    ->searchable(),
                TextColumn::make('kode_rekening')
                    ->label('Kode Rekening')
                    ->searchable(),
                TextColumn::make('tarif_layanan')
                    ->label('Tarif (Rp.)')
                    ->numeric()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->label('Filter Kategori')
                    ->relationship('poli', 'nama_poli')
            ])
            ->actions([
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
            'index' => ListPelayanans::route('/'),
        ];
    }
}
