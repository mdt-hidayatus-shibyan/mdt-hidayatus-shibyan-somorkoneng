<form action="{{ route('referensi-pelanggaran.import.store') }}" method="POST" enctype="multipart/form-data"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    <!-- Modal Header (Compact M3) -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div>
            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                Import Referensi Pelanggaran
            </h3>
            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                Unggah berkas CSV daftar kasus kedisiplinan
            </p>
        </div>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shadow-2xs">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors overflow-y-auto custom-scrollbar flex-1 space-y-4">

        <!-- Kotak Informasi -->
        <div
            class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-start gap-3 transition-colors shadow-2xs">
            <i class="bi bi-info-circle-fill text-blue-600 dark:text-blue-400 text-base shrink-0 mt-0.5"></i>
            <div>
                <p class="text-[10px] font-black text-blue-700 dark:text-blue-300 uppercase tracking-wider mb-1">
                    Petunjuk Import CSV:
                </p>
                <ol
                    class="text-xs font-bold text-blue-900 dark:text-blue-300/80 space-y-1 ml-4 list-decimal">
                    <li>Unduh template CSV yang telah disediakan.</li>
                    <li>Isi data sesuai kolom yang ditentukan.</li>
                    <li>Save As -> CSV (Comma delimited) di Excel/Spreadsheet.</li>
                    <li>Unggah file CSV pada form di bawah.</li>
                </ol>
            </div>
        </div>

        <!-- Tombol Unduh Template -->
        <a href="{{ route('referensi-pelanggaran.template') }}"
            class="h-10 w-full flex items-center justify-center px-4 border border-dashed border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 rounded-xl text-xs font-black text-emerald-600 dark:text-emerald-400 transition-all active:scale-95 outline-none shadow-2xs">
            <i class="bi bi-download mr-1.5 text-sm"></i> Unduh Template CSV
        </a>

        <!-- Input Upload File -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Upload File CSV <span class="text-rose-500">*</span>
            </label>
            <input type="file" name="file_import" accept=".csv" required
                class="block w-full text-xs font-bold text-zinc-500 dark:text-zinc-400
                file:mr-3 file:py-2 file:px-4
                file:rounded-xl file:border-0
                file:text-xs file:font-black file:uppercase file:tracking-wider
                file:bg-primary/10 dark:file:bg-primary-dark/20 file:text-primary dark:file:text-primary-dark
                hover:file:bg-primary/20 dark:hover:file:bg-primary-dark/30 file:transition-colors file:cursor-pointer
                cursor-pointer m3-input-glass !p-1.5
                {{ $errors->has('file_import') ? '!border-red-500 !ring-red-500/20' : '' }}">

            @error('file_import')
                <p class="text-[11px] font-bold text-red-500 dark:text-red-400 mt-1.5 ml-1 flex items-center">
                    <i class="bi bi-exclamation-circle-fill mr-1.5"></i> {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-cloud-arrow-up-fill mr-1.5"></i> Proses Import
        </button>
    </div>
</form>

