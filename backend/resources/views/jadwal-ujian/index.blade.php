@section('title', 'Jadwal Ujian')

<x-app-layout>
    <!-- HEADER & FILTER -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                Jadwal Induk Ujian
            </h2>
            <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                Distribusi jadwal pelaksanaan ujian per tingkat dan tingkatan kelas.
            </p>
        </div>

        <div class="flex flex-wrap gap-2.5 w-full xl:w-auto items-center">
            <!-- Form Filter -->
            <form action="{{ request()->url() }}" method="GET" id="filterForm"
                class="flex flex-wrap sm:flex-nowrap gap-2.5 w-full sm:w-auto">
                <!-- Filter Tahun Pelajaran -->
                <div class="relative group/select w-full sm:w-auto">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-calendar-event text-sm"></i>
                    </div>
                    <select name="tahun_id" onchange="document.getElementById('filterForm').submit();"
                        class="m3-input-glass w-full sm:w-auto !pl-9 !pr-9 appearance-none cursor-pointer min-w-[190px]">
                        <option value="">-- Tahun Pelajaran --</option>
                        @foreach ($daftarTahun as $tp)
                            <option value="{{ $tp->id }}"
                                {{ $tahunPelajaranId == $tp->id ? 'selected' : '' }}>
                                {{ $tp->nama_hijriyah }} - {{ $tp->nama_masehi }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Filter Ujian -->
                <div class="relative group/select w-full sm:w-auto">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-journal-bookmark text-sm"></i>
                    </div>
                    <select name="ujian_id" onchange="document.getElementById('filterForm').submit();"
                        {{ !$tahunPelajaranId ? 'disabled' : '' }}
                        class="m3-input-glass w-full sm:w-auto !pl-9 !pr-9 appearance-none cursor-pointer min-w-[190px] disabled:opacity-50 disabled:cursor-not-allowed">
                        @if (!$tahunPelajaranId)
                            <option value="">-- Pilih TP Dulu --</option>
                        @else
                            <option value="">-- Pilih Ujian --</option>
                            @foreach ($daftarUjian as $u)
                                <option value="{{ $u->id }}"
                                    {{ $ujianId == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama_ujian }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>
            </form>

            <!-- Tombol Aksi -->
            <a href="{{ route('jadwal-ujian.cetak-leger', ['tahun_pelajaran_id' => request('tahun_id'), 'ujian_id' => request('ujian_id')]) }}"
                target="_blank"
                class="h-10 inline-flex items-center justify-center px-4 rounded-xl bg-zinc-100/80 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200/80 dark:hover:bg-zinc-800 text-xs font-black transition-all hover:scale-[1.02] active:scale-95 outline-none border border-zinc-200/80 dark:border-zinc-800 shadow-2xs gap-1.5 shrink-0">
                <i class="bi bi-printer text-sm"></i>
                <span>Cetak Jadwal</span>
            </a>

            @can('create jadwal-ujian')
                <a href="{{ route('jadwal-ujian.create', ['ujian_id' => request('ujian_id')]) }}"
                    class="m3-btn-primary h-10 px-4 group/btn shrink-0">
                    <i class="bi bi-gear-fill text-sm"></i>
                    <span>Kelola Jadwal</span>
                </a>
            @endcan
        </div>
    </div>

    @php
        // 1. Ambil daftar Tingkat unik dari Levels
        $tingkats = $levels->pluck('tingkat')->filter()->unique('id')->sortBy('id')->values();

        // 2. Ambil daftar semua Tanggal (Key utama dari Matrix)
        $semuaTanggal = array_keys($matrix);
        sort($semuaTanggal);
    @endphp

    @if (count($matrix) > 0)
        <!-- TABS NAVIGASI TINGKAT -->
        <div class="flex gap-2 overflow-x-auto pb-3 mb-5 border-b border-zinc-200/80 dark:border-zinc-800 print:hidden custom-scrollbar relative z-10">
            @foreach ($tingkats as $index => $tingkat)
                <button type="button" onclick="switchTab({{ $tingkat->id }})" id="btn-tab-{{ $tingkat->id }}"
                    class="tab-btn px-5 py-2 rounded-xl font-black text-xs uppercase tracking-wider whitespace-nowrap transition-all duration-200 shadow-2xs active:scale-95 border
                    {{ $index == 0 ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 border-transparent' : 'bg-white/80 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border-zinc-200/80 dark:border-zinc-800 hover:bg-zinc-100/80 dark:hover:bg-zinc-800/60' }}">
                    {{ $tingkat->nama_tingkat }}
                </button>
            @endforeach
        </div>

        <!-- KONTEN TAB TINGKAT -->
        <div class="relative z-10">
            @foreach ($tingkats as $index => $tingkat)
                @php
                    // Filter kolom (Level) berdasarkan Tingkat yang sedang aktif
                    $levelsTingkat = $levels->filter(function ($l) use ($tingkat) {
                        return $l->tingkat_id == $tingkat->id;
                    });
                @endphp

                <div id="content-tab-{{ $tingkat->id }}"
                    class="tab-content {{ $index == 0 ? '' : 'hidden' }} space-y-6 animate-[modalFadeIn_0.2s_ease-out]">

                    @foreach ($semuaTanggal as $tanggal)
                        @php
                            // Cek apakah di tanggal ini ada jadwal untuk tingkat ini
                            $hasJadwal = false;
                            $waktuList = array_keys($matrix[$tanggal]);
                            sort($waktuList); // Urutkan jam

                            foreach ($waktuList as $wkt) {
                                foreach ($levelsTingkat as $lvl) {
                                    if (isset($matrix[$tanggal][$wkt][$lvl->id])) {
                                        $hasJadwal = true;
                                        break 2;
                                    }
                                }
                            }
                        @endphp

                        @if ($hasJadwal)
                            <!-- KARTU TABEL PER HARI -->
                            <div class="m3-glass-card overflow-hidden relative mb-6">

                                <!-- Header Card Hari -->
                                <div
                                    class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center gap-3 relative z-10">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center text-sm shrink-0">
                                        <i class="bi bi-calendar-event-fill"></i>
                                    </div>
                                    <h3
                                        class="font-black text-base text-zinc-900 dark:text-white uppercase tracking-wider">
                                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l') }}
                                    </h3>
                                    <span class="text-xs font-extrabold text-zinc-400 dark:text-zinc-500 ml-1">
                                        • {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                                    </span>
                                </div>

                                <!-- Tabel Matriks -->
                                <div class="overflow-x-auto print:overflow-visible relative z-10 custom-scrollbar">
                                    <table class="m3-table w-full text-center whitespace-nowrap min-w-max">
                                        <thead>
                                            <tr>
                                                <!-- Waktu (Kiri Sticky) -->
                                                <th scope="col"
                                                    class="text-center w-32 border-r border-zinc-200/80 dark:border-zinc-800 sticky left-0 bg-zinc-50/90 dark:bg-zinc-950/90 z-20 backdrop-blur-md">
                                                    Waktu
                                                </th>
                                                <!-- Level/Kelas -->
                                                @foreach ($levelsTingkat as $level)
                                                    <th scope="col"
                                                        class="border-r border-zinc-200/80 dark:border-zinc-800 min-w-[200px] last:border-r-0">
                                                        <div
                                                            class="text-xs font-black text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">
                                                            {{ $level->nama_level }}
                                                        </div>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($waktuList as $waktu)
                                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors duration-200">

                                                    <!-- Kolom Jam (Kiri Sticky) -->
                                                    <td
                                                        class="text-center border-r border-zinc-200/80 dark:border-zinc-800 align-middle sticky left-0 bg-white/95 dark:bg-zinc-950/95 z-20 backdrop-blur-md">
                                                        <!-- Pill Waktu -->
                                                        <span
                                                            class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-50 text-purple-600 border border-purple-200/80 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800/40 whitespace-nowrap">
                                                            {{ $waktu }}
                                                        </span>
                                                    </td>

                                                    <!-- Kolom Isi (Per Level) -->
                                                    @foreach ($levelsTingkat as $level)
                                                        <td
                                                            class="p-2.5 border-r border-zinc-200/80 dark:border-zinc-800 align-middle last:border-r-0">
                                                            @if (isset($matrix[$tanggal][$waktu][$level->id]))
                                                                @php
                                                                    $jadwalSesi = $matrix[$tanggal][$waktu][$level->id];
                                                                    $isBentrok = isset(
                                                                        $bentrokJadwalIds[$jadwalSesi->id],
                                                                    );
                                                                @endphp

                                                                @if ($isBentrok)
                                                                    <!-- JADWAL BENTROK -->
                                                                    <div
                                                                        class="p-2.5 rounded-xl border border-rose-200/80 dark:border-rose-800/50 bg-rose-50/80 dark:bg-rose-950/30 text-left relative overflow-hidden">
                                                                        <div class="absolute -right-1 -top-1 w-6 h-6 bg-rose-500 rounded-bl-lg rounded-tr-lg flex items-center justify-center text-white animate-pulse"
                                                                            title="Pengawas Bentrok!">
                                                                            <i class="bi bi-exclamation-triangle-fill text-[8px]"></i>
                                                                        </div>
                                                                        <h4
                                                                            class="text-xs font-black text-rose-700 dark:text-rose-400 uppercase tracking-wider leading-tight pr-3 truncate">
                                                                            {{ $jadwalSesi->mataPelajaran->nama_mapel ?? $jadwalSesi->nama_mata_pelajaran_custom }}
                                                                        </h4>
                                                                    </div>
                                                                @else
                                                                    <!-- JADWAL NORMAL -->
                                                                    <div
                                                                        class="p-2.5 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80 bg-zinc-50/80 dark:bg-zinc-900/60 text-left">
                                                                        <h4
                                                                            class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider leading-tight truncate">
                                                                            {{ $jadwalSesi->mataPelajaran->nama_mapel ?? $jadwalSesi->nama_mata_pelajaran_custom }}
                                                                        </h4>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <!-- KOSONG (Garis Dash) -->
                                                                <div class="flex flex-col items-center justify-center text-zinc-300 dark:text-zinc-700">
                                                                    <i class="bi bi-dash text-base"></i>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>
            @endforeach
        </div>
    @else
        <!-- EMPTY STATE -->
        <x-empty-state icon="bi-calendar-x" title="Belum Ada Jadwal Ujian"
            message="Pilih filter Tahun dan Ujian di atas untuk melihat atau mengelola jadwal pelaksanaan baru." />
    @endif

    <!-- SCRIPT UNTUK TABS -->
    @push('script')
        <script>
            function switchTab(tingkatId) {
                // Sembunyikan semua konten tab
                document.querySelectorAll('.tab-content').forEach(el => {
                    el.classList.add('hidden');
                });

                // Reset semua tombol ke gaya Inactive
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('bg-primary', 'dark:bg-primary-dark', 'text-white', 'dark:text-zinc-900', 'border-transparent');
                    btn.classList.add('bg-white/80', 'dark:bg-zinc-900', 'text-zinc-600', 'dark:text-zinc-400', 'border-zinc-200/80',
                        'dark:border-zinc-800', 'hover:bg-zinc-100/80', 'dark:hover:bg-zinc-800/60');
                });

                // Tampilkan konten tab yang dipilih
                document.getElementById('content-tab-' + tingkatId).classList.remove('hidden');

                // Set gaya tombol yang aktif
                const activeBtn = document.getElementById('btn-tab-' + tingkatId);
                activeBtn.classList.remove('bg-white/80', 'dark:bg-zinc-900', 'text-zinc-600', 'dark:text-zinc-400', 'border-zinc-200/80',
                    'dark:border-zinc-800', 'hover:bg-zinc-100/80', 'dark:hover:bg-zinc-800/60');
                activeBtn.classList.add('bg-primary', 'dark:bg-primary-dark', 'text-white', 'dark:text-zinc-900', 'border-transparent');
            }
        </script>
    @endpush
</x-app-layout>

