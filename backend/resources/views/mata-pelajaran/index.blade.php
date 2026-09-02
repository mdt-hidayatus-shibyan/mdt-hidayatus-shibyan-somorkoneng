@section('title', 'Mata Pelajaran')

<x-app-layout>
    <!-- Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                Mata Pelajaran
            </h2>
            <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                Pilih tingkatan kelas untuk mengelola kurikulum dan mata pelajaran.
            </p>
        </div>

        <!-- Tombol Import -->
        @can('create mata-pelajaran')
            <a href="{{ route('mata-pelajaran.import') }}" class="action-modal m3-btn-primary w-full sm:w-auto px-4.5 group/btn">
                <i class="bi bi-file-earmark-spreadsheet-fill text-sm transition-transform duration-300 group-hover/btn:scale-110"></i>
                <span>Import Data</span>
            </a>
        @endcan
    </div>

    <!-- Grid Level / Tingkatan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5 relative z-10">
        @foreach ($levels as $level)
            @php
                $color = $level->tingkat->kode_warna ?? '#146C2E';
            @endphp

            <!-- Kartu Level -->
            <a href="{{ route('mata-pelajaran.level', $level->id) }}"
                class="m3-glass-card p-5 flex flex-col justify-between group hover:border-primary/40 dark:hover:border-primary-dark/40 hover:scale-[1.02] transition-all duration-300 relative overflow-hidden outline-none h-full"
                style="--card-color: {{ $color }};">

                <!-- Efek Ambient Glow di pojok kanan atas -->
                <div class="absolute -top-10 -right-10 w-28 h-28 rounded-full opacity-10 group-hover:opacity-20 group-hover:scale-150 transition-all duration-500 blur-2xl pointer-events-none"
                    style="background-color: var(--card-color);"></div>

                <div>
                    <!-- Ikon / Kode Tingkat -->
                    <div class="w-12 h-12 rounded-xl text-white flex items-center justify-center font-black text-base mb-4 shadow-sm group-hover:scale-105 transition-transform duration-300 relative z-10"
                        style="background-color: var(--card-color);">
                        {{ $level->tingkat->kode_tingkat ?? 'MDT' }}
                    </div>

                    <!-- Info Level -->
                    <div class="relative z-10">
                        <h3
                            class="font-black text-lg text-zinc-900 dark:text-white tracking-tight leading-snug mb-1 transition-colors">
                            {{ $level->nama_level }}
                        </h3>

                        <p
                            class="text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="bi bi-journal-bookmark-fill opacity-70 text-xs"></i>
                            {{ $level->mata_pelajarans_count }} Pelajaran
                        </p>
                    </div>
                </div>

                <!-- Bagian Bawah (Action) -->
                <div
                    class="mt-5 pt-3.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs font-bold text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-white transition-colors relative z-10">
                    <span>Kelola Mapel</span>
                    <div
                        class="w-7 h-7 rounded-lg bg-zinc-100/80 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-[var(--card-color)] group-hover:text-white transition-all duration-300">
                        <i class="bi bi-arrow-right-short text-lg group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</x-app-layout>

