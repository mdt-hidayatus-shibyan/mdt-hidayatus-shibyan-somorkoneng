@forelse($groupedLevels as $tingkatId => $levels)
    @php
        // Ambil referensi tingkat dari item pertama di dalam grup
        $tingkat = $levels->first()->tingkat;
        // Gunakan warna database, atau fallback ke warna default
        $warnaTingkat = $tingkat->kode_warna ?? '#146C2E';
    @endphp

    <!-- 1. Header Group Tingkat -->
    <div class="mb-2.5 mt-5 first:mt-0 flex items-center gap-2 transition-colors duration-300">
        <!-- Indikator Warna -->
        <div class="w-1.5 h-5 rounded-full shadow-sm" style="background-color: {{ $warnaTingkat }};"></div>
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            {{ $tingkat->nama_tingkat ?? 'Tingkat Tidak Diketahui' }}
        </h3>
        <!-- Badge Jumlah Level -->
        <span
            class="px-2 py-0.5 bg-zinc-100/80 dark:bg-zinc-900 rounded-lg text-[10px] font-extrabold text-zinc-500 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-800 ml-1">
            {{ $levels->count() }} Level
        </span>
    </div>

    <!-- 2. Grid Daftar Level -->
    <div class="grid grid-cols-1 gap-2.5 mb-5">
        @foreach ($levels as $level)
            <div
                class="m3-glass-card p-4 md:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group relative overflow-hidden hover:border-primary/40 dark:hover:border-primary-dark/40 transition-all duration-300">

                <!-- Card Info Section -->
                <div class="flex items-center gap-3 md:gap-3.5 relative z-10 w-full sm:w-auto">

                    <!-- Urutan Badge -->
                    <span
                        class="w-9 h-9 flex items-center justify-center bg-zinc-100/80 dark:bg-zinc-900 text-primary dark:text-primary-dark rounded-xl text-xs font-black border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                        {{ $level->urutan_level }}
                    </span>

                    <!-- Kotak Ikon / Kode Level -->
                    <div class="w-11 h-11 rounded-xl shrink-0 flex items-center justify-center transition-transform duration-300 group-hover:scale-105 shadow-sm"
                        style="background-color: {{ $warnaTingkat }};">
                        <span class="text-white font-black text-xs md:text-sm tracking-wider drop-shadow-sm">
                            {{ $level->nama_level }}
                        </span>
                    </div>

                    <!-- Teks Judul -->
                    <div class="flex-1 overflow-hidden">
                        <h4
                            class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-snug truncate">
                            {{ $level->nama_level }}
                        </h4>
                        <div class="flex flex-wrap items-center gap-2 mt-0.5">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-md bg-zinc-100/80 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[10px] font-bold text-zinc-500 dark:text-zinc-400">
                                Tingkat:
                                <span class="text-zinc-900 dark:text-zinc-100 font-extrabold ml-1">
                                    {{ $level->tingkat->nama_tingkat ?? '-' }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Status & Action Buttons Section -->
                <div
                    class="flex items-center justify-between sm:justify-end gap-3 sm:gap-3.5 relative z-10 w-full sm:w-auto border-t sm:border-none border-zinc-100 dark:border-zinc-800/60 pt-3 sm:pt-0 mt-1 sm:mt-0">

                    <!-- Toggle Status Component -->
                    <x-toggle-status :is-active="$level->is_active" :url="route('level.toggle-status', $level->id)" />

                    <!-- Divider -->
                    <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-800 transition-colors duration-300">
                    </div>

                    <!-- Action Buttons Column -->
                    <div class="flex items-center gap-1.5">
                        @can('update level')
                            <!-- Tombol Edit -->
                            <a href="{{ route('level.edit', $level->id) }}"
                                class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200/60 dark:border-blue-800/40 transition-all hover:scale-105 active:scale-95 outline-none action-modal"
                                title="Edit">
                                <i class="bi bi-pencil-fill text-xs"></i>
                            </a>
                        @endcan

                        @can('delete level')
                            <!-- Tombol Hapus -->
                            <form action="{{ route('level.destroy', $level->id) }}" method="POST"
                                class="delete-ajax inline m-0 p-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-900/50 border border-red-200/60 dark:border-red-800/40 transition-all hover:scale-105 active:scale-95 outline-none"
                                    title="Hapus">
                                    <i class="bi bi-trash-fill text-xs"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@empty
    <!-- Empty State Component Utama -->
    <x-empty-state icon="bi-layout-sidebar" title="Data Level Kosong"
        message="Anda belum mengatur level kelas untuk seluruh tingkat pendidikan." />
@endforelse

