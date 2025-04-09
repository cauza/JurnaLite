<?php

namespace App\Filament\Pages;

use App\Models\Account;
use App\Models\JournalDetail;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Collection;

class GeneralLedgerReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?string $title = 'Buku Besar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static string $view = 'filament.pages.general-ledger-report';

    public ?int $account_id = null;
    public ?string $start_date = null;
    public ?string $end_date = null;

    public Collection $entries;

    public function mount(): void
    {
        $this->entries = collect();
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('account_id')
                ->label('Akun')
                ->options(
                    Account::whereNotNull('parent_id')
                        ->get()
                        ->mapWithKeys(fn($account) => [
                            $account->id => "{$account->code} - {$account->name}"
                        ])
                )
                ->searchable()
                ->required(),
            Forms\Components\DatePicker::make('start_date')
                ->label('Tanggal Mulai')
                ->displayFormat('d/m/Y')
                ->native(false)
                ->required(),
            Forms\Components\DatePicker::make('end_date')
                ->label('Tanggal Akhir')
                ->displayFormat('d/m/Y')
                ->native(false)
                ->required(),
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('submit')
                    ->label('Tampilkan')
                    ->button()
                    ->submit('loadData'),
            ])->columnSpanFull()->alignEnd()
        ])->columns(3);
    }

    public function loadData(): void
    {
        $this->entries = JournalDetail::with('journalEntry')
            ->where('account_id', $this->account_id)
            ->whereHas('journalEntry', function ($query) {
                $query->whereBetween('date', [$this->start_date, $this->end_date]);
            })
            ->get()
            ->sortBy(function ($entry) {
                return $entry->journalEntry->date ?? '';
            })
            ->values(); // reset index
    }

    public function getAccountName(): string
    {
        return Account::find($this->account_id)?->name ?? '';
    }
}
