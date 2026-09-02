@section('title', 'Rekapitulasi Presensi Ujian')

<x-app-layout>
    <!-- HEADER & FILTER -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-4 relative z-20">

        <div class="w-full xl:w-auto shrink-0">
            @include('presensi-ujian.menu')
        </div>

        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('presensi-ujian.rekap') }}" method="GET" id="formSelector"
                class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto m3-glass-card p-1.5 shadow-2xs">
                <input type="hidden" name="tahun_id" value="{{ $tahunPelajaranId }}">

                <!-- Filter Ruangan -->
                <div class="relative w-full sm:w-48 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-door-open text-xs"></i>
                    </div>
                    <select name="ruangan_id" onchange="document.getElementById('formSelector').submit()" required
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Pilih Ruangan --
                        </option>
                        @foreach ($daftarRuangan as $r)
                            <option value="{{ $r->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Filter Ujian -->
                <div class="relative w-full sm:w-52 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-file-earmark-check text-xs"></i>
                    </div>
                    <select name="ujian_id" onchange="document.getElementById('formSelector').submit()" required
                        {{ !$ruanganTerpilih ? 'disabled' : '' }}
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none disabled:opacity-50">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Pilih Ujian --
                        </option>
                        @foreach ($daftarUjian as $uj)
                            <option value="{{ $uj->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ request('ujian_id') == $uj->id ? 'selected' : '' }}>
                                {{ $uj->nama_ujian }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <div class="w-full sm:w-auto shrink-0">
                    <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-4 group/btn">
                        <i class="bi bi-search text-sm"></i>
                        <span>Tampilkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- AREA KONTEN UTAMA -->
    @if ($ruanganTerpilih && $ujianTerpilih)
        @if ($dataRekap->isEmpty())
            <x-empty-state icon="bi-people" title="Data Tidak Ditemukan"
                message="Belum ada santri aktif di kelas {{ $ruanganTerpilih->nama_ruangan }}." />
        @else
            <!-- CARD UTAMA REKAPITULASI -->
            <div class="m3-glass-card overflow-hidden">
                <!-- Header Card & Tombol Cetak -->
                <div
                    class="p-5 md:px-6 md:py-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="px-2.5 py-0.5 bg-primary/10 text-primary dark:text-primary-dark border border-primary/20 text-[10px] font-black rounded-lg uppercase tracking-wider">
                                {{ $ruanganTerpilih->nama_ruangan }}
                            </span>
                            <span
                                class="px-2.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-[10px] font-black rounded-lg uppercase tracking-wider">
                                {{ $ujianTerpilih->nama_ujian }}
                            </span>
                        </div>
                        <h3 class="font-black text-base text-zinc-900 dark:text-white uppercase tracking-tight">
                            Rekapitulasi Kehadiran Peserta Ujian
                        </h3>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('presensi-ujian.cetak-rekap', ['ujian_id' => $ujianTerpilih->id, 'ruangan_id' => $ruanganTerpilih->id]) }}"
                            target="_blank"
                            class="h-9 px-4 bg-primary text-white hover:bg-primary/90 dark:bg-primary-dark dark:text-zinc-900 dark:hover:bg-primary-dark/90 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-xs">
                            <i class="bi bi-printer text-xs"></i>
                            <span>Cetak Rekap Kelas</span>
                        </a>
                    </div>
                </div>

                <!-- Legend / Keterangan Kode -->
                <div
                    class="px-5 py-2.5 bg-zinc-100/50 dark:bg-zinc-900/40 border-b border-zinc-200/60 dark:border-zinc-800 flex flex-wrap items-center gap-4 text-xs font-bold text-zinc-500">
                    <span class="font-black text-[11px] uppercase tracking-wider text-zinc-400">Keterangan:</span>
                    <span class="flex items-center gap-1"><span
                            class="w-4 h-4 rounded text-[10px] font-black bg-emerald-500 text-white flex items-center justify-center">H</span>
                        Hadir</span>
                    <span class="flex items-center gap-1"><span
                            class="w-4 h-4 rounded text-[10px] font-black bg-blue-500 text-white flex items-center justify-center">S</span>
                        Sakit</span>
                    <span class="flex items-center gap-1"><span
                            class="w-4 h-4 rounded text-[10px] font-black bg-amber-500 text-white flex items-center justify-center">I</span>
                        Izin</span>
                    <span class="flex items-center gap-1"><span
                            class="w-4 h-4 rounded text-[10px] font-black bg-rose-500 text-white flex items-center justify-center">A</span>
                        Alpha</span>
                    <span class="flex items-center gap-1"><span
                            class="w-4 h-4 rounded text-[10px] font-black bg-purple-500 text-white flex items-center justify-center">D</span>
                        Dispensasi</span>
                    <span class="flex items-center gap-1 text-zinc-400"><span
                            class="w-4 h-4 rounded text-[10px] font-black bg-zinc-200 dark:bg-zinc-800 text-zinc-500 flex items-center justify-center">-</span>
                        Belum Diinput</span>
                </div>

                <!-- TABEL MATRIKS PRESENSI -->
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-zinc-100/80 dark:bg-zinc-900/80 text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200 dark:border-zinc-800">
                                <th class="py-3 px-3.5 text-center w-12 shrink-0">No</th>
                                <th class="py-3 px-3.5 w-24">NISM</th>
                                <th class="py-3 px-3.5 min-w-[180px]">Nama Santri</th>

                                <!-- Looping Kolom Mapel Ujian -->
                                @foreach ($jadwals as $jdw)
                                    @php
                                        $mapelSingkat = $jdw->mata_pelajaran_id
                                            ? $jdw->mataPelajaran->nama_mapel ?? '-'
                                            : $jdw->nama_mata_pelajaran_custom;
                                        $tglSingkat = \Carbon\Carbon::parse($jdw->tanggal_ujian)->format('d/m');
                                    @endphp
                                    <th class="py-3 px-2 text-center w-16 border-l border-zinc-200/60 dark:border-zinc-800"
                                        title="{{ $mapelSingkat }} ({{ \Carbon\Carbon::parse($jdw->tanggal_ujian)->format('d M Y') }})">
                                        <div class="text-[9px] font-bold text-zinc-400">{{ $tglSingkat }}</div>
                                        <div class="truncate max-w-[70px] mx-auto">{{ $mapelSingkat }}</div>
                                    </th>
                                @endforeach

                                <!-- Kolom Rekapitulasi Total -->
                                <th
                                    class="py-3 px-2 text-center w-10 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-l border-zinc-200 dark:border-zinc-800">
                                    H</th>
                                <th class="py-3 px-2 text-center w-10 bg-blue-500/10 text-blue-600 dark:text-blue-400">S
                                </th>
                                <th
                                    class="py-3 px-2 text-center w-10 bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                    I</th>
                                <th class="py-3 px-2 text-center w-10 bg-rose-500/10 text-rose-600 dark:text-rose-400">A
                                </th>
                                <th
                                    class="py-3 px-2 text-center w-10 bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                    D</th>
                                <th
                                    class="py-3 px-3 text-center w-16 bg-zinc-200/60 dark:bg-zinc-800/60 text-zinc-700 dark:text-zinc-300">
                                    % Hadir</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                            @foreach ($dataRekap as $row)
                                <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                                    <td class="py-3 px-3.5 text-center font-bold text-zinc-400">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="py-3 px-3.5 font-mono text-[11px] text-zinc-500 dark:text-zinc-400">
                                        {{ $row->murid->nism ?? '-' }}
                                    </td>
                                    <td class="py-3 px-3.5 font-black text-zinc-900 dark:text-white">
                                        {{ $row->murid->nama_lengkap }}
                                    </td>

                                    <!-- Nilai Kehadiran Per Mapel -->
                                    @foreach ($jadwals as $jdw)
                                        @php
                                            $itemPresensi = $row->detail_per_mapel[$jdw->id] ?? null;
                                            $status = $itemPresensi['status'] ?? '-';
                                        @endphp
                                        <td
                                            class="py-3 px-2 text-center border-l border-zinc-100 dark:border-zinc-800/60">
                                            @if ($status === 'Hadir')
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-emerald-500 text-white font-black text-[10px] shadow-2xs">H</span>
                                            @elseif ($status === 'Sakit')
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-blue-500 text-white font-black text-[10px] shadow-2xs">S</span>
                                            @elseif ($status === 'Izin')
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-amber-500 text-white font-black text-[10px] shadow-2xs">I</span>
                                            @elseif ($status === 'Alpha')
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-rose-500 text-white font-black text-[10px] shadow-2xs">A</span>
                                            @elseif ($status === 'Dispensasi')
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-purple-500 text-white font-black text-[10px] shadow-2xs">D</span>
                                            @else
                                                <span class="text-zinc-300 dark:text-zinc-700 font-bold">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <!-- Kolom Total -->
                                    <td
                                        class="py-3 px-2 text-center font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/5 border-l border-zinc-200 dark:border-zinc-800">
                                        {{ $row->hadir }}
                                    </td>
                                    <td
                                        class="py-3 px-2 text-center font-black text-blue-600 dark:text-blue-400 bg-blue-500/5">
                                        {{ $row->sakit }}
                                    </td>
                                    <td
                                        class="py-3 px-2 text-center font-black text-amber-600 dark:text-amber-400 bg-amber-500/5">
                                        {{ $row->izin }}
                                    </td>
                                    <td
                                        class="py-3 px-2 text-center font-black text-rose-600 dark:text-rose-400 bg-rose-500/5">
                                        {{ $row->alpha }}
                                    </td>
                                    <td
                                        class="py-3 px-2 text-center font-black text-purple-600 dark:text-purple-400 bg-purple-500/5">
                                        {{ $row->dispensasi }}
                                    </td>
                                    <td
                                        class="py-3 px-3 text-center font-black {{ $row->persentase_kehadiran >= 80 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} bg-zinc-100/60 dark:bg-zinc-800/40">
                                        {{ $row->persentase_kehadiran }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @else
        <x-empty-state icon="bi-file-spreadsheet" title="Pilih Ruangan & Pelaksanaan Ujian"
            message="Silakan tentukan Ruangan dan Pelaksanaan Ujian di atas untuk melihat matriks rekapitulasi presensi santri." />
    @endif
</x-app-layout>
