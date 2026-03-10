<x-app-layout>
    <x-slot name="header">Detail Pesanan</x-slot>

    <div class="max-w-5xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Nomor Pesanan</p>
                    <p class="text-2xl font-bold">{{ $order->order_number }}</p>
                    <p class="text-sm text-gray-600">Pembeli: {{ $order->user->name }}</p>
                    @if($order->stand)
                        <p class="text-xs text-indigo-600 font-medium mt-1">🏪 {{ $order->stand->name }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Status Pesanan</p>
                    <p class="font-semibold">{{ ucfirst($order->order_status) }}</p>
                    <p class="text-sm text-gray-500">Pembayaran: {{ ucfirst($order->payment_status) }}</p>
                </div>
            </div>

            <div class="border-t pt-4 space-y-2">
                @foreach ($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <div>
                            <p class="font-semibold">{{ $item->item_name_snapshot ?? $item->menuItem->name }}</p>
                            <p class="text-gray-500">Qty {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right">
                            <p>Rp {{ number_format((float)($item->price_snapshot ?? $item->price), 0, ',', '.') }}</p>
                            <p class="text-gray-500">Subtotal: Rp {{ number_format((float)$item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t pt-4 flex justify-between text-lg font-bold">
                <span>Total</span>
                <span>Rp {{ number_format((float)$order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <form action="{{ route('seller.orders.updateStatus', $order) }}" method="POST" class="flex items-center gap-3">
                @csrf
                @method('PATCH')
                <label class="text-sm font-semibold">Update Status:</label>
                <select name="order_status" class="rounded border-gray-300">
                    <option value="menunggu" {{ $order->order_status === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diproses" {{ $order->order_status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="siap_diambil" {{ $order->order_status === 'siap_diambil' ? 'selected' : '' }}>Siap diambil</option>
                    <option value="selesai" {{ $order->order_status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="batal" {{ $order->order_status === 'batal' ? 'selected' : '' }}>Batal</option>
                </select>
                <button class="px-4 py-2 rounded-lg bg-primary-600 text-white">Simpan</button>
            </form>
        </div>

        {{-- Audit Log --}}
        @if($order->statusLogs->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Riwayat Status</h3>
            <ol class="relative border-l border-gray-200 space-y-4 pl-4">
                @foreach($order->statusLogs as $log)
                <li class="ml-2">
                    <div class="absolute -left-1.5 mt-1.5 w-3 h-3 rounded-full border-2 border-white bg-indigo-400"></div>
                    <p class="text-xs text-gray-400">{{ $log->created_at->format('d M Y H:i') }} — {{ $log->changedBy?->name ?? 'Sistem' }}</p>
                    <p class="text-sm font-medium text-gray-700">
                        {{ $log->from_status ? ucfirst(str_replace('_',' ',$log->from_status)).' →' : '' }}
                        <span class="text-indigo-600">{{ ucfirst(str_replace('_',' ',$log->to_status)) }}</span>
                    </p>
                </li>
                @endforeach
            </ol>
        </div>
        @endif
    </div>
</x-app-layout>
