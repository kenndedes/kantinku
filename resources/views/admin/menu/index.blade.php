<x-app-layout>
    <x-slot name="header">Kelola Menu</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Daftar Menu</h1>
            <a href="{{ route('admin.menu.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 transition-all duration-200 shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Menu
            </a>
        </div>

        @if (session('status'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-700 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('admin.menu.index') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap gap-3 items-end">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama menu..."
                       class="pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-48">
            </div>
            <div>
                <select name="type" class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Tipe</option>
                    <option value="makanan"  {{ ($type ?? '') === 'makanan'  ? 'selected' : '' }}>🍽 Makanan</option>
                    <option value="minuman"  {{ ($type ?? '') === 'minuman'  ? 'selected' : '' }}>🥤 Minuman</option>
                </select>
            </div>
            <div>
                <select name="stand_id" class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Stand</option>
                    @foreach($stands as $stand)
                        <option value="{{ $stand->id }}" {{ ($standId ?? '') == $stand->id ? 'selected' : '' }}>{{ $stand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="available" class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="1" {{ ($available ?? '') === '1' ? 'selected' : '' }}>✅ Tersedia</option>
                    <option value="0" {{ ($available ?? '') === '0' ? 'selected' : '' }}>❌ Tidak Tersedia</option>
                </select>
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">Filter</button>
            @if(!empty($search) || !empty($type) || !empty($standId) || $available !== '')
                <a href="{{ route('admin.menu.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 px-3 py-2 rounded-xl text-sm transition">✕ Reset</a>
            @endif
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Menu</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Stand</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Tipe</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Harga</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Stok</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($menuItems as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->stand->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $item->type }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->stock }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $item->is_available ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                        {{ $item->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.menu.edit', $item) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-semibold transition-colors">Edit</a>
                                        <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <p>Belum ada menu. Seller dapat menambah menu dari dashboard mereka.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($menuItems->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $menuItems->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
