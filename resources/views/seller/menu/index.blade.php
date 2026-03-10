<x-app-layout>
    <x-slot name="header">Menu Stand</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Menu Saya</h1>
            <a href="{{ route('seller.menu.create') }}" class="px-4 py-2 rounded-lg bg-primary-600 text-white">Tambah Menu</a>
        </div>

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-green-800">{{ session('status') }}</div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Nama</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Tipe</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Harga</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Stok</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($menuItems as $item)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $item->name }}</td>
                            <td class="px-4 py-3 capitalize">{{ $item->type }}</td>
                            <td class="px-4 py-3">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $item->stock }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $item->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $item->is_available ? 'Tersedia' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm flex gap-3">
                                <a href="{{ route('seller.menu.edit', $item) }}" class="text-primary-600">Edit</a>
                                <form action="{{ route('seller.menu.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus menu?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada menu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
