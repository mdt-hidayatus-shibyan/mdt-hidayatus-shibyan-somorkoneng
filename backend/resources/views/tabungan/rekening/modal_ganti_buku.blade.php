<!-- Modal Ganti Buku Tabungan Fisik (custom-script.js AJAX Form) -->
<form action="{{ route('tabungan.rekening.ganti-buku.store', $tabungan->id) }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-base border border-indigo-500/20">
                <i class="bi bi-arrow-left-right"></i>
            </div>
            <div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">
                    Ganti Buku Tabungan Fisik
                </h3>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    Pengalihan nomor barcode ke buku fisik baru
                </p>
            </div>
        </div>
        <!-- Tombol Tutup Dialog Modal -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1 space-y-4 text-xs">

        <!-- Ringkasan Nasabah & Barcode Lama -->
        <div
            class="p-4 rounded-2xl bg-gradient-to-br from-indigo-500/5 via-zinc-100/70 to-zinc-100/40 dark:from-indigo-900/10 dark:via-zinc-900/60 dark:to-zinc-900/40 border border-indigo-500/20 dark:border-indigo-500/20 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-black text-zinc-400 block tracking-wider">Pemilik
                        Rekening</span>
                    <span class="font-black text-sm text-zinc-900 dark:text-white block mt-0.5">
                        {{ $tabungan->nama_nasabah }}
                    </span>
                    <span class="text-[11px] font-semibold text-zinc-500">
                        {{ $tabungan->jenis_nasabah }} &bull; {{ $tabungan->identitas_nasabah }}
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase font-black text-zinc-400 block tracking-wider">Saldo Saat
                        Ini</span>
                    <span class="font-black text-sm text-emerald-600 dark:text-emerald-400 block mt-0.5 font-mono">
                        Rp {{ number_format($tabungan->saldo, 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-emerald-600/80 font-bold">
                        <i class="bi bi-shield-check"></i> Saldo Tetap Utuh
                    </span>
                </div>
            </div>

            <div
                class="pt-2.5 border-t border-zinc-200/70 dark:border-zinc-800/80 flex items-center justify-between text-xs">
                <span class="font-bold text-zinc-500 dark:text-zinc-400">Barcode / No. Rekening Lama:</span>
                <span
                    class="px-2.5 py-1 rounded-lg bg-zinc-200/80 dark:bg-zinc-800 font-mono font-black text-zinc-700 dark:text-zinc-300 border border-zinc-300/80 dark:border-zinc-700">
                    <i class="bi bi-upc mr-1 text-zinc-400"></i>{{ $tabungan->nomor_rekening }}
                </span>
            </div>
        </div>

        <!-- Edukasi / Petunjuk Sistem -->
        <div
            class="p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-700 dark:text-blue-300 flex items-start gap-2.5">
            <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400 text-sm mt-0.5 shrink-0"></i>
            <div class="text-[11px] leading-relaxed">
                <span class="font-black block mb-0.5">Ketentuan Penggantian Buku:</span>
                Barcode lama akan dinonaktifkan dan digantikan dengan barcode buku fisik baru. <strong>Seluruh riwayat
                    mutasi transaksi sebelumnya tidak akan hilang</strong> dan langsung terhubung ke barcode baru.
            </div>
        </div>

        <!-- Input Barcode Buku Baru -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider ml-1">
                Scan / Masukkan Barcode Buku Baru <span class="text-rose-500">*</span>
            </label>
            <div class="relative flex items-center">
                <div
                    class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-base">
                    <i class="bi bi-upc-scan text-indigo-500 font-bold"></i>
                </div>
                <input type="text" name="nomor_rekening_baru" required autofocus
                    placeholder="Scan barcode buku baru (contoh: 1000025)"
                    class="m3-input-glass w-full text-base font-mono font-black !py-2.5 !pl-11 text-zinc-900 dark:text-white tracking-wider">
            </div>
            <span class="text-[10px] text-zinc-400 mt-1 block">
                *Pastikan nomor barcode belum pernah dipakai di rekening manapun dan tidak sama dengan nomor lama.
            </span>
        </div>

        <!-- Pilihan Alasan Penggantian -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider ml-1">
                Alasan Penggantian Buku <span class="text-rose-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-2">
                <label
                    class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 cursor-pointer hover:border-indigo-500/50 transition">
                    <input type="radio" name="alasan" value="Buku Hilang" checked
                        class="text-indigo-600 focus:ring-indigo-500">
                    <span class="font-bold text-zinc-800 dark:text-zinc-200 text-xs">
                        <i class="bi bi-question-circle text-amber-500 mr-1"></i> Buku Hilang
                    </span>
                </label>

                <label
                    class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 cursor-pointer hover:border-indigo-500/50 transition">
                    <input type="radio" name="alasan" value="Buku Rusak"
                        class="text-indigo-600 focus:ring-indigo-500">
                    <span class="font-bold text-zinc-800 dark:text-zinc-200 text-xs">
                        <i class="bi bi-shield-slash text-rose-500 mr-1"></i> Buku Rusak
                    </span>
                </label>

                <label
                    class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 cursor-pointer hover:border-indigo-500/50 transition">
                    <input type="radio" name="alasan" value="Halaman Penuh"
                        class="text-indigo-600 focus:ring-indigo-500">
                    <span class="font-bold text-zinc-800 dark:text-zinc-200 text-xs">
                        <i class="bi bi-journal-check text-blue-500 mr-1"></i> Halaman Penuh
                    </span>
                </label>

                <label
                    class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 cursor-pointer hover:border-indigo-500/50 transition">
                    <input type="radio" name="alasan" value="Lainnya" class="text-indigo-600 focus:ring-indigo-500">
                    <span class="font-bold text-zinc-800 dark:text-zinc-200 text-xs">
                        <i class="bi bi-three-dots text-zinc-500 mr-1"></i> Lainnya
                    </span>
                </label>
            </div>
        </div>

        <!-- Catatan Tambahan -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider ml-1">
                Catatan Tambahan (Opsional)
            </label>
            <input type="text" name="catatan" placeholder="Misal: Buku fisik hilang saat liburan semester..."
                class="m3-input-glass w-full text-xs font-medium">
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
            class="m3-btn-primary px-5 py-2.5 text-xs font-black rounded-xl flex items-center gap-1.5 shadow-sm cursor-pointer !bg-indigo-600 hover:!bg-indigo-700">
            <i class="bi bi-arrow-left-right text-sm"></i>
            <span>Simpan & Ganti Buku</span>
        </button>
    </div>
</form>
