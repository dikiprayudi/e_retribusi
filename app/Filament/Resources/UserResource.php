<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\UserResource\Pages\ListUsers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Manajemen Pengguna';

    protected static ?string $recordTitleAttribute = 'user';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationGroup = 'Setting';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Pengguna')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->maxLength(255)
                    ->unique(),
                TextInput::make('password')
                    ->label('Password')
                    ->password() // 1. Mengubah input menjadi type="password"
                    ->revealable() // 2. Ini dia! Menambahkan ikon mata (show/hide)
                    ->required(fn(string $context): bool => $context === 'create') // Wajib hanya saat buat user baru
                    ->minLength(8)
                    ->dehydrateStateUsing(fn($state) => Hash::make($state)) // Otomatis hash password saat disimpan
                    ->dehydrated(fn($state) => filled($state)), // Hanya simpan jika diisi (penting saat edit)

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password() // 1. Mengubah input menjadi type="password"
                    ->revealable() // 2. Ikon mata juga untuk konfirmasi
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrated(false) // Jangan simpan field ini ke database
                    ->same('password'), // Validasi: harus sama dengan field 'password'
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->recordTitleAttribute('user')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name') // 1. Ambil "name" dari relasi "roles"
                    ->label('Role')
                    ->badge() // 2. Tampilkan sebagai badge (pil)
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
        ];
    }
}
