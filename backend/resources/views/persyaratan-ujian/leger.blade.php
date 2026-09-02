@section('title', 'Leger Nilai Ujian')

<x-app-layout>
    <!-- HEADER -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-4 relative z-20">

        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('nilai-ujian.menu')
        </div>

        <!-- Area Form Pencarian Toolbar -->
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end print:hidden">
            <form action="{{ request()->url() }}" method="GET" id="formSelector"
                class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto">
                <input type="hidden" name="tahun_id" value="{{ $tahunPelajaranId }}">

                <!-- Filter Ruangan -->
                <div class="relative w-full sm:w-[190px] group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-door-open text-sm"></i>
                    </div>
                    <select name="ruangan_id" onchange="document.getElementById('formSelector').submit()"
                        class="m3-input-glass w-full !pl-9 !pr-9 appearance-none cursor-pointer">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($daftarRuangan as $r)
                            <option value="{{ $r->id }}"
                                {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Filter Ujian -->
                <div class="relative w-full sm:w-[190px] group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-file-earmark-check text-sm"></i>
                    </div>
                    <select name="ujian_id" {{ $daftarUjian->isEmpty() ? 'disabled' : '' }}
                        onchange="document.getElementById('formSelector').submit()"
                        class="m3-input-glass w-full !pl-9 !pr-9 appearance-none cursor-pointer disabled:opacity-50">
                        <option value="">-- Pilih Ujian --</option>
                        @foreach ($daftarUjian as $uj)
                            <option value="{{ $uj->id }}"
                                {{ request('ujian_id') == $uj->id ? 'selected' : '' }}>
                                {{ $uj->nama_ujian }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Tombol Submit (Cari) -->
                <div class="w-full sm:w-auto shrink-0">
                    <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-4 group/btn">
                        <i class="bi bi-search text-sm"></i>
                        <span class="sm:hidden xl:inline">Tampilkan</span>
                    </button>
                </div>

                <!-- Tombol Print -->
                @if (request('ruangan_id') && request('ujian_id') && count($kolomMapel) > 0)
                    <div
                        class="w-full sm:w-auto shrink-0 border-t sm:border-t-0 sm:border-l border-zinc-200/80 dark:border-zinc-800 pt-2.5 sm:pt-0 sm:pl-2.5">
                        <a href="{{ request()->fullUrlWithQuery(['print' => 'true']) }}" target="_blank"
                            class="h-10 inline-flex items-center justify-center px-4 rounded-xl bg-zinc-100/80 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200/80 dark:hover:bg-zinc-800 text-xs font-black transition-all hover:scale-[1.02] active:scale-95 outline-none border border-zinc-200/80 dark:border-zinc-800 shadow-2xs gap-1.5">
                            <i class="bi bi-printer text-sm"></i>
                            <span class="sm:hidden xl:inline">Cetak</span>
                        </a>
                    </div>
                @endif

            </form>
        </div>
    </div>

    <!-- AREA KONTEN UTAMA -->
    @if (request('ruangan_id') && request('ujian_id'))
        <div class="m3-glass-card overflow-hidden relative group animate-[modalFadeIn_0.2s_ease-out]">

            <!-- HEADER PRINT -->
            <div class="text-center mb-4 pt-6 hidden print:block">
                <h2 class="text-xl font-black uppercase tracking-wider text-black">Buku Leger Nilai Santri</h2>
                <p class="text-xs font-bold text-zinc-700 mt-1">Ruangan:
                    {{ $ruanganTerpilih->nama_ruangan ?? '-' }}
                    | {{ $ujianTerpilih->nama_ujian ?? '-' }}</p>
                <div class="w-28 border-b-2 border-black mx-auto mt-3 mb-2"></div>
            </div>

            @if (count($kolomMapel) == 0)
                <!-- STATE: BELUM ADA NILAI -->
                <div class="p-8">
                    <x-empty-state icon="bi-x-circle" title="Belum Ada Nilai"
                        message="Belum ada data nilai mata pelajaran yang masuk atau dipublikasikan untuk ujian dan ruangan ini." />
                </div>
            @else
                <!-- TABEL LEGER SPREADSHEET -->
                <div class="overflow-x-auto relative z-10 custom-scrollbar p-0">
                    <table class="m3-table w-full text-left whitespace-nowrap min-w-max">
                        <thead>
                            <tr>
                                <!-- Kolom Rank -->
                                <th scope="col"
                                    class="text-center w-16 border-r border-zinc-200/80 dark:border-zinc-800 sticky left-0 z-40 bg-zinc-50/95 dark:bg-zinc-950/95 backdrop-blur-md">
                                    Rank
                                </th>

                                <!-- Kolom Nama -->
                                <th scope="col"
                                    class="border-r border-zinc-200/80 dark:border-zinc-800 sticky left-[64px] z-40 bg-zinc-50/95 dark:bg-zinc-950/95 backdrop-blur-md w-64">
                                    Nama Lengkap
                                </th>

                                <!-- Kolom Dinamis Mapel -->
                                @foreach ($kolomMapel as $mapel)
                                    <th scope="col" class="text-center border-r border-zinc-200/80 dark:border-zinc-800 w-24"
                                        title="{{ $mapel }}">
                                        {{ strlen($mapel) > 10 ? substr($mapel, 0, 10) . '...' : $mapel }}
                                    </th>
                                @endforeach

                                <!-- Kolom Total & Rata-rata -->
                                <th scope="col"
                                    class="text-center border-r border-zinc-200/80 dark:border-zinc-800 text-primary dark:text-primary-dark bg-primary/5 dark:bg-primary-dark/5">
                                    Total
                                </th>
                                <th scope="col" class="text-center">Rata-Rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataLeger as $index => $row)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors duration-200 group/row">

                                    <!-- Sel Rank -->
                                    <td
                                        class="text-center border-r border-zinc-200/80 dark:border-zinc-800 sticky left-0 z-30 bg-white/95 dark:bg-zinc-950/95 group-hover/row:bg-zinc-50/90 dark:group-hover/row:bg-zinc-800/90 transition-colors align-middle">
                                        @if ($index == 0)
                                            <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-amber-500/20 text-amber-600 dark:text-amber-400 font-black text-xs mx-auto border border-amber-500/30 shadow-2xs"
                                                title="Peringkat 1">
                                                <i class="bi bi-trophy-fill text-xs"></i>
                                            </div>
                                        @elseif($index == 1)
                                            <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-black text-xs mx-auto border border-slate-300 dark:border-slate-600 shadow-2xs"
                                                title="Peringkat 2">2</div>
                                        @elseif($index == 2)
                                            <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-amber-700/20 text-amber-700 dark:text-amber-500 font-black text-xs mx-auto border border-amber-700/30 shadow-2xs"
                                                title="Peringkat 3">3</div>
                                        @else
                                            <span
                                                class="text-zinc-500 dark:text-zinc-400 font-black text-xs">{{ $index + 1 }}</span>
                                        @endif
                                    </td>

                                    <!-- Sel Nama -->
                                    <td
                                        class="border-r border-zinc-200/80 dark:border-zinc-800 sticky left-[64px] z-30 bg-white/95 dark:bg-zinc-950/95 group-hover/row:bg-zinc-50/90 dark:group-hover/row:bg-zinc-800/90 transition-colors align-middle">
                                        <div
                                            class="font-black text-xs text-zinc-900 dark:text-white tracking-tight truncate w-60">
                                            {{ $row->murid->nama_lengkap }}
                                        </div>
                                    </td>

                                    <!-- Sel Nilai Dinamis -->
                                    @foreach ($kolomMapel as $mapel)
                                        @php $itemNilai = $row->nilai_per_mapel[$mapel]; @endphp
                                        <td
                                            class="text-center border-r border-zinc-200/80 dark:border-zinc-800 align-middle">
                                            @if ($itemNilai['nilai'] !== null)
                                                <div
                                                    class="font-black text-xs {{ $itemNilai['nilai'] < 60 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-800 dark:text-zinc-200' }}">
                                                    {{ $itemNilai['nilai'] }}
                                                    @if (!$itemNilai['is_published'])
                                                        <sup class="text-amber-500 font-black text-[9px] ml-0.5"
                                                            title="Nilai masih Draft Guru">*</sup>
                                                    @endif
                                                </div>
                                            @else
                                                <span
                                                    class="text-zinc-300 dark:text-zinc-700 font-black text-xs opacity-50">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <!-- Sel Total -->
                                    <td
                                        class="text-center border-r border-zinc-200/80 dark:border-zinc-800 bg-primary/5 dark:bg-primary-dark/5 align-middle">
                                        <span
                                            class="inline-flex items-center justify-center min-w-[32px] py-0.5 px-2 rounded-md bg-emerald-100/80 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-black text-xs border border-emerald-200/80 dark:border-emerald-800/40">
                                            {{ $row->total }}
                                        </span>
                                    </td>

                                    <!-- Sel Rata-rata -->
                                    <td class="text-center align-middle">
                                        <span class="font-black text-xs text-zinc-900 dark:text-white">
                                            {{ $row->rata_rata }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer Keterangan Draft -->
                <div
                    class="px-5 py-3 bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center gap-1.5 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider print:hidden">
                    <span class="text-amber-500 text-xs font-black leading-none">*</span>
                    <span>= Menandakan nilai masih berstatus DRAFT (Belum dipublikasikan ke Rapor oleh Guru).</span>
                </div>
            @endif
        </div>
    @else
        <!-- STATE AWAL PANDUAN PENGGUNAAN -->
        <x-empty-state icon="bi-grid-3x3-gap" title="Kalkulator Leger & Ranking"
            message="Pilih Ruangan Kelas dan Pelaksanaan Ujian pada filter di atas untuk menghasilkan matriks nilai (leger) beserta peringkat santri secara otomatis." />
    @endif

</x-app-layout>

