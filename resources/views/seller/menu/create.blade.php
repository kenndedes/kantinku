<x-app-layout>
    <x-slot name="header">Tambah Menu</x-slot>

    <div class="space-y-6 p-4 sm:p-6 lg:p-8 max-w-2xl mx-auto">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Tambah Menu Baru</h1>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 sm:p-8">
            <form action="{{ route('seller.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Menu</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        placeholder="Contoh: Nasi Goreng Spesial" required>
                    @error('name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                    <select id="category_id" name="category_id"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Harga (Rp)</label>
                    <input id="price" type="number" name="price" value="{{ old('price') }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        min="1000" step="100" placeholder="10000" required>
                    @error('price') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Stok</label>
                    <input id="stock" type="number" name="stock" value="{{ old('stock', 0) }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                        min="0" required>
                    @error('stock') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Foto Menu (Opsional)</label>
                    <div id="photo-dropzone" class="relative flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl px-4 py-6 text-center bg-gray-50 dark:bg-gray-700/40 hover:border-primary-400 hover:bg-primary-50/40 transition-colors cursor-pointer">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h10a4 4 0 004-4M7 10l5-5m0 0l5 5m-5-5v12" /></svg>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Tarik & letakkan atau klik untuk unggah</p>
                        <p class="text-xs text-gray-500 dark:text-gray-300">JPG, PNG, maksimal 2 MB</p>
                        <input id="photo" name="photo" type="file" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" aria-label="Unggah foto menu">
                    </div>
                    <div id="photo-preview-wrapper" class="hidden mt-3">
                        <div class="flex items-center gap-3">
                            <img id="photo-preview" src="" alt="Preview foto" class="w-20 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                            <button type="button" id="photo-remove" class="text-sm font-semibold text-red-600 hover:text-red-700 dark:text-red-400">Hapus foto</button>
                        </div>
                    </div>
                    @error('photo') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <input id="is_available" type="checkbox" name="is_available" value="1"
                        class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                        @checked(old('is_available', true))>
                    <label for="is_available" class="text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer">Menu tersedia dan dapat dipesan oleh pengguna</label>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('seller.menu.index') }}" class="inline-flex items-center justify-center rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 transition-all duration-200">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 transition-all duration-200 transform hover:scale-105 active:scale-95">
                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropzone = document.getElementById('photo-dropzone');
            const input = document.getElementById('photo');
            const previewWrapper = document.getElementById('photo-preview-wrapper');
            const previewImg = document.getElementById('photo-preview');
            const removeBtn = document.getElementById('photo-remove');

            const showPreview = (file) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target?.result;
                    previewWrapper.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            };

            const resetInput = () => {
                input.value = '';
                previewImg.src = '';
                previewWrapper.classList.add('hidden');
            };

            dropzone.addEventListener('click', (e) => { if (e.target !== input) input.click(); });
            dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('border-primary-400', 'bg-primary-50/40'); });
            dropzone.addEventListener('dragleave', () => { dropzone.classList.remove('border-primary-400', 'bg-primary-50/40'); });
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-primary-400', 'bg-primary-50/40');
                if (e.dataTransfer?.files?.length) {
                    const file = e.dataTransfer.files[0];
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    input.files = transfer.files;
                    showPreview(file);
                }
            });
            input.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (file) showPreview(file); else resetInput();
            });
            removeBtn.addEventListener('click', resetInput);
        });
    </script>
</x-app-layout>

