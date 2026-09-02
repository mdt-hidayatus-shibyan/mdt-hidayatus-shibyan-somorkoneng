<form
    action="{{ isset($kategori_kegiatan) ? route('kategori-kegiatan.update', $kategori_kegiatan->id) : route('kategori-kegiatan.store') }}"
    method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh] bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl rounded-2xl md:rounded-3xl overflow-hidden border border-zinc-200/80 dark:border-zinc-800 shadow-2xl">
    @csrf
    @if (isset($kategori_kegiatan))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="px-5 py-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/80 dark:bg-zinc-950/60 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center border border-primary/20 shrink-0 shadow-2xs">
                <i class="bi bi-tags text-base"></i>
            </div>
            <div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    {{ isset($kategori_kegiatan) ? 'Edit Kategori' : 'Tambah Kategori' }}
                </h3>
                <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mt-0.5">Parameter Label Kegiatan</p>
            </div>
        </div>

        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-9 min-h-9 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none"
            title="Tutup">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
        <div class="space-y-4">

            <!-- Nama Kategori -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_kategori"
                    value="{{ $kategori_kegiatan->nama_kategori ?? old('nama_kategori') }}"
                    placeholder="Contoh: Libur Nasional, Ujian, dll..." class="m3-input-glass w-full font-bold text-xs">
            </div>

            <!-- Kode Warna -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                    Warna Penanda Label
                </label>

                <!-- Container Color Picker Modern -->
                <div
                    class="flex items-center gap-3 p-3 bg-zinc-50/80 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800 rounded-xl transition-colors group">

                    <div
                        class="relative w-10 h-10 rounded-lg overflow-hidden shrink-0 shadow-2xs border border-zinc-300 dark:border-zinc-700 transition-all">
                        <input type="color" name="kode_warna"
                            value="{{ $kategori_kegiatan->kode_warna ?? '#0ea5e9' }}"
                            class="absolute -top-3 -left-3 w-16 h-16 cursor-pointer border-0 bg-transparent p-0 outline-none">
                    </div>

                    <div class="flex-1">
                        <p class="text-xs font-black text-zinc-800 dark:text-zinc-200 tracking-tight">Pilih Warna Label</p>
                        <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mt-0.5">Klik kotak warna untuk mengganti</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="px-5 py-3.5 bg-zinc-50/80 dark:bg-zinc-950/60 border-t border-zinc-200/80 dark:border-zinc-800 flex flex-col-reverse sm:flex-row justify-end gap-2.5 shrink-0">
        <button type="button" data-dismiss="modal"
            class="m3-btn-secondary w-full sm:w-auto h-10 px-5">
            Batal
        </button>
        <button type="submit"
            class="m3-btn-primary w-full sm:w-auto h-10 px-6 group/btn">
            <i class="bi bi-save2-fill text-xs"></i>
            <span>{{ isset($kategori_kegiatan) ? 'Simpan Perubahan' : 'Simpan Baru' }}</span>
        </button>
    </div>
</form>

