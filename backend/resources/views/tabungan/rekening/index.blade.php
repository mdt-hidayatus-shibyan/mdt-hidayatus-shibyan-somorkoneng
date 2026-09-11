@section('title', 'Master Rekening Tabungan')
<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-20">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Master Rekening Tabungan
            </h2>
            <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5 uppercase tracking-wider">
                Kelola seluruh buku tabungan nasabah (Murid, Ustadz, Kas Ruangan, dan Umum)
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto shrink-0">
            <x-button :href="route('tabungan.setor.index')" variant="secondary" size="sm" icon="bi-arrow-down-circle-fill">
                <span>Setor Tunai</span>
            </x-button>

            <x-button :href="route('tabungan.tarik.index')" variant="secondary" size="sm" icon="bi-arrow-up-circle-fill">
                <span>Tarik Tunai</span>
            </x-button>

            <x-button :href="route('tabungan.barcode.generator')" variant="outline" size="sm" icon="bi-upc-scan">
                <span>Generator Barcode</span>
            </x-button>

            <x-button :href="route('tabungan.rekening.create')" variant="primary" size="sm" icon="bi-plus-circle-fill">
                <span>Buka Rekening</span>
            </x-button>
        </div>
    </div>

    <!-- MAIN CARD: TOOLBAR & TABLE -->
    <div class="m3-glass-card overflow-hidden flex flex-col relative z-10 shadow-2xs">

        <!-- Toolbar Filter & Search -->
        <div
            class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-950/40 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

            <!-- Title & Info -->
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center border border-primary/20 shrink-0">
                    <i class="bi bi-journal-bookmark-fill text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                        Daftar Rekening Tabungan
                    </h3>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                        Total {{ $tabungans->total() }} Rekening Ditemukan
                    </p>
                </div>
            </div>

            <!-- Filter Controls Form -->
            <form action="{{ route('tabungan.rekening.index') }}" method="GET" id="filterMasterRekening"
                class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 w-full lg:w-auto">

                <!-- Filter Kategori / Jenis Nasabah -->
                <div class="relative w-full sm:w-44 group/filter">
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 group-focus-within/filter:text-primary dark:group-focus-within/filter:text-primary-dark transition-colors">
                        <i class="bi bi-people-fill text-xs"></i>
                    </div>
                    <select name="jenis" id="filterJenis" onchange="handleJenisChange(this)"
                        class="m3-input-glass w-full !pl-8.5 !pr-8 text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @foreach (['Murid', 'Ustadz', 'Kas Ruangan', 'Umum'] as $j)
                            <option value="{{ $j }}" {{ request('jenis') == $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-[10px] font-black"></i>
                    </div>
                </div>

                <!-- Filter Ruangan (Aktif saat Kategori Murid Dipilih) -->
                @if (request('jenis') == 'Murid')
                    <div class="relative w-full sm:w-48 group/filter">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 group-focus-within/filter:text-primary dark:group-focus-within/filter:text-primary-dark transition-colors">
                            <i class="bi bi-door-open-fill text-xs"></i>
                        </div>
                        <select name="ruangan_id" onchange="this.form.submit()"
                            class="m3-input-glass w-full !pl-8.5 !pr-8 text-xs font-bold appearance-none cursor-pointer">
                            <option value="">Semua Ruangan</option>
                            @foreach ($daftarRuangan as $r)
                                <option value="{{ $r->id }}"
                                    {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->nama_ruangan }} ({{ $r->level?->nama_level ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-chevron-down text-[10px] font-black"></i>
                        </div>
                    </div>
                @endif

                <!-- Search Box -->
                <div class="relative w-full sm:w-56 group/search">
                    <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark transition-colors">
                        <i class="bi bi-search text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari No. Rek / Nama..."
                        class="m3-input-glass w-full !pl-8.5 !pr-3 text-xs font-medium">
                </div>

                <!-- Tombol Reset Filter -->
                @if (request('jenis') || request('ruangan_id') || request('search'))
                    <x-button :href="route('tabungan.rekening.index')" variant="danger" size="sm" icon="bi-x-circle-fill">
                        <span>Reset</span>
                    </x-button>
                @endif
            </form>
        </div>

        <!-- Content Body: Tabel atau Empty State -->
        @if ($tabungans->isNotEmpty())
            <div class="overflow-x-auto custom-scrollbar">
                <table class="m3-table">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">No</th>
                            <th>Nomor Rekening</th>
                            <th>Nama Nasabah & Rekening</th>
                            <th>Kategori</th>
                            <th class="text-right">Total Setor</th>
                            <th class="text-right">Total Tarik</th>
                            <th class="text-right">Saldo Saat Ini</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tabungans as $tab)
                            <tr>
                                <td class="text-center font-bold text-zinc-400">
                                    {{ ($tabungans->currentPage() - 1) * $tabungans->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <span class="font-mono font-black text-zinc-900 dark:text-white text-xs block">
                                        {{ $tab->nomor_rekening }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400">
                                        Dibuat: {{ $tab->created_at->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <x-avatar :name="$tab->nama_nasabah" size="sm" />
                                        <div>
                                            <span
                                                class="font-bold text-zinc-900 dark:text-white block leading-tight text-xs">
                                                {{ $tab->nama_nasabah }}
                                            </span>
                                            <span class="text-[10px] text-primary dark:text-primary-dark font-semibold">
                                                {{ $tab->nama_rekening }} • {{ $tab->identitas_nasabah }}
                                                @if (
                                                    $tab->jenis_nasabah == 'Murid' &&
                                                        !empty($tab->murid?->nama_ruangan_aktif) &&
                                                        $tab->murid->nama_ruangan_aktif !== '-')
                                                    • Ruangan: {{ $tab->murid->nama_ruangan_aktif }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($tab->jenis_nasabah == 'Murid')
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                            MURID
                                        </span>
                                    @elseif ($tab->jenis_nasabah == 'Ustadz')
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                            USTADZ
                                        </span>
                                    @elseif ($tab->jenis_nasabah == 'Kas Ruangan')
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20">
                                            KAS KELAS
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                            UMUM
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right font-mono text-xs text-zinc-500">
                                    Rp {{ number_format($tab->total_setor, 0, ',', '.') }}
                                </td>
                                <td class="text-right font-mono text-xs text-rose-500">
                                    Rp {{ number_format($tab->total_tarik, 0, ',', '.') }}
                                </td>
                                <td
                                    class="text-right font-mono font-black text-xs text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($tab->saldo, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if ($tab->status == 'Aktif')
                                        <span
                                            class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            AKTIF
                                        </span>
                                    @elseif ($tab->status == 'Dibagikan')
                                        <span
                                            class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                            DIBAGIKAN
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded text-[8px] font-black uppercase bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                            {{ $tab->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <x-button :href="route('tabungan.rekening.detail', $tab->id)" variant="secondary" size="sm" icon="bi-eye">
                                        Detail
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($tabungans->hasPages())
                <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
                    {{ $tabungans->links('vendor.pagination.custom') }}
                </div>
            @endif
        @else
            <!-- Standardized Empty State Component -->
            <div class="p-8">
                <x-empty-state icon="bi-journal-bookmark" title="Rekening Tidak Ditemukan"
                    message="Tidak ada data buku tabungan yang sesuai dengan parameter pencarian Anda.">
                    @if (request('jenis') || request('ruangan_id') || request('search'))
                        <x-button :href="route('tabungan.rekening.index')" variant="secondary" size="sm" icon="bi-arrow-counterclockwise">
                            Reset Filter
                        </x-button>
                    @endif
                    <x-button :href="route('tabungan.rekening.create')" variant="primary" size="sm" icon="bi-plus-circle-fill">
                        Buka Rekening Baru
                    </x-button>
                </x-empty-state>
            </div>
        @endif

    </div>

    <script>
        function handleJenisChange(select) {
            const form = select.form;
            const ruanganSelect = form.querySelector('select[name="ruangan_id"]');
            if (ruanganSelect && select.value !== 'Murid') {
                ruanganSelect.value = '';
            }
            form.submit();
        }
    </script>

</x-app-layout>
