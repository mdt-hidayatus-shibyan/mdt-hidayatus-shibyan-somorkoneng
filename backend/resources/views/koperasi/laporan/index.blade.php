@section('title', 'Laporan Keuangan & Penjualan Koperasi')
<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-20">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <div
                    class="w-9 h-9 rounded-xl bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center border border-primary/20 shrink-0">
                    <i class="bi bi-file-earmark-bar-graph-fill text-lg"></i>
                </div>
                <span>Laporan Penjualan & Keuangan</span>
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Periode: <strong class="text-zinc-800 dark:text-zinc-200">{{ $judulPeriode }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('koperasi.laporan.cetak', request()->all()) }}" target="_blank"
                class="m3-btn-secondary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center justify-center gap-1.5 active:scale-95 shadow-2xs">
                <i class="bi bi-printer-fill text-sm text-primary"></i>
                <span>Cetak Laporan (A4/PDF)</span>
            </a>
            <a href="{{ route('koperasi.pos.index') }}"
                class="m3-btn-primary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center justify-center gap-1.5 active:scale-95 shadow-md shadow-primary/20">
                <i class="bi bi-calculator-fill text-sm"></i>
                <span>Buka Kasir POS</span>
            </a>
        </div>
    </div>

    <!-- FILTER PERIODE FORM -->
    <div class="m3-glass-card p-4 sm:p-5 mb-6 shadow-sm dark:shadow-none relative z-10">
        <form action="{{ route('koperasi.laporan.index') }}" method="GET" class="flex flex-wrap items-end gap-3">

            <div class="w-full sm:w-40 space-y-1">
                <label class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400">Tipe Periode</label>
                <select name="periode_type" id="periodeTypeSelect" onchange="this.form.submit()"
                    class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold appearance-none cursor-pointer">
                    <option value="bulan" {{ request('periode_type', 'bulan') == 'bulan' ? 'selected' : '' }}>Bulanan
                    </option>
                    <option value="hari" {{ request('periode_type') == 'hari' ? 'selected' : '' }}>Harian</option>
                    <option value="custom" {{ request('periode_type') == 'custom' ? 'selected' : '' }}>Rentang Tanggal
                    </option>
                </select>
            </div>

            @if (request('periode_type') == 'hari')
                <div class="w-full sm:w-48 space-y-1">
                    <label class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400">Pilih Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}"
                        onchange="this.form.submit()"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold">
                </div>
            @elseif (request('periode_type') == 'custom')
                <div class="w-full sm:w-40 space-y-1">
                    <label class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai', date('Y-m-01')) }}"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold">
                </div>
                <div class="w-full sm:w-40 space-y-1">
                    <label class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir', date('Y-m-d')) }}"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold">
                </div>
            @else
                <div class="w-full sm:w-48 space-y-1">
                    <label class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400">Pilih Bulan</label>
                    <input type="month" name="bulan" value="{{ request('bulan', date('Y-m')) }}"
                        onchange="this.form.submit()"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold">
                </div>
            @endif

            <button type="submit"
                class="m3-btn-primary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center gap-1.5 active:scale-95 shrink-0 shadow-md shadow-primary/20">
                <i class="bi bi-filter text-sm"></i>
                <span>Terapkan Filter</span>
            </button>
        </form>
    </div>

    <!-- 4 KARTU RINGKASAN FINANSIAL LAPORAN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Omzet -->
        <div class="m3-glass-card p-5 relative shadow-sm dark:shadow-none">
            <span class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">
                Total Omzet Penjualan
            </span>
            <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white mt-1 tracking-tight">
                Rp {{ number_format($totalOmzet, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-semibold mt-1">
                {{ $totalTrx }} Transaksi • {{ $totalItemTerjual }} Item Terjual
            </p>
        </div>

        <!-- Total HPP -->
        <div class="m3-glass-card p-5 relative shadow-sm dark:shadow-none">
            <span class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">
                Total Modal (HPP)
            </span>
            <h3 class="text-xl md:text-2xl font-black text-zinc-700 dark:text-zinc-300 mt-1 tracking-tight">
                Rp {{ number_format($totalHpp, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-semibold mt-1">
                Harga Pokok Penjualan Barang
            </p>
        </div>

        <!-- Laba Kotor -->
        <div
            class="m3-glass-card p-5 relative shadow-sm dark:shadow-none bg-emerald-500/5 dark:bg-emerald-950/20 border-l-4 border-l-emerald-500">
            <span
                class="text-[10px] font-extrabold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block">
                Keuntungan / Laba Kotor
            </span>
            <h3 class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 tracking-tight">
                Rp {{ number_format($totalLabaKotor, 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-emerald-700/80 dark:text-emerald-400/80 font-bold mt-1">
                Margin Laba: {{ $totalOmzet > 0 ? round(($totalLabaKotor / $totalOmzet) * 100, 1) : 0 }}%
            </p>
        </div>

        <!-- Potong Tabungan Share -->
        <div class="m3-glass-card p-5 relative shadow-sm dark:shadow-none">
            <span
                class="text-[10px] font-extrabold text-purple-600 dark:text-purple-400 uppercase tracking-wider block">
                Omzet Potong Tabungan
            </span>
            <h3 class="text-xl md:text-2xl font-black text-purple-600 dark:text-purple-400 mt-1 tracking-tight">
                Rp {{ number_format($rekapMetode['Potong_Tabungan'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-semibold mt-1">
                Tunai (Cash): Rp {{ number_format($rekapMetode['Tunai'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- 2 KOLOM BREAKDOWN & TOP SELLING -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Breakdown Metode Pembayaran & Pelanggan -->
        <div class="m3-glass-card p-5 sm:p-6 shadow-sm dark:shadow-none space-y-4">
            <h4
                class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-pie-chart-fill text-primary"></i>
                <span>Komposisi Penjualan</span>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Metode Bayar -->
                <div
                    class="p-4 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5">
                    <span
                        class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">
                        Metode Pembayaran
                    </span>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between font-semibold">
                            <span class="text-zinc-600 dark:text-zinc-400">Tunai:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">Rp
                                {{ number_format($rekapMetode['Tunai'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-semibold">
                            <span class="text-zinc-600 dark:text-zinc-400">Potong Tabungan:</span>
                            <span class="font-bold text-purple-600 dark:text-purple-400">Rp
                                {{ number_format($rekapMetode['Potong_Tabungan'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-semibold">
                            <span class="text-zinc-600 dark:text-zinc-400">QRIS / Transfer:</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">Rp
                                {{ number_format($rekapMetode['QRIS'] + $rekapMetode['Transfer'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-semibold">
                            <span class="text-zinc-600 dark:text-zinc-400">Bayar Nanti (Hutang):</span>
                            <span class="font-bold text-amber-600 dark:text-amber-400">Rp
                                {{ number_format($rekapMetode['Hutang'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Jenis Pelanggan -->
                <div
                    class="p-4 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5">
                    <span
                        class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">
                        Kategori Pembeli
                    </span>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between font-semibold">
                            <span class="text-zinc-600 dark:text-zinc-400">Murid:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">Rp
                                {{ number_format($rekapPelanggan['Murid'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-semibold">
                            <span class="text-zinc-600 dark:text-zinc-400">Dewan Ustadz:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">Rp
                                {{ number_format($rekapPelanggan['Ustadz'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-semibold">
                            <span class="text-zinc-600 dark:text-zinc-400">Wali / Umum:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">Rp
                                {{ number_format($rekapPelanggan['Umum'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 10 Produk / Paket Terlaris -->
        <div class="m3-glass-card p-5 sm:p-6 shadow-sm dark:shadow-none space-y-3">
            <h4
                class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-trophy-fill text-amber-500"></i>
                <span>Top Produk & Paket Terlaris</span>
            </h4>

            <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 max-h-56 overflow-y-auto pr-1">
                @forelse ($topItem as $ti)
                    <div class="py-2.5 flex items-center justify-between gap-2 text-xs">
                        <div>
                            <div class="font-black text-zinc-900 dark:text-white">
                                {{ $ti->nama_item }}
                                @if ($ti->tipe_item === 'Paket_Bundling')
                                    <span
                                        class="ml-1 px-1.5 py-0.5 rounded text-[8px] font-black bg-indigo-600 text-white uppercase">Paket</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono">
                                {{ $ti->kode_item }} • Laba: Rp {{ number_format($ti->total_profit, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-emerald-600 dark:text-emerald-400 block">{{ $ti->total_qty }}
                                {{ $ti->satuan }}</span>
                            <span class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400">Rp
                                {{ number_format($ti->total_omzet, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-zinc-400 dark:text-zinc-500">
                        <i class="bi bi-inbox text-2xl mb-1 block opacity-40"></i>
                        Belum ada item terjual pada periode ini.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</x-app-layout>
