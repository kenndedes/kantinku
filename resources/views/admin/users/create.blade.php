<x-app-layout>
    <x-slot name="header">Tambah User</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-indigo-50 flex items-start justify-center py-10 px-4">
        <div class="w-full max-w-xl">

            {{-- Card header --}}
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-2xl px-8 py-8 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black tracking-tight">Buat Akun User</h2>
                        <p class="text-purple-100 text-sm mt-0.5">Tambah pengguna baru ke sistem KantinKu</p>
                    </div>
                </div>
            </div>

            {{-- Card body --}}
            <div class="bg-white rounded-b-2xl shadow-xl shadow-purple-100/50 px-8 py-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-red-700 mb-1">Terdapat kesalahan:</p>
                        <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Nama lengkap pengguna"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition"
                                required
                                autofocus
                            >
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="email@example.com"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition"
                                required
                            >
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition"
                                required
                            >
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition"
                                required
                            >
                        </div>
                    </div>

                    {{-- Saldo Awal --}}
                    <div>
                        <label for="balance" class="block text-sm font-semibold text-gray-700 mb-1.5">Saldo Awal</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-500 text-sm font-semibold pointer-events-none">Rp</span>
                            <input
                                type="number"
                                id="balance"
                                name="balance"
                                value="{{ old('balance', 0) }}"
                                min="0"
                                step="1000"
                                class="w-full pl-10 pr-4 py-3 rounded-xl border {{ $errors->has('balance') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Kosongkan atau isi 0 jika tidak ada saldo awal</p>
                        <x-input-error :messages="$errors->get('balance')" class="mt-1.5" />
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('admin.users.index') }}"
                           class="flex-1 text-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-sm shadow-md shadow-purple-200 transition">
                            Buat Akun
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
