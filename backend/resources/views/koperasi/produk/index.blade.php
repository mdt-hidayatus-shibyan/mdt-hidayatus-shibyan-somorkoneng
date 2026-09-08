@section('title', 'Master Produk Koperasi')
<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 relative z-10">
        <div>
            <h2
                class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <div
                    class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0">
                    <i class="bi bi-box-seam-fill text-lg"></i>
                </div>
                <span>Master Produk Koperasi</span>
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Kelola katalog kitab, seragam, atribut, alat tulis, dan stok barang.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('koperasi.pos.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-sm active:scale-95 transition-all">
                <i class="bi bi-calculator-fill text-sm"></i>
                <span>Buka Kasir POS</span>
            </a>

            <a href="{{ route('koperasi.kategori.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-purple-500/10 hover:bg-purple-500/20 text-purple-700 dark:text-purple-400 border border-purple-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-tags-fill text-sm"></i>
                <span>Kategori Produk</span>
            </a>

            <a href="{{ route('koperasi.paket.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 border border-indigo-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-collection-fill text-sm"></i>
                <span>Paket Bundling</span>
            </a>

            <a href="{{ route('koperasi.stok.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-arrow-left-right text-sm"></i>
                <span>Mutasi Stok</span>
            </a>

            <a href="{{ route('koperasi.produk.create') }}" class="action-modal m3-btn-primary text-xs">
                <i class="bi bi-plus-circle-fill text-sm"></i>
                <span>Tambah Produk</span>
            </a>
        </div>
    </div>

    <!-- MAIN CARD: FILTER TOOLBAR & TABLE -->
    <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden flex flex-col relative z-10 shadow-2xs">

        <!-- Toolbar Filter & Search -->
        <div
            class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/70 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center border border-primary/20 shrink-0">
                    <i class="bi bi-box-seam text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                        Daftar Produk & Kitab
                    </h3>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                        Total {{ $produks->total() }} Produk Ditemukan
                    </p>
                </div>
            </div>

            <!-- Filter Controls Form -->
            <form id="filterFormProduk" action="{{ route('koperasi.produk.index') }}" method="GET"
                class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 w-full lg:w-auto">
                <!-- Filter Kategori -->
                <div class="w-full sm:w-44">
                    <select name="kategori_id" onchange="this.form.submit()"
                        class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id }}"
                                {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Stok -->
                <div class="w-full sm:w-36">
                    <select name="stok_filter" onchange="this.form.submit()"
                        class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer">
                        <option value="">Semua Stok</option>
                        <option value="menipis" {{ request('stok_filter') == 'menipis' ? 'selected' : '' }}>⚠️ Stok
                            Menipis</option>
                        <option value="habis" {{ request('stok_filter') == 'habis' ? 'selected' : '' }}>🚫 Stok Habis
                            (0)</option>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-56">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama / barcode..." class="m3-input-glass w-full text-xs font-semibold">
                    <button type="submit"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <i class="bi bi-search text-xs"></i>
                    </button>
                </div>

                @if (request()->hasAny(['kategori_id', 'stok_filter', 'search']))
                    <a href="{{ route('koperasi.produk.index') }}"
                        class="h-10 px-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-xs flex items-center justify-center shrink-0 transition-colors"
                        title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- TABEL DATA PRODUK CONTAINER (Auto Refreshed by custom-script.js) -->
        <div id="data-grid-container">
            @include('koperasi.produk.list', ['produks' => $produks])
        </div>

    </div>

</x-app-layout>
