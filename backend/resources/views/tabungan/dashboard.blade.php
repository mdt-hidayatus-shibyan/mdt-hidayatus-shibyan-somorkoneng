@section('title', 'Dashboard Tabungan Madrasah')
<x-app-layout>

    <!-- Header Action Buttons -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Tabungan Madrasah
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Pusat ringkasan saldo, statistik mutasi, dan aksi cepat transaksi nasabah.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('tabungan.komplain.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 border border-rose-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all relative">
                <i class="bi bi-chat-square-dots-fill text-sm"></i>
                <span>Komplain Setoran</span>
                @if (($ringkasan['komplain_pending'] ?? 0) > 0)
                    <span
                        class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-black bg-rose-600 text-white rounded-full ml-1 animate-pulse">
                        {{ $ringkasan['komplain_pending'] }}
                    </span>
                @endif
            </a>
            <a href="{{ route('tabungan.cek-mutasi.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-sm active:scale-95 transition-all">
                <i class="bi bi-shield-check text-sm"></i>
                <span>Cek Mutasi & Verifikasi</span>
            </a>
            <a href="{{ route('tabungan.setor.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-sm active:scale-95 transition-all">
                <i class="bi bi-arrow-down-circle-fill text-sm"></i>
                <span>Setor Tunai</span>
            </a>
            <a href="{{ route('tabungan.tarik.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-arrow-up-circle-fill text-sm"></i>
                <span>Tarik Tunai</span>
            </a>
            <a href="{{ route('tabungan.rincian.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-700 dark:text-blue-400 border border-blue-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-bar-chart-line-fill text-sm"></i>
                <span>Rincian Kas</span>
            </a>
            <a href="{{ route('tabungan.rekening.create') }}" class="m3-btn-secondary text-xs">
                <i class="bi bi-plus-circle-fill text-sm"></i>
                <span>Buka Rekening</span>
            </a>
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
            <a href="{{ route('tabungan.komplain.index') }}"
                class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl transition-colors shrink-0 text-center shadow-sm">
                Buka Halaman Komplain
            </a>
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
                        •
                        Penutupan: <span
                            class="font-bold text-rose-500">{{ $periodeAktif->tanggal_penutupan->format('d M Y') }}</span>
                        •
                        Rencana Pembagian: <span
                            class="font-bold text-emerald-600 dark:text-emerald-400">{{ $periodeAktif->tanggal_pembagian->format('d M Y') }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('tabungan.pembagian.index', ['periode_id' => $periodeAktif->id]) }}"
                class="px-3.5 py-2 bg-primary/10 hover:bg-primary/20 text-primary dark:text-primary-dark text-xs font-black rounded-xl transition-colors shrink-0 text-center border border-primary/20">
                Lihat Simulasi Pembagian
            </a>
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
            <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
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
            <h3
                class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight text-blue-600 dark:text-blue-400">
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
            <h3
                class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight text-indigo-600 dark:text-indigo-400">
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
            <h3
                class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight text-teal-600 dark:text-teal-400">
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
                <a href="{{ route('tabungan.rekening.index') }}"
                    class="text-xs font-bold text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 bg-zinc-50/40 dark:bg-zinc-950/20">
                            <th class="py-3 px-4">Kode & Tanggal</th>
                            <th class="py-3 px-3">Nasabah</th>
                            <th class="py-3 px-3">Jenis</th>
                            <th class="py-3 px-3 text-right">Nominal Kotor</th>
                            <th class="py-3 px-3 text-right">Potongan</th>
                            <th class="py-3 px-4 text-right">Nominal Bersih</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium text-zinc-700 dark:text-zinc-300">
                        @forelse ($transaksiTerbaru as $trx)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-mono font-bold text-zinc-900 dark:text-white block">
                                        {{ $trx->kode_transaksi }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400">
                                        {{ $trx->tanggal->format('d M Y') }} • {{ $trx->petugas->name ?? 'Admin' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2">
                                        <x-avatar :name="$trx->tabungan->nama_nasabah ?? 'Nasabah'" size="xs" />
                                        <div class="min-w-0">
                                            <span
                                                class="font-bold text-zinc-900 dark:text-white block truncate max-w-[150px]">
                                                {{ $trx->tabungan->nama_nasabah ?? '-' }}
                                            </span>
                                            <span class="text-[10px] text-zinc-400 font-mono">
                                                {{ $trx->tabungan->nomor_rekening ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
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
                                <td class="py-3 px-3 text-right font-mono font-bold">
                                    Rp {{ number_format($trx->nominal_kotor, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-[11px] text-rose-500">
                                    {{ $trx->nominal_potongan > 0 ? '-Rp ' . number_format($trx->nominal_potongan, 0, ',', '.') : '-' }}
                                </td>
                                <td
                                    class="py-3 px-4 text-right font-mono font-black {{ $trx->jenis_transaksi == 'Setor' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-900 dark:text-white' }}">
                                    {{ $trx->jenis_transaksi == 'Setor' ? '+' : '' }}Rp
                                    {{ number_format($trx->nominal_bersih, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-zinc-400">
                                    <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                                    Belum ada mutasi transaksi tabungan yang tercatat.
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
                    <a href="{{ route('tabungan.pengaturan.index') }}"
                        class="text-xs font-bold text-primary dark:text-primary-dark hover:underline inline-flex items-center gap-1">
                        <span>Ubah Pengaturan Potongan</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
