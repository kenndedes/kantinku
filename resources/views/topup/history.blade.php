<x-app-layout>
<div class="max-w-lg mx-auto py-8 space-y-5">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900">Riwayat Top Up</h1>
        <a href="{{ route('topup.index') }}" class="text-sm text-purple-600 hover:underline">+ Request Top Up</a>
    </div>

    @if($transactions->isEmpty())
        <div class="bg-white rounded-2xl shadow p-10 text-center text-gray-400">Belum ada riwayat top up.</div>
    @else
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <ul class="divide-y divide-gray-100">
            @foreach ($transactions as $tx)
            @php
                $badge = match($tx->payment_status) {
                    'approved'       => 'bg-green-100 text-green-700',
                    'rejected'       => 'bg-red-100 text-red-700',
                    'waiting_review' => 'bg-yellow-100 text-yellow-700',
                    default          => 'bg-gray-100 text-gray-600',
                };
                $label = match($tx->payment_status) {
                    'approved'       => '✅ Disetujui',
                    'rejected'       => '❌ Ditolak',
                    'waiting_review' => '⏳ Menunggu',
                    default          => $tx->payment_status,
                };
                $methodLabel = \App\Http\Controllers\TopUpController::$methods[$tx->payment_method]['label'] ?? $tx->payment_method;
            @endphp
            <li>
                <a href="{{ route('topup.show', $tx) }}" class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800">Rp {{ number_format($tx->amount, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $tx->created_at->format('d M Y, H:i') }} · {{ $methodLabel }}</p>
                        @if($tx->payment_status === 'rejected' && $tx->admin_note)
                            <p class="text-xs text-red-500 mt-0.5">Alasan: {{ $tx->admin_note }}</p>
                        @endif
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }} whitespace-nowrap shrink-0">{{ $label }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    <div>{{ $transactions->links() }}</div>
    @endif

</div>
</x-app-layout>
