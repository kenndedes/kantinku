<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50">
        @php
            $role = auth()->user()->role;
            $isAdmin = $role === 'admin';
            $isSeller = $role === 'seller';
            $navMain = match (true) {
                $isAdmin => [
                    ['label' => 'Dashboard', 'route' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'M3.75 12l8.5-8.5c.3-.3.79-.3 1.09 0L20.25 12M5.5 10.75V19a.75.75 0 00.75.75h3.5a.75.75 0 00.75-.75v-3.5a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75V19a.75.75 0 00.75.75h3.5a.75.75 0 00.75-.75v-8.25M4.5 21h15' ],
                    ['label' => 'Stand', 'route' => route('admin.stands.index'), 'active' => request()->routeIs('admin.stands.*'), 'icon' => 'M3 5.25h18M4.5 9.75h15M6 14.25h12M9 18.75h6'],
                    ['label' => 'Seller', 'route' => route('admin.sellers.index'), 'active' => request()->routeIs('admin.sellers.*'), 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z M4.5 20.25a7.5 7.5 0 0115 0'],
                    ['label' => 'User', 'route' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*'), 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
                ],
                $isSeller => [
                    ['label' => 'Dashboard Seller', 'route' => route('seller.dashboard'), 'active' => request()->routeIs('seller.dashboard'), 'icon' => 'M3.75 12l8.5-8.5c.3-.3.79-.3 1.09 0L20.25 12M5.5 10.75V19a.75.75 0 00.75.75h3.5a.75.75 0 00.75-.75v-3.5a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75V19a.75.75 0 00.75.75h3.5a.75.75 0 00.75-.75v-8.25M4.5 21h15' ],
                    ['label' => 'Pesanan', 'route' => route('seller.orders.index'), 'active' => request()->routeIs('seller.orders.*'), 'icon' => 'M3 7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5v9.75A1.75 1.75 0 0119.25 19H5.25A2.25 2.25 0 013 16.75z M7.5 9h9' ],
                    ['label' => 'Menu Stand', 'route' => route('seller.menu.index'), 'active' => request()->routeIs('seller.menu.*'), 'icon' => 'M4.5 6.75h15m-15 3.75h15m-15 3.75h15M9 4.5v15' ],
                ],
                default => [
                    ['label' => 'Dashboard', 'route' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'M3.75 12l8.5-8.5c.3-.3.79-.3 1.09 0L20.25 12M5.5 10.75V19a.75.75 0 00.75.75h3.5a.75.75 0 00.75-.75v-3.5a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75V19a.75.75 0 00.75.75h3.5a.75.75 0 00.75-.75v-8.25M4.5 21h15' ],
                    ['label' => 'Pesanan', 'route' => route('cart.view'), 'active' => request()->routeIs('cart.*'), 'icon' => 'M3.75 5.25h1.5l1.3 9.1A1.75 1.75 0 008.28 16h7.94a1.75 1.75 0 001.72-1.42l.81-4.08A.75.75 0 0018.01 9H7.2' ],
                    ['label' => 'Menu Kantin', 'route' => route('menu.index'), 'active' => request()->routeIs('menu.*'), 'icon' => 'M4.5 6.75h15m-15 3.75h15m-15 3.75h15M9 4.5v15' ],
                ],
            };
            $navExtra = match (true) {
                $isAdmin => [
                    ['label' => 'Laporan', 'route' => route('admin.reports.index'), 'active' => request()->routeIs('admin.reports.*'), 'icon' => 'M4.5 19.5h15m-12-4.5 3 3 6-6m0-3-3-3-6 6'],
                    ['label' => 'Top Up', 'route' => route('admin.topup.index'), 'active' => request()->routeIs('admin.topup.*'), 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
                ],
                $isSeller => [
                    ['label' => 'Riwayat', 'route' => route('seller.orders.index'), 'active' => request()->routeIs('seller.orders.*'), 'icon' => 'M12 6v6l3.5 2.1M21 12A9 9 0 113 12a9 9 0 0118 0z'],
                    ['label' => 'Laporan', 'route' => route('seller.reports.index'), 'active' => request()->routeIs('seller.reports.*'), 'icon' => 'M4.5 19.5h15m-12-4.5 3 3 6-6m0-3-3-3-6 6'],
                ],
                default => [
                    ['label' => 'Riwayat', 'route' => route('orders.history'), 'active' => request()->routeIs('orders.history'), 'icon' => 'M12 6v6l3.5 2.1M21 12A9 9 0 113 12a9 9 0 0118 0z'],
                ],
            };
        @endphp

        <div class="min-h-screen flex">
            <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-30 opacity-0 pointer-events-none transition lg:hidden"></div>

            <!-- Sidebar -->
            <aside id="sidebar" class="fixed inset-y-0 left-0 w-72 bg-gradient-to-b from-purple-600 via-purple-700 to-purple-900 text-white transform -translate-x-full transition-transform duration-300 lg:translate-x-0 z-40 overflow-hidden shadow-2xl shadow-purple-200/30">
                <div class="h-full flex flex-col">
                    <!-- Logo -->
                    <div class="p-6 flex items-center justify-between border-b border-white/10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center font-black">{{ strtoupper(substr(config('app.name', 'K'), 0, 1)) }}</div>
                            <div>
                                <h1 class="text-xl font-black tracking-tight">{{ config('app.name', 'KantinKu') }}</h1>
                                <p class="text-[11px] uppercase tracking-[0.2em] text-purple-100/70">Dashboard System</p>
                            </div>
                        </div>
                        <button id="sidebar-close" class="lg:hidden text-white hover:text-purple-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 px-4 py-4 space-y-6 overflow-y-auto sidebar-scroll">
                        <div>
                            <p class="px-4 text-[11px] font-semibold text-purple-100/50 uppercase tracking-[0.15em] mb-3">Main Menu</p>
                            <div class="space-y-1">
                                @foreach ($navMain as $item)
                                    <a href="{{ $item['route'] }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all {{ $item['active'] ? 'bg-white/10 text-white shadow-inner' : 'text-purple-100 hover:bg-white/5 hover:text-white' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        @if (count($navExtra))
                            <div>
                                <p class="px-4 text-[11px] font-semibold text-purple-100/50 uppercase tracking-[0.15em] mb-3">{{ $isAdmin ? 'Management' : 'Aktivitas' }}</p>
                                <div class="space-y-1">
                                    @foreach ($navExtra as $item)
                                        <a href="{{ $item['route'] }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all {{ $item['active'] ? 'bg-white/10 text-white shadow-inner' : 'text-purple-100 hover:bg-white/5 hover:text-white' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                                            <span>{{ $item['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </nav>

                    <!-- Bottom Profile Section -->
                    <div class="p-5 border-t border-white/10 bg-white/5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="relative">
                                <div class="w-10 h-10 rounded-full bg-white/20 border border-white/30 flex items-center justify-center font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 border-purple-800 rounded-full"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-purple-100 uppercase tracking-[0.15em] truncate">{{ auth()->user()->role }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-red-500/20 text-purple-100 hover:text-red-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3-3m0 0l3 3m-3-3v12" /></svg>
                                <span class="text-xs font-semibold">Keluar Sesi</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main content wrapper -->
            <div class="flex-1 flex flex-col lg:pl-72 min-h-screen">
                <!-- Top Header -->
                <header class="bg-white border-b border-purple-100 sticky top-0 z-20">
                    <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-4">
                        <!-- Mobile menu button -->
                        <button id="sidebar-open" class="lg:hidden text-purple-700 hover:text-purple-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Page title -->
                        <h2 class="text-lg sm:text-xl font-semibold text-slate-800 flex-1 truncate">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </h2>

                        <!-- User profile -->
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <span class="text-purple-700 font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main content -->
                <main class="flex-1 pb-24 lg:pb-10 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="bg-white border-t border-purple-100 py-4 text-center text-sm text-slate-500 px-4">
                    <p>&copy; {{ date('Y') }} E-Canteen. All rights reserved.</p>
                </footer>
            </div>
        </div>

        <!-- Bottom navigation (mobile only) -->
        @php
            $navMobile = match (true) {
                $isAdmin => [
                    ['label' => 'Dashboard', 'route' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => '📊'],
                    ['label' => 'Pesanan', 'route' => route('admin.orders.index'), 'active' => request()->routeIs('admin.orders.*'), 'icon' => '📦'],
                    ['label' => 'Stand', 'route' => route('admin.stands.index'), 'active' => request()->routeIs('admin.stands.*'), 'icon' => '🏪'],
                    ['label' => 'Laporan', 'route' => route('admin.reports.index'), 'active' => request()->routeIs('admin.reports.*'), 'icon' => '📑'],
                ],
                $isSeller => [
                    ['label' => 'Dashboard', 'route' => route('seller.dashboard'), 'active' => request()->routeIs('seller.dashboard'), 'icon' => '📊'],
                    ['label' => 'Pesanan', 'route' => route('seller.orders.index'), 'active' => request()->routeIs('seller.orders.*'), 'icon' => '📦'],
                    ['label' => 'Menu', 'route' => route('seller.menu.index'), 'active' => request()->routeIs('seller.menu.*'), 'icon' => '🍜'],
                    ['label' => 'Laporan', 'route' => route('seller.reports.index'), 'active' => request()->routeIs('seller.reports.*'), 'icon' => '📑'],
                ],
                default => [
                    ['label' => 'Beranda', 'route' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => '🏠'],
                    ['label' => 'Menu', 'route' => route('menu.index'), 'active' => request()->routeIs('menu.*'), 'icon' => '🍜'],
                    ['label' => 'Keranjang', 'route' => route('cart.view'), 'active' => request()->routeIs('cart.*'), 'icon' => '🛒'],
                    ['label' => 'Riwayat', 'route' => route('orders.history'), 'active' => request()->routeIs('orders.history'), 'icon' => '⏰'],
                ],
            };
        @endphp
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-purple-100 flex z-30 shadow-lg">
            @foreach ($navMobile as $item)
                <a href="{{ $item['route'] }}" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold {{ $item['active'] ? 'text-purple-800' : 'text-slate-400 hover:text-purple-700' }} transition-colors">
                    <span class="text-lg">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <script>
            const sidebar = document.getElementById('sidebar');
            const sidebarOpen = document.getElementById('sidebar-open');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebarBackdrop = document.getElementById('sidebar-backdrop');

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                sidebarBackdrop.classList.remove('opacity-0', 'pointer-events-none');
            };

            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                sidebarBackdrop.classList.add('opacity-0', 'pointer-events-none');
            };

            sidebarOpen?.addEventListener('click', openSidebar);
            sidebarClose?.addEventListener('click', closeSidebar);
            sidebarBackdrop?.addEventListener('click', closeSidebar);
        </script>
    </body>
</html>
