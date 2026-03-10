<x-app-layout>
<div class="space-y-6 py-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Stand Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan penjualan stand Anda</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('seller.reports.exportExcel', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-emerald-700 transition">
                📊 Unduh Excel
            </a>
            <a href="{{ route('seller.reports.exportPdf', request()->query()) }}"
               class="inline-flex items-center gap-2 bg-rose-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-rose-700 transition">
                📄 Unduh PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('seller.reports.index') }}"
          class="bg-white rounded-2xl shadow p-5 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Status Pesanan</label>
            <select name="status" class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none">
                <option value="">Semua Status</option>
                <option value="menunggu"     {{ request('status') === 'menunggu'     ? 'selected' : '' }}>Menunggu</option>
                <option value="diproses"     {{ request('status') === 'diproses'     ? 'selected' : '' }}>Diproses</option>
                <option value="siap_diambil" {{ request('status') === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                <option value="selesai"      {{ request('status') === 'selesai'      ? 'selected' : '' }}>Selesai</option>
                <option value="batal"        {{ request('status') === 'batal'        ? 'selected' : '' }}>Batal</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none">
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 bg-purple-600 text-white rounded-xl px-4 py-2 text-sm font-medium hover:bg-purple-700 transition">
                Filter
            </button>
            <a href="{{ route('seller.reports.index') }}"
               class="flex-1 bg-gray-100 text-gray-700 rounded-xl px-4 py-2 text-sm font-medium hover:bg-gray-200 transition text-center">
                Reset
            </a>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl shrink-0">📦</div>
            <div>
                <p class="text-xs text-gray-500">Total Pesanan</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($summary->total_orders ?? 0) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl shrink-0">💰</div>
            <div>
                <p class="text-xs text-gray-500">Total Pendapatan</p>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl shrink-0">✅</div>
            <div>
                <p class="text-xs text-gray-500">Pesanan Selesai</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($summary->total_selesai ?? 0) }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-2xl shrink-0">❌</div>
            <div>
                <p class="text-xs text-gray-500">Pesanan Batal</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($summary->total_batal ?? 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Top Menu Items --}}
    @if($topItems->isNotEmpty())
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h2 class="font-semibold text-gray-700">10 Menu Terlaris</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-5 py-3 text-left">#</th>
                    <th class="px-5 py-3 text-left">Menu</th>
                    <th class="px-5 py-3 text-right">Terjual</th>
                    <th class="px-5 py-3 text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($topItems as $i => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-400 font-mono">{{ $i + 1 }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                    <td class="px-5 py-3 text-right text-gray-600">{{ number_format($item->total_qty) }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                        Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Order Detail Table --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h2 class="font-semibold text-gray-700">Detail Pesanan</h2>
            <span class="text-sm text-gray-400">{{ $orders->total() }} pesanan</span>
        </div>
        @if($orders->isEmpty())
            <div class="text-center py-12 text-gray-400">Tidak ada data untuk filter yang dipilih.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-5 py-3 text-left">No. Pesanan</th>
                        <th class="px-5 py-3 text-left">Pembeli</th>
                        <th class="px-5 py-3 text-left">Tanggal</th>
                        <th class="px-5 py-3 text-left">Pickup</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $order->user?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ $order->pickup_time === '09:30' ? 'Istirahat 1' : 'Istirahat 2' }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $statusClass = match($order->order_status) {
                                    'selesai'      => 'bg-green-100 text-green-700',
                                    'diproses'     => 'bg-blue-100 text-blue-700',
                                    'siap_diambil' => 'bg-purple-100 text-purple-700',
                                    'batal'        => 'bg-red-100 text-red-700',
                                    default        => 'bg-yellow-100 text-yellow-700',
                                };
                                $statusLabel = match($order->order_status) {
                                    'menunggu'     => 'Menunggu',
                                    'diproses'     => 'Diproses',
                                    'siap_diambil' => 'Siap Diambil',
                                    'selesai'      => 'Selesai',
                                    'batal'        => 'Batal',
                                    default        => $order->order_status,
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
</x-app-layout>
