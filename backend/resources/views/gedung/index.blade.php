@section('title', 'Master Gedung')

<x-app-layout>
    <!-- Header Section (Compact M3) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                Master Gedung
            </h2>
            <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                Kelola data gedung, bangunan fisik madrasah, dan alokasi ruangan.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">
            <!-- Search Form -->
            <form action="{{ route('gedung.index') }}" method="GET"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">
                <div class="relative w-full sm:w-72 group/search">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors duration-300 text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                        <i class="bi bi-search text-sm"></i>
                    </div>

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari gedung..."
                        class="m3-input-glass w-full !pl-10 !pr-10">

                    @if (request('search'))
                        <a href="{{ route('gedung.index') }}"
                            class="absolute inset-y-0 right-0 w-10 h-10 flex items-center justify-center text-zinc-400 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800 rounded-xl transition-colors duration-200 outline-none"
                            title="Reset Filter">
                            <i class="bi bi-x-lg text-xs font-bold"></i>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Add Button -->
            @can('create gedung')
                <a href="{{ route('gedung.create') }}" class="m3-btn-primary w-full sm:w-auto action-modal group/btn">
                    <i class="bi bi-plus-lg text-base transition-transform duration-300 group-hover/btn:scale-110"></i>
                    <span>Tambah Gedung</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Metric / Stats Cards Section -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Total Gedung -->
        <div class="m3-glass-card p-4 flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-2xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center text-xl shrink-0 border border-primary/20">
                <i class="bi bi-buildings-fill"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    Total Gedung
                </p>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ $totalGedung }} <span class="text-xs font-semibold text-zinc-400 font-sans">Gedung</span>
                </h3>
            </div>
        </div>

        <!-- Gedung Aktif -->
        <div class="m3-glass-card p-4 flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0 border border-emerald-500/20">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    Gedung Aktif
                </p>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ $totalGedungAktif }} <span class="text-xs font-semibold text-zinc-400 font-sans">Aktif</span>
                </h3>
            </div>
        </div>

        <!-- Total Ruangan -->
        <div class="m3-glass-card p-4 flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shrink-0 border border-blue-500/20">
                <i class="bi bi-door-open-fill"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    Total Ruangan
                </p>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ $totalRuangan }} <span class="text-xs font-semibold text-zinc-400 font-sans">Ruang</span>
                </h3>
            </div>
        </div>

        <!-- Total Sarpras -->
        <div class="m3-glass-card p-4 flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0 border border-amber-500/20">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    Total Sarpras
                </p>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ $totalSarpras }} <span class="text-xs font-semibold text-zinc-400 font-sans">Item</span>
                </h3>
            </div>
        </div>
    </div>

    <!-- Data Grid Container -->
    <div id="data-grid-container" class="flex flex-col gap-3 relative z-10">
        @include('gedung.list', ['gedungs' => $gedungs])
    </div>
</x-app-layout>
