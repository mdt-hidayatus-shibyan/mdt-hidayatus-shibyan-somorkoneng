@section('title', 'Rombongan Belajar')

<x-app-layout>
    <!-- Header Section (Compact M3) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                Rombongan Belajar
            </h2>
            <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                Kelola data rombongan belajar, pembagian kelas, dan anggota santri.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">

            <!-- Search & Filter Form -->
            <form action="{{ route('ruangan.index') }}" method="GET"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">

                <!-- Filter Tahun Pelajaran -->
                <div class="relative w-full sm:w-auto min-w-[170px] group/filter">
                    <select name="tahun_pelajaran_id" onchange="this.form.submit()"
                        class="m3-input-glass w-full appearance-none cursor-pointer !pr-9">
                        @foreach ($tahunPelajarans as $tp)
                            <option value="{{ $tp->id }}" {{ $filterTp == $tp->id ? 'selected' : '' }}>
                                {{ $tp->nama_hijriyah }} H | {{ $tp->nama_masehi }} M
                            </option>
                        @endforeach
                    </select>
                    <!-- Ikon Chevron Custom M3 -->
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400 group-focus-within/filter:text-primary dark:group-focus-within/filter:text-primary-dark transition-colors">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-72 group/search">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors duration-300 text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                        <i class="bi bi-search text-sm"></i>
                    </div>

                    <!-- Input Pencarian -->
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ruangan..."
                        class="m3-input-glass w-full !pl-10 !pr-10">

                    <!-- Tombol Reset -->
                    @if (request('search') || request('tahun_pelajaran_id'))
                        <a href="{{ route('ruangan.index') }}"
                            class="absolute inset-y-0 right-0 w-10 h-10 flex items-center justify-center text-zinc-400 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800 rounded-xl transition-colors duration-200 outline-none"
                            title="Reset Filter">
                            <i class="bi bi-x-lg text-xs font-bold"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Data Grid Container -->
    <div id="data-grid-container" class="flex flex-col gap-3 relative z-10">
        @include('rombongan-belajar.list', ['ruangans' => $ruangans])
    </div>
</x-app-layout>
