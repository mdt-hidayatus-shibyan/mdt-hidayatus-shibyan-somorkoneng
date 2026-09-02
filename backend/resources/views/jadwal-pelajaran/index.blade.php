@section('title', 'Jadwal Pelajaran')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-4 relative z-10">
        <!-- Teks Header -->
        <div class="shrink-0">
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                Jadwal Pelajaran
            </h2>
            <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                Pilih ruangan untuk menyusun dan mengatur jadwal pembelajaran.
            </p>
        </div>

        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2.5 w-full xl:w-auto">
            <!-- FORM FILTER TERPADU (TAHUN & RUANGAN) -->
            <form action="{{ request()->url() }}" method="GET" class="w-full md:w-auto flex flex-col sm:flex-row gap-2.5">

                <!-- Dropdown Tahun Pelajaran -->
                <div class="relative group/select w-full sm:w-auto sm:min-w-[200px]">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-calendar-range text-sm"></i>
                    </div>
                    <select name="tahun_id" onchange="this.form.submit()"
                        class="m3-input-glass w-full !pl-9 !pr-9 appearance-none cursor-pointer">
                        @foreach ($daftarTahun as $tahun)
                            <option value="{{ $tahun->id }}"
                                {{ $tahunPelajaranId == $tahun->id ? 'selected' : '' }}>
                                {{ $tahun->nama_hijriyah }} | {{ $tahun->nama_masehi }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Dropdown Pilih Ruangan -->
                <div class="relative group/select w-full sm:w-auto sm:min-w-[220px]">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-door-open text-sm"></i>
                    </div>
                    <select name="ruangan_id" onchange="this.form.submit()"
                        class="m3-input-glass w-full !pl-9 !pr-9 appearance-none cursor-pointer">
                        <option value="">-- Semua Ruangan --</option>
                        @foreach ($daftarRuangan as $ruangItem)
                            <option value="{{ $ruangItem->id }}"
                                {{ isset($ruanganId) && $ruanganId == $ruangItem->id ? 'selected' : '' }}>
                                {{ $ruangItem->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>
            </form>

            <!-- TOMBOL JADWAL INDUK -->
            @can('read jadwal-pelajaran')
                <a href="{{ route('jadwal-pelajaran.induk') }}"
                    class="h-10 inline-flex items-center justify-center px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-black transition-all hover:scale-[1.02] active:scale-95 outline-none shadow-sm gap-2 shrink-0">
                    <i class="bi bi-grid-3x3-gap-fill text-sm"></i>
                    <span>Jadwal Induk</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- GRID KARTU KELAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-5 relative z-10">
        @forelse ($ruangans as $ruangan)
            @php
                $hexColor = $ruangan->level->tingkat->kode_warna ?? '#146C2E';
            @endphp

            <!-- Kartu Ruangan -->
            <a href="{{ route('jadwal-pelajaran.ruangan', $ruangan->id) }}"
                class="m3-glass-card p-5 flex flex-col justify-between group hover:border-primary/40 dark:hover:border-primary-dark/40 hover:scale-[1.02] transition-all duration-300 relative overflow-hidden outline-none h-full"
                style="--card-color: {{ $hexColor }};">

                <!-- Efek Background Glow -->
                <div class="absolute -top-10 -right-10 w-28 h-28 rounded-full opacity-10 group-hover:opacity-20 group-hover:scale-150 transition-all duration-500 blur-2xl pointer-events-none"
                    style="background-color: var(--card-color);"></div>

                <div>
                    <!-- Ikon / Kode Tingkat -->
                    <div class="w-12 h-12 rounded-xl text-white flex items-center justify-center font-black text-base mb-4 shadow-sm group-hover:scale-105 transition-transform duration-300 relative z-10"
                        style="background-color: var(--card-color);">
                        {{ $ruangan->level->tingkat->kode_tingkat ?? 'MDT' }}
                    </div>

                    <!-- Info Ruangan -->
                    <div class="relative z-10">
                        <h3 class="font-black text-lg text-zinc-900 dark:text-white tracking-tight leading-snug mb-1 transition-colors">
                            {{ $ruangan->nama_ruangan }}
                        </h3>

                        <p
                            class="text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="bi bi-clock-history opacity-70 text-xs"></i>
                            {{ $ruangan->jadwal_pelajarans_count ?? 0 }} Sesi Jadwal
                        </p>
                    </div>
                </div>

                <!-- Bagian Bawah (Action) -->
                <div
                    class="mt-5 pt-3.5 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs font-bold text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-white transition-colors relative z-10">
                    <span>Atur Jadwal</span>
                    <div
                        class="w-7 h-7 rounded-lg bg-zinc-100/80 dark:bg-zinc-800 flex items-center justify-center group-hover:bg-[var(--card-color)] group-hover:text-white transition-all duration-300">
                        <i class="bi bi-arrow-right-short text-lg group-hover:translate-x-0.5 transition-transform"></i>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full">
                <x-empty-state icon="bi-door-closed" title="Ruangan Tidak Ditemukan"
                    message="Tidak ada ruangan atau kelas yang sesuai dengan filter Anda." />
            </div>
        @endforelse
    </div>
</x-app-layout>

