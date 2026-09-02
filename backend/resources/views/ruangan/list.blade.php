@forelse($ruangans as $ruangan)
    <div
        class="m3-glass-card p-4 md:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group relative overflow-hidden hover:border-primary/40 dark:hover:border-primary-dark/40 transition-all duration-300">

        <!-- ================= 1. SECTION INFO (Kiri) ================= -->
        <div class="flex items-center gap-3 md:gap-3.5 w-full lg:w-auto flex-1 relative z-10">
            <!-- Badge Nomor -->
            <span
                class="w-9 h-9 flex items-center justify-center bg-zinc-100/80 dark:bg-zinc-900 text-primary dark:text-primary-dark rounded-xl text-xs font-black border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                {{ $loop->iteration }}
            </span>

            <div class="flex-1 overflow-hidden">
                <h3
                    class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-snug mb-1 truncate">
                    {{ $ruangan->nama_ruangan }}
                </h3>

                <div class="flex flex-wrap items-center gap-2 mt-0.5">
                    <!-- Badge Tingkat -->
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase shadow-xs text-white"
                        style="background-color: {{ $ruangan->level->tingkat->kode_warna ?? '#146C2E' }};">
                        {{ $ruangan->level->tingkat->nama_tingkat ?? '-' }}
                    </span>

                    <!-- Badge Jumlah Murid -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-zinc-100/80 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 text-[10px] font-bold uppercase tracking-wider border border-zinc-200/80 dark:border-zinc-800">
                        <i class="bi bi-people text-xs"></i>
                        Aktif: {{ $ruangan->murids->where('status', 'Aktif')->count() }}
                    </span>

                    <!-- Span Jumlah Tidak Aktif -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 text-[10px] font-bold uppercase tracking-wider border border-rose-200/80 dark:border-rose-800/40">
                        <i class="bi bi-person-x text-xs"></i>
                        Non-Aktif: {{ $ruangan->murids->where('status', '!=', 'Aktif')->count() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ================= 2. SECTION WALI RUANGAN (Tengah) ================= -->
        <div
            class="flex items-center gap-3 w-full lg:w-auto lg:min-w-[200px] border-t lg:border-none border-zinc-100 dark:border-zinc-800/60 pt-3 lg:pt-0 relative z-10">
            @if ($ruangan->waliRuangan)
                <!-- Avatar Inisial Wali -->
                <div
                    class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center text-xs font-black shrink-0 border border-primary/20">
                    {{ substr($ruangan->waliRuangan->nama_lengkap, 0, 1) }}
                </div>
                <div class="flex flex-col overflow-hidden">
                    <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Wali
                        Ruangan</span>
                    <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate"
                        title="{{ $ruangan->waliRuangan->nama_lengkap }}">
                        {{ $ruangan->waliRuangan->nama_lengkap }}
                    </span>
                </div>
            @else
                <!-- State Belum Ada Wali -->
                <div
                    class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/30 text-rose-500 dark:text-rose-400 flex items-center justify-center text-xs shrink-0 border border-rose-200/60 dark:border-rose-800/40">
                    <i class="bi bi-person-x-fill text-sm"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Wali
                        Ruangan</span>
                    <span class="text-[11px] font-bold text-rose-500 dark:text-rose-400">Belum Ditunjuk</span>
                </div>
            @endif
        </div>

        <!-- ================= 3. SECTION AKSI & STATUS (Kanan) ================= -->
        <div
            class="flex items-center justify-between sm:justify-end gap-3 sm:gap-3.5 w-full lg:w-auto border-t sm:border-none border-zinc-100 dark:border-zinc-800/60 pt-3 sm:pt-0 relative z-10">

            <!-- Status Toggle / Badge -->
            @can('update ruangan')
                <x-toggle-status :is-active="$ruangan->is_active" :url="route('ruangan.toggle-status', $ruangan->id)" />
            @endcan

            <!-- Divider -->
            <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-800 transition-colors duration-300">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1.5">

                @can('update ruangan')
                    <!-- Tombol Edit -->
                    <a href="{{ route('ruangan.edit', $ruangan->id) }}"
                        class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200/60 dark:border-blue-800/40 transition-all hover:scale-105 active:scale-95 outline-none action-modal"
                        title="Edit">
                        <i class="bi bi-pencil-fill text-xs"></i>
                    </a>
                @endcan

                @can('delete ruangan')
                    <!-- Tombol Hapus -->
                    <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST"
                        class="delete-ajax inline m-0 p-0">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-900/50 border border-red-200/60 dark:border-red-800/40 transition-all hover:scale-105 active:scale-95 outline-none"
                            title="Hapus">
                            <i class="bi bi-trash-fill text-xs"></i>
                        </button>
                    </form>
                @endcan
            </div>
        </div>

    </div>
@empty
    <!-- Custom Empty State -->
    <x-empty-state icon="bi-door-closed" title="Data Ruangan Kosong"
        message="Anda belum mengatur Ruangan pada tahun pelajaran ini." />
@endforelse

