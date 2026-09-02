<!-- Modal Form Mata Pelajaran -->
<form
    action="{{ $mataPelajaran->exists ? route('mata-pelajaran.update', $mataPelajaran->id) : route('mata-pelajaran.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">

    @csrf
    @if ($mataPelajaran->exists)
        @method('PUT')
    @endif

    <input type="hidden" name="level_id" value="{{ $levelId }}">

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            {{ $mataPelajaran->exists ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
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

            <!-- Baris 1: Kode & Nama Pelajaran -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <!-- Kode -->
                <div class="sm:col-span-1 space-y-1.5">
                    <label
                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                        Kode <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="kode_mapel"
                        value="{{ $mataPelajaran->kode_mapel ?? old('kode_mapel') }}" placeholder="Cth: MAT-01"
                        class="m3-input-glass w-full uppercase" oninput="this.value = this.value.toUpperCase()">
                </div>

                <!-- Nama -->
                <div class="sm:col-span-2 space-y-1.5">
                    <label
                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                        Nama Pelajaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_mapel"
                        value="{{ $mataPelajaran->nama_mapel ?? old('nama_mapel') }}"
                        placeholder="Cth: Fiqih Ibadah" class="m3-input-glass w-full">
                </div>
            </div>

            <!-- Baris 2: Kelompok & Referensi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Kelompok -->
                <div class="space-y-1.5">
                    <label
                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                        Kelompok <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group">
                        <select name="kelompok" class="m3-input-glass w-full appearance-none cursor-pointer !pr-9">
                            @php $selectedKelompok = $mataPelajaran->kelompok ?? old('kelompok'); @endphp
                            <option value="Wajib" {{ $selectedKelompok == 'Wajib' ? 'selected' : '' }}>Wajib</option>
                            <option value="Ekstra" {{ $selectedKelompok == 'Ekstra' ? 'selected' : '' }}>Ekstra</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400 group-focus-within:text-primary dark:group-focus-within:text-primary-dark transition-colors duration-300">
                            <i class="bi bi-chevron-down text-xs font-bold"></i>
                        </div>
                    </div>
                </div>

                <!-- Referensi -->
                <div class="space-y-1.5">
                    <label
                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                        Referensi
                    </label>
                    <input type="text" name="referensi" value="{{ $mataPelajaran->referensi ?? old('referensi') }}"
                        placeholder="Cth: Kitab Mabadi Fiqih" class="m3-input-glass w-full">
                </div>
            </div>

            <!-- Baris 3: Pengarang & Penerbit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Pengarang -->
                <div class="space-y-1.5">
                    <label
                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                        Pengarang
                    </label>
                    <input type="text" name="pengarang" value="{{ $mataPelajaran->pengarang ?? old('pengarang') }}"
                        placeholder="Opsional" class="m3-input-glass w-full">
                </div>

                <!-- Penerbit -->
                <div class="space-y-1.5">
                    <label
                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                        Penerbit
                    </label>
                    <input type="text" name="penerbit" value="{{ $mataPelajaran->penerbit ?? old('penerbit') }}"
                        placeholder="Opsional" class="m3-input-glass w-full">
                </div>
            </div>

            <!-- Status Aktif -->
            <div
                class="flex items-center justify-between p-3.5 rounded-xl bg-zinc-50/80 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800">
                <div>
                    <p class="text-xs font-black text-zinc-900 dark:text-white tracking-tight">Status Pelajaran</p>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">Aktifkan untuk memunculkan di form penilaian ustadz.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ ($mataPelajaran->exists ? $mataPelajaran->is_active : old('is_active') ?? true) ? 'checked' : '' }}>
                    <div
                        class="w-10 h-5.5 bg-zinc-300 dark:bg-zinc-700 rounded-full peer peer-focus:ring-2 peer-focus:ring-primary/30 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:bg-primary dark:peer-checked:bg-primary-dark">
                    </div>
                </label>
            </div>

        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ $mataPelajaran->exists ? 'Simpan Perubahan' : 'Simpan Data' }}</span>
        </button>
    </div>
</form>

