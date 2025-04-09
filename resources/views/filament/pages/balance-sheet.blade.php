<x-filament::page>
    <form wire:submit.prevent="loadData">
        {{ $this->form }}
    </form>

    <hr class="my-4">

    <h2 class="text-xl font-bold">Neraca</h2>
    <p class="text-sm text-gray-600">Periode: {{ $start_date }} s/d {{ $end_date }}</p>

    @php
        $sections = [
            'Assets' => $assets,
            'Liabilities' => $liabilities,
            'Equity' => $equity,
        ];
    @endphp

    @foreach ($sections as $title => $accounts)
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $title }}</h3>
            <div class="border rounded p-4 bg-white dark:bg-gray-900 shadow">
                @php $sectionTotal = 0; @endphp
                @foreach ($accounts as $account)
                    <div class="font-semibold mt-2 text-gray-700 dark:text-gray-100">{{ $account['code'] }} {{ $account['name'] }}</div>
                    <div class="ml-4">
                        @foreach ($account['children'] as $child)
                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>{{ $child['code'] }} - {{ $child['name'] }}</span>
                                <span>Rp {{ number_format($child['total'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between font-bold border-t pt-2 mt-2 text-gray-800 dark:text-gray-100">
                            <span>Total {{ $account['name'] }}</span>
                            <span>Rp {{ number_format($account['total'], 0, ',', '.') }}</span>
                        </div>
                        @php $sectionTotal += $account['total']; @endphp
                    </div>
                @endforeach
                <div class="flex justify-between font-bold mt-4 pt-2 border-t text-green-600 dark:text-green-400">
                    <span>Total {{ $title }}</span>
                    <span>Rp {{ number_format($sectionTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @if ($title === 'Assets')
        <div class="border-t mt-2 pt-2 font-bold text-lg flex justify-between text-blue-600 dark:text-blue-400">
            <span>Total Assets</span>
            <span>Rp {{ number_format($total_assets, 0, ',', '.') }}</span>
        </div>
        @endif
    @endforeach

    <div class="border-t mt-6 pt-4 font-bold text-lg flex justify-between">
        <span>Total Liabilities + Equity</span>
        <span>Rp {{ number_format($total_liabilities_equity, 0, ',', '.') }}</span>
    </div>
</x-filament::page>