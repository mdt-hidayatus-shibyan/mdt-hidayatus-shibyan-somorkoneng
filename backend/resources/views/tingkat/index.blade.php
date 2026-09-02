@section('title', 'Tingkat')

<x-app-layout>
    <!-- Header Section (Compact M3) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                Tingkat
            </h2>
            <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                Kelola data Tingkat dan Jenjang Pendidikan madrasah.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">

            <!-- Search Form -->
            <form action="{{ route('tingkat.index') }}" method="GET" class="relative w-full sm:w-72 group/search">
                <div
                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors duration-300 text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                    <i class="bi bi-search text-sm"></i>
                </div>

                <!-- Input Pencarian Glass -->
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tingkat..."
                    class="m3-input-glass w-full !pl-10 !pr-10">

                @if (request('search'))
                    <!-- Tombol Reset Pencarian -->
                    <a href="{{ route('tingkat.index') }}"
                        class="absolute inset-y-0 right-0 w-10 h-10 flex items-center justify-center text-zinc-400 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800 rounded-xl transition-colors duration-200 outline-none">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </a>
                @endif
            </form>

            <!-- Add Button -->
            @can('tambah tingkat')
                <a href="{{ route('tingkat.create') }}" class="m3-btn-primary w-full sm:w-auto action-modal group/btn">
                    <i class="bi bi-patch-plus text-base transition-transform duration-300 group-hover/btn:scale-110"></i>
                    <span>Tambah Tingkat</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Data Grid Container -->
    <div id="data-grid-container" class="flex flex-col gap-3 relative z-10">
        @include('tingkat.list', ['tingkats' => $tingkats])
    </div>
</x-app-layout>

