@section('title', 'Bulanan - Presensi Ustadz')

<x-app-layout>
    <!-- HEADER & TAB MENU -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-start justify-between gap-4 relative z-10 print:hidden">

        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('presensi-ustadz.menu')
        </div>

        <!-- Area Form Pencarian (Sejajar dengan Menu) -->
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('presensi-ustadz.bulanan') }}" method="GET"
                class="flex flex-col sm:flex-row items-center gap-2 w-full xl:w-auto" id="formFilter">

                <!-- Filter Bulan -->
                <div class="w-full sm:flex-1">
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-moon-stars-fill text-xs"></i>
                        </div>
                        <select name="bulan_id" required
                            class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none">
                            <option value="" class="text-zinc-500">-- Pilih Bulan --</option>
                            @foreach ($bulans as $b)
                                <option value="{{ $b->id }}"
                                    {{ $bulan_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_bulan }} {{ $b->tahun_hijriyah }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-chevron-down text-[10px] font-black"></i>
                        </div>
                    </div>
                </div>

                <!-- Filter Ruangan -->
                <div class="w-full sm:flex-1">
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-door-open-fill text-xs"></i>
                        </div>
                        <select name="ruangan_id" required
                            class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none">
                            <option value="" class="text-zinc-500">-- Pilih Ruangan --</option>
                            @foreach ($ruangans as $ruangan)
                                <option value="{{ $ruangan->id }}"
                                    {{ $ruangan_id == $ruangan->id ? 'selected' : '' }}>
                                    {{ $ruangan->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-chevron-down text-[10px] font-black"></i>
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="w-full sm:w-auto shrink-0">
                    <button type="submit"
                        class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5">
                        <i class="bi bi-search"></i> <span>Tampilkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- AREA HASIL TABEL -->
    @if ($bulan_id && $ruangan_id)
        <div
            class="m3-glass-card overflow-hidden relative z-10 shadow-2xs">

            <!-- Header Leger -->
            <div
                class="bg-zinc-100/50 dark:bg-zinc-800/40 border-b border-zinc-200/80 dark:border-zinc-800 px-5 md:px-6 py-3.5 flex flex-col md:flex-row justify-between md:items-center gap-2 relative z-10">
                <div>
                    <h3
                        class="font-black text-zinc-900 dark:text-white text-base tracking-tight leading-tight uppercase mb-0.5">
                        Bulan: <span class="text-primary dark:text-primary-dark">{{ $bulanTerpilih->nama_bulan }}</span>
                    </h3>
                    <p
                        class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider flex items-center">
                        <i class="bi bi-info-circle-fill mr-1.5 opacity-70"></i> Menampilkan jadwal & presensi Ustadz harian.
                    </p>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="overflow-x-auto relative z-10 custom-scrollbar p-0">
                <table class="w-full text-left text-xs border-collapse min-w-[1000px]">
                    <thead
                        class="bg-zinc-100/70 dark:bg-zinc-800/50 border-b border-zinc-200/80 dark:border-zinc-800 sticky top-0 z-20">
                        <tr class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            <th class="py-3 px-4 w-16 text-center border-r border-zinc-200/60 dark:border-zinc-800/60">Tgl</th>
                            <th class="py-3 px-4 w-28 text-center border-r border-zinc-200/60 dark:border-zinc-800/60">Hari</th>
                            <th class="py-3 px-5 w-1/3 border-r border-zinc-200/60 dark:border-zinc-800/60">Jam Nadzoman</th>
                            <th class="py-3 px-5 w-1/3 border-r border-zinc-200/60 dark:border-zinc-800/60">Jam Ke-1</th>
                            <th class="py-3 px-5 w-1/3 border-r border-zinc-200/60 dark:border-zinc-800/60">Jam Ke-2</th>
                            <th class="py-3 px-5 w-1/3">Jam Ekstra</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200/80 dark:divide-zinc-800/60">
                        @php $noTgl = 1; @endphp
                        @foreach ($dates as $tglMasehi => $info)
                            <tr
                                class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors group/row">

                                <!-- Tanggal -->
                                <td
                                    class="py-3 px-4 text-center border-r border-zinc-200/60 dark:border-zinc-800/60 align-middle">
                                    <div class="font-black text-zinc-800 dark:text-zinc-200 text-sm">
                                        {{ $noTgl++ }}</div>
                                    <div class="text-[9px] text-zinc-400 dark:text-zinc-500 mt-0.5 font-bold">
                                        {{ date('d/m', strtotime($tglMasehi)) }}</div>
                                </td>

                                <!-- Hari -->
                                <td
                                    class="py-3 px-4 text-center border-r border-zinc-200/60 dark:border-zinc-800/60 align-middle font-black text-zinc-500 dark:text-zinc-400 text-[10px] uppercase tracking-wider">
                                    {{ substr($info['hari'], 0, 3) }}
                                </td>

                                <!-- Data Jadwal (3 Kolom Jam) -->
                                @if ($info['is_libur'])
                                    <!-- HARI LIBUR -->
                                    <td colspan="4" class="py-3 px-5 bg-rose-500/5 align-middle">
                                        <div
                                            class="flex items-center justify-center gap-2 text-rose-500 dark:text-rose-400 font-black text-[11px] uppercase tracking-wider">
                                            <i class="bi bi-brightness-high-fill"></i>
                                            {{ $info['keterangan_libur'] }}
                                        </div>
                                    </td>
                                @else
                                    @foreach ($jamList as $jam)
                                        <td
                                            class="py-3 px-4 border-r border-zinc-200/60 dark:border-zinc-800/60 align-top relative group/cell hover:bg-zinc-100/50 dark:hover:bg-zinc-800/50 transition-colors last:border-r-0">
                                            @php $selData = $matrix[$tglMasehi][$jam]; @endphp

                                            @if (!$selData['is_jadwal'])
                                                <!-- KOSONG -->
                                                <div class="flex items-center justify-center h-full min-h-[40px]">
                                                    <span
                                                        class="text-zinc-300 dark:text-zinc-700 font-black text-sm opacity-50">-</span>
                                                </div>
                                            @else
                                                <!-- ADA JADWAL -->
                                                <div class="flex flex-col gap-1.5 relative">
                                                    <!-- Judul & Nama Guru -->
                                                    <div class="pr-6">
                                                        <div class="text-xs font-black text-zinc-900 dark:text-white leading-tight tracking-tight mb-0.5 truncate"
                                                            title="{{ $selData['mapel'] }}">
                                                            {{ $selData['mapel'] }}
                                                        </div>
                                                        <div class="text-[9px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider truncate"
                                                            title="{{ $selData['guru_utama'] }}">
                                                            <i
                                                                class="bi bi-person-fill mr-1 opacity-70"></i>{{ $selData['guru_utama'] }}
                                                        </div>
                                                    </div>

                                                    <!-- Tombol Edit Presensi (Muncul saat Hover) -->
                                                    @can('create presensi-ustadz')
                                                        @php $p = $selData['presensi']; @endphp
                                                        <button type="button"
                                                            onclick="bukaModalPresensi('{{ $tglMasehi }}', '{{ $selData['jadwal_id'] }}', '{{ $selData['ustadz_id'] }}', '{{ addslashes($selData['mapel']) }}', '{{ $selData['guru_utama'] }}', '{{ $p ? $p->status : 'Hadir' }}', '{{ $p ? $p->ustadz_pengganti_id : '' }}', '{{ $p ? addslashes($p->keterangan) : '' }}')"
                                                            class="opacity-0 group-hover/cell:opacity-100 transition-all duration-200 absolute right-0 top-0 w-7 h-7 flex items-center justify-center bg-white dark:bg-zinc-800 text-primary dark:text-primary-dark rounded-lg shadow-2xs border border-zinc-200 dark:border-zinc-700 active:scale-95 outline-none"
                                                            title="Isi / Edit Presensi">
                                                            <i class="bi bi-pencil-square text-xs"></i>
                                                        </button>
                                                    @endcan

                                                    <!-- Status Label Bawah -->
                                                    <div
                                                        class="mt-1 pt-1.5 border-t border-zinc-200/60 dark:border-zinc-800 border-dashed flex items-center justify-between gap-1">
                                                        @if ($selData['presensi'])
                                                            @php
                                                                $badgeColor = match ($p->status) {
                                                                    'Hadir'
                                                                        => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                                                    'Sakit'
                                                                        => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                                                    'Izin'
                                                                        => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                                                    'Alpha'
                                                                        => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                                                    'Kosong'
                                                                        => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700',
                                                                    default
                                                                        => 'bg-zinc-100 text-zinc-600 border-zinc-200',
                                                                };
                                                            @endphp

                                                            <div class="flex items-center gap-1.5 w-full">
                                                                <span
                                                                    class="px-2 py-0.5 rounded-lg flex-shrink-0 text-[8px] font-black uppercase tracking-wider border shadow-2xs {{ $badgeColor }}">
                                                                    {{ $p->status }}
                                                                </span>
                                                                @if ($p->keterangan)
                                                                    <span
                                                                        class="text-[9px] text-zinc-400 dark:text-zinc-500 italic truncate"
                                                                        title="{{ $p->keterangan }}">
                                                                        {{ $p->keterangan }}
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <!-- Tombol Hapus (Mini) -->
                                                            @can('hapus presensi-ustadz')
                                                                <button type="button"
                                                                    onclick="hapusPresensi('{{ route('presensi-ustadz.destroyHarian', $p->id) }}')"
                                                                    class="w-5 h-5 flex-shrink-0 flex items-center justify-center rounded-lg bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 text-rose-500 transition-colors outline-none"
                                                                    title="Batal / Hapus">
                                                                    <i class="bi bi-trash3-fill text-[9px]"></i>
                                                                </button>
                                                            @endcan
                                                        @else
                                                            <span
                                                                class="px-2 py-0.5 inline-block rounded-lg text-[8px] font-black uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 border border-transparent">
                                                                Belum Absen
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <!-- Info Pengganti (Badal) -->
                                                    @if ($selData['presensi'] && in_array($p->status, ['Sakit', 'Izin', 'Alpha']) && $p->guruPengganti)
                                                        <div
                                                            class="mt-1 text-[9px] font-bold text-amber-600 dark:text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-lg w-full truncate uppercase tracking-wider shadow-2xs">
                                                            <i class="bi bi-arrow-return-right mr-1 opacity-70"></i>
                                                            Piket: {{ $p->guruPengganti->nama_lengkap }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- State Awal saat halaman baru dibuka -->
        <div class="col-span-full">
            <x-empty-state icon="bi-door-open" title="Pilih Parameter" message="Tentukan Bulan dan Ruangan pada filter di atas untuk melihat rekapitulasi kehadiran dan jadwal Badal Asatidz." />
        </div>
    @endif

    <!-- ============================================== -->
    <!-- MODAL PRESENSI (M3 GLASS) -->
    <!-- ============================================== -->
    <div id="modalPresensi"
        class="fixed inset-0 z-[99] hidden items-center justify-center bg-black/60 dark:bg-black/80 backdrop-blur-sm px-4">

        <div class="relative m3-glass-card w-full max-w-lg shadow-xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300"
            id="modalContent">

            <!-- Header Modal -->
            <div
                class="px-6 py-4 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center bg-zinc-100/50 dark:bg-zinc-800/40">
                <h3 class="text-base font-black text-zinc-900 dark:text-white flex items-center gap-2.5">
                    <div
                        class="w-7 h-7 rounded-xl bg-primary/10 border border-primary/20 text-primary dark:text-primary-dark flex items-center justify-center">
                        <i class="bi bi-pencil-square text-xs"></i>
                    </div>
                    Update Presensi
                </h3>
                <button type="button" onclick="tutupModalPresensi()"
                    class="text-zinc-400 hover:text-rose-500 dark:hover:text-rose-400 transition-colors w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700 active:scale-95 shadow-2xs outline-none">
                    <i class="bi bi-x-lg text-xs font-black"></i>
                </button>
            </div>

            <!-- Form Body Modal -->
            <form action="{{ route('presensi-ustadz.storeBulanan') }}" method="POST">
                @csrf
                <input type="hidden" name="tanggal" id="m_tanggal">
                <input type="hidden" name="jadwal_pelajaran_id" id="m_jadwal_id">
                <input type="hidden" name="ustadz_id" id="m_ustadz_id">

                <div class="p-5 sm:p-6 space-y-4">
                    <!-- Detail Jadwal -->
                    <div
                        class="bg-white/40 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800 p-4 rounded-xl shadow-2xs">
                        <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-1"
                            id="m_text_tanggal"></p>
                        <h4 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight mb-1"
                            id="m_text_mapel"></h4>
                        <p
                            class="text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider flex items-center">
                            <i class="bi bi-person-fill mr-1 opacity-70"></i> Utama: <span
                                class="text-zinc-900 dark:text-zinc-200 ml-1 font-black" id="m_text_guru"></span>
                        </p>
                    </div>

                    <!-- Input Status (Custom Radio) -->
                    <div>
                        <label
                            class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">Status
                            Kehadiran</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                            <!-- Opsi Hadir -->
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="Hadir"
                                    class="status-radio-modal sr-only peer">
                                <div
                                    class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 peer-checked:bg-emerald-500/15 peer-checked:border-emerald-500 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 shadow-2xs">
                                    Hadir</div>
                            </label>
                            <!-- Opsi Sakit -->
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="Sakit"
                                    class="status-radio-modal sr-only peer">
                                <div
                                    class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 peer-checked:bg-blue-500/15 peer-checked:border-blue-500 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 shadow-2xs">
                                    Sakit</div>
                            </label>
                            <!-- Opsi Izin -->
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="Izin"
                                    class="status-radio-modal sr-only peer">
                                <div
                                    class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 peer-checked:bg-amber-500/15 peer-checked:border-amber-500 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 shadow-2xs">
                                    Izin</div>
                            </label>
                            <!-- Opsi Alpha -->
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="Alpha"
                                    class="status-radio-modal sr-only peer">
                                <div
                                    class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 peer-checked:bg-rose-500/15 peer-checked:border-rose-500 peer-checked:text-rose-600 dark:peer-checked:text-rose-400 shadow-2xs">
                                    Alpha</div>
                            </label>
                            <!-- Opsi Kosong -->
                            <label class="relative cursor-pointer col-span-2 sm:col-span-1">
                                <input type="radio" name="status" value="Kosong"
                                    class="status-radio-modal sr-only peer">
                                <div
                                    class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 peer-checked:bg-zinc-500/10 peer-checked:border-zinc-500 peer-checked:text-zinc-800 dark:peer-checked:text-white shadow-2xs">
                                    Kosong</div>
                            </label>
                        </div>
                    </div>

                    <!-- Input Badal (Hidden by default) -->
                    <div id="wrapper_pengganti" class="hidden">
                        <label
                            class="block text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-wider mb-1.5 ml-1">Guru
                            Pengganti (Badal)</label>
                        <div class="relative">
                            <select name="ustadz_pengganti_id" id="m_pengganti"
                                class="m3-input-glass w-full !pr-8 text-xs font-bold text-amber-800 dark:text-amber-400 !border-amber-500/30 cursor-pointer appearance-none">
                                <option value="" class="text-zinc-500">-- Pilih Guru Pengganti --</option>
                                @foreach ($semuaGuru as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                                @endforeach
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-amber-600">
                                <i class="bi bi-chevron-down text-[10px] font-black"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Input Keterangan -->
                    <div>
                        <label
                            class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">Keterangan
                            Tambahan</label>
                        <input type="text" name="keterangan" id="m_keterangan"
                            placeholder="Alasan atau materi (Opsional)..."
                            class="m3-input-glass w-full text-xs font-medium">
                    </div>
                </div>

                <!-- Footer Modal -->
                <div
                    class="px-6 py-3.5 border-t border-zinc-200/80 dark:border-zinc-800 bg-zinc-100/50 dark:bg-zinc-800/40 flex justify-end">
                    <button type="submit"
                        class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5">
                        <i class="bi bi-check-circle-fill text-xs"></i> <span>Simpan Presensi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form untuk Hapus Presensi -->
    <form id="formHapusPresensi" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Script Modal & Toggle -->
    <script>
        function bukaModalPresensi(tgl, jadwalId, ustadzId, mapel, guru, status, penggantiId, keterangan) {
            const modal = document.getElementById('modalPresensi');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);

            document.getElementById('m_tanggal').value = tgl;
            document.getElementById('m_jadwal_id').value = jadwalId;
            document.getElementById('m_ustadz_id').value = ustadzId;

            document.getElementById('m_text_tanggal').innerText = "TANGGAL: " + tgl;
            document.getElementById('m_text_mapel').innerText = mapel;
            document.getElementById('m_text_guru').innerText = guru;

            // Set radio button status
            const statusRadios = document.querySelectorAll('.status-radio-modal');
            statusRadios.forEach(radio => {
                radio.checked = (radio.value === status);
            });

            document.getElementById('m_pengganti').value = penggantiId;
            document.getElementById('m_keterangan').value = keterangan;

            togglePengganti(status);
        }

        function tutupModalPresensi() {
            const modal = document.getElementById('modalPresensi');
            const content = document.getElementById('modalContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        function togglePengganti(statusValue) {
            const wrapper = document.getElementById('wrapper_pengganti');

            if (['Sakit', 'Izin', 'Alpha'].includes(statusValue)) {
                wrapper.classList.remove('hidden');
                wrapper.classList.add('block');
            } else {
                wrapper.classList.remove('block');
                wrapper.classList.add('hidden');
                document.getElementById('m_pengganti').value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalRadios = document.querySelectorAll('.status-radio-modal');
            modalRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        togglePengganti(this.value);
                    }
                });
            });
        });

        // SweetAlert2 Hapus Presensi
        function hapusPresensi(actionUrl) {
            const isDark = document.documentElement.classList.contains('dark');

            Swal.fire({
                title: '<span class="text-base font-black tracking-tight">Hapus Presensi?</span>',
                html: '<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Apakah Anda yakin ingin membatalkan dan menghapus data kehadiran ini?</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#71717a',
                heightAuto: false,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: isDark ? '#0c0c0e' : '#ffffff',
                color: isDark ? '#f4f4f5' : '#18181b',
                customClass: {
                    popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                    confirmButton: "h-10 px-5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none",
                    cancelButton: "h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formHapusPresensi');
                    form.action = actionUrl;
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>

