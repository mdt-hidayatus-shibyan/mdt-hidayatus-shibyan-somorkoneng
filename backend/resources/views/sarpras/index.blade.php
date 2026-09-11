@section('title', 'Sarana & Prasarana')

<x-app-layout>
    <!-- Header Section (Compact M3) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                Sarana & Prasarana
            </h2>
            <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                Inventarisasi aset, fasilitas fisik madrasah, dan pemantauan kondisi barang.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">
            <!-- Add Button -->
            @can('create sarpras')
                <a href="{{ route('sarpras.create') }}" class="m3-btn-primary w-full sm:w-auto action-modal group/btn">
                    <i class="bi bi-plus-lg text-base transition-transform duration-300 group-hover/btn:scale-110"></i>
                    <span>Tambah Sarpras</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Metric / Stats Cards Section -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Total Jenis -->
        <div class="m3-glass-card p-4 flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-2xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center text-xl shrink-0 border border-primary/20">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    Total Jenis Sarpras
                </p>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ $totalJenis }} <span class="text-xs font-semibold text-zinc-400 font-sans">Item</span>
                </h3>
            </div>
        </div>

        <!-- Total Fisik Unit -->
        <div class="m3-glass-card p-4 flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl shrink-0 border border-blue-500/20">
                <i class="bi bi-stack"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    Total Unit Fisik
                </p>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ number_format($totalUnit, 0, ',', '.') }} <span
                        class="text-xs font-semibold text-zinc-400 font-sans">Unit</span>
                </h3>
            </div>
        </div>

        <!-- Tersedia / Baik -->
        <div class="m3-glass-card p-4 flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0 border border-emerald-500/20">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    Tersedia / Baik
                </p>
                <h3 class="text-xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                    {{ number_format($totalTersedia, 0, ',', '.') }} <span
                        class="text-xs font-semibold text-zinc-400 font-sans">Unit</span>
                </h3>
            </div>
        </div>

        <!-- Rusak -->
        <div class="m3-glass-card p-4 flex items-center gap-3.5">
            <div
                class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl shrink-0 border border-rose-500/20">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                    Kondisi Rusak
                </p>
                <h3 class="text-xl font-black text-rose-600 dark:text-rose-400 tracking-tight">
                    {{ number_format($totalRusakRingan + $totalRusakBerat, 0, ',', '.') }} <span
                        class="text-xs font-semibold text-zinc-400 font-sans">Unit</span>
                </h3>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar Section -->
    <div class="m3-glass-card p-4 sm:p-5 mb-6 relative z-10">
        <form action="{{ route('sarpras.index') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

            <!-- Search Input -->
            <div class="relative group/search">
                <div
                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama/kode sarpras..." class="m3-input-glass w-full !pl-9 text-xs">
            </div>

            <!-- Filter Gedung -->
            <div class="relative group/filter">
                <select name="gedung_id" onchange="this.form.submit()"
                    class="m3-input-glass w-full appearance-none cursor-pointer text-xs !pr-8">
                    <option value="">-- Semua Gedung --</option>
                    @foreach ($gedungs as $gd)
                        <option value="{{ $gd->id }}" {{ request('gedung_id') == $gd->id ? 'selected' : '' }}>
                            {{ $gd->nama_gedung }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                    <i class="bi bi-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- Filter Ruangan -->
            <div class="relative group/filter">
                <select name="ruangan_id" onchange="this.form.submit()"
                    class="m3-input-glass w-full appearance-none cursor-pointer text-xs !pr-8">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach ($ruangans as $rg)
                        <option value="{{ $rg->id }}" {{ request('ruangan_id') == $rg->id ? 'selected' : '' }}>
                            {{ $rg->nama_ruangan }} {{ $rg->nama_kamar ? "($rg->nama_kamar)" : '' }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                    <i class="bi bi-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- Filter Kategori -->
            <div class="relative group/filter">
                <select name="kategori" onchange="this.form.submit()"
                    class="m3-input-glass w-full appearance-none cursor-pointer text-xs !pr-8">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                    <i class="bi bi-chevron-down text-xs"></i>
                </div>
            </div>

            <!-- Filter Kondisi & Action Reset -->
            <div class="flex items-center gap-2">
                <div class="relative group/filter flex-1">
                    <select name="kondisi" onchange="this.form.submit()"
                        class="m3-input-glass w-full appearance-none cursor-pointer text-xs !pr-8">
                        <option value="">-- Semua Kondisi --</option>
                        <option value="tersedia" {{ request('kondisi') == 'tersedia' ? 'selected' : '' }}>Tersedia /
                            Baik</option>
                        <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak
                            Ringan</option>
                        <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak
                            Berat</option>
                        <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak Total
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>

                @if (request('search') || request('gedung_id') || request('ruangan_id') || request('kategori') || request('kondisi'))
                    <a href="{{ route('sarpras.index') }}"
                        class="h-9 px-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-zinc-200/60 flex items-center justify-center transition-colors text-xs font-bold shrink-0"
                        title="Reset Filter">
                        <i class="bi bi-x-lg mr-1"></i> Reset
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Data Grid Container -->
    <div id="data-grid-container" class="space-y-3 relative z-10">
        @include('sarpras.list', ['sarprasItems' => $sarprasItems])
    </div>
</x-app-layout>
