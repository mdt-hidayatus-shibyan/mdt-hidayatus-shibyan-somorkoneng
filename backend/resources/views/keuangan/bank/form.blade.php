<form action="{{ isset($bank) ? route('keuangan.bank.update', $bank->id) : route('keuangan.bank.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($bank))
        @method('PUT')
    @endif

    <!-- Modal Header (Compact M3) -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg font-black border border-blue-500/20 shadow-2xs">
                <i class="bi bi-bank"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    {{ isset($bank) ? 'Edit Rekening Bank' : 'Tambah Rekening Bank' }}
                </h3>
                <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                    Data Rekening & Transfer Bank Madrasah
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
            <!-- Nama Bank -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Nama Bank <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_bank" value="{{ $bank->nama_bank ?? old('nama_bank') }}" required
                    placeholder="Contoh: BCA, BRI, BSI, Bank Jatim" class="m3-input-glass w-full text-xs font-bold">
            </div>

            <!-- Kode Bank -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Kode Bank (BI / Kliring)
                </label>
                <input type="text" name="kode_bank" value="{{ $bank->kode_bank ?? old('kode_bank') }}"
                    placeholder="Contoh: 014, 002, 451" class="m3-input-glass w-full text-xs font-bold font-mono">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Nomor Rekening -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Nomor Rekening <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nomor_rekening" value="{{ $bank->nomor_rekening ?? old('nomor_rekening') }}"
                    required placeholder="Contoh: 7182938495" class="m3-input-glass w-full text-xs font-bold font-mono">
            </div>

            <!-- Atas Nama Rekening -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Atas Nama Rekening <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="atas_nama" value="{{ $bank->atas_nama ?? old('atas_nama') }}" required
                    placeholder="Contoh: MDT HIDAYATUS SHIBYAN"
                    class="m3-input-glass w-full text-xs font-bold uppercase">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Kantor Cabang -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Kantor Cabang
                </label>
                <input type="text" name="cabang" value="{{ $bank->cabang ?? old('cabang') }}"
                    placeholder="Contoh: KC Bangkalan, KCP Blega" class="m3-input-glass w-full text-xs font-bold">
            </div>

            <!-- Saldo Awal (Khusus Create) -->
            @if (!isset($bank))
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                        Saldo Awal Kas Bank (Rp)
                    </label>
                    <input type="number" name="saldo" min="0" step="1000" value="{{ old('saldo', 0) }}"
                        placeholder="0" class="m3-input-glass w-full text-xs font-bold font-mono">
                </div>
            @endif
        </div>

        <!-- Toggle Rekening Default & Status -->
        <div class="space-y-3 pt-2">
            <div
                class="flex items-center justify-between p-3.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/70">
                <div>
                    <label class="text-xs font-black text-zinc-900 dark:text-white block">Jadikan Rekening Utama</label>
                    <span class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500">
                        Rekening ini otomatis dipilih sebagai tujuan transfer default madrasah.
                    </span>
                </div>
                <x-toggle name="is_default" :checked="isset($bank) ? $bank->is_default : false" />
            </div>

            <div
                class="flex items-center justify-between p-3.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/70">
                <div>
                    <label class="text-xs font-black text-zinc-900 dark:text-white block">Status Rekening</label>
                    <span class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500">
                        Aktifkan agar dapat dipilih untuk mutasi penerimaan transfer.
                    </span>
                </div>
                <x-toggle name="is_active" :checked="isset($bank) ? $bank->is_active : true" />
            </div>
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-save2-fill mr-1.5"></i> {{ isset($bank) ? 'Simpan Perubahan' : 'Simpan Data' }}
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="mt-2 sm:mt-0 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all">
            Batal
        </button>
    </div>
</form>
