@section('title', 'Ustadz')

<x-app-layout>
    <!-- Header Section (Compact M3) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Ustadz & Guru
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-1">
                Kelola data Dewan Asatidz / Pengajar di Madrasah.
            </p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <!-- Import Button -->
            @can('tambah ustadz')
                <a href="{{ route('ustadz.import') }}"
                    class="action-modal flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded-xl text-xs font-black transition-all active:scale-95 shadow-2xs">
                    <i class="bi bi-cloud-arrow-up-fill mr-1.5 text-sm"></i>
                    <span>Import</span>
                </a>
                <!-- Add Button -->
                <a href="{{ route('ustadz.create') }}" class="m3-btn-primary flex-1 sm:flex-none h-10 px-5 text-xs font-black shadow-2xs group/btn">
                    <i class="bi bi-person-plus-fill mr-1.5 text-sm"></i>
                    <span>Tambah Ustadz</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Data Grid Container -->
    <div id="data-grid-container" class="flex flex-col gap-4 relative z-10">

        <!-- Filter & Search Card (Sticky - Compact M3) -->
        <div class="sticky top-6 z-30 mb-2">
            <form action="{{ route('ustadz.index') }}" method="GET"
                class="m3-glass-card p-3 sm:p-4 shadow-2xs flex flex-col sm:flex-row items-center gap-3">

                <!-- Search Input -->
                <div class="relative w-full flex-1 group/search">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark transition-colors">
                        <i class="bi bi-search text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau kode ustadz..."
                        class="m3-input-glass w-full !pl-9 text-xs font-bold">
                </div>

                <!-- Filter Status (Dropdown) -->
                <div class="relative w-full sm:w-auto min-w-[150px] group/filter">
                    <select name="status"
                        class="m3-input-glass w-full !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}
                            class="bg-white dark:bg-zinc-900 text-emerald-600 dark:text-emerald-400">Aktif
                        </option>
                        <option value="tidak_aktif" {{ request('status') === 'tidak_aktif' ? 'selected' : '' }}
                            class="bg-white dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400">Tidak Aktif
                        </option>
                    </select>
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Action Buttons (Submit & Reset) -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="m3-btn-primary flex-1 sm:flex-none h-10 px-5 text-xs font-black shadow-2xs">
                        <i class="bi bi-funnel-fill mr-1 text-xs"></i> Filter
                    </button>

                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('ustadz.index') }}"
                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-rose-500/10 dark:bg-zinc-800 dark:hover:bg-rose-500/20 text-zinc-500 hover:text-rose-600 dark:text-zinc-400 dark:hover:text-rose-400 border border-zinc-200 dark:border-zinc-700 transition-all outline-none shadow-2xs"
                            title="Reset Filter">
                            <i class="bi bi-x-lg text-xs font-bold"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @include('ustadz.list', ['ustadzs' => $ustadzs])
    </div>
</x-app-layout>

