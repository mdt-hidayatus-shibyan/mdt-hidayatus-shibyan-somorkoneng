<form
    action="{{ isset($semester) ? route('pengaturan-akademik.update-semester', $semester->id) : route('pengaturan-akademik.store-semester') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($semester))
        @method('PUT')
    @endif
    <!-- Modal Header -->
    <div
        class="bg-white dark:bg-[#0c0c0e] border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between rounded-t-3xl">
        <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
            <div
                class="w-7 h-7 rounded-xl {{ isset($semester) ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center text-xs shrink-0">
                <i class="bi {{ isset($semester) ? 'bi-pencil-square' : 'bi-plus-lg' }}"></i>
            </div>
            {{ isset($semester) ? 'Edit Semester' : 'Tambah Semester' }}
        </h3>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 hover:bg-rose-500/10 hover:text-rose-500 transition-colors outline-none">
            <i class="bi bi-x-lg text-xs font-black"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 space-y-4 bg-white dark:bg-[#0c0c0e] overflow-y-auto custom-scrollbar flex-1">
        <div class="space-y-4">
            <!-- Tahun Pelajaran Terkunci -->
            <div>
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Tahun Pelajaran <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select readonly name="tahun_pelajaran_id"
                        class="m3-input-glass w-full text-xs font-bold appearance-none opacity-60 cursor-not-allowed">
                        <option value="{{ $tp->id }}" selected>
                            {{ $tp->nama_hijriyah }} H | {{ $tp->nama_masehi }} M
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-lock-fill text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Semester Selection -->
            <div>
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Semester <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="nama_semester"
                        class="m3-input-glass w-full !pr-8 text-xs font-bold appearance-none cursor-pointer">
                        <option value="" disabled
                            {{ !isset($semester) && !old('nama_semester') ? 'selected' : '' }}>
                            -- Pilih Semester --
                        </option>
                        <option value="Semester 1 (Ganjil)"
                            {{ (isset($semester) && $semester->nama_semester == 'Semester 1 (Ganjil)') || old('nama_semester') == 'Semester 1 (Ganjil)' ? 'selected' : '' }}>
                            Semester 1 (Ganjil)
                        </option>
                        <option value="Semester 2 (Genap)"
                            {{ (isset($semester) && $semester->nama_semester == 'Semester 2 (Genap)') || old('nama_semester') == 'Semester 2 (Genap)' ? 'selected' : '' }}>
                            Semester 2 (Genap)
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-[10px] font-black"></i>
                    </div>
                </div>
            </div>

            <!-- Tanggal Mulai & Selesai -->
            <div class="grid grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                        Mulai Masehi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_mulai"
                        value="{{ isset($semester) ? $semester->tanggal_mulai : old('tanggal_mulai') }}"
                        class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                        Selesai Masehi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_selesai"
                        value="{{ isset($semester) ? $semester->tanggal_selesai : old('tanggal_selesai') }}"
                        class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 flex justify-end gap-2.5 rounded-b-3xl">
        <button type="button" data-dismiss="modal"
            class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors outline-none shadow-2xs">
            Batal
        </button>
        <button type="submit"
            class="m3-btn-primary px-4 py-2 rounded-xl text-xs font-black shadow-2xs flex items-center gap-1.5">
            <i class="bi bi-save2-fill text-xs"></i>
            <span>{{ isset($semester) ? 'Simpan Perubahan' : 'Simpan Baru' }}</span>
        </button>
    </div>
</form>
