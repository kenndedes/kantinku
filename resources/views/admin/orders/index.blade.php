<x-app-layout>
    <x-slot name="header">Kelola Pesanan</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Daftar Pesanan</h1>

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-2 items-end">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Nama pembeli / ID pesanan..."
                       class="pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-56">
            </div>
            <select name="status" class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">Semua Status</option>
                <option value="menunggu"     {{ ($status ?? '') === 'menunggu'     ? 'selected' : '' }}>⏳ Menunggu</option>
                <option value="diproses"     {{ ($status ?? '') === 'diproses'     ? 'selected' : '' }}>🔄 Diproses</option>
                <option value="siap_diambil" {{ ($status ?? '') === 'siap_diambil' ? 'selected' : '' }}>✅ Siap Diambil</option>
                <option value="selesai"      {{ ($status ?? '') === 'selesai'      ? 'selected' : '' }}>🎉 Selesai</option>
                <option value="batal"        {{ ($status ?? '') === 'batal'        ? 'selected' : '' }}>❌ Batal</option>
            </select>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">Filter</button>
            @if(!empty($search) || !empty($status))
                <a href="{{ route('admin.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 px-3 py-2 rounded-xl text-sm transition">✕ Reset</a>
            @endif
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Pemesan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Jam Ambil</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Total</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $order->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $order->order_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ substr($order->pickup_time, 0, 5) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                        {{ $order->order_status === 'menunggu' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                        {{ $order->order_status === 'diproses' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                        {{ $order->order_status === 'siap_diambil' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                        {{ $order->order_status === 'selesai' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                        {{ $order->order_status === 'batal' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}">
                                        {{ str_replace('_', ' ', ucfirst($order->order_status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-semibold transition-colors">Lihat Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <p>Belum ada pesanan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
