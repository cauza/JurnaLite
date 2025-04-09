<div>
    <h2 class="text-lg font-bold mb-2">Jurnal #{{ $entry->id }}</h2>
    <p><strong>Tanggal:</strong> {{ $entry->date->format('d/m/Y') }}</p>
    <p><strong>Referensi:</strong> {{ $entry->reference }}</p>
    <p><strong>Deskripsi:</strong> {{ $entry->description }}</p>

    <hr class="my-3">

    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Akun</th>
                <th class="border px-2 py-1 text-right">Debit</th>
                <th class="border px-2 py-1 text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entry->details as $detail)
                <tr>
                    <td class="border px-2 py-1">{{ $detail->account->code }} - {{ $detail->account->name }}</td>
                    <td class="border px-2 py-1 text-right">{{ number_format($detail->debit, 0, ',', '.') }}</td>
                    <td class="border px-2 py-1 text-right">{{ number_format($detail->credit, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
