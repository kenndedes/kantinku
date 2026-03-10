<x-app-layout>
    <x-slot name="header">Kelola Seller</x-slot>

    @php
        $pending  = $profiles->where('status', 'pending')->count();
        $approved = $profiles->where('status', 'approved')->count();
        $rejected = $profiles->where('status', 'rejected')->count();
    @endphp

    <div class="space-y-6 p-4 sm:p-6 lg:p-8" x-data="{ tab: '{{ $pending > 0 ? 'pending' : 'all' }}' }">

        {{-- ── Header ── --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-700 via-purple-800 to-purple-900 px-6 py-8 shadow-lg">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(255,255,255,0.07),transparent_50%)]"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-purple-200 text-sm font-medium mb-1">Admin Panel</p>
                    <h1 class="text-2xl sm:text-3xl font-black text-white">Seller & Approval</h1>
                    <p class="text-purple-200 text-sm mt-1">Kelola pengajuan akun seller dan status verifikasi</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-center bg-white/10 border border-white/20 rounded-xl px-4 py-3 min-w-[72px]">
                        <p class="text-2xl font-black text-white">{{ $pending }}</p>
                        <p class="text-xs text-purple-200 font-medium">Pending</p>
                    </div>
                    <div class="text-center bg-white/10 border border-white/20 rounded-xl px-4 py-3 min-w-[72px]">
                        <p class="text-2xl font-black text-white">{{ $approved }}</p>
                        <p class="text-xs text-purple-200 font-medium">Approved</p>
                    </div>
                    <div class="text-center bg-white/10 border border-white/20 rounded-xl px-4 py-3 min-w-[72px]">
                        <p class="text-2xl font-black text-white">{{ $rejected }}</p>
                        <p class="text-xs text-purple-200 font-medium">Rejected</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Flash ── --}}
        @if (session('status'))
            <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-200">
                <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('status') }}
            </div>
        @endif

        {{-- ── Tab Filter ── --}}
        <div class="flex gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-xl w-fit">
            <button @click="tab = 'all'"
                :class="tab === 'all' ? 'bg-white dark:bg-gray-700 text-purple-700 dark:text-purple-300 shadow font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-150">
                Semua <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 text-xs font-bold">{{ $profiles->count() }}</span>
            </button>
            <button @click="tab = 'pending'"
                :class="tab === 'pending' ? 'bg-white dark:bg-gray-700 text-yellow-700 dark:text-yellow-300 shadow font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-150">
                Pending <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 text-xs font-bold">{{ $pending }}</span>
            </button>
            <button @click="tab = 'approved'"
                :class="tab === 'approved' ? 'bg-white dark:bg-gray-700 text-green-700 dark:text-green-300 shadow font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-150">
                Approved <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 text-xs font-bold">{{ $approved }}</span>
            </button>
            <button @click="tab = 'rejected'"
                :class="tab === 'rejected' ? 'bg-white dark:bg-gray-700 text-red-700 dark:text-red-300 shadow font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm transition-all duration-150">
                Rejected <span class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 text-xs font-bold">{{ $rejected }}</span>
            </button>
        </div>

        {{-- ── Table ── --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 bg-purple-50 dark:bg-purple-900/20">
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-purple-700 dark:text-purple-300">Seller</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-purple-700 dark:text-purple-300">Stand Diajukan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-purple-700 dark:text-purple-300">Daftar</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-purple-700 dark:text-purple-300">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide text-purple-700 dark:text-purple-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($profiles as $profile)
                            <tr x-show="tab === 'all' || tab === '{{ $profile->status }}'"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="hover:bg-purple-50/50 dark:hover:bg-purple-900/10 transition-colors">

                                {{-- Seller info --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white font-black text-sm shrink-0">
                                            {{ strtoupper(substr($profile->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $profile->user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $profile->user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Stand diajukan --}}
                                <td class="px-6 py-4">
                                    @if($profile->stand_name)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-800 text-purple-700 dark:text-purple-300 text-xs font-semibold">
                                            🏪 {{ $profile->stand_name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Tidak diisi</span>
                                    @endif
                                </td>

                                {{-- Tanggal daftar --}}
                                <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $profile->created_at->format('d M Y') }}<br>
                                    <span class="text-gray-400">{{ $profile->created_at->diffForHumans() }}</span>
                                </td>

                                {{-- Status badge --}}
                                <td class="px-6 py-4">
                                    @if($profile->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Approved
                                        </span>
                                    @elseif($profile->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($profile->status !== 'approved')
                                            <form action="{{ route('admin.sellers.update', $profile) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-green-500 hover:bg-green-600 active:scale-95 text-white text-xs font-bold shadow-sm shadow-green-200 dark:shadow-green-900/30 transition-all duration-150">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if($profile->status !== 'rejected')
                                            <form action="{{ route('admin.sellers.update', $profile) }}" method="POST"
                                                x-data
                                                @submit.prevent="if(confirm('Tolak seller {{ addslashes($profile->user->name) }}?')) $el.submit()">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 text-xs font-bold active:scale-95 transition-all duration-150">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Reject
                                                </button>
                                            </form>
                                        @endif

                                        @if($profile->status === 'approved')
                                            <span class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Aktif
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="text-5xl mb-3">🏪</div>
                                    <p class="font-semibold text-gray-500 dark:text-gray-400">Belum ada seller terdaftar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
