@section('title', 'Pengaturan Akademik')

<x-app-layout>
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-20">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Pengaturan Akademik
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Kelola konfigurasi bobot nilai, poin presensi, dan kalender semester.
            </p>
        </div>

        <!-- GLOBAL FILTER TAHUN PELAJARAN M3 -->
        <div class="flex-shrink-0 w-full md:w-auto">
            <div class="relative m3-glass-card p-1.5 shadow-2xs">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10 text-zinc-400">
                    <i class="bi bi-calendar-range text-xs"></i>
                </div>
                <select name="global_tahun_id" onchange="window.location.href='?tp_id='+this.value"
                    class="m3-input-glass w-full md:w-72 !pl-9 !pr-8 cursor-pointer appearance-none text-xs font-bold">
                    @foreach ($tahunPelajarans as $tp)
                        <option value="{{ $tp->id }}" {{ $selectedTpId == $tp->id ? 'selected' : '' }}>
                            {{ $tp->nama_hijriyah }} H | {{ $tp->nama_masehi }} M
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                    <i class="bi bi-chevron-down text-[10px] font-black"></i>
                </div>
            </div>
        </div>
    </div>

    <div id="data-grid-container" class="mb-10 relative z-10">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white flex items-center gap-2 mb-4">
            <div
                class="w-7 h-7 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs shrink-0">
                <i class="bi bi-calendar3"></i>
            </div>
            Kalender Akademik (Semester)
        </h3>

        @foreach ($tahunPelajarans as $tp)
            <div id="tp-card-{{ $tp->id }}"
                class="tp-card {{ $selectedTpId == $tp->id ? 'block' : 'hidden' }} m3-glass-card p-5 sm:p-7 rounded-3xl shadow-2xs">

                <!-- HEADER TAHUN PELAJARAN -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6 pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                    <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight flex items-center">
                        <i class="bi bi-journal-bookmark-fill text-emerald-600 dark:text-emerald-400 mr-2.5"></i>
                        Tahun Pelajaran: {{ $tp->nama_hijriyah }} H | {{ $tp->nama_masehi }} M
                    </h3>
                </div>

                <!-- SEKSI 1: DAFTAR SEMESTER -->
                <div class="mb-3.5 flex items-center justify-between">
                    <h4 class="text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                        Daftar Semester
                    </h4>
                    @can('create pengaturan-akademik')
                        <a href="{{ route('pengaturan-akademik.create-semester', ['tahun_id' => $tp->id]) }}"
                            class="action-modal px-3.5 py-1.5 rounded-xl text-xs font-black bg-white/40 dark:bg-black/40 text-zinc-700 dark:text-zinc-300 hover:text-emerald-600 dark:hover:text-emerald-400 border border-zinc-200 dark:border-zinc-700 transition-colors flex items-center gap-1.5 outline-none shadow-2xs">
                            <i class="bi bi-plus-lg text-xs"></i> <span>Tambah Semester</span>
                        </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    @forelse($tp->semesters as $semester)
                        <div
                            class="m3-glass-card rounded-2xl p-4 sm:p-5 relative overflow-hidden shadow-2xs hover:border-emerald-500/40 transition-all flex flex-col justify-between gap-4">

                            @if ($semester->is_active)
                                <div
                                    class="absolute top-0 left-0 w-full h-1 bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]">
                                </div>
                            @endif

                            <div class="flex justify-between items-start gap-3">
                                <div class="flex flex-col gap-1.5">
                                    <h4
                                        class="font-black text-base text-zinc-900 dark:text-white tracking-tight leading-tight">
                                        {{ $semester->nama_semester }}
                                    </h4>

                                    @if ($semester->is_active)
                                        <div>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[9px] font-black rounded-lg uppercase tracking-wider">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                                Semester Aktif
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    @can('update pengaturan-akademik')
                                        <a href="{{ route('pengaturan-akademik.edit-semester', $semester->id) }}"
                                            class="action-modal w-7 h-7 rounded-xl bg-white/40 dark:bg-black/40 text-amber-500 hover:bg-amber-500/10 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center transition-all outline-none shadow-2xs"
                                            title="Edit Semester">
                                            <i class="bi bi-pencil-fill text-[10px]"></i>
                                        </a>
                                    @endcan

                                    @if (!$semester->is_active)
                                        @can('update pengaturan-akademik')
                                            <x-toggle-status :is-active="$semester->is_active" :url="route('pengaturan-akademik.activate-semester', $semester->id)" />
                                        @endcan
                                    @endif

                                    @can('delete pengaturan-akademik')
                                        <form action="{{ route('pengaturan-akademik.destroy-semester', $semester->id) }}"
                                            method="POST" class="m-0 delete-ajax">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-7 h-7 rounded-xl bg-white/40 dark:bg-black/40 text-rose-500 hover:bg-rose-500/10 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center transition-all outline-none shadow-2xs"
                                                title="Hapus Semester">
                                                <i class="bi bi-trash-fill text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>

                            <div
                                class="bg-white/40 dark:bg-black/40 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 flex items-center justify-between shadow-2xs">

                                <!-- Tanggal Mulai -->
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 border border-sky-500/20 text-xs">
                                        <i class="bi bi-calendar2-event-fill"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[8px] font-black uppercase tracking-wider text-zinc-400 mb-0.5">Mulai</span>
                                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                                            {{ $semester->tanggal_mulai ? \Carbon\Carbon::parse($semester->tanggal_mulai)->translatedFormat('d M Y') : 'Belum diatur' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="w-px h-6 bg-zinc-200 dark:bg-zinc-800"></div>

                                <!-- Tanggal Selesai -->
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/20 text-xs">
                                        <i class="bi bi-calendar2-check-fill"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-[8px] font-black uppercase tracking-wider text-zinc-400 mb-0.5">Selesai</span>
                                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                                            {{ $semester->tanggal_selesai ? \Carbon\Carbon::parse($semester->tanggal_selesai)->translatedFormat('d M Y') : 'Belum diatur' }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <x-empty-state icon="bi-folder-x" title="Belum Ada Semester"
                                message="Belum ada semester yang diatur untuk tahun pelajaran ini." />
                        </div>
                    @endforelse
                </div>

                <!-- SEKSI 2: DAFTAR BULAN HIJRIYAH -->
                <div
                    class="mb-3.5 flex items-center justify-between mt-8 pt-6 border-t border-zinc-200/80 dark:border-zinc-800">
                    <h4 class="text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                        Periode Bulan Hijriyah
                    </h4>
                    @can('create pengaturan-akademik')
                        <a href="{{ route('pengaturan-akademik.create-bulan', ['tahun_id' => $tp->id]) }}"
                            class="action-modal px-3.5 py-1.5 rounded-xl text-xs font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-colors flex items-center gap-1.5 outline-none shadow-2xs">
                            <i class="bi bi-calendar-plus text-xs"></i> <span>Plotting Bulan</span>
                        </a>
                    @endcan
                </div>

                <div class="m3-glass-card rounded-2xl overflow-hidden shadow-2xs">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr
                                class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-100/60 dark:bg-zinc-800/60">
                                <th class="p-3">Bulan Hijriyah</th>
                                <th class="p-3">Periode Masehi</th>
                                <th class="p-3 text-center w-20">Status</th>
                                <th class="p-3 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800">
                            @forelse($tp->bulanHijriyahs->sortBy('urutan') as $bulan)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors group">
                                    <td class="p-3 font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ $bulan->urutan }}. {{ $bulan->nama_bulan }} {{ $bulan->tahun_hijriyah }}
                                    </td>
                                    <td class="p-3 text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                                        {{ \Carbon\Carbon::parse($bulan->tanggal_mulai_masehi)->format('d M Y') }}
                                        <span class="opacity-50 mx-1">-</span>
                                        {{ \Carbon\Carbon::parse($bulan->tanggal_selesai_masehi)->format('d M Y') }}
                                    </td>
                                    <td class="text-center p-3">
                                        <x-toggle-status :is-active="$bulan->is_active" :url="route('pengaturan-akademik.activate-bulan', $bulan->id)" />
                                    </td>
                                    <td class="p-3">
                                        <div
                                            class="flex justify-center items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @can('update pengaturan-akademik')
                                                <a href="{{ route('pengaturan-akademik.edit-bulan', $bulan->id) }}"
                                                    class="action-modal w-6 h-6 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 hover:bg-sky-500/20 flex items-center justify-center transition-colors shadow-2xs"
                                                    title="Edit Tanggal">
                                                    <i class="bi bi-pencil-fill text-[9px]"></i>
                                                </a>
                                            @endcan
                                            @can('delete pengaturan-akademik')
                                                <form action="{{ route('pengaturan-akademik.destroy-bulan', $bulan->id) }}"
                                                    method="POST" class="m-0 delete-ajax">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="w-6 h-6 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 hover:bg-rose-500/20 flex items-center justify-center transition-colors shadow-2xs"
                                                        title="Hapus Bulan">
                                                        <i class="bi bi-trash-fill text-[9px]"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center">
                                        <x-empty-state icon="bi-calendar-x" title="Belum Ada Bulan Hijriyah"
                                            message="Belum ada data bulan yang diplot untuk tahun pelajaran ini." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        @endforeach
    </div>

    <!-- DIVIDER -->
    <hr class="my-8 border-zinc-200/80 dark:border-zinc-800 relative z-10">

    <!-- SECTION 2: BOBOT NILAI & POIN -->
    <div class="mb-10 space-y-5 relative z-10">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white flex items-center gap-2 mb-4">
            <div
                class="w-7 h-7 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs shrink-0">
                <i class="bi bi-sliders"></i>
            </div>
            Bobot Nilai & Poin
        </h3>

        <form action="{{ route('pengaturan-akademik.update-konfig') }}" method="POST" class="ajax-form">
            @csrf @method('PUT')
            <input type="hidden" name="tahun_pelajaran_id" value="{{ $selectedTpId }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 md:gap-6">
                <!-- CARD: Persentase Kenaikan Kelas -->
                <div class="m3-glass-card p-5 sm:p-7 rounded-3xl shadow-2xs flex flex-col">
                    <div class="flex items-center gap-3 mb-5 pb-3.5 border-b border-zinc-200/80 dark:border-zinc-800">
                        <div
                            class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 flex items-center justify-center text-sm shrink-0">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <h3 class="font-black text-sm md:text-base text-zinc-900 dark:text-white tracking-tight">
                            Persentase Kenaikan Kelas</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label
                                class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                Bobot Ujian (%)
                            </label>
                            <input type="number" step="0.01" name="bobot_imda"
                                value="{{ $konfig->bobot_imda }}" class="m3-input-glass w-full text-xs font-bold">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                Bobot Akhlaq (%)
                            </label>
                            <input type="number" step="0.01" name="bobot_akhlaq"
                                value="{{ $konfig->bobot_akhlaq }}" class="m3-input-glass w-full text-xs font-bold">
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-zinc-200/80 dark:border-zinc-800">
                        <p
                            class="text-[10px] text-zinc-400 mb-3 font-black tracking-wider flex items-center gap-1 uppercase">
                            <i class="bi bi-info-circle-fill text-emerald-600 dark:text-emerald-400"></i> Rincian Bobot
                            Akhlaq
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Porsi Presensi (%)
                                </label>
                                <input type="number" step="0.01" name="bobot_presensi"
                                    value="{{ $konfig->bobot_presensi }}"
                                    class="m3-input-glass w-full text-xs font-bold !border-emerald-500/30 text-emerald-600 dark:text-emerald-400">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Porsi Pelanggaran (%)
                                </label>
                                <input type="number" step="0.01" name="bobot_pelanggaran"
                                    value="{{ $konfig->bobot_pelanggaran }}"
                                    class="m3-input-glass w-full text-xs font-bold !border-rose-500/30 text-rose-600 dark:text-rose-400">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: Tarif Poin Presensi -->
                <div class="m3-glass-card p-5 sm:p-7 rounded-3xl shadow-2xs flex flex-col justify-between">
                    <div>
                        <div
                            class="flex items-center gap-3 mb-5 pb-3.5 border-b border-zinc-200/80 dark:border-zinc-800">
                            <div
                                class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 flex items-center justify-center text-sm shrink-0">
                                <i class="bi bi-clipboard-x-fill"></i>
                            </div>
                            <h3 class="font-black text-sm md:text-base text-zinc-900 dark:text-white tracking-tight">
                                Tarif Poin Presensi</h3>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Alpha (A)
                                </label>
                                <input type="number" step="0.01" name="poin_alpha"
                                    value="{{ $konfig->poin_alpha }}"
                                    class="m3-input-glass w-full text-xs font-bold">
                            </div>
                            <div>
                                <label
                                    class="flex items-center justify-between text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Izin (I) <span
                                        class="text-[8px] text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-1 py-0.5 rounded border border-emerald-500/20">1/6
                                        Poin</span>
                                </label>
                                <input type="number" step="0.01" name="poin_izin"
                                    value="{{ $konfig->poin_izin }}" class="m3-input-glass w-full text-xs font-bold">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Hadir (H)
                                </label>
                                <input type="number" step="0.01" name="poin_hadir"
                                    value="{{ $konfig->poin_hadir }}" readonly
                                    class="m3-input-glass w-full text-xs font-bold opacity-60 cursor-not-allowed">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Dispensasi (D)
                                </label>
                                <input type="number" step="0.01" name="poin_dispen"
                                    value="{{ $konfig->poin_dispen }}" readonly
                                    class="m3-input-glass w-full text-xs font-bold opacity-60 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-zinc-200/80 dark:border-zinc-800 flex justify-end">
                        @can('tambah pengaturan-akademik')
                            <button type="submit"
                                class="m3-btn-primary w-full sm:w-auto h-10 px-6 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5">
                                <i class="bi bi-save-fill text-xs"></i> <span>Simpan Konfigurasi</span>
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('script')
        <script>
            function filterTahunPelajaran(tpId) {
                let allCards = document.querySelectorAll('.tp-card');
                allCards.forEach(card => {
                    card.classList.remove('block');
                    card.classList.add('hidden');
                });

                let selectedCard = document.getElementById('tp-card-' + tpId);
                if (selectedCard) {
                    selectedCard.classList.remove('hidden');
                    selectedCard.classList.add('block');
                }
            }
        </script>
    @endpush
</x-app-layout>
