@section('title', 'Sistem Pinjaman & Agunan Madrasah')

<x-app-layout>
    <!-- 1. Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-shield-lock-fill text-xs"></i>
                    <span>Sistem Pinjaman & Agunan</span>
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Pinjaman & Manajemen Agunan
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Pencatatan pengajuan pinjaman madrasah, persetujuan, titipan agunan fisik, dan kartu angsuran bulanan.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('keuangan.pinjaman.create') }}"
                class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-md flex items-center justify-center gap-2 transition-all active:scale-95 group">
                <i class="bi bi-plus-circle-fill text-sm transition-transform group-hover:scale-110"></i>
                <span>Ajukan Pinjaman Baru</span>
            </a>
        </div>
    </div>

    <!-- 2. Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Card 1: Total Pengajuan Pinjaman -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black border border-blue-500/20 flex-shrink-0">
                <i class="bi bi-journal-check"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Total Pengajuan
                </span>
                <span class="text-base md:text-xl font-black text-zinc-900 dark:text-white font-mono">
                    Rp {{ number_format($totalNominalPengajuan, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 2: Total Dana Dicairkan -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-black border border-purple-500/20 flex-shrink-0">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Dana Dicairkan
                </span>
                <span class="text-base md:text-xl font-black text-purple-600 dark:text-purple-400 font-mono">
                    Rp {{ number_format($totalPencairan, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 3: Total Terbayar -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 flex-shrink-0">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Total Angsuran Masuk
                </span>
                <span class="text-base md:text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                    Rp {{ number_format($totalTerbayar, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 4: Sisa Piutang Berjalan -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-black border border-rose-500/20 flex-shrink-0">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Sisa Piutang Berjalan
                </span>
                <span class="text-base md:text-xl font-black text-rose-600 dark:text-rose-400 font-mono">
                    Rp {{ number_format($totalSisaPiutang, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Status Tabs Filter -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar">
        <button type="button" onclick="filterByStatus('')"
            class="status-tab px-4 py-2 rounded-2xl text-xs font-black transition-all border {{ !request('status') ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100' }}">
            Semua ({{ $pinjamans->total() }})
        </button>
        <button type="button" onclick="filterByStatus('pengajuan')"
            class="status-tab px-4 py-2 rounded-2xl text-xs font-black transition-all border {{ request('status') === 'pengajuan' ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100' }}">
            Pengajuan ({{ $countPengajuan }})
        </button>
        <button type="button" onclick="filterByStatus('disetujui')"
            class="status-tab px-4 py-2 rounded-2xl text-xs font-black transition-all border {{ request('status') === 'disetujui' ? 'bg-blue-500 text-white border-blue-500 shadow-sm' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100' }}">
            Disetujui ({{ $countDisetujui }})
        </button>
        <button type="button" onclick="filterByStatus('dicairkan')"
            class="status-tab px-4 py-2 rounded-2xl text-xs font-black transition-all border {{ request('status') === 'dicairkan' ? 'bg-purple-500 text-white border-purple-500 shadow-sm' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100' }}">
            Berjalan / Dicairkan ({{ $countBerjalan }})
        </button>
        <button type="button" onclick="filterByStatus('lunas')"
            class="status-tab px-4 py-2 rounded-2xl text-xs font-black transition-all border {{ request('status') === 'lunas' ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-300 border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100' }}">
            Lunas ({{ $countLunas }})
        </button>
    </div>

    <!-- 4. Filter & Table Container -->
    <div
        class="m3-glass-card rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-2xs relative z-10">
        <!-- Search Bar -->
        <div
            class="p-4 md:p-5 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="w-full sm:w-80 relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                <input type="text" id="searchPinjamanInput" onkeyup="handleSearchPinjaman(event)"
                    placeholder="Cari kode pinjaman, nama nasabah..."
                    class="w-full pl-9 pr-3.5 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
            </div>
        </div>

        <!-- Table Container -->
        <div id="pinjamanTableContainer">
            @include('keuangan.pinjaman.list', ['pinjamans' => $pinjamans])
        </div>
    </div>

    @push('scripts')
        <script>
            let searchPinjamanTimer;
            let activeStatusFilter = '{{ request('status', '') }}';

            function filterByStatus(status) {
                activeStatusFilter = status;
                loadPinjamanTable();
            }

            function handleSearchPinjaman(e) {
                clearTimeout(searchPinjamanTimer);
                searchPinjamanTimer = setTimeout(() => {
                    loadPinjamanTable();
                }, 300);
            }

            function loadPinjamanTable(url = null) {
                const search = document.getElementById('searchPinjamanInput').value;
                const targetUrl = url ? url + (url.includes('?') ? '&' : '?') +
                    `table_only=1&search=${encodeURIComponent(search)}&status=${encodeURIComponent(activeStatusFilter)}` :
                    `{{ route('keuangan.pinjaman.index') }}?table_only=1&search=${encodeURIComponent(search)}&status=${encodeURIComponent(activeStatusFilter)}`;
                const container = document.getElementById('pinjamanTableContainer');

                container.classList.add('opacity-50');

                fetch(targetUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        container.innerHTML = html;
                        container.classList.remove('opacity-50');
                    })
                    .catch(err => {
                        container.classList.remove('opacity-50');
                        console.error(err);
                    });
            }
        </script>
    @endpush
</x-app-layout>
