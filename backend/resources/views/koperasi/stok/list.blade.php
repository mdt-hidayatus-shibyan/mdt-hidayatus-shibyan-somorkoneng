<!-- TABEL MUTASI STOK -->
<div class="overflow-x-auto custom-scrollbar">
    <table class="m3-table w-full">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Produk / Kitab</th>
                <th>Jenis Mutasi</th>
                <th class="text-center">Stok Awal</th>
                <th class="text-center">Jumlah</th>
                <th class="text-center">Stok Akhir</th>
                <th>Referensi & Keterangan</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasis as $m)
                <tr>
                    <td class="text-zinc-500 dark:text-zinc-400 text-[11px] whitespace-nowrap">
                        {{ $m->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        <div class="font-bold text-zinc-900 dark:text-white text-xs">
                            {{ $m->produk?->nama_produk ?? '-' }}
                        </div>
                        <span class="text-[10px] text-zinc-400 font-mono">
                            {{ $m->produk?->kode_produk }}
                        </span>
                    </td>
                    <td>
                        @if ($m->jenis_mutasi === 'Stok_Masuk')
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
                            </span>
                        @elseif ($m->jenis_mutasi === 'Penjualan' || $m->jenis_mutasi === 'Penjualan_Paket')
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20">
                                <i class="bi bi-cart-check"></i>
                                {{ str_replace('_', ' ', $m->jenis_mutasi) }}
                            </span>
                        @elseif ($m->jenis_mutasi === 'Penyesuaian_Opname')
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                <i class="bi bi-clipboard-check"></i> Opname
                            </span>
                        @elseif ($m->jenis_mutasi === 'Batal_Penjualan')
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/20">
                                <i class="bi bi-arrow-counterclockwise"></i> Void/Batal
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                                {{ str_replace('_', ' ', $m->jenis_mutasi) }}
                            </span>
                        @endif
                    </td>
                    <td class="text-center font-mono text-xs">
                        {{ $m->stok_sebelum }}
                    </td>
                    <td
                        class="text-center font-mono font-black text-xs {{ $m->jumlah > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $m->jumlah > 0 ? '+' . $m->jumlah : $m->jumlah }}
                    </td>
                    <td class="text-center font-mono font-black text-xs text-zinc-900 dark:text-white">
                        {{ $m->stok_sesudah }}
                    </td>
                    <td>
                        <div class="text-zinc-900 dark:text-zinc-200 font-bold text-xs">
                            {{ $m->referensi ?: '-' }}
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate max-w-xs">
                            {{ $m->keterangan }}
                        </p>
                    </td>
                    <td class="text-zinc-500 dark:text-zinc-400 text-[11px]">
                        {{ $m->petugas?->name ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-zinc-400 text-xs font-semibold">
                        <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                        Belum ada catatan mutasi stok.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($mutasis->hasPages())
    <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/30">
        {{ $mutasis->links() }}
    </div>
@endif
