<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menunggu Verifikasi — {{ config('app.name', 'Kantinku') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-purple-100 antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-lg">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-2xl font-extrabold text-purple-800">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-600 to-purple-800 text-white inline-flex items-center justify-center font-black">K</span>
                Kantinku
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-purple-100 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-400 to-orange-400 h-2"></div>
            <div class="p-8 text-center">
                <div class="text-6xl mb-4">⏳</div>
                <h1 class="text-2xl font-black text-gray-900 mb-2">Akun Seller Sedang Diverifikasi</h1>
                <p class="text-gray-600 mb-6">Pengajuan kamu sudah kami terima. Tim admin sedang meninjau data yang kamu kirimkan.</p>

                @php $profile = auth()->user()->sellerProfile; @endphp
                @if($profile?->stand_name)
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 border border-purple-100 rounded-full text-sm font-semibold text-purple-700 mb-6">
                        🏪 {{ $profile->stand_name }}
                    </div>
                @endif

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-left space-y-2 mb-6">
                    <p class="text-sm font-semibold text-yellow-800 mb-1">Yang sedang terjadi:</p>
                    <div class="flex items-start gap-2 text-sm text-yellow-700">
                        <span class="mt-0.5">✅</span><span>Pendaftaran berhasil diterima.</span>
                    </div>
                    <div class="flex items-start gap-2 text-sm text-yellow-700">
                        <span class="mt-0.5">🔍</span><span>Admin sedang memverifikasi data akun dan stand kamu.</span>
                    </div>
                    <div class="flex items-start gap-2 text-sm text-yellow-700">
                        <span class="mt-0.5">⏰</span><span>Proses verifikasi biasanya selesai dalam 1×24 jam.</span>
                    </div>
                    <div class="flex items-start gap-2 text-sm text-yellow-700">
                        <span class="mt-0.5">🚀</span><span>Setelah disetujui, kamu bisa langsung mulai menerima pesanan.</span>
                    </div>
                </div>

                {{-- Auto-check status --}}
                <div id="status-bar" class="mb-5 p-3 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs text-purple-600">
                        <span id="pulse-dot" class="w-2 h-2 rounded-full bg-purple-400 animate-pulse inline-block shrink-0"></span>
                        <span id="status-text">Mengecek status...</span>
                    </div>
                    <button id="btn-check" onclick="checkStatus(true)"
                        class="shrink-0 text-xs font-semibold text-purple-700 hover:text-purple-900 underline underline-offset-2">
                        Cek Sekarang
                    </button>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Keluar dari akun ini
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">Butuh bantuan? Hubungi admin kantin.</p>
    </div>

    <script>
        const checkUrl    = '{{ route('seller.status-check') }}';
        const dashboardUrl = '{{ route('seller.dashboard') }}';
        const rejectUrl    = '{{ route('seller.rejected') }}';
        const statusText = document.getElementById('status-text');
        const pulseDot   = document.getElementById('pulse-dot');
        const btnCheck   = document.getElementById('btn-check');
        let timer = null;
        let attempts = 0;

        async function checkStatus(manual = false) {
            clearTimeout(timer);
            if (manual) {
                statusText.textContent = 'Mengecek...';
                btnCheck.disabled = true;
            }

            try {
                const res  = await fetch(checkUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });

                if (!res.ok) throw new Error('non-200');
                const data = await res.json();

                if (data.status === 'approved') {
                    pulseDot.classList.remove('bg-purple-400', 'animate-pulse');
                    pulseDot.classList.add('bg-green-400');
                    statusText.textContent = '✅ Akun disetujui! Mengalihkan ke dashboard...';
                    statusText.classList.add('text-green-600', 'font-semibold');
                    btnCheck.style.display = 'none';
                    setTimeout(() => { window.location.href = dashboardUrl; }, 1000);
                    return;
                }

                if (data.status === 'rejected') {
                    window.location.href = rejectUrl;
                    return;
                }

                attempts++;
                statusText.textContent = 'Masih menunggu verifikasi... (cek ke-' + attempts + ')';
            } catch (e) {
                statusText.textContent = 'Gagal terhubung, mencoba lagi...';
            }

            btnCheck.disabled = false;
            timer = setTimeout(checkStatus, 5000);
        }

        // Cek pertama setelah 2 detik
        timer = setTimeout(checkStatus, 2000);
    </script>
</body>
</html>
