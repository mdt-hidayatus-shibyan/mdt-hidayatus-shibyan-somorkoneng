@section('title', 'Dashboard Koperasi Madrasah')
<x-app-layout>

    <!-- Header Action Buttons -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <div
                    class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0">
                    <i class="bi bi-shop text-lg"></i>
                </div>
                <span>Koperasi Madrasah</span>
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Pusat transaksi kasir toko, penjualan paket bundling kitab/seragam, dan manajemen stok.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('koperasi.pos.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-sm active:scale-95 transition-all">
                <i class="bi bi-calculator-fill text-sm"></i>
                <span>Buka Kasir POS</span>
            </a>
            <a href="{{ route('koperasi.pembelian.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-bag-plus-fill text-sm"></i>
                <span>Kulakan Barang</span>
            </a>
            <a href="{{ route('koperasi.paket.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 border border-indigo-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-collection-fill text-sm"></i>
                <span>Paket Bundling</span>
            </a>
            <a href="{{ route('koperasi.stok.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-arrow-left-right text-sm"></i>
                <span>Mutasi Stok</span>
            </a>
            <a href="{{ route('koperasi.produk.create') }}" class="m3-btn-secondary text-xs">
                <i class="bi bi-plus-circle-fill text-sm"></i>
                <span>Tambah Produk</span>
            </a>
        </div>
    </div>

    <!-- GRID STATISTIK REKAP KOPERASI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Omzet Hari Ini -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Omzet Hari Ini
                </span>
                <div
                    class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base shrink-0 border border-emerald-500/20">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                Rp {{ number_format($ringkasan['omzetHariIni'], 0, ',', '.') }}
            </h3>
            <div class="flex items-center gap-2 mt-2.5 text-[11px] font-bold text-zinc-500 dark:text-zinc-400">
                <span class="text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ $ringkasan['trxHariIni'] }} Transaksi Sukses</span>
                </span>
            </div>
        </div>

        <!-- Card 2: Laba Kotor Hari Ini -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Laba Kotor Hari Ini
                </span>
                <div
                    class="w-9 h-9 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-base shrink-0 border border-teal-500/20">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-teal-600 dark:text-teal-400 tracking-tight">
                Rp {{ number_format($ringkasan['labaHariIni'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] font-bold text-zinc-400 mt-2.5">
                Margin Laba Bersih (Omzet - HPP)
            </p>
        </div>

        <!-- Card 3: Omzet Bulan Ini -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Omzet Bulan Ini
                </span>
                <div
                    class="w-9 h-9 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base shrink-0 border border-blue-500/20">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                Rp {{ number_format($ringkasan['omzetBulanIni'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] font-bold text-blue-600 dark:text-blue-400 mt-2.5 truncate"
                title="{{ $ringkasan['trxBulanIni'] }} Transaksi • Laba: Rp {{ number_format($ringkasan['labaBulanIni'], 0, ',', '.') }}">
                {{ $ringkasan['trxBulanIni'] }} Trx • Laba: Rp
                {{ number_format($ringkasan['labaBulanIni'], 0, ',', '.') }}
            </p>
        </div>

        <!-- Card 4: Katalog & Alert Stok -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Katalog & Status Stok
                </span>
                <div
                    class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-base shrink-0 border border-amber-500/20">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                {{ $ringkasan['totalProduk'] }} <span class="text-sm font-semibold text-zinc-400">Item Produk</span>
            </h3>
            <div class="mt-2.5 text-[11px] font-bold">
                @if ($ringkasan['stokMenipisCount'] > 0)
                    <span
                        class="text-amber-600 dark:text-amber-400 flex items-center gap-1 font-extrabold animate-pulse">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span>{{ $ringkasan['stokMenipisCount'] }} Stok Menipis!</span>
                    </span>
                @else
                    <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-extrabold">
                        <i class="bi bi-check2-all"></i>
                        <span>{{ $ringkasan['totalPaket'] }} Paket Bundling Aktif</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- CONTENT DUA KOLOM: TRANSAKSI TERAKHIR & SIDEBAR WIDGETS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri (2 Span): 10 Transaksi Kasir Terakhir -->
        <div class="lg:col-span-2 m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-2xs">
            <div
                class="p-4 sm:p-5 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                <span
                    class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-receipt-cutoff text-primary text-sm"></i>
                    Transaksi Kasir Terbaru
                </span>
                <a href="{{ route('koperasi.transaksi.index') }}"
                    class="text-xs font-bold text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="m3-table">
                    <thead>
                        <tr>
                            <th>No. Nota & Waktu</th>
                            <th>Pelanggan</th>
                            <th>Metode Bayar</th>
                            <th class="text-right">Total Belanja</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiTerbaru as $trx)
                            <tr>
                                <td>
                                    <a href="{{ route('koperasi.transaksi.show', $trx->id) }}"
                                        class="font-mono font-bold text-primary dark:text-primary-dark hover:underline block">
                                        {{ $trx->nomor_nota }}
                                    </a>
                                    <span class="text-[10px] text-zinc-400">
                                        {{ $trx->tanggal->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-bold text-zinc-900 dark:text-white">
                                        {{ $trx->nama_pelanggan }}
                                    </div>
                                    <span class="text-[10px] text-zinc-400 uppercase font-semibold">
                                        {{ $trx->jenis_pelanggan }}
                                    </span>
                                </td>
                                <td>
                                    @if ($trx->metode_pembayaran === 'Potong_Tabungan')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20">
                                            <i class="bi bi-wallet2"></i> Potong Tabungan
                                        </span>
                                    @elseif ($trx->metode_pembayaran === 'QRIS')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20">
                                            <i class="bi bi-qr-code"></i> QRIS
                                        </span>
                                    @elseif ($trx->metode_pembayaran === 'Hutang')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                            <i class="bi bi-clock-history"></i> Bayar Nanti
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                            <i class="bi bi-cash"></i> Tunai
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right font-mono font-black text-zinc-900 dark:text-white">
                                    Rp {{ number_format($trx->total_akhir, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('koperasi.kasir.struk', $trx->id) }}" target="_blank"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-primary hover:text-white transition-all text-xs"
                                        title="Cetak Struk Thermal">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-zinc-400">
                                    <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                                    Belum ada transaksi kasir yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan (1 Span): Alert Stok & Top Produk -->
        <div class="space-y-6">

            <!-- WIDGET 1: Peringatan Stok Menipis -->
            <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-2xs">
                <div
                    class="p-4 sm:p-5 bg-amber-500/5 dark:bg-amber-950/20 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                    <span
                        class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-amber-500 text-sm"></i>
                        Peringatan Stok Menipis
                    </span>
                    <a href="{{ route('koperasi.stok.index') }}"
                        class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline">
                        Restock
                    </a>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800/60 p-2">
                    @forelse ($stokMenipis as $sm)
                        <div
                            class="p-2.5 flex items-center justify-between gap-2.5 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 rounded-xl transition-colors">
                            <div class="min-w-0">
                                <h5 class="font-black text-xs text-zinc-900 dark:text-white truncate">
                                    {{ $sm->nama_produk }}
                                </h5>
                                <p class="text-[10px] text-zinc-400 font-mono">
                                    {{ $sm->kode_produk }} • {{ $sm->kategori?->nama_kategori }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-black {{ $sm->stok <= 0 ? 'bg-rose-500/10 text-rose-600 border border-rose-500/20' : 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20' }}">
                                    {{ $sm->stok }} {{ $sm->satuan }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div
                            class="text-center py-6 text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center justify-center gap-1.5">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Semua stok barang dalam kondisi aman.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- WIDGET 2: Top 5 Produk Terlaris -->
            <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-2xs">
                <div
                    class="p-4 sm:p-5 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                    <span
                        class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-trophy-fill text-amber-500 text-sm"></i>
                        Top 5 Produk Terlaris
                    </span>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-800/60 p-2">
                    @forelse ($topProduk as $idx => $tp)
                        <div
                            class="p-2.5 flex items-center justify-between gap-2.5 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 rounded-xl transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div
                                    class="w-7 h-7 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center justify-center font-bold text-xs shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                                    {{ $idx + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <h5 class="font-black text-xs text-zinc-900 dark:text-white truncate">
                                        {{ $tp->nama_item }}
                                    </h5>
                                    <span class="text-[10px] text-zinc-400 font-semibold">
                                        {{ $tp->tipe_item === 'Paket_Bundling' ? 'Paket Bundling' : 'Produk Satuan' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right font-black text-xs text-emerald-600 dark:text-emerald-400 shrink-0">
                                {{ $tp->total_terjual }}x
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-zinc-400">
                            Belum ada data penjualan bulan ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</x-app-layout>
