<x-app-layout>
    <x-slot name="title">Rincian & Kalkulator Uang Pecahan Kas Tabungan</x-slot>

    <div class="space-y-6">

        <!-- HEADER UTAMA -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm animate-pulse"></span>
                    <h2 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                        💵 Rincian Uang Pecahan Kas Tabungan
                    </h2>
                </div>
                <p class="text-xs md:text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Kalkulator & rekapitulasi kebutuhan fisik uang pecahan (lembar & koin) untuk pengisian buku tabungan
                    murid per kelas/ruangan.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol Cetak Rekap A4 -->
                <a href="{{ route('tabungan.pecahan.cetak', request()->query()) }}" target="_blank"
                    class="m3-btn-primary !bg-zinc-800 hover:!bg-zinc-900 dark:!bg-zinc-700 dark:hover:!bg-zinc-600 text-xs flex items-center gap-1.5 shadow-sm">
                    <i class="bi bi-printer-fill text-sm"></i>
                    <span>Cetak Rekap Kas (A4)</span>
                </a>

                <!-- Tombol Cetak Slip Massal (Untuk Selipan Buku) -->
                <a href="{{ route('tabungan.pecahan.cetak-slip-massal', request()->query()) }}" target="_blank"
                    class="m3-btn-primary !bg-purple-600 hover:!bg-purple-700 text-xs flex items-center gap-1.5 shadow-sm">
                    <i class="bi bi-tags-fill text-sm"></i>
                    <span>Cetak Label Slip Massal</span>
                </a>
            </div>
        </div>

        <!-- FILTER BAR TINGKAT / LEVEL / RUANGAN / PERIODE -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                <div class="flex items-center gap-2">
                    <i class="bi bi-funnel-fill text-primary"></i>
                    <h3 class="text-xs md:text-sm font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                        Filter Rekapitulasi Kas & Ruangan
                    </h3>
                </div>
                <div class="text-[11px] font-bold text-zinc-400">
                    Menampilkan: <span class="text-primary font-mono font-black">{{ $rekap['total_rekening'] }}
                        Rekening</span>
                </div>
            </div>

            <form method="GET" action="{{ route('tabungan.pecahan.index') }}"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">

                <!-- 1. Filter Periode -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Periode Tabungan</label>
                    <select name="periode_id" class="m3-input-glass w-full text-xs font-bold"
                        onchange="this.form.submit()">
                        <option value="">Semua Periode</option>
                        @foreach ($periodes as $p)
                            <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_periode }} {{ $p->is_active ? '★ (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Filter Kategori Nasabah -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Kategori Nasabah</label>
                    <select name="jenis_nasabah" class="m3-input-glass w-full text-xs font-bold"
                        onchange="this.form.submit()">
                        <option value="">Semua Nasabah</option>
                        <option value="Murid" {{ $jenisNasabah === 'Murid' ? 'selected' : '' }}>Murid / Murid</option>
                        <option value="Ustadz" {{ $jenisNasabah === 'Ustadz' ? 'selected' : '' }}>Ustadz / Guru</option>
                        <option value="Kas Ruangan" {{ $jenisNasabah === 'Kas Ruangan' ? 'selected' : '' }}>Kas Ruangan
                        </option>
                        <option value="Umum" {{ $jenisNasabah === 'Umum' ? 'selected' : '' }}>Nasabah Umum</option>
                    </select>
                </div>

                <!-- 3. Filter Level / Kelas -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Tingkat / Level
                        Kelas</label>
                    <select name="level_id" class="m3-input-glass w-full text-xs font-bold"
                        onchange="this.form.submit()">
                        <option value="">Semua Level</option>
                        @foreach ($levels as $lvl)
                            <option value="{{ $lvl->id }}" {{ $levelId == $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->nama_level }} ({{ $lvl->tingkat->kode_tingkat ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 4. Filter Ruangan -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Ruangan Spesifik</label>
                    <select name="ruangan_id" class="m3-input-glass w-full text-xs font-bold"
                        onchange="this.form.submit()">
                        <option value="">Semua Ruangan</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}" {{ $ruanganId == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Filter Status Verifikasi -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Status Verifikasi</label>
                    <select name="status_verifikasi" class="m3-input-glass w-full text-xs font-bold"
                        onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Cocok" {{ $statusVerifikasi === 'Cocok' ? 'selected' : '' }}>✅ Sudah Cocok
                        </option>
                        <option value="Selisih" {{ $statusVerifikasi === 'Selisih' ? 'selected' : '' }}>⚠️ Ada Selisih
                        </option>
                        <option value="Buku Tidak Ada" {{ $statusVerifikasi === 'Buku Tidak Ada' ? 'selected' : '' }}>❌
                            Buku Tidak Ada</option>
                        <option value="Belum Diverifikasi"
                            {{ $statusVerifikasi === 'Belum Diverifikasi' ? 'selected' : '' }}>⏳ Belum Diverifikasi
                        </option>
                    </select>
                </div>

                <!-- 6. Search & Action Buttons -->
                <div class="flex items-end gap-1.5">
                    <div class="flex-1">
                        <label class="block text-[10px] font-black uppercase text-zinc-400 mb-1">Cari Nama/Rek</label>
                        <input type="text" name="q" value="{{ $q }}" placeholder="Nama / NISM..."
                            class="m3-input-glass w-full text-xs">
                    </div>
                    <button type="submit"
                        class="p-2.5 rounded-xl bg-primary hover:bg-primary-hover text-white shadow-xs"
                        title="Terapkan Filter">
                        <i class="bi bi-search text-xs"></i>
                    </button>
                    @if ($periodeId || $jenisNasabah || $levelId || $ruanganId || $statusVerifikasi || $q)
                        <a href="{{ route('tabungan.pecahan.index') }}"
                            class="p-2.5 rounded-xl bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-300 text-xs font-bold"
                            title="Reset Filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <!-- KARTU 1: RINGKASAN FINANSIAL TOTAL -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Rekening -->
            <div class="m3-glass-card p-4 rounded-2xl md:rounded-3xl shadow-xs border-l-4 border-blue-500">
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Buku / Amplop</div>
                <div class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white mt-1">
                    {{ number_format($rekap['total_rekening'], 0, ',', '.') }}
                    <span class="text-xs font-normal text-zinc-400">Nasabah</span>
                </div>
            </div>

            <!-- Total Saldo Kotor -->
            <div class="m3-glass-card p-4 rounded-2xl md:rounded-3xl shadow-xs border-l-4 border-emerald-500">
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Saldo Terkumpul</div>
                <div class="text-xl md:text-2xl font-mono font-black text-emerald-600 dark:text-emerald-400 mt-1">
                    Rp {{ number_format($rekap['total_saldo_kotor'], 0, ',', '.') }}
                </div>
            </div>

            <!-- Total Potongan Madrasah -->
            <div class="m3-glass-card p-4 rounded-2xl md:rounded-3xl shadow-xs border-l-4 border-amber-500">
                <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Total Potongan Madrasah</div>
                <div class="text-xl md:text-2xl font-mono font-black text-amber-600 dark:text-amber-400 mt-1">
                    Rp {{ number_format($rekap['total_potongan'], 0, ',', '.') }}
                </div>
            </div>

            <!-- Total Hak Bersih Kas Fisik -->
            <div
                class="m3-glass-card p-4 rounded-2xl md:rounded-3xl shadow-xs border-l-4 border-purple-500 bg-purple-500/5">
                <div class="text-[10px] font-bold text-purple-700 dark:text-purple-300 uppercase tracking-wider">Hak
                    Bersih Kas Fisik Disediakan</div>
                <div class="text-xl md:text-2xl font-mono font-black text-purple-700 dark:text-purple-300 mt-1">
                    Rp {{ number_format($rekap['total_hak_bersih'], 0, ',', '.') }}
                </div>
            </div>
        </div>

        <!-- KARTU 2: GRID REKAPITULASI 10 DENOMINASI PECAHAN UANG KAS FISIK -->
        <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-xs">
            <div
                class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h4 class="font-black text-xs md:text-sm text-zinc-900 dark:text-white uppercase tracking-wider">
                        🪙 Rekapitulasi Kebutuhan Uang Pecahan Fisik (Brankas / Bank)
                    </h4>
                </div>
                <span class="text-[11px] font-mono font-bold text-emerald-600 dark:text-emerald-400">
                    Total Rekonsiliasi: Rp {{ number_format($rekap['total_hak_bersih'], 0, ',', '.') }} (100% Klop)
                </span>
            </div>

            <div class="p-4 grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-2.5">
                @foreach ($rekap['total_pecahan_global'] as $denomKey => $info)
                    <div
                        class="p-3 rounded-2xl border text-center transition-all shadow-2xs {{ $info['count'] > 0 ? 'bg-emerald-500/5 border-emerald-500/30' : 'bg-zinc-50/50 dark:bg-zinc-900/30 border-zinc-200/60 dark:border-zinc-800/60 opacity-60' }}">
                        <div class="text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-300">
                            {{ $info['label'] }}
                        </div>
                        <div class="text-lg font-mono font-black text-emerald-700 dark:text-emerald-300 my-1">
                            {{ number_format($info['count'], 0, ',', '.') }}
                        </div>
                        <div class="text-[9px] text-zinc-400 uppercase font-semibold">
                            {{ $info['tipe'] }}
                        </div>
                        <div
                            class="text-[9px] font-mono font-bold text-zinc-500 dark:text-zinc-400 mt-1 border-t border-zinc-200 dark:border-zinc-800 pt-1">
                            Rp {{ number_format($info['total_nominal'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- KARTU 3: TABEL RINCIAN PECAHAN PER MURID / NASABAH -->
        <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-xs">
            <div
                class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <h4 class="font-black text-xs md:text-sm text-zinc-900 dark:text-white uppercase tracking-wider">
                        📋 Daftar Murid & Rincian Pecahan Uang Buku Tabungan
                    </h4>
                </div>
                <div class="text-xs text-zinc-500 font-bold">
                    Total: {{ count($rekap['rekap_per_nasabah']) }} Murid / Rekening
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-900">
                            <th class="py-3 px-3 text-center w-10">No</th>
                            <th class="py-3 px-3">No. Rek / Nasabah</th>
                            <th class="py-3 px-3">Kelas / Ruang</th>
                            <th class="py-3 px-3 text-right">Saldo Kotor</th>
                            <th class="py-3 px-3 text-right">Potongan</th>
                            <th
                                class="py-3 px-3 text-right bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 font-black">
                                Hak Bersih</th>

                            <!-- 10 Kolom Denominasi -->
                            <th class="py-3 px-2 text-center text-[9px] w-12 bg-zinc-50 dark:bg-zinc-900/50">100k</th>
                            <th class="py-3 px-2 text-center text-[9px] w-12 bg-zinc-50 dark:bg-zinc-900/50">50k</th>
                            <th class="py-3 px-2 text-center text-[9px] w-12 bg-zinc-50 dark:bg-zinc-900/50">20k</th>
                            <th class="py-3 px-2 text-center text-[9px] w-12 bg-zinc-50 dark:bg-zinc-900/50">10k</th>
                            <th class="py-3 px-2 text-center text-[9px] w-12 bg-zinc-50 dark:bg-zinc-900/50">5k</th>
                            <th class="py-3 px-2 text-center text-[9px] w-12 bg-zinc-50 dark:bg-zinc-900/50">2k</th>
                            <th class="py-3 px-2 text-center text-[9px] w-12 bg-zinc-50 dark:bg-zinc-900/50">1k</th>
                            <th class="py-3 px-2 text-center text-[9px] w-10 bg-zinc-50 dark:bg-zinc-900/50">500</th>
                            <th class="py-3 px-2 text-center text-[9px] w-10 bg-zinc-50 dark:bg-zinc-900/50">200</th>
                            <th class="py-3 px-2 text-center text-[9px] w-10 bg-zinc-50 dark:bg-zinc-900/50">100</th>

                            <th class="py-3 px-3 text-center w-24">Buku Fisik</th>
                            <th class="py-3 px-3 text-center w-20">Aksi Slip</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium">
                        @forelse ($rekap['rekap_per_nasabah'] as $item)
                            <tr class="hover:bg-zinc-500/5 transition-colors">
                                <td class="py-2.5 px-3 text-center font-bold text-zinc-400">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="font-bold text-zinc-900 dark:text-white truncate max-w-[160px]">
                                        {{ $item['nama_nasabah'] }}
                                    </div>
                                    <div class="text-[10px] font-mono text-zinc-400">
                                        {{ $item['nomor_rekening'] }} &bull; {{ $item['identitas_nasabah'] }}
                                    </div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="font-semibold text-zinc-800 dark:text-zinc-200">
                                        {{ $item['nama_ruangan'] }}
                                    </div>
                                    <div class="text-[10px] text-zinc-400">
                                        Level: {{ $item['nama_level'] }}
                                    </div>
                                </td>
                                <td
                                    class="py-2.5 px-3 text-right font-mono font-bold text-zinc-600 dark:text-zinc-400">
                                    Rp {{ number_format($item['saldo_kotor'], 0, ',', '.') }}
                                </td>
                                <td
                                    class="py-2.5 px-3 text-right font-mono text-amber-600 dark:text-amber-400 text-[11px]">
                                    {{ $item['nominal_potongan'] > 0 ? 'Rp ' . number_format($item['nominal_potongan'], 0, ',', '.') : '-' }}
                                    @if ($item['persentase_potongan'] > 0)
                                        <span
                                            class="text-[9px] text-zinc-400">({{ $item['persentase_potongan'] }}%)</span>
                                    @endif
                                </td>
                                <td
                                    class="py-2.5 px-3 text-right font-mono font-black text-emerald-700 dark:text-emerald-300 bg-emerald-500/10">
                                    Rp {{ number_format($item['hak_bersih'], 0, ',', '.') }}
                                </td>

                                <!-- 10 Kolom Pecahan -->
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['100000'] > 0 ? 'font-black text-emerald-600 bg-emerald-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['100000'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['50000'] > 0 ? 'font-black text-emerald-600 bg-emerald-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['50000'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['20000'] > 0 ? 'font-black text-emerald-600 bg-emerald-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['20000'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['10000'] > 0 ? 'font-black text-emerald-600 bg-emerald-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['10000'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['5000'] > 0 ? 'font-black text-emerald-600 bg-emerald-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['5000'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['2000'] > 0 ? 'font-black text-emerald-600 bg-emerald-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['2000'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['1000'] > 0 ? 'font-black text-emerald-600 bg-emerald-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['1000'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['500'] > 0 ? 'font-black text-amber-600 bg-amber-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['500'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['200'] > 0 ? 'font-black text-amber-600 bg-amber-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['200'] ?: '-' }}
                                </td>
                                <td
                                    class="py-2.5 px-2 text-center font-mono {{ $item['pecahan']['100'] > 0 ? 'font-black text-amber-600 bg-amber-500/5' : 'text-zinc-300 dark:text-zinc-700' }}">
                                    {{ $item['pecahan']['100'] ?: '-' }}
                                </td>

                                <!-- Status Buku & Verifikasi -->
                                <td class="py-2.5 px-3 text-center">
                                    @if ($item['status_verifikasi'] === 'Cocok')
                                        <span
                                            class="text-[9px] font-black px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600">
                                            ✓ Ada & Cocok
                                        </span>
                                    @elseif ($item['status_verifikasi'] === 'Buku Tidak Ada')
                                        <span
                                            class="text-[9px] font-black px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-600">
                                            ❌ Buku Hilang
                                        </span>
                                    @elseif ($item['status_verifikasi'] === 'Selisih')
                                        <span
                                            class="text-[9px] font-black px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-600">
                                            ⚠️ Selisih
                                        </span>
                                    @else
                                        <span
                                            class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600">
                                            ⏳ Belum Cek
                                        </span>
                                    @endif
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="py-2.5 px-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Tombol Cetak Slip Label Mini -->
                                        <a href="{{ route('tabungan.pecahan.cetak-slip', $item['tabungan_id']) }}"
                                            target="_blank"
                                            class="p-1.5 rounded-lg bg-purple-600/10 hover:bg-purple-600/20 text-purple-600 dark:text-purple-400 transition-colors"
                                            title="Cetak Slip Pecahan Diselipkan ke Buku">
                                            <i class="bi bi-tag-fill text-xs"></i>
                                        </a>

                                        <!-- Tombol Cek Mutasi -->
                                        <a href="{{ route('tabungan.cek-mutasi.index', ['nomor_rekening' => $item['nomor_rekening']]) }}"
                                            class="p-1.5 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors"
                                            title="Buka Mutasi Rekening">
                                            <i class="bi bi-eye-fill text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="py-8 text-center text-zinc-400 text-xs">
                                    Tidak ada data tabungan yang sesuai dengan filter di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr
                            class="bg-zinc-100 dark:bg-zinc-900 font-black border-t-2 border-zinc-200 dark:border-zinc-800 text-xs text-zinc-900 dark:text-white">
                            <td colspan="3" class="py-3 px-3 uppercase tracking-wider text-zinc-500">
                                TOTAL KESELURUHAN KAS:
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-zinc-700 dark:text-zinc-300">
                                Rp {{ number_format($rekap['total_saldo_kotor'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-amber-600">
                                Rp {{ number_format($rekap['total_potongan'], 0, ',', '.') }}
                            </td>
                            <td
                                class="py-3 px-3 text-right font-mono text-emerald-700 dark:text-emerald-300 bg-emerald-500/20 text-sm">
                                Rp {{ number_format($rekap['total_hak_bersih'], 0, ',', '.') }}
                            </td>

                            <!-- Total Pecahan Per Kolom -->
                            <td class="py-3 px-2 text-center font-mono font-black text-emerald-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['100000']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-emerald-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['50000']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-emerald-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['20000']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-emerald-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['10000']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-emerald-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['5000']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-emerald-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['2000']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-emerald-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['1000']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-amber-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['500']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-amber-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['200']['count'] ?: '-' }}
                            </td>
                            <td class="py-3 px-2 text-center font-mono font-black text-amber-700 text-[10px]">
                                {{ $rekap['total_pecahan_global']['100']['count'] ?: '-' }}
                            </td>

                            <td colspan="2" class="py-3 px-3 text-center font-mono text-[10px] text-zinc-500">
                                {{ $rekap['total_rekening'] }} Amplop Buku
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

</x-app-layout>
