<!-- Modal Form Kategori Produk -->
<form action="{{ isset($kategori) ? route('koperasi.kategori.update', $kategori->id) : route('koperasi.kategori.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($kategori))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20 shrink-0">
                <i class="bi bi-tag-fill text-base"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ isset($kategori) ? 'Edit Kategori Produk' : 'Tambah Kategori Produk' }}
                </h3>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    Pengelompokan barang pada katalog toko & kasir POS
                </p>
            </div>
        </div>

        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1 space-y-4">

        <!-- Nama Kategori -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Nama Kategori <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_kategori" value="{{ $kategori->nama_kategori ?? old('nama_kategori') }}"
                placeholder="Contoh: Kitab Kuning, Seragam, Atribut, Alat Tulis..." class="m3-input-glass w-full text-xs font-bold" required>
        </div>

        <!-- Icon Bootstrap Icons -->
        <div class="space-y-1.5" x-data="{ currentIcon: '{{ $kategori->icon ?? old('icon', 'bi-tag-fill') }}' }">
            <div class="flex items-center justify-between ml-1">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                    Icon Bootstrap Icons
                </label>
                <span class="text-[10px] text-zinc-400">Contoh: bi-book, bi-pencil, bi-bag</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-lg shrink-0 text-zinc-700 dark:text-zinc-300">
                    <i :class="'bi ' + (currentIcon || 'bi-tag-fill')"></i>
                </div>
                <input type="text" name="icon" x-model="currentIcon"
                    placeholder="bi-book-half" class="m3-input-glass w-full text-xs font-mono font-bold">
            </div>
        </div>

        <!-- Keterangan -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Keterangan (Opsional)
            </label>
            <textarea name="keterangan" rows="2" placeholder="Deskripsi singkat jenis produk dalam kategori ini..."
                class="m3-input-glass w-full text-xs font-medium py-2.5">{{ $kategori->keterangan ?? old('keterangan') }}</textarea>
        </div>

        <!-- Checkbox Status Aktif -->
        <div class="flex items-center gap-2.5 pt-1 ml-1">
            <input type="checkbox" name="is_active" id="kat_is_active" value="1"
                {{ (isset($kategori) ? $kategori->is_active : true) ? 'checked' : '' }}
                class="rounded-lg border-zinc-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
            <label for="kat_is_active" class="text-xs font-bold text-zinc-700 dark:text-zinc-300 cursor-pointer">
                Kategori Aktif (Tersedia pada Filter & Pilihan Produk)
            </label>
        </div>

    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($kategori) ? 'Simpan Perubahan' : 'Simpan Kategori' }}</span>
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="m3-btn-secondary w-full sm:w-auto mt-2 sm:mt-0">
            Batal
        </button>
    </div>
</form>
