@section('title', 'Pembagian Akhir & Verifikasi Tabungan')
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
                <span class="text-xs font-bold text-zinc-500 dark:text-zinc-400">Pembagian & Verifikasi Akhir</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Pembagian Akhir & Verifikasi Tabungan
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Verifikasi pencocokan saldo buku fisik dan aplikasi selama masa penutupan, simulasi bagi hasil, dan
                eksekusi tutup buku.
            </p>
        </div>

        <!-- Tombol Aksi Utama -->
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Cek Mutasi Bulanan -->
            <a href="{{ route('tabungan.cek-mutasi.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-primary/10 hover:bg-primary/20 text-primary dark:text-primary-dark border border-primary/25 font-bold rounded-xl md:rounded-2xl min-h-[38px] px-3.5 py-2 text-xs transition-colors">
                <i class="bi bi-upc-scan text-sm"></i>
                <span>Cek Mutasi per Buku</span>
            </a>

            @if ($simulasi && $simulasi['total_rekening'] > 0)
                <!-- Cetak Laporan -->
                <a href="{{ route('tabungan.pembagian.cetak', request()->all()) }}" target="_blank"
                    class="inline-flex items-center justify-center gap-1.5 bg-zinc-200/70 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-300 dark:hover:bg-zinc-700 font-bold rounded-xl md:rounded-2xl min-h-[38px] px-3.5 py-2 text-xs transition-colors">
                    <i class="bi bi-printer-fill text-sm"></i>
                    <span>Cetak Laporan</span>
                </a>

                <!-- Verifikasi Massal (Cocokkan Semua) -->
                @if ($simulasi['total_belum_diverifikasi'] > 0 || $simulasi['total_selisih'] > 0)
                    <button type="button" onclick="openModalVerifikasiSemua()"
                        class="inline-flex items-center justify-center gap-1.5 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-700 dark:text-emerald-400 border border-emerald-600/25 font-bold rounded-xl md:rounded-2xl min-h-[38px] px-3.5 py-2 text-xs transition-all active:scale-95 cursor-pointer">
                        <i class="bi bi-check2-all text-sm font-extrabold"></i>
                        <span>Verifikasi Semua Cocok</span>
                    </button>
                @endif

                <!-- Eksekusi Pembagian Massal -->
                <button type="button" onclick="openModalEksekusi()"
                    class="inline-flex items-center justify-center gap-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl md:rounded-2xl min-h-[38px] px-4 py-2 text-xs shadow-sm active:scale-95 transition-all cursor-pointer">
                    <i class="bi bi-gift-fill text-sm"></i>
                    <span>Eksekusi Pembagian</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if (session('success'))
        <div
            class="mb-5 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-base shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div
            class="mb-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs font-bold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-base shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- FILTER BAR INTERAKTIF -->
    <div class="m3-glass-card p-4 md:p-5 rounded-2xl md:rounded-3xl mb-6 shadow-xs">
        <form action="{{ route('tabungan.pembagian.index') }}" method="GET" id="filterForm"
            class="flex flex-col md:flex-row md:items-center justify-between gap-4">

            <div class="flex flex-wrap items-center gap-2.5 flex-1">
                <!-- 1. Filter Periode -->
                <div class="min-w-[160px]">
                    <label class="block text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 mb-1">
                        Periode Tabungan
                    </label>
                    <select name="periode_id" onchange="this.form.submit()"
                        class="m3-input-glass text-xs font-bold py-2 px-3 w-full cursor-pointer">
                        @foreach ($periodes as $per)
                            <option value="{{ $per->id }}" {{ $periodeId == $per->id ? 'selected' : '' }}>
                                {{ $per->nama_periode }} {{ $per->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Filter Kategori / Jenis Nasabah -->
                <div class="min-w-[150px]">
                    <label class="block text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 mb-1">
                        Kategori Nasabah
                    </label>
                    <select name="jenis_nasabah" onchange="this.form.submit()"
                        class="m3-input-glass text-xs font-bold py-2 px-3 w-full cursor-pointer">
                        <option value="">Semua Kategori Nasabah</option>
                        <option value="Murid" {{ $jenisNasabah === 'Murid' ? 'selected' : '' }}>👨‍🎓 Murid / Murid
                        </option>
                        <option value="Ustadz" {{ $jenisNasabah === 'Ustadz' ? 'selected' : '' }}>👨‍🏫 Ustadz / Guru
                        </option>
                        <option value="Kas Ruangan" {{ $jenisNasabah === 'Kas Ruangan' ? 'selected' : '' }}>🏫 Kas
                            Ruangan / Kelas</option>
                        <option value="Umum" {{ $jenisNasabah === 'Umum' ? 'selected' : '' }}>👥 Nasabah Umum
                        </option>
                    </select>
                </div>

                <!-- 3. Filter Ruangan / Kelas (Hanya muncul jika jenis_nasabah adalah Murid atau Semua) -->
                @if (!$jenisNasabah || $jenisNasabah === 'Murid')
                    <div class="min-w-[150px]">
                        <label class="block text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 mb-1">
                            Kelas / Ruangan Murid
                        </label>
                        <select name="ruangan_id" onchange="this.form.submit()"
                            class="m3-input-glass text-xs font-bold py-2 px-3 w-full cursor-pointer">
                            <option value="">Semua Kelas / Ruangan</option>
                            @foreach ($daftarRuangan as $r)
                                <option value="{{ $r->id }}" {{ $ruanganId == $r->id ? 'selected' : '' }}>
                                    Kelas: {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- 4. Filter Status Verifikasi -->
                <div class="min-w-[140px]">
                    <label class="block text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 mb-1">
                        Status Verifikasi
                    </label>
                    <select name="status_verifikasi" onchange="this.form.submit()"
                        class="m3-input-glass text-xs font-bold py-2 px-3 w-full cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Belum Diverifikasi"
                            {{ $statusVerifikasi === 'Belum Diverifikasi' ? 'selected' : '' }}>⏳ Belum Diverifikasi
                        </option>
                        <option value="Cocok" {{ $statusVerifikasi === 'Cocok' ? 'selected' : '' }}>✅ Sudah Cocok
                        </option>
                        <option value="Selisih" {{ $statusVerifikasi === 'Selisih' ? 'selected' : '' }}>⚠️ Ada Selisih
                        </option>
                    </select>
                </div>
            </div>

            <!-- Reset Filter -->
            @if ($jenisNasabah || $ruanganId || $statusVerifikasi)
                <div class="self-end">
                    <a href="{{ route('tabungan.pembagian.index', ['periode_id' => $periodeId]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-zinc-200/60 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-xs font-bold transition-colors">
                        <i class="bi bi-arrow-counterclockwise text-xs"></i>
                        <span>Reset Filter</span>
                    </a>
                </div>
            @endif
        </form>
    </div>

    @if ($simulasi)
        <!-- SUMMARY CARD SIMULASI PEMBAGIAN -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: Total Rekening & Status Verifikasi -->
            <div
                class="m3-glass-card rounded-2xl md:rounded-3xl p-4 md:p-5 flex flex-col justify-between shadow-2xs border-l-4 border-l-purple-500">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">
                        Rekening Berisi Saldo
                    </span>
                    <div
                        class="w-9 h-9 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-base shrink-0 border border-purple-500/20">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                        {{ $simulasi['total_rekening'] }} <span class="text-xs font-bold text-zinc-400">Rekening</span>
                    </h3>
                    <div class="flex items-center gap-1.5 mt-2 text-[10px] font-extrabold flex-wrap">
                        <span class="text-emerald-600 dark:text-emerald-400">✅ {{ $simulasi['total_diverifikasi'] }}
                            Cocok</span>
                        <span class="text-zinc-400">•</span>
                        <span class="text-amber-600 dark:text-amber-400">⏳ {{ $simulasi['total_belum_diverifikasi'] }}
                            Belum</span>
                        @if ($simulasi['total_selisih'] > 0)
                            <span class="text-zinc-400">•</span>
                            <span class="text-rose-500">⚠️ {{ $simulasi['total_selisih'] }} Selisih</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Tabungan Kotor -->
            <div
                class="m3-glass-card rounded-2xl md:rounded-3xl p-4 md:p-5 flex flex-col justify-between shadow-2xs border-l-4 border-l-blue-500">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">
                        Total Tabungan Tersimpan
                    </span>
                    <div
                        class="w-9 h-9 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base shrink-0 border border-blue-500/20">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl md:text-2xl font-black text-blue-600 dark:text-blue-400 tracking-tight">
                        Rp {{ number_format($simulasi['total_saldo_kotor'], 0, ',', '.') }}
                    </h3>
                    <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 mt-1">
                        Akumulasi saldo kotor di aplikasi
                    </p>
                </div>
            </div>

            <!-- Card 3: Total Potongan Madrasah (Infaq/Administrasi) -->
            <div
                class="m3-glass-card rounded-2xl md:rounded-3xl p-4 md:p-5 flex flex-col justify-between shadow-2xs border-l-4 border-l-amber-500">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">
                        Total Potongan Madrasah
                    </span>
                    <div
                        class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-base shrink-0 border border-amber-500/20">
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl md:text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight">
                        Rp {{ number_format($simulasi['total_potongan'], 0, ',', '.') }}
                    </h3>
                    <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 mt-1">
                        Dibulatkan ke kelipatan Rp 100
                    </p>
                </div>
            </div>

            <!-- Card 4: Total Bersih Diserahkan -->
            <div
                class="m3-glass-card rounded-2xl md:rounded-3xl p-4 md:p-5 flex flex-col justify-between shadow-2xs border-l-4 border-l-emerald-500">
                <div class="flex justify-between items-start mb-2">
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                        Total Bersih Diserahkan
                    </span>
                    <div
                        class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base shrink-0 border border-emerald-500/20">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                        Rp {{ number_format($simulasi['total_saldo_bersih'], 0, ',', '.') }}
                    </h3>
                    <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 mt-1">
                        Hak bersih siap dibagikan ke nasabah
                    </p>
                </div>
            </div>
        </div>

        @if (!empty($simulasi['rincian']) && count($simulasi['rincian']) > 0)
            <!-- TABEL RINCIAN SIMULASI & VERIFIKASI BUKU FISIK -->
            <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-xs mb-8">
                <div
                    class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <h4
                            class="font-black text-xs md:text-sm text-zinc-900 dark:text-white uppercase tracking-wider">
                            Rincian Pembagian & Verifikasi Buku Fisik ({{ $simulasi['periode']->nama_periode }})
                        </h4>
                    </div>
                    <span class="text-xs font-bold text-zinc-500">
                        Menampilkan {{ count($simulasi['rincian']) }} dari {{ $simulasi['total_rekening'] }} Rekening
                    </span>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 bg-zinc-50/50 dark:bg-zinc-900/30">
                                <th class="py-3 px-3 w-10 text-center">No</th>
                                <th class="py-3 px-3">Identitas & Barcode Rekening</th>
                                <th class="py-3 px-3">Kelas / Ruangan</th>
                                <th class="py-3 px-3 text-right">Saldo Aplikasi</th>
                                <th class="py-3 px-3 text-right">Potongan (%)</th>
                                <th class="py-3 px-3 text-right">Hak Bersih</th>
                                <th class="py-3 px-3 text-center">Status Verifikasi Buku Fisik</th>
                                <th class="py-3 px-3 text-center">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium text-zinc-700 dark:text-zinc-300">
                            @foreach ($simulasi['rincian'] as $r)
                                <tr class="hover:bg-zinc-500/5 transition-colors">
                                    <td class="py-3 px-3 text-center font-bold text-zinc-400">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <x-avatar :name="$r['nama_nasabah']" size="sm" />
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    <span
                                                        class="font-extrabold text-zinc-900 dark:text-white text-xs block leading-tight">
                                                        {{ $r['nama_nasabah'] }}
                                                    </span>
                                                    <!-- Badge Jenis Nasabah -->
                                                    @if ($r['jenis_nasabah'] === 'Murid')
                                                        <span
                                                            class="px-1.5 py-0.2 rounded bg-blue-500/10 text-blue-600 dark:text-blue-400 text-[9px] font-bold">Murid</span>
                                                    @elseif ($r['jenis_nasabah'] === 'Ustadz')
                                                        <span
                                                            class="px-1.5 py-0.2 rounded bg-purple-500/10 text-purple-600 dark:text-purple-400 text-[9px] font-bold">Ustadz</span>
                                                    @elseif ($r['jenis_nasabah'] === 'Kas Ruangan')
                                                        <span
                                                            class="px-1.5 py-0.2 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] font-bold">Kas</span>
                                                    @else
                                                        <span
                                                            class="px-1.5 py-0.2 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[9px] font-bold">Umum</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 mt-0.5 text-[10px] text-zinc-400">
                                                    <span>{{ $r['identitas_nasabah'] }}</span>
                                                    <span>•</span>
                                                    <span
                                                        class="font-mono font-bold text-primary dark:text-primary-dark">{{ $r['nomor_rekening'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-zinc-600 dark:text-zinc-400 text-[11px] font-semibold">
                                        {{ $r['nama_ruangan'] }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-bold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($r['saldo_kotor'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-amber-600 dark:text-amber-400">
                                        <span class="font-bold">Rp
                                            {{ number_format($r['nominal_potongan'], 0, ',', '.') }}</span>
                                        <span
                                            class="text-[10px] text-zinc-400 block">({{ $r['persentase_potongan'] }}%)</span>
                                    </td>
                                    <td
                                        class="py-3 px-3 text-right font-mono font-black text-xs text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($r['nominal_bersih'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @if ($r['status_verifikasi'] === 'Cocok')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black border border-emerald-500/20">
                                                <i class="bi bi-check-circle-fill"></i> Sudah Cocok
                                            </span>
                                            @if ($r['diverifikasi_oleh'])
                                                <span class="block text-[9px] text-zinc-400 mt-0.5">Oleh:
                                                    {{ $r['diverifikasi_oleh'] }}</span>
                                            @endif
                                        @elseif ($r['status_verifikasi'] === 'Selisih')
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-black border border-rose-500/20">
                                                <i class="bi bi-exclamation-triangle-fill"></i> Selisih
                                            </span>
                                            @if ($r['saldo_buku_fisik'] !== null)
                                                <span class="block text-[9px] font-bold text-rose-500 mt-0.5">
                                                    Fisik: Rp {{ number_format($r['saldo_buku_fisik'], 0, ',', '.') }}
                                                </span>
                                            @endif
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold border border-amber-500/20">
                                                <i class="bi bi-hourglass-split"></i> Belum Diverifikasi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Tombol Verifikasi Cepat Cocok -->
                                            @if ($r['status_verifikasi'] !== 'Cocok')
                                                <form
                                                    action="{{ route('tabungan.pembagian.verifikasi', $r['tabungan_id']) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status_verifikasi" value="Cocok">
                                                    <input type="hidden" name="saldo_buku_fisik"
                                                        value="{{ $r['saldo_kotor'] }}">
                                                    <button type="submit" title="Tandai Cocok dengan Buku Tabungan"
                                                        class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 flex items-center justify-center text-xs font-bold transition-all active:scale-95 cursor-pointer">
                                                        <i class="bi bi-check-lg text-sm"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Tombol Modal Edit Verifikasi -->
                                            <button type="button"
                                                onclick="openModalVerifikasiSingle({{ $r['tabungan_id'] }}, '{{ addslashes($r['nama_nasabah']) }}', '{{ $r['nomor_rekening'] }}', {{ $r['saldo_kotor'] }}, '{{ $r['status_verifikasi'] }}', {{ $r['saldo_buku_fisik'] ?? 'null' }}, '{{ addslashes($r['catatan_verifikasi'] ?? '') }}')"
                                                title="Input / Koreksi Saldo Buku Fisik"
                                                class="w-7 h-7 rounded-lg bg-zinc-200/60 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 flex items-center justify-center text-xs font-bold transition-all active:scale-95 cursor-pointer">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </button>

                                            <!-- Tombol Cek Mutasi Bulanan Detail -->
                                            <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $r['nomor_rekening']]) }}"
                                                title="Buka Lembar Cek Mutasi & Verifikasi Buku"
                                                class="w-7 h-7 rounded-lg bg-primary/10 text-primary dark:text-primary-dark hover:bg-primary/20 flex items-center justify-center text-xs font-bold transition-all active:scale-95">
                                                <i class="bi bi-journal-text text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr
                                class="bg-zinc-50 dark:bg-zinc-900 font-black border-t-2 border-zinc-200 dark:border-zinc-800 text-xs text-zinc-900 dark:text-white">
                                <td colspan="3"
                                    class="py-3.5 px-4 text-right uppercase tracking-wider text-zinc-500">
                                    TOTAL KESELURUHAN ({{ count($simulasi['rincian']) }} REKENING):
                                </td>
                                <td class="py-3.5 px-3 text-right font-mono text-zinc-900 dark:text-white">
                                    Rp {{ number_format($simulasi['total_saldo_kotor'], 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-mono text-amber-600 dark:text-amber-400">
                                    Rp {{ number_format($simulasi['total_potongan'], 0, ',', '.') }}
                                </td>
                                <td
                                    class="py-3.5 px-3 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                    Rp {{ number_format($simulasi['total_saldo_bersih'], 0, ',', '.') }}
                                </td>
                                <td colspan="2"
                                    class="py-3.5 px-3 text-center text-[10px] font-bold text-zinc-400">
                                    {{ $simulasi['total_diverifikasi'] }} Cocok / {{ $simulasi['total_rekening'] }}
                                    Total
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @else
            <x-empty-state icon="bi-gift" title="Belum Ada Rekening Bersaldo"
                message="Tidak ada rekening tabungan yang memiliki saldo aktif pada filter yang dipilih." />
        @endif

        <!-- MODAL 1: VERIFIKASI SINGLE REKENING -->
        <div id="modalVerifikasiSingle"
            class="fixed inset-0 z-50 hidden bg-black/60 dark:bg-black/80 backdrop-blur-md items-center justify-center p-4">
            <div
                class="m3-glass-card rounded-2xl md:rounded-3xl w-full max-w-md overflow-hidden relative shadow-2xl border border-zinc-200/80 dark:border-zinc-800">
                <div
                    class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-shield-check text-primary dark:text-primary-dark"></i>
                        Verifikasi Buku Tabungan Fisik
                    </h3>
                    <button type="button" onclick="closeModalVerifikasiSingle()"
                        class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </button>
                </div>

                <form id="formVerifikasiSingle" method="POST">
                    @csrf
                    <div class="p-5 md:p-6 space-y-4 text-xs">
                        <!-- Info Rekening -->
                        <div
                            class="p-3 rounded-xl bg-zinc-100 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 space-y-1">
                            <div class="font-extrabold text-zinc-900 dark:text-white text-sm" id="modalVNama"></div>
                            <div class="text-[11px] font-mono text-primary dark:text-primary-dark font-bold"
                                id="modalVNoRek"></div>
                            <div class="text-zinc-500 text-[11px] pt-1">
                                Saldo Tercatat di Aplikasi: <strong class="text-zinc-900 dark:text-white font-mono"
                                    id="modalVSaldoAplikasi"></strong>
                            </div>
                        </div>

                        <!-- Status Verifikasi -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">Hasil Pencocokan Buku
                                Fisik</label>
                            <select name="status_verifikasi" id="modalVStatus"
                                class="m3-input-glass w-full text-xs font-bold" required>
                                <option value="Cocok">✅ Sudah Cocok (Saldo buku = Saldo sistem)</option>
                                <option value="Belum Diverifikasi">⏳ Belum Diverifikasi</option>
                                <option value="Selisih">⚠️ Ada Selisih Fisik Buku</option>
                            </select>
                        </div>

                        <!-- Saldo Buku Fisik -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">Saldo Riil di Buku Tabungan
                                Fisik (Rp)</label>
                            <input type="number" step="0.01" min="0" name="saldo_buku_fisik"
                                id="modalVSaldoFisik" placeholder="Masukkan saldo yang tertera di buku fisik..."
                                class="m3-input-glass w-full text-xs font-mono font-bold">
                            <span class="text-[10px] text-zinc-400">Kosongkan jika sama persis dengan saldo
                                aplikasi.</span>
                        </div>

                        <!-- Catatan Verifikasi -->
                        <div class="space-y-1.5">
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">Catatan Petugas
                                (Opsional)</label>
                            <textarea name="catatan_verifikasi" id="modalVCatatan" rows="2"
                                placeholder="Contoh: Buku fisik paraf wali murid cocok, tidak ada selisih..."
                                class="m3-input-glass w-full text-xs"></textarea>
                        </div>
                    </div>

                    <div
                        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeModalVerifikasiSingle()"
                            class="m3-btn-secondary text-xs">
                            Batal
                        </button>
                        <button type="submit" class="m3-btn-primary text-xs">
                            <i class="bi bi-save-fill text-xs"></i>
                            <span>Simpan Verifikasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: VERIFIKASI SEMUA COCOK -->
        <div id="modalVerifikasiSemua"
            class="fixed inset-0 z-50 hidden bg-black/60 dark:bg-black/80 backdrop-blur-md items-center justify-center p-4">
            <div
                class="m3-glass-card rounded-2xl md:rounded-3xl w-full max-w-md overflow-hidden relative shadow-2xl border border-zinc-200/80 dark:border-zinc-800">
                <div
                    class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-check2-all text-emerald-600 dark:text-emerald-400"></i>
                        Verifikasi Massal Buku Tabungan
                    </h3>
                    <button type="button" onclick="closeModalVerifikasiSemua()"
                        class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </button>
                </div>

                <form action="{{ route('tabungan.pembagian.verifikasi-semua') }}" method="POST">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $periodeId }}">
                    <input type="hidden" name="jenis_nasabah" value="{{ $jenisNasabah }}">
                    <input type="hidden" name="ruangan_id" value="{{ $ruanganId }}">

                    <div class="p-5 md:p-6 space-y-4 text-xs">
                        <div
                            class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 dark:text-emerald-300 leading-relaxed">
                            <p class="font-extrabold mb-1 flex items-center gap-1.5">
                                <i class="bi bi-check-circle-fill"></i>
                                Konfirmasi Verifikasi Cocok Massal
                            </p>
                            <p class="text-[11px]">
                                Anda akan menandai seluruh <strong>{{ $simulasi['total_rekening'] }} rekening</strong>
                                pada filter saat ini sebagai <strong>"Sudah Cocok"</strong> dengan buku tabungan fisik.
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeModalVerifikasiSemua()"
                            class="m3-btn-secondary text-xs">
                            Batal
                        </button>
                        <button type="submit" class="m3-btn-primary !bg-emerald-600 hover:!bg-emerald-700 text-xs">
                            <i class="bi bi-check2-all text-sm"></i>
                            <span>Ya, Tandai Semua Cocok</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 3: EKSEKUSI PEMBAGIAN MASSAL -->
        <div id="modalEksekusi"
            class="fixed inset-0 z-50 hidden bg-black/60 dark:bg-black/80 backdrop-blur-md items-center justify-center p-4">
            <div
                class="m3-glass-card rounded-2xl md:rounded-3xl w-full max-w-md overflow-hidden relative shadow-2xl border border-zinc-200/80 dark:border-zinc-800">
                <div
                    class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-gift-fill text-purple-600 dark:text-purple-400"></i>
                        Konfirmasi Eksekusi Pembagian Tabungan
                    </h3>
                    <button type="button" onclick="closeModalEksekusi()"
                        class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </button>
                </div>

                <form action="{{ route('tabungan.pembagian.eksekusi') }}" method="POST">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $periodeId }}">
                    <input type="hidden" name="jenis_nasabah" value="{{ $jenisNasabah }}">
                    <input type="hidden" name="ruangan_id" value="{{ $ruanganId }}">

                    <div class="p-5 md:p-6 space-y-4 text-xs">
                        <div
                            class="p-3.5 rounded-2xl bg-purple-500/10 border border-purple-500/20 text-purple-800 dark:text-purple-300">
                            <p class="font-black mb-1 flex items-center gap-1.5">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span>Perhatian:</span>
                            </p>
                            <p class="text-[11px] leading-relaxed">Proses ini akan mendebit saldo
                                <strong>{{ $simulasi['total_rekening'] }} rekening
                                    ({{ $jenisNasabah ?: 'Semua Kategori' }})</strong> menjadi Rp 0,
                                mencatat transaksi pembagian akhir dengan potongan yang telah dibulatkan ke kelipatan Rp
                                100, serta menandai status rekening sebagai "Dibagikan".
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">Tanggal Eksekusi
                                Pembagian</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                class="m3-input-glass w-full">
                        </div>

                        <div
                            class="p-3.5 rounded-2xl bg-zinc-100/80 dark:bg-zinc-900/80 border border-zinc-200/80 dark:border-zinc-800 space-y-1.5">
                            <div class="flex justify-between text-zinc-500">
                                <span>Total Tabungan Kotor:</span>
                                <span class="font-mono font-bold text-zinc-900 dark:text-white">Rp
                                    {{ number_format($simulasi['total_saldo_kotor'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-amber-600 dark:text-amber-400">
                                <span>Total Potongan Madrasah:</span>
                                <span class="font-mono font-bold">Rp
                                    {{ number_format($simulasi['total_potongan'], 0, ',', '.') }}</span>
                            </div>
                            <div
                                class="flex justify-between font-black text-zinc-900 dark:text-white pt-1.5 border-t border-zinc-200/80 dark:border-zinc-800">
                                <span>Total Uang Bersih Diserahkan:</span>
                                <span class="font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">Rp
                                    {{ number_format($simulasi['total_saldo_bersih'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeModalEksekusi()" class="m3-btn-secondary text-xs">
                            Batal
                        </button>
                        <button type="submit" class="m3-btn-primary !bg-purple-600 hover:!bg-purple-700 text-xs">
                            <i class="bi bi-gift-fill text-sm"></i>
                            <span>Ya, Eksekusi Pembagian</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
            <script>
                function openModalEksekusi() {
                    const el = document.getElementById('modalEksekusi');
                    if (el) {
                        el.classList.remove('hidden');
                        el.classList.add('flex');
                    }
                }

                function closeModalEksekusi() {
                    const el = document.getElementById('modalEksekusi');
                    if (el) {
                        el.classList.remove('flex');
                        el.classList.add('hidden');
                    }
                }

                function openModalVerifikasiSemua() {
                    const el = document.getElementById('modalVerifikasiSemua');
                    if (el) {
                        el.classList.remove('hidden');
                        el.classList.add('flex');
                    }
                }

                function closeModalVerifikasiSemua() {
                    const el = document.getElementById('modalVerifikasiSemua');
                    if (el) {
                        el.classList.remove('flex');
                        el.classList.add('hidden');
                    }
                }

                function openModalVerifikasiSingle(id, nama, noRek, saldoAplikasi, status, saldoFisik, catatan) {
                    const form = document.getElementById('formVerifikasiSingle');
                    form.action = "{{ url('/tabungan/pembagian/verifikasi') }}/" + id;

                    document.getElementById('modalVNama').innerText = nama;
                    document.getElementById('modalVNoRek').innerText = noRek;
                    document.getElementById('modalVSaldoAplikasi').innerText = 'Rp ' + saldoAplikasi.toLocaleString('id-ID');

                    document.getElementById('modalVStatus').value = status;
                    document.getElementById('modalVSaldoFisik').value = saldoFisik !== null ? saldoFisik : '';
                    document.getElementById('modalVCatatan').value = catatan || '';

                    const el = document.getElementById('modalVerifikasiSingle');
                    if (el) {
                        el.classList.remove('hidden');
                        el.classList.add('flex');
                    }
                }

                function closeModalVerifikasiSingle() {
                    const el = document.getElementById('modalVerifikasiSingle');
                    if (el) {
                        el.classList.remove('flex');
                        el.classList.add('hidden');
                    }
                }
            </script>
        @endpush
    @else
        <x-empty-state icon="bi-calendar-x" title="Pilih Periode Tabungan"
            message="Silakan pilih periode tabungan untuk melihat simulasi pembagian." />
    @endif

</x-app-layout>
