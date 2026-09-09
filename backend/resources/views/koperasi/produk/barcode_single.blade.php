<!-- Modal Cetak Barcode Produk Tunggal -->
<div class="relative z-10 flex flex-col max-h-[90vh]"
    x-data="{
        qty: {{ max(1, $produk->stok > 0 ? (int)$produk->stok : 10) }},
        layout: 'a4_3col',
        showHeader: true,
        showName: true,
        showPrice: true,
        showCode: true,
        showBorder: true,
        
        get printUrl() {
            let params = new URLSearchParams({
                produk_id: '{{ $produk->id }}',
                qty: this.qty,
                layout: this.layout,
                show_header: this.showHeader ? 1 : 0,
                show_name: this.showName ? 1 : 0,
                show_price: this.showPrice ? 1 : 0,
                show_code: this.showCode ? 1 : 0,
                show_border: this.showBorder ? 1 : 0,
                auto_print: 1
            });
            return `{{ route('koperasi.produk.barcode-sheet') }}?${params.toString()}`;
        },

        bukaCetak() {
            window.open(this.printUrl, '_blank');
        }
    }">

    <!-- Modal Header -->
    <div class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20 shrink-0">
                <i class="bi bi-upc-scan text-base"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                    Cetak Barcode SKU Produk
                </h3>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    Cetak stiker barcode untuk label harga & kemasan barang
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

        <!-- Info Produk & Live Preview Label Stiker -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
            
            <!-- Detail Singkat Produk -->
            <div class="sm:col-span-6 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                        {{ $produk->kategori?->nama_kategori ?? 'Umum' }}
                    </span>
                    <span class="text-[10px] font-bold text-zinc-400">
                        Stok: <strong class="text-zinc-700 dark:text-zinc-200">{{ $produk->stok }} {{ $produk->satuan }}</strong>
                    </span>
                </div>

                <h4 class="text-sm font-black text-zinc-900 dark:text-white leading-tight">
                    {{ $produk->nama_produk }}
                </h4>

                <div class="flex items-center gap-3 text-xs">
                    <span class="font-mono font-black text-purple-600 dark:text-purple-400">
                        SKU: {{ $produk->kode_produk }}
                    </span>
                    <span class="font-mono font-black text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Box Live Preview Barcode Stiker -->
            <div class="sm:col-span-6 flex flex-col items-center justify-center">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 mb-1.5 self-start">
                    Preview 1 Stiker Label:
                </span>

                <div class="w-full max-w-[240px] p-2.5 rounded-xl bg-white text-zinc-900 border-2 border-dashed border-zinc-300 shadow-sm flex flex-col items-center justify-center text-center select-none"
                    :class="{ 'border-zinc-300': showBorder, 'border-transparent': !showBorder }">
                    
                    <!-- Header Koperasi -->
                    <div x-show="showHeader" class="text-[9px] font-extrabold uppercase tracking-tight text-zinc-600 leading-tight">
                        KOPERASI MDT HIDAYATUS SHIBYAN
                    </div>

                    <!-- Nama Produk -->
                    <div x-show="showName" class="text-[11px] font-black text-zinc-900 leading-tight mt-0.5 line-clamp-1 max-w-full">
                        {{ $produk->nama_produk }}
                    </div>

                    <!-- Barcode SVG -->
                    <div class="w-full h-9 flex items-center justify-center my-1 px-1">
                        {!! $barcodeSvg !!}
                    </div>

                    <!-- SKU & Harga -->
                    <div class="w-full flex items-center justify-between text-[10px] font-bold border-t border-zinc-200 pt-0.5">
                        <span x-show="showCode" class="font-mono font-bold text-zinc-700">
                            {{ $produk->kode_produk }}
                        </span>
                        <span x-show="showPrice" class="font-black text-emerald-700 ml-auto font-mono">
                            Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PENGATURAN CETAK (M3 Card) -->
        <div class="p-4 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/50 border border-zinc-200/80 dark:border-zinc-800/80 space-y-3.5">
            
            <!-- Jumlah Lembar Stiker -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between ml-1">
                    <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        Jumlah Label Stiker yang Dicetak
                    </label>
                    <span class="text-[10px] text-zinc-400">
                        Total: <strong class="text-purple-600 dark:text-purple-400" x-text="qty"></strong> stiker
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <input type="number" min="1" max="500" x-model.number="qty"
                        class="m3-input-glass w-28 text-center text-xs font-mono font-bold">

                    <!-- Quick Qty Chips -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" @click="qty = 5"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-purple-50 hover:text-purple-600 transition-all active:scale-95">
                            5
                        </button>
                        <button type="button" @click="qty = 10"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-purple-50 hover:text-purple-600 transition-all active:scale-95">
                            10
                        </button>
                        <button type="button" @click="qty = 24"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-purple-50 hover:text-purple-600 transition-all active:scale-95">
                            24
                        </button>
                        <button type="button" @click="qty = 40"
                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-purple-50 hover:text-purple-600 transition-all active:scale-95">
                            40
                        </button>
                        @if($produk->stok > 0)
                            <button type="button" @click="qty = {{ (int)$produk->stok }}"
                                class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-all active:scale-95">
                                Sesuai Stok ({{ (int)$produk->stok }})
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pilihan Format Kertas / Stiker -->
            <div class="space-y-1.5">
                <label class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Pilih Format Kertas & Layout
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <!-- Option 1: A4 3 Kolom -->
                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition-all"
                        :class="layout === 'a4_3col' ? 'bg-purple-500/10 border-purple-500/30 text-purple-700 dark:text-purple-300 font-bold' : 'bg-white dark:bg-zinc-800/80 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300'">
                        <input type="radio" name="layout" value="a4_3col" x-model="layout" class="text-purple-600 focus:ring-purple-500">
                        <div class="text-xs leading-tight">
                            <div>Kertas A4 - 3 Kolom (Grid 3x10)</div>
                            <span class="text-[10px] text-zinc-400">Standar Stiker Tom & Jerry 108 / 103</span>
                        </div>
                    </label>

                    <!-- Option 2: A4 4 Kolom -->
                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition-all"
                        :class="layout === 'a4_4col' ? 'bg-purple-500/10 border-purple-500/30 text-purple-700 dark:text-purple-300 font-bold' : 'bg-white dark:bg-zinc-800/80 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300'">
                        <input type="radio" name="layout" value="a4_4col" x-model="layout" class="text-purple-600 focus:ring-purple-500">
                        <div class="text-xs leading-tight">
                            <div>Kertas A4 - 4 Kolom (Grid 4x10)</div>
                            <span class="text-[10px] text-zinc-400">Ukuran Sedang (40 label / lembar)</span>
                        </div>
                    </label>

                    <!-- Option 3: A4 5 Kolom -->
                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition-all"
                        :class="layout === 'a4_5col' ? 'bg-purple-500/10 border-purple-500/30 text-purple-700 dark:text-purple-300 font-bold' : 'bg-white dark:bg-zinc-800/80 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300'">
                        <input type="radio" name="layout" value="a4_5col" x-model="layout" class="text-purple-600 focus:ring-purple-500">
                        <div class="text-xs leading-tight">
                            <div>Kertas A4 - 5 Kolom (Grid 5x13)</div>
                            <span class="text-[10px] text-zinc-400">Ukuran Mini (65 label / lembar)</span>
                        </div>
                    </label>

                    <!-- Option 4: Stiker Thermal Roll -->
                    <label class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition-all"
                        :class="layout === 'thermal' ? 'bg-purple-500/10 border-purple-500/30 text-purple-700 dark:text-purple-300 font-bold' : 'bg-white dark:bg-zinc-800/80 border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300'">
                        <input type="radio" name="layout" value="thermal" x-model="layout" class="text-purple-600 focus:ring-purple-500">
                        <div class="text-xs leading-tight">
                            <div>Stiker Thermal Roll (50x30 mm)</div>
                            <span class="text-[10px] text-zinc-400">Printer Barcode / Kasir Roll</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Checklist Elemen yang Ditampilkan -->
            <div class="pt-2 border-t border-zinc-200/60 dark:border-zinc-800/60 flex items-center gap-4 flex-wrap text-xs">
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                    <input type="checkbox" x-model="showHeader" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                    <span class="text-zinc-600 dark:text-zinc-400 font-semibold">Nama Koperasi</span>
                </label>
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                    <input type="checkbox" x-model="showName" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                    <span class="text-zinc-600 dark:text-zinc-400 font-semibold">Nama Produk</span>
                </label>
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                    <input type="checkbox" x-model="showPrice" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                    <span class="text-zinc-600 dark:text-zinc-400 font-semibold">Harga Jual</span>
                </label>
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                    <input type="checkbox" x-model="showCode" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                    <span class="text-zinc-600 dark:text-zinc-400 font-semibold">Teks Barcode</span>
                </label>
                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                    <input type="checkbox" x-model="showBorder" class="rounded text-purple-600 focus:ring-purple-500 w-3.5 h-3.5">
                    <span class="text-zinc-600 dark:text-zinc-400 font-semibold">Garis Batas</span>
                </label>
            </div>
        </div>

    </div>

    <!-- Modal Footer -->
    <div class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="button" @click="bukaCetak()"
            class="m3-btn-primary w-full sm:w-auto bg-purple-600 hover:bg-purple-700">
            <i class="bi bi-printer-fill text-sm"></i>
            <span>Cetak <span x-text="qty"></span> Stiker Barcode</span>
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="m3-btn-secondary w-full sm:w-auto mt-2 sm:mt-0">
            Tutup
        </button>
    </div>
</div>