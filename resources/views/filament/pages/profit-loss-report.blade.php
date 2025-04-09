<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <div class="mt-4 py-4 bg-blue-600 text-white rounded">
            <x-filament::button type="submit">
                Tampilkan Laporan
            </x-filament::button>
        </div>
    </form>

    @if ($revenues->count() || $expenses->count())
        <div class="mt-10 space-y-6">
            <h2 class="text-xl font-bold">Laporan Laba Rugi</h2>
            <p class="text-sm text-gray-600">Periode: {{ $data['start_date'] ?? '' }} s.d {{ $data['end_date'] ?? '' }}</p>

            <div class="border rounded p-4 bg-white dark:bg-gray-900 shadow">
                <h3 class="text-lg font-semibold">Pendapatan (Revenue)</h3>
                @php $totalRevenue = 0; @endphp
                @foreach ($revenues as $parent)
                    <div class="font-semibold mt-2 text-gray-700 dark:text-gray-100">{{ $parent['code'] }} {{ $parent['name'] }}</div>
                    @foreach ($parent->children as $child)
                        @php
                            $amount = $child->journalDetails->sum('credit');
                            $totalRevenue += $amount;
                        @endphp
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>{{ $child['code'] }} - {{ $child['name'] }}</span>
                            <span>Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-bold mt-4 pt-2 border-t text-green-600 dark:text-green-400">
                        <span>Total {{ $parent->name }}</span>
                        <span>Rp {{ number_format($parent->children->sum(fn($c) => $c->journalDetails->sum('credit')), 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border rounded p-4 bg-white dark:bg-gray-900 shadow">
                <h3 class="text-lg font-semibold mt-6">Beban (Expense)</h3>
                @php $totalExpense = 0; @endphp
                @foreach ($expenses as $parent)
                    <div class="mt-2 font-semibold">{{ $parent->name }}</div>
                    @foreach ($parent->children as $child)
                        @php
                            $amount = $child->journalDetails->sum('debit');
                            $totalExpense += $amount;
                        @endphp
                        <div class="flex justify-between text-gray-600 dark:text-gray-300">
                            <span>{{ $child->code }} - {{ $child->name }}</span>
                            <span>Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between font-bold mt-4 pt-2 border-t text-green-600 dark:text-green-400">
                        <span>Total {{ $parent->name }}</span>
                        <span>Rp {{ number_format($parent->children->sum(fn($c) => $c->journalDetails->sum('debit')), 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t pt-4 mt-4 text-lg font-bold flex justify-between">
                <span>Laba/Rugi:</span>
                <span>Rp {{ number_format($totalRevenue - $totalExpense, 0, ',', '.') }}</span>
            </div>
        </div>
    @endif
</x-filament::page>
