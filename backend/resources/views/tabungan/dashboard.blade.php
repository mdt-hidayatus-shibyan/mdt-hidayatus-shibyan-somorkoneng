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
            <a href="{{ route('tabungan.setor.index') }}"
                class="m3-btn-primary px-3.5 py-2 text-xs font-black rounded-xl flex items-center gap-1.5">
                <i class="bi bi-arrow-down-circle-fill text-sm"></i>
                <span>Setor Tunai</span>
            </a>
            <button type="button" onclick="openModal('modalTarikCepat')"
                class="px-3.5 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-xs font-black rounded-xl transition-colors flex items-center gap-1.5 cursor-pointer">
                <i class="bi bi-arrow-up-circle-fill text-sm"></i>
                <span>Tarik Tunai</span>
            </button>
            <a href="{{ route('tabungan.rekening.create') }}"
                class="px-3.5 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-xs font-black rounded-xl transition-colors flex items-center gap-1.5">
                <i class="bi bi-plus-circle text-sm"></i>
                <span>Buka Rekening</span>
            </a>
        </div>
    </div>

    <!-- Alert / Banner Periode Aktif -->
    @if ($periodeAktif)
        <div
            class="mb-6 p-4 m3-glass-card rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-l-4 border-l-primary bg-primary/5">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center shrink-0 text-lg">
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
                class="px-3 py-1.5 bg-primary/10 hover:bg-primary/20 text-primary dark:text-primary-dark text-xs font-black rounded-lg transition-colors shrink-0 text-center">
                Lihat Simulasi Pembagian
            </a>
        </div>
    @endif

    <!-- GRID STATISTIK REKAP KAS TABUNGAN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Total Kas Tabungan -->
        <div class="m3-glass-card p-5 rounded-2xl relative overflow-hidden group">
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
        <div class="m3-glass-card p-5 rounded-2xl relative overflow-hidden group">
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
        <div class="m3-glass-card p-5 rounded-2xl relative overflow-hidden group">
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
        <div class="m3-glass-card p-5 rounded-2xl relative overflow-hidden group">
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
        <div class="lg:col-span-2 m3-glass-card rounded-2xl overflow-hidden">
            <div
                class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                <span
                    class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-clock-history text-primary text-sm"></i>
                    Mutasi Transaksi Terakhir
                </span>
                <a href="{{ route('tabungan.rekening.index') }}"
                    class="text-[11px] font-bold text-primary hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500">
                            <th class="py-2.5 px-4">Kode & Tanggal</th>
                            <th class="py-2.5 px-3">Nasabah</th>
                            <th class="py-2.5 px-3">Jenis</th>
                            <th class="py-2.5 px-3 text-right">Nominal Kotor</th>
                            <th class="py-2.5 px-3 text-right">Potongan</th>
                            <th class="py-2.5 px-4 text-right">Nominal Bersih</th>
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
                                    <span class="font-bold text-zinc-900 dark:text-white block truncate max-w-[150px]">
                                        {{ $trx->tabungan->nama_nasabah ?? '-' }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400 font-mono">
                                        {{ $trx->tabungan->nomor_rekening ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    @if ($trx->jenis_transaksi == 'Setor')
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            SETOR
                                        </span>
                                    @elseif ($trx->jenis_transaksi == 'Tarik')
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
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
                                <td colspan="6" class="py-8 text-center text-zinc-400">
                                    Belum ada mutasi transaksi tabungan yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan (1 Span): Saldo Terbesar & Akses Cepat -->
        <div class="space-y-6">
            <!-- Top Rekening -->
            <div class="m3-glass-card rounded-2xl overflow-hidden">
                <div
                    class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
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
                                    class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 flex items-center justify-center font-bold text-xs shrink-0">
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
                        <li class="py-6 text-center text-xs text-zinc-400">
                            Belum ada data rekening.
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- Card Informasi Tarif Potongan Aktif -->
            <div class="m3-glass-card p-4 rounded-2xl">
                <h5
                    class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="bi bi-percent text-rose-500"></i>
                    Tarif Potongan Aktif (Musyawarah)
                </h5>
                <div class="space-y-2 text-xs">
                    <div
                        class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800/50">
                        <span class="text-zinc-500 dark:text-zinc-400">Tabungan Murid:</span>
                        <span class="font-black text-rose-600 dark:text-rose-400 font-mono">10.00%</span>
                    </div>
                    <div
                        class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800/50">
                        <span class="text-zinc-500 dark:text-zinc-400">Tabungan Ustadz:</span>
                        <span class="font-black text-amber-600 dark:text-amber-400 font-mono">2.50%</span>
                    </div>
                    <div
                        class="flex justify-between items-center py-1 border-b border-zinc-100 dark:border-zinc-800/50">
                        <span class="text-zinc-500 dark:text-zinc-400">Kas Kelas:</span>
                        <span class="font-black text-emerald-600 dark:text-emerald-400 font-mono">0.00% (Bebas
                            Potongan)</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-zinc-500 dark:text-zinc-400">Umum / Reguler:</span>
                        <span class="font-black text-rose-600 dark:text-rose-400 font-mono">10.00%</span>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-t border-zinc-100 dark:border-zinc-800/50 text-right">
                    <a href="{{ route('tabungan.pengaturan.index') }}"
                        class="text-[10px] font-bold text-primary hover:underline">
                        Ubah Pengaturan Potongan &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL SETOR CEPAT -->
    <div id="modalSetorCepat"
        class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="m3-glass-card max-w-md w-full p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3
                    class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-arrow-down-circle-fill text-emerald-500"></i> Setor Tunai Tabungan
                </h3>
                <button type="button" onclick="closeModal('modalSetorCepat')"
                    class="text-zinc-400 hover:text-zinc-600 text-lg">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form action="{{ route('tabungan.setor') }}" method="POST">
                @csrf
                <div class="space-y-3.5 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Cari
                            Rekening Nasabah:</label>
                        <select name="tabungan_id" class="select2-rekening m3-input-glass w-full" required
                            style="width: 100%">
                            <option value="">-- Pilih Rekening Nasabah --</option>
                            @foreach (\App\Models\Tabungan\Tabungan::where('status', 'Aktif')->orderBy('nomor_rekening')->get() as $tab)
                                <option value="{{ $tab->id }}">
                                    {{ $tab->nomor_rekening }} - {{ $tab->nama_nasabah }} (Saldo: Rp
                                    {{ number_format($tab->saldo, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Nominal
                            Setoran (Rp):</label>
                        <input type="number" name="nominal" min="1000" step="500" required
                            placeholder="Contoh: 50000" class="m3-input-glass w-full font-mono font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Tanggal
                            Transaksi:</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="m3-input-glass w-full text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Catatan
                            / Keterangan:</label>
                        <input type="text" name="keterangan" placeholder="Setoran tabungan..."
                            class="m3-input-glass w-full text-xs">
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modalSetorCepat')"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-500 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="m3-btn-primary px-5 py-2 rounded-xl text-xs font-black">Simpan
                        Setoran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TARIK CEPAT -->
    <div id="modalTarikCepat"
        class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div
            class="m3-glass-card max-w-md w-full p-5 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3
                    class="font-black text-sm text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-arrow-up-circle-fill text-amber-500"></i> Tarik Tunai Tabungan
                </h3>
                <button type="button" onclick="closeModal('modalTarikCepat')"
                    class="text-zinc-400 hover:text-zinc-600 text-lg">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form action="{{ route('tabungan.tarik') }}" method="POST">
                @csrf
                <div class="space-y-3.5 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Pilih
                            Rekening Nasabah:</label>
                        <select name="tabungan_id" class="select2-rekening m3-input-glass w-full" required
                            style="width: 100%">
                            <option value="">-- Pilih Rekening Nasabah --</option>
                            @foreach (\App\Models\Tabungan\Tabungan::where('status', 'Aktif')->where('saldo', '>', 0)->orderBy('nomor_rekening')->get() as $tab)
                                <option value="{{ $tab->id }}">
                                    {{ $tab->nomor_rekening }} - {{ $tab->nama_nasabah }} (Saldo: Rp
                                    {{ number_format($tab->saldo, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Nominal
                            Tarik Kotor (Rp):</label>
                        <input type="number" name="nominal" min="1000" step="500" required
                            placeholder="Contoh: 100000" class="m3-input-glass w-full font-mono font-bold text-sm">
                        <p class="text-[10px] text-zinc-400 mt-1">
                            *Uang yang diserahkan akan otomatis dipotong infaq/adm sesuai tarif kategori nasabah.
                        </p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Tanggal
                            Penarikan:</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="m3-input-glass w-full text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Alasan /
                            Keterangan:</label>
                        <input type="text" name="keterangan" placeholder="Penarikan tunai..."
                            class="m3-input-glass w-full text-xs">
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" onclick="closeModal('modalTarikCepat')"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-500 hover:bg-zinc-100">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-black">Cairkan
                        Uang</button>
                </div>
            </form>
        </div>
    </div>

    @push('script')
        <script>
            function openModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.remove('hidden');
            }

            function closeModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            }
        </script>
    @endpush

</x-app-layout>
