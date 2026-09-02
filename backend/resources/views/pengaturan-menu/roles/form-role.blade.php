<form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col">
    @csrf
    @if (isset($role))
        @method('PUT')
    @endif

    <!-- Header Modal -->
    <div
        class="bg-white dark:bg-[#0c0c0e] border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between rounded-t-3xl">
        <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
            <div
                class="w-7 h-7 rounded-xl {{ isset($role) ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center text-xs shrink-0">
                <i class="bi {{ isset($role) ? 'bi-pencil-square' : 'bi-shield-plus' }}"></i>
            </div>
            {{ isset($role) ? 'Edit Nama Role' : 'Tambah Role Baru' }}
        </h3>
        <button type="button" data-dismiss="modal"
            class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 hover:bg-rose-500/10 hover:text-rose-500 transition-colors outline-none">
            <i class="bi bi-x-lg text-xs font-black"></i>
        </button>
    </div>

    <!-- Body Modal -->
    <div class="p-5 flex-1 space-y-4 bg-white dark:bg-[#0c0c0e]">
        <div>
            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                Nama Role <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <i class="bi bi-award-fill text-xs"></i>
                </div>
                <input type="text" name="name" value="{{ isset($role) ? $role->name : old('name') }}"
                    placeholder="contoh: Wali Kelas" required class="m3-input-glass w-full !pl-9 text-xs font-bold">
            </div>
            <p class="text-[9px] font-semibold text-zinc-400 mt-1.5 ml-1">
                Nama role ini akan digunakan untuk mengelompokkan hak akses pengguna.
            </p>
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
            class="{{ isset($role) ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'm3-btn-primary' }} px-4 py-2 rounded-xl text-xs font-black shadow-2xs flex items-center gap-1.5">
            <i class="bi bi-save2-fill text-xs"></i>
            <span>{{ isset($role) ? 'Simpan Perubahan' : 'Simpan Role' }}</span>
        </button>
    </div>
</form>
