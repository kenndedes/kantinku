<x-app-layout>
    <x-slot name="header">Daftar User</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Daftar User</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total: {{ $users->total() }} user terdaftar</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-5 text-sm transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah User
            </a>
        </div>
        @if(session('status'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        {{-- Search --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2">
            <div class="relative flex-1 max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama atau email..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">Cari</button>
            @if(!empty($search))
                <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 px-3 py-2 rounded-xl text-sm transition">✕ Reset</a>
            @endif
        </form>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nama</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Saldo</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $users->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($user->photo)
                                            <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-700 dark:text-primary-300 font-bold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format((float) $user->balance, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada user terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
