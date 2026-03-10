<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            @if ($selectedStand)
                <div class="flex items-center gap-2">
                    <a href="{{ route('menu.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <span class="font-semibold text-gray-900">{{ $selectedStand->name }}</span>
                </div>
            @else
                <span class="font-semibold">Pesan Makanan</span>
            @endif
            <a href="{{ route('cart.view') }}" class="relative">
                <div class="bg-primary-600 text-white rounded-lg px-3 py-2 hover:bg-primary-700 transition flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span class="font-semibold">Keranjang</span>
                    @php $cart = session()->get('cart', []); @endphp
                    <span id="cart-badge" class="bg-red-500 text-white rounded-full w-5 h-5 items-center justify-center text-xs font-bold {{ count($cart) > 0 ? 'flex' : 'hidden' }}">{{ count($cart) }}</span>
                </div>
            </a>
        </div>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        {{-- â”€â”€ STAND SELECTION VIEW â”€â”€ --}}
        @if (! $selectedStand)
            <div>
                <h2 class="text-2xl font-black text-gray-900 mb-1">Pilih Stand</h2>
                <p class="text-sm text-gray-500 mb-6">Klik stand untuk melihat menu yang tersedia</p>

                @if ($stands->isEmpty())
                    <div class="text-center py-16 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="font-semibold text-gray-500">Belum ada stand yang tersedia saat ini.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($stands as $stand)
                            @php
                                $img = optional($stand->menuItems()->where('is_available', true)->whereNotNull('photo')->first())->photo;
                                $coverUrl = $img
                                    ? (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : asset('storage/' . $img))
                                    : null;
                            @endphp
                            <a href="{{ route('menu.index', ['stand_id' => $stand->id]) }}"
                               class="group bg-white rounded-2xl shadow-md hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-200 hover:-translate-y-1 active:scale-95">
                                {{-- Cover image --}}
                                <div class="relative h-32 bg-gradient-to-br from-purple-100 to-purple-200 overflow-hidden">
                                    @if($coverUrl)
                                        <img src="{{ $coverUrl }}" alt="{{ $stand->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-100 to-purple-200">
                                            <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                    @endif
                                </div>
                                {{-- Info --}}
                                <div class="p-3">
                                    <p class="font-bold text-gray-900 text-sm leading-tight line-clamp-2">{{ $stand->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $stand->available_menu_count }} menu tersedia</p>
                                    @if($stand->location)
                                        <p class="text-xs text-purple-600 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $stand->location }}
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        {{-- â”€â”€ MENU LIST VIEW (stand selected) â”€â”€ --}}
        @else
            {{-- Stand header --}}
            <div class="rounded-2xl overflow-hidden shadow-md bg-gradient-to-br from-purple-700 to-purple-900 text-white p-6 flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black">{{ $selectedStand->name }}</h2>
                    @if($selectedStand->location)
                        <p class="text-purple-200 text-sm flex items-center gap-1 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $selectedStand->location }}
                        </p>
                    @endif
                    <p class="text-purple-200 text-sm mt-0.5">{{ $menuItems->count() }} menu tersedia</p>
                </div>
            </div>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('menu.index') }}" class="flex gap-2">
                <input type="hidden" name="stand_id" value="{{ $selectedStandId }}">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ $search ?? '' }}"
                           placeholder="Cari nama menu..."
                           class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                </div>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">Cari</button>
                @if(!empty($search))
                    <a href="{{ route('menu.index', ['stand_id' => $selectedStandId]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2.5 rounded-xl text-sm transition">✕</a>
                @endif
            </form>

            @if ($menuItems->isEmpty())
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="font-semibold text-gray-500">{{ !empty($search) ? 'Tidak ada menu yang cocok dengan "' . $search . '".' : 'Stand ini belum punya menu yang tersedia.' }}</p>
                    <a href="{{ route('menu.index') }}" class="mt-4 inline-block text-purple-600 font-semibold hover:underline">&larr; Kembali pilih stand</a>
                </div>
            @else
                @php $groupedItems = $menuItems->groupBy(fn($i) => $i->category?->name ?? 'Lainnya'); @endphp

                @foreach ($groupedItems as $categoryName => $items)
                    <div>
                        <h3 class="text-base font-bold text-gray-700 mb-3 flex items-center gap-2">
                            @if(strtolower($categoryName) === 'makanan')
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @elseif(strtolower($categoryName) === 'minuman')
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v11a3 3 0 006 0V3M6 21h12"/></svg>
                            @else
                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            @endif
                            <span>{{ $categoryName }}</span>
                            <span class="text-xs font-normal text-gray-400">({{ $items->count() }})</span>
                        </h3>
                        <div class="space-y-3">
                            @foreach ($items as $item)
                                @php
                                    $photoUrl = null;
                                    if ($item->photo) {
                                        $photoUrl = \Illuminate\Support\Str::startsWith($item->photo, ['http://', 'https://'])
                                            ? $item->photo
                                            : asset('storage/' . $item->photo);
                                    }
                                @endphp
                                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex gap-4 items-center hover:shadow-md transition">
                                    {{-- Photo --}}
                                    <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                                        <img src="{{ $photoUrl ?: 'https://via.placeholder.com/160x160?text=Menu' }}"
                                             alt="{{ $item->name }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 text-sm leading-tight">{{ $item->name }}</p>
                                        <p class="text-purple-700 font-black text-lg mt-1">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Stok: {{ $item->stock }}</p>
                                    </div>
                                    {{-- Qty + CTA --}}
                                    <div class="flex flex-col items-end gap-2 shrink-0">
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" onclick="decreaseQty({{ $item->id }})"
                                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                            </button>
                                            <input type="number" id="qty-{{ $item->id }}" value="1" min="1" max="10"
                                                class="qty-input w-10 h-8 text-center border border-gray-200 rounded-lg text-sm font-bold focus:outline-none focus:border-purple-400"
                                                onchange="validateQty({{ $item->id }})"
                                                readonly>
                                            <button type="button" onclick="increaseQty({{ $item->id }})"
                                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                            </button>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button"
                                                onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                class="px-3 py-1.5 rounded-lg border border-purple-500 text-purple-600 hover:bg-purple-50 text-xs font-bold transition">
                                                + Keranjang
                                            </button>
                                            <button type="button"
                                                onclick="buyNow({{ $item->id }}, '{{ addslashes($item->name) }}')"
                                                class="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition">
                                                Beli
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        @endif

    </div>

    {{-- Toast --}}
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 pointer-events-none" style="transform:translateX(-50%) translateY(20px);opacity:0;transition:opacity .25s,transform .25s"></div>

    <style>
        input[type=number].qty-input::-webkit-inner-spin-button,
        input[type=number].qty-input::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number].qty-input { -moz-appearance: textfield; }
    </style>

    <script>
        const CART_ADD_URL = '{{ url("/cart/add") }}';
        const CART_VIEW_URL = '{{ route("cart.view") }}';
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function increaseQty(id) {
            const input = document.getElementById('qty-' + id);
            const v = parseInt(input.value) || 1;
            if (v < 10) input.value = v + 1;
        }
        function decreaseQty(id) {
            const input = document.getElementById('qty-' + id);
            const v = parseInt(input.value) || 1;
            if (v > 1) input.value = v - 1;
        }
        function validateQty(id) {
            const input = document.getElementById('qty-' + id);
            let v = parseInt(input.value) || 1;
            input.value = Math.min(Math.max(v, 1), 10);
        }

        function postCart(itemId, quantity) {
            return fetch(CART_ADD_URL + '/' + itemId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ quantity })
            }).then(r => {
                if (!r.ok && r.status !== 422) throw new Error('HTTP ' + r.status);
                return r.json();
            });
        }

        function addToCart(itemId, itemName) {
            const quantity = parseInt(document.getElementById('qty-' + itemId).value) || 1;
            postCart(itemId, quantity)
                .then(data => {
                    if (data.success) {
                        showToast('✓ ' + (quantity > 1 ? quantity + '× ' : '') + itemName + ' ditambahkan ke keranjang', 'success');
                        document.getElementById('qty-' + itemId).value = 1;
                        updateCartBadge(data.cart_count);
                    } else {
                        showToast('⚠ ' + (data.message || 'Gagal menambahkan ke keranjang'), 'error');
                    }
                })
                .catch(() => showToast('⚠ Gagal terhubung ke server', 'error'));
        }

        function buyNow(itemId, itemName) {
            const quantity = parseInt(document.getElementById('qty-' + itemId).value) || 1;
            postCart(itemId, quantity)
                .then(data => {
                    if (data.success) {
                        window.location.href = CART_VIEW_URL;
                    } else {
                        showToast('⚠ ' + (data.message || 'Gagal menambahkan ke keranjang'), 'error');
                    }
                })
                .catch(() => showToast('⚠ Gagal terhubung ke server', 'error'));
        }

        function updateCartBadge(count) {
            const badge = document.getElementById('cart-badge');
            if (!badge) return;
            badge.textContent = count;
            if (count > 0) {
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }

        let toastTimer;
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            const bg = type === 'error' ? '#dc2626' : '#16a34a';
            toast.innerHTML = `<div style="background:${bg};color:#fff;padding:12px 20px;border-radius:14px;font-size:14px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.18);white-space:nowrap">${msg}</div>`;
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(20px)';
            }, 3000);
        }
    </script>
</x-app-layout>
