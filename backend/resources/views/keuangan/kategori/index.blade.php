@section('title', 'Master Kategori Keuangan')

<x-app-layout>
    <!-- 1. Header Section (Compact M3) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-tags-fill text-xs"></i>
                    <span>Klasifikasi Transaksi</span>
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Kategori & Subkategori Keuangan
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Klasifikasi pos aliran dana madrasah (Pemasukan, Pengeluaran Operasional & Simpanan).
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('keuangan.kategori.create') }}"
                class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-2 action-modal group">
                <i class="bi bi-patch-plus-fill text-sm transition-transform group-hover:scale-110"></i>
                <span>Tambah Kategori</span>
            </a>
        </div>
    </div>

    <!-- 2. Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Card 1: Total Kategori -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-black border border-purple-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-tags-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Total Kategori
                </span>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white">
                    {{ $totalKategori }} <span class="text-xs font-bold text-zinc-400">Item</span>
                </span>
            </div>
        </div>

        <!-- Card 2: Pemasukan -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-arrow-down-left-circle-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Pemasukan
                </span>
                <span class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400">
                    {{ $totalPemasukan }} <span class="text-xs font-bold text-zinc-400">Pos</span>
                </span>
            </div>
        </div>

        <!-- Card 3: Pengeluaran -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-black border border-rose-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-arrow-up-right-circle-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Pengeluaran
                </span>
                <span class="text-xl md:text-2xl font-black text-rose-600 dark:text-rose-400">
                    {{ $totalPengeluaran }} <span class="text-xs font-bold text-zinc-400">Pos</span>
                </span>
            </div>
        </div>

        <!-- Card 4: Simpanan -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black border border-blue-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Simpanan
                </span>
                <span class="text-xl md:text-2xl font-black text-blue-600 dark:text-blue-400">
                    {{ $totalSimpanan }} <span class="text-xs font-bold text-zinc-400">Pos</span>
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Filter Section -->
    <div class="mb-4 relative z-10 flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form action="{{ route('keuangan.kategori.index') }}" method="GET" class="w-full sm:w-80 relative group/search">
            <div
                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                <i class="bi bi-search text-xs"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari kode atau nama kategori..."
                class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold">
            @if (request('search'))
                <a href="{{ route('keuangan.kategori.index', request()->except('search')) }}"
                    class="absolute inset-y-0 right-0 w-9 h-full flex items-center justify-center text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors outline-none">
                    <i class="bi bi-x-lg text-xs font-bold"></i>
                </a>
            @endif
        </form>

        <div class="w-full sm:w-auto flex items-center gap-2">
            <form action="{{ route('keuangan.kategori.index') }}" method="GET" id="filterJenisForm" class="m-0 p-0">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="jenis" onchange="this.form.submit()"
                    class="m3-input-glass w-full sm:w-48 text-xs font-bold">
                    <option value="">Semua Jenis Aliran</option>
                    <option value="pemasukan" {{ request('jenis') == 'pemasukan' ? 'selected' : '' }}>Pemasukan (Cash In)</option>
                    <option value="pengeluaran" {{ request('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (Cash Out)</option>
                    <option value="simpanan" {{ request('jenis') == 'simpanan' ? 'selected' : '' }}>Simpanan / Tabungan</option>
                </select>
            </form>
        </div>
    </div>

    <!-- 4. Data Grid Container (Dipertahankan ID untuk AJAX Refresh handler custom-script.js) -->
    <div id="data-grid-container"
        class="m3-glass-card rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-2xs relative z-10">
        @include('keuangan.kategori.list', ['kategoris' => $kategoris])
    </div>
</x-app-layout>
