<?php

namespace App\Filament\Resources;

use App\Models\Poli;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\PoliResource\Pages\ListPolis;

class PoliResource extends Resource
{
    protected static ?string $model = Poli::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static ?string $modelLabel = 'Poli Layanan';
    protected static ?string $navigationLabel = 'Poli';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Pelayanan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Periode Penerimaan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('nama_poli')
                                    ->label('Poli Layanan')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('pegawai_id')
                                    ->label('Penanggung Jawab')
                                    ->relationship('pegawai', 'nama_pegawai') // 'user' = nama method relasi, 'name' = kolom yg ingin ditampilkan
                                    ->searchable()
                                    ->required(),
                                Select::make('keterangan')
                                    ->label('Keterangan')
                                    ->options([
                                        'Aktif' => 'Aktif',
                                        'Tidak Aktif' => 'Tidak Aktif',
                                    ])
                                    ->native(false)
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('nama_poli')
                    ->label('Poli Layanan')
                    ->searchable(),
                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Penanggung Jawab') // Mengubah judul kolom
                    ->searchable() // Agar bisa dicari berdasarkan nama penanggung jawab
                    ->placeholder('Belum Ditentukan'),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
            ])
            ->filters([
                //
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
            'index' => ListPolis::route('/'),
        ];
    }
}
