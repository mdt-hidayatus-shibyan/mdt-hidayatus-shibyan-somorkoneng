<!-- Modal Form -->
<form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf @method('PUT')

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            Edit Ruangan
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

            <!-- Tahun Pelajaran -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Tahun Pelajaran <span class="text-rose-500">*</span>
                </label>
                <div class="relative group">
                    <select name="tahun_pelajaran_id" class="m3-select2 w-full">
                        @foreach ($tahunPelajarans as $tp)
                            <option value="{{ $tp->id }}"
                                {{ $ruangan->tahun_pelajaran_id == $tp->id ? 'selected' : '' }}>
                                {{ $tp->nama_hijriyah }} - {{ $tp->nama_masehi }}
                                {{ $tp->is_active ? '  [Aktif]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kelas/Level -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kelas / Level <span class="text-rose-500">*</span>
                </label>
                <div class="relative group">
                    <select name="level_id" class="m3-select2 w-full">
                        <option value="" disabled>-- Pilih Kelas/Level --</option>
                        @foreach ($levels as $lvl)
                            <option value="{{ $lvl->id }}" {{ $ruangan->level_id == $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->nama_level }} - {{ $lvl->tingkat->kode_tingkat }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nama Ruangan -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Ruangan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_ruangan" value="{{ $ruangan->nama_ruangan }}"
                    placeholder="Contoh: 1-A TPQ / 1-B IBT"
                    class="m3-input-glass w-full uppercase"
                    oninput="this.value = this.value.toUpperCase()">
            </div>

            <!-- Wali Ruangan -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Wali Ruangan (Opsional)
                </label>
                <div class="relative group">
                    <select name="ustadz_id" class="m3-select2 w-full">
                        <option value="" {{ !old('ustadz_id', $ruangan->ustadz_id) ? 'selected' : '' }}>
                            -- Belum Ditunjuk --
                        </option>
                        @foreach ($dataAsatidz as $guru)
                            <option value="{{ $guru->id }}"
                                {{ $ruangan->ustadz_id == $guru->id ? 'selected' : '' }}>
                                {{ $guru->nigm }} - {{ $guru->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Kapasitas -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kapasitas
                </label>
                <input type="number" name="kapasitas" value="{{ $ruangan->kapasitas }}" placeholder="Min 1"
                    min="1" class="m3-input-glass w-full">
            </div>

        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto px-8">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.ajax-form .m3-select2').select2({
            width: '100%',
            dropdownParent: $('#modal-action'),
            language: {
                noResults: function() {
                    return "Data tidak ditemukan";
                }
            }
        });
    });
</script>

