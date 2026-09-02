<form action="{{ isset($periode) ? route('periode-pengurus.update', $periode->id) : route('periode-pengurus.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($periode))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div>
            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                {{ isset($periode) ? 'Edit Periode Kepengurusan' : 'Tambah Periode Baru' }}
            </h3>
            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                Masa bakti struktur organisasi
            </p>
        </div>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shadow-2xs">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">
        <!-- Nama Periode -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Nama Periode <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_periode" value="{{ $periode->nama_periode ?? old('nama_periode') }}" required
                placeholder="Contoh: Masa Bakti 2024-2029" class="m3-input-glass w-full text-xs font-bold uppercase">
        </div>

        <!-- Grid untuk Tanggal Mulai & Selesai -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Tanggal Mulai
                </label>
                <input type="date" name="tanggal_mulai"
                    value="{{ isset($periode) && $periode->tanggal_mulai ? $periode->tanggal_mulai->format('Y-m-d') : old('tanggal_mulai') }}"
                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
            </div>

            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Tanggal Selesai
                </label>
                <input type="date" name="tanggal_selesai"
                    value="{{ isset($periode) && $periode->tanggal_selesai ? $periode->tanggal_selesai->format('Y-m-d') : old('tanggal_selesai') }}"
                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
            </div>
        </div>

        <!-- Status Aktif (Dropdown) -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Status Periode
            </label>
            <div class="relative">
                <select name="status_aktif" class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer">
                    <option value="0"
                        {{ (isset($periode) && $periode->status_aktif == 0) || old('status_aktif') == '0' ? 'selected' : '' }}>
                        Tidak Aktif / Riwayat
                    </option>
                    <option value="1"
                        {{ (isset($periode) && $periode->status_aktif == 1) || old('status_aktif') == '1' ? 'selected' : '' }}>
                        Periode Aktif (Sekarang)
                    </option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                    <i class="bi bi-chevron-down text-xs"></i>
                </div>
            </div>
            <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mt-1.5 ml-1">
                Pilih "Aktif" jika ini adalah kepengurusan yang sedang berjalan saat ini.
            </p>
        </div>
    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-save2-fill mr-1.5"></i> {{ isset($periode) ? 'Simpan Perubahan' : 'Simpan Data' }}
        </button>
    </div>
</form>

