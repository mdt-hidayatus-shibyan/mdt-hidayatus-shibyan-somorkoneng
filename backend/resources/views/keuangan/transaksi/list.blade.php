<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr
                class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 text-[11px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                <th class="py-3.5 px-4">Tanggal & No. Ref</th>
                <th class="py-3.5 px-4">Kode Transaksi</th>
                <th class="py-3.5 px-4">Kategori & Pos Akun</th>
                <th class="py-3.5 px-4">Keterangan</th>
                <th class="py-3.5 px-4">Metode</th>
                <th class="py-3.5 px-4 text-right">Nominal</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
            @forelse ($transaksis as $trx)
                <tr
                    class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors group {{ $trx->status === 'batal' ? 'opacity-50' : '' }}">
                    <td class="py-3.5 px-4">
                        <span class="font-bold text-zinc-900 dark:text-white block">
                            {{ $trx->tanggal_transaksi->format('d/m/Y') }}
                        </span>
                        <span class="text-[11px] text-zinc-400 font-mono">
                            {{ $trx->nomor_referensi ?? '-' }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-700 dark:text-zinc-300">
                        {{ $trx->kode_transaksi }}
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="font-bold text-zinc-900 dark:text-white">
                            {{ $trx->kategoriKeuangan->nama_kategori ?? 'Umum / Mutasi' }}
                        </div>
                        <div class="text-[11px] text-zinc-400 flex items-center gap-1.5 mt-0.5">
                            <i class="bi bi-wallet2"></i>
                            <span>{{ $trx->akunKeuangan->nama_akun }}</span>
                            @if ($trx->jenis_transaksi === 'mutasi' && $trx->akunTujuan)
                                <i class="bi bi-arrow-right text-xs"></i>
                                <span>{{ $trx->akunTujuan->nama_akun }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-3.5 px-4 max-w-xs">
                        <p class="text-zinc-600 dark:text-zinc-300 truncate" title="{{ $trx->keterangan }}">
                            {{ $trx->keterangan ?? '-' }}
                        </p>
                        <span class="text-[10px] text-zinc-400 block">Oleh: {{ $trx->user->name ?? 'Sistem' }}</span>
                    </td>
                    <td class="py-3.5 px-4">
                        @if ($trx->metode_pembayaran === 'transfer_bank')
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 inline-flex items-center gap-1">
                                <i class="bi bi-bank text-[10px]"></i>
                                <span>{{ $trx->bank->nama_bank ?? 'Bank' }}</span>
                            </span>
                        @else
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 inline-flex items-center gap-1">
                                <i class="bi bi-cash text-[10px]"></i>
                                <span>Tunai</span>
                            </span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-right font-black text-sm font-mono">
                        @if ($trx->jenis_transaksi === 'pemasukan' || $trx->jenis_transaksi === 'simpanan')
                            <span class="text-emerald-600 dark:text-emerald-400">
                                + Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                            </span>
                        @elseif($trx->jenis_transaksi === 'pengeluaran')
                            <span class="text-rose-600 dark:text-rose-400">
                                - Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-blue-600 dark:text-blue-400">
                                ⇄ Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        @if ($trx->status === 'sukses')
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                Sukses
                            </span>
                        @elseif($trx->status === 'batal')
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                Batal
                            </span>
                        @else
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                Draft
                            </span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button type="button" onclick="openDetailTransaksiModal({{ $trx->id }})"
                                class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 flex items-center justify-center transition-colors"
                                title="Detail Transaksi">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            <a href="{{ route('keuangan.transaksi.cetak-kwitansi', $trx->id) }}" target="_blank"
                                class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 hover:bg-purple-500/20 flex items-center justify-center transition-colors"
                                title="Cetak Kwitansi">
                                <i class="bi bi-printer-fill"></i>
                            </a>
                            @if ($trx->status === 'sukses')
                                <button type="button"
                                    onclick="confirmBatalTransaksi({{ $trx->id }}, '{{ $trx->kode_transaksi }}')"
                                    class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 flex items-center justify-center transition-colors"
                                    title="Batalkan Transaksi">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-zinc-400 dark:text-zinc-500">
                        <i class="bi bi-journal-text text-3xl mb-2 block"></i>
                        <p class="font-bold">Tidak ada transaksi ditemukan pada periode ini.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
    {{ $transaksis->links('vendor.pagination.custom') }}
</div>
