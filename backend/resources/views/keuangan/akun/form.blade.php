<form action="{{ isset($akun) ? route('keuangan.akun.update', $akun->id) : route('keuangan.akun.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($akun))
        @method('PUT')
    @endif

    <!-- Modal Header (Compact M3) -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg font-black border border-emerald-500/20 shadow-2xs">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    {{ isset($akun) ? 'Edit Pos Akun Keuangan' : 'Tambah Pos Akun Keuangan' }}
                </h3>
                <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                    Perbendaharaan & Pos Kas Madrasah
                </p>
            </div>
        </div>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shadow-2xs">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Kode Akun -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Kode Pos Akun <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kode_akun" value="{{ $akun->kode_akun ?? old('kode_akun') }}" required
                    placeholder="Contoh: KAS-01, SPP-01"
                    class="m3-input-glass w-full text-xs font-bold font-mono uppercase">
            </div>

            <!-- Tipe Akun -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Tipe Pos Akun <span class="text-rose-500">*</span>
                </label>
                <select name="tipe_akun" required class="m3-input-glass w-full text-xs font-bold">
                    <option value="kas" {{ ($akun->tipe_akun ?? 'kas') == 'kas' ? 'selected' : '' }}>Kas Tunai
                        (Fisik)</option>
                    <option value="bank" {{ ($akun->tipe_akun ?? '') == 'bank' ? 'selected' : '' }}>Bank / Rekening
                    </option>
                    <option value="operasional" {{ ($akun->tipe_akun ?? '') == 'operasional' ? 'selected' : '' }}>
                        Operasional</option>
                    <option value="investasi" {{ ($akun->tipe_akun ?? '') == 'investasi' ? 'selected' : '' }}>Investasi
                        & Aset</option>
                    <option value="kewajiban" {{ ($akun->tipe_akun ?? '') == 'kewajiban' ? 'selected' : '' }}>Kewajiban
                        / Hutang</option>
                    <option value="ekuitas" {{ ($akun->tipe_akun ?? '') == 'ekuitas' ? 'selected' : '' }}>Ekuitas /
                        Modal</option>
                </select>
            </div>
        </div>

        <!-- Nama Pos Akun -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Nama Pos Akun <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_akun" value="{{ $akun->nama_akun ?? old('nama_akun') }}" required
                placeholder="Contoh: Kas Umum Madrasah, Kas SPP / Syahriyah"
                class="m3-input-glass w-full text-xs font-bold">
        </div>

        <!-- Saldo Awal (Khusus Create) -->
        @if (!isset($akun))
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Saldo Awal (Rp)
                </label>
                <input type="number" name="saldo_awal" min="0" step="1000"
                    value="{{ old('saldo_awal', 0) }}" placeholder="0"
                    class="m3-input-glass w-full text-xs font-bold font-mono">
                <span class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500 mt-1 ml-1 block">
                    Saldo berjalan akan disesuaikan otomatis saat transaksi dicatat.
                </span>
            </div>
        @endif

        <!-- Deskripsi / Catatan -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Deskripsi / Catatan Pos
            </label>
            <textarea name="deskripsi" rows="3" placeholder="Keterangan peruntukan atau rincian pos akun ini..."
                class="m3-input-glass w-full text-xs font-medium resize-none">{{ $akun->deskripsi ?? old('deskripsi') }}</textarea>
        </div>

        <!-- Toggle Status -->
        <div
            class="flex items-center justify-between p-3.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/70">
            <div>
                <label class="text-xs font-black text-zinc-900 dark:text-white block">Status Pos Akun</label>
                <span class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500">
                    Pos aktif dapat dipilih pada form pencatatan transaksi kas & pinjaman.
                </span>
            </div>
            <x-toggle name="is_active" :checked="isset($akun) ? $akun->is_active : true" />
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-save2-fill mr-1.5"></i> {{ isset($akun) ? 'Simpan Perubahan' : 'Simpan Data' }}
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="mt-2 sm:mt-0 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all">
            Batal
        </button>
    </div>
</form>
