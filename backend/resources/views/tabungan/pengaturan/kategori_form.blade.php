<!-- Modal Form Kategori Penarikan Tabungan -->
<form
    action="{{ isset($kategori) ? route('tabungan.pengaturan.kategori.update', $kategori->id) : route('tabungan.pengaturan.kategori.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($kategori))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">
                <i class="bi bi-tag-fill"></i>
            </div>
            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                {{ isset($kategori) ? 'Edit Kategori Penarikan' : 'Tambah Kategori Penarikan' }}
            </h3>
        </div>
        <!-- Touch Target 40px -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1 space-y-4 text-xs">

        <!-- Nama Kategori & Kode Kategori -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Kategori Penarikan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_kategori"
                    value="{{ $kategori->nama_kategori ?? old('nama_kategori') }}"
                    placeholder="Contoh: Pembayaran SPP Madrasah" required
                    class="m3-input-glass w-full text-xs font-bold">
            </div>

            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kode Kategori (Opsional)
                </label>
                <input type="text" name="kode_kategori"
                    value="{{ $kategori->kode_kategori ?? old('kode_kategori') }}" placeholder="Contoh: TAGIHAN_SPP"
                    class="m3-input-glass w-full text-xs font-mono uppercase">
            </div>
        </div>

        <!-- Jenis Tujuan & Urutan Tampilan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Jenis / Tujuan Penarikan <span class="text-rose-500">*</span>
                </label>
                <select name="jenis_tujuan" required class="m3-input-glass w-full text-xs font-bold">
                    <option value="Tunai"
                        {{ isset($kategori) && $kategori->jenis_tujuan == 'Tunai' ? 'selected' : '' }}>
                        💵 Penarikan Tunai Mandiri (Uang Saku / Tunai)
                    </option>
                    <option value="Tagihan"
                        {{ isset($kategori) && $kategori->jenis_tujuan == 'Tagihan' ? 'selected' : '' }}>
                        📋 Pembayaran Tagihan Madrasah (SPP / Kitab / Gedung)
                    </option>
                    <option value="Kas Ruangan"
                        {{ isset($kategori) && $kategori->jenis_tujuan == 'Kas Ruangan' ? 'selected' : '' }}>
                        🏫 Pembayaran Kas Ruangan / Kelas
                    </option>
                    <option value="Lainnya"
                        {{ isset($kategori) && $kategori->jenis_tujuan == 'Lainnya' ? 'selected' : '' }}>
                        📦 Keperluan Lainnya (Kebutuhan Murid / Pembagian Akhir)
                    </option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nomor Urutan Tampilan
                </label>
                <input type="number" name="urutan" min="0" value="{{ $kategori->urutan ?? old('urutan', 1) }}"
                    placeholder="1" class="m3-input-glass w-full text-xs font-mono font-bold">
            </div>
        </div>

        <!-- Keterangan -->
        <div class="space-y-1.5">
            <label
                class="block text-[11px] font-extrabold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Keterangan / Deskripsi
            </label>
            <textarea name="keterangan" rows="2.5"
                placeholder="Penjelasan singkat mengenai peruntukan kategori penarikan ini..."
                class="m3-input-glass w-full text-xs">{{ $kategori->keterangan ?? old('keterangan') }}</textarea>
        </div>

        <!-- Status Aktif Checkbox -->
        <div class="pt-2">
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input type="checkbox" name="is_active" value="1"
                    {{ !isset($kategori) || $kategori->is_active ? 'checked' : '' }}
                    class="w-4 h-4 rounded text-primary focus:ring-primary/20 border-zinc-300 dark:border-zinc-700">
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                    Aktifkan Kategori Penarikan Ini
                </span>
            </label>
            <span class="text-[10px] text-zinc-400 ml-6 block mt-0.5">
                Kategori yang aktif akan muncul di pilihan saat petugas memproses penarikan saldo nasabah.
            </span>
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
            <span>{{ isset($kategori) ? 'Simpan Perubahan' : 'Simpan Kategori' }}</span>
        </button>
    </div>
</form>
