<form action="{{ isset($permission) ? route('permissions.update', $permission->id) : route('permissions.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col" data-refresh-target="#data-grid-container">

    @csrf
    @if (isset($permission))
        @method('PUT')
    @endif

    <!-- Header Modal -->
    <div
        class="bg-white dark:bg-[#0c0c0e] border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between rounded-t-3xl">
        <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
            <div
                class="w-7 h-7 rounded-xl {{ isset($permission) ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center text-xs shrink-0">
                <i class="bi {{ isset($permission) ? 'bi-pencil-square' : 'bi-plus-lg' }}"></i>
            </div>
            {{ isset($permission) ? 'Edit Hak Akses' : 'Tambah Hak Akses Baru' }}
        </h3>
        <button type="button" data-dismiss="modal"
            class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 hover:bg-rose-500/10 hover:text-rose-500 transition-colors outline-none">
            <i class="bi bi-x-lg text-xs font-black"></i>
        </button>
    </div>

    <!-- Body Modal -->
    <div class="p-5 flex-1 space-y-4 bg-white dark:bg-[#0c0c0e]">

        <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-2xl flex items-start gap-2.5">
            <i class="bi bi-info-circle-fill text-emerald-600 dark:text-emerald-400 text-xs mt-0.5"></i>
            <p class="text-[10px] font-semibold text-zinc-600 dark:text-zinc-400">
                Gunakan format yang mudah dibaca seperti <b class="text-emerald-600 dark:text-emerald-400">tambah data
                    guru</b>, <b class="text-emerald-600 dark:text-emerald-400">lihat laporan</b>, dll. Sistem otomatis
                mengubahnya menjadi huruf kecil.
            </p>
        </div>

        <div>
            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                Nama Izin (Permission Name) <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="name" value="{{ isset($permission) ? $permission->name : old('name') }}"
                placeholder="Contoh: hapus transaksi" class="m3-input-glass w-full text-xs font-bold">
        </div>

    </div>

    <!-- Footer Modal -->
    <div
        class="bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 flex justify-end gap-2.5 rounded-b-3xl">
        <button type="button" data-dismiss="modal"
            class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors outline-none shadow-2xs">
            Batal
        </button>
        <button type="submit"
            class="{{ isset($permission) ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'm3-btn-primary' }} px-4 py-2 rounded-xl text-xs font-black shadow-2xs flex items-center gap-1.5">
            <i class="bi bi-save2-fill text-xs"></i>
            <span>{{ isset($permission) ? 'Simpan Perubahan' : 'Simpan Izin' }}</span>
        </button>
    </div>
</form>
