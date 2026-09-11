<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr
                class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 text-[11px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                <th class="py-3.5 px-4">Kode & Nama Kategori</th>
                <th class="py-3.5 px-4">Jenis Aliran</th>
                <th class="py-3.5 px-4">Jumlah Subkategori</th>
                <th class="py-3.5 px-4">Deskripsi / Alokasi</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
            @forelse ($kategoris as $kategori)
                <!-- Parent Row -->
                <tr
                    class="bg-zinc-50/40 dark:bg-zinc-900/20 hover:bg-zinc-100/60 dark:hover:bg-zinc-800/40 transition-colors font-bold group">
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 flex items-center justify-center text-xs shadow-2xs flex-shrink-0">
                                <i class="bi bi-folder-fill"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono text-zinc-400 dark:text-zinc-500 text-[11px]">
                                        [{{ $kategori->kode_kategori }}]
                                    </span>
                                    <span class="font-black text-zinc-900 dark:text-white text-sm truncate">
                                        {{ $kategori->nama_kategori }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-4">
                        @php
                            $jenisBadge = match ($kategori->jenis) {
                                'pemasukan'
                                    => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                'pengeluaran' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                'simpanan' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                default => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20',
                            };
                        @endphp
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border shadow-2xs {{ $jenisBadge }}">
                            {{ strtoupper($kategori->jenis) }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 font-bold text-zinc-600 dark:text-zinc-400">
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-[11px] font-mono font-bold">
                            {{ $kategori->children->count() }} Sub
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-zinc-500 dark:text-zinc-400 text-[11px] max-w-xs truncate">
                        {{ $kategori->deskripsi ?? '-' }}
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <x-toggle :checked="$kategori->is_active" :url="route('keuangan.kategori.toggle-status', $kategori->id)" name="is_active"
                            ajax="true" />
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('keuangan.kategori.edit', $kategori->id) }}"
                                class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 border border-blue-500/20 shadow-2xs outline-none action-modal"
                                title="Edit Kategori">
                                <i class="bi bi-pencil-fill text-xs"></i>
                            </a>
                            <form action="{{ route('keuangan.kategori.destroy', $kategori->id) }}" method="POST"
                                class="delete-ajax inline m-0 p-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 border border-rose-500/20 shadow-2xs outline-none"
                                    title="Hapus Kategori">
                                    <i class="bi bi-trash-fill text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Child Rows (Subkategori) -->
                @foreach ($kategori->children as $child)
                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-900/30 transition-colors">
                        <td class="py-2.5 px-4 pl-12">
                            <div class="flex items-center gap-2">
                                <span class="text-zinc-400 font-bold">↳</span>
                                <span class="font-mono text-zinc-400 dark:text-zinc-500 text-[10px]">
                                    [{{ $child->kode_kategori }}]
                                </span>
                                <span class="font-bold text-zinc-800 dark:text-zinc-200">
                                    {{ $child->nama_kategori }}
                                </span>
                            </div>
                        </td>
                        <td class="py-2.5 px-4">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase">
                                {{ $child->jenis }}
                            </span>
                        </td>
                        <td class="py-2.5 px-4 text-zinc-400 text-[11px]">-</td>
                        <td class="py-2.5 px-4 text-zinc-400 dark:text-zinc-500 text-[11px] max-w-xs truncate">
                            {{ $child->deskripsi ?? '-' }}
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            <x-toggle :checked="$child->is_active" :url="route('keuangan.kategori.toggle-status', $child->id)" name="is_active"
                                ajax="true" />
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('keuangan.kategori.edit', $child->id) }}"
                                    class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 border border-blue-500/20 shadow-2xs outline-none action-modal"
                                    title="Edit Subkategori">
                                    <i class="bi bi-pencil-fill text-[11px]"></i>
                                </a>
                                <form action="{{ route('keuangan.kategori.destroy', $child->id) }}" method="POST"
                                    class="delete-ajax inline m-0 p-0">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 border border-rose-500/20 shadow-2xs outline-none"
                                        title="Hapus Subkategori">
                                        <i class="bi bi-trash-fill text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-zinc-400 dark:text-zinc-500">
                        <i class="bi bi-tags text-3xl mb-2 block"></i>
                        <p class="font-bold">Belum ada data kategori keuangan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
