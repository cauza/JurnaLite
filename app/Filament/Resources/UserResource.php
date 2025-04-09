<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteAction;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\UserResource\Pages;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen User';
    protected static ?string $modelLabel = 'User';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->label('Nama')->required(),
                    TextInput::make('email')->email()->required(),
                ]),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context) => $context === 'create'),
                Select::make('roles')
                    ->label('Role')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->visible(auth()->user()->hasRole('administrator')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('roles.name')->label('Role')->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(
                        fn(User $record) =>
                        auth()->user()->hasRole('administrator') || auth()->id() === $record->id
                    ),

                Tables\Actions\EditAction::make()
                    ->visible(
                        fn(User $record) =>
                        auth()->user()->hasRole('administrator') || auth()->id() === $record->id
                    ),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn(User $record) => auth()->user()->hasRole('administrator'))
                    ->before(function (User $record, Tables\Actions\DeleteAction $action) {
                        if ($record->journalEntries()->exists()) {
                            Notification::make()
                                ->title('Tidak bisa menghapus user')
                                ->body('User ini memiliki data terkait dan tidak bisa dihapus.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Jika user bukan administrator, tampilkan hanya dirinya sendiri
        if (!auth()->user()->hasRole('administrator')) {
            $query->where('id', auth()->id());
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('administrator');
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
