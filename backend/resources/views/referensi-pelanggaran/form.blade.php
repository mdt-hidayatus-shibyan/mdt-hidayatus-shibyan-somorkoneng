<form
    action="{{ isset($referensiPelanggaran) ? route('referensi-pelanggaran.update', $referensiPelanggaran->id) : route('referensi-pelanggaran.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">

    @csrf
    @if (isset($referensiPelanggaran))
        @method('PUT')
    @endif

    <!-- Modal Header (Compact) -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div>
            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                {{ isset($referensiPelanggaran) ? 'Edit Referensi Pelanggaran' : 'Tambah Referensi Pelanggaran' }}
            </h3>
            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                Katalog aturan kedisiplinan santri
            </p>
        </div>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shadow-2xs">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">

        <!-- Nama Pelanggaran -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Nama Pelanggaran <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_pelanggaran"
                value="{{ $referensiPelanggaran->nama_pelanggaran ?? old('nama_pelanggaran') }}"
                placeholder="Contoh: Terlambat masuk kelas" required
                class="m3-input-glass w-full text-xs font-bold">
        </div>

        <!-- Kategori & Poin Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <!-- Kategori -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <div class="relative group/select">
                    <select name="kategori" class="m3-input-glass w-full !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        @php $selectedKategori = $referensiPelanggaran->kategori ?? old('kategori', 'Ringan'); @endphp
                        <option value="Ringan" {{ $selectedKategori == 'Ringan' ? 'selected' : '' }}>Ringan</option>
                        <option value="Sedang" {{ $selectedKategori == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="Berat" {{ $selectedKategori == 'Berat' ? 'selected' : '' }}>Berat</option>
                    </select>
                    <!-- Custom Chevron Icon -->
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>
            </div>

            <!-- Skor Poin -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Skor Poin <span class="text-rose-500">*</span>
                </label>
                <input type="number" step="0.1" name="poin"
                    value="{{ $referensiPelanggaran->poin ?? old('poin', 0) }}" placeholder="0" required
                    class="m3-input-glass w-full text-xs font-bold">
            </div>

        </div>

    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <!-- Tombol Submit -->
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-save2-fill mr-1.5"></i>
            {{ isset($referensiPelanggaran) ? 'Simpan Perubahan' : 'Simpan Baru' }}
        </button>
    </div>
</form>

