@section('title', 'Plotting Kenaikan')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 relative z-30">
        <div class="flex items-center gap-3 sm:gap-4">
            <!-- Back Button -->
            <a href="{{ route('rombongan-belajar.anggota', $ruangan->id) }}"
                class="w-10 h-10 bg-white/80 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-xl flex items-center justify-center transition-all duration-200 shadow-sm active:scale-95 shrink-0 outline-none"
                title="Kembali ke Rombongan Belajar">
                <i class="bi bi-arrow-left text-base font-bold"></i>
            </a>

            <div>
                <h2
                    class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                    Plotting Kenaikan Kelas
                </h2>
                <p
                    class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                    Pilih murid yang lulus/naik kelas untuk ditempatkan ke ruangan ini.
                </p>
            </div>
        </div>
    </div>

    <!-- Informasi Ruangan Target -->
    <div
        class="m3-glass-card p-4 sm:p-5 flex flex-col sm:flex-row gap-4 sm:gap-6 transition-colors mb-5 relative z-20 border-l-4 border-l-primary dark:border-l-primary-dark">
        <div class="flex-1">
            <p class="text-[10px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-0.5">Ruangan Target</p>
            <h4 class="text-base font-black text-primary dark:text-primary-dark">{{ $ruangan->nama_ruangan }}</h4>
        </div>
        <div class="flex-1">
            <p class="text-[10px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-0.5">Tahun Ajaran</p>
            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">
                {{ $ruangan->tahunPelajaran->nama_hijriyah ?? '-' }} H |
                {{ $ruangan->tahunPelajaran->nama_masehi ?? '-' }} M
            </h4>
        </div>
        <div class="flex-1">
            <p class="text-[10px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-0.5">Level</p>
            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $ruangan->level->nama_level ?? '-' }}</h4>
        </div>
    </div>

    @if (!$tahunLalu)
        <!-- Alert Warning -->
        <div
            class="m3-glass-card p-4 sm:p-5 border-amber-200/80 dark:border-amber-800/40 bg-amber-50/50 dark:bg-amber-950/20 flex items-start gap-3.5 mb-5 relative z-20">
            <i class="bi bi-exclamation-triangle-fill text-amber-500 dark:text-amber-400 text-xl shrink-0 mt-0.5"></i>
            <div>
                <strong class="font-extrabold text-amber-900 dark:text-amber-300 block mb-0.5 text-sm">Perhatian</strong>
                <p class="text-xs text-amber-800 dark:text-amber-400/90 leading-relaxed font-medium">
                    Tidak ditemukan data Tahun Ajaran sebelumnya. Plotting kenaikan tidak dapat dilakukan.
                </p>
            </div>
        </div>
    @else
        <!-- Card Tabel -->
        <div id="data-table-container"
            class="m3-glass-card overflow-hidden flex flex-col relative z-10">

            <!-- Header Table -->
            <div
                class="p-4 sm:p-4.5 border-b border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-black/30 flex flex-col sm:flex-row justify-between items-center gap-3 transition-colors duration-300">
                <div class="w-full sm:w-auto">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                        <i class="bi bi-list-check text-primary dark:text-primary-dark"></i>
                        Daftar Murid Eligible
                        <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 ml-1">(T.A {{ $tahunLalu->nama_tahun ?? '-' }})</span>
                    </h3>
                </div>

                <span
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 border border-sky-200/80 dark:border-sky-800/40 shrink-0">
                    <i class="bi bi-people-fill mr-1.5"></i> {{ $muridsKenaikan->count() }} Murid Tersedia
                </span>
            </div>

            <form action="{{ route('ruangan.store-plotting', $ruangan->id) }}" method="POST" id="formPlotting"
                class="flex flex-col m-0">
                @csrf

                <div class="overflow-x-auto custom-scrollbar relative z-10 w-full">
                    <table class="m3-table w-full text-left whitespace-nowrap">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center w-12 !px-3">
                                    <div class="flex items-center justify-center min-h-[36px] min-w-[36px]">
                                        <input type="checkbox" id="checkAll"
                                            class="w-4.5 h-4.5 rounded-md border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary dark:bg-black cursor-pointer transition-colors shadow-none">
                                    </div>
                                </th>
                                <th scope="col" class="text-center w-12">No</th>
                                <th scope="col">NIS / NISM</th>
                                <th scope="col" class="w-64">Nama Murid</th>
                                <th scope="col" class="text-center w-16">L/P</th>
                                <th scope="col">Status Sebelumnya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($muridsKenaikan as $index => $riwayat)
                                @php
                                    $murid = $riwayat->murid;
                                @endphp
                                <tr>
                                    <!-- CHECKBOX -->
                                    <td class="text-center !px-3">
                                        <div class="flex items-center justify-center min-h-[36px] min-w-[36px]">
                                            <input type="checkbox" name="murid_ids[]" value="{{ $murid->id }}"
                                                class="murid-checkbox w-4.5 h-4.5 rounded-md border-zinc-300 dark:border-zinc-700 text-primary focus:ring-primary dark:bg-black cursor-pointer transition-colors shadow-none">
                                        </div>
                                    </td>

                                    <!-- NO -->
                                    <td class="text-center">
                                        <span
                                            class="w-8 h-8 mx-auto flex items-center justify-center bg-zinc-100/80 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-extrabold border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>

                                    <!-- NIS/NISM -->
                                    <td>
                                        <div class="font-black text-zinc-900 dark:text-white text-xs sm:text-sm">
                                            {{ $murid->nis ?? '-' }}
                                        </div>
                                        <div
                                            class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 tracking-wider uppercase mt-0.5 flex items-center gap-1">
                                            <i class="bi bi-upc-scan"></i> {{ $murid->nism ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- NAMA -->
                                    <td class="font-extrabold text-zinc-900 dark:text-white text-xs sm:text-sm">
                                        {{ $murid->nama_lengkap ?? '-' }}
                                    </td>

                                    <!-- GENDER -->
                                    <td class="text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-black {{ ($murid->jenis_kelamin ?? '') == 'Laki-laki' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/40' : 'bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 border border-pink-200/60 dark:border-pink-800/40' }}">
                                            {{ ($murid->jenis_kelamin ?? '') == 'Laki-laki' ? 'L' : 'P' }}
                                        </span>
                                    </td>

                                    <!-- STATUS RIWAYAT -->
                                    <td>
                                        <div class="flex flex-col items-start gap-1">
                                            @if ($riwayat->status_keputusan == 'Naik Kelas')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/40 text-emerald-600 dark:text-emerald-400">
                                                    <i class="bi bi-arrow-up-circle-fill mr-1"></i> Naik Kelas
                                                </span>
                                            @elseif($riwayat->status_keputusan == 'Tinggal Kelas')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/40 text-rose-600 dark:text-rose-400">
                                                    <i class="bi bi-arrow-counterclockwise mr-1"></i> Tinggal Kelas
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300">
                                                    {{ $riwayat->status_keputusan }}
                                                </span>
                                            @endif

                                            <div class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                                                Dari: <strong class="text-zinc-700 dark:text-zinc-300">{{ $riwayat->ruanganAsal->nama_ruangan ?? 'Tidak Diketahui' }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <!-- EMPTY STATE -->
                                <tr>
                                    <td colspan="6" class="px-5 py-12">
                                        <x-empty-state icon="bi-inbox" title="Tidak Ada Murid Eligible"
                                            message="Semua murid yang memenuhi syarat mungkin sudah memiliki ruangan." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer / Tombol Submit -->
                @if ($muridsKenaikan->count() > 0)
                    <div
                        class="p-4 border-t border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-black/30 flex justify-end">
                        <button type="submit"
                            class="m3-btn-primary w-full sm:w-auto px-6 group/submit"
                            id="btnSubmit">
                            <i class="bi bi-check2-circle text-base transition-transform group-hover/submit:scale-110"></i>
                            <span>Simpan Murid ke Kelas Ini</span>
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkAll = document.getElementById('checkAll');
                const checkboxes = document.querySelectorAll('.murid-checkbox');
                const formPlotting = document.getElementById('formPlotting');
                const btnSubmit = document.getElementById('btnSubmit');

                // Fitur Check All
                if (checkAll) {
                    checkAll.addEventListener('change', function() {
                        checkboxes.forEach(function(checkbox) {
                            checkbox.checked = checkAll.checked;
                        });
                    });
                }

                // Konfirmasi sebelum submit
                if (formPlotting) {
                    formPlotting.addEventListener('submit', function(e) {
                        let checkedCount = document.querySelectorAll('.murid-checkbox:checked').length;

                        if (checkedCount === 0) {
                            e.preventDefault();
                            alert('Pilih minimal 1 murid untuk disimpan ke ruangan ini!');
                            return;
                        }

                        if (!confirm(`Anda yakin ingin memasukkan ${checkedCount} murid yang dipilih ke ruangan ini?`)) {
                            e.preventDefault();
                        } else {
                            btnSubmit.innerHTML =
                                '<i class="bi bi-arrow-repeat animate-spin text-base mr-1.5"></i> Menyimpan...';
                            btnSubmit.disabled = true;
                            btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');
                        }
                    });
                }
            });
        </script>
    @endpush

</x-app-layout>

