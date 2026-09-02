<x-app-layout>

    <!-- HEADER & TOOLBAR SEJAJAR -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-4 relative z-20">

        <!-- Sisi Kiri: Judul Halaman -->
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Kas Ruangan
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Monitoring saldo kas terkumpul dan target iuran per ruangan kelas.
            </p>
        </div>

        <!-- Sisi Kanan: Toolbar Filter -->
        <div class="w-full xl:w-auto shrink-0">
            <form action="{{ request()->url() }}" method="GET" id="formFilter"
                class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto">

                <!-- Filter Tahun Pelajaran -->
                <div class="relative w-full sm:w-[200px] h-10">
                    <select name="tahun_id" onchange="document.getElementById('formFilter').submit()"
                        class="m3-input-glass w-full h-full !py-0 !pl-3.5 !pr-8 text-xs font-bold appearance-none cursor-pointer">
                        @foreach ($daftarTahun as $tahun)
                            <option value="{{ $tahun->id }}" {{ $tahunPelajaranId == $tahun->id ? 'selected' : '' }}>
                                {{ $tahun->nama_hijriyah }} | {{ $tahun->nama_masehi }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>

                <!-- Filter Ruangan -->
                <div class="relative w-full sm:w-[200px] h-10">
                    <select name="ruangan_id" onchange="document.getElementById('formFilter').submit()"
                        class="m3-input-glass w-full h-full !py-0 !pl-3.5 !pr-8 text-xs font-bold appearance-none cursor-pointer">
                        <option value="">-- Tampilkan Semua --</option>
                        @foreach ($daftarRuangan as $ruangItem)
                            <option value="{{ $ruangItem->id }}"
                                {{ isset($ruanganId) && $ruanganId == $ruangItem->id ? 'selected' : '' }}>
                                {{ $ruangItem->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- AREA GRID KONTEN (M3 Glass Cards) -->
    <div
        class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 relative z-10">
        @forelse ($ruangans as $ruang)
            <div
                class="m3-glass-card p-5 flex flex-col justify-between gap-5 transition-all shadow-2xs hover:border-primary/40 dark:hover:border-primary-dark/40 relative group">

                <div class="flex flex-col gap-4">
                    <!-- Card Header -->
                    <div class="flex justify-between items-start border-b border-zinc-200/80 dark:border-zinc-800 pb-3.5">
                        <div class="pr-2">
                            <h3
                                class="font-black text-lg text-zinc-900 dark:text-white tracking-tight leading-tight mb-1">
                                {{ $ruang->nama_ruangan }}
                            </h3>
                            <div
                                class="text-[10px] font-black px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded border border-zinc-200 dark:border-zinc-700 uppercase tracking-wider inline-block shadow-2xs">
                                {{ $ruang->level->nama_level ?? 'Madrasah' }}
                            </div>
                        </div>
                        <div
                            class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center shrink-0 border border-primary/20 shadow-2xs">
                            <i class="bi bi-door-open-fill text-base"></i>
                        </div>
                    </div>

                    <!-- Statistik Card -->
                    <div class="flex flex-col gap-2.5">
                        <!-- Kas Terkumpul -->
                        <div
                            class="bg-emerald-500/10 p-3.5 rounded-xl border border-emerald-500/20 shadow-2xs flex flex-col gap-1">
                            <span
                                class="text-[10px] font-black uppercase tracking-wider text-emerald-600/80 dark:text-emerald-400/80">
                                Kas Terkumpul
                            </span>
                            <span class="font-black text-xl md:text-2xl text-emerald-600 dark:text-emerald-400 tracking-tight font-mono">
                                Rp {{ number_format($ruang->pembayaran_kas_sum_jumlah_bayar ?? 0, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Target Nominal -->
                        <div
                            class="flex justify-between items-center bg-zinc-500/5 dark:bg-zinc-800/40 p-2.5 rounded-xl border border-zinc-200/80 dark:border-zinc-700/60 shadow-2xs">
                            <span
                                class="text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                Target L / P:
                            </span>
                            <span class="font-bold text-xs text-zinc-800 dark:text-zinc-200 font-mono">
                                {{ number_format($ruang->pengaturanKas->nominal_laki ?? 0, 0, ',', '.') }} /
                                {{ number_format($ruang->pengaturanKas->nominal_perempuan ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <a href="{{ route('kas-ruangan.show', $ruang->id) }}"
                    class="m3-btn-primary w-full h-10 text-xs font-black shadow-2xs">
                    Kelola Kas Murid <i class="bi bi-arrow-right ml-1"></i>
                </a>
            </div>
        @empty
            <!-- STATE KOSONG -->
            <div class="col-span-full">
                <x-empty-state icon="bi-door-closed" title="Tidak Ada Data Ruangan" message="Tidak ditemukan data ruangan yang sesuai dengan filter Anda." />
            </div>
        @endforelse
    </div>
</x-app-layout>

