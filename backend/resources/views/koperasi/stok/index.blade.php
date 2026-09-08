@section('title', 'Mutasi & Kartu Stok Koperasi')
<x-app-layout>

    <div class="relative z-10">

        <!-- Header Page & Actions -->
        <div
            class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 relative z-10">
            <div>
                <h2
                    class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <div
                        class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 shrink-0">
                        <i class="bi bi-arrow-left-right text-lg"></i>
                    </div>
                    <span>Mutasi & Kartu Stok</span>
                </h2>
                <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Pencatatan riwayat stok masuk, penjualan kasir, dan penyesuaian stock opname.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('koperasi.stok.restock') }}"
                    class="action-modal inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-sm active:scale-95 transition-all">
                    <i class="bi bi-box-arrow-in-down text-sm"></i>
                    <span>+ Restock / Stok Masuk</span>
                </a>

                <a href="{{ route('koperasi.stok.opname') }}"
                    class="action-modal inline-flex items-center justify-center gap-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-400 border border-amber-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-4 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                    <i class="bi bi-clipboard-check-fill text-sm"></i>
                    <span>Stock Opname Fisik</span>
                </a>
            </div>
        </div>

        <!-- MAIN CARD -->
        <div class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden flex flex-col relative z-10 shadow-2xs">

            <!-- Toolbar Filter -->
            <div
                class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/70 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center border border-primary/20 shrink-0">
                        <i class="bi bi-journal-text text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                            Riwayat Mutasi Stok
                        </h3>
                        <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                            Total {{ $mutasis->total() }} Log Mutasi Tercatat
                        </p>
                    </div>
                </div>

                <form action="{{ route('koperasi.stok.index') }}" method="GET"
                    class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 w-full lg:w-auto">
                    <!-- Filter Produk -->
                    <div class="w-full sm:w-48">
                        <select name="produk_id" onchange="this.form.submit()"
                            class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer">
                            <option value="">Semua Produk</option>
                            @foreach ($produks as $pr)
                                <option value="{{ $pr->id }}"
                                    {{ request('produk_id') == $pr->id ? 'selected' : '' }}>
                                    {{ $pr->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Jenis Mutasi -->
                    <div class="w-full sm:w-40">
                        <select name="jenis_mutasi" onchange="this.form.submit()"
                            class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer">
                            <option value="">Semua Mutasi</option>
                            <option value="Stok_Masuk" {{ request('jenis_mutasi') == 'Stok_Masuk' ? 'selected' : '' }}>
                                Stok Masuk</option>
                            <option value="Penjualan" {{ request('jenis_mutasi') == 'Penjualan' ? 'selected' : '' }}>
                                Penjualan Kasir</option>
                            <option value="Penjualan_Paket"
                                {{ request('jenis_mutasi') == 'Penjualan_Paket' ? 'selected' : '' }}>Penjualan Paket
                            </option>
                            <option value="Penyesuaian_Opname"
                                {{ request('jenis_mutasi') == 'Penyesuaian_Opname' ? 'selected' : '' }}>Stock Opname
                            </option>
                            <option value="Batal_Penjualan"
                                {{ request('jenis_mutasi') == 'Batal_Penjualan' ? 'selected' : '' }}>Void / Batal Transaksi
                            </option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-48">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari ref / nota..." class="m3-input-glass w-full text-xs font-semibold">
                    </div>

                    @if (request()->hasAny(['produk_id', 'jenis_mutasi', 'search']))
                        <a href="{{ route('koperasi.stok.index') }}"
                            class="h-10 px-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-xs flex items-center justify-center shrink-0 transition-colors"
                            title="Reset">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- TABEL MUTASI STOK CONTAINER (Auto Refreshed by custom-script.js) -->
            <div id="data-grid-container">
                @include('koperasi.stok.list', ['mutasis' => $mutasis, 'produks' => $produks])
            </div>

        </div>

    </div>

</x-app-layout>
