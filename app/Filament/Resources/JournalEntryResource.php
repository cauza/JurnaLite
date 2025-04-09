<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Models\JournalEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Jurnal Umum';
    protected static ?string $navigationGroup = 'Akuntansi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->columnSpan(3)
                    ->columns(12)
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->label('Referensi')
                    ->columnSpan(9)
                    ->columns(12)
                    ->nullable(),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpan(12)
                    ->required(),
                Forms\Components\Repeater::make('details')
                    ->label('Baris Jurnal')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('account_id')
                            ->label('Akun')
                            ->relationship(
                                name: 'account',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query->whereNotNull('parent_id')
                            )
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->code} - {$record->name}")
                            ->searchable()
                            ->preload()
                            ->columnSpan(6)
                            ->required(),
                        Forms\Components\TextInput::make('debit')
                            ->label('Debit')
                            // ->mask(RawJs::make('$money($input)'))
                            // ->stripCharacters(',')
                            // ->dehydrateStateUsing(fn($state) => floatval(str_replace(',', '.', preg_replace('/[^\d,]/', '', $state))))
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->columnSpan(3)
                            ->required(),
                        Forms\Components\TextInput::make('credit')
                            ->label('Kredit')
                            // ->mask(RawJs::make('$money($input)'))
                            // ->stripCharacters(',')
                            // ->dehydrateStateUsing(fn($state) => (float) str_replace(',', '', $state))
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->columnSpan(3)
                            ->required(),
                    ])
                    ->columnSpan(12)
                    ->columns(12)
                    ->minItems(2)
                    ->reorderable()
                    ->createItemButtonLabel('Tambah Baris'),
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id())
                    ->dehydrated(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                JournalEntry::query()
                    ->withSum('details', 'debit')
                    ->withSum('details', 'credit')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('Tanggal')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('reference')->label('Referensi')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('details_sum_debit')->label('Total Debit')->money('IDR')->alignment('right'),
                Tables\Columns\TextColumn::make('details_sum_credit')->label('Total Kredit')->money('IDR')->alignment('right'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
            'detail' => Pages\ViewJournalEntry::route('/{record}'),
        ];
    }
}