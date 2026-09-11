<div class="space-y-4">
    <!-- Header Info -->
    <div
        class="flex items-center justify-between p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200/80 dark:border-zinc-800">
        <div>
            <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block">Kode Transaksi</span>
            <span
                class="text-base font-black text-zinc-900 dark:text-white font-mono">{{ $transaksi->kode_transaksi }}</span>
        </div>
        <div class="text-right">
            <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block">Tanggal</span>
            <span
                class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $transaksi->tanggal_transaksi->translatedFormat('d F Y') }}</span>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-2 gap-3 text-xs">
        <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/60 dark:border-zinc-800/60">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Jenis
                Transaksi</span>
            <span class="font-bold text-zinc-900 dark:text-white uppercase">{{ $transaksi->jenis_transaksi }}</span>
        </div>
        <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/60 dark:border-zinc-800/60">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Metode
                Pembayaran</span>
            <span
                class="font-bold text-zinc-900 dark:text-white">{{ $transaksi->metode_pembayaran === 'transfer_bank' ? 'Transfer Bank (' . ($transaksi->bank->nama_bank ?? 'Bank') . ')' : 'Tunai' }}</span>
        </div>
        <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/60 dark:border-zinc-800/60">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Pos Akun Kas</span>
            <span
                class="font-bold text-zinc-900 dark:text-white">{{ $transaksi->akunKeuangan->nama_akun ?? '-' }}</span>
        </div>
        <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/60 dark:border-zinc-800/60">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-0.5">Kategori</span>
            <span
                class="font-bold text-zinc-900 dark:text-white">{{ $transaksi->kategoriKeuangan->nama_kategori ?? '-' }}</span>
        </div>
    </div>

    <!-- Nominal Card -->
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-between">
        <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Total Nominal</span>
        <span class="text-lg font-black text-emerald-700 dark:text-emerald-400 font-mono">
            Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}
        </span>
    </div>

    <!-- Keterangan & Catatan -->
    <div
        class="p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200/60 dark:border-zinc-800/60 text-xs">
        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block mb-1">Uraian / Keterangan</span>
        <p class="text-zinc-700 dark:text-zinc-300 font-medium">
            {{ $transaksi->keterangan ?? 'Tidak ada keterangan tambahan.' }}</p>
    </div>

    <!-- Bukti Transaksi -->
    @if ($transaksi->bukti_transaksi)
        <div>
            <span
                class="text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-2 block uppercase tracking-wider">Lampiran
                Bukti Transaksi</span>
            <div
                class="p-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex items-center justify-between">
                <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" target="_blank"
                    class="text-xs font-bold text-primary dark:text-primary-dark hover:underline flex items-center gap-2">
                    <i class="bi bi-file-earmark-image text-base"></i>
                    <span>Lihat Dokumen / Foto Bukti</span>
                </a>
                <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" download
                    class="px-2.5 py-1 rounded-xl bg-zinc-200 dark:bg-zinc-800 text-[11px] font-bold hover:bg-zinc-300">
                    Unduh
                </a>
            </div>
        </div>
    @endif

    <!-- Footer Actions -->
    <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-200/80 dark:border-zinc-800">
        <a href="{{ route('keuangan.transaksi.cetak-kwitansi', $transaksi->id) }}" target="_blank"
            class="px-4 py-2 rounded-2xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-600 dark:text-purple-400 text-xs font-bold transition-all flex items-center gap-1.5">
            <i class="bi bi-printer-fill"></i>
            <span>Cetak Kwitansi</span>
        </a>
        <button type="button" onclick="closeDetailTransaksiModal()"
            class="px-4 py-2 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all">
            Tutup
        </button>
    </div>
</div>
