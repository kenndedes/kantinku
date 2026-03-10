<x-app-layout>
    <x-slot name="header">Kelola Stand</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Daftar Stand</h1>
            <a href="{{ route('admin.stands.create') }}" class="inline-flex items-center justify-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 transition-all duration-200 shadow-md">
                Tambah Stand
            </a>
        </div>

        <form method="GET" action="{{ route('admin.stands.index') }}" class="flex gap-3">
            <input
                type="text"
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Cari nama atau lokasi stand..."
                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
            />
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold px-5 py-2.5 text-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" /></svg>
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.stands.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-semibold px-5 py-2.5 text-sm transition-all">
                    Reset
                </a>
            @endif
        </form>

        @if (session('status'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-700 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nama</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Lokasi</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Seller</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Menu</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Order</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($stands as $stand)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $stand->name }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $stand->location ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ optional($stand->sellerProfile?->user)->name ?? 'Belum ada' }}</td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $stand->menu_items_count }}</td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $stand->orders_count }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $stand->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
                                        {{ $stand->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex gap-3">
                                        <a href="{{ route('admin.stands.edit', $stand) }}" class="text-primary-600 hover:text-primary-700 font-semibold">Edit</a>
                                        <form action="{{ route('admin.stands.destroy', $stand) }}" method="POST" onsubmit="return confirm('Hapus stand ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-700 font-semibold">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">Belum ada stand.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
