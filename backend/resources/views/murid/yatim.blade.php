@section('title', 'Data Murid Yatim')

<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10 no-print">

        <div class="flex items-center gap-3.5">
            <!-- Back Button -->
            <a href="{{ route('murid.index') }}"
                class="m3-btn-secondary w-10 h-10 !p-0 inline-flex items-center justify-center shadow-2xs shrink-0"
                title="Kembali">
                <i class="bi bi-arrow-left text-base"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    Data Murid Yatim <span class="text-primary dark:text-primary-dark font-black">({{ $tahunAktif->nama_hijriyah ?? 'Semua Tahun' }})</span>
                </h2>
                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5 uppercase tracking-wider">
                    Santri aktif dengan status ayah meninggal & usia di bawah 16 tahun
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2.5 w-full md:w-auto mt-2 md:mt-0">
            <a href="{{ route('murid.downloadYatim') }}" class="m3-btn-primary h-10 w-full md:w-auto px-5 group/btn">
                <i class="bi bi-file-earmark-pdf-fill mr-1.5 text-sm transition-transform group-hover/btn:-translate-y-0.5"></i>
                <span>Download PDF</span>
            </a>
        </div>

    </div>

    <!-- Data Table Container -->
    <div class="m3-glass-card overflow-hidden flex flex-col relative z-10 print-container">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="m3-table whitespace-nowrap">
                <thead>
                    <tr>
                        <th scope="col" class="text-center w-12">No</th>
                        <th scope="col" class="w-16">NISM</th>
                        <th scope="col">Ruangan</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col" class="text-center w-12">L/P</th>
                        <th scope="col">Usia</th>
                        <th scope="col">Nama Ibu Kandung</th>
                        <th scope="col">Dusun/Kampung</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($murids as $index => $murid)
                        @php
                            $umur = $murid->tanggal_lahir ? \Carbon\Carbon::parse($murid->tanggal_lahir)->age : '-';
                            $namaRuangan = $murid->ruangans->first()->nama_ruangan ?? '-';
                        @endphp

                        <tr class="group/tr">

                            <td class="text-center">
                                <span
                                    class="w-7 h-7 mx-auto flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-lg text-xs font-black shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <td>
                                <span class="font-mono text-xs font-black text-zinc-700 dark:text-zinc-300">
                                    {{ $murid->nism }}
                                </span>
                            </td>

                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-primary/10 text-primary dark:text-primary-dark border border-primary/20">
                                    {{ $namaRuangan }}
                                </span>
                            </td>

                            <td>
                                <span class="text-xs font-black text-zinc-900 dark:text-white tracking-tight">
                                    {{ $murid->nama_lengkap }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-[10px] font-black {{ $murid->jenis_kelamin == 'L' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' }}">
                                    {{ $murid->jenis_kelamin }}
                                </span>
                            </td>

                            <td>
                                <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                    {{ $umur }} Thn
                                </span>
                            </td>

                            <td>
                                <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                    {{ $murid->nama_ibu ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="text-xs font-bold text-zinc-600 dark:text-zinc-400">
                                    {{ $murid->waliMurid->kampung->nama_kampung ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 rounded-2xl flex items-center justify-center text-zinc-400 dark:text-zinc-500 mb-3 shadow-2xs">
                                        <i class="bi bi-heartbreak text-2xl"></i>
                                    </div>
                                    <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">
                                        Tidak Ada Data Yatim
                                    </h3>
                                    <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5">
                                        Belum ada data murid yatim yang terdaftar pada tahun ajaran ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>

