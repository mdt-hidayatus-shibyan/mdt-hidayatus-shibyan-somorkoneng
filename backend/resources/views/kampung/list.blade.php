@forelse($kampungs as $kampung)
    <div
        class="m3-glass-card p-4 md:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group relative overflow-hidden transition-all shadow-2xs hover:border-primary/40 dark:hover:border-primary-dark/40">

        <!-- Card Info Section (Compact) -->
        <div class="flex items-center gap-3 md:gap-4 relative z-10 w-full sm:w-auto">

            <!-- Badge Nomor -->
            <span
                class="w-9 h-9 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 rounded-xl text-xs font-black border border-zinc-200 dark:border-zinc-700 flex-shrink-0 shadow-2xs">
                {{ $loop->iteration }}
            </span>

            <!-- Badge Kode -->
            <div
                class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center bg-primary/10 dark:bg-primary-dark/20 border border-primary/20 text-primary dark:text-primary-dark shadow-2xs font-mono font-black text-sm">
                {{ $kampung->kode ?? '-' }}
            </div>

            <!-- Nama Kampung -->
            <div class="flex-1 overflow-hidden">
                <h4
                    class="text-sm md:text-base font-black text-zinc-900 dark:text-white tracking-tight leading-tight truncate">
                    {{ $kampung->nama_kampung }}
                </h4>
                <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                    Kampung / Dusun Domisili
                </p>
            </div>
        </div>

        <!-- Action Buttons Section -->
        <div
            class="flex items-center justify-end gap-1.5 relative z-10 w-full sm:w-auto border-t sm:border-none border-zinc-200/60 dark:border-zinc-800 pt-3 sm:pt-0 mt-1 sm:mt-0">

            @can('update kampung')
                <a href="{{ route('kampung.edit', $kampung->id) }}"
                    class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-500/20 border border-blue-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none action-modal"
                    title="Edit Data Kampung">
                    <i class="bi bi-pencil-fill text-xs"></i>
                </a>
            @endcan

            @can('delete kampung')
                <form action="{{ route('kampung.destroy', $kampung->id) }}" method="POST"
                    class="delete-ajax inline m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center hover:bg-rose-500/20 border border-rose-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                        title="Hapus Data Kampung">
                        <i class="bi bi-trash-fill text-xs"></i>
                    </button>
                </form>
            @endcan

        </div>
    </div>
@empty
    <!-- Empty State -->
    <x-empty-state icon="bi-geo-alt" title="Data Kampung Masih Kosong" message="Anda belum menambahkan data kampung/dusun." />
@endforelse

