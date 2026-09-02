@forelse($ruangans as $ruangan)
    <div
        class="m3-glass-card p-4 md:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group relative overflow-hidden hover:border-primary/40 dark:hover:border-primary-dark/40 transition-all duration-300">

        <!-- ================= 1. SECTION INFO (Kiri) ================= -->
        <div class="flex items-center gap-3 md:gap-3.5 w-full lg:w-auto flex-1 relative z-10">
            <!-- Badge Nomor -->
            <span
                class="w-9 h-9 flex items-center justify-center bg-zinc-100/80 dark:bg-zinc-900 text-primary dark:text-primary-dark rounded-xl text-xs font-black border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                {{ $loop->iteration }}
            </span>

            <div class="flex-1 overflow-hidden">
                <h3
                    class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-snug mb-1 truncate">
                    {{ $ruangan->nama_ruangan }}
                </h3>

                <div class="flex flex-wrap items-center gap-2 mt-0.5">
                    <!-- Badge Tingkat -->
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase shadow-xs text-white"
                        style="background-color: {{ $ruangan->level->tingkat->kode_warna ?? '#146C2E' }};">
                        {{ $ruangan->level->tingkat->nama_tingkat ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ================= 2. SECTION WALI RUANGAN (Tengah) ================= -->
        <div
            class="flex items-center gap-3 w-full lg:w-auto lg:min-w-[200px] border-t lg:border-none border-zinc-100 dark:border-zinc-800/60 pt-3 lg:pt-0 relative z-10">
            @if ($ruangan->waliRuangan)
                <!-- Avatar Inisial Wali -->
                <div
                    class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center text-xs font-black shrink-0 border border-primary/20">
                    {{ substr($ruangan->waliRuangan->nama_lengkap, 0, 1) }}
                </div>
                <div class="flex flex-col overflow-hidden">
                    <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Wali
                        Ruangan</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate"
                        title="{{ $ruangan->waliRuangan->nama_lengkap }}">
                        {{ $ruangan->waliRuangan->nama_lengkap }}
                    </span>
                </div>
            @else
                <!-- State Belum Ada Wali -->
                <div
                    class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/30 text-rose-500 dark:text-rose-400 flex items-center justify-center text-xs shrink-0 border border-rose-200/60 dark:border-rose-800/40">
                    <i class="bi bi-person-x-fill text-sm"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Wali
                        Ruangan</span>
                    <span class="text-[11px] font-bold text-rose-500 dark:text-rose-400">Belum Ditunjuk</span>
                </div>
            @endif
        </div>

        <!-- ================= 3. SECTION AKSI & STATUS (Kanan) ================= -->
        <div
            class="flex items-center justify-between sm:justify-end gap-3 sm:gap-3.5 w-full lg:w-auto border-t sm:border-none border-zinc-100 dark:border-zinc-800/60 pt-3 sm:pt-0 relative z-10">
            <!-- Divider -->
            <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-800 transition-colors duration-300">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">

                @can('read rombongan-belajar')
                    <!-- Tombol Kelola Memanjang -->
                    <a href="{{ route('rombongan-belajar.anggota', $ruangan->id) }}"
                        class="w-full sm:w-auto flex items-center justify-between sm:justify-start gap-2.5 px-3.5 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200/80 dark:border-emerald-800/40 transition-all hover:scale-[1.02] active:scale-95 outline-none group/action"
                        title="Kelola Anggota Ruangan">

                        <!-- Ikon Utama -->
                        <div class="flex items-center gap-2">
                            <i class="bi bi-people-fill text-emerald-600 dark:text-emerald-400 text-sm"></i>
                            <span class="text-xs font-black text-emerald-800 dark:text-emerald-300">Kelola Anggota</span>
                        </div>

                        <!-- Separator vertikal -->
                        <div class="w-px h-4 bg-emerald-200 dark:bg-emerald-800/60"></div>

                        <div class="flex items-center gap-1.5">
                            <!-- Span Jumlah Aktif -->
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-white/90 dark:bg-black/60 text-zinc-700 dark:text-zinc-300 text-[10px] font-extrabold uppercase border border-emerald-200/50 dark:border-emerald-800/40 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $ruangan->murids->where('status', 'Aktif')->count() }} Aktif
                            </span>

                            <!-- Span Jumlah Tidak Aktif -->
                            @if($ruangan->murids->where('status', '!=', 'Aktif')->count() > 0)
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-white/90 dark:bg-black/60 text-rose-600 dark:text-rose-400 text-[10px] font-extrabold uppercase border border-rose-200/50 dark:border-rose-800/40 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    {{ $ruangan->murids->where('status', '!=', 'Aktif')->count() }} Non-Aktif
                                </span>
                            @endif
                        </div>
                    </a>
                @endcan

            </div>
        </div>

    </div>
@empty
    <!-- Custom Empty State -->
    <x-empty-state icon="bi-people" title="Data Ruangan Kosong"
        message="Anda belum mengatur Ruangan pada tahun pelajaran ini." />
@endforelse

