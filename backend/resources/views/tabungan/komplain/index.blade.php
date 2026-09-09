@section('title', 'Verifikasi Komplain Setor Tunai Tabungan')
<x-app-layout>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                <i class="bi bi-shield-exclamation text-amber-500"></i>
                <span>Komplain Setor Tunai</span>
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Verifikasi dan penyesuaian saldo tabungan atas sanggahan setoran dari wali murid.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <x-button :href="route('tabungan.setor.index')" variant="secondary" size="sm" icon="bi-arrow-down-circle-fill">
                Setor Tunai
            </x-button>
            <x-button :href="route('tabungan.rekening.index')" variant="secondary" size="sm" icon="bi-journal-bookmark-fill">
                Master Rekening
            </x-button>
        </div>
    </div>

    <!-- Alert Flash -->
    @if (session('success'))
        <div
            class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border-2 border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs font-bold space-y-1 relative z-10 flex items-start gap-3 shadow-2xs">
            <div
                class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base shrink-0 font-black border border-emerald-500/30">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="flex-1">
                <div class="font-black text-sm text-emerald-700 dark:text-emerald-300">Berhasil</div>
                <p class="text-xs font-semibold text-emerald-700/90 dark:text-emerald-300/90 mt-0.5">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div
            class="mb-6 p-4 rounded-2xl bg-rose-500/10 border-2 border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs font-bold space-y-1 relative z-10 flex items-start gap-3 shadow-2xs">
            <div
                class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-base shrink-0 font-black border border-rose-500/30">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            <div class="flex-1">
                <div class="font-black text-sm text-rose-700 dark:text-rose-300">Pemberitahuan</div>
                <p class="text-xs font-semibold text-rose-700/90 dark:text-rose-300/90 mt-0.5">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    @endif

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
        <!-- Menunggu Verifikasi -->
        <a href="{{ route('tabungan.komplain.index', ['status' => 'Menunggu_Verifikasi']) }}"
            class="p-4 rounded-2xl border transition-all duration-200 {{ $status === 'Menunggu_Verifikasi' ? 'bg-amber-500/15 border-amber-500/40 dark:bg-amber-500/20' : 'bg-white dark:bg-zinc-900 border-zinc-200/80 dark:border-zinc-800/80 hover:border-amber-500/30' }}">
            <div class="flex items-center justify-between mb-2">
                <span
                    class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400">Menunggu</span>
                <div
                    class="w-7 h-7 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ number_format($counts['pending']) }}</div>
            <div class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Selisih: Rp {{ number_format($counts['total_selisih_pending'], 0, ',', '.') }}
            </div>
        </a>

        <!-- Disetujui -->
        <a href="{{ route('tabungan.komplain.index', ['status' => 'Disetujui']) }}"
            class="p-4 rounded-2xl border transition-all duration-200 {{ $status === 'Disetujui' ? 'bg-emerald-500/15 border-emerald-500/40 dark:bg-emerald-500/20' : 'bg-white dark:bg-zinc-900 border-zinc-200/80 dark:border-zinc-800/80 hover:border-emerald-500/30' }}">
            <div class="flex items-center justify-between mb-2">
                <span
                    class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Disetujui</span>
                <div
                    class="w-7 h-7 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ number_format($counts['disetujui']) }}
            </div>
            <div class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">Saldo telah disesuaikan</div>
        </a>

        <!-- Ditolak -->
        <a href="{{ route('tabungan.komplain.index', ['status' => 'Ditolak']) }}"
            class="p-4 rounded-2xl border transition-all duration-200 {{ $status === 'Ditolak' ? 'bg-rose-500/15 border-rose-500/40 dark:bg-rose-500/20' : 'bg-white dark:bg-zinc-900 border-zinc-200/80 dark:border-zinc-800/80 hover:border-rose-500/30' }}">
            <div class="flex items-center justify-between mb-2">
                <span
                    class="text-[11px] font-extrabold uppercase tracking-wider text-rose-600 dark:text-rose-400">Ditolak</span>
                <div
                    class="w-7 h-7 rounded-xl bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ number_format($counts['ditolak']) }}
            </div>
            <div class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">Klaim tidak diterima</div>
        </a>

        <!-- Total Komplain -->
        <a href="{{ route('tabungan.komplain.index', ['status' => 'semua']) }}"
            class="p-4 rounded-2xl border transition-all duration-200 {{ $status === 'semua' ? 'bg-primary/10 border-primary/30 dark:bg-primary-dark/15' : 'bg-white dark:bg-zinc-900 border-zinc-200/80 dark:border-zinc-800/80 hover:border-primary/30' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Semua
                    Status</span>
                <div
                    class="w-7 h-7 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center text-sm">
                    <i class="bi bi-list-ul"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-zinc-900 dark:text-white">{{ number_format($counts['total']) }}</div>
            <div class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">Total pengajuan komplain
            </div>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div
        class="bg-white dark:bg-zinc-900 rounded-3xl p-4 md:p-6 border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs mb-6">
        <form action="{{ route('tabungan.komplain.index') }}" method="GET"
            class="flex flex-col md:flex-row items-center justify-between gap-3">
            <input type="hidden" name="status" value="{{ $status }}">

            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                <a href="{{ route('tabungan.komplain.index', ['status' => 'semua', 'q' => $q]) }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ $status === 'semua' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200' }}">
                    Semua ({{ $counts['total'] }})
                </a>
                <a href="{{ route('tabungan.komplain.index', ['status' => 'Menunggu_Verifikasi', 'q' => $q]) }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 {{ $status === 'Menunggu_Verifikasi' ? 'bg-amber-500 text-white' : 'bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-500/20' }}">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Menunggu ({{ $counts['pending'] }})</span>
                </a>
                <a href="{{ route('tabungan.komplain.index', ['status' => 'Disetujui', 'q' => $q]) }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ $status === 'Disetujui' ? 'bg-emerald-600 text-white' : 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-500/20' }}">
                    Disetujui ({{ $counts['disetujui'] }})
                </a>
                <a href="{{ route('tabungan.komplain.index', ['status' => 'Ditolak', 'q' => $q]) }}"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 {{ $status === 'Ditolak' ? 'bg-rose-600 text-white' : 'bg-rose-500/10 text-rose-700 dark:text-rose-400 hover:bg-rose-500/20' }}">
                    Ditolak ({{ $counts['ditolak'] }})
                </a>
            </div>

            <div class="relative w-full md:w-80">
                <input type="text" name="q" value="{{ $q }}"
                    placeholder="Cari santri, NISM, kode komplain..."
                    class="w-full pl-9 pr-4 py-2 text-xs font-semibold rounded-2xl bg-zinc-100 dark:bg-zinc-800 border-none focus:ring-2 focus:ring-primary dark:focus:ring-primary-dark">
                <i class="bi bi-search absolute left-3 top-2.5 text-zinc-400 text-xs"></i>
                @if ($q)
                    <a href="{{ route('tabungan.komplain.index', ['status' => $status]) }}"
                        class="absolute right-3 top-2 text-zinc-400 hover:text-zinc-600">
                        <i class="bi bi-x-circle-fill text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table List Section -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xs overflow-hidden"
        x-data="komplainHandler()">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="border-b border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-800/20 text-[11px] font-extrabold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        <th class="py-3.5 px-4">Kode & Waktu</th>
                        <th class="py-3.5 px-4">Santri / Nasabah</th>
                        <th class="py-3.5 px-4">Transaksi Asli</th>
                        <th class="py-3.5 px-4">Nominal & Klaim</th>
                        <th class="py-3.5 px-4">Alasan Wali Murid</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
                    @forelse ($komplains as $kmp)
                        @php
                            $trx = $kmp->transaksiTabungan;
                            $murid = $kmp->murid;
                            $isPending = $kmp->status === 'Menunggu_Verifikasi';
                            $isApproved = $kmp->status === 'Disetujui';
                            $isRejected = $kmp->status === 'Ditolak';
                        @endphp
                        <tr
                            class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors {{ $isPending ? 'bg-amber-500/[0.02]' : '' }}">
                            <!-- Kode & Waktu -->
                            <td class="py-3.5 px-4 font-mono">
                                <div class="font-extrabold text-zinc-900 dark:text-white">{{ $kmp->kode_komplain }}
                                </div>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 font-sans mt-0.5">
                                    {{ $kmp->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>

                            <!-- Santri -->
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-zinc-900 dark:text-white">
                                    {{ $murid->nama_lengkap ?? '-' }}</div>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    NISM: {{ $murid->nism ?? '-' }} •
                                    {{ $kmp->tabungan->ruangan->nama_ruangan ?? ($murid->ruangan_aktif ?? '-') }}
                                </div>
                            </td>

                            <!-- Transaksi Asli -->
                            <td class="py-3.5 px-4">
                                <div class="font-mono text-[11px] font-bold text-zinc-700 dark:text-zinc-300">
                                    {{ $trx->kode_transaksi ?? '-' }}</div>
                                <div class="text-[10px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    Tgl: {{ $trx ? \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') : '-' }} •
                                    Petugas: {{ $trx->petugas->name ?? 'Sistem' }}
                                </div>
                            </td>

                            <!-- Nominal & Klaim -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-zinc-500 line-through text-[11px]">Rp
                                        {{ number_format($kmp->nominal_tercatat, 0, ',', '.') }}</span>
                                    <i class="bi bi-arrow-right text-[10px] text-zinc-400"></i>
                                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs">Rp
                                        {{ number_format($kmp->nominal_klaim, 0, ',', '.') }}</span>
                                </div>
                                <div
                                    class="text-[10px] font-bold mt-0.5 {{ $kmp->selisih > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600' }}">
                                    Selisih: {{ $kmp->selisih > 0 ? '+' : '' }}Rp
                                    {{ number_format($kmp->selisih, 0, ',', '.') }}
                                </div>
                            </td>

                            <!-- Alasan -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-[11px] text-zinc-700 dark:text-zinc-300 font-medium line-clamp-2"
                                    title="{{ $kmp->alasan }}">
                                    {{ $kmp->alasan }}
                                </p>
                                @if ($kmp->catatan_verifikasi)
                                    <div
                                        class="mt-1 text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 italic">
                                        Admin: "{{ $kmp->catatan_verifikasi }}"
                                    </div>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4">
                                @if ($isPending)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-500/20">
                                        <i class="bi bi-hourglass-split"></i> Menunggu
                                    </span>
                                @elseif ($isApproved)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20">
                                        <i class="bi bi-check-circle-fill"></i> Disetujui
                                    </span>
                                @elseif ($isRejected)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-500/20">
                                        <i class="bi bi-x-circle-fill"></i> Ditolak
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-zinc-500/15 text-zinc-600 dark:text-zinc-400 border border-zinc-500/20">
                                        {{ $kmp->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                @if ($isPending)
                                    <button type="button" @click="openModal({{ json_encode($kmp) }})"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-primary text-white hover:bg-primary-dark shadow-2xs active:scale-95 transition-all">
                                        <i class="bi bi-patch-check-fill"></i>
                                        <span>Verifikasi</span>
                                    </button>
                                @else
                                    <button type="button" @click="openModal({{ json_encode($kmp) }})"
                                        class="inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-xl text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all">
                                        <i class="bi bi-eye"></i>
                                        <span>Detail</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-zinc-400 dark:text-zinc-500">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-3 text-xl">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="font-bold text-sm text-zinc-700 dark:text-zinc-300">Tidak Ada Komplain
                                    Setor Tunai</div>
                                <p class="text-xs text-zinc-500 mt-1">Belum ada pengajuan komplain setoran dari wali
                                    murid pada filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($komplains->hasPages())
            <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800/80">
                {{ $komplains->links() }}
            </div>
        @endif

        <!-- MODAL VERIFIKASI / DETAIL (Alpine.js) -->
        <div x-show="isModalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-300"
            @keydown.escape.window="closeModal()">
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all"
                @click.away="closeModal()">

                <!-- Modal Header -->
                <div class="p-5 border-b border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-base font-black">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white"
                                x-text="selectedKmp?.status === 'Menunggu_Verifikasi' ? 'Verifikasi Komplain Setor Tunai' : 'Detail Komplain Setor Tunai'">
                            </h3>
                            <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 font-mono"
                                x-text="selectedKmp?.kode_komplain"></p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="text-zinc-400 hover:text-zinc-600 text-lg">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Form Verifikasi -->
                <form :action="'{{ url('tabungan/komplain') }}/' + (selectedKmp?.id ?? 0) + '/verifikasi'"
                    method="POST">
                    @csrf
                    <div class="p-5 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar">
                        <!-- Info Santri & Transaksi Card -->
                        <div
                            class="p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/80 dark:border-zinc-700/60 space-y-2 text-xs">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-[10px] uppercase font-extrabold text-zinc-400">Santri / Rekening
                                    </div>
                                    <div class="font-black text-sm text-zinc-900 dark:text-white"
                                        x-text="selectedKmp?.murid?.nama_lengkap ?? '-'"></div>
                                    <div class="text-[11px] text-zinc-500"
                                        x-text="'NISM: ' + (selectedKmp?.murid?.nism ?? '-')"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] uppercase font-extrabold text-zinc-400">Transaksi Asli
                                    </div>
                                    <div class="font-mono font-bold text-zinc-800 dark:text-zinc-200"
                                        x-text="selectedKmp?.transaksi_tabungan?.kode_transaksi ?? '-'"></div>
                                    <div class="text-[11px] text-zinc-500"
                                        x-text="'Petugas: ' + (selectedKmp?.transaksi_tabungan?.petugas?.name ?? 'Sistem')">
                                    </div>
                                </div>
                            </div>

                            <div
                                class="pt-2 border-t border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] uppercase font-extrabold text-zinc-400">Nominal Tercatat
                                    </div>
                                    <div class="font-extrabold text-zinc-500 line-through text-xs"
                                        x-text="formatRupiah(selectedKmp?.nominal_tercatat)"></div>
                                </div>
                                <i class="bi bi-arrow-right text-zinc-400 text-sm"></i>
                                <div class="text-right">
                                    <div
                                        class="text-[10px] uppercase font-extrabold text-emerald-600 dark:text-emerald-400">
                                        Klaim Wali Murid</div>
                                    <div class="font-black text-emerald-600 dark:text-emerald-400 text-sm"
                                        x-text="formatRupiah(selectedKmp?.nominal_klaim)"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Selisih Banner -->
                        <div
                            class="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-calculator-fill text-amber-600 dark:text-amber-400 text-base"></i>
                                <span class="text-xs font-bold text-amber-900 dark:text-amber-200">Selisih Penyesuaian
                                    Saldo:</span>
                            </div>
                            <span class="text-sm font-black text-amber-600 dark:text-amber-400"
                                x-text="'+ ' + formatRupiah(selectedKmp?.selisih)"></span>
                        </div>

                        <!-- Alasan Komplain -->
                        <div>
                            <label
                                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                                Kronologi / Alasan Wali Murid:
                            </label>
                            <div class="p-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-xs font-medium text-zinc-800 dark:text-zinc-200 leading-relaxed"
                                x-text="selectedKmp?.alasan ?? '-'"></div>
                        </div>

                        <!-- Jika Status Sudah Selesai (Bukan Menunggu) -->
                        <template x-if="selectedKmp?.status !== 'Menunggu_Verifikasi'">
                            <div class="p-3.5 rounded-2xl border text-xs space-y-1.5"
                                :class="selectedKmp?.status === 'Disetujui' ?
                                    'bg-emerald-500/10 border-emerald-500/30 text-emerald-800 dark:text-emerald-300' :
                                    'bg-rose-500/10 border-rose-500/30 text-rose-800 dark:text-rose-300'">
                                <div class="font-black flex items-center gap-1.5">
                                    <i class="bi"
                                        :class="selectedKmp?.status === 'Disetujui' ? 'bi-check-circle-fill' :
                                            'bi-x-circle-fill'"></i>
                                    <span x-text="'Status: ' + selectedKmp?.status"></span>
                                </div>
                                <div class="text-[11px]"
                                    x-text="'Diverifikasi oleh: ' + (selectedKmp?.diverifikasi_oleh?.name ?? 'Admin')">
                                </div>
                                <div class="text-[11px] font-medium"
                                    x-text="'Catatan Admin: ' + (selectedKmp?.catatan_verifikasi ?? '-')"></div>
                            </div>
                        </template>

                        <!-- Pilihan Tindakan Verifikasi (Jika Masih Pending) -->
                        <template x-if="selectedKmp?.status === 'Menunggu_Verifikasi'">
                            <div class="space-y-3 pt-2 border-t border-zinc-200/80 dark:border-zinc-800/80">
                                <div>
                                    <label
                                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2">
                                        Pilih Keputusan Verifikasi:
                                    </label>
                                    <div class="grid grid-cols-2 gap-2.5">
                                        <label
                                            class="p-3 rounded-2xl border-2 flex items-center gap-2.5 cursor-pointer transition-all"
                                            :class="tindakan === 'setujui' ?
                                                'bg-emerald-500/15 border-emerald-500 text-emerald-800 dark:text-emerald-300 font-black' :
                                                'border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 font-bold'">
                                            <input type="radio" name="tindakan" value="setujui" x-model="tindakan"
                                                class="text-emerald-600 focus:ring-emerald-500">
                                            <span>Setujui (Koreksi)</span>
                                        </label>
                                        <label
                                            class="p-3 rounded-2xl border-2 flex items-center gap-2.5 cursor-pointer transition-all"
                                            :class="tindakan === 'tolak' ?
                                                'bg-rose-500/15 border-rose-500 text-rose-800 dark:text-rose-300 font-black' :
                                                'border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 font-bold'">
                                            <input type="radio" name="tindakan" value="tolak" x-model="tindakan"
                                                class="text-rose-600 focus:ring-rose-500">
                                            <span>Tolak Komplain</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                                        Catatan / Alasan Keputusan <span x-show="tindakan === 'tolak'"
                                            class="text-rose-500">*</span>:
                                    </label>
                                    <textarea name="catatan" rows="3" x-model="catatan"
                                        :placeholder="tindakan === 'setujui' ? 'Opsional: Catatan konfirmasi verifikasi...' :
                                            'Wajib diisi: Alasan penolakan sanggahan...'"
                                        class="w-full text-xs font-semibold rounded-2xl bg-zinc-100 dark:bg-zinc-800 border-none focus:ring-2 focus:ring-primary p-3"></textarea>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="p-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-end gap-2">
                        <button type="button" @click="closeModal()"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all">
                            Tutup
                        </button>
                        <template x-if="selectedKmp?.status === 'Menunggu_Verifikasi'">
                            <button type="submit"
                                :class="tindakan === 'setujui' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' :
                                    'bg-rose-600 hover:bg-rose-700 text-white'"
                                class="px-5 py-2 rounded-xl text-xs font-black shadow-2xs active:scale-95 transition-all flex items-center gap-1.5">
                                <i class="bi"
                                    :class="tindakan === 'setujui' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"></i>
                                <span
                                    x-text="tindakan === 'setujui' ? 'Simpan & Sesuaikan Saldo' : 'Tolak Sanggahan'"></span>
                            </button>
                        </template>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js Handler -->
    <script>
        function komplainHandler() {
            return {
                isModalOpen: false,
                selectedKmp: null,
                tindakan: 'setujui',
                catatan: '',
                openModal(kmp) {
                    this.selectedKmp = kmp;
                    this.tindakan = 'setujui';
                    this.catatan = kmp.catatan_verifikasi || '';
                    this.isModalOpen = true;
                },
                closeModal() {
                    this.isModalOpen = false;
                    this.selectedKmp = null;
                },
                formatRupiah(val) {
                    if (!val && val !== 0) return 'Rp 0';
                    return 'Rp ' + Number(val).toLocaleString('id-ID');
                }
            }
        }
    </script>
</x-app-layout>
