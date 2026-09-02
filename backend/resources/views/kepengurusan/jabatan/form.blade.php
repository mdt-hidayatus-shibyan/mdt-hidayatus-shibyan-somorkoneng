<form action="{{ isset($jabatan) ? route('jabatan-pengurus.update', $jabatan->id) : route('jabatan-pengurus.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($jabatan))
        @method('PUT')
    @endif

    <!-- Modal Header (Compact) -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div>
            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                {{ isset($jabatan) ? 'Edit Jabatan Pengurus' : 'Tambah Jabatan Baru' }}
            </h3>
            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                Struktur organisasi yayasan
            </p>
        </div>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shadow-2xs">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">
        <!-- Nama Jabatan -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Nama Jabatan <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_jabatan" value="{{ $jabatan->nama_jabatan ?? old('nama_jabatan') }}" required
                placeholder="Contoh: Ketua Umum, Sekretaris, dll" class="m3-input-glass w-full text-xs font-bold uppercase">
        </div>

        <!-- Level -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Level / Kategori
            </label>
            <input type="text" name="level" value="{{ $jabatan->level ?? old('level') }}"
                placeholder="Contoh: Pengurus Harian, Divisi (Opsional)" class="m3-input-glass w-full text-xs font-bold uppercase">
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-save2-fill mr-1.5"></i> {{ isset($jabatan) ? 'Simpan Perubahan' : 'Simpan Data' }}
        </button>
    </div>
</form>

