<!-- Modal Edit Setoran Tunai (custom-script.js AJAX Form) -->
<form action="{{ route('tabungan.setor.update', $transaksi->id) }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @method('PUT')

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm border border-amber-500/20">
                <i class="bi bi-pencil-square"></i>
            </div>
            <div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">
                    Edit Setoran Tunai
                </h3>
                <p class="text-[11px] font-mono text-zinc-400">
                    {{ $transaksi->kode_transaksi }}
                </p>
            </div>
        </div>
        <!-- Tombol Tutup Dialog Modal (data-dismiss="modal" / command="close") -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1 space-y-4 text-xs">

        <!-- Ringkasan Nasabah & Rekening -->
        <div
            class="p-3.5 rounded-2xl bg-zinc-100/70 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-zinc-400 block">Nama Nasabah</span>
                <span class="font-black text-zinc-900 dark:text-white text-xs">
                    {{ $transaksi->tabungan->nama_nasabah ?? '-' }}
                </span>
                <span class="text-[10px] text-zinc-500 block">
                    {{ $transaksi->tabungan->jenis_nasabah ?? '' }}
                </span>
            </div>
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-zinc-400 block">No. Rekening</span>
                <span class="font-mono font-black text-primary text-xs">
                    {{ $transaksi->tabungan->nomor_rekening ?? '-' }}
                </span>
                <span class="text-[10px] text-zinc-400 block font-mono">
                    Saldo: Rp {{ number_format($transaksi->tabungan->saldo ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Tanggal Setoran -->
        <div class="space-y-1.5">
            <label
                class="block text-[11px] font-extrabold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Tanggal Setoran
            </label>
            <input type="date" name="tanggal"
                value="{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('Y-m-d') }}" required
                class="m3-input-glass w-full font-bold text-xs">
        </div>

        <!-- Nominal Setor Baru -->
        <div class="space-y-1.5">
            <label
                class="block text-[11px] font-extrabold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Nominal Setoran (Rp)
            </label>
            <div class="relative flex items-center">
                <div
                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-base font-black text-emerald-600 dark:text-emerald-400 font-mono">
                    <span>Rp</span>
                </div>
                <input type="number" name="nominal" min="1000" step="500"
                    value="{{ (int) $transaksi->nominal_bersih }}" required
                    class="m3-input-glass w-full text-lg font-mono font-black !py-2.5 !pl-11 text-zinc-900 dark:text-white">
            </div>
            <span class="text-[10px] text-zinc-400 mt-1 block">
                *Saldo rekening nasabah akan otomatis dikalkulasikan dengan selisih nominal baru.
            </span>
        </div>

        <!-- Keterangan / Catatan -->
        <div class="space-y-1.5">
            <label
                class="block text-[11px] font-extrabold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Keterangan / Catatan
            </label>
            <input type="text" name="keterangan" value="{{ $transaksi->keterangan ?? 'Setoran Tunai' }}"
                placeholder="Catatan setoran..." class="m3-input-glass w-full text-xs font-medium">
        </div>
    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-end gap-2.5 transition-colors duration-300 shrink-0">
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="px-4 py-2.5 rounded-xl text-xs font-bold text-zinc-500 hover:bg-zinc-200/60 dark:hover:bg-zinc-800 transition cursor-pointer">
            Batal
        </button>
        <button type="submit"
            class="m3-btn-primary px-5 py-2.5 text-xs font-black rounded-xl flex items-center gap-1.5 shadow-sm cursor-pointer">
            <i class="bi bi-check2-circle text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>
