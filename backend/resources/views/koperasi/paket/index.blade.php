@section('title', 'Master Paket Bundling Koperasi')
<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <div
                    class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20 shrink-0">
                    <i class="bi bi-collection-fill text-lg"></i>
                </div>
                <span>Paket Bundling Kitab & Seragam</span>
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Kelola bundling paket kitab murid per level/kelas untuk awal tahun ajaran baru.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('koperasi.pos.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-sm active:scale-95 transition-all">
                <i class="bi bi-calculator-fill text-sm"></i>
                <span>Buka Kasir POS</span>
            </a>

            <a href="{{ route('koperasi.produk.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs border border-zinc-200 dark:border-zinc-700 active:scale-95 transition-all">
                <i class="bi bi-box-seam text-sm"></i>
                <span>Master Produk</span>
            </a>

            <a href="{{ route('koperasi.paket.create') }}" class="action-modal m3-btn-primary text-xs">
                <i class="bi bi-plus-circle-fill text-sm"></i>
                <span>Buat Paket Baru</span>
            </a>
        </div>
    </div>

    <!-- MAIN CARD -->
    <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden flex flex-col relative z-10 shadow-2xs">

        <!-- Toolbar Filter -->
        <div
            class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/70 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20 shrink-0">
                    <i class="bi bi-layers-fill text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                        Daftar Paket Bundling
                    </h3>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                        Total {{ $pakets->total() }} Paket Terdaftar
                    </p>
                </div>
            </div>

            <form action="{{ route('koperasi.paket.index') }}" method="GET"
                class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 w-full lg:w-auto">
                <!-- Filter Level/Kelas -->
                <div class="w-full sm:w-48">
                    <select name="level_id" onchange="this.form.submit()"
                        class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Level Kelas</option>
                        @foreach ($levels as $lvl)
                            <option value="{{ $lvl->id }}" {{ request('level_id') == $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->nama_level }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search -->
                <div class="relative w-full sm:w-56">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama / kode paket..." class="m3-input-glass w-full text-xs font-semibold">
                    <button type="submit"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <i class="bi bi-search text-xs"></i>
                    </button>
                </div>

                @if (request()->hasAny(['level_id', 'search']))
                    <a href="{{ route('koperasi.paket.index') }}"
                        class="h-10 px-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-xs flex items-center justify-center shrink-0 transition-colors"
                        title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- TABEL DATA PAKET CONTAINER (Auto Refreshed by custom-script.js) -->
        <div id="data-grid-container">
            @include('koperasi.paket.list', ['pakets' => $pakets, 'levels' => $levels])
        </div>

    </div>

</x-app-layout>
