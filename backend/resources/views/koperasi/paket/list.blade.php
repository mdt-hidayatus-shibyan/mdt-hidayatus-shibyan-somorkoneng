<!-- TABEL DATA PAKET -->
<div class="overflow-x-auto custom-scrollbar">
    <table class="m3-table w-full">
        <thead>
            <tr>
                <th>Kode Paket</th>
                <th>Nama Paket & Peruntukan</th>
                <th>Rincian Komponen Kitab / Barang</th>
                <th class="text-right">HPP Komponen</th>
                <th class="text-right">Harga Jual Paket</th>
                <th class="text-center">Status</th>
                <th class="text-center w-28">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pakets as $p)
                <tr>
                    <td class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-xs">
                        {{ $p->kode_paket }}
                    </td>
                    <td>
                        <div class="flex items-center gap-3">
                            @if ($p->foto)
                                <img src="{{ $p->foto_url }}" alt="{{ $p->nama_paket }}"
                                    class="w-10 h-10 rounded-xl object-cover border border-indigo-500/20 shadow-2xs shrink-0">
                            @else
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20 shrink-0">
                                    <i class="bi bi-collection-fill text-base"></i>
                                </div>
                            @endif
                            <div>
                                <div class="font-bold text-zinc-900 dark:text-white text-xs">
                                    {{ $p->nama_paket }}
                                </div>
                                <span
                                    class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-500/20">
                                    <i class="bi bi-mortarboard-fill"></i>
                                    <span>{{ $p->level?->nama_level ?? ($p->tingkat?->nama_tingkat ?? 'Semua Kelas') }}</span>
                                </span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="space-y-1">
                            @foreach ($p->items as $item)
                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                    <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ $item->jumlah }}x</span>
                                    <span
                                        class="text-zinc-600 dark:text-zinc-400 truncate max-w-xs">{{ $item->produk?->nama_produk ?? '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="text-right font-mono text-zinc-500 dark:text-zinc-400 text-xs">
                        Rp {{ number_format($p->total_hpp_komponen, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-xs">
                        Rp {{ number_format($p->harga_paket, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <x-toggle-status :is-active="$p->is_active" :url="route('koperasi.paket.toggle-status', $p->id)" />
                    </td>
                    <td class="text-center">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('koperasi.paket.edit', $p->id) }}"
                                class="action-modal w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-primary hover:text-white flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                title="Edit Paket">
                                <i class="bi bi-pencil-square text-xs"></i>
                            </a>
                            <form action="{{ route('koperasi.paket.destroy', $p->id) }}" method="POST"
                                class="delete-ajax inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                    title="Hapus Paket">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-zinc-400 text-xs font-semibold">
                        <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                        Belum ada data paket bundling koperasi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($pakets->hasPages())
    <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/30">
        {{ $pakets->links('vendor.pagination.custom') }}
    </div>
@endif
