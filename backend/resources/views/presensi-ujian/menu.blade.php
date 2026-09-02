<div
    class="flex gap-1.5 p-1 h-11 bg-zinc-100/80 dark:bg-zinc-900/80 rounded-xl w-full xl:w-max overflow-x-auto custom-scrollbar border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">

    {{-- TAB 1: PANTAU PROGRES --}}
    @php $isProgres = request()->routeIs('presensi-ujian.index'); @endphp
    <a href="{{ route('presensi-ujian.index') }}"
        class="{{ $isProgres ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50' }} h-full px-3.5 md:px-4 rounded-lg text-xs font-black transition-all duration-200 whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0">
        <i class="bi bi-speedometer text-sm"></i>
        <span>Pantau Progres</span>
    </a>

    {{-- TAB 2: INPUT PRESENSI --}}
    @php $isInput = request()->routeIs('presensi-ujian.input'); @endphp
    <a href="{{ route('presensi-ujian.input') }}"
        class="{{ $isInput ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50' }} h-full px-3.5 md:px-4 rounded-lg text-xs font-black transition-all duration-200 whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0">
        <i class="bi bi-person-check text-sm"></i>
        <span>Input Presensi</span>
    </a>

    {{-- TAB 3: REKAPITULASI --}}
    @php $isRekap = request()->routeIs('presensi-ujian.rekap'); @endphp
    <a href="{{ route('presensi-ujian.rekap') }}"
        class="{{ $isRekap ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50' }} h-full px-3.5 md:px-4 rounded-lg text-xs font-black transition-all duration-200 whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0">
        <i class="bi bi-file-spreadsheet text-sm"></i>
        <span>Rekapitulasi</span>
    </a>

    {{-- TAB 4: CETAK DOKUMEN --}}
    @php $isCetak = request()->routeIs('presensi-ujian.cetak-menu'); @endphp
    <a href="{{ route('presensi-ujian.cetak-menu') }}"
        class="{{ $isCetak ? 'bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50' }} h-full px-3.5 md:px-4 rounded-lg text-xs font-black transition-all duration-200 whitespace-nowrap flex items-center justify-center gap-1.5 flex-1 xl:flex-none shrink-0">
        <i class="bi bi-printer text-sm"></i>
        <span>Cetak Dokumen</span>
    </a>
</div>
