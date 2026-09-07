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
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="openModalSetor()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2 rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="bi bi-arrow-down-left-circle-fill"></i>
                        <span>Setor Tunai</span>
                    </button>
                    <button type="button" onclick="openModalTarik()"
                        class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-3.5 py-2 rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                        <span>Tarik Tunai</span>
                    </button>
                    <x-button type="button" onclick="openModalPengajuan()" variant="secondary" size="sm"
                        icon="bi-file-earmark-text">
                        Ajukan
                    </x-button>
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
                    <span>Potongan Penarikan:</span>
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
                                    <div class="font-bold text-zinc-800 dark:text-zinc-200">
                                        {{ $trx->keterangan ?? '-' }}</div>
                                    <div class="text-[10px] text-zinc-400">Petugas:
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
        class="fixed inset-0 z-50 hidden bg-zinc-900/60 backdrop-blur-xs items-center justify-center p-4">
        <div class="m3-glass-card rounded-2xl w-full max-w-md p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-arrow-down-left-circle-fill text-emerald-600"></i>
                    Input Setor Tunai
                </h3>
                <button type="button" onclick="closeModalSetor()"
                    class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('tabungan.setor') }}" method="POST">
                @csrf
                <input type="hidden" name="tabungan_id" value="{{ $tabungan->id }}">

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Setor</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="m3-input-glass w-full">
                    </div>

                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Nominal Setoran
                            (Rp)</label>
                        <input type="number" name="nominal" min="1000" step="500" required
                            placeholder="Contoh: 50000"
                            class="m3-input-glass w-full text-base font-bold text-emerald-600">
                    </div>

                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Keterangan /
                            Catatan</label>
                        <input type="text" name="keterangan" placeholder="Opsional (misal: Setoran Mingguan)"
                            class="m3-input-glass w-full">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" onclick="closeModalSetor()" variant="secondary" size="sm">
                        Batal
                    </x-button>
                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-black shadow-xs cursor-pointer">
                        Simpan Setoran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TARIK TUNAI -->
    <div id="modalTarik"
        class="fixed inset-0 z-50 hidden bg-zinc-900/60 backdrop-blur-xs items-center justify-center p-4">
        <div class="m3-glass-card rounded-2xl w-full max-w-md p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-arrow-up-right-circle-fill text-amber-600"></i>
                    Input Penarikan Tunai
                </h3>
                <button type="button" onclick="closeModalTarik()"
                    class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('tabungan.tarik') }}" method="POST" id="formTarik">
                @csrf
                <input type="hidden" name="tabungan_id" value="{{ $tabungan->id }}">

                <div class="space-y-4 text-xs">
                    <div
                        class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400">
                        <span class="block text-[10px] font-black uppercase">Ketentuan Potongan
                            ({{ $kalkulasiPenarikan['persentase'] }}%)</span>
                        <span class="text-[11px]">Setiap penarikan dana nasabah jenis
                            <strong>{{ $tabungan->jenis_nasabah }}</strong> dikenakan potongan sebesar
                            <strong>{{ $kalkulasiPenarikan['persentase'] }}%</strong>.</span>
                    </div>

                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Penarikan</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="m3-input-glass w-full">
                    </div>

                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Nominal Tarik dari
                            Tabungan (Rp)</label>
                        <input type="number" name="nominal" id="nominalTarikInput" min="1000"
                            max="{{ $tabungan->saldo }}" step="500" required
                            placeholder="Maks: {{ $tabungan->saldo }}"
                            oninput="hitungPotonganLive(this.value, {{ $kalkulasiPenarikan['persentase'] }})"
                            class="m3-input-glass w-full text-base font-bold text-rose-600">
                        <div class="flex justify-between items-center mt-1 text-[10px] text-zinc-400">
                            <span>Maksimal Saldo: Rp {{ number_format($tabungan->saldo, 0, ',', '.') }}</span>
                            <button type="button"
                                onclick="setNominalMaks({{ $tabungan->saldo }}, {{ $kalkulasiPenarikan['persentase'] }})"
                                class="text-primary font-bold hover:underline cursor-pointer">Tarik Semua</button>
                        </div>
                    </div>

                    <!-- Live Calculation Card -->
                    <div
                        class="p-3 rounded-xl bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-1">
                        <div class="flex justify-between text-zinc-500">
                            <span>Potongan ({{ $kalkulasiPenarikan['persentase'] }}%):</span>
                            <span id="livePotongan" class="font-bold text-rose-600">Rp 0</span>
                        </div>
                        <div
                            class="flex justify-between font-black text-zinc-900 dark:text-white pt-1 border-t border-zinc-200 dark:border-zinc-800">
                            <span>Uang Bersih Diserahkan:</span>
                            <span id="liveBersih" class="font-black text-emerald-600">Rp 0</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Keterangan /
                            Keperluan</label>
                        <input type="text" name="keterangan" placeholder="Opsional (misal: Penarikan Uang Saku)"
                            class="m3-input-glass w-full">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" onclick="closeModalTarik()" variant="secondary" size="sm">
                        Batal
                    </x-button>
                    <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl text-xs font-black shadow-xs cursor-pointer">
                        Proses Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL AJUKAN PENARIKAN -->
    <div id="modalPengajuan"
        class="fixed inset-0 z-50 hidden bg-zinc-900/60 backdrop-blur-xs items-center justify-center p-4">
        <div class="m3-glass-card rounded-2xl w-full max-w-md p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    Form Pengajuan Penarikan Dana
                </h3>
                <button type="button" onclick="closeModalPengajuan()"
                    class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="{{ route('tabungan.pengajuan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tabungan_id" value="{{ $tabungan->id }}">

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Nominal yang Diajukan
                            (Rp)</label>
                        <input type="number" name="nominal_pengajuan" min="1000" max="{{ $tabungan->saldo }}"
                            required placeholder="Maks: {{ $tabungan->saldo }}"
                            class="m3-input-glass w-full text-base font-bold text-primary">
                    </div>

                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Alasan Penarikan</label>
                        <textarea name="alasan_penarikan" rows="3" required placeholder="Jelaskan peruntukan penarikan dana..."
                            class="m3-input-glass w-full"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" onclick="closeModalPengajuan()" variant="secondary" size="sm">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Kirim Pengajuan
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openModalSetor() {
                document.getElementById('modalSetor').classList.remove('hidden');
                document.getElementById('modalSetor').classList.add('flex');
            }

            function closeModalSetor() {
                document.getElementById('modalSetor').classList.remove('flex');
                document.getElementById('modalSetor').classList.add('hidden');
            }

            function openModalTarik() {
                document.getElementById('modalTarik').classList.remove('hidden');
                document.getElementById('modalTarik').classList.add('flex');
            }

            function closeModalTarik() {
                document.getElementById('modalTarik').classList.remove('flex');
                document.getElementById('modalTarik').classList.add('hidden');
            }

            function openModalPengajuan() {
                document.getElementById('modalPengajuan').classList.remove('hidden');
                document.getElementById('modalPengajuan').classList.add('flex');
            }

            function closeModalPengajuan() {
                document.getElementById('modalPengajuan').classList.remove('flex');
                document.getElementById('modalPengajuan').classList.add('hidden');
            }

            function hitungPotonganLive(nominal, persen) {
                let n = parseFloat(nominal) || 0;
                let pot = (n * persen) / 100;
                let bersih = n - pot;

                document.getElementById('livePotongan').innerText = 'Rp ' + pot.toLocaleString('id-ID');
                document.getElementById('liveBersih').innerText = 'Rp ' + bersih.toLocaleString('id-ID');
            }

            function setNominalMaks(saldo, persen) {
                document.getElementById('nominalTarikInput').value = saldo;
                hitungPotonganLive(saldo, persen);
            }
        </script>
    @endpush

</x-app-layout>
