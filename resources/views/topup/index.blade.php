<x-app-layout>
<div class="max-w-2xl mx-auto space-y-6 py-6">

    {{-- Balance Card --}}
    <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-6 text-white shadow-lg">
        <p class="text-sm opacity-80">Saldo Anda</p>
        <p class="text-3xl font-bold mt-1">Rp {{ number_format((float) auth()->user()->balance, 0, ',', '.') }}</p>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
            <ul class="list-disc ps-5 space-y-1">
                @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow p-6 space-y-6">
        <h2 class="text-xl font-bold text-gray-900">Request Top Up Saldo</h2>

        <form method="POST" action="{{ route('topup.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Nominal --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal Top Up</label>
                <div class="grid grid-cols-3 gap-2 mb-3">
                    @foreach ([10000, 20000, 50000, 100000, 200000, 500000] as $nominal)
                    <button type="button" onclick="setAmount({{ $nominal }})"
                            class="quick-btn border-2 border-gray-200 rounded-xl py-3 px-2 text-center text-sm font-bold text-gray-700
                                   hover:border-purple-500 hover:bg-purple-50 transition-all">
                        Rp {{ number_format($nominal, 0, ',', '.') }}
                    </button>
                    @endforeach
                </div>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rp</span>
                    <input type="number" name="amount" id="amount" required
                           min="10000" max="5000000" step="1000"
                           value="{{ old('amount') }}"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-400 focus:border-purple-400 outline-none text-lg font-semibold"
                           placeholder="10.000">
                </div>
                <p class="text-xs text-gray-400 mt-1">Min Rp 10.000 · Maks Rp 5.000.000</p>
            </div>

            {{-- Payment Method --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($methods as $key => $method)
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="{{ $key }}"
                               {{ old('payment_method') === $key ? 'checked' : '' }}
                               class="sr-only peer" onchange="showAccountInfo()">
                        <div class="peer-checked:border-purple-600 peer-checked:bg-purple-50 border-2 border-gray-200
                                    rounded-xl p-3 flex items-center gap-3 hover:border-purple-400 transition-all">
                            <span class="text-xl">{{ $method['icon'] }}</span>
                            <span class="font-semibold text-sm text-gray-800">{{ $method['label'] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Account info per method --}}
                @foreach ($methods as $key => $method)
                <div id="info-{{ $key }}" class="method-info hidden mt-3 bg-purple-50 border border-purple-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-purple-800">{{ $method['icon'] }} {{ $method['label'] }}</p>
                    <p class="text-sm text-purple-700 mt-1">
                        Transfer ke: <span class="font-bold select-all">{{ $method['account'] }}</span>
                    </p>
                    <p class="text-xs text-purple-500 mt-1">Pastikan nominal transfer sesuai lalu simpan bukti pembayaran.</p>
                </div>
                @endforeach
            </div>

            {{-- Proof Upload --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Pembayaran <span class="text-red-500">*</span></label>
                <label for="proof_image"
                       class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300
                              rounded-xl cursor-pointer bg-gray-50 hover:border-purple-400 hover:bg-purple-50 transition-all">
                    <div id="proof-placeholder" class="text-center pointer-events-none">
                        <div class="text-3xl mb-1">📸</div>
                        <p class="text-sm text-gray-600 font-medium">Klik untuk unggah bukti transfer</p>
                        <p class="text-xs text-gray-400">JPG, PNG · maks 4MB</p>
                    </div>
                    <img id="proof-preview" class="hidden max-h-28 rounded-lg pointer-events-none" alt="Preview">
                    <input type="file" id="proof_image" name="proof_image" class="sr-only" accept="image/*" required onchange="previewProof(event)">
                </label>
            </div>

            {{-- Summary --}}
            <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
                <div class="flex justify-between text-gray-600">
                    <span>Saldo sekarang</span>
                    <span class="font-semibold">Rp {{ number_format((float) auth()->user()->balance, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Nominal top up</span>
                    <span class="font-semibold text-purple-700" id="amount-display">Rp 0</span>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between font-bold text-gray-800">
                    <span>Saldo setelah disetujui</span>
                    <span class="text-purple-700 text-base" id="new-balance">Rp {{ number_format((float) auth()->user()->balance, 0, ',', '.') }}</span>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition">
                📤 Kirim Permintaan Top Up
            </button>
        </form>
    </div>

    {{-- Info --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
        <p class="font-semibold mb-2">ℹ️ Cara Top Up:</p>
        <ol class="list-decimal pl-4 space-y-1">
            <li>Pilih nominal dan metode pembayaran</li>
            <li>Transfer ke nomor/rekening yang tertera</li>
            <li>Unggah screenshot bukti transfer</li>
            <li>Kirim permintaan — admin akan mengonfirmasi segera</li>
            <li>Saldo otomatis masuk setelah disetujui admin</li>
        </ol>
    </div>

    {{-- Recent History --}}
    @if ($history->isNotEmpty())
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-700">Riwayat Top Up Terakhir</h3>
            <a href="{{ route('topup.history') }}" class="text-xs text-purple-600 hover:underline">Lihat semua</a>
        </div>
        <ul class="divide-y divide-gray-100">
            @foreach ($history as $tx)
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
            <li class="px-5 py-3 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($tx->amount, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $tx->created_at->format('d M Y') }} · {{ $methodLabel }}</p>
                </div>
                <a href="{{ route('topup.show', $tx) }}" class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }} whitespace-nowrap shrink-0">{{ $label }}</a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

</div>

<script>
    const currentBalance = {{ (float) auth()->user()->balance }};
    const amountInput    = document.getElementById('amount');

    function setAmount(val) {
        amountInput.value = val;
        updateSummary();
        document.querySelectorAll('.quick-btn').forEach(b => {
            const btnVal = parseFloat(b.textContent.replace(/[^\d]/g, ''));
            b.classList.toggle('border-purple-600', btnVal === val);
            b.classList.toggle('bg-purple-50', btnVal === val);
        });
    }

    function updateSummary() {
        const v = parseFloat(amountInput.value) || 0;
        document.getElementById('amount-display').textContent = 'Rp ' + v.toLocaleString('id-ID');
        document.getElementById('new-balance').textContent    = 'Rp ' + (currentBalance + v).toLocaleString('id-ID');
    }

    amountInput.addEventListener('input', updateSummary);

    function showAccountInfo() {
        document.querySelectorAll('.method-info').forEach(el => el.classList.add('hidden'));
        const checked = document.querySelector('input[name="payment_method"]:checked');
        if (checked) document.getElementById('info-' + checked.value)?.classList.remove('hidden');
    }

    @if(old('payment_method'))
        document.getElementById('info-{{ old('payment_method') }}')?.classList.remove('hidden');
    @endif

    function previewProof(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('proof-placeholder').classList.add('hidden');
            const img = document.getElementById('proof-preview');
            img.src = e.target.result;
            img.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
</x-app-layout>
