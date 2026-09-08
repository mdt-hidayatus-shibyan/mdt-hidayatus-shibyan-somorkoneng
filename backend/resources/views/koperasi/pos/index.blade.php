@section('title', 'Kasir POS Toko Koperasi')
<x-app-layout>

    <!-- POS CONTAINER (Alpine.js State) -->
    <div x-data="posKasirApp()" x-init="initPos()" class="relative z-10">

        <!-- HEADER BAR: TITLE & SHORTCUTS -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:gap-4 relative z-10">
            <div>
                <h2
                    class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0">
                        <i class="bi bi-calculator-fill text-lg"></i>
                    </div>
                    <span>Kasir Koperasi MDT</span>

                </h2>
                <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Petugas: <strong class="text-zinc-700 dark:text-zinc-200">{{ Auth::user()->name }}</strong> •
                    {{ date('d M Y') }}
                </p>
            </div>

            <!-- SHORTCUTS & ACTION LINKS -->
            <div class="flex items-center gap-2 flex-wrap">
                <span
                    class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[11px] font-mono border border-zinc-200 dark:border-zinc-700">
                    <kbd
                        class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold">F2</kbd>
                    Pelanggan
                </span>
                <span
                    class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-[11px] font-mono border border-zinc-200 dark:border-zinc-700">
                    <kbd
                        class="px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 text-[10px] font-bold">F8</kbd>
                    Bayar
                </span>
                <a href="{{ route('koperasi.transaksi.index') }}" class="m3-btn-secondary">
                    <i class="bi bi-receipt text-sm"></i>
                    <span>Riwayat Nota</span>
                </a>
                <a href="{{ route('koperasi.dashboard') }}" class="m3-btn-secondary">
                    <i class="bi bi-arrow-left text-sm"></i>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>

        <!-- MAIN 2-COLUMN LAYOUT -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- ======================================================== -->
            <!-- KOLOM KIRI (7 Kolom): KATALOG PRODUK & PAKET BUNDLING    -->
            <!-- ======================================================== -->
            <div class="lg:col-span-7 flex flex-col space-y-4">

                <!-- TOOLBAR PENCARIAN & SCANNER BARCODE -->
                <div class="m3-glass-card p-4 rounded-2xl md:rounded-3xl space-y-3">
                    <div class="flex items-center gap-2">
                        <!-- Input Barcode Scanner -->
                        <div class="relative flex-1">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                                <i class="bi bi-upc-scan text-base text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <input type="text" x-model="barcodeInput" @keydown.enter.prevent="scanBarcode()"
                                placeholder="Scan Barcode / SKU lalu Enter..." id="posBarcodeInput"
                                class="m3-input-glass w-full !pl-10 text-xs font-mono font-bold tracking-wide placeholder:font-sans placeholder:font-normal focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <!-- Tombol Scan Enter -->
                        <button type="button" @click="scanBarcode()"
                            class="m3-btn-primary min-h-[40px] px-4 !bg-emerald-600 hover:!bg-emerald-700 text-white shrink-0">
                            <i class="bi bi-arrow-return-left"></i>
                            <span>Scan</span>
                        </button>
                    </div>

                    <!-- Input Filter Pencarian Nama Produk -->
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                                <i class="bi bi-search text-xs"></i>
                            </div>
                            <input type="text" x-model="searchQuery"
                                placeholder="Cari nama kitab, seragam, paket, atau alat tulis..."
                                class="m3-input-glass w-full !pl-9 text-xs font-semibold placeholder:font-normal">
                        </div>

                        <!-- Filter Kelas / Level untuk Paket -->
                        <div class="w-40 shrink-0">
                            <select x-model="selectedLevelFilter"
                                class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer">
                                <option value="">Semua Level</option>
                                @foreach ($levels as $lvl)
                                    <option value="{{ $lvl->id }}">{{ $lvl->nama_level }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Kategori Pills Tabs -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs">
                        <button type="button" @click="selectedCategory = 'all'"
                            :class="selectedCategory === 'all' ?
                                'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-sm' :
                                'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                            class="min-h-[38px] px-4 py-2 rounded-xl md:rounded-2xl font-bold whitespace-nowrap transition-all flex items-center gap-1.5 shrink-0 active:scale-95 cursor-pointer">
                            <i class="bi bi-grid-fill text-xs"></i>
                            <span>Semua ({{ count($produks) + count($pakets) }})</span>
                        </button>

                        <!-- Tab Khusus Paket Bundling -->
                        <button type="button" @click="selectedCategory = 'paket'"
                            :class="selectedCategory === 'paket' ?
                                'bg-indigo-600 text-white shadow-sm' :
                                'bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-500/20 hover:bg-indigo-500/20'"
                            class="min-h-[38px] px-4 py-2 rounded-xl md:rounded-2xl font-black whitespace-nowrap transition-all flex items-center gap-1.5 shrink-0 active:scale-95 cursor-pointer">
                            <i class="bi bi-collection-fill text-xs"></i>
                            <span>📦 Paket Bundling ({{ count($pakets) }})</span>
                        </button>

                        @foreach ($kategoris as $kat)
                            <button type="button" @click="selectedCategory = {{ $kat->id }}"
                                :class="selectedCategory === {{ $kat->id }} ? 'bg-emerald-600 text-white shadow-sm' :
                                    'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700'"
                                class="min-h-[38px] px-4 py-2 rounded-xl md:rounded-2xl font-bold whitespace-nowrap transition-all flex items-center gap-1.5 shrink-0 active:scale-95 cursor-pointer">
                                <i class="bi {{ $kat->icon ?: 'bi-tag-fill' }} text-xs"></i>
                                <span>{{ $kat->nama_kategori }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- REKOMENDASI PAKET BUNDLING OTOMATIS (Jika Pelanggan Murid Dipilih) -->
                <div x-show="pelanggan.jenis === 'Murid' && rekomendasiPakets.length > 0" x-cloak
                    class="p-4 m3-glass-card rounded-2xl md:rounded-3xl bg-indigo-500/5 border border-indigo-500/20 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-indigo-700 dark:text-indigo-400">
                            <i class="bi bi-stars text-base animate-spin text-amber-500"></i>
                            <h4 class="text-xs font-black uppercase tracking-wider">
                                Rekomendasi Paket untuk <span x-text="pelanggan.nama"></span> (<span
                                    x-text="pelanggan.kelas"></span>)
                            </h4>
                        </div>
                        <span class="text-[10px] font-bold text-indigo-500 bg-indigo-500/10 px-2 py-0.5 rounded-full">
                            Klik Cepat
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <template x-for="rec in rekomendasiPakets" :key="'rec-' + rec.id">
                            <div @click="addPaketToCart(rec)"
                                class="p-3 rounded-2xl bg-white/90 dark:bg-zinc-900/90 border border-indigo-500/30 hover:border-indigo-500 hover:shadow-md cursor-pointer transition-all flex items-center justify-between gap-3 group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-11 h-11 rounded-xl bg-indigo-500/10 border border-indigo-500/20 overflow-hidden shrink-0 flex items-center justify-center">
                                        <template x-if="rec.foto_url">
                                            <img :src="rec.foto_url" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!rec.foto_url">
                                            <i class="bi bi-collection-fill text-indigo-600 text-base"></i>
                                        </template>
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        <div class="text-xs font-black text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate"
                                            x-text="rec.nama"></div>
                                        <div class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate"
                                            x-text="rec.items_count + ' komponen kitab/barang'"></div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-xs font-black text-indigo-600 dark:text-indigo-400 font-mono"
                                        x-text="rec.harga_format"></div>
                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">+
                                        Tambah</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- GRID KATALOG PRODUK & PAKET (SCROLLABLE) -->
                <div
                    class="m3-glass-card p-4 md:p-5 rounded-2xl md:rounded-3xl flex-1 overflow-y-auto max-h-[580px] custom-scrollbar">
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5">

                        <!-- LOOP PAKET BUNDLING -->
                        <template x-for="pkt in filteredPakets" :key="'pkt-' + pkt.id">
                            <div @click="addPaketToCart(pkt)"
                                class="p-3 rounded-2xl bg-gradient-to-br from-indigo-500/5 to-purple-500/5 dark:from-indigo-950/30 dark:to-purple-950/30 border border-indigo-500/20 hover:border-indigo-500 hover:shadow-lg hover:-translate-y-0.5 transition-all cursor-pointer flex flex-col justify-between group relative overflow-hidden">

                                <div class="absolute top-2.5 right-2.5 z-10">
                                    <span
                                        class="px-2 py-0.5 rounded-md text-[9px] font-black bg-indigo-600 text-white uppercase shadow-xs">
                                        Paket
                                    </span>
                                </div>

                                <div class="space-y-2.5">
                                    <!-- Foto / Placeholder Paket -->
                                    <div
                                        class="w-full h-24 rounded-xl overflow-hidden bg-indigo-500/10 border border-indigo-500/15 flex items-center justify-center relative">
                                        <template x-if="pkt.foto_url">
                                            <img :src="pkt.foto_url" :alt="pkt.nama_paket"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </template>
                                        <template x-if="!pkt.foto_url">
                                            <div
                                                class="text-indigo-600 dark:text-indigo-400 flex flex-col items-center justify-center">
                                                <i class="bi bi-collection-fill text-2xl"></i>
                                                <span
                                                    class="text-[9px] font-bold mt-1 text-indigo-500/70">Bundling</span>
                                            </div>
                                        </template>
                                    </div>

                                    <div>
                                        <h4 class="text-xs font-black text-zinc-900 dark:text-white line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-snug"
                                            x-text="pkt.nama_paket"></h4>
                                        <p class="text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5"
                                            x-text="pkt.level ? pkt.level.nama_level : 'Semua Kelas'"></p>
                                    </div>
                                </div>

                                <div
                                    class="mt-3 pt-2.5 border-t border-indigo-500/10 dark:border-indigo-500/20 flex items-center justify-between">
                                    <div>
                                        <span class="text-[9px] text-zinc-400 font-bold block leading-tight">Harga
                                            Paket</span>
                                        <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 font-mono"
                                            x-text="'Rp ' + formatNominal(pkt.harga_paket)"></span>
                                    </div>
                                    <span
                                        class="w-7 h-7 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs font-black shadow-xs group-hover:scale-110 transition-transform">
                                        +
                                    </span>
                                </div>
                            </div>
                        </template>

                        <!-- LOOP PRODUK SATUAN -->
                        <template x-for="prod in filteredProduks" :key="'prod-' + prod.id">
                            <div @click="addProdukToCart(prod)"
                                class="p-3 rounded-2xl bg-white/90 dark:bg-zinc-900/90 border border-zinc-200/80 dark:border-zinc-800 hover:border-emerald-500 hover:shadow-lg hover:-translate-y-0.5 transition-all cursor-pointer flex flex-col justify-between group relative overflow-hidden">

                                <div class="space-y-2.5">
                                    <!-- Foto / Placeholder Produk with Stock overlay -->
                                    <div
                                        class="w-full h-24 rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/70 dark:border-zinc-700/60 flex items-center justify-center relative">
                                        <template x-if="prod.foto_url">
                                            <img :src="prod.foto_url" :alt="prod.nama_produk"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </template>
                                        <template x-if="!prod.foto_url">
                                            <div class="text-zinc-400 flex flex-col items-center justify-center">
                                                <i class="bi bi-box-seam text-2xl"></i>
                                            </div>
                                        </template>

                                        <!-- Stock Badge overlay on image bottom-right -->
                                        <div class="absolute bottom-1.5 right-1.5">
                                            <span class="text-[9px] font-black px-1.5 py-0.5 rounded shadow-xs"
                                                :class="prod.stok <= 0 ? 'bg-rose-500 text-white' : (prod.stok <= prod
                                                    .stok_minimum ? 'bg-amber-500 text-white' :
                                                    'bg-zinc-900/80 backdrop-blur-xs text-white')"
                                                x-text="'Stok ' + prod.stok">
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <span
                                            class="text-[9px] font-mono font-bold text-zinc-400 block leading-tight truncate"
                                            x-text="prod.kode_produk"></span>
                                        <h4 class="text-xs font-black text-zinc-900 dark:text-white line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-snug"
                                            x-text="prod.nama_produk"></h4>
                                        <p class="text-[10px] text-zinc-400 mt-0.5"
                                            x-text="prod.kategori ? prod.kategori.nama_kategori : '-'"></p>
                                    </div>
                                </div>

                                <div
                                    class="mt-3 pt-2.5 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                                    <div class="text-xs font-black text-zinc-900 dark:text-white font-mono"
                                        x-text="'Rp ' + formatNominal(prod.harga_jual)"></div>
                                    <span
                                        class="w-7 h-7 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs font-black shadow-xs group-hover:scale-110 transition-transform">
                                        +
                                    </span>
                                </div>
                            </div>
                        </template>

                    </div>

                    <!-- Empty state -->
                    <div x-show="filteredProduks.length === 0 && filteredPakets.length === 0"
                        class="text-center py-12 text-zinc-400 text-xs font-semibold">
                        <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                        Tidak ada barang atau paket yang cocok dengan pencarian.
                    </div>
                </div>

            </div>


            <!-- ======================================================== -->
            <!-- KOLOM KANAN (5 Kolom): KERANJANG BELANJA & CHECKOUT      -->
            <!-- ======================================================== -->
            <div class="lg:col-span-5 flex flex-col space-y-4">

                <!-- CARD KERANJANG UTAMA -->
                <div class="m3-glass-card p-4 sm:p-5 rounded-2xl md:rounded-3xl flex flex-col justify-between flex-1">

                    <!-- BAGIAN 1: PEMILIH PELANGGAN & INFO TABUNGAN -->
                    <div class="space-y-3 pb-3.5 border-b border-zinc-200/80 dark:border-zinc-800">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                                <i class="bi bi-person-badge-fill text-primary"></i>
                                <span>Pelanggan</span>
                            </span>

                            <!-- Kategori Pelanggan Tabs -->
                            <div class="inline-flex rounded-xl bg-zinc-100 dark:bg-zinc-800 p-1 text-[11px] font-bold">
                                <button type="button" @click="setJenisPelanggan('Umum')"
                                    :class="pelanggan.jenis === 'Umum' ?
                                        'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-2xs' :
                                        'text-zinc-500 dark:text-zinc-400'"
                                    class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">Umum</button>
                                <button type="button" @click="setJenisPelanggan('Murid')"
                                    :class="pelanggan.jenis === 'Murid' ?
                                        'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-2xs' :
                                        'text-zinc-500 dark:text-zinc-400'"
                                    class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">Murid</button>
                                <button type="button" @click="setJenisPelanggan('Ustadz')"
                                    :class="pelanggan.jenis === 'Ustadz' ?
                                        'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-2xs' :
                                        'text-zinc-500 dark:text-zinc-400'"
                                    class="px-2.5 py-1 rounded-lg transition-all cursor-pointer">Ustadz</button>
                            </div>
                        </div>

                        <!-- Input Lookup Pelanggan Murid / Ustadz -->
                        <div x-show="pelanggan.jenis !== 'Umum'" class="relative" x-cloak>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                                        <i class="bi bi-search text-xs"></i>
                                    </div>
                                    <input type="text" x-model="pelangganSearchQuery"
                                        @input.debounce.300ms="cariPelanggan()"
                                        :placeholder="'Ketik NISM/NIGM atau nama ' + pelanggan.jenis + '...'"
                                        class="m3-input-glass w-full !pl-9 text-xs font-bold">
                                </div>
                                <button type="button" @click="resetPelanggan()" x-show="pelanggan.id"
                                    class="min-h-[40px] px-3 rounded-xl bg-zinc-100 hover:bg-rose-50 text-rose-600 dark:bg-zinc-800 dark:hover:bg-rose-950/40 text-xs font-bold transition-all shrink-0 cursor-pointer"
                                    title="Ganti Pelanggan">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>

                            <!-- Dropdown Hasil Pencarian Pelanggan -->
                            <div x-show="pelangganSearchResults.length > 0 && !pelanggan.id"
                                @click.away="pelangganSearchResults = []"
                                class="absolute top-full left-0 right-0 mt-1.5 p-1.5 bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800 z-50 max-h-56 overflow-y-auto">
                                <template x-for="p in pelangganSearchResults" :key="'p-' + p.id">
                                    <div @click="selectPelanggan(p)"
                                        class="p-2.5 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-black text-zinc-900 dark:text-white"
                                                x-text="p.nama"></div>
                                            <div class="text-[10px] text-zinc-400"
                                                x-text="p.identitas + ' • ' + p.kelas"></div>
                                        </div>
                                        <div class="text-right">
                                            <span x-show="p.ada_tabungan"
                                                class="text-[10px] font-black text-purple-600 dark:text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded-md block font-mono"
                                                x-text="'Tabungan: ' + p.saldo_tabungan_format"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Banner Info Pelanggan Terpilih -->
                        <div x-show="pelanggan.id" x-cloak class="space-y-2">
                            <div
                                class="p-3.5 rounded-2xl bg-primary/5 border border-primary/20 flex items-center justify-between">
                                <div>
                                    <h5 class="text-xs font-black text-zinc-900 dark:text-white"
                                        x-text="pelanggan.nama">
                                    </h5>
                                    <p class="text-[10px] text-zinc-500 dark:text-zinc-400"
                                        x-text="pelanggan.identitas + ' • ' + pelanggan.kelas"></p>
                                </div>
                                <div class="text-right" x-show="pelanggan.ada_tabungan">
                                    <span class="text-[9px] text-zinc-400 font-bold block">Saldo Tabungan</span>
                                    <span class="text-xs font-black font-mono text-purple-600 dark:text-purple-400"
                                        x-text="pelanggan.saldo_tabungan_format"></span>
                                </div>
                            </div>

                            <!-- Peringatan Hutang Belum Lunas jika ada -->
                            <div x-show="pelanggan.ada_hutang"
                                class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-exclamation-triangle-fill text-amber-600 dark:text-amber-400"></i>
                                    <span class="text-amber-800 dark:text-amber-300 font-bold text-[11px]">
                                        Hutang belum lunas: <strong class="font-mono"
                                            x-text="pelanggan.total_hutang_format"></strong> (<span
                                            x-text="pelanggan.count_hutang"></span> nota)
                                    </span>
                                </div>
                                <a :href="'{{ route('koperasi.transaksi.index') }}?status_pembayaran=Belum_Lunas&search=' +
                                encodeURIComponent(pelanggan.nama)"
                                    target="_blank"
                                    class="text-[10px] font-black text-amber-700 dark:text-amber-400 underline hover:text-amber-900 shrink-0">
                                    Lihat Nota
                                </a>
                            </div>
                        </div>

                        <!-- Input Nama Pelanggan Umum jika Jenis = Umum -->
                        <div x-show="pelanggan.jenis === 'Umum'">
                            <input type="text" x-model="pelanggan.namaUmum"
                                placeholder="Nama Pembeli Umum / Tamu (Opsional)..."
                                class="m3-input-glass w-full text-xs font-semibold">
                        </div>
                    </div>

                    <!-- BAGIAN 2: DAFTAR ITEM KERANJANG -->
                    <div class="py-3 flex-1 overflow-y-auto max-h-[280px] custom-scrollbar space-y-2">
                        <div
                            class="flex items-center justify-between text-xs font-black text-zinc-500 pb-1 border-b border-zinc-100 dark:border-zinc-800">
                            <span>Item Belanja (<span x-text="cart.length"></span>)</span>
                            <button type="button" @click="clearCart()" x-show="cart.length > 0"
                                class="text-[11px] text-rose-500 hover:underline font-bold cursor-pointer">
                                Kosongkan
                            </button>
                        </div>

                        <template x-for="(item, index) in cart" :key="'cart-' + index">
                            <div
                                class="p-2.5 rounded-2xl bg-zinc-50/80 dark:bg-zinc-900/80 border border-zinc-200/60 dark:border-zinc-800 flex items-center justify-between gap-2.5">

                                <!-- Thumbnail Foto Item -->
                                <div
                                    class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 flex items-center justify-center">
                                    <template x-if="item.foto_url">
                                        <img :src="item.foto_url" :alt="item.nama"
                                            class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.foto_url">
                                        <i class="bi text-base"
                                            :class="item.tipe === 'Paket_Bundling' ? 'bi-collection-fill text-indigo-500' :
                                                'bi-box-seam text-zinc-400'"></i>
                                    </template>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span x-show="item.tipe === 'Paket_Bundling'"
                                            class="px-1.5 py-0.2 rounded text-[8px] font-black bg-indigo-600 text-white uppercase">Paket</span>
                                        <h5 class="text-xs font-black text-zinc-900 dark:text-white truncate"
                                            x-text="item.nama"></h5>
                                    </div>
                                    <div class="text-[11px] text-zinc-500 font-semibold font-mono"
                                        x-text="'Rp ' + formatNominal(item.harga_jual) + ' / ' + item.satuan"></div>
                                </div>

                                <!-- Qty Counter -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" @click="updateQty(index, -1)"
                                        class="w-7 h-7 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black text-xs hover:bg-zinc-300 dark:hover:bg-zinc-700 flex items-center justify-center active:scale-95 transition-all cursor-pointer">-</button>
                                    <span class="w-7 text-center font-black text-xs text-zinc-900 dark:text-white"
                                        x-text="item.jumlah"></span>
                                    <button type="button" @click="updateQty(index, 1)"
                                        class="w-7 h-7 rounded-lg bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black text-xs hover:bg-zinc-300 dark:hover:bg-zinc-700 flex items-center justify-center active:scale-95 transition-all cursor-pointer">+</button>
                                </div>

                                <!-- Subtotal Item & Hapus -->
                                <div class="text-right shrink-0 min-w-[75px]">
                                    <div class="text-xs font-black text-zinc-900 dark:text-white font-mono"
                                        x-text="'Rp ' + formatNominal(item.harga_jual * item.jumlah)"></div>
                                    <button type="button" @click="removeItem(index)"
                                        class="text-[10px] text-rose-500 hover:underline font-bold cursor-pointer">Hapus</button>
                                </div>
                            </div>
                        </template>

                        <!-- Empty cart state -->
                        <div x-show="cart.length === 0" class="text-center py-10 text-zinc-400 text-xs font-medium">
                            <i class="bi bi-cart-x text-3xl block mb-2 opacity-50"></i>
                            Keranjang kasir masih kosong. Klik barang di sebelah kiri atau scan barcode.
                        </div>
                    </div>

                    <!-- BAGIAN 3: KALKULASI & TOMBOL BAYAR -->
                    <div class="pt-3 border-t border-zinc-200/80 dark:border-zinc-800 space-y-3">
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                                <span>Subtotal</span>
                                <span class="font-bold font-mono text-zinc-900 dark:text-white"
                                    x-text="'Rp ' + formatNominal(calcSubtotal())"></span>
                            </div>
                            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                                <span>Diskon Transaksi (Rp)</span>
                                <input type="number" x-model.number="diskonTotal" min="0"
                                    class="w-28 px-2.5 py-1 text-right text-xs font-mono font-bold rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                            </div>
                        </div>

                        <!-- Grand Total Display -->
                        <div
                            class="p-4 rounded-2xl md:rounded-3xl bg-zinc-900 dark:bg-black text-white flex items-center justify-between shadow-md border border-zinc-800/80">
                            <div>
                                <span
                                    class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 block">Total
                                    Akhir Belanja</span>
                                <span class="text-xs font-bold text-emerald-400 flex items-center gap-1 mt-0.5">
                                    <i class="bi bi-bag-check-fill text-xs"></i>
                                    <span x-text="calcTotalItem() + ' Item Barang'"></span>
                                </span>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-emerald-400 font-mono tracking-tight"
                                x-text="'Rp ' + formatNominal(calcTotalAkhir())"></div>
                        </div>

                        <!-- Tombol Buka Modal Pembayaran -->
                        <button type="button" @click="openPaymentModal()" :disabled="cart.length === 0"
                            class="m3-btn-primary w-full min-h-[52px] !bg-emerald-600 hover:!bg-emerald-700 text-white font-black text-sm tracking-wide shadow-lg shadow-emerald-600/20 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                            <i class="bi bi-cash-stack text-lg"></i>
                            <span>PROSES BAYAR (F8)</span>
                        </button>
                    </div>

                </div>

            </div>

        </div>


        <!-- ======================================================== -->
        <!-- MODAL CHECKOUT & PEMBAYARAN KASIR                        -->
        <!-- ======================================================== -->
        <div x-show="showPaymentModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 dark:bg-black/80 backdrop-blur-md p-4 overflow-y-auto">

            <div @click.away="showPaymentModal = false"
                class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden flex flex-col max-h-[90vh]">

                <!-- Modal Header -->
                <div
                    class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-6 py-4 flex items-center justify-between transition-colors duration-300">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0 font-black">
                            <i class="bi bi-wallet2 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                                Pembayaran Kasir
                            </h3>
                            <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                                Pilih metode bayar & selesaikan transaksi belanja
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="showPaymentModal = false"
                        class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none cursor-pointer">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-4 flex-1">

                    <!-- Grand Total Banner (M3 Surface Card) -->
                    <div
                        class="p-4 sm:p-5 rounded-2xl md:rounded-3xl bg-emerald-500/10 dark:bg-emerald-950/40 border border-emerald-500/20 flex items-center justify-between gap-3">
                        <div>
                            <span
                                class="text-[10px] font-black uppercase tracking-wider text-emerald-800/80 dark:text-emerald-300/80 block">
                                Total yang Harus Dibayar
                            </span>
                            <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono tracking-tight"
                                x-text="'Rp ' + formatNominal(calcTotalAkhir())"></div>
                        </div>
                        <div class="text-right shrink-0">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-zinc-900/90 border border-emerald-500/20 text-xs font-black text-zinc-800 dark:text-zinc-200 shadow-2xs">
                                <i class="bi bi-person-fill text-emerald-600 dark:text-emerald-400"></i>
                                <span x-text="pelanggan.nama || (pelanggan.namaUmum || 'Pelanggan Umum')"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Pilihan Metode Pembayaran (4 M3 Interactive Tiles) -->
                    <div class="space-y-2">
                        <label
                            class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                            Metode Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <!-- 1. Tunai (Cash) -->
                            <button type="button" @click="setMetode('Tunai')"
                                :class="paymentMethod === 'Tunai' ?
                                    'bg-emerald-500/15 border-emerald-500 text-emerald-800 dark:text-emerald-300 ring-2 ring-emerald-500/30 shadow-xs' :
                                    'bg-zinc-50/80 dark:bg-zinc-800/40 border-zinc-200/80 dark:border-zinc-700/60 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                class="min-h-[64px] p-2 rounded-2xl border text-center transition-all flex flex-col items-center justify-center gap-1 active:scale-95 cursor-pointer">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-base"
                                    :class="paymentMethod === 'Tunai' ?
                                        'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' :
                                        'text-zinc-500 dark:text-zinc-400'">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <span class="text-xs font-black tracking-tight">Tunai</span>
                            </button>

                            <!-- 2. Potong Tabungan -->
                            <button type="button" @click="setMetode('Potong_Tabungan')"
                                :disabled="!pelanggan.ada_tabungan"
                                :class="paymentMethod === 'Potong_Tabungan' ?
                                    'bg-purple-500/15 border-purple-500 text-purple-800 dark:text-purple-300 ring-2 ring-purple-500/30 shadow-xs' :
                                    (!pelanggan.ada_tabungan ?
                                        'opacity-40 cursor-not-allowed bg-zinc-50/40 dark:bg-zinc-800/20 border-zinc-200/50 dark:border-zinc-800 text-zinc-400' :
                                        'bg-zinc-50/80 dark:bg-zinc-800/40 border-zinc-200/80 dark:border-zinc-700/60 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'
                                    )"
                                class="min-h-[64px] p-2 rounded-2xl border text-center transition-all flex flex-col items-center justify-center gap-1 active:scale-95 cursor-pointer">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-base"
                                    :class="paymentMethod === 'Potong_Tabungan' ?
                                        'bg-purple-500/20 text-purple-600 dark:text-purple-400' :
                                        'text-zinc-500 dark:text-zinc-400'">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <span class="text-xs font-black tracking-tight">Tabungan</span>
                            </button>

                            <!-- 3. QRIS / TF -->
                            <button type="button" @click="setMetode('QRIS')"
                                :class="paymentMethod === 'QRIS' ?
                                    'bg-sky-500/15 border-sky-500 text-sky-800 dark:text-sky-300 ring-2 ring-sky-500/30 shadow-xs' :
                                    'bg-zinc-50/80 dark:bg-zinc-800/40 border-zinc-200/80 dark:border-zinc-700/60 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                class="min-h-[64px] p-2 rounded-2xl border text-center transition-all flex flex-col items-center justify-center gap-1 active:scale-95 cursor-pointer">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-base"
                                    :class="paymentMethod === 'QRIS' ?
                                        'bg-sky-500/20 text-sky-600 dark:text-sky-400' :
                                        'text-zinc-500 dark:text-zinc-400'">
                                    <i class="bi bi-qr-code"></i>
                                </div>
                                <span class="text-xs font-black tracking-tight">QRIS / TF</span>
                            </button>

                            <!-- 4. Bayar Nanti / Hutang -->
                            <button type="button" @click="setMetode('Hutang')"
                                :class="paymentMethod === 'Hutang' ?
                                    'bg-amber-500/15 border-amber-500 text-amber-800 dark:text-amber-300 ring-2 ring-amber-500/30 shadow-xs' :
                                    'bg-zinc-50/80 dark:bg-zinc-800/40 border-zinc-200/80 dark:border-zinc-700/60 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                class="min-h-[64px] p-2 rounded-2xl border text-center transition-all flex flex-col items-center justify-center gap-1 active:scale-95 cursor-pointer">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-base"
                                    :class="paymentMethod === 'Hutang' ?
                                        'bg-amber-500/20 text-amber-600 dark:text-amber-400' :
                                        'text-zinc-500 dark:text-zinc-400'">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <span class="text-xs font-black tracking-tight">Hutang</span>
                            </button>
                        </div>
                    </div>

                    <!-- 1. FORM PEMBAYARAN TUNAI -->
                    <div x-show="paymentMethod === 'Tunai'" class="space-y-3.5">
                        <div class="space-y-1.5">
                            <label
                                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                                Uang Diterima (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <div
                                class="relative flex rounded-2xl overflow-hidden shadow-2xs border border-zinc-300 dark:border-zinc-700 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all">
                                <div
                                    class="px-4 bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border-r border-zinc-300 dark:border-zinc-700 text-xs font-mono font-black text-zinc-600 dark:text-zinc-400 select-none">
                                    Rp
                                </div>
                                <input type="number" x-model.number="nominalBayar" id="inputNominalBayar"
                                    placeholder="0"
                                    class="w-full bg-white/80 dark:bg-black/80 px-4 py-2.5 text-xl sm:text-2xl font-mono font-black text-right text-zinc-900 dark:text-white outline-none">
                            </div>
                        </div>

                        <!-- Tombol Pecahan Cepat -->
                        <div class="space-y-1">
                            <span
                                class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider ml-1">Pecahan
                                Cepat:</span>
                            <div class="grid grid-cols-5 gap-1.5">
                                <button type="button" @click="nominalBayar = calcTotalAkhir()"
                                    class="min-h-[38px] px-2 py-1.5 rounded-xl bg-zinc-100 hover:bg-emerald-500 hover:text-white dark:bg-zinc-800 dark:hover:bg-emerald-600 text-[11px] font-black text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/60 transition-all active:scale-95 shadow-2xs cursor-pointer">
                                    Pas
                                </button>
                                <button type="button" @click="nominalBayar = 10000"
                                    class="min-h-[38px] px-2 py-1.5 rounded-xl bg-zinc-100 hover:bg-emerald-500 hover:text-white dark:bg-zinc-800 dark:hover:bg-emerald-600 text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/60 transition-all active:scale-95 shadow-2xs cursor-pointer">
                                    10k
                                </button>
                                <button type="button" @click="nominalBayar = 20000"
                                    class="min-h-[38px] px-2 py-1.5 rounded-xl bg-zinc-100 hover:bg-emerald-500 hover:text-white dark:bg-zinc-800 dark:hover:bg-emerald-600 text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/60 transition-all active:scale-95 shadow-2xs cursor-pointer">
                                    20k
                                </button>
                                <button type="button" @click="nominalBayar = 50000"
                                    class="min-h-[38px] px-2 py-1.5 rounded-xl bg-zinc-100 hover:bg-emerald-500 hover:text-white dark:bg-zinc-800 dark:hover:bg-emerald-600 text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/60 transition-all active:scale-95 shadow-2xs cursor-pointer">
                                    50k
                                </button>
                                <button type="button" @click="nominalBayar = 100000"
                                    class="min-h-[38px] px-2 py-1.5 rounded-xl bg-zinc-100 hover:bg-emerald-500 hover:text-white dark:bg-zinc-800 dark:hover:bg-emerald-600 text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700/60 transition-all active:scale-95 shadow-2xs cursor-pointer">
                                    100k
                                </button>
                            </div>
                        </div>

                        <!-- Kembalian / Status Display -->
                        <div class="p-3.5 sm:p-4 rounded-2xl flex items-center justify-between border transition-all"
                            :class="calcKembalian() >= 0 ?
                                'bg-emerald-500/10 dark:bg-emerald-950/30 border-emerald-500/25 text-emerald-900 dark:text-emerald-300' :
                                'bg-rose-500/10 dark:bg-rose-950/30 border-rose-500/25 text-rose-900 dark:text-rose-300'">
                            <div class="flex items-center gap-2">
                                <i class="bi text-lg"
                                    :class="calcKembalian() >= 0 ?
                                        'bi-check-circle-fill text-emerald-600 dark:text-emerald-400' :
                                        'bi-exclamation-triangle-fill text-rose-600 dark:text-rose-400'"></i>
                                <span class="text-xs font-black uppercase tracking-wider"
                                    x-text="calcKembalian() >= 0 ? 'Kembalian' : 'Kurang Bayar'"></span>
                            </div>
                            <div class="text-lg sm:text-xl font-mono font-black tracking-tight"
                                :class="calcKembalian() >= 0 ?
                                    'text-emerald-600 dark:text-emerald-400' :
                                    'text-rose-600 dark:text-rose-400'"
                                x-text="'Rp ' + formatNominal(Math.abs(calcKembalian()))"></div>
                        </div>
                    </div>

                    <!-- 2. FORM POTONG TABUNGAN -->
                    <div x-show="paymentMethod === 'Potong_Tabungan'" x-cloak
                        class="p-4 rounded-2xl bg-purple-500/10 dark:bg-purple-950/30 border border-purple-500/20 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between font-bold text-purple-900 dark:text-purple-300">
                            <span>Saldo Tabungan Saat Ini:</span>
                            <span class="font-black font-mono" x-text="pelanggan.saldo_tabungan_format"></span>
                        </div>
                        <div class="flex items-center justify-between font-bold text-purple-900 dark:text-purple-300">
                            <span>Total Belanja:</span>
                            <span class="font-black font-mono"
                                x-text="'Rp ' + formatNominal(calcTotalAkhir())"></span>
                        </div>
                        <div
                            class="pt-2 border-t border-purple-500/20 flex items-center justify-between font-black text-purple-900 dark:text-purple-200">
                            <span>Sisa Saldo Tabungan:</span>
                            <span class="font-mono text-sm"
                                :class="(pelanggan.saldo_tabungan - calcTotalAkhir()) < 0 ? 'text-rose-600' :
                                    'text-purple-700 dark:text-purple-300'"
                                x-text="'Rp ' + formatNominal(pelanggan.saldo_tabungan - calcTotalAkhir())"></span>
                        </div>
                        <div x-show="(pelanggan.saldo_tabungan - calcTotalAkhir()) < 0"
                            class="text-[11px] font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1 pt-1">
                            <i class="bi bi-x-circle-fill"></i>
                            <span>Saldo tabungan tidak mencukupi untuk pembayaran ini.</span>
                        </div>
                    </div>

                    <!-- 3. FORM QRIS / TF -->
                    <div x-show="paymentMethod === 'QRIS'" x-cloak
                        class="p-4 rounded-2xl bg-sky-500/10 dark:bg-sky-950/30 border border-sky-500/20 text-center space-y-2">
                        <div
                            class="w-12 h-12 rounded-2xl bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center mx-auto text-xl">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <div class="text-xs font-black text-sky-900 dark:text-sky-200">
                            Pindai QRIS Toko Koperasi
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                            Pastikan pembeli telah menyelesaikan transfer senilai <strong
                                class="text-zinc-900 dark:text-white font-mono"
                                x-text="'Rp ' + formatNominal(calcTotalAkhir())"></strong> sebelum konfirmasi.
                        </p>
                    </div>

                    <!-- 4. FORM BAYAR NANTI / HUTANG -->
                    <div x-show="paymentMethod === 'Hutang'" x-cloak
                        class="p-4 rounded-2xl bg-amber-500/10 dark:bg-amber-950/30 border border-amber-500/20 space-y-3 text-xs">
                        <div class="flex items-center gap-2.5 text-amber-900 dark:text-amber-300">
                            <div
                                class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-xs">Transaksi Bayar Nanti / Hutang (Piutang)</h5>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Tagihan akan dicatat sebagai
                                    piutang koperasi atas nama pelanggan ini.</p>
                            </div>
                        </div>

                        <div
                            class="p-3 rounded-xl bg-white/80 dark:bg-zinc-900/80 border border-amber-500/20 space-y-1.5">
                            <div class="flex justify-between font-bold text-zinc-600 dark:text-zinc-400">
                                <span>Atas Nama Pelanggan:</span>
                                <span class="font-black text-zinc-900 dark:text-white"
                                    x-text="pelanggan.nama || (pelanggan.namaUmum || 'Pelanggan Umum')"></span>
                            </div>
                            <div class="flex justify-between font-bold text-zinc-600 dark:text-zinc-400">
                                <span>Nominal Hutang / Piutang:</span>
                                <span class="font-black font-mono text-amber-600 dark:text-amber-400 text-sm"
                                    x-text="'Rp ' + formatNominal(calcTotalAkhir())"></span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Catatan / Perjanjian Jatuh Tempo (Opsional):
                            </label>
                            <input type="text" x-model="catatanTransaksi"
                                placeholder="Contoh: Janji bayar hari Kamis depan / Titip wali santri..."
                                class="m3-input-glass w-full text-xs font-medium">
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div
                    class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-6 py-4 flex items-center justify-end gap-3 transition-colors duration-300">
                    <button type="button" @click="showPaymentModal = false"
                        class="m3-btn-secondary w-1/3 min-h-[44px]">
                        Batal
                    </button>
                    <button type="button" @click="submitCheckout()" :disabled="isSubmitting || !isPaymentValid()"
                        class="m3-btn-primary w-2/3 min-h-[44px] !bg-emerald-600 hover:!bg-emerald-700 text-white">
                        <span x-show="!isSubmitting" class="inline-flex items-center gap-1.5">
                            <i class="bi bi-printer-fill text-sm"></i>
                            <span
                                x-text="paymentMethod === 'Hutang' ? 'Simpan Nota Hutang' : 'Simpan & Cetak Struk'"></span>
                        </span>
                        <span x-show="isSubmitting" class="inline-flex items-center gap-1.5">
                            <i class="bi bi-arrow-repeat animate-spin text-sm"></i>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>

            </div>

        </div>


        <!-- ======================================================== -->
        <!-- MODAL SUKSES TRANSAKSI                                   -->
        <!-- ======================================================== -->
        <div x-show="showSuccessModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 dark:bg-black/80 backdrop-blur-md p-4">

            <div
                class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-3xl shadow-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden flex flex-col">

                <div class="p-6 text-center space-y-4">
                    <div
                        class="w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mx-auto border border-emerald-500/20 shadow-2xs">
                        <i class="bi bi-check2-circle"></i>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">Transaksi Selesai!
                        </h3>
                        <p class="text-xs font-mono font-bold text-zinc-500 dark:text-zinc-400"
                            x-text="'No. Nota: ' + (lastSuccessData.nomor_nota || '-')"></p>
                    </div>

                    <div
                        class="p-4 rounded-2xl bg-zinc-50/90 dark:bg-zinc-800/50 border border-zinc-200/60 dark:border-zinc-700/60 space-y-2 text-xs">
                        <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                            <span>Metode Bayar</span>
                            <span class="font-black text-zinc-900 dark:text-white"
                                x-text="lastSuccessData.metode_pembayaran === 'Hutang' ? 'Bayar Nanti / Hutang' : lastSuccessData.metode_pembayaran"></span>
                        </div>
                        <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                            <span>Total Belanja</span>
                            <span class="font-black font-mono text-zinc-900 dark:text-white"
                                x-text="lastSuccessData.total_akhir_format"></span>
                        </div>
                        <div class="flex justify-between text-zinc-500 dark:text-zinc-400"
                            x-show="lastSuccessData.metode_pembayaran === 'Tunai'">
                            <span>Uang Diterima</span>
                            <span class="font-bold font-mono text-zinc-900 dark:text-white"
                                x-text="'Rp ' + formatNominal(lastSuccessData.nominal_bayar)"></span>
                        </div>
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400 pt-2 border-t border-zinc-200/80 dark:border-zinc-700"
                            x-show="lastSuccessData.metode_pembayaran === 'Tunai'">
                            <span class="font-black">Kembalian</span>
                            <span class="font-black font-mono text-base"
                                x-text="lastSuccessData.kembalian_format"></span>
                        </div>
                        <div class="p-2 rounded-xl bg-amber-500/10 text-amber-700 dark:text-amber-400 font-bold text-center"
                            x-show="lastSuccessData.metode_pembayaran === 'Hutang'">
                            Status: Tercatat sebagai Piutang (Belum Lunas)
                        </div>
                    </div>
                </div>

                <div
                    class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-6 py-4 flex items-center gap-3">
                    <a :href="lastSuccessData.cetak_url" target="_blank" class="m3-btn-secondary w-1/2 min-h-[44px]">
                        <i class="bi bi-printer text-sm"></i>
                        <span>Cetak Struk</span>
                    </a>
                    <button type="button" @click="resetPosBaru()"
                        class="m3-btn-primary w-1/2 min-h-[44px] !bg-emerald-600 hover:!bg-emerald-700 text-white">
                        <i class="bi bi-plus-circle text-sm"></i>
                        <span>Transaksi Baru</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- ALPINE.JS COMPONENT SCRIPT -->
    <script>
        function posKasirApp() {
            return {
                barcodeInput: '',
                searchQuery: '',
                selectedCategory: 'all',
                selectedLevelFilter: '',

                // Master data dari backend
                produks: @json($produks),
                pakets: @json($pakets),

                // Keranjang belanja
                cart: [],
                diskonTotal: 0,
                catatanTransaksi: '',

                // Pelanggan
                pelanggan: {
                    jenis: 'Umum',
                    id: null,
                    nama: '',
                    namaUmum: '',
                    identitas: '',
                    kelas: '',
                    levelId: null,
                    ada_tabungan: false,
                    saldo_tabungan: 0,
                    saldo_tabungan_format: 'Rp 0',
                    ada_hutang: false,
                    total_hutang: 0,
                    total_hutang_format: 'Rp 0',
                    count_hutang: 0,
                },
                pelangganSearchQuery: '',
                pelangganSearchResults: [],
                rekomendasiPakets: [],

                // Pembayaran
                showPaymentModal: false,
                paymentMethod: 'Tunai',
                nominalBayar: 0,
                isSubmitting: false,

                // Success Modal
                showSuccessModal: false,
                lastSuccessData: {},

                initPos() {
                    // Shortcut Keyboard Listeners
                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'F8') {
                            e.preventDefault();
                            if (this.cart.length > 0) this.openPaymentModal();
                        } else if (e.key === 'F2') {
                            e.preventDefault();
                            this.setJenisPelanggan('Murid');
                        }
                    });
                },

                // Getter Filtered Produk
                get filteredProduks() {
                    if (this.selectedCategory === 'paket') return [];

                    return this.produks.filter(p => {
                        const matchCat = (this.selectedCategory === 'all' || p.kategori_id === this
                            .selectedCategory);
                        const matchSearch = !this.searchQuery ||
                            p.nama_produk.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            p.kode_produk.toLowerCase().includes(this.searchQuery.toLowerCase());
                        return matchCat && matchSearch;
                    });
                },

                // Getter Filtered Paket Bundling
                get filteredPakets() {
                    if (this.selectedCategory !== 'all' && this.selectedCategory !== 'paket') return [];

                    return this.pakets.filter(p => {
                        const matchSearch = !this.searchQuery ||
                            p.nama_paket.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            p.kode_paket.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchLevel = !this.selectedLevelFilter || p.level_id == this.selectedLevelFilter;
                        return matchSearch && matchLevel;
                    });
                },

                // Tambah Produk Satuan ke Keranjang
                addProdukToCart(prod) {
                    if (prod.stok <= 0) {
                        alert('Maaf, stok produk ' + prod.nama_produk + ' sedang habis.');
                        return;
                    }

                    const existingIndex = this.cart.findIndex(item => item.tipe === 'Produk' && item.produk_id ===
                        prod.id);
                    if (existingIndex > -1) {
                        if (this.cart[existingIndex].jumlah + 1 > prod.stok) {
                            alert('Stok tidak mencukupi untuk menambah item ini lagi.');
                            return;
                        }
                        this.cart[existingIndex].jumlah++;
                    } else {
                        this.cart.push({
                            tipe: 'Produk',
                            produk_id: prod.id,
                            paket_id: null,
                            kode: prod.kode_produk,
                            nama: prod.nama_produk,
                            satuan: prod.satuan,
                            harga_jual: prod.harga_jual,
                            jumlah: 1,
                            diskon_item: 0,
                            stok_tersedia: prod.stok,
                            foto_url: prod.foto_url || null,
                        });
                    }
                },

                // Tambah Paket Bundling ke Keranjang
                addPaketToCart(pkt) {
                    const existingIndex = this.cart.findIndex(item => item.tipe === 'Paket_Bundling' && item.paket_id ===
                        pkt.id);
                    if (existingIndex > -1) {
                        this.cart[existingIndex].jumlah++;
                    } else {
                        this.cart.push({
                            tipe: 'Paket_Bundling',
                            produk_id: null,
                            paket_id: pkt.id,
                            kode: pkt.kode_paket,
                            nama: pkt.nama_paket,
                            satuan: 'paket',
                            harga_jual: pkt.harga_paket,
                            jumlah: 1,
                            diskon_item: 0,
                            stok_tersedia: 999,
                            foto_url: pkt.foto_url || null,
                        });
                    }
                },

                // Scan Barcode Produk / Paket
                async scanBarcode() {
                    const code = this.barcodeInput.trim();
                    if (!code) return;

                    try {
                        const response = await fetch(
                            `{{ route('koperasi.pos.cari-barcode') }}?barcode=${encodeURIComponent(code)}`);
                        const res = await response.json();

                        if (res.success) {
                            if (res.tipe === 'Paket_Bundling') {
                                this.addPaketToCart({
                                    id: res.data.id,
                                    kode_paket: res.data.kode,
                                    nama_paket: res.data.nama,
                                    harga_paket: res.data.harga_jual,
                                    foto_url: res.data.foto_url || null,
                                });
                            } else {
                                this.addProdukToCart({
                                    id: res.data.id,
                                    kode_produk: res.data.kode,
                                    nama_produk: res.data.nama,
                                    satuan: res.data.satuan,
                                    harga_jual: res.data.harga_jual,
                                    stok: res.data.stok,
                                    foto_url: res.data.foto_url || null,
                                });
                            }
                            this.barcodeInput = '';
                        } else {
                            alert(res.message || 'Barcode tidak ditemukan.');
                        }
                    } catch (e) {
                        alert('Gagal memindai barcode.');
                    }
                },

                // Kelola Kuantitas Item di Keranjang
                updateQty(index, change) {
                    const item = this.cart[index];
                    const newQty = item.jumlah + change;
                    if (newQty <= 0) {
                        this.removeItem(index);
                    } else {
                        if (item.tipe === 'Produk' && newQty > item.stok_tersedia) {
                            alert('Stok barang hanya tersedia ' + item.stok_tersedia + ' ' + item.satuan);
                            return;
                        }
                        item.jumlah = newQty;
                    }
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    if (confirm('Yakin ingin mengosongkan keranjang belanja?')) {
                        this.cart = [];
                        this.diskonTotal = 0;
                        this.catatanTransaksi = '';
                    }
                },

                // Kalkulasi Subtotal & Total
                calcSubtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.harga_jual * item.jumlah), 0);
                },

                calcTotalItem() {
                    return this.cart.reduce((sum, item) => sum + item.jumlah, 0);
                },

                calcTotalAkhir() {
                    return Math.max(0, this.calcSubtotal() - (this.diskonTotal || 0));
                },

                calcKembalian() {
                    if (this.paymentMethod !== 'Tunai') return 0;
                    return (this.nominalBayar || 0) - this.calcTotalAkhir();
                },

                // Pelanggan Handler
                setJenisPelanggan(jenis) {
                    this.pelanggan.jenis = jenis;
                    this.resetPelanggan();
                },

                resetPelanggan() {
                    this.pelanggan.id = null;
                    this.pelanggan.nama = '';
                    this.pelanggan.identitas = '';
                    this.pelanggan.kelas = '';
                    this.pelanggan.levelId = null;
                    this.pelanggan.ada_tabungan = false;
                    this.pelanggan.saldo_tabungan = 0;
                    this.pelanggan.saldo_tabungan_format = 'Rp 0';
                    this.pelanggan.ada_hutang = false;
                    this.pelanggan.total_hutang = 0;
                    this.pelanggan.total_hutang_format = 'Rp 0';
                    this.pelanggan.count_hutang = 0;
                    this.pelangganSearchQuery = '';
                    this.pelangganSearchResults = [];
                    this.rekomendasiPakets = [];
                },

                async cariPelanggan() {
                    const q = this.pelangganSearchQuery.trim();
                    if (!q) {
                        this.pelangganSearchResults = [];
                        return;
                    }

                    try {
                        const response = await fetch(
                            `{{ route('koperasi.pos.cari-pelanggan') }}?tipe=${this.pelanggan.jenis}&q=${encodeURIComponent(q)}`
                        );
                        const res = await response.json();
                        if (res.success) {
                            this.pelangganSearchResults = res.data;
                        }
                    } catch (e) {}
                },

                selectPelanggan(p) {
                    this.pelanggan.id = p.id;
                    this.pelanggan.nama = p.nama;
                    this.pelanggan.identitas = p.identitas;
                    this.pelanggan.kelas = p.kelas;
                    this.pelanggan.levelId = p.level_id;
                    this.pelanggan.ada_tabungan = p.ada_tabungan;
                    this.pelanggan.saldo_tabungan = p.saldo_tabungan;
                    this.pelanggan.saldo_tabungan_format = p.saldo_tabungan_format;
                    this.pelanggan.ada_hutang = p.ada_hutang || false;
                    this.pelanggan.total_hutang = p.total_hutang || 0;
                    this.pelanggan.total_hutang_format = p.total_hutang_format || 'Rp 0';
                    this.pelanggan.count_hutang = p.count_hutang || 0;
                    this.pelangganSearchResults = [];
                    this.paymentMethod = 'Tunai'; // Default selalu Tunai / Cash (bukan potong tabungan otomatis)

                    // Ambil rekomendasi paket jika Murid memiliki level/kelas
                    if (this.pelanggan.jenis === 'Murid' && p.level_id) {
                        this.fetchRekomendasiPaket(p.level_id);
                    }
                },

                async fetchRekomendasiPaket(levelId) {
                    try {
                        const res = await (await fetch(
                            `{{ route('koperasi.pos.paket-rekomendasi') }}?level_id=${levelId}`)).json();
                        if (res.success) {
                            this.rekomendasiPakets = res.data;
                        }
                    } catch (e) {}
                },

                // Modal Pembayaran
                openPaymentModal() {
                    this.showPaymentModal = true;
                    this.paymentMethod = 'Tunai'; // Default selalu Tunai (Cash)
                    this.nominalBayar = this.calcTotalAkhir();
                    this.catatanTransaksi = '';

                    this.$nextTick(() => {
                        const inp = document.getElementById('inputNominalBayar');
                        if (inp) inp.select();
                    });
                },

                setMetode(m) {
                    this.paymentMethod = m;
                    if (m === 'Potong_Tabungan') {
                        this.nominalBayar = this.calcTotalAkhir();
                    } else if (m === 'Tunai') {
                        this.nominalBayar = this.calcTotalAkhir();
                        this.$nextTick(() => {
                            const inp = document.getElementById('inputNominalBayar');
                            if (inp) inp.select();
                        });
                    } else if (m === 'QRIS') {
                        this.nominalBayar = this.calcTotalAkhir();
                    } else if (m === 'Hutang') {
                        this.nominalBayar = 0;
                    }
                },

                isPaymentValid() {
                    const total = this.calcTotalAkhir();
                    if (total <= 0) return true;

                    if (this.paymentMethod === 'Tunai') {
                        return this.nominalBayar >= total;
                    } else if (this.paymentMethod === 'Potong_Tabungan') {
                        return this.pelanggan.ada_tabungan && this.pelanggan.saldo_tabungan >= total;
                    } else if (this.paymentMethod === 'Hutang') {
                        return true;
                    }
                    return true;
                },

                // Submit Transaksi ke Backend
                async submitCheckout() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;

                    const payload = {
                        jenis_pelanggan: this.pelanggan.jenis,
                        murid_id: this.pelanggan.jenis === 'Murid' ? this.pelanggan.id : null,
                        ustadz_id: this.pelanggan.jenis === 'Ustadz' ? this.pelanggan.id : null,
                        nama_pelanggan_umum: this.pelanggan.namaUmum || 'Pelanggan Umum',
                        metode_pembayaran: this.paymentMethod,
                        nominal_bayar: this.nominalBayar,
                        diskon: this.diskonTotal || 0,
                        catatan: this.catatanTransaksi || null,
                        items: this.cart.map(item => ({
                            tipe: item.tipe,
                            produk_id: item.produk_id,
                            paket_id: item.paket_id,
                            jumlah: item.jumlah,
                            harga_jual: item.harga_jual,
                            diskon_item: item.diskon_item || 0,
                        })),
                    };

                    try {
                        const response = await fetch(`{{ route('koperasi.pos.checkout') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(payload),
                        });

                        const res = await response.json();

                        if (res.success) {
                            this.showPaymentModal = false;
                            this.lastSuccessData = res;
                            this.showSuccessModal = true;
                        } else {
                            alert(res.message || 'Terjadi kesalahan saat memproses transaksi.');
                        }
                    } catch (e) {
                        alert('Gagal menghubungi server kasir.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                resetPosBaru() {
                    this.showSuccessModal = false;
                    this.cart = [];
                    this.diskonTotal = 0;
                    this.catatanTransaksi = '';
                    this.resetPelanggan();
                    this.pelanggan.jenis = 'Umum';
                },

                formatNominal(val) {
                    if (!val || isNaN(val)) return '0';
                    return new Intl.NumberFormat('id-ID').format(Math.round(val));
                }
            }
        }
    </script>

</x-app-layout>
