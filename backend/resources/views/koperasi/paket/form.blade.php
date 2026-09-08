<!-- Modal Form Paket Bundling -->
<form action="{{ isset($paket) ? route('koperasi.paket.update', $paket->id) : route('koperasi.paket.store') }}"
    method="POST" enctype="multipart/form-data" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($paket))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20 shrink-0">
                <i class="bi {{ isset($paket) ? 'bi-pencil-square' : 'bi-collection-fill' }} text-base"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ isset($paket) ? 'Edit Paket Bundling' : 'Buat Paket Bundling Baru' }}
                </h3>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    Bundling beberapa kitab/barang menjadi 1 paket per level/kelas
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
            <!-- Kode Barcode Paket -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kode Barcode Paket
                </label>
                <input type="text" name="kode_paket"
                    value="{{ old('kode_paket', $paket->kode_paket ?? ($generatedBarcode ?? '')) }}"
                    placeholder="Otomatis jika kosong..." class="m3-input-glass w-full text-xs font-mono font-bold">
            </div>

            <!-- Level / Kelas Peruntukan -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Peruntukan Level / Kelas
                </label>
                <select name="level_id" class="m3-input-glass w-full text-xs font-bold">
                    <option value="">-- Semua Kelas / Umum --</option>
                    @foreach ($levels as $lvl)
                        <option value="{{ $lvl->id }}"
                            {{ old('level_id', $paket->level_id ?? '') == $lvl->id ? 'selected' : '' }}>
                            {{ $lvl->nama_level }} ({{ $lvl->tingkat?->nama_tingkat ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Nama Paket -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Nama Paket Bundling <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_paket" required
                value="{{ old('nama_paket', $paket->nama_paket ?? '') }}"
                placeholder="Contoh: Paket Kitab Lengkap Kelas 5 Ibtidaiyah..."
                class="m3-input-glass w-full text-xs font-bold">
        </div>

        <!-- Upload Foto Paket -->
        <div class="space-y-1.5" x-data="{ imagePreview: '{{ isset($paket) && $paket->foto_url ? $paket->foto_url : '' }}' }">
            <div class="flex items-center justify-between ml-1">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                    Foto Cover Paket
                </label>
                <span class="text-[10px] text-zinc-400">JPG, PNG, WEBP (Maks. 2MB)</span>
            </div>
            <div class="flex items-center gap-3.5">
                <!-- Preview Box -->
                <div
                    class="w-16 h-16 rounded-2xl border-2 border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/60 flex items-center justify-center overflow-hidden shrink-0 relative shadow-2xs">
                    <template x-if="imagePreview">
                        <img :src="imagePreview" alt="Preview Paket" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!imagePreview">
                        <div class="text-center text-zinc-400">
                            <i class="bi bi-collection text-xl block leading-none mb-0.5 opacity-50"></i>
                            <span class="text-[8px] font-bold block leading-tight">No Foto</span>
                        </div>
                    </template>
                </div>

                <div class="flex-1 space-y-1">
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp,image/jpg"
                        @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }"
                        class="block w-full text-xs text-zinc-500 dark:text-zinc-400 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-500/10 file:text-indigo-700 dark:file:text-indigo-400 hover:file:bg-indigo-500/20 file:cursor-pointer transition-all">
                    <p class="text-[10px] text-zinc-400">Foto memudahkan identifikasi paket di kasir POS.</p>
                </div>
            </div>
        </div>

        <!-- Harga Jual Paket & Status -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Harga Jual Paket (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-emerald-600">Rp</span>
                    <input type="number" step="500" name="harga_paket" required
                        value="{{ old('harga_paket', $paket->harga_paket ?? 0) }}"
                        class="m3-input-glass w-full !pl-9 text-xs font-mono font-black text-emerald-600 dark:text-emerald-400">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Status Paket <span class="text-rose-500">*</span>
                </label>
                <select name="is_active" required class="m3-input-glass w-full text-xs font-bold">
                    <option value="1" {{ old('is_active', $paket->is_active ?? true) ? 'selected' : '' }}>
                        Aktif (Tersedia di Kasir)
                    </option>
                    <option value="0" {{ old('is_active', $paket->is_active ?? true) ? '' : 'selected' }}>
                        Nonaktif
                    </option>
                </select>
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="space-y-1.5">
            <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Deskripsi / Catatan Paket
            </label>
            <textarea name="deskripsi" rows="2" placeholder="Catatan peruntukan paket..."
                class="m3-input-glass w-full text-xs font-medium py-2.5">{{ old('deskripsi', $paket->deskripsi ?? '') }}</textarea>
        </div>

        <!-- ======================================================== -->
        <!-- BUILDER KOMPONEN PRODUK DALAM PAKET                      -->
        <!-- ======================================================== -->
        <div class="pt-3 border-t border-zinc-200/80 dark:border-zinc-800 space-y-3"
            x-data="{
                items: {{ json_encode(isset($paket) ? $paket->items->map(fn($it) => ['produk_id' => $it->produk_id, 'jumlah' => $it->jumlah]) : [['produk_id' => '', 'jumlah' => 1]]) }},
                addItem() { this.items.push({ produk_id: '', jumlah: 1 }); },
                removeItem(idx) { if (this.items.length > 1) this.items.splice(idx, 1); }
            }">
            
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-box-seam-fill text-indigo-600"></i>
                        <span>Rincian Komponen Kitab / Barang</span>
                    </h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Pilih produk dan tentukan jumlah item untuk 1 paket ini.</p>
                </div>

                <button type="button" @click="addItem()"
                    class="min-h-[34px] px-3 py-1 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 active:scale-95">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Item</span>
                </button>
            </div>

            <!-- Items List -->
            <div class="space-y-2">
                <template x-for="(item, index) in items" :key="'item-' + index">
                    <div class="p-3 rounded-2xl bg-zinc-50/80 dark:bg-zinc-900/80 border border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center gap-2.5">
                        <span class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-xs font-black flex items-center justify-center shrink-0"
                            x-text="index + 1"></span>

                        <!-- Select Produk -->
                        <div class="flex-1 w-full">
                            <select :name="'items[' + index + '][produk_id]'" x-model="item.produk_id" required
                                class="m3-input-glass w-full text-xs font-bold">
                                <option value="">-- Pilih Produk / Kitab --</option>
                                @foreach ($produks as $prod)
                                    <option value="{{ $prod->id }}">
                                        {{ $prod->nama_produk }} ({{ $prod->kode_produk }}) - Stok: {{ $prod->stok }} {{ $prod->satuan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jumlah Qty -->
                        <div class="w-full sm:w-24 shrink-0 flex items-center gap-2">
                            <input type="number" min="1" :name="'items[' + index + '][jumlah]'"
                                x-model.number="item.jumlah" required placeholder="Qty"
                                class="m3-input-glass w-full text-xs font-bold text-center">
                        </div>

                        <!-- Tombol Hapus -->
                        <button type="button" @click="removeItem(index)" :disabled="items.length <= 1"
                            class="w-8 h-8 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition-all shrink-0">
                            <i class="bi bi-trash text-sm"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($paket) ? 'Simpan Perubahan Paket' : 'Buat Paket Bundling' }}</span>
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="m3-btn-secondary w-full sm:w-auto mt-2 sm:mt-0">
            Batal
        </button>
    </div>
</form>
