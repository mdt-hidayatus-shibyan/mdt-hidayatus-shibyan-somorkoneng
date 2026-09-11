<!-- Modal Form Gedung -->
<form action="{{ isset($gedung) ? route('gedung.update', $gedung->id) : route('gedung.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($gedung))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            {{ isset($gedung) ? 'Edit Data Gedung' : 'Tambah Gedung Baru' }}
        </h3>
        <!-- Close Button -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1">
        <div class="space-y-4">

            <!-- Kode Gedung -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kode Gedung <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kode_gedung" value="{{ $gedung->kode_gedung ?? old('kode_gedung') }}"
                    placeholder="Contoh: GD-A / GD-UTAMA" class="m3-input-glass w-full uppercase"
                    oninput="this.value = this.value.toUpperCase()" required>
            </div>

            <!-- Nama Gedung -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Gedung <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_gedung" value="{{ $gedung->nama_gedung ?? old('nama_gedung') }}"
                    placeholder="Contoh: Gedung Utama Madrasah / Asrama Putra" class="m3-input-glass w-full" required>
            </div>

            <!-- Jumlah Lantai -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Jumlah Lantai <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="jumlah_lantai"
                    value="{{ $gedung->jumlah_lantai ?? old('jumlah_lantai', 1) }}" min="1" max="20"
                    placeholder="1" class="m3-input-glass w-full" required>
            </div>

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Keterangan (Opsional)
                </label>
                <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan lokasi fisik atau deskripsi gedung..."
                    class="m3-input-glass w-full resize-none">{{ $gedung->keterangan ?? old('keterangan') }}</textarea>
            </div>

        </div>
    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto px-8">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($gedung) ? 'Simpan Perubahan' : 'Simpan Gedung' }}</span>
        </button>
    </div>
</form>
