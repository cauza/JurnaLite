@if ($subject)
    <div class="space-y-2">
        <div><strong>ID:</strong> {{ $subject->id }}</div>
        <div><strong>Created At:</strong> {{ $subject->created_at }}</div>

        {{-- Tampilkan field penting lainnya sesuai model subject --}}
        @if (method_exists($subject, 'toArray'))
            <div class="text-sm bg-gray-100 p-2 rounded dark:bg-gray-800">
                <pre class="whitespace-pre-wrap">{{ json_encode($subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif
    </div>
@else
    <div>Data tidak ditemukan atau sudah dihapus.</div>
@endif


