<x-app-layout>
    <x-slot name="header">Keranjang Belanja</x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-2xl mx-auto space-y-4">

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
        @endif

        @php $cart = session()->get('cart', []); @endphp

        @if(count($cart) == 0)
            <div class="bg-white rounded-2xl shadow p-10 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h2 class="text-xl font-bold text-gray-700 mb-1">Keranjang Kosong</h2>
                <p class="text-gray-400 text-sm mb-6">Yuk pilih menu yang kamu suka!</p>
                <a href="{{ route('menu.index') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-8 rounded-xl transition">
                    Lihat Menu
                </a>
            </div>
        @else
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">{{ count($cart) }} item di keranjang</h2>
                <button onclick="clearCart()" class="text-xs text-red-500 hover:text-red-700 font-semibold transition">Kosongkan</button>
            </div>

            <div class="bg-white rounded-2xl shadow divide-y divide-gray-100 overflow-hidden">
                @foreach($cart as $itemId => $item)
                    <div class="p-4 flex gap-3 items-center">
                        <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                            @php
                                $photo = $item['photo'] ?? null;
                                $photoUrl = $photo
                                    ? (\Illuminate\Support\Str::startsWith($photo, ['http://', 'https://']) ? $photo : asset('storage/' . $photo))
                                    : 'https://via.placeholder.com/100';
                            @endphp
                            <img src="{{ $photoUrl }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $item['name'] }}</p>
                            <p class="text-purple-600 font-bold text-sm mt-0.5">Rp {{ number_format((float)$item['price'], 0, ',', '.') }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button onclick="updateQuantity({{ $itemId }}, {{ $item['quantity'] - 1 }})"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-lg leading-none transition">&#8722;</button>
                            <span class="w-8 text-center font-bold text-sm">{{ $item['quantity'] }}</span>
                            <button onclick="updateQuantity({{ $itemId }}, {{ $item['quantity'] + 1 }})"
                                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-lg leading-none transition">&#43;</button>
                        </div>
                        <div class="text-right shrink-0 ml-1">
                            <p class="font-bold text-sm text-gray-800">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            <button onclick="removeItem({{ $itemId }})" class="text-xs text-red-400 hover:text-red-600 mt-0.5 transition">Hapus</button>
                        </div>
                    </div>
                @endforeach
            </div>

            @php $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart)); @endphp
            <div class="bg-white rounded-2xl shadow p-5 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-sm">Total</span>
                    <span class="text-xl font-black text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('checkout.index') }}"
                   class="block w-full text-center bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-md shadow-purple-200 transition text-sm">
                    Lanjut ke Checkout &#8594;
                </a>
                <a href="{{ route('menu.index') }}" class="block w-full text-center text-sm text-purple-600 hover:underline font-medium">
                    + Tambah menu lagi
                </a>
            </div>
        @endif
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateQuantity(itemId, newQty) {
            if (newQty < 1) { removeItem(itemId); return; }
            fetch('{{ url("/cart/update") }}/' + itemId, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ quantity: newQty }),
            }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
        }

        function removeItem(itemId) {
            fetch('{{ url("/cart/remove") }}/' + itemId, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
        }

        function clearCart() {
            if (!confirm('Kosongkan semua item dari keranjang?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("cart.clear") }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-app-layout>
