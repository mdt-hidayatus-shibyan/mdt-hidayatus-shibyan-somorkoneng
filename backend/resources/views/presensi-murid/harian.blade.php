@section('title', 'Harian - Presensi Murid')
<x-app-layout>

    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10">

        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('presensi-murid.menu')
        </div>

        <!-- Area Form Pencarian -->
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('presensi-murid.index') }}" method="GET"
                class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto m3-glass-card p-1.5 shadow-2xs">

                <!-- Filter Tanggal -->
                <div class="relative w-full sm:w-44 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-calendar-date text-xs"></i>
                    </div>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" required
                        class="m3-input-glass w-full !pl-9 !pr-3 text-xs font-bold cursor-pointer">
                </div>

                <!-- Filter Ruangan -->
                <div class="relative w-full sm:w-48 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-door-open text-xs"></i>
                    </div>
                    <select name="ruangan_id" required
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Ruangan --</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ $ruangan_id == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Filter Jam Ke -->
                <div class="relative w-full sm:w-36 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-clock-history text-xs"></i>
                    </div>
                    <select name="jam_ke" required
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Jam --</option>
                        @foreach ($jamList as $j)
                            <option value="{{ $j }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ $jam_ke == $j ? 'selected' : '' }}>
                                Jam {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Tombol Submit Tampilkan -->
                <div class="w-full sm:w-auto shrink-0">
                    <button type="submit"
                        class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs group/btn">
                        <i class="bi bi-search text-xs mr-1"></i>
                        <span>Tampilkan</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    @if ($ruangan_id && $jam_ke)
        @if ($isLibur)
            <!-- STATE HARI LIBUR -->
            <div
                class="m3-glass-card p-8 md:p-12 text-center relative z-10 border-rose-500/30 bg-rose-500/5">
                <div
                    class="w-14 h-14 bg-rose-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-500/20 text-rose-500 text-2xl shadow-2xs">
                    <i class="bi bi-brightness-high-fill"></i>
                </div>
                <h3 class="text-xl font-black text-rose-600 dark:text-rose-400 mb-1 tracking-tight">Hari Libur Madrasah</h3>
                <div
                    class="inline-block bg-white/80 dark:bg-black/40 py-1.5 px-4 rounded-xl border border-rose-500/20 mt-2">
                    <p class="text-xs font-black text-rose-600 dark:text-rose-400 tracking-wide uppercase">
                        Keterangan: {{ $keteranganLibur }}
                    </p>
                </div>
                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-3">Form presensi dinonaktifkan pada hari libur.</p>
            </div>
        @elseif (!$jadwal)
            <!-- STATE TIDAK ADA JADWAL -->
            <div class="py-16 text-center m3-glass-card relative z-10">
                <div
                    class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800/80 rounded-2xl flex items-center justify-center mx-auto mb-3 text-zinc-400 dark:text-zinc-500 text-2xl shadow-2xs">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight mb-0.5">Tidak Ada Jadwal</h3>
                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                    Kelas ini tidak memiliki jadwal pada hari {{ $hari_ini }} Jam ke-{{ $jam_ke }}.
                </p>
            </div>
        @elseif($murids->isEmpty())
            <!-- STATE KELAS KOSONG -->
            <div class="py-16 text-center m3-glass-card relative z-10">
                <div
                    class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800/80 rounded-2xl flex items-center justify-center mx-auto mb-3 text-zinc-400 dark:text-zinc-500 text-2xl shadow-2xs">
                    <i class="bi bi-people"></i>
                </div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight mb-0.5">Kelas Kosong</h3>
                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">Belum ada murid yang terdaftar di ruangan ini.</p>
            </div>
        @else
            <!-- ============================================== -->
            <!-- FORM INPUT PRESENSI (DENSE LIST) -->
            <!-- ============================================== -->
            <form action="{{ route('presensi-murid.storeHarian') }}" method="POST"
                class="relative z-10">
                @csrf
                <input type="hidden" name="jadwal_pelajaran_id" value="{{ $jadwal->id }}">
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div class="m3-glass-card overflow-hidden relative">

                    <!-- Header List Presensi -->
                    <div
                        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 md:px-6 py-4 flex flex-col md:flex-row justify-between md:items-center gap-4 relative z-10">
                        <div class="flex items-center gap-3.5">
                            <div
                                class="w-10 h-10 rounded-xl bg-primary/10 border border-primary/20 text-primary dark:text-primary-dark flex items-center justify-center text-lg shrink-0">
                                <i class="bi bi-journal-bookmark-fill"></i>
                            </div>
                            <div>
                                <h3
                                    class="font-black text-zinc-900 dark:text-white text-base tracking-tight leading-tight uppercase mb-0.5">
                                    {{ $jadwal->mataPelajaran->nama_mapel }}
                                </h3>
                                <p
                                    class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest flex items-center">
                                    <i class="bi bi-person-fill mr-1 opacity-70"></i> Pengajar: <span
                                        class="text-zinc-700 dark:text-zinc-300 ml-1">{{ $jadwal->ustadz->nama_lengkap }}</span>
                                </p>
                            </div>
                        </div>

                        <div
                            class="text-left md:text-right flex items-center md:items-end gap-3 md:flex-col md:gap-1.5">
                            <span
                                class="inline-block px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-[9px] font-black rounded-lg uppercase tracking-widest">
                                Jam Ke-{{ $jam_ke }}
                            </span>
                            <span
                                class="block text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">
                                <i class="bi bi-calendar-event mr-1"></i>
                                {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                            </span>
                        </div>
                    </div>

                    <!-- Area List Murid (Padat / Dense) -->
                    <div class="p-0">
                        <ul class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            @foreach ($murids as $murid)
                                @php
                                    $statusSekarang = $presensiTersimpan->has($murid->id)
                                        ? $presensiTersimpan[$murid->id]->status
                                        : 'Hadir';
                                @endphp

                                <li
                                    class="px-5 py-3 flex flex-col md:flex-row md:items-center justify-between gap-3 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">

                                    <!-- Info Identitas -->
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-bold text-zinc-500 dark:text-zinc-400 shrink-0 text-[11px]">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-1 min-w-0 pr-2">
                                            <h4
                                                class="font-black text-xs text-zinc-900 dark:text-white truncate mb-0.5">
                                                {{ $murid->nama_lengkap }}
                                            </h4>
                                            <p
                                                class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider truncate font-mono">
                                                NISM: {{ $murid->nism ?? '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Pilihan Kehadiran Radio Buttons -->
                                    <div class="shrink-0 w-full md:w-auto">
                                        <!-- Container kotak pill radio -->
                                        <div
                                            class="grid grid-cols-5 gap-1 w-full md:w-[280px] bg-zinc-100/80 dark:bg-zinc-950/60 p-1 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                                            @foreach (['Hadir' => 'H', 'Sakit' => 'S', 'Izin' => 'I', 'Alpha' => 'A', 'Dispen' => 'D'] as $val => $label)
                                                <label class="cursor-pointer relative block w-full text-center">
                                                    <input type="radio" name="presensi[{{ $murid->id }}]"
                                                        value="{{ $val }}" class="peer sr-only"
                                                        {{ $statusSekarang == $val ? 'checked' : '' }}>

                                                    <div
                                                        class="w-full py-1.5 flex items-center justify-center text-[11px] font-black rounded-lg transition-all duration-200 border border-transparent text-zinc-400 dark:text-zinc-500 hover:bg-white dark:hover:bg-zinc-800
                                                    {{ $val == 'Hadir' ? 'peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:shadow-2xs dark:peer-checked:bg-emerald-600' : '' }}
                                                    {{ $val == 'Sakit' ? 'peer-checked:bg-blue-500 peer-checked:text-white peer-checked:shadow-2xs dark:peer-checked:bg-blue-600' : '' }}
                                                    {{ $val == 'Izin' ? 'peer-checked:bg-amber-500 peer-checked:text-white peer-checked:shadow-2xs dark:peer-checked:bg-amber-600' : '' }}
                                                    {{ $val == 'Alpha' ? 'peer-checked:bg-rose-500 peer-checked:text-white peer-checked:shadow-2xs dark:peer-checked:bg-rose-600' : '' }}
                                                    {{ $val == 'Dispen' ? 'peer-checked:bg-purple-500 peer-checked:text-white peer-checked:shadow-2xs dark:peer-checked:bg-purple-600' : '' }}
                                                    ">
                                                        {{ $label }}
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Footer / Tombol Simpan -->
                    @can('create presensi-murid')
                        <div
                            class="px-5 py-3.5 bg-zinc-50/80 dark:bg-zinc-950/70 border-t border-zinc-200/80 dark:border-zinc-800 flex justify-end z-20 sticky bottom-0">
                            <button type="submit"
                                class="m3-btn-primary w-full md:w-auto h-10 px-6 text-xs group/btn">
                                <i class="bi bi-check2-circle text-sm mr-1"></i>
                                <span>Simpan Presensi Jam Ini</span>
                            </button>
                        </div>
                    @endcan
                </div>
            </form>
        @endif
    @else
        <div class="py-16 text-center m3-glass-card relative z-10">
            <div
                class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800/80 rounded-2xl flex items-center justify-center mx-auto mb-3 text-zinc-400 dark:text-zinc-500 text-2xl shadow-2xs">
                <i class="bi bi-funnel"></i>
            </div>
            <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight mb-0.5">Pilih Filter Terlebih Dahulu</h3>
            <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                Silakan pilih Tanggal, Ruangan, dan Jam Pelajaran di atas untuk mulai menginput data presensi.
            </p>
        </div>
    @endif
</x-app-layout>

