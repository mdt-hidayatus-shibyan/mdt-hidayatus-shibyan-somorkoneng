@section('title', 'Pembelian (Kulakan) Koperasi')
<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-20">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <div
                    class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0">
                    <i class="bi bi-bag-plus-fill text-lg"></i>
                </div>
                <span>Pembelian & Kulakan Barang</span>
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Pencatatan faktur pengadaan barang grosir, stok masuk supplier, dan hutang tempo koperasi
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto shrink-0 flex-wrap">
            <a href="{{ route('koperasi.pembelian.create') }}"
                class="m3-btn-primary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center justify-center gap-1.5 active:scale-95 shadow-md shadow-primary/20">
                <i class="bi bi-plus-circle-fill text-sm"></i>
                <span>Catat Pembelian Baru</span>
            </a>

            <a href="{{ route('koperasi.stok.index') }}"
                class="m3-btn-secondary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center justify-center gap-1.5 active:scale-95">
                <i class="bi bi-boxes text-sm text-primary"></i>
                <span>Mutasi Stok</span>
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 relative z-10">
        <!-- Belanja Bulan Ini -->
        <div class="m3-glass-card p-5 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Belanja
                    Bulan Ini</span>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white mt-1">
                    Rp {{ number_format($totalBelanjaBulanIni, 0, ',', '.') }}
                </h3>
                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5 flex items-center gap-1">
                    <i class="bi bi-calendar-check"></i> Periode {{ date('F Y') }}
                </p>
            </div>
            <div
                class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0 border border-emerald-500/20">
                <i class="bi bi-cart-check-fill"></i>
            </div>
        </div>

        <!-- Hutang ke Supplier -->
        <div class="m3-glass-card p-5 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Hutang
                    Supplier (Tempo)</span>
                <h3 class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">
                    Rp {{ number_format($totalHutangSupplier, 0, ',', '.') }}
                </h3>
                <p class="text-[10px] text-amber-700/80 dark:text-amber-400/80 font-bold mt-0.5">
                    {{ $countHutangSupplier }} Faktur Belum Lunas
                </p>
            </div>
            <div
                class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0 border border-amber-500/20">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <!-- Total Faktur Kulakan -->
        <div class="m3-glass-card p-5 flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Total
                    Transaksi Kulakan</span>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white mt-1">
                    {{ $totalTrxKulakan }} Faktur
                </h3>
                <p class="text-[10px] text-zinc-500 dark:text-zinc-400 font-semibold mt-0.5">
                    Total: Rp {{ number_format($totalNominalFiltered, 0, ',', '.') }}
                </p>
            </div>
            <div
                class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shrink-0 border border-blue-500/20">
                <i class="bi bi-receipt"></i>
            </div>
        </div>
    </div>

    <!-- MAIN CARD & FILTER -->
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
                        Riwayat Faktur Pengadaan
                    </h3>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                        Kelola data pembelian, faktur toko grosir, dan mutasi barang masuk
                    </p>
                </div>
            </div>

            <form action="{{ route('koperasi.pembelian.index') }}" method="GET"
                class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <!-- Filter Status Pembayaran -->
                <div class="w-full sm:w-40">
                    <select name="status_pembayaran" onchange="this.form.submit()"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Status Bayar</option>
                        <option value="Lunas" {{ request('status_pembayaran') == 'Lunas' ? 'selected' : '' }}>Lunas
                        </option>
                        <option value="Belum_Lunas"
                            {{ request('status_pembayaran') == 'Belum_Lunas' ? 'selected' : '' }}>Belum Lunas (Tempo)
                        </option>
                    </select>
                </div>

                <!-- Filter Metode Bayar -->
                <div class="w-full sm:w-36">
                    <select name="metode_pembayaran" onchange="this.form.submit()"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Metode</option>
                        <option value="Tunai_Kas" {{ request('metode_pembayaran') == 'Tunai_Kas' ? 'selected' : '' }}>
                            Tunai Kas</option>
                        <option value="Transfer_Bank"
                            {{ request('metode_pembayaran') == 'Transfer_Bank' ? 'selected' : '' }}>Transfer Bank
                        </option>
                        <option value="Hutang_Tempo"
                            {{ request('metode_pembayaran') == 'Hutang_Tempo' ? 'selected' : '' }}>Hutang (Tempo)
                        </option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="w-full sm:w-32">
                    <select name="status" onchange="this.form.submit()"
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan
                        </option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-48">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="No. faktur / supplier..."
                        class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs font-medium">
                </div>

                @if (request()->hasAny(['status_pembayaran', 'metode_pembayaran', 'status', 'search']))
                    <a href="{{ route('koperasi.pembelian.index') }}"
                        class="min-h-[40px] px-3 rounded-xl md:rounded-2xl bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-xs font-bold flex items-center justify-center transition-all shrink-0"
                        title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- TABEL PEMBELIAN -->
        <div class="overflow-x-auto">
            <table class="m3-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">No. Faktur</th>
                        <th class="text-left">Tanggal</th>
                        <th class="text-left">Supplier / Toko</th>
                        <th class="text-center">Jml Item</th>
                        <th class="text-right">Total Nominal</th>
                        <th class="text-left">Metode Bayar</th>
                        <th class="text-center">Status Bayar</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 font-medium text-xs">
                    @forelse ($pembelians as $p)
                        <tr
                            class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors {{ $p->status === 'Dibatalkan' ? 'opacity-60 bg-rose-500/5' : '' }}">
                            <td class="font-mono font-bold text-zinc-900 dark:text-white">
                                <a href="{{ route('koperasi.pembelian.show', $p->id) }}"
                                    class="text-primary hover:underline inline-flex items-center gap-1.5">
                                    <i class="bi bi-receipt-cutoff text-xs"></i>
                                    <span>{{ $p->nomor_faktur }}</span>
                                </a>
                                @if ($p->foto_faktur)
                                    <span class="inline-block ml-1 text-zinc-400" title="Ada Lampiran Nota Fisik">
                                        <i class="bi bi-paperclip"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="text-zinc-500 dark:text-zinc-400 text-[11px] whitespace-nowrap">
                                {{ $p->tanggal->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="font-bold text-zinc-900 dark:text-white">
                                    {{ $p->supplier }}
                                </div>
                                @if ($p->nomor_faktur_supplier)
                                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono">
                                        Ref: #{{ $p->nomor_faktur_supplier }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center font-bold text-zinc-800 dark:text-zinc-200">
                                {{ $p->total_item }}
                            </td>
                            <td class="text-right font-mono font-black text-zinc-900 dark:text-white">
                                Rp {{ number_format($p->total_nominal, 0, ',', '.') }}
                            </td>
                            <td>
                                @if ($p->metode_pembayaran === 'Tunai_Kas')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                        <i class="bi bi-cash"></i> Tunai Kas
                                    </span>
                                @elseif ($p->metode_pembayaran === 'Transfer_Bank')
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20">
                                        <i class="bi bi-bank"></i> Transfer Bank
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                        <i class="bi bi-clock-history"></i> Hutang Tempo
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($p->status_pembayaran === 'Lunas')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                        Lunas
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20"
                                        title="Sisa: Rp {{ number_format($p->sisa_hutang, 0, ',', '.') }}">
                                        Tempo (Rp {{ number_format($p->sisa_hutang, 0, ',', '.') }})
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($p->status === 'Selesai')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                        Selesai
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    @if ($p->status === 'Selesai' && $p->status_pembayaran === 'Belum_Lunas')
                                        <a href="{{ route('koperasi.pembelian.show', $p->id) }}"
                                            class="px-2.5 py-1 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold shadow-2xs inline-flex items-center gap-1 transition-all active:scale-95"
                                            title="Lunasi Hutang Supplier Ini">
                                            <i class="bi bi-wallet2"></i>
                                            <span>Lunasi</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('koperasi.pembelian.show', $p->id) }}"
                                        class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-primary hover:text-white dark:hover:bg-primary-dark dark:hover:text-zinc-950 flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                        title="Detail Faktur">
                                        <i class="bi bi-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('koperasi.pembelian.cetak', $p->id) }}" target="_blank"
                                        class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                        title="Cetak Bukti Faktur">
                                        <i class="bi bi-printer text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-zinc-400 dark:text-zinc-500 text-xs">
                                <i class="bi bi-bag-x text-3xl mb-2 block opacity-40"></i>
                                Belum ada riwayat transaksi pembelian (kulakan).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pembelians->hasPages())
            <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-950/30">
                {{ $pembelians->links('vendor.pagination.custom') }}
            </div>
        @endif

    </div>

</x-app-layout>
