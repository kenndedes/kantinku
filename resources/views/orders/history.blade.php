<x-app-layout>
    <x-slot name="header">Riwayat Pesanan</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        @if (session('status'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-sm text-green-700 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif

        @forelse ($orders as $order)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <!-- Order Header -->
                <div class="bg-gradient-to-r from-primary-50 to-gray-50 dark:from-primary-900/30 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Pesanan #{{ $order->order_number ?? $order->id }}</p>
                        @php
                            $pickupLabel = match(substr($order->pickup_time ?? '', 0, 5)) {
                                '09:30' => 'Istirahat 1 (09:30)',
                                '12:00' => 'Istirahat 2 (12:00)',
                                default => substr($order->pickup_time ?? '', 0, 5),
                            };
                        @endphp
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $order->order_date->format('d M Y') }} • {{ $pickupLabel }}</p>
                        @if($order->stand)
                            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mt-0.5">🏪 {{ $order->stand->name }}</p>
                        @endif
                    </div>
                    <span class="inline-flex w-fit rounded-full px-4 py-2 text-sm font-semibold
                        {{ $order->status === 'menunggu' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                        {{ $order->status === 'diproses' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                        {{ $order->status === 'siap_diambil' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                        {{ $order->status === 'selesai' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}">
                        {{ str_replace('_', ' ', ucfirst($order->status)) }}
                    </span>
                </div>

                <!-- Order Items -->
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-3 pe-3">Menu</th>
                                    <th class="py-3 pe-3 text-center">Qty</th>
                                    <th class="py-3 pe-3 text-right">Harga</th>
                                    <th class="py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($order->items as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="py-3 pe-3 font-medium text-gray-900 dark:text-white">{{ $item->menuItem->name }}</td>
                                        <td class="py-3 pe-3 text-center">{{ $item->quantity }}</td>
                                        <td class="py-3 pe-3 text-right">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                                        <td class="py-3 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Footer -->
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <p>
                                Dipesan: <span class="text-gray-900 dark:text-white font-semibold">{{ $order->created_at->format('d M Y H:i') }}</span>
                            </p>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                Kode pickup: <span class="font-mono tracking-wide text-primary-700 dark:text-primary-300">{{ $order->pickup_code ?? 'Belum tersedia' }}</span>
                            </p>
                            @php
                                $badgeClass = match($order->payment_status) {
                                    'paid' => 'bg-green-100 text-green-700',
                                    'unpaid' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-gray-100 text-gray-500',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $order->payment_status === 'paid' ? '✓ Lunas' : ($order->payment_status === 'unpaid' ? '⏳ Belum Bayar' : ucfirst($order->payment_status)) }}
                            </span>
                        </div>
                        <p class="text-right text-lg font-bold text-gray-900 dark:text-white">
                            Total: <span class="text-primary-600 dark:text-primary-400">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <p class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada pesanan</p>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Mulai pesan menu favorit Anda sekarang juga!</p>
                <a href="{{ route('menu.index') }}" class="inline-flex items-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Lihat Menu
                </a>
            </div>
        @endforelse
    </div>
</x-app-layout>
