<x-app-layout>
    <x-slot name="header">Konfirmasi Pesanan</x-slot>

    <div class="px-4 sm:px-6 lg:px-8 py-6 max-w-2xl mx-auto space-y-4">

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $e) <p>⚠ {{ $e }}</p> @endforeach
            </div>
        @endif

        @php $cart = session()->get('cart', []); @endphp

        @if(count($cart) == 0)
            <div class="text-center py-16">
                <p class="text-gray-500 mb-4">Keranjang kosong.</p>
                <a href="{{ route('menu.index') }}" class="text-purple-600 font-semibold hover:underline">← Kembali ke Menu</a>
            </div>
        @else

        {{-- Ringkasan pesanan --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-purple-600 to-indigo-600">
                <h3 class="text-white font-bold text-sm">Ringkasan Pesanan</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($cart as $item)
                <div class="px-5 py-3 flex justify-between items-center text-sm">
                    <div>
                        <span class="font-semibold text-gray-800">{{ $item['name'] }}</span>
                        <span class="text-gray-400 ml-1">×{{ $item['quantity'] }}</span>
                    </div>
                    <span class="font-bold text-gray-800">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @php $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cart)); @endphp
            <div class="px-5 py-4 bg-gray-50 flex justify-between items-center border-t border-gray-100">
                <span class="font-bold text-gray-700">Total</span>
                <span class="text-lg font-black text-purple-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('orders.store') }}" class="space-y-4">
            @csrf

            {{-- Tanggal Ambil --}}
            <div class="bg-white rounded-2xl shadow p-5 space-y-4">
                <h3 class="font-bold text-gray-800 text-sm">Jadwal Pengambilan</h3>

                <div>
                    <label for="order_date" class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Tanggal Ambil</label>
                    <input id="order_date" name="order_date" type="date"
                        value="{{ old('order_date', now()->toDateString()) }}"
                        min="{{ now()->toDateString() }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition" required>
                    @error('order_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="pickup_time" class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Waktu Pengambilan</label>
                    <select id="pickup_time" name="pickup_time"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition" required>
                        <option value="">Pilih waktu</option>
                        <option value="09:30" @selected(old('pickup_time') === '09:30')>⏰ Istirahat 1 — 09:30</option>
                        <option value="12:00" @selected(old('pickup_time') === '12:00')>⏰ Istirahat 2 — 12:00</option>
                    </select>
                    @error('pickup_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Metode Bayar --}}
            <div class="bg-white rounded-2xl shadow p-5 space-y-3">
                <h3 class="font-bold text-gray-800 text-sm">Metode Pembayaran</h3>

                <label class="flex items-center gap-3 p-3.5 border-2 rounded-xl cursor-pointer transition
                              has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 border-gray-200">
                    <input type="radio" name="payment_method" value="saldo" class="accent-purple-600" checked required>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 text-sm">💰 Saldo E-Canteen</p>
                        <p class="text-xs text-gray-500 mt-0.5">Saldo kamu: <span class="font-bold text-gray-700">Rp {{ number_format((float)auth()->user()->balance, 0, ',', '.') }}</span></p>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 border-2 rounded-xl cursor-pointer transition
                              has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 border-gray-200">
                    <input type="radio" name="payment_method" value="cash" class="accent-purple-600" required>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 text-sm">💵 Tunai</p>
                        <p class="text-xs text-gray-500 mt-0.5">Bayar langsung saat ambil pesanan</p>
                    </div>
                </label>

                @error('payment_method')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                @if(auth()->user()->balance < $total)
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-xs text-yellow-700">
                    ⚠️ Saldo tidak cukup untuk metode saldo. Gunakan <strong>Tunai</strong>.
                </div>
                @endif
            </div>

            {{-- Hidden cart data --}}
            @foreach($cart as $itemId => $item)
                <input type="hidden" name="menu_items[]" value="{{ $itemId }}">
                <input type="hidden" name="quantities[{{ $itemId }}]" value="{{ $item['quantity'] }}">
            @endforeach

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('cart.view') }}"
                   class="flex-1 text-center py-3.5 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                    ← Keranjang
                </a>
                <button type="submit"
                        class="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-sm shadow-md shadow-purple-200 transition">
                    Pesan Sekarang 🎉
                </button>
            </div>

        </form>
        @endif
    </div>
</x-app-layout>
