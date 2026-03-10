<x-app-layout>
    <x-slot name="header">Edit Stand</x-slot>

    <div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 space-y-4">
            <form action="{{ route('admin.stands.update', $stand) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Kode Stand</label>
                    <input type="text" name="code" value="{{ old('code', $stand->code) }}" class="mt-2 w-full rounded-lg border-gray-300" required>
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $stand->name) }}" class="mt-2 w-full rounded-lg border-gray-300" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $stand->location) }}" class="mt-2 w-full rounded-lg border-gray-300">
                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" class="rounded" {{ old('is_active', $stand->is_active) ? 'checked' : '' }}>
                    <label class="text-sm font-semibold text-gray-700">Aktif</label>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Assign Seller (opsional)</label>
                    <select name="seller_id" class="mt-2 w-full rounded-lg border-gray-300">
                        <option value="">Tidak ada</option>
                        @foreach ($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ old('seller_id', optional($stand->sellerProfile?->user)->id) == $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.stands.index') }}" class="px-4 py-2 rounded-lg border">Batal</a>
                    <button class="px-4 py-2 rounded-lg bg-primary-600 text-white">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
