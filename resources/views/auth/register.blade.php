<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - {{ config('app.name', 'Kantinku') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-purple-100 antialiased">
    <div class="min-h-screen grid lg:grid-cols-2">
        <section class="relative hidden lg:flex flex-col justify-between bg-gradient-to-br from-purple-700 via-purple-800 to-purple-900 text-white p-12 xl:p-16 overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_10%_30%,rgba(255,255,255,0.08),transparent_35%),radial-gradient(circle_at_90%_10%,rgba(255,255,255,0.06),transparent_30%)]"></div>
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <span class="w-11 h-11 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center font-black">K</span>
                    <span class="text-3xl leading-none font-extrabold tracking-tight">Kantinku</span>
                </a>
                <div class="mt-12 space-y-4 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/15 text-sm font-semibold">Buat Akun Baru</div>
                    <h1 class="text-4xl xl:text-5xl font-black leading-tight">Daftar dan Mulai Pesan Cepat</h1>
                    <p class="text-lg text-purple-100">Catat pesanan, atur jadwal ambil, dan kelola saldo kantin di satu aplikasi. Desain bersih dan responsif agar nyaman digunakan setiap hari.</p>
                </div>
            </div>
            <div class="relative z-10 grid grid-cols-3 gap-4 max-w-xl">
                <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                    <p class="text-2xl font-black">100%</p>
                    <p class="text-sm text-purple-100">Pre-order tanpa antre</p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                    <p class="text-2xl font-black">Multi</p>
                    <p class="text-sm text-purple-100">Pembayaran fleksibel</p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/10 p-4">
                    <p class="text-2xl font-black">Responsif</p>
                    <p class="text-sm text-purple-100">Mobile dan desktop</p>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center p-4 sm:p-10 lg:p-12">
            <div class="w-full max-w-md bg-white/90 backdrop-blur rounded-2xl border border-purple-100 shadow-xl p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xl font-extrabold text-purple-800 lg:hidden">
                        <span class="w-9 h-9 rounded-lg bg-gradient-to-r from-purple-600 to-purple-800 text-white inline-flex items-center justify-center font-black">K</span>
                        Kantinku
                    </a>
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-purple-700 hover:text-purple-800">Login</a>
                </div>
                <h2 class="mt-4 text-2xl font-black text-gray-900">Daftar Akun Pembeli</h2>
                <p class="mt-1 text-sm text-gray-600">Buat akun untuk mulai memesan menu kantin.</p>

                {{-- Register type nav --}}
                <div class="mt-5 flex rounded-xl overflow-hidden border border-purple-200 text-sm font-semibold">
                    <span class="flex-1 py-2.5 text-center bg-purple-700 text-white">🛒 Daftar Pembeli</span>
                    <a href="{{ route('register.seller') }}" class="flex-1 py-2.5 text-center bg-white text-purple-700 hover:bg-purple-50 transition">🏪 Daftar Seller</a>
                </div>

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="mt-2 block w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition" placeholder="Nama lengkap">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="mt-2 block w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition" placeholder="nama@email.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="mt-2 block w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition" placeholder="minimal 8 karakter">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="mt-2 block w-full rounded-xl border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition" placeholder="tulis ulang password">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-purple-800 text-white font-bold shadow-lg shadow-purple-300/30 transition transform hover:-translate-y-0.5">
                        Daftar
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-purple-700 font-semibold hover:text-purple-800">Masuk di sini</a>
                </p>
            </div>
        </section>
    </div>
</body>
</html>
