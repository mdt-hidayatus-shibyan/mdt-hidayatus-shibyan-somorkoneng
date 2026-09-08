@section('title', 'Kategori Produk Koperasi')
<x-app-layout>

    <div class="space-y-6">

        <!-- Header Page & Actions -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 relative z-10">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20 shrink-0">
                        <i class="bi bi-tags-fill text-lg"></i>
                    </div>
                    <span>Kategori Produk Koperasi</span>
                </h2>
                <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Kelola kategori kitab, seragam, atribut, dan alat tulis untuk toko koperasi madrasah.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('koperasi.produk.index') }}"
                    class="m3-btn-secondary text-xs">
                    <i class="bi bi-box-seam text-sm text-primary"></i>
                    <span>Kembali ke Master Produk</span>
                </a>

                <a href="{{ route('koperasi.kategori.create') }}"
                    class="action-modal m3-btn-primary text-xs">
                    <i class="bi bi-plus-circle-fill text-sm"></i>
                    <span>Tambah Kategori</span>
                </a>
            </div>
        </div>

        <!-- MAIN CARD: TABEL KATEGORI (Auto refreshed by custom-script.js) -->
        <div id="data-grid-container" class="m3-glass-card rounded-2xl md:rounded-3xl overflow-hidden flex flex-col relative z-10 shadow-sm dark:shadow-none">
            @include('koperasi.kategori.list', ['kategoris' => $kategoris])
        </div>

    </div>

</x-app-layout>
