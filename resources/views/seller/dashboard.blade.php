<x-app-layout>
    <x-slot name="header">Dashboard Seller</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Order Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $todayOrders }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <p class="text-sm text-gray-500">Menunggu / Diproses</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pending }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aksi Cepat</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('seller.menu.index') }}" class="px-4 py-2 rounded-lg bg-primary-600 text-white">Kelola Menu</a>
                <a href="{{ route('seller.orders.index') }}" class="px-4 py-2 rounded-lg border">Lihat Pesanan</a>
            </div>
        </div>
    </div>
</x-app-layout>
