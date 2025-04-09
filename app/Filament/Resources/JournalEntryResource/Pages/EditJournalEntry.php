<?php

namespace App\Filament\Resources\JournalEntryResource\Pages;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournalEntry extends EditRecord
{
    protected static string $resource = JournalEntryResource::class;

    protected function afterSave(): void
    {
        $this->redirect(JournalEntryResource::getUrl());
    }

    protected function beforeSave(): void
    {
        $totalDebit = collect($this->data['details'])->sum('debit');
        $totalCredit = collect($this->data['details'])->sum('credit');

        if ($totalDebit !== $totalCredit) {
            Notification::make()
                ->title('Total debit dan kredit tidak seimbang!')
                ->danger()
                ->body("Total Debit: " . number_format($totalDebit, 2, '.', ',') . " | Total Kredit: " . number_format($totalCredit, 2, '.', ','))
                ->persistent()
                ->send();

            $this->halt(); // Hentikan proses simpan
        }

        $this->data['user_id'] = Auth::id(); // Pastikan user_id tersimpan
    }

}
