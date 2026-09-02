<x-app-layout>

    <!-- HEADER & TOOLBAR SEJAJAR -->
    <div
        class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10 print:hidden">

        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('bintang-pelajar.menu')
        </div>

        <!-- Area Form Pencarian -->
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('bintang-pelajar.index') }}" method="GET" id="formFilter"
                class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto">

                <!-- Filter Tahun -->
                <div class="relative w-full sm:w-[190px] group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-calendar-range text-sm"></i>
                    </div>
                    <select name="tahun_id" onchange="document.getElementById('formFilter').submit()"
                        class="m3-input-glass w-full !pl-9 !pr-9 appearance-none cursor-pointer">
                        @foreach ($daftarTahun as $t)
                            <option value="{{ $t->id }}" {{ $tahunPelajaranId == $t->id ? 'selected' : '' }}>
                                {{ $t->nama_hijriyah }}
                            </option>
                        @endforeach
                    </select>
                    <div
                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Filter Ujian -->
                <div class="relative w-full sm:w-[190px] group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-trophy text-sm"></i>
                    </div>
                    <select name="ujian_id" onchange="document.getElementById('formFilter').submit()"
                        class="m3-input-glass w-full !pl-9 !pr-9 appearance-none cursor-pointer">
                        <option value="">-- Pilih Ujian --</option>
                        @foreach ($daftarUjian as $uj)
                            <option value="{{ $uj->id }}"
                                {{ request('ujian_id') == $uj->id ? 'selected' : '' }}>
                                {{ $uj->nama_ujian }}
                            </option>
                        @endforeach
                    </select>
                    <div
                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Tombol Cetak -->
                @if (request('ujian_id') && ($bintangLevel->count() > 0 || $bintangRuangan->count() > 0))
                    <div
                        class="w-full sm:w-auto shrink-0 border-t sm:border-t-0 sm:border-l border-zinc-200/80 dark:border-zinc-800 pt-2.5 sm:pt-0 sm:pl-2.5">
                        <button type="button" onclick="window.print()"
                            class="m3-btn-secondary w-full sm:w-auto h-10 px-4 group/btn">
                            <i class="bi bi-printer text-sm"></i>
                            <span class="sm:hidden xl:inline">Cetak Piagam</span>
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- AREA KONTEN -->
    @if (request('ujian_id'))

        <!-- BINTANG LEVEL / TINGKAT -->
        <div class="mb-8 animate-[modalFadeIn_0.2s_ease-out]">
            <div class="flex items-center gap-2.5 mb-4 border-b border-zinc-200/80 dark:border-zinc-800 pb-3">
                <div
                    class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/40 border border-sky-200/80 dark:border-sky-800/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm shadow-2xs">
                    <i class="bi bi-layers-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight uppercase">Bintang Tingkat (Per Jenjang)</h3>
                    <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Top 3 akumulasi nilai tertinggi lintas kelas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($bintangLevel as $namaLevel => $santris)
                    <div
                        class="m3-glass-card overflow-hidden flex flex-col">
                        <div
                            class="bg-zinc-50/80 dark:bg-zinc-950/60 px-4 py-3 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                            <h4 class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider">
                                Jenjang: {{ $namaLevel }}</h4>
                            <span
                                class="text-[9px] font-black bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark border border-primary/20 dark:border-primary-dark/30 px-2 py-0.5 rounded-md uppercase tracking-wider">Top
                                3</span>
                        </div>
                        <div class="p-3 flex-1 flex flex-col gap-2">
                            @foreach ($santris as $i => $s)
                                <div
                                    class="flex items-center gap-2.5 p-2 rounded-xl transition-colors {{ $i == 0 ? 'bg-amber-50/50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/40' : 'bg-zinc-50/40 dark:bg-zinc-900/40' }}">
                                    <div
                                        class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs shrink-0 shadow-2xs
                                        {{ $i == 0 ? 'bg-amber-500 text-white' : ($i == 1 ? 'bg-slate-400 text-white' : 'bg-amber-700 text-white') }}">
                                        @if ($i == 0)
                                            <i class="bi bi-trophy-fill text-[11px]"></i>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-black text-xs text-zinc-900 dark:text-zinc-100 truncate">
                                            {{ $s->murid->nama_lengkap }}</div>
                                        <div
                                            class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider truncate">
                                            {{ $s->ruangan_nama }}</div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-black text-xs text-primary dark:text-primary-dark">
                                            {{ $s->total_nilai }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- BINTANG RUANGAN -->
        <div class="animate-[modalFadeIn_0.3s_ease-out]">
            <div class="flex items-center gap-2.5 mb-4 border-b border-zinc-200/80 dark:border-zinc-800 pb-3">
                <div
                    class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm shadow-2xs">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight uppercase">Bintang Ruangan (Per Kelas)</h3>
                    <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Peringkat 3 besar pada masing-masing ruangan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ($bintangRuangan as $namaRuangan => $santris)
                    <div
                        class="m3-glass-card overflow-hidden flex flex-col">
                        <div
                            class="bg-zinc-50/80 dark:bg-zinc-950/60 px-3.5 py-2.5 border-b border-zinc-200/80 dark:border-zinc-800">
                            <h4
                                class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider truncate">
                                {{ $namaRuangan }}</h4>
                        </div>
                        <div class="p-3 flex-1 flex flex-col gap-1.5">
                            @foreach ($santris as $i => $s)
                                <div
                                    class="flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800/60 last:border-0 pb-1.5 last:pb-0">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span
                                            class="font-black text-[10px] text-zinc-400 w-3.5 text-center">{{ $i + 1 }}.</span>
                                        <span
                                            class="font-bold text-xs text-zinc-800 dark:text-zinc-200 truncate">{{ $s->murid->nama_lengkap }}</span>
                                    </div>
                                    <span
                                        class="font-black text-xs text-primary dark:text-primary-dark shrink-0">{{ $s->total_nilai }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- STATE AWAL PANDUAN -->
        <x-empty-state icon="bi-award" title="Peringkat Bintang Pelajar"
            message="Pilih Agenda Ujian pada filter di atas untuk melihat peringkat Top 3 di setiap Ruangan Kelas dan Jenjang Tingkatan." />
    @endif

</x-app-layout>

