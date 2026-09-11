@section('title', 'Detail Pinjaman - ' . $pinjaman->kode_pinjaman)

<x-app-layout>
    <div class="space-y-6">
        <!-- 1. Header Navigation & Document Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('keuangan.pinjaman.index') }}"
                    class="text-xs font-bold text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 inline-flex items-center gap-1.5 mb-1.5 transition-colors">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Daftar Pinjaman</span>
                </a>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                        Pinjaman {{ $pinjaman->kode_pinjaman }}
                    </h2>
                    @php
                        $statusBadge = match ($pinjaman->status) {
                            'pengajuan' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
                            'disetujui' => 'bg-blue-500/10 text-blue-600 border-blue-500/20',
                            'dicairkan' => 'bg-purple-500/10 text-purple-600 border-purple-500/20',
                            'lunas' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
                            'ditolak' => 'bg-rose-500/10 text-rose-600 border-rose-500/20',
                            default => 'bg-zinc-500/10 text-zinc-600 border-zinc-500/20',
                        };
                    @endphp
                    <span
                        class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border {{ $statusBadge }}">
                        {{ $pinjaman->status }}
                    </span>
                </div>
            </div>

            <!-- Print Actions -->
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('keuangan.pinjaman.cetak-perjanjian', $pinjaman->id) }}" target="_blank"
                    class="px-3.5 py-2 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-file-earmark-text-fill text-purple-600 dark:text-purple-400"></i>
                    <span>Akad Pinjaman</span>
                </a>
                <a href="{{ route('keuangan.pinjaman.cetak-tanda-terima-jaminan', $pinjaman->id) }}" target="_blank"
                    class="px-3.5 py-2 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-shield-check text-amber-600 dark:text-amber-400"></i>
                    <span>Tanda Terima Agunan</span>
                </a>
                <a href="{{ route('keuangan.pinjaman.cetak-kartu-angsuran', $pinjaman->id) }}" target="_blank"
                    class="px-3.5 py-2 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-card-checklist text-blue-600 dark:text-blue-400"></i>
                    <span>Kartu Angsuran</span>
                </a>
            </div>
        </div>

        <!-- 2. Workflow Action Banner (Conditional on status) -->
        @if ($pinjaman->status === 'pengajuan')
            <div
                class="p-4 md:p-5 rounded-3xl bg-amber-500/10 border border-amber-500/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold flex-shrink-0">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-zinc-900 dark:text-white text-sm">Menunggu Persetujuan Pengurus</h4>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Pengajuan pinjaman memerlukan verifikasi
                            berkas agunan dan persetujuan.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="button" onclick="confirmTolakPinjaman({{ $pinjaman->id }})"
                        class="px-4 py-2 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold transition-all">
                        Tolak Pengajuan
                    </button>
                    <button type="button" onclick="confirmApprovePinjaman({{ $pinjaman->id }})"
                        class="m3-btn-primary px-5 py-2 text-xs font-black shadow-md flex items-center gap-1.5">
                        <i class="bi bi-check-lg"></i>
                        <span>Setujui Pinjaman</span>
                    </button>
                </div>
            </div>
        @elseif($pinjaman->status === 'disetujui')
            <div
                class="p-4 md:p-5 rounded-3xl bg-blue-500/10 border border-blue-500/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold flex-shrink-0">
                        <i class="bi bi-check2-all"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-zinc-900 dark:text-white text-sm">Pinjaman Telah Disetujui</h4>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Siap dicairkan ke nasabah. Saldo kas
                            madrasah akan otomatis dipotong saat pencairan.</p>
                    </div>
                </div>
                <button type="button" onclick="openCairkanModal()"
                    class="m3-btn-primary px-6 py-2.5 text-xs font-black shadow-md flex items-center gap-1.5">
                    <i class="bi bi-cash-stack"></i>
                    <span>Cairkan Dana Sekarang</span>
                </button>
            </div>
        @endif

        <!-- 3. Metrics Summary -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4">
            <div class="m3-glass-card p-4 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Nominal Pinjaman</span>
                <span class="text-lg md:text-xl font-black text-zinc-900 dark:text-white font-mono">
                    Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}
                </span>
                <span class="text-[10px] text-zinc-400 block mt-0.5">Admin: Rp
                    {{ number_format($pinjaman->biaya_administrasi, 0, ',', '.') }}</span>
            </div>

            <div class="m3-glass-card p-4 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Cicilan Bulanan</span>
                <span class="text-lg md:text-xl font-black text-primary dark:text-primary-dark font-mono">
                    Rp {{ number_format($pinjaman->nominal_angsuran_total, 0, ',', '.') }}
                </span>
                <span class="text-[10px] text-zinc-400 block mt-0.5">Tenor: {{ $pinjaman->tenor_bulan }} Bulan</span>
            </div>

            <div class="m3-glass-card p-4 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Total Terbayar</span>
                <span class="text-lg md:text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                    Rp {{ number_format($pinjaman->total_terbayar, 0, ',', '.') }}
                </span>
                <span class="text-[10px] text-zinc-400 block mt-0.5">
                    {{ $pinjaman->angsurans->where('status', 'lunas')->count() }}/{{ $pinjaman->tenor_bulan }}
                    Angsuran
                </span>
            </div>

            <div class="m3-glass-card p-4 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Sisa Hutang
                    Pokok</span>
                <span
                    class="text-lg md:text-xl font-black font-mono {{ $pinjaman->sisa_pinjaman > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                </span>
                <span class="text-[10px] text-zinc-400 block mt-0.5">
                    {{ $pinjaman->status === 'lunas' ? 'Lunas Sepenuhnya' : 'Sisa Tagihan Berjalan' }}
                </span>
            </div>
        </div>

        <!-- 4. Two Columns: Borrower Info & Collateral Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Borrower Info -->
            <div
                class="m3-glass-card p-6 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    <x-avatar :src="$pinjaman->nasabah->foto_nasabah
                        ? asset('storage/' . $pinjaman->nasabah->foto_nasabah)
                        : null" :name="$pinjaman->nasabah->nama_lengkap" size="md" />
                    <div>
                        <span class="text-[10px] font-black uppercase text-primary tracking-wider block">Data
                            Peminjam</span>
                        <h3 class="font-bold text-zinc-900 dark:text-white text-sm">
                            {{ $pinjaman->nasabah->nama_lengkap }}
                        </h3>
                        <p class="text-xs text-zinc-400 font-mono">{{ $pinjaman->nasabah->kode_nasabah }}</p>
                    </div>
                </div>

                <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
                    <div class="py-2 flex justify-between">
                        <span class="text-zinc-400">No. WhatsApp</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pinjaman->nasabah->no_hp) }}"
                            target="_blank" class="font-bold text-emerald-600">
                            {{ $pinjaman->nasabah->no_hp }}
                        </a>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-zinc-400">Tipe Nasabah</span>
                        <span
                            class="font-bold text-zinc-800 dark:text-zinc-200 uppercase">{{ str_replace('_', ' ', $pinjaman->nasabah->tipe_nasabah) }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-zinc-400">Pos Kas Sumber</span>
                        <span
                            class="font-bold text-zinc-800 dark:text-zinc-200">{{ $pinjaman->akunKeuangan->nama_akun ?? '-' }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-zinc-400">Keperluan</span>
                        <span
                            class="font-bold text-zinc-800 dark:text-zinc-200 text-right max-w-[150px] truncate">{{ $pinjaman->keperluan_pinjaman }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-zinc-400">Disetujui Oleh</span>
                        <span
                            class="font-bold text-zinc-800 dark:text-zinc-200">{{ $pinjaman->disetujuiOleh->name ?? '-' }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-zinc-400">Dicairkan Oleh</span>
                        <span
                            class="font-bold text-zinc-800 dark:text-zinc-200">{{ $pinjaman->dicairkanOleh->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Agunan / Collaterals Cards -->
            <div class="lg:col-span-2 space-y-4">
                <div
                    class="m3-glass-card p-6 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
                    <div
                        class="flex items-center justify-between pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                        <h3
                            class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="bi bi-shield-lock-fill text-amber-500"></i>
                            <span>Agunan & Jaminan Terdaftar ({{ $pinjaman->jaminans->count() }})</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($pinjaman->jaminans as $jmn)
                            <div
                                class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 space-y-3 relative">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span
                                            class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">
                                            {{ str_replace('_', ' ', $jmn->jenis_jaminan) }}
                                        </span>
                                        <h4 class="font-black text-zinc-900 dark:text-white text-sm">
                                            {{ $jmn->nama_barang_jaminan }}
                                        </h4>
                                    </div>
                                    @php
                                        $jmnBadge = match ($jmn->status_jaminan) {
                                            'ditahan_madrasah' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
                                            'dikembalikan'
                                                => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
                                            'disita_dilelang' => 'bg-rose-500/10 text-rose-600 border-rose-500/20',
                                            default => 'bg-zinc-500/10 text-zinc-600 border-zinc-500/20',
                                        };
                                    @endphp
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $jmnBadge }}">
                                        {{ str_replace('_', ' ', $jmn->status_jaminan) }}
                                    </span>
                                </div>

                                <div class="text-xs space-y-1 text-zinc-600 dark:text-zinc-400">
                                    <p><span class="font-semibold text-zinc-700 dark:text-zinc-300">No. Dok:</span>
                                        <span class="font-mono">{{ $jmn->nomor_dokumen_jaminan ?? '-' }}</span></p>
                                    <p><span class="font-semibold text-zinc-700 dark:text-zinc-300">a.n Dokumen:</span>
                                        {{ $jmn->atas_nama_dokumen ?? '-' }}</p>
                                    <p><span class="font-semibold text-zinc-700 dark:text-zinc-300">Taksiran:</span>
                                        <span class="font-mono font-bold text-zinc-900 dark:text-white">Rp
                                            {{ number_format($jmn->taksiran_nilai, 0, ',', '.') }}</span></p>
                                    <p><span class="font-semibold text-zinc-700 dark:text-zinc-300">Lokasi:</span>
                                        {{ $jmn->lokasi_penyimpanan ?? 'Brankas' }}</p>
                                </div>

                                <!-- Photo Links -->
                                <div
                                    class="flex items-center gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                                    @if ($jmn->foto_dokumen)
                                        <a href="{{ asset('storage/' . $jmn->foto_dokumen) }}" target="_blank"
                                            class="px-2 py-1 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-[11px] font-bold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 flex items-center gap-1">
                                            <i class="bi bi-file-earmark-image"></i> Dokumen
                                        </a>
                                    @endif
                                    @if ($jmn->foto_barang)
                                        <a href="{{ asset('storage/' . $jmn->foto_barang) }}" target="_blank"
                                            class="px-2 py-1 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-[11px] font-bold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 flex items-center gap-1">
                                            <i class="bi bi-camera"></i> Fisik
                                        </a>
                                    @endif

                                    @if ($jmn->status_jaminan === 'ditahan_madrasah')
                                        <button type="button"
                                            onclick="confirmKembalikanJaminan({{ $jmn->id }}, '{{ $jmn->nama_barang_jaminan }}')"
                                            class="ml-auto px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold transition-all">
                                            Kembalikan Agunan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 py-6 text-center text-zinc-400">
                                Pinjaman ini tidak menyertakan agunan fisik.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Table of Installments (Jadwal & Pembayaran Angsuran) -->
        <div
            class="m3-glass-card rounded-3xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="p-4 md:p-5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                        Jadwal & Riwayat Pembayaran Angsuran Bulanan
                    </h3>
                    <p class="text-[11px] text-zinc-400">Pencatatan cicilan bulanan dan penerimaan kas</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30 text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="py-3 px-4 text-center">Bulan Ke</th>
                            <th class="py-3 px-4">Jatuh Tempo</th>
                            <th class="py-3 px-4 text-right">Pokok</th>
                            <th class="py-3 px-4 text-right">Infaq Margin</th>
                            <th class="py-3 px-4 text-right">Denda</th>
                            <th class="py-3 px-4 text-right">Total Angsuran</th>
                            <th class="py-3 px-4">Tanggal Bayar</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
                        @forelse($pinjaman->angsurans as $angsuran)
                            <tr
                                class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 {{ $angsuran->status === 'lunas' ? 'bg-emerald-500/5' : '' }}">
                                <td class="py-3.5 px-4 text-center font-bold font-mono text-zinc-900 dark:text-white">
                                    #{{ $angsuran->angsuran_ke }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-zinc-800 dark:text-zinc-200">
                                    {{ $angsuran->tanggal_jatuh_tempo->translatedFormat('d F Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-zinc-600 dark:text-zinc-300">
                                    Rp {{ number_format($angsuran->nominal_pokok, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($angsuran->nominal_infaq_margin, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-rose-600 dark:text-rose-400">
                                    Rp {{ number_format($angsuran->nominal_denda, 0, ',', '.') }}
                                </td>
                                <td
                                    class="py-3.5 px-4 text-right font-black font-mono text-sm text-zinc-900 dark:text-white">
                                    Rp {{ number_format($angsuran->total_bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @if ($angsuran->tanggal_bayar)
                                        <span class="font-bold text-zinc-900 dark:text-white block">
                                            {{ $angsuran->tanggal_bayar->translatedFormat('d/m/Y') }}
                                        </span>
                                        <span class="text-[10px] text-zinc-400">Oleh:
                                            {{ $angsuran->diterimaOleh->name ?? 'Kasir' }}</span>
                                    @else
                                        <span class="text-zinc-400 italic">Belum Dibayar</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($angsuran->status === 'lunas')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            Lunas
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($angsuran->status !== 'lunas' && $pinjaman->status === 'dicairkan')
                                        <button type="button"
                                            onclick="openBayarAngsuranModal({{ $angsuran->id }}, {{ $angsuran->angsuran_ke }}, {{ $angsuran->total_bayar }})"
                                            class="m3-btn-primary px-3 py-1.5 text-xs font-black shadow-sm flex items-center justify-center gap-1 mx-auto">
                                            <i class="bi bi-wallet2"></i>
                                            <span>Bayar</span>
                                        </button>
                                    @elseif($angsuran->status === 'lunas')
                                        <span
                                            class="text-[11px] font-bold text-emerald-600 flex items-center justify-center gap-1">
                                            <i class="bi bi-check-circle-fill"></i> Selesai
                                        </span>
                                    @else
                                        <span class="text-zinc-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-zinc-400">
                                    Jadwal angsuran akan dibuat secara otomatis saat dana pinjaman dicairkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Cairkan Dana Pinjaman -->
    <div id="cairkanModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div
            class="relative w-full max-w-lg bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg font-black border border-purple-500/20">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white">
                            Pencairan Dana Pinjaman
                        </h3>
                        <p class="text-[11px] text-zinc-400">Nominal Bersih: Rp
                            {{ number_format($pinjaman->nominal_pencairan, 0, ',', '.') }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeCairkanModal()"
                    class="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form id="formCairkan" onsubmit="submitCairkanPinjaman(event)" class="space-y-4">
                @csrf
                <div>
                    <label
                        class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                        Tanggal Pencairan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_pencairan" required value="{{ date('Y-m-d') }}"
                        class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                        Pos Akun Sumber Pengeluaran <span class="text-rose-500">*</span>
                    </label>
                    <select name="akun_keuangan_id" required
                        class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                        @foreach ($akuns as $a)
                            <option value="{{ $a->id }}"
                                {{ $pinjaman->akun_keuangan_id == $a->id ? 'selected' : '' }}>
                                [{{ $a->kode_akun }}] {{ $a->nama_akun }} (Saldo: Rp
                                {{ number_format($a->saldo_berjalan, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                        Rekening Bank (Jika Ditransfer)
                    </label>
                    <select name="bank_id"
                        class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                        <option value="">-- Pencairan Tunai (Kas Fisik) --</option>
                        @foreach ($banks as $b)
                            <option value="{{ $b->id }}" {{ $pinjaman->bank_id == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bank }} - {{ $b->nomor_rekening }} (Saldo: Rp
                                {{ number_format($b->saldo, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" onclick="closeCairkanModal()"
                        class="px-4 py-2 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitCairkan"
                        class="m3-btn-primary px-5 py-2 text-xs font-black shadow-md flex items-center gap-1.5">
                        <i class="bi bi-check2-circle"></i>
                        <span>Konfirmasi Pencairan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pembayaran Angsuran -->
    <div id="bayarAngsuranModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div
            class="relative w-full max-w-lg bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6 overflow-hidden">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg font-black border border-emerald-500/20">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white" id="bayarModalTitle">
                            Bayar Angsuran
                        </h3>
                        <p class="text-[11px] text-zinc-400">Penerimaan cicilan bulanan pinjaman nasabah</p>
                    </div>
                </div>
                <button type="button" onclick="closeBayarAngsuranModal()"
                    class="w-8 h-8 rounded-full bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 flex items-center justify-center transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <form id="formBayarAngsuran" onsubmit="submitBayarAngsuran(event)" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                <input type="hidden" name="angsuran_id" id="bayar_angsuran_id">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Tanggal Bayar <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_bayar" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Metode Bayar <span class="text-rose-500">*</span>
                        </label>
                        <select name="metode_pembayaran" id="bayar_metode" onchange="toggleBayarBank(this.value)"
                            required
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                            <option value="tunai">Tunai / Kasir</option>
                            <option value="transfer_bank">Transfer Bank</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                        Pos Kas Penerimaan <span class="text-rose-500">*</span>
                    </label>
                    <select name="akun_keuangan_id" required
                        class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                        @foreach ($akuns as $a)
                            <option value="{{ $a->id }}"
                                {{ $pinjaman->akun_keuangan_id == $a->id ? 'selected' : '' }}>
                                [{{ $a->kode_akun }}] {{ $a->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="containerBayarBank" class="hidden">
                    <label
                        class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                        Rekening Bank Penerima
                    </label>
                    <select name="bank_id" id="bayar_bank_id"
                        class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                        <option value="">-- Pilih Rekening Bank --</option>
                        @foreach ($banks as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->nama_bank }} - {{ $b->nomor_rekening }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                        Nominal Denda Keterlambatan (Opsional)
                    </label>
                    <input type="number" name="nominal_denda" min="0" step="1000" value="0"
                        class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none font-mono">
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                        Upload Bukti Transfer / Resi (Opsional)
                    </label>
                    <input type="file" name="bukti_bayar" accept="image/*,application/pdf"
                        class="w-full text-xs text-zinc-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500/10 file:text-emerald-600">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" onclick="closeBayarAngsuranModal()"
                        class="px-4 py-2 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitBayar"
                        class="m3-btn-primary px-5 py-2 text-xs font-black shadow-md flex items-center gap-1.5">
                        <i class="bi bi-check2-circle"></i>
                        <span>Konfirmasi Pembayaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmApprovePinjaman(id) {
                Swal.fire({
                    title: 'Setujui Pengajuan Pinjaman?',
                    text: 'Pinjaman akan disetujui dan siap untuk dicairkan.',
                    icon: 'question',
                    input: 'text',
                    inputPlaceholder: 'Catatan persetujuan (opsional)...',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('keuangan/pinjaman') }}/${id}/approve`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    catatan: result.value
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            });
                    }
                });
            }

            function confirmTolakPinjaman(id) {
                Swal.fire({
                    title: 'Tolak Pengajuan Pinjaman?',
                    text: 'Harap masukkan alasan penolakan pinjaman ini.',
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: 'Alasan penolakan...',
                    inputValidator: (value) => {
                        if (!value) return 'Alasan penolakan wajib diisi!';
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Tolak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('keuangan/pinjaman') }}/${id}/tolak`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    alasan: result.value
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Ditolak!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            });
                    }
                });
            }

            function openCairkanModal() {
                document.getElementById('cairkanModal').classList.remove('hidden');
            }

            function closeCairkanModal() {
                document.getElementById('cairkanModal').classList.add('hidden');
            }

            function submitCairkanPinjaman(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSubmitCairkan');
                btn.disabled = true;
                btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin text-sm"></i> Memproses...`;

                const formData = new FormData(e.target);

                fetch(`{{ route('keuangan.pinjaman.cairkan', $pinjaman->id) }}`, {
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
                        closeCairkanModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Pencairan Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mencairkan',
                            text: err.message || 'Terjadi kesalahan saat memproses pencairan.'
                        });
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = `<i class="bi bi-check2-circle"></i> <span>Konfirmasi Pencairan</span>`;
                    });
            }

            function openBayarAngsuranModal(id, ke, total) {
                document.getElementById('bayar_angsuran_id').value = id;
                document.getElementById('bayarModalTitle').innerText =
                    `Bayar Angsuran #${ke} (Rp ${total.toLocaleString('id-ID')})`;
                document.getElementById('bayarAngsuranModal').classList.remove('hidden');
            }

            function closeBayarAngsuranModal() {
                document.getElementById('bayarAngsuranModal').classList.add('hidden');
            }

            function toggleBayarBank(val) {
                const cBank = document.getElementById('containerBayarBank');
                const bankSelect = document.getElementById('bayar_bank_id');
                if (val === 'transfer_bank') {
                    cBank.classList.remove('hidden');
                    bankSelect.required = true;
                } else {
                    cBank.classList.add('hidden');
                    bankSelect.required = false;
                }
            }

            function submitBayarAngsuran(e) {
                e.preventDefault();
                const btn = document.getElementById('btnSubmitBayar');
                btn.disabled = true;
                btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin text-sm"></i> Menyimpan...`;

                const formData = new FormData(e.target);

                fetch(`{{ route('keuangan.pinjaman.bayar-angsuran', $pinjaman->id) }}`, {
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
                        closeBayarAngsuranModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Diterima!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Membayar',
                            text: err.message || 'Terjadi kesalahan saat memproses pembayaran angsuran.'
                        });
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = `<i class="bi bi-check2-circle"></i> <span>Konfirmasi Pembayaran</span>`;
                    });
            }

            function confirmKembalikanJaminan(jaminanId, namaBarang) {
                Swal.fire({
                    title: 'Kembalikan Agunan ke Nasabah?',
                    text: `Apakah agunan "${namaBarang}" sudah diserahkan kembali kepada nasabah?`,
                    icon: 'question',
                    input: 'text',
                    inputPlaceholder: 'Catatan pengembalian (opsional)...',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kembalikan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ route('keuangan.pinjaman.kembalikan-jaminan', $pinjaman->id) }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    jaminan_id: jaminanId,
                                    catatan: result.value
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Agunan Dikembalikan!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
