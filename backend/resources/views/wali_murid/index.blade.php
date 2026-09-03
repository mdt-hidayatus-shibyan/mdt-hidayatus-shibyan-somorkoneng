@section('title', 'Wali Murid')

<x-app-layout>

    <!-- Header & Actions Section -->
    <div class="mb-6 md:mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-end gap-5 relative z-30">

        <!-- Judul & Subjudul -->
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Kepala Keluarga (Wali)
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-1">
                Daftar penanggung jawab dan wali santri berdasarkan Kartu Keluarga.
            </p>
        </div>

        <!-- Toolbar (Filter, Search, & Buttons) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">

            <!-- Form Filter & Search -->
            <form action="{{ route('wali-murid.index') }}" method="GET"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">

                <!-- Filter Kampung -->
                <div class="relative w-full sm:w-auto min-w-[170px] group/filter">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/filter:text-primary dark:group-focus-within/filter:text-primary-dark transition-colors">
                        <i class="bi bi-geo-alt text-xs"></i>
                    </div>
                    <select name="kampung_id" onchange="this.form.submit()"
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">Semua Kampung</option>
                        @foreach ($kampungs as $kp)
                            <option value="{{ $kp->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ $filterKampung == $kp->id ? 'selected' : '' }}>
                                ({{ $kp->kode }})
                                - {{ $kp->nama_kampung }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Ikon Chevron M3 -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-72 group/search">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors duration-300 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                        <i class="bi bi-search text-zinc-400 text-xs"></i>
                    </div>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, No. KK/Reg..."
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold">

                    <!-- Tombol Reset Search (Muncul jika ada filter aktif) -->
                    @if (request('search') || request('kampung_id'))
                        <a href="{{ route('wali-murid.index') }}"
                            class="absolute inset-y-0 right-0 w-9 h-9 my-auto mr-1 flex items-center justify-center text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800 rounded-full transition-colors outline-none"
                            title="Reset Pencarian">
                            <i class="bi bi-x-lg text-xs font-bold"></i>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 w-full sm:w-auto">



                @can('tambah wali-murid')
                    <!-- Tombol Tambah (Primary M3) -->
                    <a href="{{ route('wali-murid.create') }}"
                        class="m3-btn-primary flex-1 sm:flex-none h-10 px-5 text-xs font-black shadow-2xs group/btn">
                        <i class="bi bi-person-plus-fill mr-1.5 text-sm"></i>
                        <span>Tambah KK</span>
                    </a>
                @endcan

                <!-- Tombol Dropdown Opsi Data -->
                <div class="relative inline-block text-left dropdown-container z-30">

                    <button type="button" data-dropdown-toggle="dropdownOpsiData"
                        class="m3-btn-secondary h-10 w-10 !p-0 inline-flex items-center justify-center shadow-2xs"
                        title="Opsi Lainnya">
                        <i class="bi bi-three-dots text-sm"></i>
                    </button>

                    <div id="dropdownOpsiData" class="m3-dropdown-menu hidden right-0 left-auto min-w-[180px] !z-50">

                        <a href="{{ route('wali-murid.export-excel', ['kampung_id' => request('kampung_id')]) }}"
                            class="m3-dropdown-item hover:!text-emerald-600 dark:hover:!text-emerald-400">
                            <i class="bi bi-file-earmark-excel text-base text-emerald-600 dark:text-emerald-400"></i>
                            <span>Export Excel</span>
                        </a>

                        <a href="{{ route('wali-murid.cetak', ['kampung_id' => request('kampung_id')]) }}"
                            target="_blank" class="m3-dropdown-item hover:!text-blue-600 dark:hover:!text-blue-400">
                            <i class="bi bi-printer text-base text-blue-600 dark:text-blue-400"></i>
                            <span>Print Data</span>
                        </a>

                        @can('tambah wali-murid')
                            <hr class="m3-dropdown-divider">

                            <a href="{{ route('wali-murid.import') }}"
                                class="m3-dropdown-item !text-amber-600 dark:!text-amber-400 hover:!bg-amber-50 dark:hover:!bg-amber-900/20 action-modal">
                                <i class="bi bi-file-earmark-arrow-up text-base"></i>
                                <span>Import Data</span>
                            </a>
                        @endcan

                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Data Grid / List Card Container -->
    <div id="data-grid-container" class="flex flex-col gap-3 md:gap-4 relative z-10">
        @include('wali_murid.list', ['walis' => $walis])
    </div>

</x-app-layout>
