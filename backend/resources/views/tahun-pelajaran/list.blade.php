@forelse($tahun_pelajarans as $tp)
    <div
        class="m3-glass-card p-4 md:p-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group relative overflow-hidden hover:border-primary/40 dark:hover:border-primary-dark/40 transition-all duration-300">

        <!-- Card Info Section -->
        <div class="flex items-center gap-3 md:gap-3.5 relative z-10 w-full sm:w-auto">

            <!-- Badge Nomor -->
            <span
                class="w-9 h-9 flex items-center justify-center bg-zinc-100/80 dark:bg-zinc-900 text-primary dark:text-primary-dark rounded-xl text-xs font-black border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                {{ $loop->iteration }}
            </span>

            <div class="flex-1 overflow-hidden">
                <h3
                    class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-snug truncate transition-colors duration-300">
                    {{ $tp->nama_hijriyah }} H <span class="text-zinc-400 font-semibold mx-1">|</span> {{ $tp->nama_masehi }} M
                </h3>
            </div>
        </div>

        <!-- Status & Action Buttons Section -->
        <div
            class="flex items-center justify-between sm:justify-end gap-3 sm:gap-3.5 relative z-10 w-full sm:w-auto border-t sm:border-none border-zinc-100 dark:border-zinc-800/60 pt-3 sm:pt-0 mt-1 sm:mt-0">

            <!-- Status Toggle Component -->
            <x-toggle-status :is-active="$tp->is_active" :url="route('tahun-pelajaran.toggle-status', $tp->id)" />

            <!-- Divider Garis -->
            <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-800 transition-colors duration-300">
            </div>

            <!-- Action Buttons Column -->
            <div class="flex items-center gap-1.5">
                @can('update tahun-pelajaran')
                    <!-- Touch Target 40px -->
                    <a href="{{ route('tahun-pelajaran.edit', $tp->id) }}"
                        class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200/60 dark:border-blue-800/40 transition-all hover:scale-105 active:scale-95 outline-none action-modal"
                        title="Edit">
                        <i class="bi bi-pencil-fill text-xs"></i>
                    </a>
                @endcan

                @can('delete tahun-pelajaran')
                    <form action="{{ route('tahun-pelajaran.destroy', $tp->id) }}" method="POST"
                        class="delete-ajax inline m-0 p-0">
                        @csrf @method('DELETE')
                        <!-- Touch Target 40px -->
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
    <!-- Empty State -->
    <x-empty-state icon="bi-calendar3" title="Data Tahun Pelajaran Masih Kosong"
        message="Anda belum mengatur Tahun Pelajaran." />
@endforelse

