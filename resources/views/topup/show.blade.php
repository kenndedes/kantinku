<x-app-layout>
<div class="max-w-lg mx-auto py-8 space-y-6">

@php
    $status       = $transaction->payment_status;
    $methodInfo   = $methods[$transaction->payment_method] ?? ['label' => $transaction->payment_method, 'icon' => '💳'];
@endphp

    {{-- Status Card --}}
    @if($status === 'approved')
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-300 rounded-2xl p-8 text-center shadow">
        <div class="text-5xl mb-3">✅</div>
        <h2 class="text-2xl font-bold text-green-900">Top Up Disetujui!</h2>
        <p class="text-green-700 mt-2">
            Rp {{ number_format($transaction->amount, 0, ',', '.') }} telah masuk ke saldo Anda.
        </p>
        <p class="text-sm text-green-600 mt-1">
            Disetujui oleh: {{ $transaction->reviewer?->name ?? 'Admin' }} · {{ $transaction->reviewed_at?->format('d M Y H:i') }}
        </p>
        <a href="{{ route('dashboard') }}"
           class="mt-6 inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-xl transition">
            Kembali ke Dashboard
        </a>
    </div>

    @elseif($status === 'rejected')
    <div class="bg-gradient-to-br from-red-50 to-rose-50 border-2 border-red-300 rounded-2xl p-8 text-center shadow">
        <div class="text-5xl mb-3">❌</div>
        <h2 class="text-2xl font-bold text-red-900">Top Up Ditolak</h2>
        @if($transaction->admin_note)
        <p class="text-red-700 mt-2 text-sm">Alasan: <span class="font-semibold">{{ $transaction->admin_note }}</span></p>
        @endif
        <p class="text-sm text-red-500 mt-1">
            Oleh: {{ $transaction->reviewer?->name ?? 'Admin' }} · {{ $transaction->reviewed_at?->format('d M Y H:i') }}
        </p>
        <div class="mt-6 flex gap-3 justify-center">
            <a href="{{ route('topup.index') }}"
               class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl transition">
                Coba Lagi
            </a>
            <a href="{{ route('dashboard') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-xl transition">
                Dashboard
            </a>
        </div>
    </div>

    @else
    {{-- Waiting --}}
    <div class="bg-white rounded-2xl shadow p-6 text-center space-y-4">
        <div class="text-5xl">⏳</div>
        <h2 class="text-xl font-bold text-gray-900">Menunggu Konfirmasi Admin</h2>
        <p class="text-gray-500 text-sm">Permintaan top up Anda sedang ditinjau oleh admin. Biasanya diproses dalam beberapa menit.</p>
    </div>

    {{-- Transaction Details --}}
    <div class="bg-white rounded-2xl shadow divide-y divide-gray-100 overflow-hidden">
        <div class="px-5 py-4">
            <h3 class="font-semibold text-gray-700 mb-3">Detail Permintaan</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Nominal</dt>
                    <dd class="font-bold text-purple-700 text-base">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Metode</dt>
                    <dd class="font-semibold text-gray-800">{{ $methodInfo['icon'] }} {{ $methodInfo['label'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Waktu Kirim</dt>
                    <dd class="text-gray-700">{{ $transaction->created_at->format('d M Y, H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ref ID</dt>
                    <dd class="font-mono text-xs text-gray-500">{{ $transaction->ref_id }}</dd>
                </div>
            </dl>
        </div>

        {{-- Proof Image --}}
        @if($transaction->proof_image)
        <div class="px-5 py-4">
            <p class="text-sm font-semibold text-gray-700 mb-2">Bukti Pembayaran</p>
            <img src="{{ Storage::url($transaction->proof_image) }}"
                 alt="Bukti transfer"
                 class="rounded-xl max-h-60 border border-gray-200 w-full object-contain">
        </div>
        @endif
    </div>

    <div class="flex gap-3">
        <a href="{{ route('topup.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition text-sm">
            ← Kembali
        </a>
        <a href="{{ route('topup.history') }}" class="flex-1 text-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition text-sm">
            Riwayat Top Up
        </a>
    </div>
    @endif

</div>
</x-app-layout>
