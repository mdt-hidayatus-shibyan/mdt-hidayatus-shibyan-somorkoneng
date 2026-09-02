@section('title', 'Rekap - Presensi Ustadz')

<x-app-layout>
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-start justify-between gap-4 relative z-10 print:hidden">
        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('presensi-ustadz.menu')
        </div>

        <!-- Area Form Pencarian (Sejajar dengan Menu) -->
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('presensi-ustadz.rekapSemua') }}" method="GET"
                class="flex flex-col sm:flex-row items-center gap-2 w-full xl:w-auto" id="formFilter">

                <!-- Filter Bulan -->
                <div class="w-full sm:flex-1">
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-moon-stars-fill text-xs"></i>
                        </div>
                        <select name="bulan_id" required
                            class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none">
                            <option value="" class="text-zinc-500">-- Pilih Bulan --</option>
                            @foreach ($bulans as $b)
                                <option value="{{ $b->id }}"
                                    {{ $bulan_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahun_hijriyah }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-chevron-down text-[10px] font-black"></i>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="w-full sm:w-auto shrink-0">
                    <button type="submit"
                        class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5">
                        <i class="bi bi-search"></i> <span>Tampilkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($bulan_id)
        <div
            class="m3-glass-card overflow-hidden mb-6 relative z-10 shadow-2xs">

            <!-- Header Leger -->
            <div
                class="bg-zinc-100/50 dark:bg-zinc-800/40 border-b border-zinc-200/80 dark:border-zinc-800 px-5 md:px-6 py-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-3 relative z-10">
                <div>
                    <h3
                        class="font-black text-zinc-900 dark:text-white text-base tracking-tight leading-tight uppercase mb-0.5">
                        Rekap Kehadiran Mengajar <span class="text-primary dark:text-primary-dark">(Per Ruangan)</span>
                    </h3>
                    <p
                        class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider flex items-center">
                        <i class="bi bi-info-circle-fill mr-1.5 opacity-70"></i> Bulan: <span
                            class="text-zinc-700 dark:text-zinc-300 ml-1">{{ $bulanTerpilih->nama_bulan }}
                            {{ $bulanTerpilih->tahun_hijriyah }}</span>
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-2 print:hidden w-full sm:w-auto">
                    <!-- Tombol Export Excel -->
                    <a href="{{ route('presensi-ustadz.exportExcel', ['bulan_id' => $bulan_id]) }}"
                        class="w-full sm:w-auto h-9 px-4 bg-emerald-600 hover:bg-emerald-700 shadow-2xs text-white rounded-xl text-xs font-black tracking-wide transition-all active:scale-95 flex items-center justify-center gap-1.5 shrink-0 outline-none">
                        <i class="bi bi-file-earmark-excel-fill text-xs"></i> Export Excel
                    </a>

                    <!-- Tombol Cetak PDF/Printer -->
                    <a href="{{ route('presensi-ustadz.cetak-rekap', ['bulan_id' => $bulan_id]) }}" target="_blank"
                        class="w-full sm:w-auto h-9 px-4 bg-white dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 shadow-2xs text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-black tracking-wide transition-all active:scale-95 flex items-center justify-center gap-1.5 shrink-0 outline-none">
                        <i class="bi bi-printer-fill text-xs"></i> Cetak Laporan
                    </a>
                </div>
            </div>

            <!-- Area Tabel Matriks -->
            <div class="overflow-x-auto relative z-10 custom-scrollbar p-0">
                <table class="w-full text-left text-xs border-collapse min-w-max">
                    <thead
                        class="bg-zinc-100/70 dark:bg-zinc-800/50 border-b border-zinc-200/80 dark:border-zinc-800 sticky top-0 z-30">
                        <tr class="text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <!-- Sticky Col: No -->
                            <th
                                class="py-3 px-4 text-center border-r border-zinc-200/60 dark:border-zinc-800/60 sticky left-0 z-30 bg-zinc-100/90 dark:bg-zinc-800/90 backdrop-blur w-14">
                                No
                            </th>
                            <!-- Sticky Col: Nama Guru -->
                            <th
                                class="py-3 px-5 border-r border-zinc-200/60 dark:border-zinc-800/60 sticky left-[56px] z-30 bg-zinc-100/90 dark:bg-zinc-800/90 backdrop-blur w-64">
                                Nama Guru
                            </th>

                            <!-- Looping Kolom Ruangan -->
                            @foreach ($ruangans as $ruangan)
                                <th class="py-3 px-4 text-center border-r border-zinc-200/60 dark:border-zinc-800/60 whitespace-nowrap min-w-[80px]"
                                    title="{{ $ruangan->nama_ruangan }}">
                                    {{ $ruangan->nama_ruangan }}
                                </th>
                            @endforeach

                            <th
                                class="py-3 px-5 text-center text-primary dark:text-primary-dark min-w-[100px] bg-primary/5">
                                Jumlah Total
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200/80 dark:divide-zinc-800/60">
                        @php $no = 1; @endphp
                        @foreach ($rekap as $row)
                            <tr
                                class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors group/row">

                                <!-- Sel: No (Sticky) -->
                                <td
                                    class="py-3 px-4 text-center border-r border-zinc-200/60 dark:border-zinc-800/60 font-bold text-zinc-500 dark:text-zinc-400 sticky left-0 z-20 bg-white/90 dark:bg-zinc-900/90 backdrop-blur group-hover/row:bg-zinc-100 dark:group-hover/row:bg-zinc-800 transition-colors">
                                    {{ $no++ }}
                                </td>

                                <!-- Sel: Nama (Sticky) -->
                                <td
                                    class="py-3 px-5 border-r border-zinc-200/60 dark:border-zinc-800/60 font-black text-zinc-900 dark:text-white sticky left-[56px] z-20 bg-white/90 dark:bg-zinc-900/90 backdrop-blur group-hover/row:bg-zinc-100 dark:group-hover/row:bg-zinc-800 whitespace-nowrap transition-colors">
                                    {{ $row['nama'] }}
                                </td>

                                <!-- Sel: Kehadiran per Ruangan -->
                                @foreach ($ruangans as $ruangan)
                                    <td class="py-2 px-3 text-center border-r border-zinc-200/60 dark:border-zinc-800/60">
                                        @if ($row['ruangan'][$ruangan->id] > 0)
                                            <span
                                                class="inline-flex items-center justify-center min-w-[24px] h-6 px-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-black text-[11px] shadow-2xs">
                                                {{ $row['ruangan'][$ruangan->id] }}
                                            </span>
                                        @else
                                            <span
                                                class="text-zinc-300 dark:text-zinc-700 font-black text-sm opacity-50">-</span>
                                        @endif
                                    </td>
                                @endforeach

                                <!-- Sel: Total -->
                                <td class="py-2 px-5 text-center bg-primary/5">
                                    @if ($row['total'] > 0)
                                        <span
                                            class="inline-flex items-center justify-center min-w-[28px] h-7 px-2.5 rounded-xl bg-primary text-white font-black text-xs shadow-2xs">
                                            {{ $row['total'] }}
                                        </span>
                                    @else
                                        <span
                                            class="text-zinc-300 dark:text-zinc-700 font-black text-sm opacity-50">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- State Awal saat halaman baru dibuka -->
        <div class="col-span-full">
            <x-empty-state icon="bi-funnel" title="Pilih Bulan" message="Silakan pilih bulan pada filter di atas untuk melihat rekapitulasi kinerja/kehadiran mengajar asatidz." />
        </div>
    @endif
</x-app-layout>
