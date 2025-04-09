<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationGroup = 'Manajemen User';
    protected static ?string $navigationLabel = 'Role';
    protected static ?string $label = 'Role';
    protected static ?string $pluralLabel = 'Roles';
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('administrator');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Role')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\CheckboxList::make('permissions')
                    ->label('Permissions')
                    ->relationship('permissions', 'name')
                    ->columns(2)
                    ->searchable()
                    ->helperText('Pilih permission untuk role ini.'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Role')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->badge()
                    ->separator(', ')
                    ->limitList(5),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y, H:i'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make() // Contoh: role 'administrator' tidak bisa dihapus
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

