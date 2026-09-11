@section('title', 'Dashboard Tabungan Madrasah')
<x-app-layout>

    <!-- Header Action Buttons -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Tabungan Madrasah
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Pusat ringkasan saldo, statistik mutasi, dan aksi cepat transaksi nasabah.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <x-button :href="route('tabungan.komplain.index')" variant="secondary" size="sm" icon="bi-chat-square-dots-fill">
                <span>Komplain</span>
                @if (($ringkasan['komplain_pending'] ?? 0) > 0)
                    <span
                        class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-black bg-rose-600 text-white rounded-full ml-1 animate-pulse">
                        {{ $ringkasan['komplain_pending'] }}
                    </span>
                @endif
            </x-button>

            <x-button :href="route('tabungan.cek-mutasi.index')" variant="outline" size="sm" icon="bi-shield-check">
                <span>Cek Mutasi</span>
            </x-button>

            <x-button :href="route('tabungan.setor.index')" variant="primary" size="sm" icon="bi-arrow-down-circle-fill">
                <span>Setor Tunai</span>
            </x-button>

            <x-button :href="route('tabungan.tarik.index')" variant="secondary" size="sm" icon="bi-arrow-up-circle-fill">
                <span>Tarik Tunai</span>
            </x-button>

            <x-button :href="route('tabungan.rincian.index')" variant="secondary" size="sm" icon="bi-bar-chart-line-fill">
                <span>Rincian Kas</span>
            </x-button>

            <x-button :href="route('tabungan.rekening.create')" variant="secondary" size="sm" icon="bi-plus-circle-fill">
                <span>Buka Rekening</span>
            </x-button>
        </div>
    </div>

    <!-- Alert Banner Komplain Setoran Pending -->
    @if (($ringkasan['komplain_pending'] ?? 0) > 0)
        <div
            class="mb-6 p-4 m3-glass-card rounded-2xl md:rounded-3xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-l-4 border-l-rose-500 bg-rose-500/5">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 text-lg border border-rose-500/20">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h4 class="font-black text-xs text-rose-700 dark:text-rose-400 uppercase tracking-wider">
                        Ada {{ $ringkasan['komplain_pending'] }} Pengajuan Komplain Setoran Menunggu Verifikasi
                    </h4>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Wali murid telah mengajukan koreksi atas nominal setor tunai yang salah input. Segera periksa
                        dan sesuaikan saldo nasabah.
                    </p>
                </div>
            </div>
            <x-button :href="route('tabungan.komplain.index')" variant="danger" size="sm">
                Buka Halaman Komplain
            </x-button>
        </div>
    @endif

    <!-- Alert / Banner Periode Aktif -->
    @if ($periodeAktif)
        <div
            class="mb-6 p-4 m3-glass-card rounded-2xl md:rounded-3xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-l-4 border-l-primary bg-primary/5">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-2xl bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center shrink-0 text-lg border border-primary/20">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <h4 class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider">
                        Program Aktif: {{ $periodeAktif->nama_periode }}
                    </h4>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Mulai: <span
                            class="font-bold text-zinc-700 dark:text-zinc-300">{{ $periodeAktif->tanggal_mulai->format('d M Y') }}</span>
                        • Penutupan: <span
                            class="font-bold text-rose-500">{{ $periodeAktif->tanggal_penutupan->format('d M Y') }}</span>
                        • Rencana Pembagian: <span
                            class="font-bold text-emerald-600 dark:text-emerald-400">{{ $periodeAktif->tanggal_pembagian->format('d M Y') }}</span>
                    </p>
                </div>
            </div>
            <x-button :href="route('tabungan.pembagian.index', ['periode_id' => $periodeAktif->id])" variant="outline" size="sm">
                Lihat Simulasi Pembagian
            </x-button>
        </div>
    @endif

    <!-- GRID STATISTIK REKAP KAS TABUNGAN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Total Kas Tabungan -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Total Saldo Kas Tabungan
                </span>
                <div
                    class="w-9 h-9 rounded-2xl bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center text-base shrink-0 border border-primary/20">
                    <i class="bi bi-safe2-fill"></i>
                </div>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight font-mono">
                Rp {{ number_format($ringkasan['total_saldo'], 0, ',', '.') }}
            </h3>
            <div class="flex items-center gap-2 mt-2.5 text-[11px] font-bold text-zinc-500 dark:text-zinc-400">
                <span>{{ $ringkasan['total_rekening_aktif'] }} Rekening Aktif</span>
                <span>•</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-extrabold">+Rp
                    {{ number_format($ringkasan['setor_hari_ini'], 0, ',', '.') }} hari ini</span>
            </div>
        </div>

        <!-- Card 2: Saldo Tabungan Murid -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Tabungan Murid
                </span>
                <div
                    class="w-9 h-9 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base shrink-0 border border-blue-500/20">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-blue-600 dark:text-blue-400 tracking-tight font-mono">
                Rp {{ number_format($ringkasan['saldo_murid'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] font-bold text-zinc-400 mt-2.5">
                Tabungan berjangka & harian murid
            </p>
        </div>

        <!-- Card 3: Saldo Tabungan Ustadz -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Tabungan Ustadz
                </span>
                <div
                    class="w-9 h-9 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base shrink-0 border border-indigo-500/20">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight font-mono">
                Rp {{ number_format($ringkasan['saldo_ustadz'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] font-bold text-zinc-400 mt-2.5">
                Simpanan dewan pengajar / ustadz
            </p>
        </div>

        <!-- Card 4: Saldo Kas Kelas & Umum -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs">
            <div class="flex justify-between items-start mb-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400 dark:text-zinc-500">
                    Kas Kelas & Umum
                </span>
                <div
                    class="w-9 h-9 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-base shrink-0 border border-teal-500/20">
                    <i class="bi bi-door-open-fill"></i>
                </div>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-teal-600 dark:text-teal-400 tracking-tight font-mono">
                Rp {{ number_format($ringkasan['saldo_kas'] + $ringkasan['saldo_umum'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] font-bold text-zinc-400 mt-2.5 truncate"
                title="Kas Kelas: Rp {{ number_format($ringkasan['saldo_kas'], 0, ',', '.') }} • Umum: Rp {{ number_format($ringkasan['saldo_umum'], 0, ',', '.') }}">
                Kas Kelas: Rp {{ number_format($ringkasan['saldo_kas'], 0, ',', '.') }} • Umum: Rp
                {{ number_format($ringkasan['saldo_umum'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- CONTENT DUA KOLOM: TRANSAKSI TERBARU & TOP REKENING -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri (2 Span): 10 Transaksi Terakhir -->
        <div class="lg:col-span-2 m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-2xs">
            <div
                class="p-4 sm:p-5 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                <span
                    class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-clock-history text-primary text-sm"></i>
                    Mutasi Transaksi Terakhir
                </span>
                <x-button :href="route('tabungan.rekening.index')" variant="text" size="sm" icon="bi-arrow-right" icon-position="right">
                    Lihat Semua
                </x-button>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="m3-table">
                    <thead>
                        <tr>
                            <th>Kode & Tanggal</th>
                            <th>Nasabah</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-right">Nominal Kotor</th>
                            <th class="text-right">Potongan</th>
                            <th class="text-right">Nominal Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiTerbaru as $trx)
                            <tr>
                                <td>
                                    <span class="font-mono font-bold text-zinc-900 dark:text-white text-xs block">
                                        {{ $trx->kode_transaksi }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400">
                                        {{ $trx->tanggal->format('d M Y') }} • {{ $trx->petugas->name ?? 'Admin' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <x-avatar :name="$trx->tabungan->nama_nasabah ?? 'Nasabah'" size="xs" />
                                        <div class="min-w-0">
                                            <span
                                                class="font-bold text-zinc-900 dark:text-white block truncate max-w-[150px] text-xs">
                                                {{ $trx->tabungan->nama_nasabah ?? '-' }}
                                            </span>
                                            <span class="text-[10px] text-zinc-400 font-mono">
                                                {{ $trx->tabungan->nomor_rekening ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($trx->jenis_transaksi == 'Setor')
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            SETOR
                                        </span>
                                    @elseif ($trx->jenis_transaksi == 'Tarik')
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            TARIK
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                            {{ strtoupper($trx->jenis_transaksi) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-right font-mono font-bold text-xs">
                                    Rp {{ number_format($trx->nominal_kotor, 0, ',', '.') }}
                                </td>
                                <td class="text-right font-mono text-[11px] text-rose-500">
                                    {{ $trx->nominal_potongan > 0 ? '-Rp ' . number_format($trx->nominal_potongan, 0, ',', '.') : '-' }}
                                </td>
                                <td
                                    class="text-right font-mono font-black text-xs {{ $trx->jenis_transaksi == 'Setor' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-900 dark:text-white' }}">
                                    {{ $trx->jenis_transaksi == 'Setor' ? '+' : '' }}Rp
                                    {{ number_format($trx->nominal_bersih, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8">
                                    <x-empty-state icon="bi-inbox" title="Tidak Ada Mutasi"
                                        message="Belum ada mutasi transaksi tabungan yang tercatat." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan (1 Span): Saldo Terbesar & Tarif Potongan -->
        <div class="space-y-6">
            <!-- Top Rekening -->
            <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-2xs">
                <div
                    class="p-4 sm:p-5 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                    <span
                        class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-trophy-fill text-amber-500 text-sm"></i>
                        Saldo Terbesar
                    </span>
                </div>

                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800/60 p-2">
                    @forelse ($topRekening as $top)
                        <li
                            class="p-2.5 flex items-center justify-between gap-3 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 rounded-xl transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div
                                    class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center justify-center font-bold text-xs shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="min-w-0">
                                    <h5 class="font-black text-xs text-zinc-900 dark:text-white truncate">
                                        {{ $top->nama_nasabah }}
                                    </h5>
                                    <p class="text-[10px] text-zinc-400 font-mono">
                                        {{ $top->nomor_rekening }} • {{ $top->jenis_nasabah }}
                                    </p>
                                </div>
                            </div>
                            <span class="font-mono font-black text-xs text-emerald-600 dark:text-emerald-400 shrink-0">
                                Rp {{ number_format($top->saldo, 0, ',', '.') }}
                            </span>
                        </li>
                    @empty
                        <li class="py-8 text-center text-xs text-zinc-400">
                            Belum ada data rekening.
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- Card Informasi Tarif Potongan Aktif -->
            <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl shadow-2xs">
                <h5
                    class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider mb-3.5 flex items-center gap-2">
                    <i class="bi bi-percent text-rose-500 text-sm"></i>
                    Tarif Potongan Aktif (Musyawarah)
                </h5>
                <div class="space-y-2.5 text-xs">
                    <div
                        class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800/60">
                        <span class="text-zinc-500 dark:text-zinc-400">Tabungan Murid:</span>
                        <span class="font-black text-rose-600 dark:text-rose-400 font-mono">10.00%</span>
                    </div>
                    <div
                        class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800/60">
                        <span class="text-zinc-500 dark:text-zinc-400">Tabungan Ustadz:</span>
                        <span class="font-black text-indigo-600 dark:text-indigo-400 font-mono">2.50%</span>
                    </div>
                    <div
                        class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800/60">
                        <span class="text-zinc-500 dark:text-zinc-400">Kas Kelas:</span>
                        <span class="font-black text-emerald-600 dark:text-emerald-400 font-mono">0.00% (Bebas
                            Potongan)</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-zinc-500 dark:text-zinc-400">Umum / Reguler:</span>
                        <span class="font-black text-purple-600 dark:text-purple-400 font-mono">10.00%</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/60 text-right">
                    <x-button :href="route('tabungan.pengaturan.index')" variant="text" size="sm" icon="bi-arrow-right"
                        icon-position="right">
                        Ubah Pengaturan Potongan
                    </x-button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
