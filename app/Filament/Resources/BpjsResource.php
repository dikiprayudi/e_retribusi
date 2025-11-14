<?php

namespace App\Filament\Resources;

use App\Models\Bpjs;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\KategoriBpjs;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\BpjsResource\Pages\ListBpjs;
use App\Filament\Resources\BpjsResource\Pages\EditBpjs;
use App\Filament\Resources\BpjsResource\Pages\CreateBpjs;

class BpjsResource extends Resource
{
    protected static ?string $model = Bpjs::class;

    // MENJADI INI (Sintaks v3):
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $recordTitleAttribute = 'bpjs';

    protected static ?string $modelLabel = 'Penerimaan Non Tunai';

    protected static ?string $navigationLabel = 'Non Tunai';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Retribusi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Pasien')
                    ->schema([
                        Grid::make(2) // 4 Kolom
                            ->schema([
                                DatePicker::make('tanggal')
                                    ->label('Tanggal Penerimaan')
                                    ->displayFormat('d F Y')
                                    ->placeholder('-- Pilih Tanggal Disini --')
                                    ->native(false)
                                    ->required(), // Otomatis isi tanggal hari ini

                                TextInput::make('no_sts')
                                    ->label('No STS')
                                    ->placeholder('Silahkan Masukan No STS')
                                    ->required(),

                                Select::make('kategori')
                                    ->label('Kategori')
                                    ->placeholder('-- Pilih Kategori --')
                                    ->options(KategoriBpjs::class) // <-- Otomatis ambil dari Enum
                                    ->required(),

                                TextInput::make('jumlah')
                                    ->label('Jumlah (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                TextInput::make('bank')
                                    ->label('Nama Bank')
                                    ->default('BJB')
                                    ->required(),
                                TextInput::make('no_rekening')
                                    ->label('No. Rekening')
                                    ->default('0059355228001')
                                    ->required(),

                                TextArea::make('uraian')
                                    ->label('Uraian Penerimaan')
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('keterangan')
                                    ->label('Keterangan (Optional)')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan('full'),
                // Ambil lebar penuh
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bpjs')
            ->striped()
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal Penerimaan')
                    ->date('d F Y')
                    ->searchable(),

                TextColumn::make('no_sts')
                    ->label('No STS'),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->searchable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah (Rp)')
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->searchable(),

                TextColumn::make('bank')
                    ->label('Nama Bank')
                    ->searchable(),

                TextColumn::make('no_rekening')
                    ->label('No. Rekening')
                    ->searchable(),

                TagsColumn::make('uraian')
                    ->label('Uraian Penerimaan')
                    ->searchable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
            ])
            ->filters([
            // INI BAGIAN PENTING UNTUK ANDA
            SelectFilter::make('kategori')
                ->label('Filter Kategori')
                ->options(KategoriBpjs::class)
            ])
            ->actions([
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
            // GANTI 'index' dari ManageBpjs menjadi:
            'index' => ListBpjs::route('/'),
            //'create' => CreateBpjs::route('/create'), // Tombol Create akan mengarah ke sini
            //'edit' => EditBpjs::route('/{record}/edit'), // Tombol Edit akan mengarah ke sini
            // 'view' => ViewBpjs::route('/{record}'), // Jika Anda menambahkan ViewAction
        ];
    }
}
