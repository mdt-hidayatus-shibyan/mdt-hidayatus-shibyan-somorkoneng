@section('title', 'Dashboard')
<x-app-layout>

    <!-- Header Page & Filter -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-30">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                Dashboard Utama
            </h2>
            <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                Ringkasan statistik & pusat informasi operasional MDT Hidayatus Shibyan.
            </p>
        </div>

        <form action="{{ route('dashboard') }}" method="GET" id="formFilterTahun" class="w-full md:w-auto">
            <div class="relative group">
                <!-- Icon Kiri -->
                <div
                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-primary dark:group-focus-within:text-primary-dark transition-colors z-10">
                    <i class="bi bi-calendar-event text-sm"></i>
                </div>

                <!-- Select M3 Glass -->
                <select name="tahun_pelajaran_id" onchange="document.getElementById('formFilterTahun').submit()"
                    class="m3-input-glass w-full md:w-72 !pl-10 !pr-10 appearance-none cursor-pointer font-bold !rounded-2xl">
                    @foreach ($tahunPelajarans as $tp)
                        <option value="{{ $tp->id }}" {{ $selectedTahunId == $tp->id ? 'selected' : '' }}>
                            {{ $tp->nama_hijriyah }} | {{ $tp->nama_masehi }}
                        </option>
                    @endforeach
                </select>

                <!-- Icon Kanan -->
                <div
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within:text-primary dark:group-focus-within:text-primary-dark transition-colors z-10">
                    <i class="bi bi-chevron-down text-xs font-bold"></i>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- 3. PINTASAN AKSES CEPAT (QUICK ACTIONS)    -->
    <!-- ========================================== -->
    <div class="mb-6 md:mb-8 relative z-10">
        <h3 class="font-extrabold text-zinc-900 dark:text-white text-base tracking-tight mb-3 flex items-center gap-2">
            <i class="bi bi-lightning-charge-fill text-amber-500"></i> Pintasan Akses Cepat
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 md:gap-3.5">
            <a href="{{ route('presensi-murid.index') }}"
                class="m3-glass-card p-3.5 flex flex-col items-center justify-center text-center hover:border-blue-400/60 dark:hover:border-blue-500/50 hover:bg-blue-50/20 transition-all duration-200 group active:scale-95">
                <div
                    class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200/60 dark:border-blue-800/40 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2 text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Presensi Murid</span>
            </a>

            <a href="{{ route('pembayaran-tagihan.index') }}"
                class="m3-glass-card p-3.5 flex flex-col items-center justify-center text-center hover:border-emerald-400/60 dark:hover:border-emerald-500/50 hover:bg-emerald-50/20 transition-all duration-200 group active:scale-95">
                <div
                    class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/60 dark:border-emerald-800/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Bayar SPP</span>
            </a>

            <a href="{{ route('kas-ruangan.index') }}"
                class="m3-glass-card p-3.5 flex flex-col items-center justify-center text-center hover:border-indigo-400/60 dark:hover:border-indigo-500/50 hover:bg-indigo-50/20 transition-all duration-200 group active:scale-95">
                <div
                    class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200/60 dark:border-indigo-800/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-2 text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Kas Ruangan</span>
            </a>

            <a href="{{ route('nilai-ujian.input-leger') }}"
                class="m3-glass-card p-3.5 flex flex-col items-center justify-center text-center hover:border-purple-400/60 dark:hover:border-purple-500/50 hover:bg-purple-50/20 transition-all duration-200 group active:scale-95">
                <div
                    class="w-11 h-11 rounded-2xl bg-purple-50 dark:bg-purple-900/30 border border-purple-200/60 dark:border-purple-800/40 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-2 text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                </div>
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Leger Nilai</span>
            </a>

            <a href="{{ route('kartu-pelajar.index') }}"
                class="m3-glass-card p-3.5 flex flex-col items-center justify-center text-center hover:border-amber-400/60 dark:hover:border-amber-500/50 hover:bg-amber-50/20 transition-all duration-200 group active:scale-95">
                <div
                    class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-900/30 border border-amber-200/60 dark:border-amber-800/40 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-2 text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-card-heading"></i>
                </div>
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Kartu Pelajar</span>
            </a>

            <a href="{{ route('kenaikan-kelas.index') }}"
                class="m3-glass-card p-3.5 flex flex-col items-center justify-center text-center hover:border-rose-400/60 dark:hover:border-rose-500/50 hover:bg-rose-50/20 transition-all duration-200 group active:scale-95">
                <div
                    class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-900/30 border border-rose-200/60 dark:border-rose-800/40 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-2 text-lg group-hover:scale-110 transition-transform">
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Kenaikan Kelas</span>
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 1. GRID EXECUTIVE KPI CARDS (6 STATS)       -->
    <!-- ========================================== -->
    <div class="mb-6 md:mb-8 relative z-10">
        <h3 class="font-extrabold text-zinc-900 dark:text-white text-base tracking-tight mb-3 flex items-center gap-2">
            <i class="bi bi-bar-chart-fill text-primary dark:text-primary-dark"></i> Rangkuman Data
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3.5 md:gap-4 relative z-10">
            <!-- CARD 1: Total Murid -->
            <div
                class="m3-glass-card p-4 flex flex-col justify-between group hover:border-blue-300 dark:hover:border-blue-800 transition-all">
                <div class="flex justify-between items-start mb-2.5">
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white text-xs tracking-tight">Total Murid</h4>
                        <p
                            class="text-[9px] uppercase tracking-widest font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                            Status Aktif</p>
                    </div>
                    <div
                        class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0 transition-transform group-hover:scale-110">
                        <i class="bi bi-people-fill text-base"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                        {{ number_format($totalMurid ?? 0, 0, ',', '.') }}
                        <span class="text-[11px] font-bold text-zinc-400 dark:text-zinc-500">Murid</span>
                    </div>
                    <div
                        class="mt-2 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 pt-2">
                        <span class="text-blue-500">L: {{ $totalLaki }}</span>
                        <span>•</span>
                        <span class="text-pink-500">P: {{ $totalPerempuan }}</span>
                    </div>
                </div>
            </div>

            <!-- CARD 2: Wali Murid -->
            <div
                class="m3-glass-card p-4 flex flex-col justify-between group hover:border-purple-300 dark:hover:border-purple-800 transition-all">
                <div class="flex justify-between items-start mb-2.5">
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white text-xs tracking-tight">Wali Murid</h4>
                        <p
                            class="text-[9px] uppercase tracking-widest font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                            Keluarga Aktif</p>
                    </div>
                    <div
                        class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-900/30 border border-purple-200/60 dark:border-purple-800/40 flex items-center justify-center text-purple-600 dark:text-purple-400 shrink-0 transition-transform group-hover:scale-110">
                        <i class="bi bi-person-hearts text-base"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                        {{ number_format($totalWaliMurid ?? 0, 0, ',', '.') }}
                        <span class="text-[11px] font-bold text-zinc-400 dark:text-zinc-500">KK</span>
                    </div>
                    <div
                        class="mt-2 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 pt-2 truncate">
                        <i class="bi bi-house-door-fill text-purple-500 mr-1"></i> Terdata di Sistem
                    </div>
                </div>
            </div>

            <!-- CARD 3: Ustadz & Staff -->
            <div
                class="m3-glass-card p-4 flex flex-col justify-between group hover:border-emerald-300 dark:hover:border-emerald-800 transition-all">
                <div class="flex justify-between items-start mb-2.5">
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white text-xs tracking-tight">Ustadz/Guru</h4>
                        <p
                            class="text-[9px] uppercase tracking-widest font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                            Tenaga Pendidik</p>
                    </div>
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0 transition-transform group-hover:scale-110">
                        <i class="bi bi-person-badge-fill text-base"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                        {{ number_format($totalUstadz ?? 0, 0, ',', '.') }}
                        <span class="text-[11px] font-bold text-zinc-400 dark:text-zinc-500">Orang</span>
                    </div>
                    <div
                        class="mt-2 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 pt-2 truncate">
                        <i class="bi bi-check-circle-fill text-emerald-500 mr-1"></i> Pengajar Aktif
                    </div>
                </div>
            </div>

            <!-- CARD 4: Rombongan Belajar -->
            <div
                class="m3-glass-card p-4 flex flex-col justify-between group hover:border-amber-300 dark:hover:border-amber-800 transition-all">
                <div class="flex justify-between items-start mb-2.5">
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white text-xs tracking-tight">Rombel / Kelas</h4>
                        <p
                            class="text-[9px] uppercase tracking-widest font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                            Ruang Aktif</p>
                    </div>
                    <div
                        class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/30 border border-amber-200/60 dark:border-amber-800/40 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0 transition-transform group-hover:scale-110">
                        <i class="bi bi-door-open-fill text-base"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                        {{ number_format($totalRombel ?? 0, 0, ',', '.') }}
                        <span class="text-[11px] font-bold text-zinc-400 dark:text-zinc-500">Kelas</span>
                    </div>
                    <div
                        class="mt-2 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 pt-2 truncate">
                        <i class="bi bi-book-fill text-amber-500 mr-1"></i> Tahun Ini
                    </div>
                </div>
            </div>

            <!-- CARD 5: Pelunasan Tagihan / SPP -->
            <div
                class="m3-glass-card p-4 flex flex-col justify-between group hover:border-teal-300 dark:hover:border-teal-800 transition-all">
                <div class="flex justify-between items-start mb-2.5">
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white text-xs tracking-tight">Pelunasan SPP</h4>
                        <p
                            class="text-[9px] uppercase tracking-widest font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                            Tingkat Lunas</p>
                    </div>
                    <div
                        class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-900/30 border border-teal-200/60 dark:border-teal-800/40 flex items-center justify-center text-teal-600 dark:text-teal-400 shrink-0 transition-transform group-hover:scale-110">
                        <i class="bi bi-wallet2 text-base"></i>
                    </div>
                </div>
                <div>
                    <div class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                        {{ $persenLunas }}%
                    </div>
                    <div class="mt-2 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 pt-2 truncate"
                        title="Lunas: Rp {{ number_format($totalNominalLunas, 0, ',', '.') }}">
                        Rp {{ number_format($totalNominalLunas, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- CARD 6: Total Kas Ruangan -->
            <div
                class="m3-glass-card p-4 flex flex-col justify-between group hover:border-indigo-300 dark:hover:border-indigo-800 transition-all">
                <div class="flex justify-between items-start mb-2.5">
                    <div>
                        <h4 class="font-bold text-zinc-900 dark:text-white text-xs tracking-tight">Setoran Kas</h4>
                        <p
                            class="text-[9px] uppercase tracking-widest font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                            Kas Ruangan</p>
                    </div>
                    <div
                        class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200/60 dark:border-indigo-800/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0 transition-transform group-hover:scale-110">
                        <i class="bi bi-cash-stack text-base"></i>
                    </div>
                </div>
                <div>
                    <div class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight truncate"
                        title="Rp {{ number_format($totalSetoranKas ?? 0, 0, ',', '.') }}">
                        Rp {{ number_format($totalSetoranKas ?? 0, 0, ',', '.') }}
                    </div>
                    <div
                        class="mt-2 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 pt-2 truncate">
                        <i class="bi bi-piggy-bank-fill text-indigo-500 mr-1"></i> Disetorkan
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- ========================================== -->
    <!-- 2. ANALYTICS & VISUAL CHARTS SECTION       -->
    <!-- ========================================== -->

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-5 mb-6 md:mb-8 relative z-10">

        <!-- CHART 4: Distribusi Murid Per Ruangan (12/12) -->
        <div class="m3-glass-card p-5 md:p-6 lg:col-span-12 flex flex-col group">
            <div class="flex justify-between items-start mb-5 relative z-10">
                <div>
                    <h3 class="font-extrabold text-zinc-900 dark:text-white text-base md:text-lg tracking-tight">Jumlah
                        Murid Berdasarkan
                        Ruangan</h3>
                    <p
                        class="text-[11px] uppercase tracking-wider font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                        Statistik Kepadatan Murid per Ruangan Aktif
                    </p>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200/60 dark:border-indigo-800/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400 transition-transform group-hover:scale-110 shrink-0">
                    <i class="bi bi-door-open-fill text-lg"></i>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-[300px] z-10">
                <canvas id="ruanganChart"></canvas>
            </div>
        </div>
        <!-- CHART 1: Distribusi Per Tingkat (6/12) -->
        <div class="m3-glass-card p-5 md:p-6 lg:col-span-6 flex flex-col group">
            <div class="flex justify-between items-start mb-5 relative z-10">
                <div>
                    <h3 class="font-extrabold text-zinc-900 dark:text-white text-base md:text-lg tracking-tight">
                        Distribusi Murid Per
                        Tingkat</h3>
                    <p
                        class="text-[11px] uppercase tracking-wider font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                        Perbandingan Putra & Putri per Jenjang Level</p>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200/60 dark:border-emerald-800/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 transition-transform group-hover:scale-110 shrink-0">
                    <i class="bi bi-bar-chart-fill text-lg"></i>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-[260px] z-10">
                <canvas id="levelChart"></canvas>
            </div>
        </div>

        <!-- CHART 2: Status Pelunasan Tagihan / SPP (3/12) -->
        <div class="m3-glass-card p-5 md:p-6 lg:col-span-3 flex flex-col group">
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <h3 class="font-bold text-zinc-900 dark:text-white text-sm md:text-base tracking-tight">Status
                        Tagihan SPP
                    </h3>
                    <p
                        class="text-[10px] uppercase tracking-wider font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                        Proporsi Pembayaran</p>
                </div>
                <div
                    class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-900/30 border border-teal-200/60 dark:border-teal-800/40 flex items-center justify-center text-teal-600 dark:text-teal-400 transition-transform group-hover:scale-110 shrink-0">
                    <i class="bi bi-pie-chart-fill text-base"></i>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-[190px] z-10 flex items-center justify-center">
                <canvas id="tagihanChart"></canvas>
            </div>
            <div
                class="mt-3.5 pt-3 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 text-[11px] text-zinc-500 dark:text-zinc-400 flex justify-between font-bold">
                <span>Belum: <strong
                        class="text-rose-500">{{ number_format($totalTagihanBelumLunas) }}</strong></span>
                <span>Lunas: <strong
                        class="text-emerald-500">{{ number_format($totalTagihanLunasCount) }}</strong></span>
            </div>
        </div>

        <!-- CHART 3: Rekapitulasi Presensi Murid (3/12) -->
        <div class="m3-glass-card p-5 md:p-6 lg:col-span-3 flex flex-col group">
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <h3 class="font-bold text-zinc-900 dark:text-white text-sm md:text-base tracking-tight">Rekap
                        Presensi</h3>
                    <p
                        class="text-[10px] uppercase tracking-wider font-extrabold text-zinc-400 dark:text-zinc-500 mt-0.5">
                        Kehadiran Harian</p>
                </div>
                <div
                    class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200/60 dark:border-blue-800/40 flex items-center justify-center text-blue-600 dark:text-blue-400 transition-transform group-hover:scale-110 shrink-0">
                    <i class="bi bi-calendar-week-fill text-base"></i>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-[190px] z-10 flex items-center justify-center">
                <canvas id="presensiChart"></canvas>
            </div>
            <div
                class="mt-3.5 pt-3 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 text-[11px] text-zinc-500 dark:text-zinc-400 flex justify-between font-bold">
                <span>Hadir: <strong class="text-blue-500">{{ number_format($presensiHadir) }}</strong></span>
                <span>Alpha: <strong class="text-rose-500">{{ number_format($presensiAlpha) }}</strong></span>
            </div>
        </div>

    </div>



    <!-- ========================================== -->
    <!-- 4. OPERATIONAL WIDGETS & ACTIVITY FEEDS    -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5 relative z-10">

        <!-- WIDGET 1: PENGUMUMAN MADRASAH -->
        <div class="m3-glass-card p-5 flex flex-col">
            <div
                class="flex justify-between items-center mb-3.5 pb-2.5 border-b border-zinc-100 dark:border-zinc-800/80">
                <h3
                    class="font-bold text-zinc-900 dark:text-white text-sm md:text-base tracking-tight flex items-center gap-2">
                    <i class="bi bi-megaphone-fill text-primary dark:text-primary-dark"></i> Pengumuman
                </h3>
                <a href="{{ route('pengumuman.index') }}"
                    class="text-[11px] font-bold text-primary dark:text-primary-dark hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-2.5 flex-1">
                @forelse($pengumumans as $p)
                    <div
                        class="p-3 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200/70 dark:border-zinc-800/70 hover:border-primary/40 transition-colors">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span
                                class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wider 
                                {{ $p->tipe == 'Penting'
                                    ? 'bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400'
                                    : ($p->tipe == 'Kegiatan'
                                        ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400'
                                        : 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400') }}">
                                {{ $p->tipe }}
                            </span>
                            <span
                                class="text-[10px] font-semibold text-zinc-400">{{ $p->created_at ? $p->created_at->diffForHumans() : '-' }}</span>
                        </div>
                        <h4 class="font-bold text-xs text-zinc-900 dark:text-white line-clamp-1">{{ $p->judul }}
                        </h4>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 line-clamp-2 mt-0.5">
                            {{ strip_tags($p->konten) }}</p>
                    </div>
                @empty
                    <div class="text-center py-8 text-zinc-400 dark:text-zinc-500 text-xs font-semibold">
                        <i class="bi bi-inbox text-2xl block mb-1"></i> Belum ada pengumuman terbit.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- WIDGET 2: AGENDA & KALENDER AKADEMIK -->
        <div class="m3-glass-card p-5 flex flex-col">
            <div
                class="flex justify-between items-center mb-3.5 pb-2.5 border-b border-zinc-100 dark:border-zinc-800/80">
                <h3
                    class="font-bold text-zinc-900 dark:text-white text-sm md:text-base tracking-tight flex items-center gap-2">
                    <i class="bi bi-calendar-event-fill text-emerald-500"></i> Agenda Akademik
                </h3>
                <a href="{{ route('kalendar-pendidikan.index') }}"
                    class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">Lihat
                    Kalender</a>
            </div>
            <div class="space-y-2.5 flex-1">
                @forelse($agendaKalender as $agenda)
                    <div
                        class="p-2.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200/70 dark:border-zinc-800/70 flex items-start gap-2.5">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex flex-col items-center justify-center shrink-0 font-bold">
                            <span
                                class="text-xs">{{ $agenda->tanggal_mulai ? \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('d') : '-' }}</span>
                            <span
                                class="text-[8px] uppercase font-black">{{ $agenda->tanggal_mulai ? \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('M') : '' }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-xs text-zinc-900 dark:text-white truncate">
                                {{ $agenda->nama_kegiatan }}</h4>
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                                {{ $agenda->kategoriKegiatan->nama_kategori ?? 'Kegiatan' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-zinc-400 dark:text-zinc-500 text-xs font-semibold">
                        <i class="bi bi-calendar-x text-2xl block mb-1"></i> Tidak ada agenda terdekat.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- WIDGET 3: CATATAN PELANGGARAN TERBARU -->
        <div class="m3-glass-card p-5 flex flex-col">
            <div
                class="flex justify-between items-center mb-3.5 pb-2.5 border-b border-zinc-100 dark:border-zinc-800/80">
                <h3
                    class="font-bold text-zinc-900 dark:text-white text-sm md:text-base tracking-tight flex items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-rose-500"></i> Catatan Pelanggaran
                </h3>
                <a href="{{ route('pelanggaran-murid.index') }}"
                    class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline">Lihat Rekap</a>
            </div>
            <div class="space-y-2.5 flex-1">
                @forelse($pelanggaranTerbaru as $plg)
                    <div
                        class="p-2.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200/70 dark:border-zinc-800/70 flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs text-zinc-900 dark:text-white truncate">
                                {{ $plg->murid->nama_lengkap ?? 'Murid' }}</h4>
                            <p class="text-[11px] text-rose-600 dark:text-rose-400 font-semibold truncate mt-0.5">
                                {{ $plg->referensiPelanggaran->nama_pelanggaran ?? 'Pelanggaran' }}
                            </p>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">
                                {{ $plg->ruangan->nama_ruangan ?? '' }} •
                                {{ $plg->tanggal ? \Carbon\Carbon::parse($plg->tanggal)->format('d/m/Y') : '' }}
                            </p>
                        </div>
                        <span
                            class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 shrink-0">
                            +{{ $plg->referensiPelanggaran->poin ?? 0 }} Poin
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-zinc-400 dark:text-zinc-500 text-xs font-semibold">
                        <i class="bi bi-shield-check text-2xl block mb-1 text-emerald-500"></i> Tidak ada catatan
                        pelanggaran.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Deteksi Mode Gelap untuk warna teks dan grid Chart.js
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#a1a1aa' : '#71717a';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

            // Konfigurasi Default Chart.js
            Chart.defaults.font.family =
                "Lexend, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif";
            Chart.defaults.color = textColor;

            // ============================================
            // CHART 1: DISTRIBUSI LEVEL (Laki vs Perempuan)
            // ============================================
            const levelData = @json($muridPerLevel);
            const ctxLevel = document.getElementById('levelChart')?.getContext('2d');

            if (ctxLevel) {
                new Chart(ctxLevel, {
                    type: 'bar',
                    data: {
                        labels: levelData.map(l => l.nama_level),
                        datasets: [{
                                label: 'Putra',
                                data: levelData.map(l => l.total_l),
                                backgroundColor: '#3b82f6',
                                hoverBackgroundColor: '#2563eb',
                                borderRadius: 6,
                                barPercentage: 0.8,
                                categoryPercentage: 0.8
                            },
                            {
                                label: 'Putri',
                                data: levelData.map(l => l.total_p),
                                backgroundColor: '#ec4899',
                                hoverBackgroundColor: '#db2777',
                                borderRadius: 6,
                                barPercentage: 0.8,
                                categoryPercentage: 0.8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    padding: 15,
                                    font: {
                                        weight: 'bold',
                                        size: 11
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor,
                                    drawBorder: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    precision: 0,
                                    padding: 8,
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        weight: 'bold'
                                    },
                                    padding: 8
                                }
                            }
                        }
                    }
                });
            }


            const dataRuangan = @json($muridPerRuangan);

            const labelsRuangan = dataRuangan.map(item => item.nama_ruangan);
            // Ambil data L dan P
            const dataPutra = dataRuangan.map(item => item.total_l);
            const dataPutri = dataRuangan.map(item => item.total_p);

            const ctxRuangan = document.getElementById('ruanganChart').getContext('2d');
            new Chart(ctxRuangan, {
                type: 'bar',
                data: {
                    labels: labelsRuangan,
                    datasets: [{
                            label: 'Putra (L)',
                            data: dataPutra,
                            backgroundColor: 'rgba(59, 130, 246, 0.8)', // Biru (Blue-500)
                            borderColor: 'rgba(37, 99, 235, 1)', // Blue-600
                            borderWidth: 1,
                            borderRadius: {
                                topLeft: 0,
                                topRight: 0,
                                bottomLeft: 4,
                                bottomRight: 4
                            } // Opsional: untuk efek tumpukan
                        },
                        {
                            label: 'Putri (P)',
                            data: dataPutri,
                            backgroundColor: 'rgba(236, 72, 153, 0.8)', // Pink (Pink-500)
                            borderColor: 'rgba(219, 39, 119, 1)', // Pink-600
                            borderWidth: 1,
                            borderRadius: {
                                topLeft: 4,
                                topRight: 4,
                                bottomLeft: 0,
                                bottomRight: 0
                            }
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true, // Ubah ke false jika ingin grafiknya bersebelahan, bukan ditumpuk
                        },
                        y: {
                            stacked: true, // Ubah ke false jika ingin grafiknya bersebelahan
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true, // Aktifkan legend agar user tau warna Biru/Pink
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                // Menambahkan total murid di dalam tooltip saat kursor diarahkan ke grafik
                                footer: function(tooltipItems) {
                                    let total = 0;
                                    tooltipItems.forEach(function(tooltipItem) {
                                        // Ambil total dari dataset original
                                        total = dataRuangan[tooltipItem.dataIndex].total;
                                    });
                                    return 'Total: ' + total + ' Murid';
                                }
                            }
                        }
                    }
                }
            });

            // ============================================
            // CHART 2: STATUS PELUNASAN SPP (Doughnut Chart)
            // ============================================
            const ctxTagihan = document.getElementById('tagihanChart')?.getContext('2d');
            if (ctxTagihan) {
                new Chart(ctxTagihan, {
                    type: 'doughnut',
                    data: {
                        labels: ['Belum Lunas', 'Lunas', 'Bebas/Donatur'],
                        datasets: [{
                            data: [{{ $totalTagihanBelumLunas }}, {{ $totalTagihanLunasCount }},
                                {{ $totalTagihanDonaturBebas }}
                            ],
                            backgroundColor: ['#f43f5e', '#10b981', '#3b82f6'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    padding: 12,
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            // ============================================
            // CHART 3: REKAP PRESENSI MURID (Doughnut Chart)
            // ============================================
            const ctxPresensi = document.getElementById('presensiChart')?.getContext('2d');
            if (ctxPresensi) {
                new Chart(ctxPresensi, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
                        datasets: [{
                            data: [{{ $presensiHadir }}, {{ $presensiSakit }}, {{ $presensiIzin }},
                                {{ $presensiAlpha }}
                            ],
                            backgroundColor: ['#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    padding: 12,
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
