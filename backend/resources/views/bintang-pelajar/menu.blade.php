<div
    class="flex gap-1.5 p-1 h-11 bg-zinc-100/80 dark:bg-zinc-900/80 rounded-xl w-full xl:w-max overflow-x-auto custom-scrollbar border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">

    {{-- TAB 1: BINTANG PELAJAR (PER UJIAN) --}}
    @php $isBintangPelajar = request()->routeIs('bintang-pelajar.*') || request()->is('*bintang-pelajar*'); @endphp
    <a href="{{ route('bintang-pelajar.index') }}"
        class="{{ $isBintangPelajar ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50' }} h-full px-4 rounded-lg text-xs font-black transition-all duration-200 whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0">
        <i class="bi bi-award text-sm"></i>
        <span>Bintang Ujian</span>
    </a>

    {{-- TAB 2: BINTANG MADRASAH (TAHUNAN) --}}
    @php $isBintangMadrasah = request()->routeIs('bintang-madrasah.*') || request()->is('*bintang-madrasah*'); @endphp
    <a href="{{ route('bintang-madrasah.index') }}"
        class="{{ $isBintangMadrasah ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50' }} h-full px-4 rounded-lg text-xs font-black transition-all duration-200 whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0">
        <i class="bi bi-star-fill text-sm"></i>
        <span>Bintang Madrasah</span>
    </a>

</div>

