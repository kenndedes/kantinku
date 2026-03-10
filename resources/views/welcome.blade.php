<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Kantinku') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-purple-50 via-white to-purple-50 text-gray-800 antialiased">
    <div class="min-h-screen flex flex-col">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-purple-100/70">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-purple-600 to-purple-800 text-white flex items-center justify-center font-black">K</div>
                    <span class="text-2xl sm:text-3xl font-black bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent">Kantinku</span>
                </a>
                <nav class="hidden md:flex items-center gap-4 text-sm font-semibold text-gray-700">
                    <a href="#fitur" class="hover:text-purple-700 transition">Fitur</a>
                    <a href="#alur" class="hover:text-purple-700 transition">Cara Pre-Order</a>
                    <a href="#cta" class="hover:text-purple-700 transition">Mulai</a>
                </nav>
                <div class="flex items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 sm:px-6 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-purple-800 text-white text-sm font-semibold shadow-lg shadow-purple-400/30 transition hover:translate-y-[-1px]">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 sm:px-5 py-2 rounded-xl border border-purple-200 text-purple-700 text-sm font-semibold hover:bg-purple-50 transition">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 sm:px-6 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-purple-800 text-white text-sm font-semibold shadow-lg shadow-purple-400/30 transition hover:translate-y-[-1px]">Daftar</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main class="flex-1">
            <section id="cta" class="relative overflow-hidden py-16 sm:py-20 lg:py-28">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-600/15 via-white to-purple-100/70"></div>
                <div class="absolute -left-20 top-10 w-72 h-72 bg-purple-200 rounded-full blur-3xl opacity-60"></div>
                <div class="absolute -right-16 bottom-10 w-64 h-64 bg-purple-400 rounded-full blur-3xl opacity-40"></div>

                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div>
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white shadow-sm border border-purple-100 text-sm font-semibold text-purple-700">
                                E-Canteen Modern
                                <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
                            </div>

                            <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-black leading-tight">
                                Pesan Makanan Kantin
                                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-purple-600 via-purple-700 to-purple-800">Lebih Mudah dan Cepat</span>
                            </h1>

                            <p class="mt-5 text-lg text-gray-700 max-w-xl">
                                Pre-order menu favorit tanpa antre. Pesan dari mana saja, bayar aman, dan ambil pesanan tanpa menunggu lama. Semua terasa ringan dan serba cepat.
                            </p>

                            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="group inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-purple-600 to-purple-800 text-white font-bold shadow-xl shadow-purple-400/30 transition transform hover:-translate-y-0.5">
                                        <span>Buka Dashboard</span>
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="group inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-purple-600 to-purple-800 text-white font-bold shadow-xl shadow-purple-400/30 transition transform hover:-translate-y-0.5">
                                        <span>Pesan Sekarang</span>
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                @endauth
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl border-2 border-purple-200 text-purple-700 font-bold bg-white/70 backdrop-blur hover:border-purple-400 hover:text-purple-800 transition">
                                    Login
                                </a>
                            </div>

                            <div class="mt-10 grid grid-cols-3 gap-4 max-w-lg">
                                <div class="rounded-2xl border border-purple-100 bg-white/80 p-4 shadow-sm">
                                    <p class="text-2xl sm:text-3xl font-black text-gray-900">10K+</p>
                                    <p class="text-sm text-gray-500 mt-1">Menu siap dipesan</p>
                                </div>
                                <div class="rounded-2xl border border-purple-100 bg-white/80 p-4 shadow-sm">
                                    <p class="text-2xl sm:text-3xl font-black text-gray-900">5K+</p>
                                    <p class="text-sm text-gray-500 mt-1">Pelanggan puas</p>
                                </div>
                                <div class="rounded-2xl border border-purple-100 bg-white/80 p-4 shadow-sm">
                                    <p class="text-2xl sm:text-3xl font-black text-gray-900">24H</p>
                                    <p class="text-sm text-gray-500 mt-1">Ambil tanpa antre</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute -top-10 -left-6 w-24 h-24 bg-purple-200 rounded-full blur-3xl opacity-70"></div>
                            <div class="absolute -bottom-12 -right-6 w-28 h-28 bg-purple-400 rounded-full blur-3xl opacity-60"></div>

                            <div class="relative bg-white/80 backdrop-blur rounded-3xl border border-purple-100 shadow-2xl p-8 lg:p-10">
                                <div class="flex flex-col gap-4">
                                    <div class="flex items-center gap-3">
                                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 text-white flex items-center justify-center font-bold">PO</span>
                                        <div>
                                            <p class="text-sm text-gray-500">Pre-Order</p>
                                            <p class="text-lg font-bold text-gray-900">Jadwalkan pesanan</p>
                                        </div>
                                    </div>
                                    <div class="rounded-2xl border border-purple-100 bg-gradient-to-br from-purple-50 to-white p-4">
                                        <p class="text-sm text-gray-500">Keranjang</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <div>
                                                <p class="font-semibold text-gray-900">Nasi Ayam Mentai</p>
                                                <p class="text-xs text-gray-500">Ambil pukul 12.00</p>
                                            </div>
                                            <span class="text-sm font-bold text-purple-700">Rp22.000</span>
                                        </div>
                                    </div>
                                    <div class="rounded-2xl border border-purple-100 bg-white p-4 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <p class="text-gray-900 font-semibold">Pembayaran</p>
                                            <span class="text-xs px-3 py-1 rounded-full bg-purple-100 text-purple-700">Non Tunai</span>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">Terima konfirmasi sebelum jam pengambilan.</p>
                                    </div>
                                    <div class="rounded-2xl border border-purple-100 bg-gradient-to-r from-purple-600 to-purple-800 text-white p-5 shadow-lg">
                                        <p class="text-sm">Proses</p>
                                        <div class="mt-2 flex items-center justify-between">
                                            <p class="text-xl font-black">Sedang disiapkan</p>
                                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                        </div>
                                        <p class="text-sm mt-1 text-purple-100">Ambil pesanan tanpa antre.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fitur" class="relative py-16 sm:py-20 lg:py-24 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-purple-100 text-purple-700 text-sm font-semibold mb-4">
                            Fitur Unggulan
                        </div>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900">
                            Lanjutkan Belanja Menu Favoritmu
                        </h2>
                        <p class="mt-4 text-lg text-gray-600">
                            Akses keranjang, wishlist, dan riwayat pesanan dengan cepat. Semua terasa rapi, ringan, dan selalu siap digunakan.
                        </p>
                    </div>

                    <div class="mt-12 sm:mt-16 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        <div class="group bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center mb-5 text-white font-bold">01</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Pre-Order Tanpa Antre</h3>
                            <p class="text-gray-600">Pilih waktu ambil, bayar aman, dan pesanan siap saat tiba di kantin.</p>
                        </div>

                        <div class="group bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center mb-5 text-white font-bold">02</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Keranjang Pintar</h3>
                            <p class="text-gray-600">Simpan favorit, cek ketersediaan, dan atur catatan khusus untuk penjual.</p>
                        </div>

                        <div class="group bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center mb-5 text-white font-bold">03</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Notifikasi Real-Time</h3>
                            <p class="text-gray-600">Status pesanan dan pembayaran selalu terpantau dengan update instan.</p>
                        </div>

                        <div class="group bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center mb-5 text-white font-bold">04</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Pembayaran Fleksibel</h3>
                            <p class="text-gray-600">Dukungan saldo kantin, e-wallet, maupun transfer untuk proses cepat.</p>
                        </div>

                        <div class="group bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center mb-5 text-white font-bold">05</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Dashboard Ringkas</h3>
                            <p class="text-gray-600">Pantau riwayat transaksi, jadwal ambil, dan progres pesanan di satu tempat.</p>
                        </div>

                        <div class="group bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100 p-8 hover:shadow-xl hover:-translate-y-1 transition duration-300">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center mb-5 text-white font-bold">06</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Responsif untuk Semua</h3>
                            <p class="text-gray-600">Desain mobile-first di perangkat kecil dan tampilan dua kolom elegan di desktop.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="alur" class="relative py-14 sm:py-16 lg:py-20 bg-gradient-to-br from-purple-900 via-purple-800 to-purple-900 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                        <div>
                            <h3 class="text-3xl sm:text-4xl font-black">Alur Pre-Order yang Singkat</h3>
                            <p class="mt-4 text-purple-100 max-w-xl">Semua langkah dibuat sederhana agar pengguna tidak bosan. Pilih menu, atur jadwal ambil, lakukan pembayaran, dan terima notifikasi siap ambil.</p>
                            <div class="mt-6 space-y-4">
                                <div class="flex items-start gap-3">
                                    <span class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center text-lg font-bold">1</span>
                                    <div>
                                        <p class="font-semibold text-white">Pilih Menu Favorit</p>
                                        <p class="text-sm text-purple-100">Cari cepat, tambahkan catatan, dan simpan ke keranjang.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center text-lg font-bold">2</span>
                                    <div>
                                        <p class="font-semibold text-white">Atur Jadwal Ambil</p>
                                        <p class="text-sm text-purple-100">Pilih waktu ambil agar pesanan sudah siap.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center text-lg font-bold">3</span>
                                    <div>
                                        <p class="font-semibold text-white">Bayar dan Lacak</p>
                                        <p class="text-sm text-purple-100">Pembayaran fleksibel, status real-time, tanpa antre.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute -top-10 -right-10 w-28 h-28 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute -bottom-12 -left-10 w-32 h-32 bg-purple-600/30 rounded-full blur-3xl"></div>
                            <div class="relative bg-white/10 backdrop-blur-lg border border-white/15 rounded-3xl p-8 shadow-2xl">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-purple-100">Status Pesanan</p>
                                        <p class="text-xl font-black">Disiapkan</p>
                                    </div>
                                    <span class="text-xs px-3 py-1 rounded-full bg-white/15 border border-white/20">Tanpa antre</span>
                                </div>
                                <div class="mt-6 space-y-4">
                                    <div class="flex items-center justify-between bg-white/10 rounded-2xl p-4 border border-white/15">
                                        <div>
                                            <p class="font-semibold">Sate Taichan</p>
                                            <p class="text-xs text-purple-100">Ambil 12.00</p>
                                        </div>
                                        <span class="text-sm font-bold">Rp18.000</span>
                                    </div>
                                    <div class="flex items-center justify-between bg-white/10 rounded-2xl p-4 border border-white/15">
                                        <div>
                                            <p class="font-semibold">Es Teh Susu</p>
                                            <p class="text-xs text-purple-100">Ambil 12.00</p>
                                        </div>
                                        <span class="text-sm font-bold">Rp8.000</span>
                                    </div>
                                    <button class="w-full mt-2 inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl bg-white text-purple-800 font-bold hover:translate-y-[-1px] transition">
                                        Lihat keranjang
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M13 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="relative py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-purple-100 pt-8">
                <div>
                    <h3 class="text-xl font-black text-gray-900">Kantinku</h3>
                    <p class="text-sm text-gray-600">Platform kantin digital untuk pre-order tanpa antre.</p>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <a href="#fitur" class="hover:text-purple-700 transition">Fitur</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <a href="#alur" class="hover:text-purple-700 transition">Cara Pakai</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <a href="{{ route('login') }}" class="hover:text-purple-700 transition">Login</a>
                </div>
                <p class="text-sm text-gray-500">© {{ date('Y') }} Kantinku. Semua hak dilindungi.</p>
            </div>
        </footer>
    </div>
</body>
</html>