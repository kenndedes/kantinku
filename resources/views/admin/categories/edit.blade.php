<x-app-layout>
    <x-slot name="header">Edit Kategori</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8 max-w-xl mx-auto">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Edit Kategori</h1>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Kategori</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        required>
                    @error('name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi (Opsional)</label>
                    <input id="description" type="text" name="description" value="{{ old('description', $category->description) }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        placeholder="Deskripsi singkat kategori">
                    @error('description') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 transition-all duration-200">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 transition-all duration-200 transform hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
