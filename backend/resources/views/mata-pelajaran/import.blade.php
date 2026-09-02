<form action="{{ route('mata-pelajaran.import.store') }}" method="POST" enctype="multipart/form-data"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            Import Mata Pelajaran
        </h3>
        <!-- Touch Target 40px -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1">

        <!-- Kotak Informasi -->
        <div
            class="mb-4 p-4 rounded-xl bg-blue-50/80 dark:bg-blue-950/30 border border-blue-200/80 dark:border-blue-800/40 flex items-start gap-3 transition-colors duration-300">
            <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400 text-base shrink-0 mt-0.5"></i>
            <div>
                <p class="text-[10px] font-extrabold text-blue-800 dark:text-blue-300 uppercase tracking-wider mb-1">
                    Panduan Import CSV:
                </p>
                <ol
                    class="text-xs font-semibold text-blue-900 dark:text-blue-300/90 space-y-1 ml-4 list-decimal marker:font-bold">
                    <li>Unduh template CSV yang telah disediakan.</li>
                    <li>Isi kolom data dengan benar sesuai format kurikulum.</li>
                    <li>Simpan file sebagai format CSV (Comma delimited).</li>
                    <li>Unggah file CSV pada form di bawah.</li>
                </ol>
            </div>
        </div>

        <!-- Tombol Unduh Template -->
        <a href="{{ route('mata-pelajaran.template') }}"
            class="min-h-[38px] w-full flex items-center justify-center mb-4 px-4 py-2 border border-emerald-300/80 dark:border-emerald-800/50 bg-emerald-50/80 dark:bg-emerald-950/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 rounded-xl text-xs font-black text-emerald-700 dark:text-emerald-300 transition-all active:scale-95 outline-none">
            <i class="bi bi-download mr-1.5"></i> Unduh Template CSV
        </a>

        <!-- Input Upload File -->
        <div class="space-y-1.5">
            <label
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Upload File CSV <span class="text-rose-500">*</span>
            </label>
            <input type="file" name="file_import" accept=".csv"
                class="block w-full text-xs font-semibold text-zinc-500 dark:text-zinc-400
                file:mr-3 file:py-1.5 file:px-4
                file:rounded-xl file:border-0
                file:text-[11px] file:font-black file:uppercase file:tracking-wider
                file:bg-primary/10 dark:file:bg-primary-dark/20 file:text-primary dark:file:text-primary-dark
                hover:file:bg-primary/20 dark:hover:file:bg-primary-dark/30 file:transition-colors file:cursor-pointer
                cursor-pointer m3-input-glass !p-1.5
                {{ $errors->has('file_import') ? '!border-red-500 !ring-red-500/20' : '' }}">

            @error('file_import')
                <p class="text-[11px] font-bold text-rose-500 dark:text-rose-400 mt-1 ml-1 flex items-center">
                    <i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-cloud-arrow-up-fill text-sm"></i>
            <span>Mulai Import</span>
        </button>
    </div>
</form>

