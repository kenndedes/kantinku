<x-app-layout>
    <x-slot name="header">Pembayaran QRIS</x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6">
        <div class="max-w-4xl mx-auto">
            @if ($transaction->status === 'completed')
                <!-- Success State -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-8 text-center border-2 border-green-300">
                    <div class="text-6xl mb-4">✅</div>
                    <h2 class="text-3xl font-bold text-green-900 mb-2">Pembayaran Berhasil!</h2>
                    <p class="text-green-700 mb-4">Saldo Anda telah berhasil ditambahkan sebesar Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                    <p class="text-sm text-green-600 mb-6">Saldo baru Anda: Rp {{ number_format((float) auth()->user()->balance, 0, ',', '.') }}</p>
                    <a href="{{ route('dashboard') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition">
                        Kembali ke Dashboard
                    </a>
                </div>
            @elseif ($transaction->status === 'failed' || $transaction->status === 'cancelled')
                <!-- Failed State -->
                <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-lg p-8 text-center border-2 border-red-300">
                    <div class="text-6xl mb-4">❌</div>
                    <h2 class="text-3xl font-bold text-red-900 mb-2">Pembayaran Gagal</h2>
                    <p class="text-red-700 mb-6">{{ $transaction->status === 'cancelled' ? 'Transaksi dibatalkan' : 'Terjadi kesalahan saat memproses pembayaran' }}</p>
                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('topup.index') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-8 rounded-lg transition">
                            Coba Lagi
                        </a>
                        <a href="{{ route('dashboard') }}" class="inline-block bg-gray-400 hover:bg-gray-500 text-white font-semibold py-3 px-8 rounded-lg transition">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            @else
                <!-- Pending State -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white p-6">
                        <h2 class="text-2xl font-bold">Selesaikan Pembayaran Anda</h2>
                        <p class="text-primary-100 mt-2">Scan QR Code di bawah dengan aplikasi dompet digital Anda</p>
                    </div>

                    <div class="p-8 space-y-8">
                        <!-- Amount -->
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-600 text-sm">Jumlah yang Harus Dibayar</p>
                            <p class="text-4xl font-bold text-primary-600 mt-2">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                        </div>

                        <!-- QR Code Display -->
                        <div class="flex flex-col items-center">
                            <div class="bg-white p-6 border-4 border-gray-200 rounded-lg">
                                <div id="qrcode" class="flex items-center justify-center" style="width: 300px; height: 300px; background: #f9fafb;">
                                    <div id="qr-loading" class="text-center">
                                        <div class="animate-spin inline-block h-10 w-10 text-primary-600 mb-2">
                                            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-600 text-sm">Generate QR Code...</p>
                                    </div>
                                </div>
                            </div>
                            @if ($transaction->qris_text)
                                <p class="text-xs text-gray-500 mt-4 text-center">
                                    <span class="font-semibold">QRIS String ({{ strlen($transaction->qris_text) }} chars):</span><br>
                                    <code class="text-gray-700 break-all">{{ substr($transaction->qris_text, 0, 80) }}...</code>
                                </p>
                            @else
                                <p class="text-xs text-red-500 mt-4 text-center">
                                    <span class="font-semibold">⚠️ QRIS Text Kosong!</span><br>
                                    Cek respons API dari Xoftware
                                </p>
                            @endif
                        </div>

                        <!-- Status -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8">
                                    <div id="status-spinner" class="animate-spin h-6 w-6 text-blue-600">
                                        <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="font-semibold text-blue-900">Menunggu Pembayaran...</p>
                                <p id="status-text" class="text-sm text-blue-700">Silakan selesaikan pembayaran melalui scan QR Code</p>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                            <h3 class="font-bold text-amber-900 mb-4">📱 Cara Pembayaran QRIS:</h3>
                            <ol class="space-y-3 text-sm text-amber-800">
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">1</span>
                                    <span>Buka aplikasi dompet digital Anda (OVO, GCash, Dana, dll)</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">2</span>
                                    <span>Pilih menu "Scan QRIS" atau "Pembayaran"</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">3</span>
                                    <span>Arahkan kamera ponsel ke QR Code di atas</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">4</span>
                                    <span>Konfirmasi dan masukkan PIN atau password</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="flex-shrink-0 font-bold bg-amber-300 text-amber-900 w-6 h-6 flex items-center justify-center rounded-full">5</span>
                                    <span>Tunggu notifikasi pembayaran berhasil</span>
                                </li>
                            </ol>
                        </div>

                        <!-- Transaction Info -->
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 rounded-lg p-4 text-sm">
                            <div>
                                <p class="text-gray-600">Ref ID</p>
                                <p class="font-semibold text-gray-900 break-all">{{ $transaction->ref_id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Waktu Berlaku (Countdown)</p>
                                <p class="font-semibold text-gray-900" id="timer-display">
                                    <span id="time-left">15:00</span>
                                    <span id="timer-status" class="text-amber-600 text-xs ml-2">(Menghitung...)</span>
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-4">
                            <button 
                                type="button" 
                                id="check-status-btn"
                                onclick="checkStatus()"
                                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                                🔄 Cek Status
                            </button>
                            <form action="{{ route('topup.cancel', $transaction->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-lg transition">
                                    ❌ Batalkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="mt-6 bg-info-50 border border-info-200 rounded-lg p-4">
                    <h3 class="font-semibold text-info-900 mb-2">ℹ️ Informasi Pembayaran QRIS</h3>
                    <ul class="text-sm text-info-800 space-y-1 list-disc ps-5">
                        <li>QRIS berlaku selama 15 menit</li>
                        <li>Pembayaran dapat dilakukan melalui berbagai aplikasi dompet digital</li>
                        <li>Status pembayaran akan diperbarui secara otomatis</li>
                        <li>Jika pembayaran berhasil, saldo Anda akan langsung bertambah</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    @if ($transaction->status === 'pending')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            // Generate QR Code
            @if ($transaction->qris_text)
                new QRCode(document.getElementById('qrcode'), {
                    text: '{{ $transaction->qris_text }}',
                    width: 300,
                    height: 300,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });
                // Hide loading spinner when QR is generated
                document.getElementById('qr-loading').style.display = 'none';
            @endif

            // Start countdown timer
            const expiresAtTimestamp = {{ $transaction->expires_at->timestamp }};
            let timerInterval = startCountdownTimer(expiresAtTimestamp);

            function startCountdownTimer(expiresAtTimestamp) {
                return setInterval(() => {
                    const now = Math.floor(Date.now() / 1000);
                    const remaining = expiresAtTimestamp - now;
                    
                    if (remaining <= 0) {
                        document.getElementById('time-left').textContent = '00:00';
                        document.getElementById('time-left').classList.add('text-red-600');
                        document.getElementById('timer-status').textContent = '(Expired)';
                        document.getElementById('timer-status').classList.remove('text-amber-600');
                        document.getElementById('timer-status').classList.add('text-red-600');
                        clearInterval(timerInterval);
                        return;
                    }
                    
                    const minutes = Math.floor(remaining / 60);
                    const seconds = remaining % 60;
                    const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    
                    document.getElementById('time-left').textContent = display;
                    
                    // Show warning when < 2 minutes (120 seconds)
                    if (remaining < 120) {
                        document.getElementById('time-left').classList.add('text-red-600');
                        document.getElementById('timer-status').textContent = '(Hampir habis)';
                        document.getElementById('timer-status').classList.remove('text-amber-600');
                        document.getElementById('timer-status').classList.add('text-red-600');
                    }
                }, 1000);
            }

            // Auto-check status every 5 seconds
            let checkInterval = setInterval(checkStatus, 5000);

            function checkStatus() {
                fetch('{{ route('topup.check-status', $transaction->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Status Check Response:', data);
                    
                    if (data.status === 'completed') {
                        clearInterval(checkInterval);
                        clearInterval(timerInterval);
                        document.getElementById('status-spinner').innerHTML = '✅';
                        document.getElementById('status-text').textContent = 'Pembayaran berhasil! Sedang memproses...';
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else if (data.status === 'failed') {
                        clearInterval(checkInterval);
                        clearInterval(timerInterval);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                });
            }

            // Listen for webhook updates (optional - client side fallback)
            window.addEventListener('message', function(event) {
                if (event.data.type === 'payment_success') {
                    clearInterval(checkInterval);
                    clearInterval(timerInterval);
                    location.reload();
                }
            });
        </script>
    @endif
</x-app-layout>
