@section('title', 'Tagihan')

<x-app-layout>
    <!-- Header Page -->
    <div
        class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 relative z-20 print:hidden">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                Tagihan Murid
            </h2>
        </div>

        <!-- Filter Tahun Pelajaran (Header) -->
        <div class="w-full sm:w-auto shrink-0">
            <form action="{{ request()->url() }}" method="GET" id="formTahun"
                class="m-0 relative group h-10 w-full sm:w-64">
                <div
                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10 text-zinc-400">
                    <i class="bi bi-calendar-range text-xs"></i>
                </div>
                <select name="tahun_id" onchange="document.getElementById('formTahun').submit()"
                    class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none">
                    @foreach ($daftarTahun as $tahun)
                        <option value="{{ $tahun->id }}" {{ $tahunPelajaranId == $tahun->id ? 'selected' : '' }}>
                            {{ $tahun->nama_hijriyah }} | {{ $tahun->nama_masehi }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none z-10 text-zinc-400">
                    <i class="bi bi-chevron-down text-[10px] font-black"></i>
                </div>
            </form>
        </div>
    </div>

    <!-- PANEL FILTER UTAMA -->
    <div
        class="m3-glass-card p-3.5 md:p-4 mb-6 md:mb-8 relative overflow-hidden z-10 print:hidden shadow-2xs">
        <form action="{{ request()->url() }}" method="GET" id="formFilterMatriks"
            class="flex flex-col sm:flex-row items-center gap-2.5 w-full">
            <input type="hidden" name="tahun_id" value="{{ $tahunPelajaranId }}">

            <!-- 1. Pilih Ruangan -->
            <div class="w-full sm:flex-1">
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-door-open-fill text-xs"></i>
                    </div>
                    <select name="ruangan_id" onchange="document.getElementById('formFilterMatriks').submit()"
                        class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="text-zinc-500">-- Silakan Pilih Ruangan --</option>
                        @foreach ($daftarRuangan as $r)
                            <option value="{{ $r->id }}"
                                {{ isset($ruanganTerpilih) && $ruanganTerpilih->id == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-[10px] font-black"></i>
                    </div>
                </div>
            </div>

            <!-- 2. Pilih Tagihan -->
            <div class="w-full sm:flex-1">
                <div class="relative">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-tags-fill text-xs"></i>
                    </div>
                    <select name="pengaturan_tagihan_id" {{ !$ruanganTerpilih ? 'disabled' : '' }}
                        class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none disabled:opacity-50 disabled:cursor-not-allowed">
                        @if (!$ruanganTerpilih)
                            <option value="">-- Terkunci (Pilih Ruangan Dulu) --</option>
                        @else
                            <option value="">-- Pilih Jenis Tagihan --</option>
                            @foreach ($masterBiayas as $biaya)
                                <option value="{{ $biaya->id }}"
                                    {{ isset($jenisTagihanTerpilih) && $jenisTagihanTerpilih->id == $biaya->id ? 'selected' : '' }}>
                                    {{ $biaya->nama_tagihan }}
                                    {{ $biaya->is_completed ? '(Siap)' : '(Belum Lengkap)' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-[10px] font-black"></i>
                    </div>
                </div>
            </div>

            <!-- Tombol Action -->
            <div class="w-full sm:w-auto shrink-0">
                <button type="submit" {{ !$ruanganTerpilih ? 'disabled' : '' }}
                    class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="bi bi-table"></i> <span>Tampilkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- AREA MATRIKS INVOICE -->
    @if ($ruanganTerpilih && $jenisTagihanTerpilih)
        <div class="relative z-10">

            <form action="{{ route('tagihan-murid.proses') }}" method="POST"
                class="relative z-10 flex flex-col gap-4">
                @csrf
                <input type="hidden" name="ruangan_id" value="{{ $ruanganTerpilih->id }}">
                <input type="hidden" name="pengaturan_tagihan_id" value="{{ $jenisTagihanTerpilih->id }}">

                <!-- 1. KARTU HEADER -->
                <div
                    class="m3-glass-card px-5 py-3.5 flex flex-col sm:flex-row justify-between sm:items-center gap-3 relative z-10 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-xl bg-primary/10 text-primary dark:text-primary-dark border border-primary/20 flex items-center justify-center shrink-0">
                            <i class="bi bi-receipt text-sm"></i>
                        </div>
                        <div>
                            <h3
                                class="font-black text-zinc-900 dark:text-white text-base uppercase tracking-tight mb-0.5 leading-none">
                                {{ $ruanganTerpilih->nama_ruangan }}
                            </h3>
                            <p
                                class="text-[10px] font-black text-primary dark:text-primary-dark uppercase tracking-wider flex items-center">
                                <i class="bi bi-tags-fill mr-1.5 opacity-70"></i> Penerbitan:
                                <span
                                    class="ml-1 text-zinc-600 dark:text-zinc-400">{{ $jenisTagihanTerpilih->nama_tagihan }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Kumpulan Tombol Aksi -->
                    <div
                        class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto shrink-0">
                        <!-- Tombol Check All Global -->
                        <button type="button" id="btnCentangGlobal" data-state="none" onclick="toggleCentangSemua()"
                            class="h-9 px-4 w-full sm:w-auto bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-black uppercase tracking-wider transition-all active:scale-95 flex items-center justify-center gap-1.5 shadow-2xs outline-none">
                            <i class="bi bi-check-all text-sm"></i> <span>Centang Semua</span>
                        </button>

                        <!-- Tombol Cetak Tercentang -->
                        @if ($jenisTagihanTerpilih->tipe === 'bulanan')
                            <button type="button" onclick="cetakKartuTercentang()"
                                class="h-9 px-4 w-full sm:w-auto bg-sky-500/10 border border-sky-500/20 hover:bg-sky-500 hover:text-white dark:hover:bg-sky-500 dark:hover:text-white text-sky-600 dark:text-sky-400 rounded-xl text-xs font-black uppercase tracking-wider transition-all active:scale-95 flex items-center justify-center gap-1.5 shadow-2xs outline-none">
                                <i class="bi bi-printer-fill text-xs"></i>
                                <span>Cetak Pilihan</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- 2. AREA GRID MATRIX TABLE -->
                <div class="overflow-x-auto custom-scrollbar pb-4">
                    <table class="w-full text-left text-xs border-separate border-spacing-y-2 min-w-[950px]">
                        <thead class="sticky top-0 z-30">
                            <tr
                                class="text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">

                                <!-- STICKY CHECKBOX HEADER -->
                                <th
                                    class="py-3 px-3 text-center sticky left-0 z-30 bg-zinc-100/90 dark:bg-zinc-800/90 backdrop-blur-md border-y border-l border-zinc-200/80 dark:border-zinc-800 rounded-l-xl w-[50px] min-w-[50px] shadow-2xs">
                                    <i class="bi bi-ui-checks text-xs" title="Centang Per Baris"></i>
                                </th>

                                <!-- STICKY NAMA HEADER -->
                                <th
                                    class="py-3 pl-4 sticky left-[50px] z-30 bg-zinc-100/90 dark:bg-zinc-800/90 backdrop-blur-md border-y border-zinc-200/80 dark:border-zinc-800 w-[240px] min-w-[240px] shadow-2xs">
                                    Nama Murid
                                </th>

                                <!-- KOLOM DINAMIS (BULAN / SEMESTER) -->
                                @if ($jenisTagihanTerpilih->tipe === 'bulanan')
                                    @foreach ($bulanHijriyah as $bln)
                                        <th class="py-2 text-center bg-zinc-100/90 dark:bg-zinc-800/90 backdrop-blur-md border-y border-zinc-200/80 dark:border-zinc-800"
                                            title="{{ $bln->nama_bulan }} {{ $bln->tahun_hijriyah }}">
                                            <div class="leading-none">{{ substr($bln->nama_bulan, 0, 3) }}</div>
                                            <div class="text-[8px] opacity-60 mt-0.5 tracking-tighter">
                                                {{ $bln->tahun_hijriyah }}</div>
                                        </th>
                                    @endforeach
                                @else
                                    <th
                                        class="py-3 px-5 text-center bg-zinc-100/90 dark:bg-zinc-800/90 backdrop-blur-md border-y border-zinc-200/80 dark:border-zinc-800">
                                        Status Tagihan
                                    </th>
                                @endif

                                <!-- KOLOM CETAK KARTU SPP -->
                                @if ($jenisTagihanTerpilih->tipe === 'bulanan')
                                    <th
                                        class="py-3 px-4 text-center bg-sky-500/10 text-sky-600 dark:text-sky-400 backdrop-blur-md border-y border-r border-zinc-200/80 dark:border-zinc-800 rounded-r-xl w-[100px]">
                                        Cetak Kartu
                                    </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody class="bg-transparent">
                            @foreach ($murids as $murid)
                                @php
                                    $umur = $murid->tanggal_lahir
                                        ? \Carbon\Carbon::parse($murid->tanggal_lahir)->age
                                        : 16;
                                    $isKelasRendahYatim = ($ruanganTerpilih->level->urutan_level ?? 0) <= 7;
                                    $wali = $murid->waliMurid ?? $murid->wali_murid;
                                    $statusAyah = $murid ? strtolower($murid->status_ayah ?? '') : '';
                                    $isKeluargaAsatidz = $wali
                                        ? (bool) ($wali->is_asatidz ?? ($wali->is_ustadz ?? false))
                                        : false;
                                    $isYatimLayak = $statusAyah === 'meninggal' && $umur <= 15 && $isKelasRendahYatim;

                                    $namaTagihanClean = trim(strtolower($jenisTagihanTerpilih->nama_tagihan));
                                    $isSPP =
                                        in_array($namaTagihanClean, ['spp', 'syahriyah', 'spp/syahriyah']) ||
                                        str_contains($namaTagihanClean, 'spp') ||
                                        str_contains($namaTagihanClean, 'syahriyah');

                                    $kategoriKhusus = $isSPP && ($isYatimLayak || $isKeluargaAsatidz);
                                    $isTerkunci = $isKeluargaAsatidz && $isSPP;
                                @endphp

                                <!-- BARIS KARTU MELAYANG -->
                                <tr class="group/row transition-all duration-200">

                                    <!-- STICKY ROW CHECKBOX CELL -->
                                    <td
                                        class="p-2 text-center sticky left-0 z-20 bg-white/90 dark:bg-zinc-900/90 backdrop-blur border-y border-l border-zinc-200/80 dark:border-zinc-800 group-hover/row:border-primary/40 rounded-l-xl align-middle w-[50px] min-w-[50px] shadow-2xs transition-colors">
                                        <label
                                            class="relative inline-flex items-center justify-center cursor-pointer group/label w-5 h-5">
                                            <input type="checkbox" class="sr-only peer chk-row"
                                                data-row="{{ $murid->id }}"
                                                onchange="toggleCentangBaris(this, '{{ $murid->id }}')">
                                            <div
                                                class="absolute inset-0 rounded-lg transition-all shadow-2xs bg-white/40 dark:bg-black/40 border border-zinc-300 dark:border-zinc-600 peer-checked:bg-primary peer-checked:border-primary-dark">
                                            </div>
                                            <i
                                                class="bi bi-check-lg absolute text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none pointer-events-none font-black"></i>
                                        </label>
                                    </td>

                                    <!-- STICKY NAMA CELL -->
                                    <td
                                        class="p-3 pl-4 sticky left-[50px] z-20 bg-white/90 dark:bg-zinc-900/90 backdrop-blur border-y border-zinc-200/80 dark:border-zinc-800 group-hover/row:border-primary/40 transition-colors w-[240px] min-w-[240px] shadow-2xs">
                                        <div class="font-black text-xs text-zinc-900 dark:text-white tracking-tight mb-0.5 truncate"
                                            title="{{ $murid->nama_lengkap }}">{{ $murid->nama_lengkap }}</div>
                                        <div
                                            class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider truncate">
                                            NISM: {{ $murid->nism ?? '-' }}</div>

                                        <!-- Badges Status -->
                                        @if ($kategoriKhusus)
                                            <div class="mt-1.5 flex gap-1">
                                                @if ($isKeluargaAsatidz)
                                                    <span
                                                        class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[9px] font-black px-1.5 py-0.5 rounded-lg tracking-wider flex items-center shadow-2xs">
                                                        <i class="bi bi-stars mr-1"></i> BEBAS SPP
                                                        <button type="button" id="btn-unlock-{{ $murid->id }}"
                                                            onclick="toggleKunciSpp('{{ $murid->id }}', '{{ addslashes($murid->nama_lengkap) }}', 'buka')"
                                                            class="ml-1.5 px-1 rounded-md bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-700 dark:text-emerald-300 transition-colors outline-none"
                                                            title="Buka Kunci untuk Membayar SPP">
                                                            <i class="bi bi-unlock-fill"></i>
                                                        </button>
                                                        <button type="button" id="btn-lock-{{ $murid->id }}"
                                                            onclick="toggleKunciSpp('{{ $murid->id }}', '{{ addslashes($murid->nama_lengkap) }}', 'kunci')"
                                                            class="hidden ml-1.5 px-1 rounded-md bg-rose-500/20 hover:bg-rose-500/30 text-rose-700 dark:text-rose-300 transition-colors outline-none"
                                                            title="Kunci Kembali Bebas SPP">
                                                            <i class="bi bi-lock-fill"></i>
                                                        </button>
                                                    </span>
                                                @else
                                                    <span
                                                        class="bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 text-[9px] font-black px-1.5 py-0.5 rounded-lg tracking-wider shadow-2xs">
                                                        <i class="bi bi-heart-fill mr-1"></i> M. DONATUR
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    <!-- KOLOM DINAMIS (BULANAN) -->
                                    @if ($jenisTagihanTerpilih->tipe === 'bulanan')
                                        @foreach ($bulanHijriyah as $bln)
                                            @php
                                                $recordTerbit = isset($tagihanExisting[$murid->id])
                                                    ? $tagihanExisting[$murid->id]->firstWhere(
                                                        'bulan_hijriyah_id',
                                                        $bln->id,
                                                    )
                                                    : null;
                                            @endphp

                                            <td
                                                class="p-2 text-center bg-white/40 dark:bg-zinc-900/30 border-y border-zinc-200/80 dark:border-zinc-800 group-hover/row:border-primary/40 align-middle transition-colors shadow-2xs">

                                                @if ($recordTerbit)
                                                    <!-- JIKA SUDAH PERNAH DITERBITKAN/DIBAYAR -->
                                                    <div class="flex items-center justify-center w-full h-full">
                                                        @if ($recordTerbit->status_bayar === 'Lunas')
                                                            <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center mx-auto"
                                                                title="Sudah Lunas">
                                                                <i class="bi bi-check-all text-xs leading-none"></i>
                                                            </div>
                                                        @else
                                                            <label
                                                                class="relative inline-flex items-center justify-center cursor-pointer group/circle w-6 h-6"
                                                                title="Belum Lunas (Bisa dibatalkan)">
                                                                <input type="checkbox"
                                                                    name="tagihan[{{ $murid->id }}][]"
                                                                    value="{{ $bln->id }}" checked
                                                                    class="sr-only peer chk-matriks"
                                                                    data-row="{{ $murid->id }}">
                                                                <div
                                                                    class="absolute inset-0 rounded-lg transition-all border bg-rose-500/10 border-rose-500/20 peer-checked:bg-primary peer-checked:border-primary-dark group-hover/circle:scale-110 active:scale-95 shadow-2xs">
                                                                </div>
                                                                <i
                                                                    class="bi bi-dash absolute text-rose-500 opacity-100 peer-checked:opacity-0 transition-opacity text-sm leading-none pointer-events-none"></i>
                                                                <i
                                                                    class="bi bi-check-lg absolute text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none pointer-events-none font-black"></i>
                                                            </label>
                                                        @endif
                                                    </div>
                                                @else
                                                    <!-- JIKA BELUM DITERBITKAN -->
                                                    <div
                                                        class="kunci-spp-{{ $murid->id }} text-zinc-300 dark:text-zinc-700 font-black select-none {{ !$isTerkunci ? 'hidden' : '' }}">
                                                        —</div>

                                                    <div
                                                        class="checkbox-spp-{{ $murid->id }} {{ $isTerkunci ? 'hidden' : '' }} flex items-center justify-center w-full h-full">
                                                        <label
                                                            class="relative inline-flex items-center justify-center cursor-pointer group/circle w-6 h-6">
                                                            <input type="checkbox"
                                                                name="tagihan[{{ $murid->id }}][]"
                                                                value="{{ $bln->id }}"
                                                                {{ $isTerkunci ? 'disabled' : '' }}
                                                                class="sr-only peer chk-matriks"
                                                                data-row="{{ $murid->id }}">
                                                            <div
                                                                class="absolute inset-0 rounded-lg transition-all border bg-rose-500/10 border-rose-500/20 peer-checked:bg-primary peer-checked:border-primary-dark group-hover/circle:scale-110 active:scale-95 shadow-2xs">
                                                            </div>
                                                            <i
                                                                class="bi bi-dash absolute text-rose-500 opacity-100 peer-checked:opacity-0 transition-opacity text-sm leading-none pointer-events-none"></i>
                                                            <i
                                                                class="bi bi-check-lg absolute text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none pointer-events-none font-black"></i>
                                                        </label>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td
                                            class="p-2 text-center bg-white/40 dark:bg-zinc-900/30 border-y border-r border-zinc-200/80 dark:border-zinc-800 group-hover/row:border-primary/40 rounded-r-xl align-middle transition-colors shadow-2xs">
                                            @if ($isSPP)
                                                <a href="{{ route('tagihan-murid.cetak-kartu-spp', ['murid_id' => $murid->id, 'tahun_id' => $tahunPelajaranId, 'ruangan_id' => $ruanganTerpilih->id]) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-500/10 text-sky-600 hover:bg-sky-500 hover:text-white border border-sky-500/20 dark:text-sky-400 dark:hover:bg-sky-500 dark:hover:text-white transition-all shadow-2xs group/btn outline-none"
                                                    title="Cetak Kartu SPP {{ $murid->nama_lengkap }}">
                                                    <i class="bi bi-card-heading text-xs group-hover/btn:scale-110 transition-transform"></i>
                                                    <span class="text-[9px] font-black uppercase tracking-wider ml-1">Cetak</span>
                                                </a>
                                            @else
                                                <span class="text-zinc-300 dark:text-zinc-700 font-bold opacity-50">-</span>
                                            @endif
                                        </td>
                                    @else
                                        <!-- KOLOM DINAMIS (SEMESTER/LAINNYA) -->
                                        @php
                                            $namaSpesifik = $jenisTagihanTerpilih->nama_tagihan;
                                            if (
                                                $jenisTagihanTerpilih->tipe === 'semester' &&
                                                in_array($ruanganTerpilih->level->nama_level ?? '', [
                                                    '3 TPQ',
                                                    '6 IBT',
                                                    '3 TSA',
                                                ]) &&
                                                strtolower($namaSpesifik) === 'iuran imda 2'
                                            ) {
                                                $namaSpesifik = 'Iuran IMNI';
                                            }
                                            if ($isYatimLayak && $isSPP) {
                                                $namaSpesifik .= ' (Dibayarkan donatur jika ada)';
                                            }

                                            $recordTerbit = isset($tagihanExisting[$murid->id])
                                                ? $tagihanExisting[$murid->id]->firstWhere(
                                                    'nama_tagihan_spesifik',
                                                    $namaSpesifik,
                                                )
                                                : null;
                                        @endphp

                                        <td
                                            class="p-2 text-center bg-white/40 dark:bg-zinc-900/30 border-y border-zinc-200/80 dark:border-zinc-800 group-hover/row:border-primary/40 align-middle transition-colors shadow-2xs">

                                            @if ($recordTerbit)
                                                <div class="flex items-center justify-center w-full h-full">
                                                    @if ($recordTerbit->status_bayar === 'Lunas')
                                                        <div class="w-6 h-6 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center mx-auto"
                                                            title="Sudah Lunas">
                                                            <i class="bi bi-check-all text-xs leading-none"></i>
                                                        </div>
                                                    @else
                                                        <label
                                                            class="relative inline-flex items-center justify-center cursor-pointer group/circle w-6 h-6"
                                                            title="Belum Lunas (Bisa dibatalkan)">
                                                            <input type="checkbox"
                                                                name="tagihan[{{ $murid->id }}][]" value="1"
                                                                checked class="sr-only peer chk-matriks"
                                                                data-row="{{ $murid->id }}">
                                                            <div
                                                                class="absolute inset-0 rounded-lg transition-all border bg-rose-500/10 border-rose-500/20 peer-checked:bg-primary peer-checked:border-primary-dark group-hover/circle:scale-110 active:scale-95 shadow-2xs">
                                                            </div>
                                                            <i
                                                                class="bi bi-dash absolute text-rose-500 opacity-100 peer-checked:opacity-0 transition-opacity text-sm leading-none pointer-events-none"></i>
                                                            <i
                                                                class="bi bi-check-lg absolute text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none pointer-events-none font-black"></i>
                                                        </label>
                                                    @endif
                                                </div>
                                            @else
                                                <div
                                                    class="kunci-spp-{{ $murid->id }} text-zinc-300 dark:text-zinc-700 font-black select-none {{ !$isTerkunci ? 'hidden' : '' }}">
                                                    —</div>
                                                <div
                                                    class="checkbox-spp-{{ $murid->id }} {{ $isTerkunci ? 'hidden' : '' }} flex items-center justify-center w-full h-full">
                                                    <label
                                                        class="relative inline-flex items-center justify-center cursor-pointer group/circle w-6 h-6">
                                                        <input type="checkbox" name="tagihan[{{ $murid->id }}][]"
                                                            value="1" {{ $isTerkunci ? 'disabled' : '' }}
                                                            class="sr-only peer chk-matriks"
                                                            data-row="{{ $murid->id }}">
                                                        <div
                                                            class="absolute inset-0 rounded-lg transition-all border bg-rose-500/10 border-rose-500/20 peer-checked:bg-primary peer-checked:border-primary-dark group-hover/circle:scale-110 active:scale-95 shadow-2xs">
                                                        </div>
                                                        <i
                                                            class="bi bi-dash absolute text-rose-500 opacity-100 peer-checked:opacity-0 transition-opacity text-sm leading-none pointer-events-none"></i>
                                                        <i
                                                            class="bi bi-check-lg absolute text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none pointer-events-none font-black"></i>
                                                        </label>
                                                </div>
                                            @endif
                                        </td>
                                    @endif

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="h-2"></div>

                <!-- 3. KARTU FOOTER STICKY -->
                <div
                    class="sticky bottom-6 z-40 m3-glass-card p-3.5 md:px-6 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 shadow-xl">
                    <p class="text-[10px] text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider hidden md:block">
                        Pastikan Anda mencentang tagihan sebelum menekan tombol terbitkan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <button type="submit"
                            class="m3-btn-primary w-full sm:w-auto h-10 px-6 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5">
                            <i class="bi bi-cloud-arrow-up-fill text-xs"></i> <span>Terbitkan Tagihan Centang</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- SCRIPT LOGIKA CHECKBOX DAN BUKA/TUTUP KUNCI -->
        <script>
            function toggleKunciSpp(muridId, namaMurid, aksi) {
                const isDark = document.documentElement.classList.contains('dark');
                let title, html, confirmText, confirmColor, iconType;

                if (aksi === 'buka') {
                    title = '<span class="text-base font-black tracking-tight">Buka Kunci SPP?</span>';
                    html =
                        `<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Tindakan ini akan memunculkan opsi penerbitan tagihan SPP bagi Murid <b>${namaMurid}</b> (Keluarga Ustadz) atas keinginan sendiri.</p>`;
                    iconType = 'question';
                    confirmText = 'Ya, Buka Kunci';
                    confirmColor = '#059669';
                } else {
                    title = '<span class="text-base font-black tracking-tight">Kunci Kembali SPP?</span>';
                    html =
                        `<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Status bulan yang <b>belum diterbitkan</b> untuk <b>${namaMurid}</b> akan dikunci dan dikembalikan menjadi Bebas SPP.</p>`;
                    iconType = 'warning';
                    confirmText = 'Ya, Kunci Kembali';
                    confirmColor = '#e11d48';
                }

                Swal.fire({
                    title: title,
                    html: html,
                    icon: iconType,
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#71717a',
                    confirmButtonText: confirmText,
                    heightAuto: false,
                    cancelButtonText: 'Batal',
                    background: isDark ? '#0c0c0e' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b',
                    customClass: {
                        popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                        confirmButton: "h-10 px-5 bg-primary hover:bg-primary-dark text-white font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none",
                        cancelButton: "h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none"
                    }
                }).then((res) => {
                    if (res.isConfirmed) {
                        if (aksi === 'buka') {
                            document.querySelectorAll('.kunci-spp-' + muridId).forEach(el => el.classList.add(
                                'hidden'));
                            document.querySelectorAll('.checkbox-spp-' + muridId).forEach(el => {
                                el.classList.remove('hidden');
                                el.querySelectorAll('input[type="checkbox"]').forEach(chk => chk.disabled =
                                    false);
                            });

                            document.getElementById('btn-unlock-' + muridId).classList.add('hidden');
                            document.getElementById('btn-lock-' + muridId).classList.remove('hidden');
                        } else {
                            document.querySelectorAll('.checkbox-spp-' + muridId).forEach(el => {
                                el.classList.add('hidden');
                                el.querySelectorAll('input[type="checkbox"]').forEach(chk => {
                                    chk.checked = false;
                                    chk.disabled = true;
                                });
                            });
                            document.querySelectorAll('.kunci-spp-' + muridId).forEach(el => el.classList.remove(
                                'hidden'));

                            document.getElementById('btn-lock-' + muridId).classList.add('hidden');
                            document.getElementById('btn-unlock-' + muridId).classList.remove('hidden');
                        }
                        updateGlobalButtonState();
                    }
                });
            }

            function updateGlobalButtonState() {
                const checkboxes = document.querySelectorAll('.chk-matriks:not([disabled])');
                const checkedBoxes = document.querySelectorAll('.chk-matriks:not([disabled]):checked');
                const btn = document.getElementById('btnCentangGlobal');
                if (!btn) return;

                const icon = btn.querySelector('i');
                const text = btn.querySelector('span');

                if (checkboxes.length > 0 && checkboxes.length === checkedBoxes.length) {
                    btn.dataset.state = 'all';
                    icon.className = 'bi bi-x-lg text-xs font-black';
                    text.innerText = 'Hapus Centang';
                    btn.classList.replace('text-zinc-700', 'text-rose-600');
                    btn.classList.replace('dark:text-zinc-300', 'dark:text-rose-400');
                } else {
                    btn.dataset.state = 'none';
                    icon.className = 'bi bi-check-all text-sm';
                    text.innerText = 'Centang Semua';
                    btn.classList.replace('text-rose-600', 'text-zinc-700');
                    btn.classList.replace('dark:text-rose-400', 'dark:text-zinc-300');
                }

                document.querySelectorAll('.chk-row').forEach(rowChk => {
                    const rowId = rowChk.dataset.row;
                    const rowCheckboxes = document.querySelectorAll(
                        `.chk-matriks[data-row="${rowId}"]:not([disabled])`);
                    const rowChecked = document.querySelectorAll(
                        `.chk-matriks[data-row="${rowId}"]:not([disabled]):checked`);

                    if (rowCheckboxes.length > 0) {
                        rowChk.checked = (rowCheckboxes.length === rowChecked.length);
                        rowChk.disabled = false;
                        rowChk.parentElement.classList.remove('opacity-40', 'cursor-not-allowed');
                    } else {
                        rowChk.disabled = true;
                        rowChk.parentElement.classList.add('opacity-40', 'cursor-not-allowed');
                    }
                });
            }

            function toggleCentangSemua() {
                const btn = document.getElementById('btnCentangGlobal');
                const isAll = btn.dataset.state === 'all';
                document.querySelectorAll('.chk-matriks:not([disabled])').forEach(chk => {
                    chk.checked = !isAll;
                });
                updateGlobalButtonState();
            }

            function toggleCentangBaris(obj, rowId) {
                const isChecked = obj.checked;
                document.querySelectorAll(`.chk-matriks[data-row="${rowId}"]:not([disabled])`).forEach(chk => {
                    chk.checked = isChecked;
                });
                updateGlobalButtonState();
            }

            function cetakKartuTercentang() {
                const checkedRows = document.querySelectorAll('.chk-row:checked');

                if (checkedRows.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<span class="text-base font-black tracking-tight">Pilih Murid Dulu!</span>',
                        html: '<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Silakan centang minimal satu nama murid di sebelah kiri tabel untuk dicetak kartunya.</p>',
                        confirmButtonColor: '#0ea5e9',
                        heightAuto: false,
                        confirmButtonText: 'Mengerti',
                        background: document.documentElement.classList.contains('dark') ? '#0c0c0e' : '#ffffff',
                        color: document.documentElement.classList.contains('dark') ? '#f4f4f5' : '#18181b',
                        customClass: {
                            popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                            confirmButton: "h-10 px-5 bg-sky-600 hover:bg-sky-700 text-white font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none"
                        }
                    });
                    return;
                }

                let url = new URL(
                    "{{ route('tagihan-murid.cetak-kartu-spp-massal', ['ruangan_id' => $ruanganTerpilih->id, 'tahun_id' => $tahunPelajaranId]) }}"
                );

                checkedRows.forEach(chk => {
                    url.searchParams.append('murid_ids[]', chk.dataset.row);
                });

                window.open(url.toString(), '_blank');
            }

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.chk-matriks').forEach(chk => {
                    chk.addEventListener('change', updateGlobalButtonState);
                });
                updateGlobalButtonState();
            });
        </script>
    @else
        <!-- STATE KOSONG / AWAL -->
        <div class="col-span-full">
            <x-empty-state icon="bi-layout-sidebar" title="Menunggu Parameter Matriks" message="Silakan pilih Ruangan dan Kriteria Tagihan pada filter di atas untuk memulai penerbitan tagihan secara massal." />
        </div>
    @endif

</x-app-layout>

