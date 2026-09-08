@section('title', 'Riwayat Transaksi Koperasi')
<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-20">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <div
                    class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-500/20 shrink-0">
                    <i class="bi bi-receipt-cutoff text-lg"></i>
                </div>
                <span>Riwayat Transaksi</span>
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Daftar seluruh nota penjualan dan riwayat transaksi kasir koperasi madrasah
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto shrink-0 flex-wrap">
            <a href="{{ route('koperasi.pos.index') }}"
                class="m3-btn-primary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center justify-center gap-1.5 active:scale-95 shadow-md shadow-primary/20">
                <i class="bi bi-calculator-fill text-sm"></i>
                <span>Buka Kasir POS</span>
            </a>

            <a href="{{ route('koperasi.laporan.index') }}"
                class="m3-btn-secondary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center justify-center gap-1.5 active:scale-95">
                <i class="bi bi-file-earmark-bar-graph-fill text-sm text-primary"></i>
                <span>Laporan Penjualan</span>
            </a>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="m3-glass-card overflow-hidden flex flex-col relative z-10 shadow-sm dark:shadow-none">

        <!-- Toolbar Filter -->
        <div
            class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/60 dark:bg-zinc-950/40 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center border border-primary/20 shrink-0">
                    <i class="bi bi-filter-circle-fill text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                        Filter & Pencarian Nota
                    </h3>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                        {{ $totalTrx }} Transaksi • Total Omzet: <strong
                            class="text-emerald-600 dark:text-emerald-400">Rp
                            {{ number_format($totalOmzet, 0, ',', '.') }}</strong>
                        @if (isset($countHutangAktif) && $countHutangAktif > 0)
                            • <span class="text-amber-600 dark:text-amber-400 font-bold">Piutang Belum Lunas: Rp
                                {{ number_format($totalHutangAktif, 0, ',', '.') }} ({{ $countHutangAktif }}
                                Nota)</span>
                        @endif
                    </p>
                </div>
            </div>

            <form action="{{ route('koperasi.transaksi.index') }}" method="GET"
                class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <!-- Filter Status Pembayaran -->
                <div class="w-full sm:w-40">
                    <select name="status_pembayaran" onchange="this.form.submit()"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Status Bayar</option>
                        <option value="Lunas" {{ request('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas
                        </option>
                        <option value="Belum_Lunas"
                            {{ request('status_pembayaran') == 'Belum_Lunas' ? 'selected' : '' }}>
                            Belum Lunas (Hutang)
                        </option>
                    </select>
                </div>

                <!-- Filter Metode Bayar -->
                <div class="w-full sm:w-36">
                    <select name="metode_pembayaran" onchange="this.form.submit()"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Metode</option>
                        <option value="Tunai" {{ request('metode_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai
                            (Cash)</option>
                        <option value="Potong_Tabungan"
                            {{ request('metode_pembayaran') == 'Potong_Tabungan' ? 'selected' : '' }}>Potong Tabungan
                        </option>
                        <option value="QRIS" {{ request('metode_pembayaran') == 'QRIS' ? 'selected' : '' }}>QRIS
                        </option>
                        <option value="Hutang" {{ request('metode_pembayaran') == 'Hutang' ? 'selected' : '' }}>Hutang
                            / Piutang
                        </option>
                    </select>
                </div>

                <!-- Filter Kategori Pelanggan -->
                <div class="w-full sm:w-36">
                    <select name="jenis_pelanggan" onchange="this.form.submit()"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Pelanggan</option>
                        <option value="Murid" {{ request('jenis_pelanggan') == 'Murid' ? 'selected' : '' }}>Murid
                        </option>
                        <option value="Ustadz" {{ request('jenis_pelanggan') == 'Ustadz' ? 'selected' : '' }}>Ustadz
                        </option>
                        <option value="Umum" {{ request('jenis_pelanggan') == 'Umum' ? 'selected' : '' }}>Umum
                        </option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-48">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="No. nota / nama..."
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-medium">
                </div>

                @if (request()->hasAny(['status_pembayaran', 'metode_pembayaran', 'jenis_pelanggan', 'search']))
                    <a href="{{ route('koperasi.transaksi.index') }}"
                        class="min-h-[40px] px-3 rounded-xl md:rounded-2xl bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-xs font-bold flex items-center justify-center transition-all shrink-0"
                        title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- TABEL TRANSAKSI -->
        <div class="overflow-x-auto">
            <table class="m3-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">No. Nota</th>
                        <th class="text-left">Tanggal & Waktu</th>
                        <th class="text-left">Pelanggan</th>
                        <th class="text-center">Jml Item</th>
                        <th class="text-right">Total Belanja</th>
                        <th class="text-left">Metode Bayar</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 font-medium text-xs">
                    @forelse ($transaksis as $t)
                        <tr
                            class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors {{ $t->status === 'Dibatalkan' ? 'opacity-60 bg-rose-500/5' : '' }}">
                            <td class="font-mono font-bold text-zinc-900 dark:text-white">
                                <a href="{{ route('koperasi.transaksi.show', $t->id) }}"
                                    class="text-primary hover:underline inline-flex items-center gap-1.5">
                                    <i class="bi bi-receipt text-xs"></i>
                                    <span>{{ $t->nomor_nota }}</span>
                                </a>
                            </td>
                            <td class="text-zinc-500 dark:text-zinc-400 text-[11px] whitespace-nowrap">
                                {{ $t->tanggal->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="font-bold text-zinc-900 dark:text-white">
                                    {{ $t->nama_pelanggan }}
                                </div>
                                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-semibold uppercase">
                                    {{ $t->jenis_pelanggan }}
                                </span>
                            </td>
                            <td class="text-center font-bold text-zinc-800 dark:text-zinc-200">
                                {{ $t->total_item }}
                            </td>
                            <td class="text-right font-mono font-black text-zinc-900 dark:text-white">
                                Rp {{ number_format($t->total_akhir, 0, ',', '.') }}
                            </td>
                            <td>
                                @if ($t->metode_pembayaran === 'Potong_Tabungan')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20">
                                        <i class="bi bi-wallet2"></i> Potong Tabungan
                                    </span>
                                @elseif ($t->metode_pembayaran === 'QRIS')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20">
                                        <i class="bi bi-qr-code"></i> QRIS
                                    </span>
                                @elseif ($t->metode_pembayaran === 'Hutang')
                                    @if ($t->status_pembayaran === 'Belum_Lunas')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                            <i class="bi bi-clock-history"></i> Hutang (Belum Lunas)
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                            <i class="bi bi-check2-all"></i> Hutang (Lunas)
                                        </span>
                                    @endif
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                        <i class="bi bi-cash"></i> Tunai
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($t->status === 'Selesai')
                                    @if ($t->status_pembayaran === 'Belum_Lunas')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                            Belum Lunas
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                            Lunas
                                        </span>
                                    @endif
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    @if ($t->status === 'Selesai' && $t->status_pembayaran === 'Belum_Lunas')
                                        <a href="{{ route('koperasi.transaksi.show', $t->id) }}"
                                            class="px-2.5 py-1 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold shadow-2xs inline-flex items-center gap-1 transition-all active:scale-95"
                                            title="Lunasi Tagihan Hutang Ini">
                                            <i class="bi bi-wallet2"></i>
                                            <span>Lunasi</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('koperasi.transaksi.show', $t->id) }}"
                                        class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-primary hover:text-white dark:hover:bg-primary-dark dark:hover:text-zinc-950 flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                        title="Detail Nota">
                                        <i class="bi bi-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('koperasi.kasir.struk', $t->id) }}" target="_blank"
                                        class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                        title="Cetak Struk Thermal">
                                        <i class="bi bi-printer text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-zinc-400 dark:text-zinc-500 text-xs">
                                <i class="bi bi-receipt text-3xl mb-2 block opacity-40"></i>
                                Belum ada data riwayat transaksi penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transaksis->hasPages())
            <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-950/30">
                {{ $transaksis->links() }}
            </div>
        @endif

    </div>

</x-app-layout>
