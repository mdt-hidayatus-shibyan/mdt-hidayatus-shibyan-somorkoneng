<form
    action="{{ isset($bulan) ? route('pengaturan-akademik.update-bulan', $bulan->id) : route('pengaturan-akademik.store-bulan') }}"
    method="POST" class="ajax-form" data-refresh-target="#data-grid-container">

    @csrf
    @if (isset($bulan))
        @method('PUT')
    @endif

    <!-- HIDDEN INPUT -->
    <input type="hidden" name="tahun_pelajaran_id"
        value="{{ isset($bulan) ? $bulan->tahun_pelajaran_id : $tahunPelajaran->id }}">

    <!-- HEADER MODAL -->
    <div
        class="px-5 py-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-white dark:bg-[#0c0c0e] rounded-t-3xl">
        <h3 class="font-black text-sm text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
            <div
                class="w-7 h-7 rounded-xl {{ isset($bulan) ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center text-xs shrink-0">
                <i class="bi {{ isset($bulan) ? 'bi-pencil-square' : 'bi-calendar-plus' }}"></i>
            </div>
            {{ isset($bulan) ? 'Edit Bulan Hijriyah' : 'Tambah Bulan Hijriyah' }}
        </h3>
        <button type="button" data-dismiss="modal"
            class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 hover:bg-rose-500/10 hover:text-rose-500 transition-colors outline-none">
            <i class="bi bi-x-lg text-xs font-black"></i>
        </button>
    </div>

    <!-- BODY MODAL -->
    <div class="p-5 space-y-4 bg-white dark:bg-[#0c0c0e]">

        <!-- VISUAL INPUT (Terkunci - Hanya Informasi) -->
        <div>
            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                Ikat Ke Tahun Pelajaran
            </label>
            <div class="relative">
                <input type="text" disabled
                    value="{{ isset($bulan) ? $bulan->tahunPelajaran->nama_hijriyah . ' H | ' . $bulan->tahunPelajaran->nama_masehi . ' M' : $tahunPelajaran->nama_hijriyah . ' H | ' . $tahunPelajaran->nama_masehi . ' M' }}"
                    class="m3-input-glass w-full text-xs font-bold opacity-60 cursor-not-allowed">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                    <i class="bi bi-lock-fill text-xs"></i>
                </div>
            </div>
        </div>

        <!-- GRID 4 KOLOM: Nama Bulan (2), Tahun (1), Urutan (1) -->
        <div class="grid grid-cols-4 gap-3.5">
            <div class="col-span-2">
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Nama Bulan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_bulan" placeholder="Misal: Syawal"
                    value="{{ isset($bulan) ? $bulan->nama_bulan : old('nama_bulan') }}"
                    class="m3-input-glass w-full text-xs font-bold">
            </div>

            <!-- INPUT BARU: Tahun Hijriyah -->
            <div class="col-span-1">
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1"
                    title="Tahun Hijriyah">
                    Tahun <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="tahun_hijriyah" placeholder="1448"
                    value="{{ isset($bulan) ? $bulan->tahun_hijriyah : old('tahun_hijriyah', isset($tahunPelajaran) ? explode('/', $tahunPelajaran->nama_hijriyah)[0] : '') }}"
                    class="m3-input-glass w-full text-center text-xs font-bold">
            </div>

            <div class="col-span-1">
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Urutan <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="urutan" placeholder="1-12"
                    value="{{ isset($bulan) ? $bulan->urutan : old('urutan') }}"
                    class="m3-input-glass w-full text-center text-xs font-bold">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3.5">
            <div>
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Mulai Masehi <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="tanggal_mulai_masehi"
                    value="{{ isset($bulan) ? $bulan->tanggal_mulai_masehi : old('tanggal_mulai_masehi') }}"
                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Selesai Masehi <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="tanggal_selesai_masehi"
                    value="{{ isset($bulan) ? $bulan->tanggal_selesai_masehi : old('tanggal_selesai_masehi') }}"
                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
            </div>
        </div>
    </div>

    <!-- FOOTER MODAL -->
    <div
        class="px-5 py-3.5 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200/80 dark:border-zinc-800 flex justify-end gap-2.5 rounded-b-3xl">
        <button type="button" data-dismiss="modal"
            class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors outline-none shadow-2xs">
            Batal
        </button>
        <button type="submit"
            class="m3-btn-primary px-4 py-2 rounded-xl text-xs font-black shadow-2xs flex items-center gap-1.5">
            <i class="bi bi-save-fill text-xs"></i> <span>Simpan</span>
        </button>
    </div>
</form>
