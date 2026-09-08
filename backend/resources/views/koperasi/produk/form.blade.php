<!-- Modal Form Produk Koperasi -->
<form action="{{ isset($produk) ? route('koperasi.produk.update', $produk->id) : route('koperasi.produk.store') }}"
    method="POST" enctype="multipart/form-data" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($produk))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0">
                <i class="bi {{ isset($produk) ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} text-base"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ isset($produk) ? 'Edit Produk Koperasi' : 'Tambah Produk Koperasi' }}
                </h3>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    Katalog kitab, seragam, atribut, alat tulis, dan stok barang
                </p>
            </div>
        </div>

        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body (Scrollable) -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1 space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Kategori Produk -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <select name="kategori_id" required class="m3-input-glass w-full text-xs font-bold">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id }}"
                            {{ (isset($produk) && $produk->kategori_id == $k->id) || old('kategori_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Kode Barcode / SKU -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kode Barcode / SKU
                </label>
                <input type="text" name="kode_produk"
                    value="{{ old('kode_produk', $produk->kode_produk ?? ($generatedBarcode ?? '')) }}"
                    placeholder="Otomatis jika dikosongkan..." class="m3-input-glass w-full text-xs font-mono font-bold">
            </div>
        </div>

        <!-- Nama Produk -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Nama Produk / Kitab <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_produk" required
                value="{{ old('nama_produk', $produk->nama_produk ?? '') }}"
                placeholder="Contoh: Kitab Fathul Qorib, Seragam Putih Hijau, Buku Pegon..."
                class="m3-input-glass w-full text-xs font-bold">
        </div>

        <!-- Upload Foto Produk -->
        <div class="space-y-1.5" x-data="{ imagePreview: '{{ isset($produk) && $produk->foto_url ? $produk->foto_url : '' }}' }">
            <div class="flex items-center justify-between ml-1">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                    Foto Produk / Cover Kitab
                </label>
                <span class="text-[10px] text-zinc-400">JPG, PNG, WEBP (Maks. 2MB)</span>
            </div>
            <div class="flex items-center gap-3.5">
                <!-- Preview Box -->
                <div
                    class="w-16 h-16 rounded-2xl border-2 border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/60 flex items-center justify-center overflow-hidden shrink-0 relative shadow-2xs">
                    <template x-if="imagePreview">
                        <img :src="imagePreview" alt="Preview Foto" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!imagePreview">
                        <div class="text-center text-zinc-400">
                            <i class="bi bi-image text-xl block leading-none mb-0.5 opacity-50"></i>
                            <span class="text-[8px] font-bold block leading-tight">No Foto</span>
                        </div>
                    </template>
                </div>

                <div class="flex-1 space-y-1">
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp,image/jpg"
                        @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }"
                        class="block w-full text-xs text-zinc-500 dark:text-zinc-400 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-500/10 file:text-emerald-700 dark:file:text-emerald-400 hover:file:bg-emerald-500/20 file:cursor-pointer transition-all">
                    <p class="text-[10px] text-zinc-400">Ditampilkan pada katalog grid Kasir POS Koperasi.</p>
                </div>
            </div>
        </div>

        <!-- Satuan, Harga Beli, Harga Jual -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <!-- Satuan -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Satuan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="satuan" required
                    value="{{ old('satuan', $produk->satuan ?? 'Pcs') }}" placeholder="Pcs, Stel..."
                    class="m3-input-glass w-full text-xs font-bold">
            </div>

            <!-- Harga Beli / Modal -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Harga Modal (HPP) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-zinc-400">Rp</span>
                    <input type="number" name="harga_beli" required min="0"
                        value="{{ old('harga_beli', $produk->harga_beli ?? 0) }}"
                        class="m3-input-glass w-full !pl-9 text-xs font-bold font-mono">
                </div>
            </div>

            <!-- Harga Jual -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Harga Jual <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-emerald-600">Rp</span>
                    <input type="number" name="harga_jual" required min="0"
                        value="{{ old('harga_jual', $produk->harga_jual ?? 0) }}"
                        class="m3-input-glass w-full !pl-9 text-xs font-bold font-mono text-emerald-600 dark:text-emerald-400">
                </div>
            </div>
        </div>

        <!-- Stok Awal, Stok Minimum, Status -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <!-- Stok Awal -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    {{ isset($produk) ? 'Stok (Read Only)' : 'Stok Awal' }}
                </label>
                <input type="number" {{ isset($produk) ? 'disabled' : 'name=stok' }} min="0"
                    value="{{ old('stok', $produk->stok ?? 0) }}"
                    class="m3-input-glass w-full text-xs font-bold text-center {{ isset($produk) ? 'bg-zinc-100 dark:bg-zinc-800/60 opacity-70 cursor-not-allowed' : '' }}">
            </div>

            <!-- Stok Minimum -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Batas Alert Stok
                </label>
                <input type="number" name="stok_minimum"
                    value="{{ old('stok_minimum', $produk->stok_minimum ?? 5) }}"
                    class="m3-input-glass w-full text-xs font-bold text-center">
            </div>

            <!-- Status -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Status <span class="text-rose-500">*</span>
                </label>
                <select name="status" required class="m3-input-glass w-full text-xs font-bold">
                    <option value="Aktif" {{ old('status', $produk->status ?? 'Aktif') === 'Aktif' ? 'selected' : '' }}>
                        Aktif
                    </option>
                    <option value="Nonaktif" {{ old('status', $produk->status ?? 'Aktif') === 'Nonaktif' ? 'selected' : '' }}>
                        Nonaktif
                    </option>
                </select>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Keterangan (Opsional)
            </label>
            <textarea name="keterangan" rows="2" placeholder="Catatan spesifikasi produk..."
                class="m3-input-glass w-full text-xs font-medium py-2.5">{{ old('keterangan', $produk->keterangan ?? '') }}</textarea>
        </div>

    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($produk) ? 'Simpan Perubahan' : 'Simpan Produk' }}</span>
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="m3-btn-secondary w-full sm:w-auto mt-2 sm:mt-0">
            Batal
        </button>
    </div>
</form>
