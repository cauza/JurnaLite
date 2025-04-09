<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Spatie\Activitylog\Models\Activity;
use App\Filament\Resources\ActivityResource\Pages;
use Filament\Tables\Columns\TextColumn;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationGroup = 'Manajemen User';
    protected static ?string $label = 'Activity Log';
    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('causer.name')->label('User Name')->searchable(),
                TextColumn::make('causer.email')->label('User Email')->searchable(),
                TextColumn::make('description')->label('Action')->searchable(),
                TextColumn::make('subject_id')
                        ->label('Data Link')
                        ->url(fn(Activity $record) => self::generateLink($record))
                        ->openUrlInNewTab()
                        ->html(),
                TextColumn::make('subject_type')->label('Model'),
                TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y, H:i'),
            ])
            ->actions([
                Action::make('Lihat Data')
                    ->label('View Data')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Data')
                    ->modalSubheading(fn($record) => "Data ID: {$record->subject_id}")
                    ->modalContent(fn($record) => view('filament.components.activity-subject-view', [
                        'subject' => $record->subject,
                    ]))
                    ->visible(fn($record) => !is_null($record->subject))
                    ->color('primary'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected static function generateLink(Activity $record): ?string
    {
        if (!$record->subject_type || !$record->subject_id) {
            return null;
        }

        // Contoh untuk link resource berdasarkan tipe
        if (str_contains($record->subject_type, 'JournalEntry')) {
            return route('filament.admin.resources.journal-entries.detail', ['record' => $record->subject_id]);
        }

        // Tambah kondisi sesuai resource lainnya
        return null;
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
