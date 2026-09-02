<form action="{{ isset($kampung) ? route('kampung.update', $kampung->id) : route('kampung.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($kampung))
        @method('PUT')
    @endif

    <!-- Modal Header (Compact) -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div>
            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                {{ isset($kampung) ? 'Edit Data Kampung' : 'Tambah Kampung Baru' }}
            </h3>
            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                Wilayah domisili santri & wali
            </p>
        </div>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shadow-2xs">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">
        <!-- Kode -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Kode Kampung
            </label>
            <input type="text" name="kode" value="{{ $kampung->kode ?? old('kode') }}"
                placeholder="Contoh: A, B, atau SMN" class="m3-input-glass w-full text-xs font-bold font-mono uppercase">
        </div>

        <!-- Nama -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Nama Kampung / Dusun <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_kampung" value="{{ $kampung->nama_kampung ?? old('nama_kampung') }}" required
                placeholder="Contoh: Somorkoneng Barat" class="m3-input-glass w-full text-xs font-bold uppercase">
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-save2-fill mr-1.5"></i> {{ isset($kampung) ? 'Simpan Perubahan' : 'Simpan Data' }}
        </button>
    </div>
</form>

