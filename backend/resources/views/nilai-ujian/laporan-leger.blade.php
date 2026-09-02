@section('title', 'Leger Nilai Ujian')

<x-app-layout>
    <!-- HEADER -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-4 relative z-20">

        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('nilai-ujian.menu')
        </div>

        <!-- Area Form Pencarian (Sejajar bergaya Toolbar) -->
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

                <!-- Tombol Print (Muncul Jika Data Lengkap) -->
                @if (request('ruangan_id') && request('ujian_id') && count($kolomMapel) > 0)
                    <div
                        class="w-full sm:w-auto shrink-0 border-t sm:border-t-0 sm:border-l border-zinc-200/80 dark:border-zinc-800 pt-2.5 sm:pt-0 sm:pl-2.5">
                        <a href="{{ request()->fullUrlWithQuery(['print' => 'true']) }}" target="_blank"
                            class="m3-btn-secondary w-full sm:w-auto h-10 px-4 group/btn">
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
        <div
            class="m3-glass-card overflow-hidden relative group animate-[modalFadeIn_0.2s_ease-out]">

            <!-- HEADER PRINT -->
            <div class="text-center mb-4 pt-8 hidden print:block">
                <h2 class="text-2xl font-black uppercase tracking-widest text-black">Buku Leger Nilai Santri</h2>
                <p class="text-xs font-bold text-zinc-700 mt-1">Ruangan:
                    {{ $ruanganTerpilih->nama_ruangan ?? '-' }}
                    | {{ $ujianTerpilih->nama_ujian ?? '-' }}</p>
                <div class="w-32 border-b-2 border-black mx-auto mt-4 mb-2"></div>
            </div>

            @if (count($kolomMapel) == 0)
                <!-- STATE: BELUM ADA NILAI -->
                <div class="py-12 text-center relative z-10 print:hidden">
                    <div
                        class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border border-zinc-200/80 dark:border-zinc-700 rounded-xl flex items-center justify-center text-xl mb-3 mx-auto shadow-2xs">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 class="text-base font-black text-zinc-800 dark:text-zinc-200 tracking-tight">Belum Ada Nilai</h3>
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-1 max-w-sm mx-auto">Belum ada data nilai mata
                        pelajaran yang masuk atau dipublikasikan untuk ujian dan ruangan ini.</p>
                </div>
            @else
                <!-- TABEL LEGER SPREADSHEET -->
                <div class="overflow-x-auto relative z-10 custom-scrollbar p-0">
                    <table
                        class="w-full text-left border-separate border-spacing-0 text-xs whitespace-nowrap min-w-max">

                        <!-- HEADER TABEL -->
                        <thead class="bg-zinc-50/95 dark:bg-zinc-950/95 sticky top-0 z-40">
                            <tr
                                class="text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">

                                <!-- Kolom Rank -->
                                <th
                                    class="py-3 px-0 text-center border-r border-b border-zinc-200/80 dark:border-zinc-800 sticky left-0 z-40 bg-zinc-50/95 dark:bg-zinc-950/95 backdrop-blur-md w-12 min-w-[3rem] max-w-[3rem] md:w-16 md:min-w-[4rem] md:max-w-[4rem] align-middle">
                                    Rank
                                </th>

                                <!-- Kolom Nama -->
                                <th
                                    class="py-3 px-3.5 border-r border-b border-zinc-200/80 dark:border-zinc-800 sticky left-12 md:left-16 z-40 bg-zinc-50/95 dark:bg-zinc-950/95 backdrop-blur-md w-[180px] min-w-[180px] max-w-[180px] md:w-[260px] md:min-w-[260px] md:max-w-[260px] align-middle">
                                    Nama Santri
                                </th>

                                <!-- Kolom Dinamis Mapel -->
                                @foreach ($kolomMapel as $jadwalId => $mapel)
                                    <th class="py-3 px-2 text-center border-r border-b border-zinc-200/80 dark:border-zinc-800 w-24 bg-zinc-50 dark:bg-zinc-900/80 align-middle"
                                        title="{{ $mapel }}">
                                        {{ strlen($mapel) > 10 ? substr($mapel, 0, 10) . '...' : $mapel }}
                                    </th>
                                @endforeach

                                <!-- Kolom Total & Rata-rata -->
                                <th
                                    class="py-3 px-3.5 text-center border-r border-b border-zinc-200/80 dark:border-zinc-800 text-primary dark:text-primary-dark bg-primary/5 dark:bg-primary-dark/10 align-middle">
                                    Total
                                </th>
                                <th
                                    class="py-3 px-3.5 text-center border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/80 align-middle">
                                    Rata-Rata
                                </th>
                            </tr>
                        </thead>

                        <!-- BODY TABEL -->
                        <tbody>
                            @foreach ($dataLeger as $index => $row)
                                <tr
                                    class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors group/row">

                                    <!-- Sel Rank -->
                                    <td
                                        class="py-2 px-0 text-center border-r border-b border-zinc-200/80 dark:border-zinc-800 sticky left-0 z-30 bg-white dark:bg-zinc-900 group-hover/row:bg-zinc-50 dark:group-hover/row:bg-zinc-800/80 transition-colors align-middle w-12 min-w-[3rem] max-w-[3rem] md:w-16 md:min-w-[4rem] md:max-w-[4rem]">
                                        @if ($index == 0)
                                            <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 font-black text-xs mx-auto border border-amber-200/80 dark:border-amber-500/30 shadow-2xs"
                                                title="Peringkat 1">
                                                <i class="bi bi-trophy-fill"></i>
                                            </div>
                                        @elseif($index == 1)
                                            <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 font-black text-xs mx-auto border border-slate-200/80 dark:border-slate-700 shadow-2xs"
                                                title="Peringkat 2">2</div>
                                        @elseif($index == 2)
                                            <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 font-black text-xs mx-auto border border-amber-200/80 dark:border-amber-800/40 shadow-2xs"
                                                title="Peringkat 3">3</div>
                                        @else
                                            <span
                                                class="text-zinc-500 dark:text-zinc-400 font-bold text-xs">{{ $index + 1 }}</span>
                                        @endif
                                    </td>

                                    <!-- Sel Nama -->
                                    <td
                                        class="py-2 px-3.5 border-r border-b border-zinc-200/80 dark:border-zinc-800 sticky left-12 md:left-16 z-30 bg-white dark:bg-zinc-900 group-hover/row:bg-zinc-50 dark:group-hover/row:bg-zinc-800/80 transition-colors align-middle w-[180px] min-w-[180px] max-w-[180px] md:w-[260px] md:min-w-[260px] md:max-w-[260px]">
                                        <div class="font-black text-xs text-zinc-900 dark:text-zinc-100 tracking-tight truncate max-w-[150px] md:max-w-[230px]"
                                            title="{{ $row->murid->nama_lengkap }}">
                                            {{ $row->murid->nama_lengkap }}
                                        </div>
                                    </td>

                                    <!-- Sel Nilai Dinamis -->
                                    @foreach ($kolomMapel as $jadwalId => $mapel)
                                        @php $itemNilai = $row->nilai_per_mapel[$jadwalId]; @endphp
                                        <td
                                            class="py-2 px-3 text-center border-r border-b border-zinc-200/80 dark:border-zinc-800 align-middle">
                                            @if ($itemNilai['nilai'] !== null)
                                                <div
                                                    class="font-black text-xs {{ $itemNilai['nilai'] < 60 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-800 dark:text-zinc-200' }}">
                                                    {{ $itemNilai['nilai'] }}
                                                    @if (!$itemNilai['is_published'])
                                                        <sup class="text-amber-500 font-black text-[9px] ml-0.5"
                                                            title="Nilai masih Draft">*</sup>
                                                    @endif
                                                </div>
                                            @else
                                                <span
                                                    class="text-zinc-300 dark:text-zinc-700 font-bold text-xs opacity-60">-</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <!-- Sel Total -->
                                    <td
                                        class="py-2 px-3.5 text-center border-r border-b border-zinc-200/80 dark:border-zinc-800 bg-primary/5 dark:bg-primary-dark/5 align-middle">
                                        <div
                                            class="inline-flex items-center justify-center min-w-[32px] py-0.5 px-2 rounded-md bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark font-black text-xs border border-primary/20 dark:border-primary-dark/30">
                                            {{ $row->total }}
                                        </div>
                                    </td>

                                    <!-- Sel Rata-rata -->
                                    <td
                                        class="py-2 px-3.5 text-center border-b border-zinc-200/80 dark:border-zinc-800 align-middle">
                                        <span class="font-black text-xs text-zinc-800 dark:text-zinc-200">
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
                    class="px-5 py-3 bg-zinc-50/80 dark:bg-zinc-950/60 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center gap-2 text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider print:hidden">
                    <span class="text-amber-500 text-sm leading-none font-black">*</span>
                    <span>= Menandakan nilai masih berstatus DRAFT (Belum dipublikasikan ke Rapor oleh Guru).</span>
                </div>
            @endif
        </div>
    @else
        <!-- STATE AWAL PANDUAN PENGGUNAAN -->
        <x-empty-state icon="bi-calculator" title="Kalkulator Leger & Ranking"
            message="Pilih Ruangan Kelas dan Pelaksanaan Ujian pada filter di atas untuk menghasilkan matriks nilai (leger) beserta peringkat santri secara otomatis." />
    @endif

</x-app-layout>

