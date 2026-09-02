<form
    action="{{ isset($tahunPelajaran) ? route('tahun-pelajaran.update', $tahunPelajaran->id) : route('tahun-pelajaran.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($tahunPelajaran))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            {{ isset($tahunPelajaran) ? 'Edit Tahun Pelajaran' : 'Tambah Tahun Pelajaran' }}
        </h3>
        <!-- Touch Target 40px -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1">

        <!-- Wrapper Form Input -->
        <div class="space-y-4">

            <!-- Nama Masehi -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Masehi
                </label>
                <input type="text" name="nama_masehi"
                    value="{{ $tahunPelajaran->nama_masehi ?? old('nama_masehi') }}" placeholder="Contoh: 2026-2027"
                    class="m3-input-glass w-full">
            </div>

            <!-- Nama Hijriyah -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Hijriyah
                </label>
                <input type="text" name="nama_hijriyah"
                    value="{{ $tahunPelajaran->nama_hijriyah ?? old('nama_hijriyah') }}" placeholder="Contoh: 1447-1448"
                    class="m3-input-glass w-full">
            </div>

        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($tahunPelajaran) ? 'Simpan Perubahan' : 'Simpan Data' }}</span>
        </button>
    </div>
</form>

