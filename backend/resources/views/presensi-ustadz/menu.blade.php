<div
    class="flex items-center gap-1.5 p-1.5 h-11 m3-glass-card !rounded-2xl w-full xl:w-max overflow-x-auto custom-scrollbar shadow-2xs">

    {{-- TAB 1: HARIAN --}}
    @php $isharian = request()->is('*presensi-ustadz') || request()->routeIs('presensi-murid.index'); @endphp
    <a href="{{ route('presensi-ustadz.index') }}"
        class="{{ $isharian ? 'bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark border border-primary/20 shadow-2xs font-black' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-100/50 dark:hover:bg-zinc-800/50 font-bold border border-transparent' }} h-full px-4 md:px-5 rounded-xl text-xs transition-all whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0 outline-none">
        <i class="bi bi-person-bounding-box text-xs"></i>
        Harian
    </a>

    {{-- TAB 2: BULANAN --}}
    @php $isBulanan = request()->is('*bulanan*'); @endphp
    <a href="{{ route('presensi-ustadz.bulanan') }}"
        class="{{ $isBulanan ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 shadow-2xs font-black' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-100/50 dark:hover:bg-zinc-800/50 font-bold border border-transparent' }} h-full px-4 md:px-5 rounded-xl text-xs transition-all whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0 outline-none">
        <i class="bi bi-calendar-month text-xs"></i>
        Bulanan
    </a>

    {{-- TAB 3: REKAP SEMESTER --}}
    @php $isRekap = request()->is('*rekap-semua*'); @endphp
    <a href="{{ route('presensi-ustadz.rekapSemua') }}"
        class="{{ $isRekap ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 shadow-2xs font-black' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-100/50 dark:hover:bg-zinc-800/50 font-bold border border-transparent' }} h-full px-4 md:px-5 rounded-xl text-xs transition-all whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0 outline-none">
        <i class="bi bi-file-text text-xs"></i>
        Rekap
    </a>

</div>

