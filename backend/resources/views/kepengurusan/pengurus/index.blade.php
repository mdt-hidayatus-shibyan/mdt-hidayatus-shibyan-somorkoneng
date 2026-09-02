@section('title', 'Susunan Pengurus')

<x-app-layout>
    <!-- Header Section (Compact M3) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Susunan Pengurus
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Kelola penempatan anggota pada jabatan, unit/tingkat, dan periode kepengurusan.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">
            <!-- Search Form -->
            <form action="{{ route('pengurus.index') }}" method="GET" class="relative w-full sm:w-72 group/search">
                <div
                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengurus..."
                    class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold">
                @if (request('search'))
                    <a href="{{ route('pengurus.index') }}"
                        class="absolute inset-y-0 right-0 w-9 h-full flex items-center justify-center text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors outline-none">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </a>
                @endif
            </form>

            @can('create pengurus')
                <a href="{{ route('pengurus.create') }}" class="m3-btn-primary w-full sm:w-auto action-modal group/btn h-10 px-5 text-xs font-black shadow-2xs">
                    <i class="bi bi-patch-plus-fill text-sm"></i>
                    <span>Tugaskan Pengurus</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Data Grid Container (Dipertahankan ID untuk AJAX handler) -->
    <div id="data-grid-container" class="flex flex-col gap-3 relative z-10">
        @include('kepengurusan.pengurus.list', ['pengurus' => $pengurus])
    </div>
</x-app-layout>

