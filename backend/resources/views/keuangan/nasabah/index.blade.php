@section('title', 'Data Nasabah Peminjam')

<x-app-layout>
    <!-- 1. Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-people-fill text-xs"></i>
                    <span>Database Peminjam</span>
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Data Nasabah Peminjam
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Database nasabah (Ustadz, Pengurus, Wali Murid, dan Masyarakat Umum) beserta riwayat pinjaman & agunan.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button type="button" onclick="openCreateNasabahModal()"
                class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-md flex items-center justify-center gap-2 transition-all active:scale-95 group">
                <i class="bi bi-person-plus-fill text-sm transition-transform group-hover:scale-110"></i>
                <span>Daftar Nasabah Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Metrics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Card 1: Total Nasabah -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black border border-blue-500/20 flex-shrink-0">
                <i class="bi bi-people"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Total Nasabah
                </span>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white">
                    {{ $totalNasabah }} <span class="text-xs font-bold text-zinc-400">Orang</span>
                </span>
            </div>
        </div>

        <!-- Card 2: Peminjam Aktif -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-black border border-amber-500/20 flex-shrink-0">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Peminjam Berjalan
                </span>
                <span class="text-xl md:text-2xl font-black text-amber-600 dark:text-amber-400">
                    {{ $totalPeminjamAktif }} <span class="text-xs font-bold text-zinc-400">Aktif</span>
                </span>
            </div>
        </div>

        <!-- Card 3: Nasabah Ustadz -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 flex-shrink-0">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Ustadz / Pengurus
                </span>
                <span class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400">
                    {{ $totalUstadzNasabah }}
                </span>
            </div>
        </div>

        <!-- Card 4: Wali Murid -->
        <div
            class="m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-black border border-purple-500/20 flex-shrink-0">
                <i class="bi bi-person-hearts"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Wali Murid
                </span>
                <span class="text-xl md:text-2xl font-black text-purple-600 dark:text-purple-400">
                    {{ $totalWaliNasabah }}
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Filter & Table Container -->
    <div
        class="m3-glass-card rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-2xs relative z-10">
        <!-- Filter Bar -->
        <div
            class="p-4 md:p-5 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="w-full sm:w-80 relative">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                <input type="text" id="searchNasabahInput" onkeyup="handleSearchNasabah(event)"
                    placeholder="Cari kode, nama, NIK, atau no HP..."
                    class="w-full pl-9 pr-3.5 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
            </div>

            <div class="w-full sm:w-auto flex items-center gap-2">
                <select id="filterTipeNasabah" onchange="loadNasabahTable()"
                    class="w-full sm:w-48 px-3.5 py-2 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    <option value="">Semua Tipe Nasabah</option>
                    <option value="ustadz">Ustadz</option>
                    <option value="wali_murid">Wali Murid</option>
                    <option value="pengurus">Pengurus</option>
                    <option value="umum">Umum / Masyarakat</option>
                </select>
            </div>
        </div>

        <!-- Table Container -->
        <div id="nasabahTableContainer">
            @include('keuangan.nasabah.list', ['nasabahs' => $nasabahs])
        </div>
    </div>

    <!-- Modal Form Nasabah -->
    <div id="nasabahModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div
            class="relative w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 overflow-hidden max-h-[90vh] flex flex-col">
            <div
                class="flex items-center justify-between pb-4 mb-4 border-b border-zinc-200/80 dark:border-zinc-800 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg font-black border border-blue-500/20">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white" id="modalNasabahTitle">
                            Daftarkan Nasabah Peminjam
                        </h3>
                        <p class="text-[11px] text-zinc-400">Pengisian biodata & kelengkapan identitas nasabah</p>
                    </div>
                </div>
                <button type="button" onclick="closeNasabahModal()"
                    class="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <div id="nasabahModalContent" class="overflow-y-auto custom-scrollbar flex-1 pr-1">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let searchNasabahTimer;

            function handleSearchNasabah(e) {
                clearTimeout(searchNasabahTimer);
                searchNasabahTimer = setTimeout(() => {
                    loadNasabahTable();
                }, 300);
            }

            function loadNasabahTable(url = null) {
                const search = document.getElementById('searchNasabahInput').value;
                const tipe = document.getElementById('filterTipeNasabah').value;
                const targetUrl = url ? url + (url.includes('?') ? '&' : '?') +
                    `table_only=1&search=${encodeURIComponent(search)}&tipe_nasabah=${encodeURIComponent(tipe)}` :
                    `{{ route('keuangan.nasabah.index') }}?table_only=1&search=${encodeURIComponent(search)}&tipe_nasabah=${encodeURIComponent(tipe)}`;
                const container = document.getElementById('nasabahTableContainer');

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

            function openCreateNasabahModal() {
                const modal = document.getElementById('nasabahModal');
                const title = document.getElementById('modalNasabahTitle');
                const content = document.getElementById('nasabahModalContent');

                title.innerText = 'Daftarkan Nasabah Peminjam';
                content.innerHTML =
                    `<div class="p-8 text-center text-zinc-400"><i class="bi bi-arrow-repeat animate-spin text-2xl block mb-2"></i>Memuat form...</div>`;
                modal.classList.remove('hidden');

                fetch(`{{ route('keuangan.nasabah.create') }}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        content.innerHTML = html;
                    });
            }

            function openEditNasabahModal(id) {
                const modal = document.getElementById('nasabahModal');
                const title = document.getElementById('modalNasabahTitle');
                const content = document.getElementById('nasabahModalContent');

                title.innerText = 'Edit Data Nasabah';
                content.innerHTML =
                    `<div class="p-8 text-center text-zinc-400"><i class="bi bi-arrow-repeat animate-spin text-2xl block mb-2"></i>Memuat data...</div>`;
                modal.classList.remove('hidden');

                fetch(`{{ url('keuangan/nasabah') }}/${id}/edit`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        content.innerHTML = html;
                    });
            }

            function closeNasabahModal() {
                document.getElementById('nasabahModal').classList.add('hidden');
            }

            function submitNasabahForm(e) {
                e.preventDefault();
                const form = e.target;
                const btn = document.getElementById('btnSubmitNasabah');
                const nasabahId = document.getElementById('nasabah_id')?.value;
                const isEdit = !!nasabahId;
                const url = isEdit ? `{{ url('keuangan/nasabah') }}/${nasabahId}` : `{{ route('keuangan.nasabah.store') }}`;

                btn.disabled = true;
                btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin text-sm"></i> Menyimpan...`;

                const formData = new FormData(form);

                fetch(url, {
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
                        closeNasabahModal();
                        loadNasabahTable();
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
                        btn.innerHTML =
                            `<i class="bi bi-check2-circle text-sm"></i> <span>${isEdit ? 'Simpan Perubahan' : 'Daftarkan Nasabah'}</span>`;
                    });
            }

            function confirmDeleteNasabah(id, name) {
                Swal.fire({
                    title: 'Hapus Data Nasabah?',
                    text: `Apakah Anda yakin ingin menghapus nasabah "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#71717a',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('keuangan/nasabah') }}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                                loadNasabahTable();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            })
                            .catch(err => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menghapus',
                                    text: err.message || 'Tidak dapat menghapus data nasabah.'
                                });
                            });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
