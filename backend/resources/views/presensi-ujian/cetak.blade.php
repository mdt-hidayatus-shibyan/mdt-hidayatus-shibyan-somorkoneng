@section('title', 'Pusat Cetak Dokumen Ujian')

<x-app-layout>
    <!-- HEADER & FILTER -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-4 relative z-20">

        <div class="w-full xl:w-auto shrink-0">
            @include('presensi-ujian.menu')
        </div>

        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('presensi-ujian.cetak-menu') }}" method="GET" id="formSelector"
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
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
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
            </form>
        </div>
    </div>

    <!-- AREA KONTEN UTAMA -->
    @if ($ruanganTerpilih && $ujianTerpilih)
        <div class="space-y-6 animate-[modalFadeIn_0.2s_ease-out]">
            <!-- BANNER CETAK REKAPITULASI SATU KELAS -->
            <div
                class="m3-glass-card p-5 md:p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gradient-to-r from-primary/10 via-white/50 to-primary/5 dark:from-primary-dark/10 dark:via-zinc-900/50 dark:to-transparent border-primary/20">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-primary text-white dark:bg-primary-dark dark:text-zinc-900 flex items-center justify-center text-2xl shrink-0 shadow-sm">
                        <i class="bi bi-printer-fill"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-base text-zinc-900 dark:text-white uppercase tracking-tight">
                            Rekapitulasi Presensi Seluruh Ujian Kelas
                        </h3>
                        <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Cetak lembar rekapitulasi kehadiran seluruh mata pelajaran untuk
                            {{ $ruanganTerpilih->nama_ruangan }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('presensi-ujian.cetak-rekap', ['ujian_id' => $ujianTerpilih->id, 'ruangan_id' => $ruanganTerpilih->id]) }}"
                    target="_blank" class="m3-btn-primary h-10 px-5 text-xs group/btn shrink-0 w-full md:w-auto">
                    <i class="bi bi-file-earmark-pdf-fill mr-1"></i>
                    <span>Cetak Rekap Kelas</span>
                </a>
            </div>

            <!-- DAFTAR SESI UJIAN UNTUK CETAK DHPU & BERITA ACARA -->
            <div class="m3-glass-card overflow-hidden">
                <div
                    class="px-5 py-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                    <span
                        class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-card-checklist text-primary dark:text-primary-dark text-sm"></i>
                        Daftar Dokumen Pelaksanaan Ujian Per Mata Pelajaran
                    </span>
                    <span class="text-[11px] font-bold text-zinc-400">
                        {{ $jadwals->count() }} Sesi Terjadwal
                    </span>
                </div>

                @if ($jadwals->isEmpty())
                    <div class="py-12 text-center">
                        <p class="text-xs font-bold text-zinc-500">Belum ada jadwal ujian yang disusun untuk level ini.
                        </p>
                    </div>
                @else
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @foreach ($jadwals as $jdw)
                            @php
                                $mapelNama = $jdw->mata_pelajaran_id
                                    ? $jdw->mataPelajaran->nama_mapel ?? '-'
                                    : $jdw->nama_mata_pelajaran_custom;
                            @endphp

                            <div
                                class="p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <!-- Info Sesi Ujian -->
                                <div class="flex items-start gap-3.5">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center font-black text-xs text-zinc-600 dark:text-zinc-300 shrink-0">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <span
                                                class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-[9px] font-black rounded-md uppercase">
                                                <i class="bi bi-calendar-event mr-1"></i>
                                                {{ \Carbon\Carbon::parse($jdw->tanggal_ujian)->translatedFormat('l, d M Y') }}
                                            </span>
                                            <span
                                                class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-[9px] font-black rounded-md uppercase">
                                                <i class="bi bi-clock mr-1"></i>
                                                {{ \Carbon\Carbon::parse($jdw->waktu_mulai)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($jdw->waktu_selesai)->format('H:i') }} WIB
                                            </span>
                                        </div>
                                        <h4
                                            class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-tight">
                                            {{ $mapelNama }}
                                        </h4>
                                        <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mt-0.5">
                                            Pengawas: <span
                                                class="text-zinc-700 dark:text-zinc-300 font-extrabold">{{ $jdw->pengawas->nama_lengkap ?? 'Belum Diatur' }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Tombol Aksi Cetak -->
                                <div class="flex flex-wrap items-center gap-2 shrink-0">
                                    <!-- DHPU Lembar Kosong (Tanda Tangan Basah) -->
                                    <a href="{{ route('presensi-ujian.cetak-dhpu', ['ujian_id' => $ujianTerpilih->id, 'ruangan_id' => $ruanganTerpilih->id, 'jadwal_ujian_id' => $jdw->id, 'mode' => 'kosong']) }}"
                                        target="_blank"
                                        title="Daftar Hadir Kosong untuk Tanda Tangan Fisik Peserta Ujian"
                                        class="h-9 px-3.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-2xs">
                                        <i class="bi bi-pen text-xs text-primary dark:text-primary-dark"></i>
                                        <span>DHPU Kosong</span>
                                    </a>

                                    <!-- DHPU Terisi (Hasil Rekam Presensi) -->
                                    <a href="{{ route('presensi-ujian.cetak-dhpu', ['ujian_id' => $ujianTerpilih->id, 'ruangan_id' => $ruanganTerpilih->id, 'jadwal_ujian_id' => $jdw->id, 'mode' => 'terisi']) }}"
                                        target="_blank" title="Daftar Hadir Terisi dengan Status Kehadiran"
                                        class="h-9 px-3.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-2xs">
                                        <i class="bi bi-check2-square text-xs text-emerald-600"></i>
                                        <span>DHPU Terisi</span>
                                    </a>

                                    <!-- Berita Acara Ujian -->
                                    <a href="{{ route('presensi-ujian.cetak-berita-acara', ['ujian_id' => $ujianTerpilih->id, 'ruangan_id' => $ruanganTerpilih->id, 'jadwal_ujian_id' => $jdw->id]) }}"
                                        target="_blank" title="Berita Acara Pelaksanaan Ujian Resmi"
                                        class="h-9 px-3.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-1.5 transition-all shadow-2xs">
                                        <i class="bi bi-file-earmark-text text-xs text-amber-600"></i>
                                        <span>Berita Acara</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        <x-empty-state icon="bi-printer" title="Pilih Ruangan & Pelaksanaan Ujian"
            message="Silakan tentukan Ruangan dan Pelaksanaan Ujian di atas untuk mengakses pusat cetak dokumen presensi ujian." />
    @endif
</x-app-layout>
