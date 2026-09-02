<div
    class="flex gap-1.5 p-1 h-11 bg-zinc-100/80 dark:bg-zinc-900/80 rounded-xl w-full xl:w-max overflow-x-auto custom-scrollbar border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">

    {{-- TAB 1: HARIAN --}}
    @php $isInputNilai = request()->is('*nilai-ujian') || request()->routeIs('nilai-ujian.index'); @endphp
    <a href="{{ route('nilai-ujian.index') }}"
        class="{{ $isInputNilai ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50' }} h-full px-4 md:px-5 rounded-lg text-xs font-black transition-all duration-200 whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0">
        <i class="bi bi-input-cursor-text text-sm"></i>
        <span>Input Nilai</span>
    </a>

    {{-- TAB 2: BULANAN --}}
    @php $isLeger = request()->is('*leger*'); @endphp
    <a href="{{ route('nilai-ujian.leger') }}"
        class="{{ $isLeger ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50' }} h-full px-4 md:px-5 rounded-lg text-xs font-black transition-all duration-200 whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0">
        <i class="bi bi-file-spreadsheet text-sm"></i>
        <span>Leger Nilai</span>
    </a>
</div>

