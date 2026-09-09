<!-- TABEL DATA PRODUK -->
<div class="overflow-x-auto custom-scrollbar">
    <table class="m3-table w-full">
        <thead>
            <tr>
                <th>Barcode / SKU</th>
                <th>Nama Produk & Kitab</th>
                <th>Kategori</th>
                <th class="text-right">Harga Pokok (HPP)</th>
                <th class="text-right">Harga Jual</th>
                <th class="text-center">Stok</th>
                <th class="text-center">Status</th>
                <th class="text-center w-28">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produks as $p)
                <tr>
                    <td class="font-mono font-bold text-zinc-900 dark:text-white text-xs">
                        <div class="flex items-center gap-1.5">
                            <span
                                class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                                {{ $p->kode_produk }}
                            </span>
                            <a href="{{ route('koperasi.produk.barcode-single', $p->id) }}"
                                class="action-modal text-purple-600 hover:text-purple-700 text-xs"
                                title="Cetak Barcode Stiker">
                                <i class="bi bi-upc-scan"></i>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-3">
                            @if ($p->foto)
                                <img src="{{ $p->foto_url }}" alt="{{ $p->nama_produk }}"
                                    class="w-10 h-10 rounded-xl object-cover border border-zinc-200 dark:border-zinc-700 shadow-2xs shrink-0">
                            @else
                                <div
                                    class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shrink-0">
                                    <i class="bi bi-box-seam text-base"></i>
                                </div>
                            @endif
                            <div>
                                <div class="font-bold text-zinc-900 dark:text-white text-xs">
                                    {{ $p->nama_produk }}
                                </div>
                                <span class="text-[10px] text-zinc-400 font-semibold">
                                    Satuan: {{ $p->satuan }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                            {{ $p->kategori?->nama_kategori ?? '-' }}
                        </span>
                    </td>
                    <td class="text-right font-mono text-zinc-500 dark:text-zinc-400 text-xs">
                        Rp {{ number_format($p->harga_beli, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-xs">
                        Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-black {{ $p->stok <= 0 ? 'bg-rose-500/10 text-rose-600 border border-rose-500/20' : ($p->stok <= $p->stok_minimum ? 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20') }}">
                            {{ $p->stok }} {{ $p->satuan }}
                        </span>
                    </td>
                    <td class="text-center">
                        <x-toggle-status :is-active="$p->status === 'Aktif'" :url="route('koperasi.produk.toggle-status', $p->id)" />
                    </td>
                    <td class="text-center">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('koperasi.produk.barcode-single', $p->id) }}"
                                class="action-modal w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 hover:bg-purple-600 hover:text-white flex items-center justify-center transition-all border border-purple-500/20 shadow-2xs"
                                title="Cetak Barcode SKU">
                                <i class="bi bi-upc-scan text-xs"></i>
                            </a>
                            <a href="{{ route('koperasi.produk.edit', $p->id) }}"
                                class="action-modal w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-primary hover:text-white flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                title="Edit Produk">
                                <i class="bi bi-pencil-square text-xs"></i>
                            </a>
                            <form action="{{ route('koperasi.produk.destroy', $p->id) }}" method="POST"
                                class="delete-ajax inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                    title="Hapus Produk">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-zinc-400 text-xs font-semibold">
                        <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                        Belum ada data produk koperasi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($produks->hasPages())
    <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/30">
        {{ $produks->links() }}
    </div>
@endif
