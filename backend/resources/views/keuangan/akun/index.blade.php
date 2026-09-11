@section('title', 'Master Pos Akun Keuangan')

<x-app-layout>
    <!-- 1. Header Section (Compact M3) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-wallet2 text-xs"></i>
                    <span>Perbendaharaan & Kas</span>
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Pos Akun Keuangan
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Kelola pos buku kas madrasah (SPP/Syahriyah, Kas Umum, Tabungan, Koperasi, Sarpras & Donatur).
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('keuangan.akun.create') }}"
                class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-2 action-modal group">
                <i class="bi bi-patch-plus-fill text-sm transition-transform group-hover:scale-110"></i>
                <span>Tambah Pos Akun</span>
            </a>
        </div>
    </div>

    <!-- 2. Metrics Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Card 1: Total Saldo Berjalan Kas -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Total Saldo Kas Pos
                </span>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white font-mono">
                    Rp {{ number_format($totalSaldoKas, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 2: Total Pos Akun -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black border border-blue-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Total Pos Terdaftar
                </span>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white">
                    {{ $totalAkun }} <span class="text-xs font-bold text-zinc-400">Pos</span>
                </span>
            </div>
        </div>

        <!-- Card 3: Pos Aktif -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-black border border-purple-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Pos Akun Aktif
                </span>
                <span class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400">
                    {{ $totalAktif }} <span class="text-xs font-bold text-zinc-400">Aktif</span>
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Filter Section -->
    <div class="mb-4 relative z-10">
        <form action="{{ route('keuangan.akun.index') }}" method="GET"
            class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="w-full sm:w-80 relative group/search">
                <div
                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode atau nama pos akun..."
                    class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold">
                @if (request('search'))
                    <a href="{{ route('keuangan.akun.index') }}"
                        class="absolute inset-y-0 right-0 w-9 h-full flex items-center justify-center text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors outline-none">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </a>
                @endif
            </div>

            <div class="w-full sm:w-auto flex items-center gap-2">
                <select name="tipe_akun" onchange="this.form.submit()"
                    class="m3-input-glass w-full sm:w-48 text-xs font-bold">
                    <option value="">Semua Tipe Akun</option>
                    <option value="kas" {{ request('tipe_akun') == 'kas' ? 'selected' : '' }}>Kas Tunai (Fisik)
                    </option>
                    <option value="bank" {{ request('tipe_akun') == 'bank' ? 'selected' : '' }}>Bank / Rekening
                    </option>
                    <option value="operasional" {{ request('tipe_akun') == 'operasional' ? 'selected' : '' }}>
                        Operasional</option>
                    <option value="investasi" {{ request('tipe_akun') == 'investasi' ? 'selected' : '' }}>Investasi &
                        Aset</option>
                    <option value="kewajiban" {{ request('tipe_akun') == 'kewajiban' ? 'selected' : '' }}>Kewajiban
                    </option>
                    <option value="ekuitas" {{ request('tipe_akun') == 'ekuitas' ? 'selected' : '' }}>Ekuitas</option>
                </select>
            </div>
        </form>
    </div>

    <!-- 4. Data Grid Container (Dipertahankan ID untuk AJAX Refresh handler custom-script.js) -->
    <div id="data-grid-container"
        class="m3-glass-card rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-2xs relative z-10">
        @include('keuangan.akun.list', ['akuns' => $akuns])
    </div>
</x-app-layout>
