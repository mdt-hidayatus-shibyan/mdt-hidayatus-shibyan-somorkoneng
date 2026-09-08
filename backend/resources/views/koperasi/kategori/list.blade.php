<!-- Toolbar Header Table -->
<div class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/80 dark:bg-zinc-950/70 flex justify-between items-center">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20 shrink-0">
            <i class="bi bi-tag-fill text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                Daftar Kategori Produk
            </h3>
            <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                Total {{ $kategoris->count() }} Kategori Terdaftar
            </p>
        </div>
    </div>

    <a href="{{ route('koperasi.kategori.create') }}"
        class="action-modal min-h-[38px] px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm active:scale-95 transition-all">
        <i class="bi bi-plus-lg"></i>
        <span>Kategori Baru</span>
    </a>
</div>

<!-- Table -->
<div class="overflow-x-auto">
    <table class="m3-table w-full">
        <thead>
            <tr>
                <th class="text-center w-12">No</th>
                <th class="text-left">Nama Kategori</th>
                <th class="text-left">Icon Bootstrap</th>
                <th class="text-left">Keterangan</th>
                <th class="text-center">Jumlah Produk</th>
                <th class="text-center">Status</th>
                <th class="text-center w-28">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 font-medium text-xs">
            @forelse ($kategoris as $idx => $kat)
                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors">
                    <td class="text-center font-bold text-zinc-400">{{ $idx + 1 }}</td>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm shrink-0 border border-purple-500/20">
                                <i class="bi {{ $kat->icon ?: 'bi-tag-fill' }}"></i>
                            </div>
                            <div>
                                <div class="font-black text-zinc-900 dark:text-white text-xs">
                                    {{ $kat->nama_kategori }}
                                </div>
                                <span class="text-[10px] text-zinc-400 font-mono">{{ $kat->slug }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-1.5">
                            <code class="px-2 py-0.5 rounded text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-mono">
                                {{ $kat->icon ?: 'bi-tag-fill' }}
                            </code>
                        </div>
                    </td>
                    <td class="text-zinc-600 dark:text-zinc-400 max-w-xs truncate">
                        {{ $kat->keterangan ?: '-' }}
                    </td>
                    <td class="text-center font-bold">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                            {{ $kat->produks_count }} Produk
                        </span>
                    </td>
                    <td class="text-center">
                        <x-toggle-status :is-active="$kat->is_active" :url="route('koperasi.kategori.toggle-status', $kat->id)" />
                    </td>
                    <td class="text-center">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('koperasi.kategori.edit', $kat->id) }}"
                                class="action-modal w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-primary hover:text-white dark:hover:bg-primary-dark dark:hover:text-zinc-950 flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                title="Edit Kategori">
                                <i class="bi bi-pencil-square text-xs"></i>
                            </a>

                            @if ($kat->produks_count == 0)
                                <form action="{{ route('koperasi.kategori.destroy', $kat->id) }}" method="POST"
                                    class="delete-ajax inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-rose-600 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs"
                                        title="Hapus Kategori">
                                        <i class="bi bi-trash text-xs"></i>
                                    </button>
                                </form>
                            @else
                                <span class="w-8 h-8 rounded-xl bg-zinc-50 dark:bg-zinc-900 text-zinc-300 dark:text-zinc-700 flex items-center justify-center cursor-not-allowed"
                                    title="Tidak dapat dihapus karena memiliki {{ $kat->produks_count }} produk">
                                    <i class="bi bi-trash text-xs"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-zinc-400 dark:text-zinc-500 text-xs font-semibold">
                        <i class="bi bi-tags text-3xl block mb-2 opacity-50"></i>
                        Belum ada kategori produk. Silakan tambahkan kategori pertama.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
