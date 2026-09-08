<!-- Modal Form Penyesuaian Stock Opname -->
<form action="{{ route('koperasi.stok.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    <input type="hidden" name="tipe_aksi" value="Opname">

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shrink-0">
                <i class="bi bi-clipboard-check text-base"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                    Penyesuaian Stock Opname Fisik
                </h3>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    Sinkronisasi stok fisik riil toko dengan data pada sistem
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
                Produk yang Di-Opname <span class="text-rose-500">*</span>
            </label>
            <select name="produk_id" required class="m3-input-glass w-full text-xs font-bold">
                <option value="">-- Pilih Produk --</option>
                @foreach ($produks as $pr)
                    <option value="{{ $pr->id }}">
                        {{ $pr->nama_produk }} (Stok Sistem: {{ $pr->stok }} {{ $pr->satuan }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Stok Fisik Riil -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Jumlah Stok Fisik Riil <span class="text-rose-500">*</span>
            </label>
            <input type="number" name="stok_fisik" min="0" required placeholder="Hasil hitung fisik..."
                class="m3-input-glass w-full text-xs font-black text-center text-amber-600 dark:text-amber-400">
        </div>

        <!-- Alasan Penyesuaian -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Alasan Penyesuaian <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="keterangan" required
                placeholder="Penyesuaian opname bulanan, ada barang rusak..."
                class="m3-input-glass w-full text-xs font-medium">
        </div>

    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>Simpan Penyesuaian</span>
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="m3-btn-secondary w-full sm:w-auto mt-2 sm:mt-0">
            Batal
        </button>
    </div>
</form>
