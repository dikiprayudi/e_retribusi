<?php

namespace App\Filament\Resources;

use App\Models\Pegawai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\PegawaiResource\Pages\ListPegawais;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $recordTitleAttribute = 'pegawai';

    protected static ?string $modelLabel = 'Manajemen Pegawai';

    protected static ?string $navigationLabel = 'Pegawai';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Setting';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_pegawai')
                    ->label('Nama Pegawai')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nip')
                        ->label('NIP')
                        ->required()
                        ->maxLength(255),
                TextInput::make('pangkat_golongan')
                        ->label('Pangkat / Gol. Ruang')
                        ->required()
                        ->maxLength(255),
                TextInput::make('jabatan')
                        ->label('Jabatan')
                        ->required()
                        ->maxLength(255),
                Select::make('status_pegawai')
                        ->label('Status Pegawai')
                        ->options([
                            'Aktif' => 'Aktif',
                            'Tidak Aktif' => 'Tidak Aktif',
                        ])
                        ->native(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('nama_pegawai')
                    ->label('Nama Pegawai')
                    ->searchable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                TextColumn::make('pangkat_golongan')
                    ->label('Pangkat / Gol. Ruang')
                    ->searchable(),
                TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable(),
                TextColumn::make('status_pegawai')
                    ->label('Status Pegawai')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status_pegawai')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Tidak Aktif' => 'Tidak Aktif',
                    ])
            ])
            ->Actions([
                ViewAction::make()
                ->iconButton(),
                EditAction::make()
                ->iconButton(),
                DeleteAction::make()
                ->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // --- PERBAIKAN: Ganti 'DeleteAction' menjadi 'DeleteBulkAction' ---
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPegawais::route('/'),
        ];
    }
}