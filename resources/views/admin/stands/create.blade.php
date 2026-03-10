<x-app-layout>
    <x-slot name="header">Tambah Stand</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-indigo-50 flex items-start justify-center py-10 px-4">
        <div class="w-full max-w-xl">

            {{-- Card header --}}
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-2xl px-8 py-8 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5.25h18M4.5 9.75h15M6 14.25h12M9 18.75h6" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black tracking-tight">Tambah Stand Baru</h2>
                        <p class="text-purple-100 text-sm mt-0.5">Kode stand akan digenerate otomatis oleh sistem</p>
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

                <form action="{{ route('admin.stands.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Nama Stand --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Stand <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Contoh: Stand Mie Ayam Bu Sri"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition"
                                required
                                autofocus
                            >
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                id="location"
                                name="location"
                                value="{{ old('location') }}"
                                placeholder="Contoh: Lantai 1, Blok A"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border {{ $errors->has('location') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50' }} text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('location')" class="mt-1.5" />
                    </div>

                    {{-- Status Aktif --}}
                    <div class="bg-gray-50 rounded-xl px-5 py-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Status Stand</p>
                            <p class="text-xs text-gray-400 mt-0.5">Stand aktif dapat menerima pesanan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-purple-400 rounded-full peer peer-checked:bg-purple-600 transition-all after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('admin.stands.index') }}"
                           class="flex-1 text-center px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-sm shadow-md shadow-purple-200 transition">
                            Buat Stand
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
