<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-primary-400 uppercase tracking-widest mb-1">Admin · Pesanan</p>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight flex items-center gap-3">
                    Detail Pesanan
                    <span class="text-primary-600">#{{ $order->id }}</span>
                </h2>
            </div>
            @php
                $statusConfig = [
                    'menunggu'     => ['label' => 'Menunggu',     'bg' => 'bg-amber-100',  'text' => 'text-amber-700',  'dot' => 'bg-amber-400',  'icon' => '⏳'],
                    'diproses'     => ['label' => 'Diproses',     'bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500',   'icon' => '⚙️'],
                    'siap_diambil' => ['label' => 'Siap Diambil', 'bg' => 'bg-green-100',  'text' => 'text-green-700',  'dot' => 'bg-green-500',  'icon' => '✅'],
                    'selesai'      => ['label' => 'Selesai',      'bg' => 'bg-emerald-100','text' => 'text-emerald-700','dot' => 'bg-emerald-500','icon' => '🏁'],
                    'batal'        => ['label' => 'Batal',        'bg' => 'bg-red-100',    'text' => 'text-red-700',    'dot' => 'bg-red-500',    'icon' => '❌'],
                ];
                $sc = $statusConfig[$order->order_status] ?? ['label' => $order->order_status, 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dot' => 'bg-gray-400', 'icon' => '•'];
            @endphp
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold {{ $sc['bg'] }} {{ $sc['text'] }}">
                <span class="w-2 h-2 rounded-full {{ $sc['dot'] }} animate-pulse inline-block"></span>
                {{ $sc['icon'] }} {{ $sc['label'] }}
            </span>
        </div>
    </x-slot>

    @php
        $statusSteps = ['menunggu', 'diproses', 'siap_diambil', 'selesai'];
        $currentIdx  = array_search($order->order_status, $statusSteps);
        $isCancelled = $order->order_status === 'batal';
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-sm">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            {{-- ─── Status Timeline ─────────────────────── --}}
            @if (!$isCancelled)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Progress Pesanan</p>
                <div class="flex items-center">
                    @foreach ($statusSteps as $idx => $step)
                        @php
                            $labels = ['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'siap_diambil' => 'Siap Diambil', 'selesai' => 'Selesai'];
                            $icons  = ['menunggu' => 'M12 8v4l3 3', 'diproses' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'siap_diambil' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'selesai' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'];
                            $done   = $currentIdx !== false && $idx <= $currentIdx;
                            $active = $currentIdx !== false && $idx === $currentIdx;
                        @endphp
                        <div class="flex flex-col items-center {{ $idx < count($statusSteps) - 1 ? 'flex-1' : '' }}">
                            <div class="relative flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300
                                {{ $active ? 'border-primary-600 bg-primary-600 shadow-lg shadow-primary-200' : ($done ? 'border-primary-400 bg-primary-100' : 'border-gray-200 bg-gray-50') }}">
                                <svg class="w-5 h-5 {{ $active ? 'text-white' : ($done ? 'text-primary-600' : 'text-gray-300') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$step] }}"/>
                                </svg>
                                @if ($active)
                                    <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-primary-500 border-2 border-white animate-ping"></span>
                                    <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-primary-500 border-2 border-white"></span>
                                @endif
                            </div>
                            <p class="mt-2 text-xs font-medium text-center leading-tight
                                {{ $active ? 'text-primary-700' : ($done ? 'text-primary-500' : 'text-gray-400') }}">
                                {{ $labels[$step] }}
                            </p>
                        </div>
                        @if ($idx < count($statusSteps) - 1)
                            <div class="flex-1 h-0.5 mx-1 mb-4 rounded-full transition-all duration-300
                                {{ ($currentIdx !== false && $idx < $currentIdx) ? 'bg-primary-400' : 'bg-gray-200' }}"></div>
                        @endif
                    @endforeach
                </div>
            </div>
            @else
            <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 shadow-sm">
                <span class="text-2xl">❌</span>
                <div>
                    <p class="font-semibold text-red-700">Pesanan Dibatalkan</p>
                    <p class="text-sm text-red-500">Pesanan ini telah dibatalkan dan tidak dapat diproses lebih lanjut.</p>
                </div>
            </div>
            @endif

            {{-- ─── Info Cards Row ──────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    $infoCards = [
                        ['icon' => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14', 'label' => 'ID Pesanan', 'value' => '#' . $order->id, 'accent' => 'from-violet-500 to-purple-600'],
                        ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Pemesan', 'value' => $order->user->name, 'accent' => 'from-blue-500 to-cyan-600'],
                        ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Tanggal Ambil', 'value' => $order->order_date->format('d M Y'), 'accent' => 'from-orange-400 to-rose-500'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Jam Ambil', 'value' => substr($order->pickup_time, 0, 5), 'accent' => 'from-teal-400 to-emerald-600'],
                    ];
                @endphp
                @foreach ($infoCards as $card)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-start gap-3 hover:shadow-md transition-shadow duration-200">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $card['accent'] }} flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">{{ $card['label'] }}</p>
                        <p class="text-base font-bold text-gray-800 mt-0.5 truncate">{{ $card['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ─── Order Items ─────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Item Pesanan
                    </h3>
                    <span class="text-xs font-semibold bg-primary-50 text-primary-600 px-3 py-1 rounded-full">
                        {{ $order->items->count() }} item
                    </span>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach ($order->items as $item)
                        @php
                            $photoUrl = null;
                            if ($item->menuItem && $item->menuItem->photo) {
                                $photoUrl = \Illuminate\Support\Str::startsWith($item->menuItem->photo, ['http://', 'https://'])
                                    ? $item->menuItem->photo
                                    : asset('storage/' . $item->menuItem->photo);
                            }
                        @endphp
                        <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/60 transition-colors duration-150">
                            {{-- Thumbnail --}}
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                                @if ($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="{{ $item->menuItem->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                            </div>
                            {{-- Name --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 truncate">{{ $item->menuItem->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->quantity }} × Rp {{ number_format((float) $item->price, 0, ',', '.') }}</p>
                            </div>
                            {{-- Qty badge --}}
                            <div class="shrink-0 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-primary-50 text-primary-700 font-bold text-sm rounded-lg">
                                    {{ $item->quantity }}
                                </span>
                            </div>
                            {{-- Subtotal --}}
                            <div class="shrink-0 text-right min-w-[100px]">
                                <p class="font-bold text-gray-800">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Total --}}
                <div class="flex items-center justify-between bg-gradient-to-r from-primary-600 to-violet-600 px-6 py-5">
                    <p class="text-primary-100 text-sm font-medium">Total Pesanan</p>
                    <p class="text-2xl font-extrabold text-white tracking-tight">
                        Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- ─── Update Status ───────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <h3 class="font-semibold text-gray-800">Perbarui Status Pesanan</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="flex flex-col sm:flex-row gap-3" id="statusForm">
                        @csrf
                        @method('PATCH')
                        <div class="relative flex-1">
                            <select name="order_status" id="statusSelect"
                                onchange="updateSelectStyle(this)"
                                class="w-full appearance-none rounded-xl border-2 border-gray-200 bg-gray-50 pl-4 pr-10 py-3 text-gray-700 font-medium focus:border-primary-500 focus:bg-white focus:ring-2 focus:ring-primary-100 focus:outline-none transition-all duration-200 cursor-pointer">
                                <option value="menunggu"     @selected($order->order_status === 'menunggu')>⏳ Menunggu</option>
                                <option value="diproses"     @selected($order->order_status === 'diproses')>⚙️ Diproses</option>
                                <option value="siap_diambil" @selected($order->order_status === 'siap_diambil')>✅ Siap Diambil</option>
                                <option value="selesai"      @selected($order->order_status === 'selesai')>🏁 Selesai</option>
                                <option value="batal"        @selected($order->order_status === 'batal')>❌ Batal</option>
                            </select>
                            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-violet-600 px-7 py-3 font-semibold text-white shadow-md hover:from-primary-700 hover:to-violet-700 active:scale-95 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            {{-- ─── Back ────────────────────────────────── --}}
            <div>
                <a href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border-2 border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-600 hover:border-gray-300 hover:text-gray-800 hover:shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Pesanan
                </a>
            </div>

        </div>
    </div>

    <script>
        function updateSelectStyle(sel) {
            const colors = {
                menunggu:     'border-amber-300  bg-amber-50  text-amber-800',
                diproses:     'border-blue-300   bg-blue-50   text-blue-800',
                siap_diambil: 'border-green-300  bg-green-50  text-green-800',
                selesai:      'border-emerald-300 bg-emerald-50 text-emerald-800',
                batal:        'border-red-300    bg-red-50    text-red-800',
            };
            // Reset
            sel.className = sel.className
                .replace(/border-\S+/g, '')
                .replace(/bg-\S+/g, '')
                .replace(/text-\S+/g, '');
            sel.className = 'w-full appearance-none rounded-xl border-2 pl-4 pr-10 py-3 font-medium focus:ring-2 focus:outline-none transition-all duration-200 cursor-pointer ' + (colors[sel.value] || 'border-gray-200 bg-gray-50 text-gray-700');
        }
        // Apply on load
        document.addEventListener('DOMContentLoaded', () => updateSelectStyle(document.getElementById('statusSelect')));
    </script>
</x-app-layout>
