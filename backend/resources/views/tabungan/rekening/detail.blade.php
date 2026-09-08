@section('title', 'Detail Buku Tabungan - ' . $tabungan->nomor_rekening)
<x-app-layout>

    <!-- Header & Action Buttons -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Detail Rekening
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                No. Rekening: <span class="font-mono font-bold text-primary">{{ $tabungan->nomor_rekening }}</span>
                ({{ $tabungan->jenis_nasabah }})
            </p>
        </div>

        <div class="flex items-center gap-2">
            <x-button :href="route('tabungan.rekening.index')" variant="secondary" size="sm" icon="bi-arrow-left">
                Kembali
            </x-button>
            <x-button :href="route('tabungan.rekening.cetak', $tabungan->id)" target="_blank" variant="outline" size="sm" icon="bi-printer">
                Cetak Mutasi
            </x-button>
        </div>
    </div>


    <!-- GRID UTAMA INFO REKENING & SALDO -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- KARTU UTAMA IDENTITAS REKENING -->
        <div class="lg:col-span-2 m3-glass-card rounded-2xl p-6 relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <x-avatar :name="$tabungan->nama_nasabah" size="lg" />
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            @if ($tabungan->jenis_nasabah == 'Murid')
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                    Nasabah Murid
                                </span>
                            @elseif($tabungan->jenis_nasabah == 'Ustadz')
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                    Nasabah Ustadz
                                </span>
                            @elseif($tabungan->jenis_nasabah == 'Kas Ruangan')
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20">
                                    Kas Kelas
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                    Nasabah Umum
                                </span>
                            @endif

                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $tabungan->status == 'Aktif' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-zinc-500/10 text-zinc-600 border border-zinc-500/20' }}">
                                {{ $tabungan->status }}
                            </span>
                        </div>

                        <h2 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                            {{ $tabungan->nama_nasabah }}
                        </h2>
                        <p class="text-xs text-zinc-500 font-mono mt-0.5">
                            No. Rek: <span
                                class="font-bold text-zinc-700 dark:text-zinc-300">{{ $tabungan->nomor_rekening }}</span>
                            &bull; Nama Akun: <span
                                class="font-bold text-zinc-700 dark:text-zinc-300">{{ $tabungan->nama_rekening }}</span>
                        </p>
                    </div>
                </div>

                <!-- TOMBOL-TOMBOL AKSI TRANSAKSI -->
                <div class="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
                    <button type="button" onclick="openModalSetor()"
                        class="h-10 inline-flex items-center justify-center px-4 rounded-xl md:rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs active:scale-95 transition-all cursor-pointer gap-1.5">
                        <i class="bi bi-arrow-down-left-circle-fill"></i>
                        <span>Setor Tunai</span>
                    </button>
                    <button type="button" onclick="openModalTarik()"
                        class="h-10 inline-flex items-center justify-center px-4 rounded-xl md:rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs active:scale-95 transition-all cursor-pointer gap-1.5">
                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                        <span>Tarik Tunai</span>
                    </button>
                    <a href="{{ route('tabungan.rekening.ganti-buku', $tabungan->id) }}"
                        class="action-modal h-10 inline-flex items-center justify-center px-4 rounded-xl md:rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs active:scale-95 transition-all cursor-pointer gap-1.5"
                        title="Ganti Buku Tabungan Fisik">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Ganti Buku</span>
                    </a>
                </div>
            </div>

            <!-- DETAIL INFO GRID -->
            <div
                class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-zinc-200/80 dark:border-zinc-800 text-xs">
                <div>
                    <span class="text-zinc-400 font-bold block text-[10px] uppercase">Identitas Tambahan</span>
                    <span class="font-bold text-zinc-800 dark:text-zinc-200 mt-0.5 block">
                        @if ($tabungan->jenis_nasabah == 'Murid')
                            NISM: {{ $tabungan->murid?->nism ?? '-' }}
                        @elseif ($tabungan->jenis_nasabah == 'Ustadz')
                            NIGM: {{ $tabungan->ustadz?->nigm ?? '-' }}
                        @elseif ($tabungan->jenis_nasabah == 'Kas Ruangan')
                            Ruangan: {{ $tabungan->ruangan?->nama_ruangan ?? '-' }}
                        @else
                            Kontak: {{ $tabungan->kontak_umum ?? '-' }}
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-zinc-400 font-bold block text-[10px] uppercase">Periode Tabungan</span>
                    <span class="font-bold text-zinc-800 dark:text-zinc-200 mt-0.5 block">
                        {{ $tabungan->periodeTabungan?->nama_periode ?? 'Reguler / Fleksibel' }}
                    </span>
                </div>
                <div>
                    <span class="text-zinc-400 font-bold block text-[10px] uppercase">Total Setor</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 block">
                        Rp {{ number_format($tabungan->total_setor, 0, ',', '.') }}
                    </span>
                </div>
                <div>
                    <span class="text-zinc-400 font-bold block text-[10px] uppercase">Total Tarik</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400 mt-0.5 block">
                        Rp {{ number_format($tabungan->total_tarik, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- KARTU SALDO & ESTIMASI POTONGAN -->
        <div
            class="m3-glass-card rounded-2xl p-6 flex flex-col justify-between relative bg-gradient-to-br from-emerald-500/5 via-transparent to-primary/5">
            <div>
                <span class="text-zinc-400 font-bold text-[10px] uppercase tracking-wider block">Saldo Tabungan Saat
                    Ini</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                    Rp {{ number_format($tabungan->saldo, 0, ',', '.') }}
                </div>
            </div>

            <div
                class="p-3.5 rounded-xl bg-zinc-100/70 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800 mt-4 text-xs">
                <div class="flex justify-between items-center text-zinc-500 dark:text-zinc-400 text-[11px] mb-1">
                    <span>Simulasi Pembagian Akhir:</span>
                    <span
                        class="font-black text-zinc-800 dark:text-zinc-200">{{ $kalkulasiPenarikan['persentase'] }}%</span>
                </div>
                <div class="flex justify-between items-center text-zinc-500 dark:text-zinc-400 text-[11px] mb-1">
                    <span>Estimasi Potongan:</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400">Rp
                        {{ number_format($kalkulasiPenarikan['potongan'], 0, ',', '.') }}</span>
                </div>
                <div
                    class="flex justify-between items-center pt-2 border-t border-zinc-200/80 dark:border-zinc-800 font-black">
                    <span class="text-zinc-700 dark:text-zinc-300">Estimasi Bersih:</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-black">Rp
                        {{ number_format($kalkulasiPenarikan['nominal_bersih'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIWAYAT PENGGANTIAN BUKU FISIK (JIKA ADA) -->
    @if ($tabungan->riwayatBukus && $tabungan->riwayatBukus->isNotEmpty())
        <div class="m3-glass-card rounded-2xl overflow-hidden shadow-2xs mb-6 border border-indigo-500/20">
            <div
                class="p-4 bg-indigo-50/60 dark:bg-indigo-950/40 border-b border-indigo-100 dark:border-indigo-900/40 flex justify-between items-center">
                <span
                    class="font-black text-xs text-indigo-900 dark:text-indigo-300 uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-journal-bookmark-fill text-indigo-600 dark:text-indigo-400 text-sm"></i>
                    Riwayat Penggantian Buku Fisik ({{ $tabungan->riwayatBukus->count() }} Kali Penggantian)
                </span>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500 bg-zinc-50/40 dark:bg-zinc-900/40">
                            <th class="py-2.5 px-4 w-12 text-center">No</th>
                            <th class="py-2.5 px-3">Tanggal Ganti</th>
                            <th class="py-2.5 px-3">Barcode Lama</th>
                            <th class="py-2.5 px-3">Barcode Baru</th>
                            <th class="py-2.5 px-3">Alasan</th>
                            <th class="py-2.5 px-3 text-right">Saldo Saat Pengalihan</th>
                            <th class="py-2.5 px-3">Petugas / Catatan</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium text-zinc-700 dark:text-zinc-300">
                        @foreach ($tabungan->riwayatBukus as $rb)
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-indigo-950/20 transition-colors">
                                <td class="py-2.5 px-4 text-center font-bold text-zinc-400">{{ $loop->iteration }}</td>
                                <td class="py-2.5 px-3 font-bold text-zinc-900 dark:text-white">
                                    {{ $rb->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-2.5 px-3 font-mono font-bold text-rose-600 dark:text-rose-400">
                                    <span
                                        class="line-through decoration-rose-500">{{ $rb->nomor_rekening_lama }}</span>
                                </td>
                                <td class="py-2.5 px-3 font-mono font-black text-emerald-600 dark:text-emerald-400">
                                    {{ $rb->nomor_rekening_baru }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span
                                        class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                        {{ $rb->alasan }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-black text-zinc-900 dark:text-white">
                                    Rp {{ number_format($rb->saldo_terakhir, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-[11px] text-zinc-500">
                                    <div class="font-bold text-zinc-800 dark:text-zinc-200">{{ $rb->catatan ?? '-' }}
                                    </div>
                                    <div class="text-[10px] text-zinc-400">Petugas:
                                        {{ $rb->petugas?->name ?? 'Admin' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TABEL MUTASI BUKU TABUNGAN -->
    @if ($transaksis->isNotEmpty())
        <div id="data-grid-container" class="m3-glass-card rounded-2xl overflow-hidden shadow-2xs">
            <div
                class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                <span
                    class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-clock-history text-primary text-sm"></i>
                    Buku Mutasi Transaksi ({{ $transaksis->total() }} Catatan)
                </span>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-3">Tanggal & Kode</th>
                            <th class="py-3 px-3">Jenis Mutasi</th>
                            <th class="py-3 px-3 text-right">Nominal Kotor</th>
                            <th class="py-3 px-3 text-right">Potongan</th>
                            <th class="py-3 px-3 text-right">Nominal Bersih</th>
                            <th class="py-3 px-3 text-right">Saldo Buku</th>
                            <th class="py-3 px-3">Petugas / Ket</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium text-zinc-700 dark:text-zinc-300">
                        @foreach ($transaksis as $trx)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3 px-4 text-center font-bold text-zinc-400">
                                    {{ ($transaksis->currentPage() - 1) * $transaksis->perPage() + $loop->iteration }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-bold text-zinc-900 dark:text-white block">
                                        {{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}
                                    </span>
                                    <span class="text-[10px] font-mono text-zinc-400">
                                        {{ $trx->kode_transaksi }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    @if ($trx->jenis_transaksi == 'Setor')
                                        <span
                                            class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            <i class="bi bi-arrow-down-left"></i> Setor
                                        </span>
                                    @elseif ($trx->jenis_transaksi == 'Tarik')
                                        <span
                                            class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            <i class="bi bi-arrow-up-right"></i> Tarik
                                        </span>
                                    @elseif ($trx->jenis_transaksi == 'Bagi Tabungan')
                                        <span
                                            class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                            <i class="bi bi-gift"></i> Pembagian
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            Potongan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold">
                                    Rp {{ number_format($trx->nominal_kotor, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-rose-600 dark:text-rose-400">
                                    @if ($trx->nominal_potongan > 0)
                                        Rp {{ number_format($trx->nominal_potongan, 0, ',', '.') }}
                                        <span
                                            class="text-[10px] block text-zinc-400">({{ $trx->persentase_potongan }}%)</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td
                                    class="py-3 px-3 text-right font-mono font-black {{ $trx->jenis_transaksi == 'Setor' ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-900 dark:text-white' }}">
                                    Rp {{ number_format($trx->nominal_bersih, 0, ',', '.') }}
                                </td>
                                <td
                                    class="py-3 px-3 text-right font-mono font-black text-primary dark:text-primary-dark">
                                    Rp {{ number_format($trx->saldo_setelah, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-zinc-500 text-[11px]">
                                    <div class="font-bold text-zinc-900 dark:text-white">
                                        {{ $trx->keterangan ?? '-' }}
                                    </div>
                                    @if ($trx->kategoriPenarikan)
                                        <div class="mt-0.5">
                                            <span
                                                class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20">
                                                <i class="bi bi-tag-fill text-[8px]"></i>
                                                {{ $trx->kategoriPenarikan->nama_kategori }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="text-[10px] text-zinc-400 mt-0.5">Petugas:
                                        {{ $trx->petugas?->name ?? 'Sistem' }}</div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($trx->jenis_transaksi == 'Setor')
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('tabungan.setor.edit', $trx->id) }}"
                                                class="action-modal p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 transition-colors inline-flex items-center justify-center cursor-pointer"
                                                title="Edit Setoran">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                            <form action="{{ route('tabungan.setor.destroy', $trx->id) }}"
                                                method="POST" class="delete-ajax inline m-0 p-0"
                                                data-refresh-target="#data-grid-container">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 transition-colors cursor-pointer inline-flex items-center justify-center"
                                                    title="Hapus Setoran">
                                                    <i class="bi bi-trash3 text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif ($trx->jenis_transaksi == 'Tarik')
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('tabungan.tarik.edit', $trx->id) }}"
                                                class="action-modal p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 transition-colors inline-flex items-center justify-center cursor-pointer"
                                                title="Edit Penarikan">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                            <form action="{{ route('tabungan.tarik.destroy', $trx->id) }}"
                                                method="POST" class="delete-ajax inline m-0 p-0"
                                                data-refresh-target="#data-grid-container">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 transition-colors cursor-pointer inline-flex items-center justify-center"
                                                    title="Hapus Penarikan">
                                                    <i class="bi bi-trash3 text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-zinc-400 text-[10px]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($transaksis->hasPages())
                <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
                    {{ $transaksis->links() }}
                </div>
            @endif
        </div>
    @else
        <x-empty-state icon="bi-clock-history" title="Belum Ada Transaksi"
            message="Belum ada riwayat mutasi setoran atau penarikan pada rekening ini." />
    @endif

    <!-- MODAL SETOR TUNAI -->
    <div id="modalSetor"
        class="fixed inset-0 z-50 hidden bg-black/60 dark:bg-black/80 backdrop-blur-md items-center justify-center p-4">
        <div
            class="m3-glass-card rounded-2xl md:rounded-3xl w-full max-w-md overflow-hidden relative shadow-2xl border border-zinc-200/80 dark:border-zinc-800">
            <div
                class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-arrow-down-left-circle-fill text-emerald-600"></i>
                    Input Setor Tunai
                </h3>
                <button type="button" onclick="closeModalSetor()"
                    class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
                    <i class="bi bi-x-lg text-xs font-bold"></i>
                </button>
            </div>

            <form action="{{ route('tabungan.setor') }}" method="POST">
                @csrf
                <input type="hidden" name="tabungan_id" value="{{ $tabungan->id }}">

                <div class="p-5 md:p-6 space-y-4 text-xs">
                    <div class="space-y-1.5">
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300">Tanggal Setor</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="m3-input-glass w-full">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300">Nominal Setoran (Rp)</label>
                        <input type="number" name="nominal" min="1000" step="500" required
                            placeholder="Contoh: 50000"
                            class="m3-input-glass w-full text-base font-mono font-bold text-emerald-600 dark:text-emerald-400">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300">Keterangan / Catatan</label>
                        <input type="text" name="keterangan" placeholder="Opsional (misal: Setoran Mingguan)"
                            class="m3-input-glass w-full">
                    </div>
                </div>

                <div
                    class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModalSetor()" class="m3-btn-secondary text-xs">
                        Batal
                    </button>
                    <button type="submit" class="m3-btn-primary !bg-emerald-600 hover:!bg-emerald-700 text-xs">
                        Simpan Setoran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TARIK TUNAI -->
    <div id="modalTarik"
        class="fixed inset-0 z-50 hidden bg-black/60 dark:bg-black/80 backdrop-blur-md items-center justify-center p-4">
        <div
            class="m3-glass-card rounded-2xl md:rounded-3xl w-full max-w-md overflow-hidden relative shadow-2xl border border-zinc-200/80 dark:border-zinc-800">
            <div
                class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-arrow-up-right-circle-fill text-amber-600"></i>
                    Input Penarikan Tunai
                </h3>
                <button type="button" onclick="closeModalTarik()"
                    class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
                    <i class="bi bi-x-lg text-xs font-bold"></i>
                </button>
            </div>

            <form action="{{ route('tabungan.tarik') }}" method="POST" id="formTarik">
                @csrf
                <input type="hidden" name="tabungan_id" value="{{ $tabungan->id }}">

                <div class="p-5 md:p-6 space-y-4 text-xs">
                    <!-- Info Saldo & Potongan -->
                    <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 space-y-1.5">
                        <div class="flex justify-between items-center text-[11px] text-zinc-600 dark:text-zinc-400">
                            <span>Total Saldo Tabungan:</span>
                            <span class="font-mono font-bold text-zinc-900 dark:text-white">Rp
                                {{ number_format($tabungan->saldo, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] text-zinc-600 dark:text-zinc-400">
                            <span>Potongan Madrasah ({{ $kalkulasiPenarikan['persentase'] }}%):</span>
                            <span class="font-mono font-bold text-rose-600 dark:text-rose-400">- Rp
                                {{ number_format($kalkulasiPenarikan['nominal_potongan'], 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center pt-1 border-t border-amber-500/20 font-black text-xs text-amber-800 dark:text-amber-300">
                            <span>Bisa Ditarik (Maksimal):</span>
                            <span class="font-mono text-sm">Rp
                                {{ number_format($kalkulasiPenarikan['saldo_dapat_ditarik'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if (isset($kategoriPenarikans) && $kategoriPenarikans->isNotEmpty())
                        <div class="space-y-1.5">
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">Kategori / Peruntukan
                                Penarikan</label>
                            <select name="kategori_penarikan_id" class="m3-input-glass w-full text-xs font-bold">
                                @foreach ($kategoriPenarikans as $kat)
                                    <option value="{{ $kat->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }} ({{ $kat->jenis_tujuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300">Tanggal Penarikan</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="m3-input-glass w-full">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300">Nominal Tarik dari Tabungan
                            (Rp)</label>
                        <input type="number" name="nominal" id="nominalTarikInput" min="1000"
                            max="{{ $kalkulasiPenarikan['saldo_dapat_ditarik'] }}" step="500" required
                            placeholder="Maks: {{ $kalkulasiPenarikan['saldo_dapat_ditarik'] }}"
                            oninput="hitungTarikLive(this.value, {{ $tabungan->saldo }}, {{ $kalkulasiPenarikan['saldo_dapat_ditarik'] }})"
                            class="m3-input-glass w-full text-base font-mono font-bold text-rose-600 dark:text-rose-400">
                        <div class="flex justify-between items-center mt-1 text-[10px] text-zinc-400">
                            <span>Maksimal Ditarik: Rp
                                {{ number_format($kalkulasiPenarikan['saldo_dapat_ditarik'], 0, ',', '.') }}</span>
                            <button type="button"
                                onclick="setNominalMaks({{ $kalkulasiPenarikan['saldo_dapat_ditarik'] }}, {{ $tabungan->saldo }})"
                                class="text-primary font-bold hover:underline cursor-pointer">Tarik Maksimal</button>
                        </div>
                    </div>

                    <!-- Live Calculation Card -->
                    <div
                        class="p-3.5 rounded-2xl bg-zinc-100/80 dark:bg-zinc-900/80 border border-zinc-200/80 dark:border-zinc-800 space-y-1.5">
                        <div class="flex justify-between text-zinc-500">
                            <span>Uang Tunai Diserahkan:</span>
                            <span id="liveBersih"
                                class="font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">Rp 0</span>
                        </div>
                        <div
                            class="flex justify-between font-bold text-zinc-700 dark:text-zinc-300 pt-1.5 border-t border-zinc-200/80 dark:border-zinc-800 text-[11px]">
                            <span>Sisa Saldo Tabungan (Buku):</span>
                            <span id="liveSisaSaldo" class="font-mono font-bold text-zinc-800 dark:text-zinc-200">Rp
                                {{ number_format($tabungan->saldo, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-zinc-700 dark:text-zinc-300 text-[11px]">
                            <span>Sisa Dapat Ditarik:</span>
                            <span id="liveSisaTarik" class="font-mono font-bold text-amber-600 dark:text-amber-400">Rp
                                {{ number_format($kalkulasiPenarikan['saldo_dapat_ditarik'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300">Keterangan / Keperluan</label>
                        <input type="text" name="keterangan" placeholder="Opsional (misal: Penarikan Uang Saku)"
                            class="m3-input-glass w-full">
                    </div>
                </div>

                <div
                    class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeModalTarik()" class="m3-btn-secondary text-xs">
                        Batal
                    </button>
                    <button type="submit" class="m3-btn-primary !bg-amber-600 hover:!bg-amber-700 text-xs">
                        Proses Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openModalSetor() {
                const el = document.getElementById('modalSetor');
                if (el) {
                    el.classList.remove('hidden');
                    el.classList.add('flex');
                }
            }

            function closeModalSetor() {
                const el = document.getElementById('modalSetor');
                if (el) {
                    el.classList.remove('flex');
                    el.classList.add('hidden');
                }
            }

            function openModalTarik() {
                const el = document.getElementById('modalTarik');
                if (el) {
                    el.classList.remove('hidden');
                    el.classList.add('flex');
                }
            }

            function closeModalTarik() {
                const el = document.getElementById('modalTarik');
                if (el) {
                    el.classList.remove('flex');
                    el.classList.add('hidden');
                }
            }

            function hitungTarikLive(nominal, saldoTotal, saldoDapatDitarik) {
                let n = parseFloat(nominal) || 0;
                let sisaBuku = Math.max(0, saldoTotal - n);
                let sisaTarik = Math.max(0, saldoDapatDitarik - n);

                document.getElementById('liveBersih').innerText = 'Rp ' + n.toLocaleString('id-ID');
                document.getElementById('liveSisaSaldo').innerText = 'Rp ' + sisaBuku.toLocaleString('id-ID');
                document.getElementById('liveSisaTarik').innerText = 'Rp ' + sisaTarik.toLocaleString('id-ID');
            }

            function setNominalMaks(saldoDapatDitarik, saldoTotal) {
                document.getElementById('nominalTarikInput').value = saldoDapatDitarik;
                hitungTarikLive(saldoDapatDitarik, saldoTotal, saldoDapatDitarik);
            }
        </script>
    @endpush

</x-app-layout>
