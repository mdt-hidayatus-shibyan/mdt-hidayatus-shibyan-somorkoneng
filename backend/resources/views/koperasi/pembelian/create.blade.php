@section('title', 'Catat Pembelian (Kulakan) Baru')
<x-app-layout>

    <div class="max-w-6xl mx-auto mb-8" x-data="pembelianApp({{ json_encode($produks) }})">

        <!-- Top Header & Back Link -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-20">
            <div>
                <a href="{{ route('koperasi.pembelian.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors mb-2">
                    <i class="bi bi-arrow-left text-sm"></i>
                    <span>Kembali ke Riwayat Pembelian</span>
                </a>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0">
                        <i class="bi bi-bag-plus-fill text-lg"></i>
                    </div>
                    <span>Form Pembelian & Kulakan Barang</span>
                </h2>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-mono font-bold text-xs border border-zinc-200/80 dark:border-zinc-700/80">
                    No. Faktur: <strong class="text-emerald-600 dark:text-emerald-400">{{ $nomorFakturPreview }}</strong>
                </span>
            </div>
        </div>

        <!-- FORM UTAMA -->
        <form action="{{ route('koperasi.pembelian.store') }}" method="POST" enctype="multipart/form-data" id="formPembelian" class="space-y-6">
            @csrf

            <!-- CARD 1: INFORMASI FAKTUR & SUPPLIER -->
            <div class="m3-glass-card p-5 sm:p-7 shadow-sm dark:shadow-none space-y-5">
                <div class="flex items-center gap-2.5 pb-4 border-b border-zinc-200/80 dark:border-zinc-800/80">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-black text-sm">
                        1
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                            Informasi Faktur & Supplier
                        </h3>
                        <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                            Identitas toko grosir, tanggal faktur, dan bukti nota pembelian
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Tanggal Pembelian -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Tanggal & Waktu Kulakan <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="tanggal" value="{{ date('Y-m-d\TH:i') }}" required
                            class="m3-input-glass w-full text-xs font-semibold">
                    </div>

                    <!-- Nama Supplier / Toko Grosir -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Nama Toko / Supplier <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="supplier" required placeholder="Contoh: Toko Grosir Barokah / Agen Kitab"
                            class="m3-input-glass w-full text-xs font-semibold">
                    </div>

                    <!-- No. Faktur Asli Supplier -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                            No. Nota / Faktur Fisik Supplier
                        </label>
                        <input type="text" name="nomor_faktur_supplier" placeholder="Contoh: INV/2026/09/0182"
                            class="m3-input-glass w-full text-xs font-mono">
                    </div>

                    <!-- Upload Foto Nota Fisik -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Upload Foto Nota Fisik
                        </label>
                        <input type="file" name="foto_faktur" accept="image/*"
                            class="m3-input-glass w-full text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                    </div>
                </div>

                <!-- Toggle Otomatis Update Harga Jual/Beli -->
                <div class="pt-3 border-t border-zinc-200/60 dark:border-zinc-800/60 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <input type="checkbox" name="update_harga_produk" id="update_harga_produk" value="1" checked
                            class="w-4 h-4 rounded text-primary focus:ring-primary/20 cursor-pointer">
                        <label for="update_harga_produk" class="text-xs font-bold text-zinc-700 dark:text-zinc-300 cursor-pointer select-none">
                            Otomatis perbarui Harga Beli (HPP) & Target Harga Jual di master produk
                        </label>
                    </div>
                    <span class="text-[11px] text-zinc-400 font-semibold hidden sm:inline">
                        Stok produk akan otomatis bertambah saat disimpan
                    </span>
                </div>
            </div>

            <!-- CARD 2: DAFTAR BARANG YANG DIBELI (REPEATER) -->
            <div class="m3-glass-card p-5 sm:p-7 shadow-sm dark:shadow-none space-y-4">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 pb-4 border-b border-zinc-200/80 dark:border-zinc-800/80">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-black text-sm">
                            2
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                                Rincian Barang Kulakan
                            </h3>
                            <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                                Pilih produk madrasah, masukkan jumlah masuk dan harga beli grosir
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="addItem()"
                        class="m3-btn-secondary min-h-[36px] px-3.5 py-1.5 text-xs font-bold inline-flex items-center gap-1.5 active:scale-95 self-start sm:self-auto">
                        <i class="bi bi-plus-lg text-primary"></i>
                        <span>Tambah Baris Produk</span>
                    </button>
                </div>

                <!-- REPEATER TABLE -->
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left font-bold uppercase tracking-wider text-[10px] text-zinc-400 dark:text-zinc-500 border-b border-zinc-200/60 dark:border-zinc-800/60 pb-2">
                                <th class="pb-2 w-10 text-center">#</th>
                                <th class="pb-2 min-w-[220px]">Produk Koperasi</th>
                                <th class="pb-2 w-28 text-center">Stok Saat Ini</th>
                                <th class="pb-2 w-28 text-center">Jumlah Masuk</th>
                                <th class="pb-2 w-36 text-right">Harga Beli Satuan</th>
                                <th class="pb-2 w-36 text-right">Target Harga Jual</th>
                                <th class="pb-2 w-36 text-right">Subtotal</th>
                                <th class="pb-2 w-12 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200/40 dark:divide-zinc-800/40">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <!-- Index -->
                                    <td class="py-3 text-center font-bold text-zinc-400" x-text="index + 1"></td>

                                    <!-- Produk Select -->
                                    <td class="py-3 pr-2">
                                        <select :name="'items[' + index + '][produk_id]'" required
                                            x-model="item.produk_id"
                                            @change="onSelectProduct(index, $event)"
                                            class="m3-input-glass w-full text-xs font-bold">
                                            <option value="">-- Pilih Produk --</option>
                                            <template x-for="p in produksList" :key="p.id">
                                                <option :value="p.id" x-text="p.kode_produk + ' - ' + p.nama_produk + ' (' + p.satuan + ')'" :selected="p.id == item.produk_id"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <!-- Stok Saat Ini -->
                                    <td class="py-3 px-2 text-center font-semibold text-zinc-500 dark:text-zinc-400">
                                        <span x-text="item.stok_sekarang + ' ' + (item.satuan || '')"></span>
                                    </td>

                                    <!-- Jumlah Masuk (Qty) -->
                                    <td class="py-3 px-2">
                                        <input type="number" :name="'items[' + index + '][jumlah]'" required min="1" step="1"
                                            x-model.number="item.jumlah"
                                            @input="updateSubtotal(index)"
                                            class="m3-input-glass w-full text-center text-xs font-bold">
                                    </td>

                                    <!-- Harga Beli Satuan -->
                                    <td class="py-3 px-2">
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-zinc-400 font-bold">Rp</span>
                                            <input type="number" :name="'items[' + index + '][harga_beli]'" required min="0" step="100"
                                                x-model.number="item.harga_beli"
                                                @input="updateSubtotal(index)"
                                                class="m3-input-glass w-full pl-7 text-right text-xs font-bold font-mono">
                                        </div>
                                    </td>

                                    <!-- Target Harga Jual Satuan -->
                                    <td class="py-3 px-2">
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-zinc-400 font-bold">Rp</span>
                                            <input type="number" :name="'items[' + index + '][harga_jual]'" min="0" step="100"
                                                x-model.number="item.harga_jual"
                                                placeholder="Sama"
                                                class="m3-input-glass w-full pl-7 text-right text-xs font-bold font-mono">
                                        </div>
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="py-3 px-2 text-right font-mono font-black text-zinc-900 dark:text-white">
                                        <span x-text="'Rp ' + formatRupiah(item.subtotal)"></span>
                                    </td>

                                    <!-- Hapus Baris -->
                                    <td class="py-3 text-center">
                                        <button type="button" @click="removeItem(index)" :disabled="items.length <= 1"
                                            class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 hover:bg-rose-500 hover:text-white disabled:opacity-30 disabled:hover:bg-rose-500/10 disabled:hover:text-rose-600 inline-flex items-center justify-center transition-colors">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Repeater Add Button -->
                <div class="pt-2 flex justify-between items-center">
                    <button type="button" @click="addItem()"
                        class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1.5">
                        <i class="bi bi-plus-circle"></i>
                        <span>+ Tambah Baris Produk Lain</span>
                    </button>

                    <div class="text-xs font-semibold text-zinc-500">
                        Total Item: <strong class="text-zinc-900 dark:text-white" x-text="totalQty"></strong> barang (<strong class="text-zinc-900 dark:text-white" x-text="items.length"></strong> jenis)
                    </div>
                </div>
            </div>

            <!-- CARD 3: PEMBAYARAN & TOTAL AKHIR -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Kolom Catatan -->
                <div class="lg:col-span-6 m3-glass-card p-5 sm:p-7 shadow-sm dark:shadow-none space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-200/80 dark:border-zinc-800/80">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center font-black text-sm">
                            3
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                                Metode Pembayaran & Catatan
                            </h3>
                            <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                                Pilih metode pembayaran ke supplier atau tempo
                            </p>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Metode Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <select name="metode_pembayaran" required x-model="metode_pembayaran" @change="onMetodeChange()"
                            class="m3-input-glass w-full text-xs font-bold">
                            <option value="Tunai_Kas">Tunai Kas Koperasi (Cash Langsung)</option>
                            <option value="Transfer_Bank">Transfer Bank (Rekening Koperasi)</option>
                            <option value="Hutang_Tempo">Hutang ke Supplier (Pembayaran Tempo)</option>
                        </select>
                    </div>

                    <!-- Tanggal Jatuh Tempo (Hanya jika Hutang_Tempo) -->
                    <div x-show="metode_pembayaran === 'Hutang_Tempo'" x-transition class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-amber-800 dark:text-amber-400 mb-1.5">
                            Tanggal Jatuh Tempo Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_jatuh_tempo" x-model="tanggal_jatuh_tempo"
                            class="m3-input-glass w-full text-xs font-semibold">
                        <p class="text-[10px] text-amber-700/80 dark:text-amber-400/80 font-medium mt-1">
                            Faktur akan dicatat sebagai hutang supplier dan dapat dilunasi kapan saja.
                        </p>
                    </div>

                    <!-- Catatan / Keterangan -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Catatan / Keterangan Tambahan
                        </label>
                        <textarea name="catatan" rows="3" placeholder="Contoh: Pengadaan awal semester genap, diskon grosir 5%, diantar kurir."
                            class="m3-input-glass w-full text-xs font-medium"></textarea>
                    </div>
                </div>

                <!-- Kolom Ringkasan & Submit -->
                <div class="lg:col-span-6 m3-glass-card p-5 sm:p-7 shadow-sm dark:shadow-none space-y-4 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-black text-zinc-900 dark:text-white pb-3 border-b border-zinc-200/80 dark:border-zinc-800/80">
                            Ringkasan Biaya Pembelian
                        </h3>

                        <div class="space-y-3 pt-3 text-xs">
                            <!-- Subtotal -->
                            <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 font-semibold">
                                <span>Subtotal Pembelian Barang</span>
                                <span class="font-mono font-bold text-zinc-900 dark:text-white" x-text="'Rp ' + formatRupiah(subtotalPembelian)"></span>
                            </div>

                            <!-- Ongkos Kirim -->
                            <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 font-semibold">
                                <span>Biaya Pengiriman (Ongkir)</span>
                                <div class="w-36">
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-zinc-400 font-bold">Rp</span>
                                        <input type="number" name="ongkir" min="0" step="500" x-model.number="ongkir"
                                            class="m3-input-glass w-full pl-7 py-1 text-right text-xs font-bold font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- Diskon / Potongan Grosir -->
                            <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 font-semibold">
                                <span>Diskon / Potongan Toko</span>
                                <div class="w-36">
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-zinc-400 font-bold">Rp</span>
                                        <input type="number" name="diskon" min="0" step="500" x-model.number="diskon"
                                            class="m3-input-glass w-full pl-7 py-1 text-right text-xs font-bold font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- GRAND TOTAL -->
                            <div class="pt-3 border-t border-zinc-200/80 dark:border-zinc-800/80 flex justify-between items-center">
                                <span class="text-sm font-black text-zinc-900 dark:text-white">Grand Total Tagihan</span>
                                <span class="text-lg font-mono font-black text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + formatRupiah(grandTotal)"></span>
                            </div>

                            <!-- Nominal Yang Dibayar (DP / Lunas) -->
                            <div class="pt-2 border-t border-dashed border-zinc-200/80 dark:border-zinc-800/80 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-zinc-700 dark:text-zinc-300">Nominal Dibayar Saat Ini</span>
                                    <div class="w-44">
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-zinc-400 font-bold">Rp</span>
                                            <input type="number" name="nominal_bayar" min="0" step="1000" x-model.number="nominal_bayar"
                                                class="m3-input-glass w-full pl-7 py-1 text-right text-xs font-bold font-mono">
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Buttons (Pas, DP 50%, DP 0) -->
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button type="button" @click="setNominal(grandTotal)" class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-[10px] font-bold text-zinc-700 dark:text-zinc-300">
                                        Bayar Pas (Lunas)
                                    </button>
                                    <button type="button" @click="setNominal(Math.round(grandTotal / 2))" class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-[10px] font-bold text-zinc-700 dark:text-zinc-300">
                                        DP 50%
                                    </button>
                                    <button type="button" @click="setNominal(0)" class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-[10px] font-bold text-zinc-700 dark:text-zinc-300">
                                        Tempo Rp 0
                                    </button>
                                </div>

                                <!-- Sisa Hutang atau Kembalian Info -->
                                <template x-if="sisaHutang > 0">
                                    <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 flex justify-between items-center text-xs">
                                        <span class="font-bold text-amber-800 dark:text-amber-400 flex items-center gap-1">
                                            <i class="bi bi-clock-history"></i> Sisa Hutang Supplier:
                                        </span>
                                        <span class="font-mono font-black text-amber-700 dark:text-amber-300" x-text="'Rp ' + formatRupiah(sisaHutang)"></span>
                                    </div>
                                </template>

                                <template x-if="kembalian > 0">
                                    <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex justify-between items-center text-xs">
                                        <span class="font-bold text-emerald-800 dark:text-emerald-400 flex items-center gap-1">
                                            <i class="bi bi-cash-stack"></i> Kembalian Kas:
                                        </span>
                                        <span class="font-mono font-black text-emerald-700 dark:text-emerald-300" x-text="'Rp ' + formatRupiah(kembalian)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800/80 flex items-center gap-3">
                        <a href="{{ route('koperasi.pembelian.index') }}"
                            class="m3-btn-secondary min-h-[44px] px-4 text-xs font-bold inline-flex items-center justify-center active:scale-95">
                            Batal
                        </a>
                        <button type="submit"
                            class="m3-btn-primary flex-1 min-h-[44px] px-6 text-xs font-black inline-flex items-center justify-center gap-2 active:scale-95 shadow-lg shadow-primary/25">
                            <i class="bi bi-check-circle-fill text-sm"></i>
                            <span>Simpan Faktur Pembelian</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function pembelianApp(produks) {
            return {
                produksList: produks || [],
                items: [
                    {
                        produk_id: '',
                        stok_sekarang: 0,
                        satuan: '',
                        jumlah: 1,
                        harga_beli: 0,
                        harga_jual: 0,
                        subtotal: 0
                    }
                ],
                ongkir: 0,
                diskon: 0,
                metode_pembayaran: 'Tunai_Kas',
                nominal_bayar: 0,
                tanggal_jatuh_tempo: '',

                init() {
                    this.recalcAll();
                },

                addItem() {
                    this.items.push({
                        produk_id: '',
                        stok_sekarang: 0,
                        satuan: '',
                        jumlah: 1,
                        harga_beli: 0,
                        harga_jual: 0,
                        subtotal: 0
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.recalcAll();
                    }
                },

                onSelectProduct(index, event) {
                    const id = event.target.value;
                    const p = this.produksList.find(x => x.id == id);
                    if (p) {
                        this.items[index].stok_sekarang = p.stok;
                        this.items[index].satuan = p.satuan;
                        this.items[index].harga_beli = p.harga_beli;
                        this.items[index].harga_jual = p.harga_jual;
                        this.items[index].subtotal = (this.items[index].jumlah || 1) * p.harga_beli;
                    } else {
                        this.items[index].stok_sekarang = 0;
                        this.items[index].satuan = '';
                        this.items[index].harga_beli = 0;
                        this.items[index].harga_jual = 0;
                        this.items[index].subtotal = 0;
                    }
                    this.recalcAll();
                },

                updateSubtotal(index) {
                    const qty = parseFloat(this.items[index].jumlah) || 0;
                    const hb = parseFloat(this.items[index].harga_beli) || 0;
                    this.items[index].subtotal = qty * hb;
                    this.recalcAll();
                },

                recalcAll() {
                    if (this.metode_pembayaran !== 'Hutang_Tempo' && this.nominal_bayar === 0) {
                        this.nominal_bayar = this.grandTotal;
                    }
                },

                onMetodeChange() {
                    if (this.metode_pembayaran === 'Hutang_Tempo') {
                        this.nominal_bayar = 0;
                    } else {
                        this.nominal_bayar = this.grandTotal;
                    }
                },

                setNominal(val) {
                    this.nominal_bayar = Math.max(0, val);
                },

                get totalQty() {
                    return this.items.reduce((sum, item) => sum + (parseInt(item.jumlah) || 0), 0);
                },

                get subtotalPembelian() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.subtotal) || 0), 0);
                },

                get grandTotal() {
                    const sub = this.subtotalPembelian;
                    const ong = parseFloat(this.ongkir) || 0;
                    const disc = parseFloat(this.diskon) || 0;
                    return Math.max(0, sub + ong - disc);
                },

                get sisaHutang() {
                    const gt = this.grandTotal;
                    const bayar = parseFloat(this.nominal_bayar) || 0;
                    return Math.max(0, gt - bayar);
                },

                get kembalian() {
                    const gt = this.grandTotal;
                    const bayar = parseFloat(this.nominal_bayar) || 0;
                    return Math.max(0, bayar - gt);
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(Math.round(number || 0));
                }
            };
        }

        $(document).on('submit', '#formPembelian', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const $submitBtn = $(form).find('button[type="submit"]');
            const originalText = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan Faktur...');

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json'
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Faktur Berhasil Disimpan!',
                            text: res.message,
                            timer: 1600,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = res.redirect || "{{ route('koperasi.pembelian.index') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: res.message || 'Terjadi kesalahan sistem.'
                        });
                        $submitBtn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan saat memproses data.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.errors) {
                            const firstErr = Object.values(xhr.responseJSON.errors)[0];
                            if (Array.isArray(firstErr)) {
                                errorMsg = firstErr[0];
                            }
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: errorMsg
                    });
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>

</x-app-layout>
