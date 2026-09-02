<form action="{{ route('murid.import.store') }}" method="POST" enctype="multipart/form-data"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh] bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl rounded-2xl md:rounded-3xl overflow-hidden border border-zinc-200/80 dark:border-zinc-800 shadow-2xl">
    @csrf

    <!-- Modal Header -->
    <div
        class="px-5 py-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/80 dark:bg-zinc-950/60 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shrink-0 shadow-2xs">
                <i class="bi bi-file-earmark-arrow-up text-base"></i>
            </div>
            <div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Import Data Murid
                </h3>
                <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mt-0.5">Unggah CSV Master Santri</p>
            </div>
        </div>

        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-9 min-h-9 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shrink-0"
            title="Tutup">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar flex-1">

        <!-- Kotak Informasi -->
        <div
            class="mb-4 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-start gap-3">
            <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400 text-base shrink-0 mt-0.5"></i>
            <div>
                <p class="text-[11px] font-black text-blue-800 dark:text-blue-300 uppercase tracking-wider mb-1">
                    Petunjuk Pengisian CSV:
                </p>
                <ol
                    class="text-xs font-semibold text-blue-950 dark:text-blue-200/90 space-y-1 ml-3.5 list-decimal marker:font-black">
                    <li>Unduh template CSV yang telah disediakan di bawah.</li>
                    <li>Isi kolom dengan benar. Format Tanggal Lahir: <strong
                            class="font-black text-blue-900 dark:text-blue-100 underline decoration-blue-400">YYYY-MM-DD</strong>.
                    </li>
                    <li>Simpan dokumen sebagai CSV (Comma delimited).</li>
                    <li>Unggah file CSV pada form di bawah.</li>
                </ol>
            </div>
        </div>

        <!-- Tombol Unduh Template -->
        <a href="{{ route('murid.template') }}"
            class="min-h-10 w-full flex items-center justify-center mb-4 px-4 py-2 border border-dashed border-emerald-500/40 dark:border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 rounded-xl text-xs font-black text-emerald-700 dark:text-emerald-400 transition-all active:scale-95 outline-none">
            <i class="bi bi-download mr-1.5 text-sm"></i> Unduh Template CSV (.csv)
        </a>

        <!-- Input Upload File -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                Upload File CSV <span class="text-rose-500">*</span>
            </label>
            <input type="file" name="file_import" accept=".csv"
                class="block w-full text-xs font-bold text-zinc-600 dark:text-zinc-300
                file:mr-3 file:py-2 file:px-4
                file:rounded-xl file:border-0
                file:text-[10px] file:font-black file:uppercase file:tracking-wider
                file:bg-primary/10 dark:file:bg-primary-dark/20 file:text-primary dark:file:text-primary-dark
                hover:file:bg-primary/20 dark:hover:file:bg-primary-dark/30 file:transition-colors file:cursor-pointer
                cursor-pointer m3-input-glass !p-1.5
                {{ $errors->has('file_import') ? '!border-rose-500' : '' }}">

            @error('file_import')
                <p class="text-[11px] font-bold text-rose-500 mt-1 ml-1 flex items-center">
                    <i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="px-5 py-3.5 bg-zinc-50/80 dark:bg-zinc-950/60 border-t border-zinc-200/80 dark:border-zinc-800 flex flex-col-reverse sm:flex-row justify-end gap-2.5 shrink-0">
        <button type="button" data-dismiss="modal"
            class="m3-btn-secondary w-full sm:w-auto h-10 px-5">
            Batal
        </button>
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-6 group/btn">
            <i class="bi bi-cloud-arrow-up-fill text-xs"></i>
            <span>Import Sekarang</span>
        </button>
    </div>
</form>

