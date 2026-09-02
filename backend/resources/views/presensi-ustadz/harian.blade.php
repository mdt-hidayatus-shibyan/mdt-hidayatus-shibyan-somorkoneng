@section('title', 'Harian - Presensi Ustadz')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10">

        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('presensi-ustadz.menu')
        </div>

        <!-- Area Form Pencarian -->
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('presensi-ustadz.index') }}" method="GET"
                class="flex flex-col sm:flex-row items-center gap-2 w-full xl:w-auto" id="formFilter">

                <!-- Filter Tanggal -->
                <div class="w-full sm:w-auto flex-1">
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-calendar-date-fill text-xs"></i>
                        </div>
                        <input type="date" name="tanggal" value="{{ $tanggal }}" required
                            class="m3-input-glass w-full !pl-9 text-xs font-bold cursor-pointer">
                    </div>
                </div>

                <!-- Filter Ruangan -->
                <div class="w-full sm:w-auto flex-1">
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

    <!-- AREA HASIL PENCARIAN -->
    @if ($ruangan_id)
        @if ($isLibur)
            <!-- STATE HARI LIBUR -->
            <div
                class="m3-glass-card !bg-rose-500/10 !border-rose-500/20 p-8 md:p-12 text-center relative z-10 shadow-2xs">
                <div
                    class="w-14 h-14 bg-rose-500/20 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-rose-500/30 text-rose-500">
                    <i class="bi bi-brightness-high-fill text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-rose-600 dark:text-rose-400 mb-1 tracking-tight">Hari Libur Madrasah</h3>
                <div
                    class="inline-block bg-white/60 dark:bg-black/40 py-1.5 px-4 rounded-xl border border-rose-500/20 mt-2">
                    <p class="text-xs font-black text-rose-600 dark:text-rose-400 tracking-wide uppercase">
                        Keterangan: {{ $keteranganLibur }}
                    </p>
                </div>
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-3">Form presensi dinonaktifkan pada hari libur.</p>
            </div>
        @elseif ($jadwals->isEmpty())
            <!-- STATE TIDAK ADA JADWAL -->
            <div class="col-span-full">
                <x-empty-state icon="bi-calendar-x" title="Tidak Ada Jadwal" message="Tidak ditemukan jadwal pelajaran di ruangan ini pada tanggal yang Anda pilih." />
            </div>
        @else
            <!-- FORM PENGISIAN PRESENSI (GRID KIRI-KANAN) -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 md:gap-6 relative z-10">

                <!-- KOLOM KIRI (FORM INPUT) -->
                <div
                    class="xl:col-span-8 m3-glass-card overflow-hidden relative flex flex-col shadow-2xs">

                    <!-- Header Kiri -->
                    <div
                        class="px-5 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-100/50 dark:bg-zinc-800/40 relative z-10 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-xl bg-primary/10 border border-primary/20 text-primary dark:text-primary-dark flex items-center justify-center text-sm shrink-0">
                            <i class="bi bi-ui-checks-grid"></i>
                        </div>
                        <h3 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                            Form Input Presensi
                        </h3>
                    </div>

                    <form action="{{ route('presensi-ustadz.storeHarian') }}" method="POST"
                        class="flex-1 flex flex-col relative z-10">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <input type="hidden" name="ruangan_id" value="{{ $ruangan_id }}">

                        <!-- Daftar Jadwal -->
                        <div class="p-4 sm:p-5 space-y-3.5">
                            @foreach ($jadwals as $jadwal)
                                @php
                                    $riwayat = $riwayatPresensi->get($jadwal->id);
                                    $currentStatus = $riwayat ? $riwayat->status : 'Hadir';
                                    $currentPengganti = $riwayat ? $riwayat->guru_pengganti_id : '';
                                    $currentKeterangan = $riwayat ? $riwayat->keterangan : '';
                                @endphp

                                <!-- Solid Card Tiap Jadwal -->
                                <div
                                    class="p-4 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white/40 dark:bg-zinc-900/30 transition-all shadow-2xs">

                                    <!-- Identitas Jadwal -->
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 mb-3.5 pb-3 border-b border-zinc-200/80 dark:border-zinc-800 relative z-10">
                                        <div>
                                            <span
                                                class="inline-block px-2 py-0.5 bg-primary/10 text-primary dark:text-primary-dark border border-primary/20 text-[9px] font-black rounded-lg uppercase tracking-wider mb-1">
                                                Jam Ke-{{ $jadwal->jam_ke }}
                                            </span>
                                            <h4
                                                class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                                                {{ $jadwal->mataPelajaran->nama_mapel }}
                                            </h4>
                                        </div>
                                        <div
                                            class="text-left sm:text-right flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-start">
                                            <p
                                                class="text-[9px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">
                                                Guru Terjadwal</p>
                                            <p
                                                class="text-xs font-bold text-zinc-800 dark:text-zinc-200 flex items-center">
                                                <i class="bi bi-person-fill mr-1 text-zinc-400"></i>
                                                {{ $jadwal->ustadz->nama_lengkap }}
                                            </p>
                                        </div>
                                    </div>

                                    <input type="hidden" name="presensi[{{ $jadwal->id }}][ustadz_id]"
                                        value="{{ $jadwal->ustadz_id }}">

                                    <!-- Form Inputs (Grid) -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 relative z-10">

                                        <!-- Select Status (Menggunakan Radio Button) -->
                                        <div class="col-span-1 md:col-span-2">
                                            <label
                                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                                Status Kehadiran
                                            </label>

                                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-1.5">
                                                <!-- Opsi Hadir -->
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="presensi[{{ $jadwal->id }}][status]"
                                                        value="Hadir" class="status-radio sr-only peer"
                                                        {{ $currentStatus == 'Hadir' ? 'checked' : '' }}>
                                                    <div
                                                        class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all shadow-2xs
                                                        bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400
                                                        hover:bg-emerald-500/10
                                                        peer-checked:bg-emerald-500/15 peer-checked:border-emerald-500 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400">
                                                        Hadir
                                                    </div>
                                                </label>

                                                <!-- Opsi Sakit -->
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="presensi[{{ $jadwal->id }}][status]"
                                                        value="Sakit" class="status-radio sr-only peer"
                                                        {{ $currentStatus == 'Sakit' ? 'checked' : '' }}>
                                                    <div
                                                        class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all shadow-2xs
                                                        bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400
                                                        hover:bg-blue-500/10
                                                        peer-checked:bg-blue-500/15 peer-checked:border-blue-500 peer-checked:text-blue-600 dark:peer-checked:text-blue-400">
                                                        Sakit
                                                    </div>
                                                </label>

                                                <!-- Opsi Izin -->
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="presensi[{{ $jadwal->id }}][status]"
                                                        value="Izin" class="status-radio sr-only peer"
                                                        {{ $currentStatus == 'Izin' ? 'checked' : '' }}>
                                                    <div
                                                        class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all shadow-2xs
                                                        bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400
                                                        hover:bg-amber-500/10
                                                        peer-checked:bg-amber-500/15 peer-checked:border-amber-500 peer-checked:text-amber-600 dark:peer-checked:text-amber-400">
                                                        Izin
                                                    </div>
                                                </label>

                                                <!-- Opsi Alpha -->
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="presensi[{{ $jadwal->id }}][status]"
                                                        value="Alpha" class="status-radio sr-only peer"
                                                        {{ $currentStatus == 'Alpha' ? 'checked' : '' }}>
                                                    <div
                                                        class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all shadow-2xs
                                                        bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400
                                                        hover:bg-rose-500/10
                                                        peer-checked:bg-rose-500/15 peer-checked:border-rose-500 peer-checked:text-rose-600 dark:peer-checked:text-rose-400">
                                                        Alpha
                                                    </div>
                                                </label>

                                                <!-- Opsi Kosong -->
                                                <label class="relative cursor-pointer col-span-2 sm:col-span-1">
                                                    <input type="radio" name="presensi[{{ $jadwal->id }}][status]"
                                                        value="Kosong" class="status-radio sr-only peer"
                                                        {{ $currentStatus == 'Kosong' ? 'checked' : '' }}>
                                                    <div
                                                        class="w-full flex items-center justify-center py-2 px-1 text-xs font-black rounded-xl border transition-all shadow-2xs
                                                        bg-white/40 dark:bg-black/40 border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400
                                                        hover:bg-zinc-100 dark:hover:bg-zinc-800
                                                        peer-checked:bg-zinc-500/10 peer-checked:border-zinc-500 peer-checked:text-zinc-800 dark:peer-checked:text-white">
                                                        Kosong
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Select Guru Pengganti (Toggled via JS) -->
                                        <div
                                            class="pengganti-wrapper col-span-1 md:col-span-2 {{ in_array($currentStatus, ['Sakit', 'Izin', 'Alpha']) ? 'block' : 'hidden' }}">
                                            <label
                                                class="block text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-wider mb-1.5 ml-1">
                                                Digantikan Oleh (Badal)
                                            </label>
                                            <div class="relative">
                                                <select name="presensi[{{ $jadwal->id }}][ustadz_pengganti_id]"
                                                    class="m3-input-glass w-full !pr-8 text-xs font-bold text-amber-800 dark:text-amber-400 !border-amber-500/30 cursor-pointer appearance-none">
                                                    <option value="" class="text-zinc-500">-- Pilih Guru Pengganti --</option>
                                                    @foreach ($semuaGuru as $guru)
                                                        @if ($guru->id != $jadwal->ustadz_id)
                                                            <option value="{{ $guru->id }}"
                                                                {{ $currentPengganti == $guru->id ? 'selected' : '' }}>
                                                                {{ $guru->nama_lengkap }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-amber-500">
                                                    <i class="bi bi-chevron-down text-[10px] font-black"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Input Keterangan -->
                                        <div class="col-span-1 md:col-span-2">
                                            <input type="text" name="presensi[{{ $jadwal->id }}][keterangan]"
                                                value="{{ $currentKeterangan }}"
                                                placeholder="Keterangan materi atau alasan izin (Opsional)..."
                                                class="m3-input-glass w-full text-xs font-medium">
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer Kiri / Submit -->
                        <div
                            class="px-5 py-3.5 border-t border-zinc-200/80 dark:border-zinc-800 bg-zinc-100/50 dark:bg-zinc-800/40 flex justify-end mt-auto relative z-10">
                            @can('create presensi-ustadz')
                                <button type="submit"
                                    class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5">
                                    <i class="bi bi-save2-fill text-xs"></i> <span>Simpan Data Presensi</span>
                                </button>
                            @endcan
                        </div>
                    </form>
                </div>

                <!-- KOLOM KANAN (STATUS HARI INI) -->
                <div class="xl:col-span-4">
                    <div
                        class="m3-glass-card overflow-hidden sticky top-6 relative shadow-2xs">

                        <!-- Header Kanan -->
                        <div
                            class="px-5 py-3.5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-100/50 dark:bg-zinc-800/40 relative z-10 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-primary/10 border border-primary/20 text-primary dark:text-primary-dark flex items-center justify-center text-sm shrink-0">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h3
                                class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                                Status Hari Ini
                            </h3>
                        </div>

                        <div class="p-4 space-y-2.5 relative z-10">
                            @foreach ($jadwals as $jadwal)
                                @php
                                    $riwayat = $riwayatPresensi->get($jadwal->id);

                                    if ($riwayat) {
                                        $badgeColor = match ($riwayat->status) {
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
                                            default => 'bg-zinc-100 text-zinc-600 border-zinc-200',
                                        };
                                        $cardStyle = 'border-zinc-200/80 dark:border-zinc-800 bg-white/40 dark:bg-zinc-900/30';
                                    } else {
                                        $badgeColor = 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border-zinc-200 dark:border-zinc-700';
                                        $cardStyle = 'border-dashed border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/10';
                                    }
                                @endphp

                                <div class="p-3 rounded-xl border {{ $cardStyle }} transition-all shadow-2xs">
                                    <div class="flex justify-between items-start mb-1.5">
                                        <span
                                            class="text-[9px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                                            <i class="bi bi-clock mr-1"></i> Jam {{ $jadwal->jam_ke }}
                                        </span>

                                        @if ($riwayat)
                                            <div class="flex items-center gap-1">
                                                <span
                                                    class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider border shadow-2xs {{ $badgeColor }}">
                                                    {{ $riwayat->status }}
                                                </span>

                                                @can('hapus presensi-ustadz')
                                                    <button type="button"
                                                        onclick="hapusPresensi('{{ route('presensi-ustadz.destroyHarian', $riwayat->id) }}')"
                                                        class="w-5 h-5 flex items-center justify-center rounded-lg bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 text-rose-500 transition-all outline-none"
                                                        title="Batal / Hapus Presensi">
                                                        <i class="bi bi-trash3-fill text-[9px]"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider border {{ $badgeColor }}">
                                                Belum Diisi
                                            </span>
                                        @endif
                                    </div>

                                    <p
                                        class="text-xs font-black text-zinc-900 dark:text-white tracking-tight leading-snug">
                                        {{ $jadwal->mataPelajaran->nama_mapel }}
                                    </p>

                                    @if ($riwayat && $riwayat->guruPengganti)
                                        <div
                                            class="mt-1.5 pt-1.5 border-t border-zinc-200/60 dark:border-zinc-800 flex items-start gap-1.5">
                                            <i
                                                class="bi bi-arrow-return-right text-amber-500 text-[10px] mt-[1px]"></i>
                                            <div>
                                                <p
                                                    class="text-[8px] font-black text-zinc-400 uppercase tracking-wider">
                                                    Digantikan:</p>
                                                <p class="text-xs font-bold text-amber-600 dark:text-amber-500">
                                                    {{ $riwayat->guruPengganti->nama_lengkap }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($riwayat && $riwayat->penginput)
                                        <div
                                            class="mt-1.5 pt-1.5 border-t border-zinc-200/60 dark:border-zinc-800 text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider flex items-center">
                                            <i class="bi bi-person-check-fill mr-1.5"></i> Diinput oleh:
                                            {{ $riwayat->penginput->name }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        @endif
    @else
        <!-- State Awal (Belum Pilih Filter) -->
        <div class="col-span-full">
            <x-empty-state icon="bi-door-open" title="Pilih Ruangan" message="Silakan tentukan ruangan/kelas pada filter di atas untuk memuat daftar jadwal guru yang mengajar hari ini." />
        </div>
    @endif
    <form id="formHapusPresensi" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const radioButtons = document.querySelectorAll('.status-radio');

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {

                    if (this.checked) {

                        const container = this.closest('.relative.z-10');

                        const penggantiWrapper = container.querySelector('.pengganti-wrapper');
                        const penggantiInput = penggantiWrapper.querySelector('select');
                        const status = this.value;
                        if (['Sakit', 'Izin', 'Alpha'].includes(status)) {
                            penggantiWrapper.classList.remove('hidden');
                            penggantiWrapper.classList.add('block');
                        } else {
                            penggantiWrapper.classList.remove('block');
                            penggantiWrapper.classList.add('hidden');
                            penggantiInput.value = '';
                        }
                    }
                });
            });
        });

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

