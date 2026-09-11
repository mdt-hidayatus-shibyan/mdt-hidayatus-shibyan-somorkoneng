@section('title', 'Laporan Keuangan Madrasah')

<x-app-layout>
    <!-- 1. Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-file-earmark-bar-graph-fill text-xs"></i>
                    <span>Pusat Laporan & Akuntansi</span>
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Laporan Keuangan & Perbendaharaan
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Laporan buku kas umum, arus kas operasional, dan rekapitulasi portofolio pinjaman & agunan.
            </p>
        </div>

        <!-- Quick Export / Print Actions -->
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('keuangan.laporan.cetak-buku-kas', ['start_date' => $startDate, 'end_date' => $endDate, 'akun_keuangan_id' => $akunId]) }}"
                target="_blank"
                class="px-4 py-2 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                <i class="bi bi-printer-fill text-emerald-600 dark:text-emerald-400"></i>
                <span>Cetak Buku Kas</span>
            </a>
            <a href="{{ route('keuangan.laporan.cetak-pinjaman', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                target="_blank"
                class="px-4 py-2 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                <i class="bi bi-printer-fill text-purple-600 dark:text-purple-400"></i>
                <span>Cetak Rekap Pinjaman</span>
            </a>
        </div>
    </div>

    <!-- 2. Filter Bar -->
    <div
        class="m3-glass-card p-4 md:p-5 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs mb-6 relative z-10">
        <form method="GET" action="{{ route('keuangan.laporan.index') }}"
            class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Pos Akun
                    Kas</label>
                <select name="akun_keuangan_id"
                    class="w-full px-3 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                    <option value="">Semua Pos Akun</option>
                    @foreach ($akuns as $a)
                        <option value="{{ $a->id }}" {{ $akunId == $a->id ? 'selected' : '' }}>
                            {{ $a->nama_akun }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Periode
                    Dari</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full px-3 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Periode
                    Sampai</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full px-3 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                    class="w-full m3-btn-primary py-2 px-4 text-xs font-bold shadow-md flex items-center justify-center gap-1.5">
                    <i class="bi bi-filter"></i>
                    <span>Tampilkan Laporan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Card 1: Saldo Kas -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 flex-shrink-0">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Saldo Semua Pos Kas
                </span>
                <span class="text-base md:text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                    Rp {{ number_format($totalSaldoSemuaKas, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 2: Saldo Bank -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black border border-blue-500/20 flex-shrink-0">
                <i class="bi bi-bank"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Saldo Kas Bank
                </span>
                <span class="text-base md:text-xl font-black text-blue-600 dark:text-blue-400 font-mono">
                    Rp {{ number_format($totalSaldoSemuaBank, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 3: Arus Kas Bersih Periode Ini -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-black border border-purple-500/20 flex-shrink-0">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Surplus / Defisit Periode
                </span>
                <span
                    class="text-base md:text-xl font-black font-mono {{ $netCashFlow >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 4: Sisa Piutang Pinjaman -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-black border border-rose-500/20 flex-shrink-0">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Piutang Pinjaman Berjalan
                </span>
                <span class="text-base md:text-xl font-black text-rose-600 dark:text-rose-400 font-mono">
                    Rp {{ number_format($totalSisaPiutang, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- 4. Reports Tabs Container -->
    <div class="space-y-6">
        <!-- Breakdown Per Kategori Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pemasukan Breakdown -->
            <div
                class="m3-glass-card p-6 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-3">
                <h3
                    class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-arrow-down-left-circle-fill"></i>
                    <span>Rincian Pemasukan per Kategori</span>
                </h3>
                <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
                    @forelse($pemasukanPerKategori as $kat)
                        <div class="py-2.5 flex justify-between items-center">
                            <span
                                class="font-bold text-zinc-800 dark:text-zinc-200">{{ $kat->kategoriKeuangan->nama_kategori ?? 'Pemasukan Lain-lain' }}</span>
                            <span class="font-mono font-black text-emerald-600 dark:text-emerald-400">Rp
                                {{ number_format($kat->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="py-4 text-center text-zinc-400 italic">Tidak ada pemasukan pada periode ini.</div>
                    @endforelse
                </div>
            </div>

            <!-- Pengeluaran Breakdown -->
            <div
                class="m3-glass-card p-6 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-3">
                <h3
                    class="text-xs font-black text-rose-600 dark:text-rose-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-arrow-up-right-circle-fill"></i>
                    <span>Rincian Pengeluaran per Kategori</span>
                </h3>
                <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
                    @forelse($pengeluaranPerKategori as $kat)
                        <div class="py-2.5 flex justify-between items-center">
                            <span
                                class="font-bold text-zinc-800 dark:text-zinc-200">{{ $kat->kategoriKeuangan->nama_kategori ?? 'Pengeluaran Lain-lain' }}</span>
                            <span class="font-mono font-black text-rose-600 dark:text-rose-400">Rp
                                {{ number_format($kat->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="py-4 text-center text-zinc-400 italic">Tidak ada pengeluaran pada periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Jurnal Transaksi Singkat -->
        <div class="m3-glass-card rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="p-4 md:p-5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                    Jurnal Buku Kas Periode Terpilih ({{ $transaksis->count() }} Transaksi)
                </h3>
                <a href="{{ route('keuangan.transaksi.index') }}"
                    class="text-xs font-bold text-primary dark:text-primary-dark hover:underline">
                    Buka Buku Kas Lengkap →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Kode Transaksi</th>
                            <th class="py-3 px-4">Pos Akun & Kategori</th>
                            <th class="py-3 px-4">Uraian Transaksi</th>
                            <th class="py-3 px-4 text-right">Debet (Masuk)</th>
                            <th class="py-3 px-4 text-right">Kredit (Keluar)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        @forelse($transaksis as $trx)
                            @php
                                $isMasuk = in_array($trx->jenis_transaksi, ['pemasukan', 'simpanan']);
                                $isKeluar = $trx->jenis_transaksi === 'pengeluaran';
                            @endphp
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40">
                                <td class="py-3 px-4">{{ $trx->tanggal_transaksi->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 font-mono font-bold">{{ $trx->kode_transaksi }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-bold">{{ $trx->akunKeuangan->nama_akun }}</span>
                                    <span
                                        class="text-[10px] text-zinc-400 block">{{ $trx->kategoriKeuangan->nama_kategori ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 max-w-xs truncate">{{ $trx->keterangan ?? '-' }}</td>
                                <td
                                    class="py-3 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $isMasuk ? 'Rp ' . number_format($trx->nominal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                                    {{ $isKeluar ? 'Rp ' . number_format($trx->nominal, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-zinc-400">
                                    Tidak ada transaksi ditemukan pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
