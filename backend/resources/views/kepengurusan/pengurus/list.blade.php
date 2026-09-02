@forelse($pengurus as $item)
    <div
        class="m3-glass-card p-4 md:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group relative overflow-hidden transition-all shadow-2xs hover:border-primary/40 dark:hover:border-primary-dark/40">

        <!-- Card Info Section (Compact) -->
        <div class="flex items-center gap-3 md:gap-4 relative z-10 w-full sm:w-auto">
            <!-- Badge Nomor -->
            <span
                class="w-9 h-9 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 rounded-xl text-xs font-black border border-zinc-200 dark:border-zinc-700 flex-shrink-0 shadow-2xs">
                {{ $loop->iteration }}
            </span>

            <!-- Foto Anggota -->
            <div
                class="w-11 h-11 md:w-12 md:h-12 rounded-xl overflow-hidden flex-shrink-0 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-2xs">
                @if ($item->anggota->foto_utama)
                    <img src="{{ asset('storage/' . $item->anggota->foto_utama) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 dark:text-zinc-500">
                        <i class="bi bi-person-fill text-lg"></i>
                    </div>
                @endif
            </div>

            <div class="flex-1 overflow-hidden">
                <h4
                    class="text-sm md:text-base font-black text-zinc-900 dark:text-white tracking-tight leading-tight truncate">
                    {{ $item->anggota->nama_lengkap }}
                </h4>

                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-primary/10 dark:bg-primary-dark/20 border border-primary/20 text-[10px] font-black text-primary dark:text-primary-dark uppercase tracking-wider shadow-2xs">
                        <i class="bi bi-briefcase-fill text-[9px]"></i>
                        {{ $item->jabatan->nama_jabatan }}
                    </span>

                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider shadow-2xs">
                        <i class="bi bi-calendar-event text-[9px]"></i>
                        {{ $item->periode->nama_periode }}
                    </span>

                    @if ($item->no_sk)
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20 text-[10px] font-bold text-amber-600 dark:text-amber-400 font-mono shadow-2xs">
                            <i class="bi bi-file-earmark-text text-[9px]"></i>
                            SK: {{ $item->no_sk }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons Section -->
        <div
            class="flex items-center justify-end gap-1.5 relative z-10 w-full sm:w-auto border-t sm:border-none border-zinc-200/60 dark:border-zinc-800 pt-3 sm:pt-0 mt-1 sm:mt-0">
            @can('update pengurus')
                <a href="{{ route('pengurus.edit', $item->id) }}"
                    class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-500/20 border border-blue-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none action-modal"
                    title="Edit Penugasan">
                    <i class="bi bi-pencil-fill text-xs"></i>
                </a>
            @endcan

            @can('delete pengurus')
                <form action="{{ route('pengurus.destroy', $item->id) }}" method="POST" class="delete-ajax inline m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center hover:bg-rose-500/20 border border-rose-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                        title="Hapus Penugasan">
                        <i class="bi bi-trash-fill text-xs"></i>
                    </button>
                </form>
            @endcan
        </div>
    </div>
@empty
    <x-empty-state icon="bi-diagram-3-fill" title="Belum Ada Susunan Pengurus"
        message="Silakan tambahkan data penugasan pengurus." />
@endforelse

