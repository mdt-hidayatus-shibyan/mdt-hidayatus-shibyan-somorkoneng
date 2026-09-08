@section('title', 'Rincian & Rekap Kas Tabungan Madrasah')
<x-app-layout>

    <!-- Header Action Buttons & Breadcrumb -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('tabungan.dashboard') }}"
                    class="text-xs font-bold text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                    <i class="bi bi-wallet2"></i> Tabungan Madrasah
                </a>
                <span class="text-zinc-400 dark:text-zinc-600 text-xs">•</span>
                <span class="text-xs font-bold text-zinc-500 dark:text-zinc-400">Rincian & Rekap Kas</span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Rincian & Kontrol Kas Tabungan
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Monitoring perolehan bulanan Masehi, kontrol kas madrasah, rekap potongan, alokasi penarikan, dan audit
                uang pecahan brankas.
            </p>
        </div>

        <!-- Filter Bar & Cetak -->
        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('tabungan.rincian.index') }}"
                class="flex items-center gap-2 flex-wrap">
                <!-- Filter Tahun Masehi -->
                <div class="relative min-w-[130px]">
                    <select name="tahun" onchange="this.form.submit()"
                        class="m3-input-glass text-xs font-bold rounded-xl md:rounded-2xl py-2 px-3 pr-8 w-full cursor-pointer">
                        @foreach ($tahunList as $th)
                            <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>
                                Tahun {{ $th }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Periode Tabungan -->
                <div class="relative min-w-[180px]">
                    <select name="periode_id" onchange="this.form.submit()"
                        class="m3-input-glass text-xs font-bold rounded-xl md:rounded-2xl py-2 px-3 pr-8 w-full cursor-pointer">
                        <option value="">Semua Periode Tabungan</option>
                        @foreach ($periodeList as $p)
                            <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_periode }} {{ $p->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($periodeId || $tahun != date('Y'))
                    <a href="{{ route('tabungan.rincian.index') }}" title="Reset Filter"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-zinc-200/60 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                        <i class="bi bi-arrow-counterclockwise text-sm"></i>
                    </a>
                @endif
            </form>

            <!-- Tombol Cetak Laporan -->
            <a href="{{ route('tabungan.rincian.cetak', ['tahun' => $tahun, 'periode_id' => $periodeId]) }}"
                target="_blank"
                class="inline-flex items-center justify-center gap-1.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl md:rounded-2xl min-h-[38px] px-4 py-2 text-xs shadow-sm active:scale-95 transition-all">
                <i class="bi bi-printer-fill text-sm"></i>
                <span>Cetak Laporan</span>
            </a>
        </div>
    </div>

    <!-- 1. GRID STATISTIK KONTROL KAS & KEWAJIBAN MADRASAH -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Total Saldo Kas Fisik Tabungan -->
        <div
            class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs border-l-4 border-l-emerald-500">
            <div class="flex justify-between items-start mb-2.5">
                <div>
                    <span
                        class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                        Kas Fisik Tabungan
                    </span>
                    <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight mt-1">
                        Rp {{ number_format($totalKasFisik, 0, ',', '.') }}
                    </h3>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0 border border-emerald-500/20">
                    <i class="bi bi-safe2-fill"></i>
                </div>
            </div>
            <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 flex items-center justify-between">
                <span>{{ $totalRekeningAktif }} Rekening Aktif</span>
                <span class="text-zinc-400 dark:text-zinc-500">Total: {{ $totalRekening }}</span>
            </p>
        </div>

        <!-- Card 2: Hak Bersih Nasabah (Kewajiban Madrasah) -->
        <div
            class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs border-l-4 border-l-blue-500">
            <div class="flex justify-between items-start mb-2.5">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400">
                        Hak Bersih Nasabah
                    </span>
                    <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight mt-1">
                        Rp {{ number_format($totalHakBersihNasabah, 0, ',', '.') }}
                    </h3>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0 border border-blue-500/20">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
            <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400">
                Sisa hak yang dapat ditarik nasabah
            </p>
        </div>

        <!-- Card 3: Akumulasi Potongan Bagian Madrasah -->
        <div
            class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs border-l-4 border-l-amber-500">
            <div class="flex justify-between items-start mb-2.5">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 dark:text-amber-400">
                        Potongan Madrasah
                    </span>
                    <h3 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight mt-1">
                        Rp {{ number_format($totalPotonganMadrasah, 0, ',', '.') }}
                    </h3>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0 border border-amber-500/20">
                    <i class="bi bi-percent"></i>
                </div>
            </div>
            <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400">
                Infaq / Alokasi bagi hasil madrasah
            </p>
        </div>

        <!-- Card 4: Perputaran Kas Seluruh Waktu -->
        <div
            class="m3-glass-card p-5 rounded-2xl md:rounded-3xl relative overflow-hidden group shadow-2xs border-l-4 border-l-purple-500">
            <div class="flex justify-between items-start mb-2.5">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-purple-600 dark:text-purple-400">
                        Total Perputaran
                    </span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">
                            +{{ number_format($totalSetorSemua / 1000, 0) }}k
                        </span>
                        <span class="text-zinc-400">/</span>
                        <span class="text-sm font-extrabold text-rose-500">
                            -{{ number_format($totalTarikSemua / 1000, 0) }}k
                        </span>
                    </div>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg shrink-0 border border-purple-500/20">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
            </div>
            <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 truncate">
                Setor: Rp {{ number_format($totalSetorSemua, 0, ',', '.') }} • Tarik: Rp
                {{ number_format($totalTarikSemua, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Alert Banner Rekonsiliasi & Audit Kontrol -->
    <div
        class="mb-6 p-4 m3-glass-card rounded-2xl md:rounded-3xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 border border-emerald-500/20 bg-emerald-500/5">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-lg border border-emerald-500/20">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <h4 class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider">
                    Formula Kontrol Kas Madrasah (Audit Keuangan)
                </h4>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Saldo Kas Fisik (<strong>Rp {{ number_format($totalKasFisik, 0, ',', '.') }}</strong>) =
                    Kewajiban Hak Nasabah (<strong>Rp
                        {{ number_format($totalHakBersihNasabah, 0, ',', '.') }}</strong>) +
                    Potongan Madrasah (<strong>Rp {{ number_format($totalPotonganMadrasah, 0, ',', '.') }}</strong>)
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <span
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-extrabold text-xs border border-emerald-500/20">
                <i class="bi bi-check-circle-fill"></i> Data Terkoreksi
            </span>
        </div>
    </div>

    <!-- 2. TABEL PEROLEHAN UANG PER BULAN MASEHI (JANUARI - DESEMBER) -->
    <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl mb-6 shadow-xs">
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-zinc-200/60 dark:border-zinc-800/60">
            <div>
                <h3
                    class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="bi bi-calendar3 text-primary dark:text-primary-dark"></i>
                    <span>Perolehan Uang Per Bulan Masehi (Tahun {{ $tahun }})</span>
                </h3>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Rekapitulasi setoran masuk, penarikan keluar, dan arus kas bersih bulanan sepanjang tahun
                    {{ $tahun }}.
                </p>
            </div>

            <!-- Ringkasan Net Flow Tahunan -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400">Total Net Flow
                        {{ $tahun }}</span>
                    <p
                        class="text-sm md:text-base font-black {{ $totalNetFlowTahun >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                        {{ $totalNetFlowTahun >= 0 ? '+' : '' }}Rp
                        {{ number_format($totalNetFlowTahun, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto -mx-5 px-5 custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr
                        class="border-b border-zinc-200/60 dark:border-zinc-800/60 text-[11px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                        <th class="py-3 px-3">Bulan Masehi</th>
                        <th class="py-3 px-3 text-right">Trx Setor</th>
                        <th class="py-3 px-3 text-right">Total Setor (Masuk)</th>
                        <th class="py-3 px-3 text-right">Trx Tarik</th>
                        <th class="py-3 px-3 text-right">Total Tarik (Keluar)</th>
                        <th class="py-3 px-3 text-right">Arus Kas Bersih (Net)</th>
                        <th class="py-3 px-3 text-right">Saldo Berjalan</th>
                        <th class="py-3 px-3 text-center">Status Arus Kas</th>
                    </tr>
                </thead>
                <tbody
                    class="divide-y divide-zinc-100 dark:divide-zinc-900/60 text-xs font-bold text-zinc-700 dark:text-zinc-300">
                    @foreach ($rekapBulanan as $b)
                        @php
                            $isCurrentMonth = $tahun == date('Y') && $b['bulan_angka'] == (int) date('m');
                            $hasActivity = $b['jumlah_setor'] > 0 || $b['jumlah_tarik'] > 0;
                        @endphp
                        <tr
                            class="hover:bg-zinc-500/5 transition-colors {{ $isCurrentMonth ? 'bg-primary/5 dark:bg-primary/10 font-extrabold' : '' }}">
                            <td class="py-3 px-3 flex items-center gap-2">
                                <span
                                    class="w-6 h-6 rounded-lg {{ $isCurrentMonth ? 'bg-primary text-white' : 'bg-zinc-200/60 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300' }} flex items-center justify-center text-[10px] font-black">
                                    {{ $b['bulan_angka'] }}
                                </span>
                                <span>{{ $b['nama_bulan'] }}</span>
                                @if ($isCurrentMonth)
                                    <span
                                        class="px-1.5 py-0.5 rounded-md bg-primary/20 text-primary dark:text-primary-dark text-[9px] font-black uppercase">
                                        Bulan Ini
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right text-zinc-500">
                                {{ $b['jumlah_setor'] > 0 ? number_format($b['jumlah_setor']) . 'x' : '-' }}
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-emerald-600 dark:text-emerald-400">
                                {{ $b['nominal_setor'] > 0 ? 'Rp ' . number_format($b['nominal_setor'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-3 text-right text-zinc-500">
                                {{ $b['jumlah_tarik'] > 0 ? number_format($b['jumlah_tarik']) . 'x' : '-' }}
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-rose-500">
                                {{ $b['nominal_tarik'] > 0 ? 'Rp ' . number_format($b['nominal_tarik'], 0, ',', '.') : '-' }}
                            </td>
                            <td
                                class="py-3 px-3 text-right font-black {{ $b['net_flow'] > 0 ? 'text-emerald-600 dark:text-emerald-400' : ($b['net_flow'] < 0 ? 'text-rose-500' : 'text-zinc-400') }}">
                                {{ $b['net_flow'] != 0 ? ($b['net_flow'] > 0 ? '+' : '') . 'Rp ' . number_format($b['net_flow'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-zinc-900 dark:text-white">
                                Rp {{ number_format($b['running_balance'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if ($b['net_flow'] > 0)
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black border border-emerald-500/20">
                                        <i class="bi bi-arrow-up-right"></i> Surplus
                                    </span>
                                @elseif ($b['net_flow'] < 0)
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-black border border-rose-500/20">
                                        <i class="bi bi-arrow-down-right"></i> Defisit
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md bg-zinc-200/50 dark:bg-zinc-800 text-zinc-400 text-[10px] font-bold">
                                        Nihil
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr
                        class="border-t-2 border-zinc-300 dark:border-zinc-700 bg-zinc-500/5 font-black text-xs text-zinc-900 dark:text-white">
                        <td class="py-3 px-3 uppercase tracking-wider">
                            Total Tahun {{ $tahun }}
                        </td>
                        <td class="py-3 px-3 text-right text-zinc-500">
                            {{ number_format($totalTrxSetorTahun) }}x
                        </td>
                        <td class="py-3 px-3 text-right text-emerald-600 dark:text-emerald-400 font-black">
                            Rp {{ number_format($totalSetorTahun, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right text-zinc-500">
                            {{ number_format($totalTrxTarikTahun) }}x
                        </td>
                        <td class="py-3 px-3 text-right text-rose-500 font-black">
                            Rp {{ number_format($totalTarikTahun, 0, ',', '.') }}
                        </td>
                        <td
                            class="py-3 px-3 text-right {{ $totalNetFlowTahun >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }} font-black">
                            {{ $totalNetFlowTahun >= 0 ? '+' : '' }}Rp
                            {{ number_format($totalNetFlowTahun, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-black">
                            Rp {{ number_format($totalNetFlowTahun, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400">12 Bulan</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- 3. DUA KOLOM: PEROLEHAN POTONGAN & REKAP KATEGORI PENARIKAN -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Kolom Kiri: Perolehan Potongan Tabungan per Jenis Nasabah -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl shadow-xs flex flex-col justify-between">
            <div>
                <div
                    class="flex items-center justify-between pb-3 mb-3 border-b border-zinc-200/60 dark:border-zinc-800/60">
                    <div>
                        <h3
                            class="text-base font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                            <i class="bi bi-pie-chart-fill text-amber-500"></i>
                            <span>Perolehan Potongan per Jenis Nasabah</span>
                        </h3>
                        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Rincian persentase dan akumulasi bagi hasil / infaq madrasah.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-5 px-5 custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="border-b border-zinc-200/60 dark:border-zinc-800/60 text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                <th class="py-2.5 px-2">Kategori Nasabah</th>
                                <th class="py-2.5 px-2 text-right">Jml Rek</th>
                                <th class="py-2.5 px-2 text-right">Total Tabungan</th>
                                <th class="py-2.5 px-2 text-center">% Pot</th>
                                <th class="py-2.5 px-2 text-right">Potongan Madrasah</th>
                                <th class="py-2.5 px-2 text-right">Hak Nasabah</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-zinc-100 dark:divide-zinc-900/60 text-xs font-bold text-zinc-700 dark:text-zinc-300">
                            @foreach ($rekapPotongan as $rp)
                                <tr class="hover:bg-zinc-500/5 transition-colors">
                                    <td class="py-2.5 px-2 flex items-center gap-2">
                                        @if ($rp['jenis_nasabah'] === 'Murid')
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        @elseif ($rp['jenis_nasabah'] === 'Ustadz')
                                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                        @elseif ($rp['jenis_nasabah'] === 'Kas Ruangan')
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        @endif
                                        <span>{{ $rp['jenis_nasabah'] }}</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-zinc-500">
                                        {{ number_format($rp['jumlah_rekening']) }}
                                    </td>
                                    <td class="py-2.5 px-2 text-right">
                                        Rp {{ number_format($rp['total_setor'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span
                                            class="px-1.5 py-0.5 rounded-md bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-black border border-amber-500/20">
                                            {{ $rp['persentase_potongan'] }}%
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right font-black text-amber-600 dark:text-amber-400">
                                        Rp {{ number_format($rp['nominal_potongan'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-2 text-right font-extrabold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($rp['sisa_hak_ditarik'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr
                                class="border-t-2 border-zinc-200/80 dark:border-zinc-800 bg-zinc-500/5 font-black text-xs text-zinc-900 dark:text-white">
                                <td class="py-2.5 px-2 uppercase tracking-wider">Total</td>
                                <td class="py-2.5 px-2 text-right text-zinc-500">{{ number_format($totalRekening) }}
                                </td>
                                <td class="py-2.5 px-2 text-right">Rp
                                    {{ number_format($totalSetorSemua, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-2 text-center">-</td>
                                <td class="py-2.5 px-2 text-right text-amber-600 dark:text-amber-400 font-black">
                                    Rp {{ number_format($totalPotonganMadrasah, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-2 text-right font-black">
                                    Rp {{ number_format($totalHakBersihNasabah, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div
                class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-900 flex items-center justify-between text-[11px] font-bold text-zinc-500">
                <span>Aturan: Perhitungan potongan berlaku saat pembagian / batas penarikan.</span>
                <a href="{{ route('tabungan.pengaturan.index') }}"
                    class="text-primary dark:text-primary-dark hover:underline font-extrabold">
                    Ubah Pengaturan <i class="bi bi-chevron-right text-[9px]"></i>
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Rekap Penarikan per Kategori Penarikan -->
        <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl shadow-xs flex flex-col justify-between">
            <div>
                <div
                    class="flex items-center justify-between pb-3 mb-3 border-b border-zinc-200/60 dark:border-zinc-800/60">
                    <div>
                        <h3
                            class="text-base font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                            <i class="bi bi-tags-fill text-primary dark:text-primary-dark"></i>
                            <span>Rincian Penarikan per Kategori</span>
                        </h3>
                        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Distribusi alokasi penarikan dana tabungan berdasarkan tujuan.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-5 px-5 custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="border-b border-zinc-200/60 dark:border-zinc-800/60 text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                <th class="py-2.5 px-2">Kategori Penarikan</th>
                                <th class="py-2.5 px-2 text-center">Tujuan</th>
                                <th class="py-2.5 px-2 text-right">Frekuensi</th>
                                <th class="py-2.5 px-2 text-right">Total Penarikan</th>
                                <th class="py-2.5 px-2 text-right">Porsi</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-zinc-100 dark:divide-zinc-900/60 text-xs font-bold text-zinc-700 dark:text-zinc-300">
                            @forelse ($rekapKategoriPenarikan as $rk)
                                <tr class="hover:bg-zinc-500/5 transition-colors">
                                    <td class="py-2.5 px-2">
                                        <div class="font-extrabold text-zinc-900 dark:text-white">
                                            {{ $rk['nama_kategori'] }}</div>
                                        <span class="text-[10px] text-zinc-400">{{ $rk['kode_kategori'] }}</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span
                                            class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] font-bold">
                                            {{ $rk['jenis_tujuan'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-2 text-right text-zinc-500">
                                        {{ $rk['jumlah_transaksi'] > 0 ? number_format($rk['jumlah_transaksi']) . 'x' : '-' }}
                                    </td>
                                    <td class="py-2.5 px-2 text-right font-black text-rose-500">
                                        {{ $rk['total_nominal'] > 0 ? 'Rp ' . number_format($rk['total_nominal'], 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-2 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <span
                                                class="text-[11px] font-black text-zinc-700 dark:text-zinc-300">{{ $rk['persentase'] }}%</span>
                                            <div
                                                class="w-12 bg-zinc-200 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-rose-500 h-1.5 rounded-full"
                                                    style="width: {{ min(100, $rk['persentase']) }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-xs text-zinc-400">
                                        Belum ada riwayat penarikan tabungan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr
                                class="border-t-2 border-zinc-200/80 dark:border-zinc-800 bg-zinc-500/5 font-black text-xs text-zinc-900 dark:text-white">
                                <td colspan="2" class="py-2.5 px-2 uppercase tracking-wider">Total Penarikan</td>
                                <td class="py-2.5 px-2 text-right text-zinc-500">
                                    {{ number_format(collect($rekapKategoriPenarikan)->sum('jumlah_transaksi')) }}x
                                </td>
                                <td class="py-2.5 px-2 text-right text-rose-500 font-black">
                                    Rp {{ number_format($totalNominalSemuaTarik, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-2 text-right font-black">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div
                class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-900 flex items-center justify-between text-[11px] font-bold text-zinc-500">
                <span>Dapat disesuaikan pada Master Kategori Penarikan.</span>
                <a href="{{ route('tabungan.pengaturan.index') }}"
                    class="text-primary dark:text-primary-dark hover:underline font-extrabold">
                    Kelola Kategori <i class="bi bi-chevron-right text-[9px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. KALKULATOR UANG PECAHAN KAS FISIK (DENOMINASI BRANKAS) -->
    <div x-data="{
        targetMode: 'hak_nasabah', // 'hak_nasabah' | 'kas_fisik'
        targetSaldo: {{ (float) $breakdownHakNasabah['nominal_bulat'] }},
        targetNama: 'Hak Bersih Nasabah (Dibulatkan)',
        hakNasabahCounts: {{ json_encode($breakdownHakNasabah['counts']) }},
        kasFisikCounts: {{ json_encode($breakdownKasFisik['counts']) }},
        counts: {{ json_encode($breakdownHakNasabah['counts']) }},
        values: {
            '100000': 100000,
            '50000': 50000,
            '20000': 20000,
            '10000': 10000,
            '5000': 5000,
            '2000': 2000,
            '1000_kertas': 1000,
            '1000_logam': 1000,
            '500': 500,
            '200': 200,
            '100': 100
        },
        getSubtotal(key) {
            const count = parseInt(this.counts[key]) || 0;
            return count * (this.values[key] || 0);
        },
        get grandTotal() {
            let total = 0;
            for (let key in this.counts) {
                total += this.getSubtotal(key);
            }
            return total;
        },
        get selisih() {
            return this.grandTotal - this.targetSaldo;
        },
        addCount(key, amount) {
            let current = parseInt(this.counts[key]) || 0;
            this.counts[key] = Math.max(0, current + amount);
        },
        setMode(mode) {
            this.targetMode = mode;
            if (mode === 'hak_nasabah') {
                this.targetSaldo = {{ (float) $breakdownHakNasabah['nominal_bulat'] }};
                this.targetNama = 'Hak Bersih Nasabah (Dibulatkan)';
                this.counts = Object.assign({}, this.hakNasabahCounts);
            } else if (mode === 'kas_fisik') {
                this.targetSaldo = {{ (float) $breakdownKasFisik['nominal_bulat'] }};
                this.targetNama = 'Total Kas Fisik Tabungan';
                this.counts = Object.assign({}, this.kasFisikCounts);
            }
        },
        resetAll() {
            for (let key in this.counts) {
                this.counts[key] = 0;
            }
        },
        formatRupiah(num) {
            return 'Rp ' + Math.abs(num).toLocaleString('id-ID');
        }
    }" class="m3-glass-card p-5 md:p-6 rounded-2xl md:rounded-3xl shadow-sm mb-8">

        <div
            class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 mb-5 border-b border-zinc-200/60 dark:border-zinc-800/60">
            <div>
                <h3
                    class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="bi bi-calculator-fill text-emerald-600 dark:text-emerald-400"></i>
                    <span>Kalkulator & Rekonsiliasi Uang Pecahan Kas Fisik (Brankas)</span>
                </h3>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Hitung alokasi pecahan uang tunai brankas dari <strong>Total Hak Nasabah</strong> (dengan pembulatan
                    Rp 100) atau <strong>Total Kas Fisik</strong>.
                </p>
            </div>

            <!-- Tombol Preset Mode & Reset -->
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" @click="setMode('hak_nasabah')"
                    :class="targetMode === 'hak_nasabah' ? 'bg-primary text-white shadow-xs' :
                        'bg-zinc-200/60 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700'"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95">
                    <i class="bi bi-person-check-fill"></i>
                    <span>Hak Nasabah (Rp
                        {{ number_format($breakdownHakNasabah['nominal_bulat'], 0, ',', '.') }})</span>
                </button>

                <button type="button" @click="setMode('kas_fisik')"
                    :class="targetMode === 'kas_fisik' ? 'bg-emerald-600 text-white shadow-xs' :
                        'bg-zinc-200/60 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700'"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95">
                    <i class="bi bi-safe2-fill"></i>
                    <span>Kas Fisik (Rp {{ number_format($breakdownKasFisik['nominal_bulat'], 0, ',', '.') }})</span>
                </button>

                <button type="button" @click="resetAll()"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-zinc-200/60 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 font-bold text-xs transition-colors active:scale-95"
                    title="Kosongkan Hitungan">
                    <i class="bi bi-trash text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- BANNER INFORMASI PEMBULATAN PECAHAN HAK NASABAH -->
        <div
            class="mb-5 p-3.5 rounded-2xl bg-amber-500/10 dark:bg-amber-500/15 border border-amber-500/25 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-start gap-2.5">
                <i class="bi bi-info-circle-fill text-amber-600 dark:text-amber-400 text-base shrink-0 mt-0.5"></i>
                <div>
                    <div class="font-extrabold text-zinc-900 dark:text-white">
                        Ketentuan Pembulatan Potongan Madrasah & Uang Pecahan Fisik:
                    </div>
                    <p class="text-zinc-600 dark:text-zinc-300 mt-0.5 text-[11px] font-medium leading-relaxed">
                        Sesuai ketersediaan uang fisik di peredaran (pecahan terkecil Rp 100, tidak ada uang kertas/koin
                        Rp 50 atau di bawahnya), <strong>potongan madrasah dibulatkan ke atas ke kelipatan Rp 100
                            terdekat</strong> (contoh: potongan Rp 25.880 menjadi Rp 25.900), sehingga hak bersih yang
                        diterima nasabah selalu pas dan siap dibagikan dalam pecahan uang fisik.
                    </p>
                </div>
            </div>
            <div
                class="shrink-0 font-extrabold text-amber-700 dark:text-amber-300 bg-amber-500/20 px-3 py-1.5 rounded-xl border border-amber-500/30 text-center">
                Pecahan Terkecil: Rp 100
            </div>
        </div>

        <!-- SUMMARY CARD LIVE VARIANCE CHECKER -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Box 1: Target Saldo yang Dihitung -->
            <div
                class="p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-900/80 border border-zinc-200/60 dark:border-zinc-800/60 flex flex-col justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400" x-text="targetNama">
                    Target Nominal yang Dihitung
                </span>
                <div class="text-xl font-black text-zinc-900 dark:text-white mt-1"
                    x-text="'Rp ' + targetSaldo.toLocaleString('id-ID')">
                    Rp {{ number_format($breakdownHakNasabah['nominal_bulat'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-1"
                    x-text="targetMode === 'hak_nasabah' ? 'Total kewajiban dibagikan ke nasabah' : 'Total saldo kas fisik tabungan madrasah'">
                </span>
            </div>

            <!-- Box 2: Total Kas Fisik Terhitung -->
            <div
                class="p-4 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/30 flex flex-col justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                    Total Pecahan Terhitung (Fisik)
                </span>
                <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1"
                    x-text="'Rp ' + grandTotal.toLocaleString('id-ID')">
                    Rp 0
                </div>
                <span class="text-[11px] font-semibold text-emerald-700/80 dark:text-emerald-400/80 mt-1">
                    Akumulasi jumlah lembar / keping pecahan
                </span>
            </div>

            <!-- Box 3: Status Audit Selisih -->
            <div class="p-4 rounded-2xl border flex flex-col justify-between transition-colors"
                :class="{
                    'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400': grandTotal > 0 &&
                        selisih === 0,
                    'bg-blue-500/10 border-blue-500/30 text-blue-600 dark:text-blue-400': selisih > 0,
                    'bg-rose-500/10 border-rose-500/30 text-rose-600 dark:text-rose-400': grandTotal > 0 && selisih < 0,
                    'bg-zinc-100 dark:bg-zinc-900/80 border-zinc-200/60 dark:border-zinc-800/60 text-zinc-500': grandTotal ===
                        0
                }">
                <span class="text-[10px] font-black uppercase tracking-wider">
                    Status Rekonsiliasi Kas
                </span>

                <div class="mt-1">
                    <template x-if="grandTotal === 0">
                        <div class="text-base font-black text-zinc-400">
                            Masukkan Jumlah Pecahan
                        </div>
                    </template>
                    <template x-if="grandTotal > 0 && selisih === 0">
                        <div
                            class="text-xl font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <i class="bi bi-check-circle-fill"></i> PAS / BALANCE (Rp 0)
                        </div>
                    </template>
                    <template x-if="selisih > 0">
                        <div class="text-xl font-black text-blue-600 dark:text-blue-400 flex items-center gap-1.5">
                            <i class="bi bi-plus-circle-fill"></i> LEBIH: +<span
                                x-text="formatRupiah(selisih)"></span>
                        </div>
                    </template>
                    <template x-if="grandTotal > 0 && selisih < 0">
                        <div class="text-xl font-black text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                            <i class="bi bi-exclamation-triangle-fill"></i> KURANG: -<span
                                x-text="formatRupiah(selisih)"></span>
                        </div>
                    </template>
                </div>

                <span class="text-[11px] font-semibold mt-1 opacity-90"
                    x-text="selisih === 0 && grandTotal > 0 ? 'Fisik uang pecahan pas dan cocok dengan target' : (selisih > 0 ? 'Terdapat kelebihan fisik kas yang dihitung' : (grandTotal > 0 ? 'Terdapat kekurangan fisik kas yang dihitung' : 'Silakan sesuaikan jumlah pecahan'))">
                </span>
            </div>
        </div>

        <!-- GRID DAFTAR PECAHAN UANG (100.000 s.d. 100) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($denominasiPecahan as $dp)
                @php
                    $rekomendasiLembar = $breakdownHakNasabah['counts'][$dp['key']] ?? 0;
                    $rekomendasiSubtotal = $rekomendasiLembar * $dp['nilai'];
                @endphp
                <div
                    class="p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200/60 dark:border-zinc-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span
                            class="px-2.5 py-1.5 rounded-xl font-black text-xs border {{ $dp['badge_class'] }} shrink-0">
                            {{ $dp['label'] }}
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-zinc-400">
                                    {{ $dp['tipe'] }}
                                </span>
                                <span class="text-[10px] font-bold text-primary dark:text-primary-dark">
                                    (Hak: {{ $rekomendasiLembar }} {{ $dp['tipe'] === 'Kertas' ? 'lbr' : 'koin' }})
                                </span>
                            </div>
                            <div class="text-xs font-black text-zinc-900 dark:text-white mt-0.5"
                                x-text="'Rp ' + getSubtotal('{{ $dp['key'] }}').toLocaleString('id-ID')">
                                Rp 0
                            </div>
                        </div>
                    </div>

                    <!-- Input Controls -->
                    <div class="flex items-center gap-1.5 self-end sm:self-center">
                        <button type="button" @click="addCount('{{ $dp['key'] }}', -1)"
                            class="w-7 h-7 rounded-lg bg-zinc-200/70 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 flex items-center justify-center font-bold text-sm transition-all active:scale-95">
                            -
                        </button>

                        <input type="number" min="0" step="1"
                            x-model.number="counts['{{ $dp['key'] }}']" placeholder="0"
                            class="m3-input-glass w-16 text-center text-xs font-black py-1.5 px-2 rounded-xl focus:ring-1 focus:ring-primary" />

                        <button type="button" @click="addCount('{{ $dp['key'] }}', 1)"
                            class="w-7 h-7 rounded-lg bg-zinc-200/70 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-700 flex items-center justify-center font-bold text-sm transition-all active:scale-95">
                            +
                        </button>

                        <!-- Quick Add Buttons -->
                        <button type="button" @click="addCount('{{ $dp['key'] }}', 10)"
                            title="Tambah 10 lembar/keping"
                            class="px-2 py-1 rounded-lg bg-primary/10 text-primary dark:text-primary-dark font-extrabold text-[10px] hover:bg-primary/20 transition-all">
                            +10
                        </button>
                        <button type="button" @click="addCount('{{ $dp['key'] }}', 50)"
                            title="Tambah 50 lembar/keping"
                            class="px-2 py-1 rounded-lg bg-primary/10 text-primary dark:text-primary-dark font-extrabold text-[10px] hover:bg-primary/20 transition-all">
                            +50
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</x-app-layout>
