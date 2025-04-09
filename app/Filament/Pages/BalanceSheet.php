<?php

namespace App\Filament\Pages;

use App\Models\Account;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;

class BalanceSheet extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $title = 'Neraca';
    protected static ?string $navigationGroup = 'Laporan';
    protected static string $view = 'filament.pages.balance-sheet';

    public ?string $start_date = null;
    public ?string $end_date = null;

    public array $assets = [];
    public array $liabilities = [];
    public array $equity = [];
    public float $net_income = 0;
    public float $total_assets = 0;
    public float $total_liabilities_equity = 0;

    public function mount(): void
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
        $this->loadData();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
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
                ])->columnSpan(1)->alignEnd(),
                ]);
    }

    public function loadData()
    {
        $this->assets = $this->getAccountsByType('assets');
        $this->liabilities = $this->getAccountsByType('liabilities');
        $this->equity = $this->getAccountsByType('equity');
        $this->net_income = $this->calculateNetIncome();

        // Hitung total asset
        $this->total_assets = collect($this->assets)->sum('total');

        // Tambahkan laba/rugi periode berjalan ke equity
        $this->equity[] = [
            'name' => 'Laba/Rugi Periode Berjalan',
            'code' => '',
            'total' => $this->net_income,
            'children' => []
        ];

        $this->total_liabilities_equity = collect($this->liabilities)->sum('total') + collect($this->equity)->sum('total');
    }

    public function getAccountsByType($type): array
    {
        $accounts = Account::with(['children.journalDetails.journalEntry', 'journalDetails.journalEntry'])
            ->whereNull('parent_id')
            ->where('type', $type)
            ->get();

        return $accounts->map(function ($account) {
            return [
                'name' => $account->name,
                'code' => $account->code,
                'total' => $this->calculateAccountTotal($account),
                'children' => $account->children->map(function ($child) {
                    return [
                        'name' => $child->name,
                        'code' => $child->code,
                        'total' => $this->sumJournalDetails($child),
                    ];
                })->toArray()
            ];
        })->toArray();
    }

    public function calculateAccountTotal($account): float
    {
        $total = $this->sumJournalDetails($account);

        foreach ($account->children as $child) {
            $total += $this->sumJournalDetails($child);
        }

        return $total;
    }

    protected function sumJournalDetails($account): float
    {
        return $account->journalDetails
            ->filter(function ($detail) {
                $date = $detail->journalEntry->date ?? null;
                return $date >= $this->start_date && $date <= $this->end_date;
            })
            ->sum(function ($detail) use ($account) {
                $type = $account->type;

                if (in_array($type, ['assets', 'expense'])) {
                    return $detail->debit - $detail->credit;
                } elseif (in_array($type, ['liabilities', 'equity', 'revenue'])) {
                    return $detail->credit - $detail->debit;
                }

                return 0;
            });
    }

    protected function calculateNetIncome(): float
    {
        $revenues = Account::with(['journalDetails.journalEntry'])
            ->where('type', 'revenue')
            ->get();

        $expenses = Account::with(['journalDetails.journalEntry'])
            ->where('type', 'expense')
            ->get();

        $totalRevenue = $revenues->sum(function ($account) {
            return $this->sumJournalDetails($account); // revenue: credit - debit
        });

        $totalExpense = $expenses->sum(function ($account) {
            return $this->sumJournalDetails($account); // expense: debit - credit
        });

        return $totalRevenue - $totalExpense;
    }
}