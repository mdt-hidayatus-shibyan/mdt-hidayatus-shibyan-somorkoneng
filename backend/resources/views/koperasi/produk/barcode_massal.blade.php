@section('title', 'Cetak Barcode SKU Massal')
<x-app-layout>

    <div class="space-y-6"
        x-data="{
            search: '',
            kategoriId: '',
            layout: 'a4_3col',
            showHeader: true,
            showName: true,
            showPrice: true,
            showCode: true,
            showBorder: true,
            
            items: [
                @foreach($produks as $p)
                {
                    id: {{ $p->id }},
                    kode: '{{ $p->kode_produk }}',
                    nama: '{{ addslashes($p->nama_produk) }}',
                    kategori_id: '{{ $p->kategori_id }}',
                    kategori_nama: '{{ $p->kategori?->nama_kategori ?? 'Umum' }}',
                    harga: {{ (int)$p->harga_jual }},
                    stok: {{ (int)$p->stok }},
                    satuan: '{{ $p->satuan }}',
                    selected: false,
                    qty: {{ max(1, (int)$p->stok > 0 ? (int)$p->stok : 5) }}
                },
                @endforeach
            ],

            get filteredItems() {
                return this.items.filter(item => {
                    const matchSearch = !this.search || 
                        item.nama.toLowerCase().includes(this.search.toLowerCase()) || 
                        item.kode.toLowerCase().includes(this.search.toLowerCase());
                    const matchKategori = !this.kategoriId || item.kategori_id == this.kategoriId;
                    return matchSearch && matchKategori;
                });
            },

            get selectedItems() {
                return this.items.filter(item => item.selected && item.qty > 0);
            },

            get totalSelectedCount() {
                return this.selectedItems.length;
            },

            get totalStickerCount() {
                return this.selectedItems.reduce((sum, item) => sum + (Number(item.qty) || 0), 0);
            },

            get isAllFilteredSelected() {
                let filtered = this.filteredItems;
                return filtered.length > 0 && filtered.every(i => i.selected);
            },

            toggleSelectAll() {
                let current = this.isAllFilteredSelected;
                this.filteredItems.forEach(i => i.selected = !current);
            },

            pilihAdaStok() {
                this.items.forEach(i => {
                    if (i.stok > 0) {
                        i.selected = true;
                        i.qty = i.stok;
                    } else {
                        i.selected = false;
                    }
                });
            },

            setSemuaQty(val) {
                this.selectedItems.forEach(i => i.qty = val);
            },

            resetSemuaQtyKeStok() {
                this.selectedItems.forEach(i => i.qty = Math.max(1, i.stok));
            },

            batalPilih() {
                this.items.forEach(i => i.selected = false);
            },

            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
            }
        }">

        <!-- Header Page & Navigation -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 relative z-10">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20 shrink-0">
                        <i class="bi bi-upc-scan text-lg"></i>
                    </div>
                    <span>Cetak Barcode SKU Massal</span>
                </h2>
                <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Pilih multi-produk dan cetak lembaran label stiker barcode sekaligus.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('koperasi.produk.index') }}"
                    class="m3-btn-secondary text-xs">
                    <i class="bi bi-arrow-left text-sm text-primary"></i>
                    <span>Kembali ke Master Produk</span>
                </a>
            </div>
        </div>

        <form action="{{ route('koperasi.produk.barcode-sheet') }}" method="GET" target="_blank" id="formBarcodeBatch"
            class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Hidden parameters passed to sheet -->
            <input type="hidden" name="layout" :value="layout">
            <input type="hidden" name="show_header" :value="showHeader ? 1 : 0">
            <input type="hidden" name="show_name" :value="showName ? 1 : 0">
            <input type="hidden" name="show_price" :value="showPrice ? 1 : 0">
            <input type="hidden" name="show_code" :value="showCode ? 1 : 0">
            <input type="hidden" name="show_border" :value="showBorder ? 1 : 0">
            <input type="hidden" name="auto_print" value="1">

            <!-- Dynamic selected inputs generated by Alpine -->
            <template x-for="item in selectedItems" :key="item.id">
                <input type="hidden" :name="'items[' + item.id + '][qty]'" :value="item.qty">
            </template>

            <!-- LEFT COLUMN: DAFTAR PRODUK & PILIHAN -->
            <div class="lg:col-span-8 space-y-4">
                
                <!-- Main Glass Card: Filter & Quick Action Toolbar -->
                <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden shadow-2xs">
                    
                    <!-- Search & Filter Controls -->
                    <div class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                        
                        <!-- Search Box -->
                        <div class="relative w-full sm:w-72">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                            <input type="text" x-model="search" placeholder="Cari nama atau barcode SKU..."
                                class="m3-input-glass w-full !pl-8 text-xs font-semibold">
                        </div>

                        <!-- Filter Kategori -->
                        <div class="w-full sm:w-52">
                            <select x-model="kategoriId" class="m3-input-glass w-full text-xs font-bold">
                                <option value="">Semua Kategori ({{ count($produks) }})</option>
                                @foreach($kategoris as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Quick Action Chips -->
                    <div class="p-3 bg-zinc-100/60 dark:bg-zinc-900/60 border-b border-zinc-200/60 dark:border-zinc-800/60 flex items-center justify-between gap-2 flex-wrap text-xs">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" @click="toggleSelectAll()"
                                class="px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 hover:bg-purple-50 hover:text-purple-700 transition-all active:scale-95 shadow-2xs">
                                <span x-text="isAllFilteredSelected ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                            </button>

                            <button type="button" @click="pilihAdaStok()"
                                class="px-2.5 py-1 rounded-lg font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-all active:scale-95 shadow-2xs">
                                <i class="bi bi-check2-all"></i>
                                <span>Pilih yang Ada Stok</span>
                            </button>

                            <button type="button" @click="resetSemuaQtyKeStok()" x-show="totalSelectedCount > 0"
                                class="px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 transition-all active:scale-95 shadow-2xs">
                                Qty = Stok
                            </button>

                            <button type="button" @click="setSemuaQty(5)" x-show="totalSelectedCount > 0"
                                class="px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 transition-all active:scale-95 shadow-2xs">
                                Set 5 Lembar
                            </button>

                            <button type="button" @click="setSemuaQty(10)" x-show="totalSelectedCount > 0"
                                class="px-2.5 py-1 rounded-lg font-bold bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 transition-all active:scale-95 shadow-2xs">
                                Set 10 Lembar
                            </button>
                        </div>

                        <button type="button" @click="batalPilih()" x-show="totalSelectedCount > 0"
                            class="text-rose-500 hover:text-rose-600 font-bold text-[11px] underline">
                            Reset Pilihan
                        </button>
                    </div>

                    <!-- Table List Produk -->
                    <div class="overflow-x-auto">
                        <table class="m3-table w-full">
                            <thead>
                                <tr>
                                    <th class="w-10 text-center">
                                        <input type="checkbox" @change="toggleSelectAll()" :checked="isAllFilteredSelected"
                                            class="rounded text-purple-600 focus:ring-purple-500 w-4 h-4 cursor-pointer">
                                    </th>
                                    <th>Barcode / SKU</th>
                                    <th>Nama Produk & Kategori</th>
                                    <th class="text-right">Harga Jual</th>
                                    <th class="text-center w-24">Stok</th>
                                    <th class="text-center w-36">Jml Cetak (Stiker)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in filteredItems" :key="item.id">
                                    <tr class="transition-colors cursor-pointer"
                                        :class="item.selected ? 'bg-purple-500/5 dark:bg-purple-950/20' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/40'"
                                        @click="if ($event.target.tagName !== 'INPUT' && $event.target.tagName !== 'BUTTON') item.selected = !item.selected">
                                        
                                        <!-- Checkbox -->
                                        <td class="text-center" @click.stop>
                                            <input type="checkbox" x-model="item.selected"
                                                class="rounded text-purple-600 focus:ring-purple-500 w-4 h-4 cursor-pointer">
                                        </td>

                                        <!-- SKU -->
                                        <td class="font-mono font-bold text-zinc-900 dark:text-white text-xs">
                                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700"
                                                x-text="item.kode"></span>
                                        </td>

                                        <!-- Nama & Kategori -->
                                        <td>
                                            <div class="font-bold text-zinc-900 dark:text-white text-xs" x-text="item.nama"></div>
                                            <span class="text-[10px] text-zinc-400 font-semibold" x-text="item.kategori_nama"></span>
                                        </td>

                                        <!-- Harga Jual -->
                                        <td class="text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-xs">
                                            Rp <span x-text="formatRupiah(item.harga)"></span>
                                        </td>

                                        <!-- Stok -->
                                        <td class="text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black"
                                                :class="item.stok > 0 ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600'">
                                                <span x-text="item.stok"></span> <span x-text="item.satuan"></span>
                                            </span>
                                        </td>

                                        <!-- Jumlah Lembar Stiker -->
                                        <td class="text-center" @click.stop>
                                            <div class="flex items-center justify-center gap-1">
                                                <button type="button" @click="if (item.qty > 1) item.qty--"
                                                    class="w-6 h-6 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center font-black text-xs hover:bg-zinc-200">
                                                    -
                                                </button>
                                                <input type="number" min="1" max="500" x-model.number="item.qty"
                                                    @focus="item.selected = true"
                                                    class="w-14 text-center text-xs font-mono font-black p-1 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100">
                                                <button type="button" @click="item.qty++; item.selected = true"
                                                    class="w-6 h-6 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 flex items-center justify-center font-black text-xs hover:bg-zinc-200">
                                                    +
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="filteredItems.length === 0">
                                    <tr>
                                        <td colspan="6" class="text-center py-12 text-zinc-400 text-xs font-semibold">
                                            <i class="bi bi-search text-3xl block mb-2 opacity-50"></i>
                                            Tidak ada produk yang cocok dengan pencarian.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: PENGATURAN KERTAS & TOMBOL CETAK (Sticky) -->
            <div class="lg:col-span-4 sticky top-20 space-y-4">
                
                <!-- SUMMARY & ACTION CARD -->
                <div class="m3-glass-card p-5 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/90 dark:bg-zinc-900/90 space-y-4 shadow-sm">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200/80 dark:border-zinc-800/80">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                                <i class="bi bi-printer-fill text-sm"></i>
                            </div>
                            <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                                Ringkasan Cetak
                            </h3>
                        </div>
                    </div>

                    <!-- Metrics Badges -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="p-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 text-center">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Produk Dipilih</span>
                            <span class="text-lg font-black text-purple-600 dark:text-purple-400 font-mono" x-text="totalSelectedCount"></span>
                            <span class="text-[10px] text-zinc-400 block">Item</span>
                        </div>
                        <div class="p-3 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60 text-center">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Total Stiker</span>
                            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 font-mono" x-text="totalStickerCount"></span>
                            <span class="text-[10px] text-zinc-400 block">Lembar Label</span>
                        </div>
                    </div>

                    <!-- Pilihan Layout Kertas -->
                    <div class="space-y-2">
                        <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Pilih Format & Kertas Stiker
                        </label>

                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2.5 p-2 rounded-xl border cursor-pointer transition-all text-xs"
                                :class="layout === 'a4_3col' ? 'bg-purple-500/10 border-purple-500/30 font-bold text-purple-700 dark:text-purple-300' : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300'">
                                <input type="radio" value="a4_3col" x-model="layout" class="text-purple-600 focus:ring-purple-500">
                                <div>
                                    <div>Kertas A4 - 3 Kolom (3x10 / 30 label)</div>
                                    <span class="text-[10px] text-zinc-400">Tom & Jerry 108 / 103</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-2 rounded-xl border cursor-pointer transition-all text-xs"
                                :class="layout === 'a4_4col' ? 'bg-purple-500/10 border-purple-500/30 font-bold text-purple-700 dark:text-purple-300' : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300'">
                                <input type="radio" value="a4_4col" x-model="layout" class="text-purple-600 focus:ring-purple-500">
                                <div>
                                    <div>Kertas A4 - 4 Kolom (4x10 / 40 label)</div>
                                    <span class="text-[10px] text-zinc-400">Ukuran Sedang (4.8 x 2.5 cm)</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-2 rounded-xl border cursor-pointer transition-all text-xs"
                                :class="layout === 'a4_5col' ? 'bg-purple-500/10 border-purple-500/30 font-bold text-purple-700 dark:text-purple-300' : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300'">
                                <input type="radio" value="a4_5col" x-model="layout" class="text-purple-600 focus:ring-purple-500">
                                <div>
                                    <div>Kertas A4 - 5 Kolom (5x13 / 65 label)</div>
                                    <span class="text-[10px] text-zinc-400">Ukuran Mini (3.8 x 2.1 cm)</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-2 rounded-xl border cursor-pointer transition-all text-xs"
                                :class="layout === 'thermal' ? 'bg-purple-500/10 border-purple-500/30 font-bold text-purple-700 dark:text-purple-300' : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300'">
                                <input type="radio" value="thermal" x-model="layout" class="text-purple-600 focus:ring-purple-500">
                                <div>
                                    <div>Stiker Thermal Roll (50x30 mm)</div>
                                    <span class="text-[10px] text-zinc-400">Printer Barcode Roll Kasir</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Elemen Stiker Checklist -->
                    <div class="space-y-1.5 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/60">
                        <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            Elemen Label Stiker
                        </label>
                        <div class="space-y-1 text-xs">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" x-model="showHeader" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                                <span class="text-zinc-700 dark:text-zinc-300">Nama Koperasi Madrasah</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" x-model="showName" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                                <span class="text-zinc-700 dark:text-zinc-300">Nama Produk & Kitab</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" x-model="showPrice" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                                <span class="text-zinc-700 dark:text-zinc-300">Harga Jual Produk</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" x-model="showCode" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                                <span class="text-zinc-700 dark:text-zinc-300">Teks Kode Barcode / SKU</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" x-model="showBorder" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                                <span class="text-zinc-700 dark:text-zinc-300">Garis Batas (Potong Stiker)</span>
                            </label>
                        </div>
                    </div>

                    <!-- TOMBOL CETAK LEMBAR BARCODE -->
                    <div class="pt-2">
                        <button type="submit" :disabled="totalSelectedCount === 0"
                            :class="totalSelectedCount === 0 ? 'opacity-50 cursor-not-allowed bg-zinc-400' : 'bg-purple-600 hover:bg-purple-700 active:scale-95 shadow-md'"
                            class="w-full min-h-[44px] px-4 py-2.5 rounded-xl md:rounded-2xl text-white font-extrabold text-xs flex items-center justify-center gap-2 transition-all">
                            <i class="bi bi-printer-fill text-base"></i>
                            <span>Buka & Cetak (<span x-text="totalStickerCount"></span> Label)</span>
                        </button>
                        <p class="text-[10px] text-zinc-400 text-center mt-1.5">
                            Akan membuka jendela baru siap print / simpan PDF
                        </p>
                    </div>

                </div>
            </div>

        </form>

    </div>

</x-app-layout>