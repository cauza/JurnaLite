<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        {{ $this->form }}
    </form>

    @if ($entries->isNotEmpty())
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-2">
                Buku Besar: {{ $this->getAccountName() }}
            </h2>
            <p class="mb-4 text-sm text-gray-500">
                Periode: {{ $start_date }} s/d {{ $end_date }}
            </p>

            <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow p-4">
                <table class="w-full text-sm">
                    <thead class="border-b font-bold text-left dark:text-gray-200">
                        <tr>
                            <th class="py-2">Tanggal</th>
                            <th>Referensi</th>
                            <th>Deskripsi</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Kredit</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 dark:text-gray-100">
                        @php $saldo = $this->saldo_awal; @endphp
                        <tr class="border-t bg-gray-100 dark:bg-gray-700">
                            <td colspan="5" class="text-right font-bold">Saldo Awal</td>
                            <td class="text-right font-bold">
                                Rp {{ number_format($saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                        @foreach ($entries as $entry)
                            @php
                                $saldo += $entry->debit - $entry->credit;
                            @endphp
                            <tr class="border-t">
                                <td class="py-1">
                                    <a href="{{ \App\Filament\Resources\JournalEntryResource::getUrl('edit', ['record' => $entry->journal_entry_id]) }}" class="text-blue-600 hover:underline">
                                        {{ $entry->journalEntry->date ?? '-' }}
                                    </a>
                                </td>
                                <td>{{ $entry->journalEntry->reference ?? '-' }}</td>
                                <td>{{ $entry->journalEntry->description ?? '-' }}</td>
                                <td class="text-right">Rp {{ number_format($entry->debit, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($entry->credit, 0, ',', '.') }}</td>
                                <td class="text-right font-bold">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>
