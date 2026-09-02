@section('title', 'Rekap - Pelanggaran Murid')
<x-app-layout>
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div class="w-full xl:w-auto shrink-0">
            @include('pelanggaran-murid.menu')
        </div>

        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('pelanggaran-murid.rekap') }}" method="GET" id="filterForm"
                class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto m3-glass-card p-1.5 shadow-2xs">

                <!-- Filter Ruangan -->
                <div class="relative w-full sm:w-44 group/select">
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

                <!-- Filter Semester (Diberi onchange agar auto-reload menarik Bulan) -->
                <div class="relative w-full sm:w-52 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-calendar-range text-xs"></i>
                    </div>
                    <select name="semester_id" required onchange="document.getElementById('filterForm').submit();"
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Pilih Semester --
                        </option>
                        @foreach ($semesters as $s)
                            <option value="{{ $s->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ $semester_id == $s->id ? 'selected' : '' }}>
                                {{ $s->tahunPelajaran->nama_hijriyah ?? '' }} ({{ $s->nama_semester }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Filter Bulan -->
                <div class="relative w-full sm:w-48 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-moon-stars text-xs"></i>
                    </div>
                    <select name="bulan_id" {{ !$semester_id ? 'disabled' : '' }}
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none disabled:opacity-50 disabled:cursor-not-allowed">

                        @if (!$semester_id)
                            <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">Pilih Semester Dulu
                            </option>
                        @else
                            <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Semua Bulan --
                            </option>
                            @foreach ($bulans as $b)
                                <option value="{{ $b->id }}"
                                    class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                    {{ $bulan_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahun_hijriyah }}
                                </option>
                            @endforeach
                        @endif
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

    <!-- AREA TABEL REKAP -->
    @if ($ruangan_id && $semester_id)
        <!-- AREA HASIL REKAP TABEL (M3 GLASS) -->
        <div
            class="m3-glass-card overflow-hidden mb-6 relative z-10">

            <!-- Header Papan Peringkat -->
            <div
                class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 md:px-6 py-4 flex flex-col sm:flex-row justify-between sm:items-center gap-4 relative z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shadow-2xs shrink-0">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <div>
                        <h3
                            class="font-black text-zinc-900 dark:text-white text-base tracking-tight leading-tight uppercase flex items-center">
                            Peringkat Kedisiplinan: <span
                                class="text-primary dark:text-primary-dark ml-1.5">{{ $ruanganTerpilih->nama_ruangan }}</span>
                        </h3>
                        <p
                            class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                            Menampilkan santri dengan akumulasi skor poin tertinggi
                        </p>
                    </div>
                </div>
            </div>

            <!-- Area Tabel Matriks -->
            <div class="overflow-x-auto relative z-10 custom-scrollbar p-0">
                <table class="w-full text-left text-xs border-collapse min-w-[700px]">
                    <thead
                        class="bg-zinc-50/90 dark:bg-zinc-950/80 border-b border-zinc-200/80 dark:border-zinc-800 sticky top-0 z-20 backdrop-blur-md">
                        <tr class="text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="py-3 px-4 text-center border-r border-zinc-200/60 dark:border-zinc-800/80 w-20">
                                Peringkat</th>
                            <th class="py-3 px-4 w-32 border-r border-zinc-200/60 dark:border-zinc-800/80 text-center">NISM
                            </th>
                            <th class="py-3 px-5 border-r border-zinc-200/60 dark:border-zinc-800/80">Nama Santri</th>
                            <th
                                class="py-3 px-5 text-center text-amber-600 dark:text-amber-400 border-r border-zinc-200/60 dark:border-zinc-800/80 w-40">
                                Total Kasus</th>
                            <th
                                class="py-3 px-5 text-center text-rose-600 dark:text-rose-400 bg-rose-500/5 dark:bg-rose-500/5 w-48">
                                Akumulasi Poin</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/80 bg-white/40 dark:bg-zinc-900/40">
                        @php $no = 1; @endphp
                        @foreach ($rekap as $row)
                            <tr
                                class="hover:bg-zinc-500/5 transition-colors group/row">

                                <!-- Peringkat -->
                                <td
                                    class="py-3 px-4 text-center border-r border-zinc-200/60 dark:border-zinc-800/80 align-middle">
                                    @if ($row['total_poin'] > 0)
                                        @if ($no == 1)
                                            <div class="w-7 h-7 flex items-center justify-center rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 font-black text-xs mx-auto border border-rose-500/20 shadow-2xs"
                                                title="Peringkat 1">
                                                1
                                            </div>
                                        @elseif ($no == 2)
                                            <div
                                                class="w-7 h-7 flex items-center justify-center rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 font-black text-xs mx-auto border border-orange-500/20 shadow-2xs"
                                                title="Peringkat 2">
                                                2
                                            </div>
                                        @elseif ($no == 3)
                                            <div
                                                class="w-7 h-7 flex items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-xs mx-auto border border-amber-500/20 shadow-2xs"
                                                title="Peringkat 3">
                                                3
                                            </div>
                                        @else
                                            <div class="text-zinc-400 dark:text-zinc-500 font-bold text-xs">
                                                {{ $no }}
                                            </div>
                                        @endif
                                    @else
                                        <span
                                            class="text-zinc-300 dark:text-zinc-600 font-bold text-xs">-</span>
                                    @endif
                                </td>

                                <!-- NISM -->
                                <td
                                    class="py-3 px-4 font-mono font-bold text-zinc-500 dark:text-zinc-400 text-xs text-center border-r border-zinc-200/60 dark:border-zinc-800/80 align-middle">
                                    {{ $row['nism'] }}
                                </td>

                                <!-- Nama Murid -->
                                <td
                                    class="py-3 px-5 font-black text-xs text-zinc-900 dark:text-zinc-100 tracking-tight border-r border-zinc-200/60 dark:border-zinc-800/80 align-middle">
                                    {{ $row['nama'] }}
                                </td>

                                <!-- Total Kasus -->
                                <td
                                    class="py-3 px-5 text-center border-r border-zinc-200/60 dark:border-zinc-800/80 align-middle">
                                    @if ($row['total_kasus'] > 0)
                                        <span
                                            class="inline-flex items-center justify-center min-w-[32px] py-1 px-2.5 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 font-black text-xs border border-amber-500/20 shadow-2xs">
                                            {{ $row['total_kasus'] }}x
                                        </span>
                                    @else
                                        <span
                                            class="text-zinc-300 dark:text-zinc-600 font-bold text-xs">-</span>
                                    @endif
                                </td>

                                <!-- Akumulasi Poin -->
                                <td class="py-3 px-5 text-center bg-rose-500/5 dark:bg-rose-500/5 align-middle">
                                    @if ($row['total_poin'] > 0)
                                        <span
                                            class="inline-flex items-center justify-center min-w-[64px] py-1 px-3 rounded-xl bg-rose-600 text-white font-black shadow-2xs text-xs">
                                            <i class="bi bi-fire mr-1 text-rose-200 text-xs"></i>{{ $row['total_poin'] }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center py-1 px-2.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold text-[10px] uppercase tracking-wider border border-emerald-500/20 shadow-2xs">
                                            Aman (0)
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @php
                                if ($row['total_poin'] > 0) {
                                    $no++;
                                }
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- State Awal saat halaman baru dibuka -->
        <div class="py-16 text-center m3-glass-card relative z-10 print:hidden">
            <div
                class="w-12 h-12 bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center text-2xl mb-3 mx-auto shadow-2xs">
                <i class="bi bi-trophy"></i>
            </div>
            <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight mb-0.5">Papan Peringkat Kedisiplinan</h3>
            <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                Silakan pilih Ruangan dan Semester pada filter di atas untuk melihat akumulasi poin pelanggaran santri yang otomatis diurutkan dari yang tertinggi.
            </p>
        </div>
    @endif

</x-app-layout>

