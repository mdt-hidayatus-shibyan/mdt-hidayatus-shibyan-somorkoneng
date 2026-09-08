<!-- Modal Form Restock / Stok Masuk -->
<form action="{{ route('koperasi.stok.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    <input type="hidden" name="tipe_aksi" value="Masuk">

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0">
                <i class="bi bi-box-arrow-in-down text-base"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                    Pencatatan Stok Masuk (Restock)
                </h3>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    Tambah kuantitas persediaan barang ke inventori toko koperasi
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

        <!-- Produk Select -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Produk / Kitab <span class="text-rose-500">*</span>
            </label>
            <select name="produk_id" required class="m3-input-glass w-full text-xs font-bold">
                <option value="">-- Pilih Produk --</option>
                @foreach ($produks as $pr)
                    <option value="{{ $pr->id }}">
                        {{ $pr->nama_produk }} (Stok Saat Ini: {{ $pr->stok }} {{ $pr->satuan }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Jumlah Masuk -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Jumlah Masuk <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="jumlah" min="1" required placeholder="0"
                    class="m3-input-glass w-full text-xs font-bold text-center">
            </div>

            <!-- Harga Beli Baru -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Harga Beli Baru (Opsional)
                </label>
                <input type="number" step="100" name="harga_beli" placeholder="Jika ada perubahan HPP"
                    class="m3-input-glass w-full text-xs font-mono font-bold text-right">
            </div>
        </div>

        <!-- No Surat Jalan / Referensi -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                No. Surat Jalan / Referensi
            </label>
            <input type="text" name="referensi" placeholder="Contoh: SJ-202609-01 atau Toko Barokah"
                class="m3-input-glass w-full text-xs font-semibold">
        </div>

        <!-- Keterangan -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Keterangan <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="keterangan" required
                placeholder="Restock kitab persiapan tahun ajaran baru..."
                class="m3-input-glass w-full text-xs font-medium">
        </div>

    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>Simpan Stok Masuk</span>
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="m3-btn-secondary w-full sm:w-auto mt-2 sm:mt-0">
            Batal
        </button>
    </div>
</form>
