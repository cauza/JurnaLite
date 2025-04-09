<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Tables;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'Akun Perkiraan';
    protected static ?string $navigationGroup = 'Akuntansi';
    protected static ?int $navigationSort = 1;

    // public static function canAccess(): bool
    // {
    //     return auth()->user()?->hasRole('administrator');
    // }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return auth()->user()?->hasRole('administrator');
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'assets' => 'Assets',
                        'liabilities' => 'Liabilities',
                        'equity' => 'Equity',
                        'revenue' => 'Revenue',
                        'expense' => 'Expense',
                    ]),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Account')
                    ->relationship(
                        name: 'parent',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn($query) => $query->whereNull('parent_id')
                    )
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->code} - {$record->name}")
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')->sortable(),
                Tables\Columns\TextColumn::make('parent.name')->label('Parent'),
            ])
            ->filters([])
            ->actions([
                // Edit dan delete hanya untuk admin
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()->hasRole('administrator')),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record, Tables\Actions\DeleteAction $action) {
                        if ($record->journalDetails()->exists()) {
                        Notification::make()
                            ->title('Tidak bisa menghapus akun')
                            ->body('Akun ini memiliki data terkait dan tidak bisa dihapus.')
                            ->danger()
                            ->send();

                            $action->cancel();
                        }
                    })
                    ->visible(fn() => auth()->user()->hasRole('administrator')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()->hasRole('administrator')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
