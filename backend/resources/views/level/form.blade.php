<!-- Modal Form -->
<form action="{{ isset($level) ? route('level.update', $level->id) : route('level.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($level))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            {{ isset($level) ? 'Edit Level/Kelas' : 'Tambah Level/Kelas' }}
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

            <!-- Urutan -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Urutan
                </label>
                <input type="number" name="urutan_level" value="{{ $level->urutan_level ?? old('urutan_level') }}"
                    placeholder="Contoh: 1/2/3" class="m3-input-glass w-full">
            </div>

            <!-- Nama level -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Level
                </label>
                <input type="text" name="nama_level" value="{{ $level->nama_level ?? old('nama_level') }}"
                    placeholder="Contoh: 1 TPQ/1 IBT/1 TSA" class="m3-input-glass w-full"
                    oninput="this.value = this.value.toUpperCase()">
            </div>

            <!-- Tingkat -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Tingkat
                </label>

                <div class="relative group">
                    <select name="tingkat_id" class="m3-input-glass w-full appearance-none cursor-pointer !pr-10">

                        <option value="" disabled {{ !isset($level) && !old('tingkat_id') ? 'selected' : '' }}>
                            -- Pilih Area Tingkat --
                        </option>

                        @foreach ($tingkats as $tingkat)
                            <option value="{{ $tingkat->id }}"
                                {{ (isset($level) && $level->tingkat_id == $tingkat->id) || old('tingkat_id') == $tingkat->id ? 'selected' : '' }}>
                                {{ $tingkat->kode_tingkat }} - {{ $tingkat->nama_tingkat }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Custom Chevron Icon -->
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-zinc-400 group-focus-within:text-primary dark:group-focus-within:text-primary-dark transition-colors duration-300">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($level) ? 'Simpan Perubahan' : 'Simpan Data' }}</span>
        </button>
    </div>
</form>

