@forelse($sarprasItems as $item)
    <div
        class="m3-glass-card p-4 md:p-4.5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 group relative overflow-hidden hover:border-primary/40 dark:hover:border-primary-dark/40 transition-all duration-300">

        <!-- ================= 1. SECTION INFO UTAMA (Kiri) ================= -->
        <div class="flex items-start sm:items-center gap-3.5 flex-1 relative z-10">
            <!-- Thumbnail Foto / Icon Kategori -->
            <div class="relative shrink-0">
                @if ($item->foto)
                    <img src="{{ route('storage.local', $item->foto) }}" alt="{{ $item->nama_sarpras }}"
                        class="w-12 h-12 rounded-2xl object-cover border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                @else
                    <div
                        class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80 text-primary dark:text-primary-dark flex items-center justify-center text-xl font-black border border-zinc-200/80 dark:border-zinc-700/60 shrink-0">
                        @if (str_contains(strtolower($item->kategori), 'elektronik'))
                            <i class="bi bi-display"></i>
                        @elseif(str_contains(strtolower($item->kategori), 'mebel'))
                            <i class="bi bi-chair"></i>
                        @elseif(str_contains(strtolower($item->kategori), 'tulis') || str_contains(strtolower($item->kategori), 'papan'))
                            <i class="bi bi-easel"></i>
                        @elseif(str_contains(strtolower($item->kategori), 'ibadah'))
                            <i class="bi bi-moon-stars"></i>
                        @elseif(str_contains(strtolower($item->kategori), 'sanitasi'))
                            <i class="bi bi-droplet"></i>
                        @else
                            <i class="bi bi-box-seam"></i>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex-1 overflow-hidden">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-snug">
                        {{ $item->nama_sarpras }}
                    </h3>
                    <span class="text-xs font-mono font-bold text-zinc-400 dark:text-zinc-500">
                        [{{ $item->kode_sarpras }}]
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <!-- Kategori Badge -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[10px] font-bold uppercase tracking-wider border border-zinc-200/60 dark:border-zinc-700/60">
                        <i class="bi bi-tag-fill text-[10px]"></i>
                        {{ $item->kategori }}
                    </span>

                    <!-- Lokasi Gedung & Ruangan -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase tracking-wider border border-blue-200/80 dark:border-blue-800/40">
                        <i class="bi bi-geo-alt-fill text-[10px]"></i>
                        {{ $item->gedung->nama_gedung ?? 'Gedung -' }}
                        @if ($item->ruangan)
                            / {{ $item->ruangan->nama_ruangan }}
                            @if ($item->ruangan->nama_kamar)
                                <span
                                    class="text-[9px] font-normal normal-case">({{ $item->ruangan->nama_kamar }})</span>
                            @endif
                        @endif
                    </span>

                    <!-- Jumlah & Satuan -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 text-[10px] font-bold uppercase tracking-wider border border-purple-200/80 dark:border-purple-800/40">
                        <i class="bi bi-stack text-[10px]"></i>
                        {{ $item->jumlah }} {{ $item->satuan }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ================= 2. SECTION STATUS KONDISI & KETERANGAN (Tengah) ================= -->
        <div class="flex flex-wrap items-center gap-3 lg:min-w-[220px] relative z-10">
            <!-- Badge Kondisi -->
            @if ($item->kondisi === 'tersedia')
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/40">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-black uppercase tracking-wider">Tersedia / Baik</span>
                </div>
            @elseif ($item->kondisi === 'rusak_ringan')
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200/80 dark:border-amber-800/40">
                    <i class="bi bi-exclamation-circle-fill text-xs"></i>
                    <span class="text-xs font-black uppercase tracking-wider">Rusak Ringan</span>
                </div>
            @elseif ($item->kondisi === 'rusak_berat')
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-800/40">
                    <i class="bi bi-x-circle-fill text-xs"></i>
                    <span class="text-xs font-black uppercase tracking-wider">Rusak Berat</span>
                </div>
            @else
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-800/40">
                    <i class="bi bi-exclamation-triangle-fill text-xs"></i>
                    <span class="text-xs font-black uppercase tracking-wider">Rusak</span>
                </div>
            @endif

            @if ($item->sumber_dana || $item->tanggal_pengadaan)
                <div class="text-[11px] text-zinc-400 dark:text-zinc-500">
                    @if ($item->sumber_dana)
                        <span>Sumber: {{ $item->sumber_dana }}</span>
                    @endif
                    @if ($item->tanggal_pengadaan)
                        <span class="block">Tgl: {{ $item->tanggal_pengadaan->format('d/m/Y') }}</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- ================= 3. SECTION AKSI & STATUS (Kanan) ================= -->
        <div
            class="flex items-center justify-between sm:justify-end gap-3 sm:gap-3.5 w-full lg:w-auto border-t lg:border-none border-zinc-100 dark:border-zinc-800/60 pt-3 lg:pt-0 relative z-10">

            <!-- Status Toggle -->
            @can('update sarpras')
                <x-toggle-status :is-active="$item->is_active" :url="route('sarpras.toggle-status', $item->id)" />
            @endcan

            <!-- Divider -->
            <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-800 transition-colors duration-300"></div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1.5">
                @can('update sarpras')
                    <!-- Tombol Edit -->
                    <a href="{{ route('sarpras.edit', $item->id) }}"
                        class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200/60 dark:border-blue-800/40 transition-all hover:scale-105 active:scale-95 outline-none action-modal"
                        title="Edit Sarpras">
                        <i class="bi bi-pencil-fill text-xs"></i>
                    </a>
                @endcan

                @can('delete sarpras')
                    <!-- Tombol Hapus -->
                    <form action="{{ route('sarpras.destroy', $item->id) }}" method="POST"
                        class="delete-ajax inline m-0 p-0">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-900/50 border border-red-200/60 dark:border-red-800/40 transition-all hover:scale-105 active:scale-95 outline-none"
                            title="Hapus Sarpras">
                            <i class="bi bi-trash-fill text-xs"></i>
                        </button>
                    </form>
                @endcan
            </div>
        </div>

    </div>
@empty
    <x-empty-state icon="bi-box-seam" title="Data Sarana & Prasarana Kosong"
        message="Belum ada sarana & prasarana yang tercatat sesuai filter. Klik 'Tambah Sarpras' untuk mencatat barang baru." />
@endforelse

<!-- Pagination -->
@if ($sarprasItems->hasPages())
    <div class="pt-4">
        {{ $sarprasItems->links('vendor.pagination.custom') }}
    </div>
@endif
