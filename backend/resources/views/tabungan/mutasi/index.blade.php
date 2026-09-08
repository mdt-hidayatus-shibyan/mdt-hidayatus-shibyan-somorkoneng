@section('title', 'Cek Mutasi & Verifikasi Buku Tabungan')
<x-app-layout>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('tabungan.dashboard') }}"
                    class="text-xs font-bold text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                    <i class="bi bi-wallet2"></i> Tabungan Madrasah
                </a>
                <span class="text-zinc-400 dark:text-zinc-600 text-xs">•</span>
                <a href="{{ route('tabungan.pembagian.index') }}"
                    class="text-xs font-bold text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                    Pembagian Akhir
                </a>
                <span class="text-zinc-400 dark:text-zinc-600 text-xs">•</span>
                <span class="text-xs font-bold text-zinc-500 dark:text-zinc-400">Cek Mutasi & Verifikasi</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Cek Mutasi & Verifikasi Buku Tabungan
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Pemeriksaan mutasi transaksi bulanan, pencocokan buku fisik dengan catatan aplikasi, dan penetapan
                status siap dikembalikan.
            </p>
        </div>

        <!-- Tombol Aksi Navigasi Atas -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('tabungan.pembagian.index') }}" class="m3-btn-secondary text-xs">
                <i class="bi bi-gift-fill text-xs text-purple-600"></i>
                <span>Tutup Buku & Pembagian</span>
            </a>
            <a href="{{ route('tabungan.rincian.index') }}" class="m3-btn-secondary text-xs">
                <i class="bi bi-safe2-fill text-xs text-blue-600"></i>
                <span>Kontrol Kas Brankas</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if (session('success'))
        <div
            class="mb-5 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="bi bi-check-circle-fill text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div
            class="mb-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="bi bi-exclamation-triangle-fill text-base shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- FORM PENCARIAN / SCAN BARCODE BUKU TABUNGAN -->
    <div class="m3-glass-card p-4 md:p-5 rounded-2xl md:rounded-3xl mb-6 shadow-xs border-l-4 border-l-primary">
        <form action="{{ route('tabungan.cek-mutasi.index') }}" method="GET" id="searchRekeningForm"
            class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <i class="bi bi-upc-scan text-base text-primary dark:text-primary-dark"></i>
                </div>
                <input type="text" name="nomor_rekening" id="scanInputRekening" value="{{ $queryInput }}"
                    placeholder="Scan Barcode / Ketik No. Rekening (contoh: 1000001 atau 001) / Nama Murid..."
                    autofocus autocomplete="off"
                    class="m3-input-glass w-full pl-10 pr-24 py-3 text-xs md:text-sm font-mono font-bold tracking-wider">
                @if ($prefix)
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span
                            class="text-[10px] font-mono px-2 py-1 rounded bg-zinc-200/70 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold">
                            Prefix: {{ $prefix }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="m3-btn-primary text-xs py-3 px-5 whitespace-nowrap">
                    <i class="bi bi-search text-xs"></i>
                    <span>Cari & Cek Mutasi</span>
                </button>
                @if ($queryInput)
                    <a href="{{ route('tabungan.cek-mutasi.index') }}"
                        class="p-3 rounded-xl bg-zinc-200/60 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-xs font-bold transition-colors flex items-center justify-center"
                        title="Bersihkan Pencarian">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
        <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-2 flex items-center gap-1.5">
            <i class="bi bi-info-circle text-primary"></i>
            <span>Tips: Gunakan barcode scanner atau ketik 3 digit akhir nomor buku. Sistem akan otomatis memuat
                rekapitulasi transaksi.</span>
        </p>
    </div>

    @if ($tabungan)
        <!-- GRID KONTEN REKENING DITEMUKAN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <!-- KOLOM KIRI: DETAIL PROFIL & METRIK SALDO NASABAH (1 Col) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Card 1: Identitas Nasabah -->
                <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl shadow-xs relative overflow-hidden">
                    <div class="flex items-start gap-3.5 mb-4">
                        <x-avatar :name="$tabungan->nama_nasabah" size="lg" />
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-black text-base text-zinc-900 dark:text-white truncate">
                                    {{ $tabungan->nama_nasabah }}
                                </span>
                                @if ($tabungan->jenis_nasabah === 'Murid')
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[10px] font-black">Murid
                                        / Murid</span>
                                @elseif ($tabungan->jenis_nasabah === 'Ustadz')
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 text-[10px] font-black">Dewan
                                        Ustadz</span>
                                @elseif ($tabungan->jenis_nasabah === 'Kas Ruangan')
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black">Kas
                                        Ruangan</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-black">Umum</span>
                                @endif
                            </div>
                            <div
                                class="text-xs font-mono font-bold text-primary dark:text-primary-dark mt-1 flex items-center gap-1.5">
                                <i class="bi bi-barcode"></i>
                                <span>{{ $tabungan->nomor_rekening }}</span>
                            </div>
                            <div class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-1">
                                {{ $tabungan->identitas_nasabah }}
                                @if ($tabungan->murid?->ruangans?->first())
                                    • Kelas {{ $tabungan->murid->ruangans->first()->nama_ruangan }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Verifikasi Banner -->
                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                        <div class="text-[10px] font-black uppercase text-zinc-400 tracking-wider mb-1.5">
                            Status Verifikasi Buku Fisik:
                        </div>
                        @if ($tabungan->status_verifikasi === 'Cocok')
                            <div
                                class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-800 dark:text-emerald-300 flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-emerald-600 text-base shrink-0 mt-0.5"></i>
                                <div class="text-xs">
                                    <p class="font-extrabold text-emerald-700 dark:text-emerald-300">
                                        SUDAH COCOK & SIAP DIKEMBALIKAN
                                    </p>
                                    <p class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 mt-0.5">
                                        Buku fisik ada & perolehan saldo sesuai sistem. Diverifikasi oleh
                                        {{ $tabungan->diverifikasiOleh?->name ?? 'Admin' }}
                                        @if ($tabungan->diverifikasi_pada)
                                            pada {{ $tabungan->diverifikasi_pada->format('d/m/Y H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @elseif ($tabungan->status_verifikasi === 'Selisih')
                            <div
                                class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/25 text-rose-800 dark:text-rose-300 flex items-start gap-2.5">
                                <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0 mt-0.5"></i>
                                <div class="text-xs">
                                    <p class="font-extrabold text-rose-700 dark:text-rose-300">
                                        ADA SELISIH DENGAN BUKU FISIK
                                    </p>
                                    @if ($tabungan->saldo_buku_fisik !== null)
                                        <p class="text-[11px] font-mono font-bold text-rose-600 mt-0.5">
                                            Saldo Fisik: Rp
                                            {{ number_format($tabungan->saldo_buku_fisik, 0, ',', '.') }}
                                            (Selisih: Rp
                                            {{ number_format($tabungan->saldo_buku_fisik - $tabungan->saldo, 0, ',', '.') }})
                                        </p>
                                    @endif
                                    @if ($tabungan->catatan_verifikasi)
                                        <p class="text-[10px] italic text-zinc-500 mt-1">
                                            "{{ $tabungan->catatan_verifikasi }}"
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @elseif ($tabungan->status_verifikasi === 'Buku Tidak Ada')
                            <div
                                class="p-3 rounded-xl bg-purple-500/10 border border-purple-500/25 text-purple-800 dark:text-purple-300 flex items-start gap-2.5">
                                <i class="bi bi-journal-x text-purple-600 text-base shrink-0 mt-0.5"></i>
                                <div class="text-xs flex-1">
                                    <p class="font-extrabold text-purple-700 dark:text-purple-300">
                                        BUKU TABUNGAN TIDAK ADA / HILANG
                                    </p>
                                    <p class="text-[10px] text-purple-700/80 dark:text-purple-400/80 mt-0.5">
                                        Gunakan cetak ukuran A6 sebagai pengganti buku fisik.
                                    </p>
                                    <div class="mt-2">
                                        <a href="{{ route('tabungan.cek-mutasi.cetak-a6', $tabungan->id) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-600 text-white hover:bg-purple-700 text-[10px] font-bold shadow-xs">
                                            <i class="bi bi-printer-fill"></i> Cetak Lembar Mutasi A6
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div
                                class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/25 text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                                <i class="bi bi-hourglass-split text-amber-600 text-base shrink-0 mt-0.5"></i>
                                <div class="text-xs">
                                    <p class="font-extrabold text-amber-700 dark:text-amber-300">
                                        BELUM DIVERIFIKASI
                                    </p>
                                    <p class="text-[10px] text-amber-700/80 dark:text-amber-400/80 mt-0.5">
                                        Silakan lakukan verifikasi keberadaan buku & kecocokan mutasi di bawah.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card 2: Ringkasan Saldo & Hak Bersih -->
                <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl shadow-xs space-y-3.5">
                    <h4 class="text-xs font-black uppercase tracking-wider text-zinc-400">
                        Ringkasan Finansial Rekening
                    </h4>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-zinc-500 font-semibold">Saldo Tercatat di Aplikasi:</span>
                        <span class="font-mono font-black text-sm text-zinc-900 dark:text-white">
                            Rp {{ number_format($tabungan->saldo, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-zinc-500 font-semibold">Total Setoran Terkumpul:</span>
                        <span class="font-mono font-bold text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($tabungan->total_setor, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-zinc-500 font-semibold">Total Penarikan Terjadi:</span>
                        <span class="font-mono font-bold text-rose-500">
                            Rp {{ number_format($tabungan->total_tarik, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($kalkulasi)
                        <div
                            class="flex justify-between items-center text-xs text-amber-600 dark:text-amber-400 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <span class="font-semibold">Potongan Madrasah
                                ({{ $kalkulasi['persentase_potongan'] }}%):</span>
                            <span class="font-mono font-bold">
                                Rp {{ number_format($kalkulasi['nominal_potongan'], 0, ',', '.') }}
                            </span>
                        </div>

                        <div
                            class="flex justify-between items-center text-xs pt-2 border-t border-zinc-200 dark:border-zinc-700 bg-emerald-500/5 p-2 rounded-xl">
                            <span class="font-black text-emerald-700 dark:text-emerald-300">Hak Bersih Diterima:</span>
                            <span class="font-mono font-black text-base text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($kalkulasi['saldo_bersih_total'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Card 3: Form Eksekusi Verifikasi Buku Fisik & Toggle Keberadaan Buku -->
                <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl shadow-xs border-2 border-primary/30">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                        <h4 class="text-xs font-black uppercase tracking-wider text-zinc-900 dark:text-white">
                            Form Verifikasi Buku Tabungan
                        </h4>
                    </div>

                    @php
                        $isBukuAda =
                            ($tabungan->buku_tabungan_ada ?? true) && $tabungan->status_verifikasi !== 'Buku Tidak Ada';
                    @endphp

                    <form action="{{ route('tabungan.cek-mutasi.verifikasi', $tabungan->id) }}" method="POST"
                        class="space-y-4 text-xs">
                        @csrf

                        <!-- TOGGLE 1: APAKAH BUKU TABUNGAN ADA? -->
                        <div>
                            <label class="block font-black text-zinc-800 dark:text-zinc-200 mb-2">
                                1. Apakah Buku Tabungan Fisik Ada?
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="buku_tabungan_ada" value="1" id="bukuAdaYes"
                                        class="peer sr-only" {{ $isBukuAda ? 'checked' : '' }}
                                        onchange="handleBukuAdaToggle(true)">
                                    <div
                                        class="p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-300 text-center font-bold transition-all flex items-center justify-center gap-1.5">
                                        <i class="bi bi-journal-check text-base"></i>
                                        <span>Buku Fisik Ada</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="buku_tabungan_ada" value="0" id="bukuAdaNo"
                                        class="peer sr-only" {{ !$isBukuAda ? 'checked' : '' }}
                                        onchange="handleBukuAdaToggle(false)">
                                    <div
                                        class="p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 peer-checked:border-rose-500 peer-checked:bg-rose-500/10 peer-checked:text-rose-700 dark:peer-checked:text-rose-300 text-center font-bold transition-all flex items-center justify-center gap-1.5">
                                        <i class="bi bi-journal-x text-base"></i>
                                        <span>Tidak Ada / Hilang</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- PANEL A: JIKA BUKU FISIK ADA -->
                        <div id="panelBukuAda"
                            class="{{ $isBukuAda ? '' : 'hidden' }} space-y-3.5 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <!-- TOGGLE 2: APAKAH PEROLEHAN COCOK DENGAN BUKU? -->
                            <div>
                                <label class="block font-black text-zinc-800 dark:text-zinc-200 mb-2">
                                    2. Apakah perolehan tabungan sudah cocok dengan yang di buku?
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status_verifikasi" value="Cocok"
                                            id="statusCocokRadio" class="peer sr-only"
                                            {{ $tabungan->status_verifikasi === 'Cocok' || $tabungan->status_verifikasi === 'Belum Diverifikasi' ? 'checked' : '' }}
                                            onchange="handleStatusCocokToggle('Cocok')">
                                        <div
                                            class="p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 peer-checked:text-emerald-700 dark:peer-checked:text-emerald-300 text-center font-bold transition-all flex items-center justify-center gap-1.5">
                                            <i class="bi bi-check-circle-fill text-emerald-500"></i>
                                            <span>Sudah Cocok</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status_verifikasi" value="Selisih"
                                            id="statusSelisihRadio" class="peer sr-only"
                                            {{ $tabungan->status_verifikasi === 'Selisih' ? 'checked' : '' }}
                                            onchange="handleStatusCocokToggle('Selisih')">
                                        <div
                                            class="p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 peer-checked:border-amber-500 peer-checked:bg-amber-500/10 peer-checked:text-amber-700 dark:peer-checked:text-amber-300 text-center font-bold transition-all flex items-center justify-center gap-1.5">
                                            <i class="bi bi-exclamation-triangle-fill text-amber-500"></i>
                                            <span>Ada Selisih</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- INPUT SALDO RIIL JIKA ADA SELISIH -->
                            <div id="saldoFisikGroup"
                                class="{{ $tabungan->status_verifikasi === 'Selisih' ? '' : 'hidden' }}">
                                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">
                                    Saldo Riil di Buku Fisik (Rp)
                                </label>
                                <input type="number" step="0.01" min="0" name="saldo_buku_fisik"
                                    id="saldoBukuFisikInput"
                                    value="{{ $tabungan->saldo_buku_fisik ?? $tabungan->saldo }}"
                                    placeholder="Masukkan nominal saldo di buku..."
                                    class="m3-input-glass w-full text-xs font-mono font-bold"
                                    oninput="updateSelisihPreview(this.value)">
                                <div id="selisihPreview" class="text-[11px] font-bold text-rose-500 mt-1"></div>
                            </div>
                        </div>

                        <!-- PANEL B: JIKA BUKU TIDAK ADA / HILANG -->
                        <div id="panelBukuTidakAda"
                            class="{{ !$isBukuAda ? '' : 'hidden' }} space-y-3 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                            <!-- Input hidden untuk status verifikasi jika buku tidak ada -->
                            <input type="radio" name="status_verifikasi" value="Buku Tidak Ada"
                                id="statusBukuTidakAdaRadio" class="sr-only" {{ !$isBukuAda ? 'checked' : '' }}>

                            <div
                                class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 space-y-2">
                                <div class="flex items-center gap-2 font-black text-xs">
                                    <i class="bi bi-exclamation-octagon-fill text-amber-600 text-base"></i>
                                    <span>Buku Fisik Tidak Ada / Hilang</span>
                                </div>
                                <p class="text-[11px] leading-relaxed text-zinc-600 dark:text-zinc-300">
                                    Cetak <strong>Lembar Mutasi Ukuran A6</strong> berikut sebagai bukti resmi mutasi
                                    bulanan, potongan madrasah, dan tanda terima hak bersih yang sah bagi wali
                                    murid/nasabah.
                                </p>
                                <a href="{{ route('tabungan.cek-mutasi.cetak-a6', $tabungan->id) }}" target="_blank"
                                    class="w-full m3-btn-primary !bg-purple-600 hover:!bg-purple-700 py-2.5 text-xs flex items-center justify-center gap-1.5 shadow-md font-bold mt-2">
                                    <i class="bi bi-printer-fill text-sm"></i>
                                    <span>🖨️ Cetak Lembar Mutasi Ukuran A6</span>
                                </a>
                            </div>
                        </div>

                        <!-- CATATAN PETUGAS -->
                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">
                                Catatan Petugas (Opsional)
                            </label>
                            <textarea name="catatan_verifikasi" id="catatanVerifikasiInput" rows="2"
                                placeholder="Misal: Buku cocok / Dicetak lembar A6 karena buku hilang..." class="m3-input-glass w-full text-xs">{{ $tabungan->catatan_verifikasi }}</textarea>
                        </div>

                        <!-- CHECKBOX ESTAFET -->
                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" name="lanjut_berikutnya" id="lanjutBerikutnya" value="1"
                                checked class="rounded text-primary focus:ring-primary h-4 w-4">
                            <label for="lanjutBerikutnya"
                                class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400 cursor-pointer">
                                Simpan & Lanjut ke Buku Berikutnya
                            </label>
                        </div>

                        <button type="submit" id="btnSubmitVerifikasi"
                            class="w-full m3-btn-primary !bg-emerald-600 hover:!bg-emerald-700 py-2.5 text-xs flex items-center justify-center gap-1.5 shadow-sm font-bold">
                            <i class="bi bi-shield-check text-sm font-bold"></i>
                            <span>Simpan Status Verifikasi</span>
                        </button>
                    </form>
                </div>

                <!-- Navigasi Cepat Estafet Buku & Pilihan Cetak -->
                <div class="flex items-center justify-between gap-2">
                    @if ($adjacent['prev'])
                        <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $adjacent['prev']->nomor_rekening]) }}"
                            class="flex-1 p-2.5 rounded-xl m3-glass-card hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-center transition-all">
                            <div class="text-[9px] font-bold text-zinc-400 uppercase">⬅️ Sebelumnya</div>
                            <div class="text-xs font-mono font-black text-zinc-800 dark:text-zinc-200 truncate">
                                {{ $adjacent['prev']->nomor_rekening }}
                            </div>
                        </a>
                    @else
                        <div
                            class="flex-1 p-2.5 rounded-xl m3-glass-card opacity-40 text-center text-[10px] font-bold text-zinc-400">
                            Awal Daftar
                        </div>
                    @endif

                    <!-- Tombol Cetak A4 Standar -->
                    <a href="{{ route('tabungan.cek-mutasi.cetak', $tabungan->id) }}" target="_blank"
                        class="p-2.5 rounded-xl bg-zinc-200/60 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-bold flex items-center justify-center px-3"
                        title="Cetak Rekap Mutasi Lembar A4 Lengkap">
                        <i class="bi bi-file-earmark-pdf-fill text-sm"></i>
                        <span class="hidden md:inline ml-1 text-[10px]">A4</span>
                    </a>

                    <!-- Tombol Cetak A6 Slip Pengganti -->
                    <a href="{{ route('tabungan.cek-mutasi.cetak-a6', $tabungan->id) }}" target="_blank"
                        class="p-2.5 rounded-xl bg-purple-600/10 hover:bg-purple-600/20 text-purple-600 dark:text-purple-400 border border-purple-500/20 text-xs font-bold flex items-center justify-center px-3"
                        title="Cetak Lembar Mutasi Ukuran A6 (Pengganti Buku)">
                        <i class="bi bi-printer-fill text-sm"></i>
                        <span class="hidden md:inline ml-1 text-[10px]">A6</span>
                    </a>

                    @if ($adjacent['next'])
                        <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $adjacent['next']->nomor_rekening]) }}"
                            class="flex-1 p-2.5 rounded-xl m3-glass-card hover:bg-zinc-200/50 dark:hover:bg-zinc-800 text-center transition-all">
                            <div class="text-[9px] font-bold text-zinc-400 uppercase">Berikutnya ➡️</div>
                            <div class="text-xs font-mono font-black text-zinc-800 dark:text-zinc-200 truncate">
                                {{ $adjacent['next']->nomor_rekening }}
                            </div>
                        </a>
                    @else
                        <div
                            class="flex-1 p-2.5 rounded-xl m3-glass-card opacity-40 text-center text-[10px] font-bold text-zinc-400">
                            Akhir Daftar
                        </div>
                    @endif
                </div>

            </div>

            <!-- KOLOM KANAN: REKAPITULASI BULANAN & HISTORI MUTASI LENGKAP (2 Cols) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- TABEL 1: REKAPITULASI MUTASI PER BULAN MASEHI -->
                <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-xs">
                    <div
                        class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <h4
                                class="font-black text-xs md:text-sm text-zinc-900 dark:text-white uppercase tracking-wider">
                                📅 Rekapitulasi Mutasi Per Bulan Masehi
                            </h4>
                        </div>
                        <span class="text-[11px] font-bold text-zinc-500">
                            Total: {{ count($mutasi['rekap_bulanan']) }} Periode Bulan
                        </span>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 bg-zinc-50/50 dark:bg-zinc-900/30">
                                    <th class="py-3 px-3.5">Bulan & Tahun</th>
                                    <th class="py-3 px-3 text-right">Setoran (Freq)</th>
                                    <th class="py-3 px-3 text-right">Penarikan (Freq)</th>
                                    <th class="py-3 px-3 text-right">Net Bulanan</th>
                                    <th class="py-3 px-3 text-right">Saldo Akhir</th>
                                    <th class="py-3 px-3 text-center w-20">Filter</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium">
                                @forelse ($mutasi['rekap_bulanan'] as $b)
                                    @php $isActive = ($filterBulan === $b['key']); @endphp
                                    <tr
                                        class="hover:bg-zinc-500/5 transition-colors {{ $isActive ? 'bg-primary/5 dark:bg-primary/10 font-bold' : '' }}">
                                        <td
                                            class="py-3 px-3.5 font-bold text-zinc-900 dark:text-white flex items-center gap-1.5">
                                            <i
                                                class="bi bi-calendar3 {{ $isActive ? 'text-primary font-black' : 'text-blue-500' }} text-xs"></i>
                                            <span>{{ $b['label'] }}</span>
                                            @if ($isActive)
                                                <span
                                                    class="px-1.5 py-0.2 rounded bg-primary text-white text-[9px] font-bold">Aktif</span>
                                            @endif
                                        </td>
                                        <td
                                            class="py-3 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            Rp {{ number_format($b['total_setor'], 0, ',', '.') }}
                                            <span
                                                class="text-[10px] text-zinc-400 font-normal">({{ $b['frekuensi_setor'] }}x)</span>
                                        </td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-rose-500">
                                            Rp {{ number_format($b['total_tarik'], 0, ',', '.') }}
                                            <span
                                                class="text-[10px] text-zinc-400 font-normal">({{ $b['frekuensi_tarik'] }}x)</span>
                                        </td>
                                        <td
                                            class="py-3 px-3 text-right font-mono font-bold {{ $b['net_mutasi'] >= 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                            {{ $b['net_mutasi'] >= 0 ? '+' : '' }}Rp
                                            {{ number_format($b['net_mutasi'], 0, ',', '.') }}
                                        </td>
                                        <td
                                            class="py-3 px-3 text-right font-mono font-black text-zinc-900 dark:text-white">
                                            Rp {{ number_format($b['saldo_akhir'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            @if ($isActive)
                                                <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $tabungan->nomor_rekening]) }}"
                                                    class="p-1 px-2 rounded-lg bg-zinc-200/80 dark:bg-zinc-800 hover:bg-zinc-300 text-zinc-700 dark:text-zinc-300 text-[10px] font-bold inline-flex items-center gap-1"
                                                    title="Tampilkan Semua Bulan">
                                                    <i class="bi bi-x"></i> Reset
                                                </a>
                                            @else
                                                <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $tabungan->nomor_rekening, 'bulan' => $b['key']]) }}"
                                                    class="p-1 px-2 rounded-lg bg-primary/10 hover:bg-primary/20 text-primary dark:text-primary-dark text-[10px] font-bold inline-flex items-center gap-1"
                                                    title="Saring Transaksi Bulan Ini">
                                                    <i class="bi bi-funnel"></i> Pilih
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-zinc-400 text-xs">
                                            Belum ada catatan mutasi transaksi pada rekening ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr
                                    class="bg-zinc-50 dark:bg-zinc-900 font-black border-t-2 border-zinc-200 dark:border-zinc-800 text-xs text-zinc-900 dark:text-white">
                                    <td class="py-3 px-3.5 uppercase tracking-wider text-zinc-500">
                                        TOTAL KESELURUHAN:
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($mutasi['total_setoran'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-rose-500">
                                        Rp {{ number_format($mutasi['total_penarikan'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-zinc-500">
                                        {{ $mutasi['total_transaksi'] }} Transaksi
                                    </td>
                                    <td
                                        class="py-3 px-3 text-right font-mono font-black text-sm text-primary dark:text-primary-dark">
                                        Rp {{ number_format($mutasi['saldo_terakhir'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @if ($filterBulan)
                                            <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $tabungan->nomor_rekening]) }}"
                                                class="text-[10px] text-primary hover:underline font-bold">
                                                Semua
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TABEL 2: BUKU TABUNGAN / LEDGER TRANSAKSI KRONOLOGIS -->
                <div id="tabel-histori-mutasi"
                    class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-xs">
                    <div
                        class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <h4
                                class="font-black text-xs md:text-sm text-zinc-900 dark:text-white uppercase tracking-wider">
                                📖 Histori Lengkap Transaksi Buku Tabungan
                            </h4>
                        </div>

                        <!-- Filter Bulan Dropdown -->
                        <div class="flex items-center gap-2">
                            <label for="selectFilterBulan"
                                class="text-[10px] font-black uppercase text-zinc-400 whitespace-nowrap">
                                <i class="bi bi-funnel-fill text-primary"></i> Filter Bulan:
                            </label>
                            <select id="selectFilterBulan" onchange="filterMutasiByMonth(this.value)"
                                class="m3-input-glass py-1.5 px-3 text-xs font-bold cursor-pointer">
                                <option value="">Semua Bulan ({{ $mutasi['total_transaksi'] }} Trx)</option>
                                @foreach ($mutasi['rekap_bulanan'] as $b)
                                    <option value="{{ $b['key'] }}"
                                        {{ $filterBulan === $b['key'] ? 'selected' : '' }}>
                                        {{ $b['label'] }} ({{ $b['frekuensi_setor'] + $b['frekuensi_tarik'] }} Trx)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Banner Info Filter Aktif -->
                    @if ($filterBulan)
                        @php
                            $activeMonthInfo = collect($mutasi['rekap_bulanan'])->firstWhere('key', $filterBulan);
                        @endphp
                        <div
                            class="px-4 py-2.5 bg-blue-500/10 border-b border-blue-500/20 text-blue-700 dark:text-blue-300 text-xs font-bold flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <i class="bi bi-info-circle-fill text-blue-500"></i>
                                Menampilkan mutasi transaksi bulan:
                                <strong>{{ $activeMonthInfo['label'] ?? $filterBulan }}</strong>
                                ({{ $mutasi['total_transaksi_filtered'] }} catatan)
                            </span>
                            <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $tabungan->nomor_rekening]) }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-600/10 hover:bg-blue-600/20 text-blue-700 dark:text-blue-300 text-[11px] font-black transition-colors">
                                <i class="bi bi-arrow-counterclockwise"></i> Tampilkan Semua
                            </a>
                        </div>
                    @endif

                    <div class="overflow-x-auto custom-scrollbar max-h-[500px]">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="sticky top-0 z-10">
                                <tr
                                    class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-900">
                                    <th class="py-3 px-3 text-center w-10">No</th>
                                    <th class="py-3 px-3">Tanggal & Kode</th>
                                    <th class="py-3 px-3">Uraian / Kategori</th>
                                    <th class="py-3 px-3 text-right">Masuk (Rp)</th>
                                    <th class="py-3 px-3 text-right">Keluar (Rp)</th>
                                    <th class="py-3 px-3 text-right">Saldo Berjalan</th>
                                    <th class="py-3 px-3">Petugas</th>
                                    <th class="py-3 px-3 text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium">
                                @forelse ($mutasi['daftar_transaksi'] as $t)
                                    <tr class="hover:bg-zinc-500/5 transition-colors">
                                        <td class="py-2.5 px-3 text-center font-bold text-zinc-400">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <div class="font-bold text-zinc-900 dark:text-white">
                                                {{ $t['tanggal_formatted'] }}
                                            </div>
                                            <div class="text-[10px] font-mono text-zinc-400">
                                                {{ $t['kode_transaksi'] }}
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <div class="font-semibold text-zinc-800 dark:text-zinc-200">
                                                {{ $t['kategori'] }}
                                            </div>
                                            @if ($t['keterangan'])
                                                <div class="text-[10px] text-zinc-400 italic">
                                                    {{ $t['keterangan'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td
                                            class="py-2.5 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $t['masuk'] > 0 ? 'Rp ' . number_format($t['masuk'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-rose-500">
                                            {{ $t['keluar'] > 0 ? 'Rp ' . number_format($t['keluar'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td
                                            class="py-2.5 px-3 text-right font-mono font-black text-zinc-900 dark:text-white">
                                            Rp {{ number_format($t['saldo_akhir'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 px-3 text-[11px] text-zinc-500">
                                            {{ $t['petugas'] }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            @if ($t['jenis_transaksi'] === 'Setor')
                                                <div class="flex items-center justify-center gap-1">
                                                    <!-- Tombol Edit Setor -->
                                                    <a href="{{ route('tabungan.setor.edit', $t['id']) }}"
                                                        class="action-modal p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 transition-colors inline-flex items-center justify-center cursor-pointer"
                                                        title="Edit Setoran">
                                                        <i class="bi bi-pencil-square text-xs"></i>
                                                    </a>
                                                    <!-- Tombol Hapus Setor -->
                                                    <form action="{{ route('tabungan.setor.destroy', $t['id']) }}"
                                                        method="POST" class="delete-ajax inline m-0 p-0"
                                                        data-refresh-target="#tabel-histori-mutasi">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 transition-colors cursor-pointer inline-flex items-center justify-center"
                                                            title="Hapus Setoran"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi setoran ini? Saldo tabungan akan disesuaikan secara otomatis.')">
                                                            <i class="bi bi-trash3 text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif ($t['jenis_transaksi'] === 'Tarik')
                                                <div class="flex items-center justify-center gap-1">
                                                    <!-- Tombol Edit Tarik -->
                                                    <a href="{{ route('tabungan.tarik.edit', $t['id']) }}"
                                                        class="action-modal p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 transition-colors inline-flex items-center justify-center cursor-pointer"
                                                        title="Edit Penarikan">
                                                        <i class="bi bi-pencil-square text-xs"></i>
                                                    </a>
                                                    <!-- Tombol Hapus Tarik -->
                                                    <form action="{{ route('tabungan.tarik.destroy', $t['id']) }}"
                                                        method="POST" class="delete-ajax inline m-0 p-0"
                                                        data-refresh-target="#tabel-histori-mutasi">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 transition-colors cursor-pointer inline-flex items-center justify-center"
                                                            title="Hapus Penarikan"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi penarikan ini? Saldo tabungan akan dikembalikan secara otomatis.')">
                                                            <i class="bi bi-trash3 text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span
                                                    class="text-[9px] font-bold text-zinc-400 px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800">
                                                    Tutup Buku
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-zinc-400 text-xs">
                                            Belum ada riwayat transaksi pada filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    @else
        <!-- TAMPILAN AWAL SEBELUM SCAN / PENCARIAN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="m3-glass-card rounded-2xl md:rounded-3xl p-8 md:p-12 text-center shadow-xs">
                    <div
                        class="w-16 h-16 rounded-3xl bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center mx-auto mb-4 text-2xl border border-primary/20">
                        <i class="bi bi-upc-scan"></i>
                    </div>
                    <h3 class="text-lg font-black text-zinc-900 dark:text-white mb-2">
                        Siap Memverifikasi Buku Tabungan
                    </h3>
                    <p
                        class="text-xs md:text-sm text-zinc-500 dark:text-zinc-400 max-w-md mx-auto leading-relaxed mb-6">
                        Silakan scan barcode buku fisik tabungan murid atau masukkan nomor rekening pada form di atas
                        untuk memeriksa rincian mutasi bulanan dan menandai status verifikasi buku.
                    </p>

                    <div
                        class="inline-flex items-center gap-3 p-3 rounded-2xl bg-zinc-100 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-xs text-zinc-600 dark:text-zinc-400">
                        <span class="font-bold flex items-center gap-1.5 text-zinc-900 dark:text-white">
                            <i class="bi bi-lightning-charge-fill text-amber-500"></i> Alur Cepat:
                        </span>
                        <span>Scan Barcode</span>
                        <span>➡️</span>
                        <span>Cek Mutasi Bulanan</span>
                        <span>➡️</span>
                        <span>Klik Verifikasi & Siap Kembali</span>
                    </div>
                </div>
            </div>

            <!-- WIDGET KANAN: REKENING TERAKHIR DIVERIFIKASI -->
            <div class="lg:col-span-1 space-y-4">
                <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl shadow-xs">
                    <div class="flex items-center gap-2 mb-3.5">
                        <i class="bi bi-clock-history text-primary"></i>
                        <h4 class="text-xs font-black uppercase tracking-wider text-zinc-900 dark:text-white">
                            Riwayat Verifikasi Terkini
                        </h4>
                    </div>

                    @if (count($riwayatVerifikasiTerakhir) > 0)
                        <div class="space-y-2.5 divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($riwayatVerifikasiTerakhir as $rv)
                                <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $rv->nomor_rekening]) }}"
                                    class="block pt-2 hover:bg-zinc-500/5 p-2 rounded-xl transition-colors">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-zinc-900 dark:text-white truncate">
                                            {{ $rv->nama_nasabah }}
                                        </span>
                                        @if ($rv->status_verifikasi === 'Cocok')
                                            <span
                                                class="text-[9px] font-black px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600">Cocok</span>
                                        @else
                                            <span
                                                class="text-[9px] font-black px-1.5 py-0.5 rounded bg-rose-500/10 text-rose-600">Selisih</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] text-zinc-400 mt-1">
                                        <span class="font-mono">{{ $rv->nomor_rekening }}</span>
                                        <span>{{ $rv->diverifikasi_pada ? $rv->diverifikasi_pada->diffForHumans() : '' }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-zinc-400 py-4 text-center">
                            Belum ada rekening yang diverifikasi hari ini.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            const currentSaldoSistem = {{ $tabungan ? (float) $tabungan->saldo : 0 }};

            function handleBukuAdaToggle(isAda) {
                const panelAda = document.getElementById('panelBukuAda');
                const panelTidakAda = document.getElementById('panelBukuTidakAda');
                const statusBukuTidakAdaRadio = document.getElementById('statusBukuTidakAdaRadio');
                const statusCocokRadio = document.getElementById('statusCocokRadio');
                const statusSelisihRadio = document.getElementById('statusSelisihRadio');
                const catatanInput = document.getElementById('catatanVerifikasiInput');
                const btnSubmit = document.getElementById('btnSubmitVerifikasi');

                if (isAda) {
                    if (panelAda) panelAda.classList.remove('hidden');
                    if (panelTidakAda) panelTidakAda.classList.add('hidden');
                    if (statusBukuTidakAdaRadio) statusBukuTidakAdaRadio.checked = false;
                    if (statusCocokRadio && !statusSelisihRadio?.checked) {
                        statusCocokRadio.checked = true;
                    }
                    if (btnSubmit) {
                        btnSubmit.className =
                            'w-full m3-btn-primary !bg-emerald-600 hover:!bg-emerald-700 py-2.5 text-xs flex items-center justify-center gap-1.5 shadow-sm font-bold';
                        btnSubmit.innerHTML =
                            '<i class="bi bi-shield-check text-sm font-bold"></i><span>Simpan Status Verifikasi</span>';
                    }
                } else {
                    if (panelAda) panelAda.classList.add('hidden');
                    if (panelTidakAda) panelTidakAda.classList.remove('hidden');
                    if (statusBukuTidakAdaRadio) statusBukuTidakAdaRadio.checked = true;
                    if (statusCocokRadio) statusCocokRadio.checked = false;
                    if (statusSelisihRadio) statusSelisihRadio.checked = false;
                    if (catatanInput && !catatanInput.value) {
                        catatanInput.value = 'Buku tabungan fisik tidak ada / hilang (dicetak lembar mutasi A6)';
                    }
                    if (btnSubmit) {
                        btnSubmit.className =
                            'w-full m3-btn-primary !bg-purple-600 hover:!bg-purple-700 py-2.5 text-xs flex items-center justify-center gap-1.5 shadow-sm font-bold';
                        btnSubmit.innerHTML =
                            '<i class="bi bi-journal-x text-sm font-bold"></i><span>Simpan Status (Buku Tidak Ada)</span>';
                    }
                }
            }

            function handleStatusCocokToggle(status) {
                const group = document.getElementById('saldoFisikGroup');
                const input = document.getElementById('saldoBukuFisikInput');
                if (status === 'Selisih') {
                    if (group) group.classList.remove('hidden');
                    if (input) updateSelisihPreview(input.value);
                } else {
                    if (group) group.classList.add('hidden');
                    const preview = document.getElementById('selisihPreview');
                    if (preview) preview.innerHTML = '';
                }
            }

            function updateSelisihPreview(val) {
                const preview = document.getElementById('selisihPreview');
                if (!preview) return;
                const nominal = parseFloat(val) || 0;
                const selisih = nominal - currentSaldoSistem;
                const formatted = new Intl.NumberFormat('id-ID').format(Math.abs(selisih));
                if (selisih === 0) {
                    preview.className = 'text-[11px] font-bold text-emerald-600 mt-1';
                    preview.innerHTML = '✓ Saldo fisik sama dengan saldo sistem.';
                } else if (selisih > 0) {
                    preview.className = 'text-[11px] font-bold text-blue-600 mt-1';
                    preview.innerHTML = 'Selisih: Saldo buku LEBIH BESAR Rp ' + formatted + ' dari sistem.';
                } else {
                    preview.className = 'text-[11px] font-bold text-rose-500 mt-1';
                    preview.innerHTML = 'Selisih: Saldo buku LEBIH KECIL Rp ' + formatted + ' dari sistem.';
                }
            }

            function filterMutasiByMonth(val) {
                const url = new URL(window.location.href);
                if (val && val !== '') {
                    url.searchParams.set('bulan', val);
                } else {
                    url.searchParams.delete('bulan');
                }
                window.location.href = url.toString();
            }
        </script>
    @endpush

</x-app-layout>
