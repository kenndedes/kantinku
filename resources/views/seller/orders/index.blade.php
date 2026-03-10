<x-app-layout>
    <x-slot name="header">Pesanan Masuk</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Pesanan Masuk</h1>
                @if(isset($stand))
                    <p class="text-sm text-indigo-600 font-medium mt-0.5">🏪 {{ $stand->name }}</p>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-green-800">{{ session('status') }}</div>
        @endif

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('seller.orders.index') }}" class="flex flex-wrap gap-2 items-end">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama pembeli..."
                       class="pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 w-48">
            </div>
            <select name="status" class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Semua Status</option>
                <option value="menunggu"     {{ ($status ?? '') === 'menunggu'     ? 'selected' : '' }}>⏳ Menunggu</option>
                <option value="diproses"     {{ ($status ?? '') === 'diproses'     ? 'selected' : '' }}>🔄 Diproses</option>
                <option value="siap_diambil" {{ ($status ?? '') === 'siap_diambil' ? 'selected' : '' }}>✅ Siap Diambil</option>
                <option value="selesai"      {{ ($status ?? '') === 'selesai'      ? 'selected' : '' }}>🎉 Selesai</option>
                <option value="batal"        {{ ($status ?? '') === 'batal'        ? 'selected' : '' }}>❌ Batal</option>
            </select>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">Filter</button>
            @if(!empty($search) || !empty($status))
                <a href="{{ route('seller.orders.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-xl text-sm transition">✕ Reset</a>
            @endif
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Nomor</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Pembeli</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Pembayaran</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Total</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">{{ $order->user->name }}</td>
                            <td class="px-4 py-3">{{ ucfirst($order->order_status) }}</td>
                            <td class="px-4 py-3">{{ ucfirst($order->payment_status) }}</td>
                            <td class="px-4 py-3">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm flex gap-3">
                                <a href="{{ route('seller.orders.show', $order) }}" class="text-primary-600">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </div>
</x-app-layout>
