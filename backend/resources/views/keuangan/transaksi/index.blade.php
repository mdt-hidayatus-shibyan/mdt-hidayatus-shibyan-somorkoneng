@section('title', 'Buku Kas & Transaksi Keuangan')

<x-app-layout>
    <!-- 1. Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-journal-text text-xs"></i>
                    <span>Jurnal Finansial</span>
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Buku Kas & Transaksi
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Catatan mutasi penerimaan kas masuk, pengeluaran operasional, dan transfer bank madrasah.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button type="button" onclick="openCreateTransaksiModal()"
                class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-md flex items-center justify-center gap-2 transition-all active:scale-95 group">
                <i class="bi bi-plus-circle-fill text-sm transition-transform group-hover:scale-110"></i>
                <span>Catat Transaksi Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Card 1: Total Pemasukan -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 flex-shrink-0">
                <i class="bi bi-arrow-down-left-circle-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Total Pemasukan
                </span>
                <span class="text-base md:text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 2: Total Pengeluaran -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-black border border-rose-500/20 flex-shrink-0">
                <i class="bi bi-arrow-up-right-circle-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Total Pengeluaran
                </span>
                <span class="text-base md:text-xl font-black text-rose-600 dark:text-rose-400 font-mono">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 3: Total Mutasi -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black border border-blue-500/20 flex-shrink-0">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Mutasi Kas / Bank
                </span>
                <span class="text-base md:text-xl font-black text-blue-600 dark:text-blue-400 font-mono">
                    Rp {{ number_format($totalMutasi, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Card 4: Net Cash Flow -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-black border border-purple-500/20 flex-shrink-0">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Surplus / Defisit
                </span>
                <span
                    class="text-base md:text-xl font-black font-mono {{ $surplusDefisit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Filter & Table Container -->
    <div
        class="m3-glass-card rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-2xs relative z-10">
        <!-- Filter Form -->
        <form id="filterTransaksiForm" onsubmit="applyFilterTransaksi(event)"
            class="p-4 md:p-5 border-b border-zinc-200/80 dark:border-zinc-800 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Search -->
            <div>
                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Cari
                    Keyword</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                    <input type="text" id="filterSearch" name="search" placeholder="Kode, ref, uraian..."
                        class="w-full pl-8 pr-3 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
            </div>

            <!-- Pos Akun -->
            <div>
                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Pos Akun</label>
                <select id="filterAkunId" name="akun_keuangan_id"
                    class="w-full px-3 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    <option value="">Semua Pos Akun</option>
                    @foreach ($akuns as $a)
                        <option value="{{ $a->id }}">{{ $a->nama_akun }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jenis Transaksi -->
            <div>
                <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Jenis
                    Aliran</label>
                <select id="filterJenis" name="jenis_transaksi"
                    class="w-full px-3 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    <option value="">Semua Jenis</option>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                    <option value="mutasi">Mutasi Kas</option>
                    <option value="simpanan">Simpanan</option>
                </select>
            </div>

            <!-- Rentang Tanggal -->
            <div class="grid grid-cols-2 gap-1.5">
                <div>
                    <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Dari</label>
                    <input type="date" id="filterStartDate" name="start_date" value="{{ $startDate }}"
                        class="w-full px-2.5 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">Sampai</label>
                    <input type="date" id="filterEndDate" name="end_date" value="{{ $endDate }}"
                        class="w-full px-2.5 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                </div>
            </div>

            <!-- Tombol Filter -->
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="w-full py-2 px-3 rounded-2xl bg-primary hover:bg-primary-dark text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="bi bi-funnel-fill"></i>
                    <span>Terapkan</span>
                </button>
                <button type="button" onclick="resetFilterTransaksi()"
                    class="py-2 px-3 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 text-xs font-bold transition-all"
                    title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </form>

        <!-- Table Container -->
        <div id="transaksiTableContainer">
            @include('keuangan.transaksi.list', ['transaksis' => $transaksis])
        </div>
    </div>

    <!-- Modal Form Catat Transaksi -->
    <div id="transaksiModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div
            class="relative w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 overflow-hidden max-h-[90vh] flex flex-col">
            <div
                class="flex items-center justify-between pb-4 mb-4 border-b border-zinc-200/80 dark:border-zinc-800 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg font-black border border-emerald-500/20">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white" id="modalTransaksiTitle">
                            Catat Transaksi Keuangan
                        </h3>
                        <p class="text-[11px] text-zinc-400">Pencatatan kas masuk, kas keluar, dan mutasi kas/bank</p>
                    </div>
                </div>
                <button type="button" onclick="closeTransaksiModal()"
                    class="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <div id="transaksiModalContent" class="overflow-y-auto custom-scrollbar flex-1 pr-1">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div id="detailTransaksiModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div
            class="relative w-full max-w-lg bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg font-black border border-blue-500/20">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white">
                            Rincian Transaksi Keuangan
                        </h3>
                        <p class="text-[11px] text-zinc-400">Informasi detail jurnal pembukuan</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailTransaksiModal()"
                    class="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <div id="detailTransaksiModalContent">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function applyFilterTransaksi(e) {
                if (e) e.preventDefault();
                loadTransaksiTable();
            }

            function resetFilterTransaksi() {
                document.getElementById('filterSearch').value = '';
                document.getElementById('filterAkunId').value = '';
                document.getElementById('filterJenis').value = '';
                document.getElementById('filterStartDate').value = '{{ now()->startOfMonth()->toDateString() }}';
                document.getElementById('filterEndDate').value = '{{ now()->endOfMonth()->toDateString() }}';
                loadTransaksiTable();
            }

            function loadTransaksiTable(url = null) {
                const form = document.getElementById('filterTransaksiForm');
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                params.append('table_only', '1');

                const targetUrl = url ? url + (url.includes('?') ? '&' : '?') + params.toString() :
                    `{{ route('keuangan.transaksi.index') }}?` + params.toString();
                const container = document.getElementById('transaksiTableContainer');

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

            function openCreateTransaksiModal() {
                const modal = document.getElementById('transaksiModal');
                const content = document.getElementById('transaksiModalContent');

                content.innerHTML =
                    `<div class="p-8 text-center text-zinc-400"><i class="bi bi-arrow-repeat animate-spin text-2xl block mb-2"></i>Memuat form...</div>`;
                modal.classList.remove('hidden');

                fetch(`{{ route('keuangan.transaksi.create') }}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        content.innerHTML = html;
                    });
            }

            function closeTransaksiModal() {
                document.getElementById('transaksiModal').classList.add('hidden');
            }

            function openDetailTransaksiModal(id) {
                const modal = document.getElementById('detailTransaksiModal');
                const content = document.getElementById('detailTransaksiModalContent');

                content.innerHTML =
                    `<div class="p-8 text-center text-zinc-400"><i class="bi bi-arrow-repeat animate-spin text-2xl block mb-2"></i>Memuat detail...</div>`;
                modal.classList.remove('hidden');

                fetch(`{{ url('keuangan/transaksi') }}/${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        content.innerHTML = html;
                    });
            }

            function closeDetailTransaksiModal() {
                document.getElementById('detailTransaksiModal').classList.add('hidden');
            }

            function submitTransaksiForm(e) {
                e.preventDefault();
                const form = e.target;
                const btn = document.getElementById('btnSubmitTransaksi');
                btn.disabled = true;
                btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin text-sm"></i> Menyimpan...`;

                const formData = new FormData(form);

                fetch("{{ route('keuangan.transaksi.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        closeTransaksiModal();
                        loadTransaksiTable();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    })
                    .catch(err => {
                        let msg = err.message || 'Terjadi kesalahan validasi.';
                        if (err.errors) {
                            msg = Object.values(err.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            html: msg
                        });
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = `<i class="bi bi-check2-circle text-sm"></i> <span>Simpan Transaksi</span>`;
                    });
            }

            function confirmBatalTransaksi(id, code) {
                Swal.fire({
                    title: 'Batalkan Transaksi?',
                    text: `Apakah Anda yakin ingin membatalkan transaksi ${code}? Saldo akun dan rekening bank akan otomatis dikembalikan.`,
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: 'Tuliskan alasan pembatalan...',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#71717a',
                    confirmButtonText: 'Ya, Batalkan Transaksi!',
                    cancelButtonText: 'Tutup'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('keuangan/transaksi') }}/${id}/batal`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    alasan: result.value || 'Pembatalan manual'
                                })
                            })
                            .then(async res => {
                                const data = await res.json();
                                if (!res.ok) throw data;
                                return data;
                            })
                            .then(data => {
                                loadTransaksiTable();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Dibatalkan!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Membatalkan',
                                    text: err.message || 'Tidak dapat membatalkan transaksi.'
                                });
                            });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
