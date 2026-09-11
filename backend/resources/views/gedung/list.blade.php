@forelse($gedungs as $gedung)
    <div
        class="m3-glass-card p-4 md:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group relative overflow-hidden hover:border-primary/40 dark:hover:border-primary-dark/40 transition-all duration-300">

        <!-- ================= 1. SECTION INFO (Kiri) ================= -->
        <div class="flex items-center gap-3.5 flex-1 relative z-10">
            <!-- Badge Icon / Inisial Gedung -->
            <div
                class="w-10 h-10 flex items-center justify-center bg-zinc-100/80 dark:bg-zinc-900 text-primary dark:text-primary-dark rounded-xl text-xs font-black border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                {{ $gedung->kode_gedung }}
            </div>

            <div class="flex-1 overflow-hidden">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-snug truncate">
                        {{ $gedung->nama_gedung }}
                    </h3>
                </div>

                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <!-- Badge Lantai -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 text-[10px] font-bold uppercase tracking-wider border border-purple-200/80 dark:border-purple-800/40">
                        <i class="bi bi-layers-fill text-xs"></i>
                        {{ $gedung->jumlah_lantai }} Lantai
                    </span>

                    <!-- Badge Jumlah Ruangan -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-[10px] font-bold uppercase tracking-wider border border-blue-200/80 dark:border-blue-800/40">
                        <i class="bi bi-door-open-fill text-xs"></i>
                        {{ $gedung->ruangans_count ?? $gedung->ruangans->count() }} Ruangan
                    </span>

                    <!-- Badge Jumlah Sarpras -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 text-[10px] font-bold uppercase tracking-wider border border-amber-200/80 dark:border-amber-800/40">
                        <i class="bi bi-box-seam-fill text-xs"></i>
                        {{ $gedung->sarpras_count ?? $gedung->sarpras->count() }} Sarpras
                    </span>

                    @if ($gedung->keterangan)
                        <span class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate max-w-xs"
                            title="{{ $gedung->keterangan }}">
                            • {{ $gedung->keterangan }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- ================= 2. SECTION AKSI & STATUS (Kanan) ================= -->
        <div
            class="flex items-center justify-between sm:justify-end gap-3 sm:gap-3.5 w-full sm:w-auto border-t sm:border-none border-zinc-100 dark:border-zinc-800/60 pt-3 sm:pt-0 relative z-10">

            <!-- Status Toggle -->
            @can('update gedung')
                <x-toggle-status :is-active="$gedung->is_active" :url="route('gedung.toggle-status', $gedung->id)" />
            @endcan

            <!-- Divider -->
            <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-800 transition-colors duration-300"></div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1.5">
                @can('update gedung')
                    <!-- Tombol Edit -->
                    <a href="{{ route('gedung.edit', $gedung->id) }}"
                        class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200/60 dark:border-blue-800/40 transition-all hover:scale-105 active:scale-95 outline-none action-modal"
                        title="Edit Gedung">
                        <i class="bi bi-pencil-fill text-xs"></i>
                    </a>
                @endcan

                @can('delete gedung')
                    <!-- Tombol Hapus -->
                    <form action="{{ route('gedung.destroy', $gedung->id) }}" method="POST"
                        class="delete-ajax inline m-0 p-0">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-900/50 border border-red-200/60 dark:border-red-800/40 transition-all hover:scale-105 active:scale-95 outline-none"
                            title="Hapus Gedung">
                            <i class="bi bi-trash-fill text-xs"></i>
                        </button>
                    </form>
                @endcan
            </div>
        </div>

    </div>
@empty
    <x-empty-state icon="bi-buildings" title="Data Gedung Kosong"
        message="Belum ada data gedung yang terdaftar di sistem. Klik tombol 'Tambah Gedung' untuk menambahkan." />
@endforelse
