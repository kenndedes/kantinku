<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengajuan Ditolak — {{ config('app.name', 'Kantinku') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-lg">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-2xl font-extrabold text-purple-800">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-600 to-purple-800 text-white inline-flex items-center justify-center font-black">K</span>
                Kantinku
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-red-100 overflow-hidden">
            <div class="bg-gradient-to-r from-red-400 to-rose-500 h-2"></div>
            <div class="p-8 text-center">
                <div class="text-6xl mb-4">❌</div>
                <h1 class="text-2xl font-black text-gray-900 mb-2">Pengajuan Seller Ditolak</h1>
                <p class="text-gray-600 mb-6">Maaf, pengajuan akun seller kamu tidak dapat kami setujui saat ini.</p>

                @php $profile = auth()->user()->sellerProfile; @endphp
                @if($profile?->stand_name)
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-100 rounded-full text-sm font-semibold text-red-700 mb-6">
                        🏪 {{ $profile->stand_name }}
                    </div>
                @endif

                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-left space-y-2 mb-6">
                    <p class="text-sm font-semibold text-red-800 mb-1">Kemungkinan alasan penolakan:</p>
                    <div class="flex items-start gap-2 text-sm text-red-700">
                        <span class="mt-0.5">•</span><span>Data yang diisi tidak lengkap atau tidak valid.</span>
                    </div>
                    <div class="flex items-start gap-2 text-sm text-red-700">
                        <span class="mt-0.5">•</span><span>Stand/kantin yang didaftarkan belum terdaftar di sistem.</span>
                    </div>
                    <div class="flex items-start gap-2 text-sm text-red-700">
                        <span class="mt-0.5">•</span><span>Nama stand sudah digunakan oleh seller lain.</span>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-left mb-6">
                    <p class="text-sm font-semibold text-gray-700 mb-1">Apa yang bisa kamu lakukan?</p>
                    <p class="text-sm text-gray-600">Hubungi admin kantin untuk informasi lebih lanjut mengenai alasan penolakan dan langkah selanjutnya.</p>
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
</body>
</html>
