<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-8">
        @if (session('status'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if (auth()->user()->role === 'admin')
            <!-- Admin Dashboard -->
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Selamat datang, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-600 mb-8">Kelola menu dan pesanan dikantin Anda</p>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Menu Card -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-600 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Total Menu</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\MenuItem::count() }}</p>
                            </div>
                            <span class="text-5xl">🍽️</span>
                        </div>
                    </div>

                    <!-- Today Orders -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Pesanan Hari Ini</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Order::whereDate('order_date', today())->count() }}</p>
                            </div>
                            <span class="text-5xl">📦</span>
                        </div>
                    </div>

                    <!-- Waiting Orders -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-600 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Menunggu Proses</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Order::where('status', 'menunggu')->count() }}</p>
                            </div>
                            <span class="text-5xl">⏳</span>
                        </div>
                    </div>

                    <!-- Today Revenue -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-600">Pendapatan Hari Ini</p>
                                <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format(\App\Models\Order::whereDate('order_date', today())->sum('total_price'), 0, ',', '.') }}</p>
                            </div>
                            <span class="text-5xl">💰</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('admin.orders.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-6 shadow-md hover:shadow-lg transition-all flex items-center gap-4">
                        <span class="text-4xl">📋</span>
                        <div>
                            <p class="font-semibold text-lg">Kelola Pesanan</p>
                            <p class="text-blue-100 text-sm">Update status dan lihat detail pesanan</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.sellers.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-6 shadow-md hover:shadow-lg transition-all flex items-center gap-4">
                        <span class="text-4xl">🏪</span>
                        <div>
                            <p class="font-semibold text-lg">Kelola Seller</p>
                            <p class="text-purple-100 text-sm">Verifikasi dan kelola akun seller</p>
                        </div>
                    </a>
                </div>
            </div>
        @else
            <!-- User Dashboard -->
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Selamat datang, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-600 mb-8">Pesan makanan dan minuman favorit Anda sekarang</p>

                <!-- Balance Card -->
                <div class="bg-gradient-to-r from-primary-600 to-primary-800 text-white rounded-2xl p-8 mb-8 shadow-lg">
                    <p class="text-sm opacity-90">Saldo Anda</p>
                    <p class="text-4xl font-bold mt-2">Rp {{ number_format((float) auth()->user()->balance, 0, ',', '.') }}</p>
                    <div class="mt-6 flex gap-4">
                        <a href="{{ route('topup.index') }}" class="bg-white text-primary-600 font-semibold px-6 py-2 rounded-lg hover:bg-gray-100 transition">💳 Top Up</a>
                        <a href="{{ route('menu.index') }}" class="bg-primary-500 hover:bg-primary-400 font-semibold px-6 py-2 rounded-lg transition">🛒 Semua Stand</a>
                    </div>
                </div>

                <!-- Stand Cards (ShopeeFood-style) -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-black text-gray-900">🏪 Pilih Stand</h2>
                        <a href="{{ route('menu.index') }}" class="text-sm font-semibold text-purple-600 hover:text-purple-800">Lihat semua →</a>
                    </div>

                    @if($stands->isEmpty())
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center text-gray-400">
                            <p class="text-4xl mb-2">🍽️</p>
                            <p class="text-sm">Belum ada stand yang tersedia.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($stands as $stand)
                                @php
                                    $img = optional($stand->menuItems()->where('is_available', true)->whereNotNull('photo')->first())->photo;
                                    $coverUrl = $img
                                        ? (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : asset('storage/' . $img))
                                        : null;
                                @endphp
                                <a href="{{ route('menu.index', ['stand_id' => $stand->id]) }}"
                                   class="group bg-white rounded-2xl shadow-md hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-200 hover:-translate-y-1 active:scale-95">
                                    <div class="relative h-28 bg-gradient-to-br from-purple-100 to-purple-200 overflow-hidden">
                                        @if($coverUrl)
                                            <img src="{{ $coverUrl }}" alt="{{ $stand->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-4xl">🏪</div>
                                        @endif
                                    </div>
                                    <div class="p-3">
                                        <p class="font-bold text-gray-900 text-sm leading-tight line-clamp-2">{{ $stand->name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $stand->available_menu_count }} menu</p>
                                        @if($stand->location)
                                            <p class="text-xs text-purple-600 mt-0.5 truncate">📍 {{ $stand->location }}</p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Shopping Cart Widget -->
                @php
                    $cart = session()->get('cart', []);
                    $cartTotal = 0;
                    foreach ($cart as $item) {
                        $cartTotal += $item['price'] * $item['quantity'];
                    }
                @endphp
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <a href="{{ route('cart.view') }}" class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4 flex items-center justify-between hover:from-primary-700 hover:to-primary-800 transition-all cursor-pointer">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <h3 class="text-white font-semibold text-lg">Keranjang Belanja</h3>
                        </div>
                        @if(count($cart) > 0)
                            <span class="bg-red-500 text-white rounded-full px-3 py-1 text-sm font-bold">{{ count($cart) }} item</span>
                        @endif
                    </a>
                    <div class="p-6">
                        @if(count($cart) > 0)
                            <div class="space-y-3 mb-6">
                                @foreach($cart as $itemId => $item)
                                    <div class="flex items-center justify-between pb-3 border-b border-gray-200 last:border-b-0">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $item['name'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                        </div>
                                        <p class="font-semibold text-primary-600 text-lg">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-gray-700">Total:</p>
                                    <p class="text-2xl font-bold text-primary-600">Rp {{ number_format($cartTotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('cart.view') }}" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition text-center">
                                🛒 Lihat Keranjang & Checkout
                            </a>
                        @else
                            <p class="text-center text-gray-600 py-8">Keranjang Anda kosong. <a href="{{ route('menu.index') }}" class="text-primary-600 font-semibold hover:underline">Mulai berbelanja!</a></p>
                        @endif
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-600">
                        <p class="text-sm font-semibold text-gray-600">Total Pesanan</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->orders()->count() }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-600">
                        <p class="text-sm font-semibold text-gray-600">Pesanan Aktif</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->orders()->whereIn('status', ['menunggu', 'diproses'])->count() }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-600">
                        <p class="text-sm font-semibold text-gray-600">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format(auth()->user()->orders()->sum('total_price'), 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                        <h3 class="text-white font-semibold text-lg">Pesanan Terbaru</h3>
                    </div>
                    <div class="p-6">
                        @if(auth()->user()->orders()->latest()->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Jam</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach(auth()->user()->orders()->latest()->take(5)->get() as $order)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-800">#{{ $order->id }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ $order->order_date->format('d M Y') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-800">{{ substr($order->pickup_time, 0, 5) }}</td>
                                                <td class="px-4 py-3 text-sm font-semibold text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-sm">
                                                    @php
                                                        $statusColors = [
                                                            'menunggu' => 'bg-yellow-100 text-yellow-800',
                                                            'diproses' => 'bg-blue-100 text-blue-800',
                                                            'siap_diambil' => 'bg-green-100 text-green-800',
                                                        ];
                                                        $statusLabels = [
                                                            'menunggu' => 'Menunggu',
                                                            'diproses' => 'Diproses',
                                                            'siap_diambil' => 'Siap Diambil',
                                                        ];
                                                    @endphp
                                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-gray-600 py-8">Belum ada pesanan. <a href="{{ route('menu.index') }}" class="text-purple-600 font-semibold hover:underline">Pesan sekarang!</a></p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
