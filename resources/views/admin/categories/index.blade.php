<x-app-layout>
    <x-slot name="header">Kelola Kategori</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Daftar Kategori</h1>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 transition-all duration-200 shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kategori
            </a>
        </div>

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
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Kategori</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Deskripsi</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Jumlah Menu</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $category->description ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $category->menu_items_count }} menu</td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-semibold transition-colors">Edit</a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Menu yang menggunakan kategori ini akan kehilangan kategorinya.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <p>Belum ada kategori. Klik <strong>Tambah Kategori</strong> untuk memulai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
