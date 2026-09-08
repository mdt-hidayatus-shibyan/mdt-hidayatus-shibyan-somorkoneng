@section('title', 'Detail Nota Penjualan #' . $penjualan->nomor_nota)
<x-app-layout>

    <div class="max-w-4xl mx-auto mb-8" x-data="pelunasanApp({{ json_encode([
        'id' => $penjualan->id,
        'nomor_nota' => $penjualan->nomor_nota,
        'total_akhir' => (float) $penjualan->total_akhir,
        'jenis_pelanggan' => $penjualan->jenis_pelanggan,
        'nama_pelanggan' => $penjualan->nama_pelanggan,
        'ada_tabungan' => (bool) $penjualan->tabungan,
        'saldo_tabungan' => $penjualan->tabungan ? (float) $penjualan->tabungan->saldo : 0.0,
        'saldo_tabungan_format' => $penjualan->tabungan
            ? 'Rp ' . number_format($penjualan->tabungan->saldo, 0, ',', '.')
            : 'Rp 0',
        'is_lunas' => $penjualan->status_pembayaran === 'Lunas',
        'status_pembayaran' => $penjualan->status_pembayaran,
    ]) }})">

        <!-- Back Link & Action Buttons -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-20">
            <a href="{{ route('koperasi.transaksi.index') }}"
                class="inline-flex items-center gap-2 text-xs font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                <i class="bi bi-arrow-left text-sm"></i>
                <span>Kembali ke Riwayat Transaksi</span>
            </a>

            <div class="flex items-center gap-2 flex-wrap">
                @if (
                    $penjualan->metode_pembayaran === 'Hutang' &&
                        $penjualan->status === 'Selesai' &&
                        $penjualan->status_pembayaran === 'Belum_Lunas')
                    <button type="button" @click="openModal()"
                        class="min-h-[40px] px-4 py-2 rounded-xl md:rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs inline-flex items-center gap-1.5 active:scale-95 shadow-md shadow-amber-500/20 transition-all">
                        <i class="bi bi-credit-card-2-front-fill text-sm"></i>
                        <span>Pelunasan Hutang</span>
                    </button>
                @endif

                <a href="{{ route('koperasi.kasir.struk', $penjualan->id) }}" target="_blank"
                    class="m3-btn-secondary min-h-[40px] px-4 py-2 text-xs font-bold inline-flex items-center gap-1.5 active:scale-95 shadow-2xs">
                    <i class="bi bi-printer text-sm"></i>
                    <span>Cetak Struk Thermal</span>
                </a>

                @if ($penjualan->status === 'Selesai')
                    <form action="{{ route('koperasi.transaksi.batal', $penjualan->id) }}" method="POST"
                        class="delete-ajax inline" data-refresh-target="#viewContainer">
                        @csrf
                        <input type="hidden" name="alasan" value="Pembatalan oleh Administrator">
                        <button type="submit"
                            class="min-h-[40px] px-3.5 py-2 rounded-xl md:rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 border border-rose-500/20 text-xs font-bold transition-all inline-flex items-center gap-1.5 active:scale-95"
                            title="Batalkan Nota Transaksi">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>Batalkan Transaksi</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- NOTIFIKASI BANNER STATUS HUTANG / PELUNASAN -->
        @if ($penjualan->metode_pembayaran === 'Hutang')
            @if ($penjualan->status_pembayaran === 'Belum_Lunas' && $penjualan->status === 'Selesai')
                <div
                    class="mb-6 p-4 md:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg font-black shrink-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-amber-800 dark:text-amber-300">
                                Nota Belum Lunas (Tercatat sebagai Hutang / Piutang)
                            </h4>
                            <p class="text-xs text-amber-700/80 dark:text-amber-400/80 font-medium mt-0.5">
                                Pelanggan memiliki tagihan piutang sebesar <strong>Rp
                                    {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</strong>
                                @if ($penjualan->catatan)
                                    • <em>{{ $penjualan->catatan }}</em>
                                @endif
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="openModal()"
                        class="min-h-[38px] px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold inline-flex items-center gap-1.5 shadow-sm active:scale-95 transition-all shrink-0">
                        <i class="bi bi-wallet2"></i>
                        <span>Lunasi Sekarang</span>
                    </button>
                </div>
            @elseif ($penjualan->status_pembayaran === 'Lunas')
                <div
                    class="mb-6 p-4 md:p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center gap-3.5">
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg font-black shrink-0 shadow-sm">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-emerald-800 dark:text-emerald-300">
                            Hutang Telah Dilunasi (LUNAS)
                        </h4>
                        <p class="text-xs text-emerald-700/80 dark:text-emerald-400/80 font-medium mt-0.5">
                            Dilunasi pada
                            <strong>{{ $penjualan->tanggal_pelunasan ? $penjualan->tanggal_pelunasan->format('d/m/Y H:i') : '-' }}</strong>
                            via <strong>{{ str_replace('_', ' ', $penjualan->metode_pelunasan ?? 'Tunai') }}</strong>
                            • Kasir Penerima:
                            <strong>{{ $penjualan->petugasPelunasan?->name ?? ($penjualan->petugas?->name ?? 'Petugas') }}</strong>
                            @if ($penjualan->catatan_pelunasan)
                                • <span class="italic">{{ $penjualan->catatan_pelunasan }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endif
        @endif

        <!-- MAIN CARD NOTA -->
        <div class="m3-glass-card p-5 sm:p-7 md:p-8 shadow-sm dark:shadow-none space-y-6 relative z-10">

            <!-- HEADER NOTA -->
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-200/80 dark:border-zinc-800/80">
                <div class="flex items-center gap-3.5">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 shrink-0">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3
                                class="text-lg md:text-xl font-black text-zinc-900 dark:text-white font-mono tracking-tight">
                                {{ $penjualan->nomor_nota }}
                            </h3>
                            @if ($penjualan->status === 'Selesai')
                                @if ($penjualan->status_pembayaran === 'Belum_Lunas')
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                        Belum Lunas (Hutang)
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                        Selesai (Lunas)
                                    </span>
                                @endif
                            @else
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                    Dibatalkan
                                </span>
                            @endif
                        </div>
                        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Waktu: {{ $penjualan->tanggal->format('d F Y, H:i') }} WIB • Kasir Pembuat:
                            {{ $penjualan->petugas?->name ?? 'Sistem' }}
                        </p>
                    </div>
                </div>

                <div class="text-left sm:text-right">
                    <span
                        class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block">
                        Total Tagihan
                    </span>
                    <span class="text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                        Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- INFO PELANGGAN & PEMBAYARAN -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80">
                <div>
                    <span
                        class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">
                        Data Pelanggan
                    </span>
                    <h5 class="text-sm font-black text-zinc-900 dark:text-white mt-1">
                        {{ $penjualan->nama_pelanggan }}
                    </h5>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-medium">
                        Kategori: <strong
                            class="text-zinc-700 dark:text-zinc-300">{{ $penjualan->jenis_pelanggan }}</strong>
                        @if ($penjualan->murid && $penjualan->murid->ruangans->first())
                            • Kelas: {{ $penjualan->murid->ruangans->first()->nama_ruangan }}
                        @endif
                        @if ($penjualan->tabungan)
                            • Saldo Tabungan: <strong class="text-indigo-600 dark:text-indigo-400">Rp
                                {{ number_format($penjualan->tabungan->saldo, 0, ',', '.') }}</strong>
                        @endif
                    </p>
                </div>

                <div>
                    <span
                        class="text-[10px] font-extrabold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">
                        Rincian Pembayaran
                    </span>
                    <p class="text-xs font-bold text-zinc-800 dark:text-zinc-200 mt-1">
                        Metode: <span
                            class="text-primary dark:text-primary-dark font-black">{{ $penjualan->metode_pembayaran === 'Hutang' ? 'Bayar Nanti / Hutang' : str_replace('_', ' ', $penjualan->metode_pembayaran) }}</span>
                        @if ($penjualan->status_pembayaran === 'Belum_Lunas')
                            <span
                                class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">Belum
                                Lunas</span>
                        @else
                            <span
                                class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">Lunas</span>
                        @endif
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-medium">
                        @if ($penjualan->status_pembayaran === 'Belum_Lunas')
                            Nominal Piutang: Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}
                            @if ($penjualan->catatan)
                                • <span class="italic text-amber-600 dark:text-amber-400">Catatan:
                                    {{ $penjualan->catatan }}</span>
                            @endif
                        @else
                            Nominal Bayar: Rp {{ number_format($penjualan->nominal_bayar, 0, ',', '.') }}
                            @if ($penjualan->kembalian > 0)
                                • Kembalian: Rp {{ number_format($penjualan->kembalian, 0, ',', '.') }}
                            @endif
                            @if ($penjualan->metode_pelunasan)
                                • Dilunasi via:
                                <strong>{{ str_replace('_', ' ', $penjualan->metode_pelunasan) }}</strong>
                            @endif
                        @endif
                    </p>
                </div>
            </div>

            <!-- TABEL ITEM BELANJA -->
            <div class="space-y-2.5">
                <h4
                    class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-basket-fill text-primary"></i>
                    <span>Rincian Item Pembelian</span>
                </h4>

                <div class="overflow-x-auto rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80">
                    <table class="m3-table w-full">
                        <thead>
                            <tr>
                                <th class="text-center w-12">No</th>
                                <th class="text-left">Kode Item</th>
                                <th class="text-left">Nama Produk / Paket</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Diskon</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 font-medium text-xs">
                            @foreach ($penjualan->details as $idx => $d)
                                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="text-center text-zinc-400 font-bold">{{ $idx + 1 }}</td>
                                    <td class="font-mono font-bold text-zinc-900 dark:text-white">
                                        {{ $d->kode_item }}
                                    </td>
                                    <td>
                                        <div class="font-bold text-zinc-900 dark:text-white">
                                            {{ $d->nama_item }}
                                            @if ($d->tipe_item === 'Paket_Bundling')
                                                <span
                                                    class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-black bg-indigo-600 text-white uppercase">Paket</span>
                                            @endif
                                        </div>
                                        @if ($d->tipe_item === 'Paket_Bundling' && !empty($d->rincian_paket_json))
                                            <div class="mt-1 space-y-0.5 text-[10px] text-zinc-500 dark:text-zinc-400">
                                                @foreach ($d->rincian_paket_json as $komp)
                                                    <div>• {{ $komp['jumlah_per_paket'] }}x {{ $komp['nama_produk'] }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center font-bold text-zinc-800 dark:text-zinc-200">
                                        {{ $d->jumlah }} {{ $d->satuan }}
                                    </td>
                                    <td class="text-right font-mono text-zinc-700 dark:text-zinc-300">
                                        Rp {{ number_format($d->harga_jual, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right font-mono text-rose-500 font-semibold">
                                        {{ $d->diskon_item > 0 ? '- Rp ' . number_format($d->diskon_item, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-right font-mono font-black text-zinc-900 dark:text-white">
                                        Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot
                            class="bg-zinc-50/80 dark:bg-zinc-900/80 font-bold border-t border-zinc-200/80 dark:border-zinc-800/80 text-xs">
                            <tr>
                                <td colspan="6"
                                    class="py-3 px-4 text-right uppercase text-[11px] text-zinc-500 dark:text-zinc-400">
                                    Subtotal Belanja
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-zinc-900 dark:text-white font-bold">
                                    Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if ($penjualan->diskon > 0)
                                <tr>
                                    <td colspan="6"
                                        class="py-2.5 px-4 text-right uppercase text-[11px] text-zinc-500 dark:text-zinc-400">
                                        Diskon Transaksi
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono text-rose-500 font-bold">
                                        - Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="text-sm font-black">
                                <td colspan="6"
                                    class="py-3.5 px-4 text-right uppercase text-zinc-900 dark:text-white tracking-tight">
                                    TOTAL AKHIR
                                </td>
                                <td
                                    class="py-3.5 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400 text-base">
                                    Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- MODAL PELUNASAN HUTANG MATERIAL 3 (GLASSMORPHISM) -->
        <!-- ============================================================== -->
        <div x-show="showModal" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div class="m3-glass-card w-full max-w-lg overflow-hidden shadow-2xl p-6 relative"
                @click.away="closeModal()">

                <!-- Header Modal -->
                <div
                    class="flex items-center justify-between pb-4 border-b border-zinc-200/80 dark:border-zinc-800/80">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg font-bold border border-amber-500/20">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white">
                                Pelunasan Hutang Koperasi
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                Nota: {{ $penjualan->nomor_nota }} • {{ $penjualan->nama_pelanggan }}
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()"
                        class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center justify-center">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>

                <!-- Form Pelunasan -->
                <form action="{{ route('koperasi.transaksi.lunasi', $penjualan->id) }}" method="POST"
                    @submit.prevent="submitPelunasan($event)" class="mt-5 space-y-4">
                    @csrf

                    <!-- Card Total Tagihan -->
                    <div
                        class="p-4 rounded-2xl bg-zinc-100/80 dark:bg-zinc-900/80 border border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase text-zinc-400 tracking-wider block">Total
                                Tagihan Hutang</span>
                            <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Harus Dibayar</span>
                        </div>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                            Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Pilih Metode Pembayaran Pelunasan -->
                    <div>
                        <label
                            class="text-[11px] font-extrabold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-2 block">
                            Pilih Metode Pelunasan
                        </label>
                        <div class="grid grid-cols-3 gap-2.5">
                            <!-- Tunai -->
                            <button type="button" @click="metode = 'Tunai'"
                                :class="metode === 'Tunai' ?
                                    'border-emerald-500 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 ring-2 ring-emerald-500/20' :
                                    'border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60'"
                                class="p-3 rounded-2xl border text-center transition-all flex flex-col items-center gap-1">
                                <i class="bi bi-cash text-lg"></i>
                                <span class="text-xs font-bold">Tunai</span>
                            </button>

                            <!-- Potong Tabungan -->
                            <button type="button" @click="metode = 'Potong_Tabungan'"
                                :class="metode === 'Potong_Tabungan' ?
                                    'border-purple-500 bg-purple-500/10 text-purple-700 dark:text-purple-400 ring-2 ring-purple-500/20' :
                                    'border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60'"
                                class="p-3 rounded-2xl border text-center transition-all flex flex-col items-center gap-1">
                                <i class="bi bi-wallet2 text-lg"></i>
                                <span class="text-xs font-bold">Tabungan</span>
                            </button>

                            <!-- QRIS / Transfer -->
                            <button type="button" @click="metode = 'QRIS'"
                                :class="metode === 'QRIS' ?
                                    'border-blue-500 bg-blue-500/10 text-blue-700 dark:text-blue-400 ring-2 ring-blue-500/20' :
                                    'border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/60'"
                                class="p-3 rounded-2xl border text-center transition-all flex flex-col items-center gap-1">
                                <i class="bi bi-qr-code text-lg"></i>
                                <span class="text-xs font-bold">QRIS / TF</span>
                            </button>
                        </div>
                        <input type="hidden" name="metode_pelunasan" :value="metode">
                    </div>

                    <!-- Input Detail Berdasarkan Metode -->
                    <template x-if="metode === 'Tunai'">
                        <div
                            class="space-y-3 p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800/80">
                            <div>
                                <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 mb-1 block">
                                    Uang Tunai Diterima (Rp)
                                </label>
                                <input type="number" name="nominal_bayar" x-model.number="nominalBayar"
                                    min="{{ $penjualan->total_akhir }}" step="500"
                                    class="m3-input-glass w-full min-h-[42px] px-3.5 text-sm font-mono font-bold"
                                    placeholder="Masukkan jumlah uang...">
                            </div>

                            <!-- Preset Nominal -->
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" @click="nominalBayar = {{ $penjualan->total_akhir }}"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-emerald-500 hover:text-white transition-colors">
                                    Uang Pas (Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }})
                                </button>
                                <button type="button"
                                    @click="nominalBayar = Math.ceil({{ $penjualan->total_akhir }} / 50000) * 50000"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-emerald-500 hover:text-white transition-colors">
                                    Rp <span
                                        x-text="formatRupiah(Math.ceil({{ $penjualan->total_akhir }} / 50000) * 50000)"></span>
                                </button>
                                <button type="button"
                                    @click="nominalBayar = Math.ceil({{ $penjualan->total_akhir }} / 100000) * 100000"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-zinc-200/80 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-emerald-500 hover:text-white transition-colors">
                                    Rp <span
                                        x-text="formatRupiah(Math.ceil({{ $penjualan->total_akhir }} / 100000) * 100000)"></span>
                                </button>
                            </div>

                            <!-- Kembalian Info -->
                            <div
                                class="flex items-center justify-between pt-2 border-t border-zinc-200/60 dark:border-zinc-800/60">
                                <span class="text-xs font-bold text-zinc-500">Kembalian:</span>
                                <span class="text-sm font-black font-mono"
                                    :class="kembalian >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'"
                                    x-text="kembalian >= 0 ? 'Rp ' + formatRupiah(kembalian) : 'Uang Kurang'">
                                </span>
                            </div>
                        </div>
                    </template>

                    <template x-if="metode === 'Potong_Tabungan'">
                        <div class="p-3.5 rounded-2xl bg-purple-500/10 border border-purple-500/20 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-purple-900 dark:text-purple-300">
                                    Saldo Tabungan Pelanggan:
                                </span>
                                <span class="text-sm font-black font-mono text-purple-700 dark:text-purple-400"
                                    x-text="pelangganData.saldo_tabungan_format">
                                </span>
                            </div>
                            <template x-if="!pelangganData.ada_tabungan">
                                <p class="text-[11px] font-medium text-rose-600 dark:text-rose-400">
                                    ⚠️ Pelanggan ini belum memiliki rekening tabungan aktif di sistem madrasah.
                                </p>
                            </template>
                            <template
                                x-if="pelangganData.ada_tabungan && pelangganData.saldo_tabungan < {{ $penjualan->total_akhir }}">
                                <p class="text-[11px] font-medium text-rose-600 dark:text-rose-400">
                                    ⚠️ Saldo tabungan tidak mencukupi untuk melunasi tagihan ini.
                                </p>
                            </template>
                            <template
                                x-if="pelangganData.ada_tabungan && pelangganData.saldo_tabungan >= {{ $penjualan->total_akhir }}">
                                <p class="text-[11px] font-medium text-emerald-700 dark:text-emerald-400">
                                    ✅ Saldo mencukupi. Saldo akan otomatis terpotong Rp
                                    {{ number_format($penjualan->total_akhir, 0, ',', '.') }}.
                                </p>
                            </template>
                        </div>
                    </template>

                    <template x-if="metode === 'QRIS'">
                        <div
                            class="p-3.5 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-xs font-medium text-blue-800 dark:text-blue-300">
                            Pastikan pembayaran digital QRIS / Transfer sebesar <strong>Rp
                                {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</strong> telah berhasil
                            diverifikasi oleh kasir.
                        </div>
                    </template>

                    <!-- Catatan Pelunasan -->
                    <div>
                        <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 mb-1 block">
                            Catatan Pelunasan (Opsional)
                        </label>
                        <input type="text" name="catatan_pelunasan" x-model="catatan"
                            placeholder="Contoh: Dilunasi oleh wali murid / tunai di koperasi..."
                            class="m3-input-glass w-full min-h-[40px] px-3.5 text-xs">
                    </div>

                    <!-- Tombol Aksi Modal -->
                    <div
                        class="pt-3 flex items-center justify-end gap-2.5 border-t border-zinc-200/80 dark:border-zinc-800/80">
                        <button type="button" @click="closeModal()"
                            class="m3-btn-secondary px-4 py-2 text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmitting || !isValid()"
                            class="m3-btn-primary px-5 py-2 text-xs font-bold inline-flex items-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting"><i class="bi bi-check2-circle"></i> Konfirmasi &
                                Lunasi</span>
                            <span x-show="isSubmitting"><i class="bi bi-arrow-repeat animate-spin"></i>
                                Memproses...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pelunasanApp', (config) => ({
                    showModal: false,
                    pelangganData: config,
                    metode: 'Tunai',
                    nominalBayar: config.total_akhir,
                    catatan: '',
                    isSubmitting: false,

                    get kembalian() {
                        return this.nominalBayar - config.total_akhir;
                    },

                    openModal() {
                        this.showModal = true;
                        this.nominalBayar = config.total_akhir;
                    },

                    closeModal() {
                        this.showModal = false;
                    },

                    isValid() {
                        if (this.metode === 'Tunai') {
                            return this.nominalBayar >= config.total_akhir;
                        }
                        if (this.metode === 'Potong_Tabungan') {
                            return this.pelangganData.ada_tabungan && (this.pelangganData.saldo_tabungan >=
                                config.total_akhir);
                        }
                        return true;
                    },

                    formatRupiah(number) {
                        return new Intl.NumberFormat('id-ID').format(number);
                    },

                    async submitPelunasan(event) {
                        if (!this.isValid()) return;
                        this.isSubmitting = true;

                        const form = event.target;
                        const formData = new FormData(form);

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });

                            const res = await response.json();

                            if (res.success) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pelunasan Berhasil!',
                                        text: res.message ||
                                            'Nota hutang telah berhasil dilunasi.',
                                        timer: 1800,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    alert(res.message || 'Pelunasan berhasil!');
                                    window.location.reload();
                                }
                            } else {
                                throw new Error(res.message || 'Gagal memproses pelunasan.');
                            }
                        } catch (error) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Pelunasan',
                                    text: error.message
                                });
                            } else {
                                alert(error.message);
                            }
                        } finally {
                            this.isSubmitting = false;
                        }
                    }
                }));
            });
        </script>
    @endpush

</x-app-layout>
