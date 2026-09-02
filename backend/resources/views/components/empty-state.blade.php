@props([
    'icon' => 'bi-inbox', // Ikon default saat data kosong
    'title' => 'Data Kosong', // Judul default
    'message' => 'Belum ada data yang ditambahkan.', // Pesan default
    'searchParam' => 'search', // Parameter URL pencarian (default: ?search=)
    'searchIcon' => 'bi-search', // Ikon saat pencarian tidak ditemukan
    'searchTitle' => 'Pencarian Tidak Ditemukan',
    'searchMessage' => 'Coba gunakan kata kunci lain.',
])

@php
    // Mengecek apakah ada parameter pencarian di URL (contoh: ?search=budi)
    $isSearching = request()->has($searchParam) && request($searchParam) != '';

    // Menentukan data mana yang akan ditampilkan (Data Kosong vs Pencarian Gagal)
    $displayIcon = $isSearching ? $searchIcon : $icon;
    $displayTitle = $isSearching ? $searchTitle : $title;
    $displayMessage = $isSearching ? $searchMessage : $message;
@endphp

<div
    {{ $attributes->merge(['class' => 'bg-white/60 dark:bg-zinc-950/40 backdrop-blur-md rounded-2xl md:rounded-3xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 p-8 md:p-12 text-center flex flex-col items-center justify-center relative overflow-hidden transition-all duration-300']) }}>

    <!-- Kotak Ikon Squircle M3 -->
    <div
        class="w-14 h-14 bg-primary/10 dark:bg-primary-dark/10 border border-primary/20 dark:border-primary-dark/20 text-primary dark:text-primary-dark rounded-2xl flex items-center justify-center mb-4 shadow-sm dark:shadow-none transition-transform duration-300 hover:scale-105">
        <i class="{{ $displayIcon }} text-2xl"></i>
    </div>

    <!-- Judul -->
    <h3 class="text-base md:text-lg font-bold text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
        {{ $displayTitle }}
    </h3>

    <!-- Pesan -->
    <p
        class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-1 transition-colors duration-300 max-w-sm mx-auto leading-relaxed">
        {{ $displayMessage }}
    </p>

    {{-- Slot tambahan (opsional) misal tombol Tambah Data --}}
    @if ($slot->isNotEmpty())
        <div class="mt-5 flex items-center justify-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
