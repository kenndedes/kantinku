<x-app-layout>
<div class="space-y-6 py-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Top Up</h1>
            <p class="text-sm text-gray-500 mt-0.5">Setujui atau tolak permintaan top up dari pengguna</p>
        </div>
        @if($pending > 0)
        <span class="inline-flex items-center gap-2 bg-amber-100 text-amber-800 font-semibold px-4 py-2 rounded-full text-sm">
            ⏳ {{ $pending }} menunggu persetujuan
        </span>
        @endif
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="bg-white rounded-2xl shadow p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Cari User</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..." class="w-full border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300 outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300 outline-none">
                <option value="">Semua</option>
                <option value="waiting_review" {{ request('status') === 'waiting_review' ? 'selected' : '' }}>⏳ Menunggu</option>
                <option value="approved"        {{ request('status') === 'approved'        ? 'selected' : '' }}>✅ Disetujui</option>
                <option value="rejected"        {{ request('status') === 'rejected'        ? 'selected' : '' }}>❌ Ditolak</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Metode</label>
            <select name="method" class="border rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300 outline-none">
                <option value="">Semua</option>
                @foreach($methods as $key => $m)
                <option value="{{ $key }}" {{ request('method') === $key ? 'selected' : '' }}>{{ $m['icon'] }} {{ $m['label'] }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-purple-700 transition">Filter</button>
        <a href="{{ route('admin.topup.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200 transition">Reset</a>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        @if($transactions->isEmpty())
            <div class="text-center py-16 text-gray-400">Tidak ada permintaan top up.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-right">Nominal</th>
                        <th class="px-4 py-3 text-left">Metode</th>
                        <th class="px-4 py-3 text-left">Bukti</th>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transactions as $tx)
                    @php
                        $badge = match($tx->payment_status) {
                            'approved'       => 'bg-green-100 text-green-700',
                            'rejected'       => 'bg-red-100 text-red-700',
                            'waiting_review' => 'bg-amber-100 text-amber-700',
                            default          => 'bg-gray-100 text-gray-500',
                        };
                        $labelTxt = match($tx->payment_status) {
                            'approved'       => '✅ Disetujui',
                            'rejected'       => '❌ Ditolak',
                            'waiting_review' => '⏳ Menunggu',
                            default          => $tx->payment_status,
                        };
                        $methodInfo = $methods[$tx->payment_method] ?? ['label' => $tx->payment_method, 'icon' => '💳'];
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $tx->payment_status === 'waiting_review' ? 'bg-amber-50/40' : '' }}">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-800">{{ $tx->user?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $tx->user?->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">
                            Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $methodInfo['icon'] }} {{ $methodInfo['label'] }}
                        </td>
                        <td class="px-4 py-3">
                            @if($tx->proof_image)
                                <a href="{{ Storage::url($tx->proof_image) }}" target="_blank"
                                   class="inline-block rounded-lg overflow-hidden border border-gray-200 hover:opacity-80 transition">
                                    <img src="{{ Storage::url($tx->proof_image) }}" alt="Bukti"
                                         class="h-14 w-14 object-cover">
                                </a>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $tx->created_at->format('d M Y') }}<br>
                            {{ $tx->created_at->format('H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">{{ $labelTxt }}</span>
                            @if($tx->payment_status === 'rejected' && $tx->admin_note)
                                <p class="text-xs text-red-400 mt-1 max-w-24">{{ Str::limit($tx->admin_note, 30) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($tx->payment_status === 'waiting_review')
                            <div class="flex flex-col gap-2 items-center">
                                {{-- Approve --}}
                                <form action="{{ route('admin.topup.approve', $tx) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Setujui top up Rp {{ number_format($tx->amount, 0, ',', '.') }} dari {{ $tx->user?->name }}?')"
                                            class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                        ✅ Setujui
                                    </button>
                                </form>
                                {{-- Reject with note --}}
                                <button type="button"
                                        onclick="openRejectModal({{ $tx->id }}, '{{ addslashes($tx->user?->name) }}')"
                                        class="inline-flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                    ❌ Tolak
                                </button>
                            </div>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t">{{ $transactions->links() }}</div>
        @endif
    </div>

</div>

{{-- Reject Modal --}}
<div id="reject-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Tolak Permintaan</h3>
        <p class="text-sm text-gray-500 mb-4" id="reject-modal-user"></p>
        <form id="reject-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan penolakan (opsional)</label>
                <textarea name="admin_note" rows="3"
                          class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 outline-none resize-none"
                          placeholder="Contoh: Bukti transfer tidak jelas, nominal tidak sesuai..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 rounded-xl transition text-sm">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-xl transition text-sm">
                    Tolak Permintaan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id, name) {
        document.getElementById('reject-modal-user').textContent = 'User: ' + name;
        document.getElementById('reject-form').action = '/admin/topup/' + id + '/reject';
        document.getElementById('reject-modal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
    }
    document.getElementById('reject-modal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>
</x-app-layout>
