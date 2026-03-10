<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-primary-50 to-gray-50 dark:from-primary-900 dark:to-gray-900 flex flex-col">
        <!-- Sidebar -->
        <aside id="sidebar" class="hidden lg:fixed lg:left-0 lg:top-0 lg:h-screen lg:w-64 lg:flex lg:flex-col bg-primary-800 dark:bg-primary-900 text-white z-40">
            <!-- Logo -->
            <div class="p-6 border-b border-primary-700">
                <h1 class="text-2xl font-bold text-center">KantiKu</h1>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto py-4 space-y-2 px-4">
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.menu.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.menu.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Data Menu
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.orders.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Pesanan
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 hover:bg-primary-700">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Laporan
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('menu.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('menu.*') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Lihat Menu
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 hover:bg-primary-700">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m10 0l2-8m0 0h2.71a1 1 0 00.986-1.164l-.882-5.772a2 2 0 00-1.971-1.636H5.4"/></svg>
                        Keranjang
                    </a>
                    <a href="{{ route('orders.history') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 {{ request()->routeIs('orders.history') ? 'bg-primary-700' : 'hover:bg-primary-700' }}">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Pesanan
                    </a>
                @endif
            </nav>

            <!-- Logout -->
            <div class="border-t border-primary-700 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-sm rounded-lg transition-colors duration-200 hover:bg-primary-700 text-white">
                        <svg class="w-5 h-5 me-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:ms-64 flex flex-col min-h-screen">
            <!-- TopBar -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="lg:hidden text-primary-600 hover:text-primary-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Header Title -->
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-100 flex-1 text-center lg:text-left">
                        {{ $header ?? 'Dashboard' }}
                    </h2>

                    <!-- User Dropdown -->
                    <div class="relative w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center cursor-pointer">
                        <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12a3 3 0 100-6 3 3 0 000 6z"/><path fill-rule="evenodd" d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22C6.48 22 2 17.52 2 12S6.48 2 12 2s10 4.48 10 10-4.48 10-10 10z"/></svg>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 overflow-y-auto">
                {{ $slot }}
            </main>

            <!-- Bottom Navigation (Mobile) -->
            <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-primary-800 border-t border-primary-700 flex">
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold {{ request()->routeIs('dashboard') ? 'text-white bg-primary-700' : 'text-primary-200 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.menu.index') }}" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold {{ request()->routeIs('admin.menu.*') ? 'text-white bg-primary-700' : 'text-primary-200 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Menu
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold {{ request()->routeIs('admin.orders.*') ? 'text-white bg-primary-700' : 'text-primary-200 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Pesanan
                    </a>
                    <button id="mobile-logout-btn" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold text-primary-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                        Logout
                    </button>
                @else
                    <a href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold {{ request()->routeIs('dashboard') ? 'text-white bg-primary-700' : 'text-primary-200 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9"/></svg>
                        Beranda
                    </a>
                    <a href="{{ route('menu.index') }}" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold {{ request()->routeIs('menu.*') ? 'text-white bg-primary-700' : 'text-primary-200 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Menu
                    </a>
                    <a href="#" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold text-primary-200 hover:text-white transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m10 0l2-8"/></svg>
                        Bag
                    </a>
                    <a href="{{ route('orders.history') }}" class="flex-1 flex flex-col items-center py-3 text-xs font-semibold {{ request()->routeIs('orders.history') ? 'text-white bg-primary-700' : 'text-primary-200 hover:text-white' }} transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                        Riwayat
                    </a>
                @endif
            </nav>
        </div>
    </div>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('hidden');
        });
        document.getElementById('mobile-logout-btn')?.addEventListener('click', function() {
            document.querySelector('form[action="{{ route("logout") }}"]')?.submit();
        });
    </script>
</x-guest-layout>
