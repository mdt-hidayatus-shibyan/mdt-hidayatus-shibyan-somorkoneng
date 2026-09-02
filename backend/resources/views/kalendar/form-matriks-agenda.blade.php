<form action="{{ route('kalendar-pendidikan.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh] bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl rounded-2xl md:rounded-3xl overflow-hidden border border-zinc-200/80 dark:border-zinc-800 shadow-2xl">

    @csrf
    <input type="hidden" name="tahun_pelajaran_id" value="{{ $tp->id ?? '' }}">

    <!-- Modal Header -->
    <div
        class="px-5 py-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/80 dark:bg-zinc-950/60 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0 shadow-2xs">
                <i class="bi bi-calendar-plus text-base"></i>
            </div>
            <div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Tambah Agenda Matriks
                </h3>
                <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mt-0.5">Penjadwalan Tanggal Terpilih</p>
            </div>
        </div>

        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-9 min-h-9 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shrink-0"
            title="Tutup">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
        <div class="space-y-4">

            <!-- Jenis Agenda -->
            <div class="relative group/select">
                <label
                    class="block text-[11px] font-black text-primary dark:text-primary-dark uppercase tracking-wider mb-1 ml-0.5">
                    Jenis Agenda <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="jenis_agenda" id="jenisAgenda" onchange="ubahFormSesuaiJenis()"
                        class="m3-input-glass w-full !pr-9 font-bold text-xs cursor-pointer appearance-none bg-primary/5 dark:bg-primary-dark/10 border-primary/20 text-primary dark:text-primary-dark">
                        <option value="kegiatan" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                            Kegiatan Umum (Pendidikan)</option>
                        <option value="libur" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">Hari
                            Libur (Nasional/Pesantren)</option>
                        <option value="ujian" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">Ujian
                            Akademik</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-primary dark:text-primary-dark">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>
            </div>

            <!-- Judul Agenda -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                    Judul Agenda / Keterangan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_agenda" placeholder="Contoh: Ujian Kitab Lisan"
                    class="m3-input-glass w-full font-bold text-xs">
            </div>

            <!-- Grid Mulai & Selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                        Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_mulai" id="inputMulaiMatriks" value="{{ $tanggal ?? '' }}"
                        class="m3-input-glass w-full font-bold text-xs bg-zinc-100 dark:bg-zinc-800/50 cursor-not-allowed text-zinc-500">
                </div>
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                        Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_selesai" id="inputSelesaiMatriks" value="{{ $tanggal ?? '' }}"
                        min="{{ $tanggal ?? '' }}"
                        class="m3-input-glass w-full font-bold text-xs cursor-pointer border-emerald-500/50 focus:border-emerald-500">
                </div>
            </div>

            <!-- Kolom Khusus: Kategori Kegiatan -->
            <div id="kolom_kegiatan" class="block relative group/select">
                <label
                    class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                    Kategori Kegiatan
                </label>
                <div class="relative">
                    <select name="kategori_kegiatan_id" id="inputKategori"
                        class="m3-input-glass w-full !pr-9 font-bold text-xs cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Pilih Kategori --
                        </option>
                        @foreach ($kategoris ?? [] as $kat)
                            <option value="{{ $kat->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                                {{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>
            </div>

            <!-- Kolom Khusus: Ujian -->
            <div id="kolom_ujian"
                class="hidden p-4 rounded-xl bg-amber-500/5 dark:bg-amber-500/10 border border-amber-300/40 dark:border-amber-700/40">
                <div class="space-y-3.5">
                    <div class="relative group/select">
                        <label
                            class="block text-[11px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-1 ml-0.5">
                            Pilih Semester <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="semester_id" id="inputSemester"
                                class="m3-input-glass w-full !pr-9 font-bold text-xs cursor-pointer appearance-none border-amber-300/60 dark:border-amber-700/60 text-amber-900 dark:text-amber-100">
                                <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Pilih Semester
                                    --</option>
                                @foreach (\App\Models\Semester::where('tahun_pelajaran_id', $tp->id ?? null)->get() as $sem)
                                    <option value="{{ $sem->id }}"
                                        class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                                        {{ $sem->nama_semester }}</option>
                                @endforeach
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-amber-600 dark:text-amber-400">
                                <i class="bi bi-chevron-down text-xs font-bold"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative group/select">
                        <label
                            class="block text-[11px] font-black text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-1 ml-0.5">
                            Tipe Ujian <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="tipe_ujian" id="inputTipeUjian"
                                class="m3-input-glass w-full !pr-9 font-bold text-xs cursor-pointer appearance-none border-amber-300/60 dark:border-amber-700/60 text-amber-900 dark:text-amber-100">
                                <option value="IMDA 1" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                                    IMDA 1</option>
                                <option value="IMDA 2" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                                    IMDA 2</option>
                                <option value="IMDA 3" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                                    IMDA 3</option>
                                <option value="IMNI" class="bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white">
                                    IMNI</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-amber-600 dark:text-amber-400">
                                <i class="bi bi-chevron-down text-xs font-bold"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="px-5 py-3.5 bg-zinc-50/80 dark:bg-zinc-950/60 border-t border-zinc-200/80 dark:border-zinc-800 flex flex-col-reverse sm:flex-row justify-end gap-2.5 shrink-0">
        <button type="button" data-dismiss="modal"
            class="m3-btn-secondary w-full sm:w-auto h-10 px-5">
            Batal
        </button>
        <button type="submit"
            class="m3-btn-primary w-full sm:w-auto h-10 px-6 group/btn">
            <i class="bi bi-check-circle-fill text-xs"></i>
            <span>Simpan Agenda</span>
        </button>
    </div>

</form>

<script>
    function ubahFormSesuaiJenis() {
        const jenisSelect = document.getElementById('jenisAgenda');
        if (!jenisSelect) return;
        const jenis = jenisSelect.value;
        const kolomKegiatan = document.getElementById('kolom_kegiatan');
        const kolomUjian = document.getElementById('kolom_ujian');

        if (jenis === 'kegiatan') {
            kolomKegiatan.classList.remove('hidden');
            kolomKegiatan.classList.add('block');
            kolomUjian.classList.remove('block');
            kolomUjian.classList.add('hidden');

        } else if (jenis === 'ujian') {
            kolomKegiatan.classList.remove('block');
            kolomKegiatan.classList.add('hidden');
            kolomUjian.classList.remove('hidden');
            kolomUjian.classList.add('block');

        } else if (jenis === 'libur') {
            kolomKegiatan.classList.remove('block');
            kolomKegiatan.classList.add('hidden');
            kolomUjian.classList.remove('block');
            kolomUjian.classList.add('hidden');
        }
    }

    ubahFormSesuaiJenis();
</script>

