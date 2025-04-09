<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Models\Account;
use App\Models\JournalDetail;
use Illuminate\Support\Collection;

class ProfitLossReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.profit-loss-report';
    protected static ?string $title = 'Laba Rugi';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public Collection $revenues;
    public Collection $expenses;

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->endOfMonth()->format('Y-m-d'),
        ]);

        $this->loadData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('start_date')->label('Dari Tanggal')->displayFormat('d/m/Y')
                ->native(false)->required(),
                Forms\Components\DatePicker::make('end_date')->label('Sampai Tanggal')->displayFormat('d/m/Y')
                ->native(false)->required(),
            ])
            ->statePath('data');
    }


    public function submit(): void
    {
        $this->loadData();
    }

    protected function loadData(): void
    {
        $start = $this->data['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->data['end_date'] ?? now()->endOfMonth()->format('Y-m-d');

        $this->revenues = Account::with(['children.journalDetails' => function ($q) use ($start, $end) {
            $q->whereHas('journalEntry', function ($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            });
        }])
            ->where('type', 'revenue')
            ->whereNull('parent_id')
            ->get();

        $this->expenses = Account::with(['children.journalDetails' => function ($q) use ($start, $end) {
            $q->whereHas('journalEntry', function ($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            });
        }])
            ->where('type', 'expense')
            ->whereNull('parent_id')
            ->get();
    }

    protected function getTotal(Account $account, string $field = 'credit'): float
    {
        return $account->children->sum(function ($child) use ($field) {
            return $child->journalDetails->sum($field);
        });
    }
}
