@section('title', 'Kalender Pendidikan')
<x-app-layout>
    @push('style')
        <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
        <style>
            .font-amiri {
                font-family: 'Amiri', serif;
            }

            .agenda-scroll::-webkit-scrollbar {
                width: 4px;
            }

            .agenda-scroll::-webkit-scrollbar-track {
                background: transparent;
            }

            .agenda-scroll::-webkit-scrollbar-thumb {
                background: rgba(156, 163, 175, 0.4);
                border-radius: 10px;
            }

            .dark .agenda-scroll::-webkit-scrollbar-thumb {
                background: rgba(82, 82, 91, 0.5);
            }
        </style>
    @endpush


    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-20">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Kalender Pendidikan</h2>
            <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5 uppercase tracking-wider">Kelola agenda madrasah, jadwal ujian, dan kalender Hisab MABIMS</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
            <form id="formFilterTp" class="relative w-full sm:w-72 group/select" onsubmit="return false;">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10 text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                    <i class="bi bi-calendar-range text-sm"></i>
                </div>
                <select name="tahun_id" onchange="filterAgendaAjax(this.value)"
                    class="m3-input-glass w-full !pl-9 !pr-9 cursor-pointer appearance-none">
                    @foreach ($tahun_pelajarans as $tahun)
                        <option value="{{ $tahun->id }}" {{ $tahunPelajaranId == $tahun->id ? 'selected' : '' }}>
                            {{ $tahun->nama_hijriyah }} H | {{ $tahun->nama_masehi }} M
                        </option>
                    @endforeach
                </select>
                <div
                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none z-10 text-zinc-400">
                    <i class="bi bi-chevron-down text-xs font-bold"></i>
                </div>
            </form>
            @can('read kalendar-pendidikan')
                <a href="{{ route('kalendar-pendidikan.matriks') }}" class="m3-btn-primary w-full sm:w-auto group/btn">
                    <i class="bi bi-grid-3x3 text-sm"></i>
                    <span>Matriks</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- TATA LETAK UTAMA: 2 KOLOM -->
    <div class="flex flex-col xl:flex-row gap-6 relative z-10 items-start">
        <!-- PANEL KIRI: DAFTAR AGENDA GABUNGAN -->
        <div class="flex-1 m3-glass-card p-5 lg:p-6 flex flex-col lg:flex-row gap-6 relative w-full">

            <!-- Loading Overlay -->
            <div id="calLoader"
                class="absolute inset-0 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md z-50 flex flex-col items-center justify-center rounded-2xl hidden">
                <i class="bi bi-arrow-repeat text-3xl text-primary dark:text-primary-dark animate-spin mb-2"></i>
                <span class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 tracking-widest uppercase">Menyiapkan Kalender...</span>
            </div>

            <!-- BAGIAN GRID KALENDER -->
            <div class="flex-1 flex flex-col min-w-0">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-5 gap-3.5">
                    <div class="flex items-center gap-2">
                        <button onclick="geserBulan(-1)"
                            class="m3-btn-secondary w-8 h-8 !p-0 inline-flex items-center justify-center shadow-2xs"
                            title="Bulan Sebelumnya"><i
                                class="bi bi-chevron-left text-xs"></i></button>
                        <div class="text-center sm:text-left px-2">
                            <h3 id="calTitleMain" class="text-lg md:text-xl font-black text-zinc-900 dark:text-white leading-tight">
                                Memuat...</h3>
                            <p id="calTitleSub" class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mt-0.5">
                                -</p>
                        </div>
                        <button onclick="geserBulan(1)"
                            class="m3-btn-secondary w-8 h-8 !p-0 inline-flex items-center justify-center shadow-2xs"
                            title="Bulan Berikutnya"><i
                                class="bi bi-chevron-right text-xs"></i></button>
                    </div>

                    <div class="flex bg-zinc-100/90 dark:bg-zinc-900/90 p-1 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                        <button onclick="switchMode('gregorian')" id="btnModeMasehi"
                            class="px-3.5 py-1 rounded-lg text-xs font-black text-zinc-500 dark:text-zinc-400 transition-all">Masehi</button>
                        <button onclick="switchMode('hijri')" id="btnModeHijriyah"
                            class="px-3.5 py-1 rounded-lg text-xs font-black bg-primary dark:bg-primary-dark text-white dark:text-zinc-900 shadow-2xs transition-all">Hijriyah</button>
                    </div>
                </div>

                <!-- Header Hari -->
                <div class="grid grid-cols-7 gap-1.5 mb-2">
                    <div class="text-center text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Ahd</div>
                    <div class="text-center text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Sen</div>
                    <div class="text-center text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Sel</div>
                    <div class="text-center text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Rab</div>
                    <div class="text-center text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Kam</div>
                    <!-- JUMAT MERAH -->
                    <div class="text-center text-[10px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400">Jum</div>
                    <div class="text-center text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Sab</div>
                </div>

                <div id="m3CalendarGrid" class="grid grid-cols-7 gap-1.5 md:gap-2"></div>
            </div>

            <!-- BAGIAN DETAIL HARI INI -->
            <div
                class="w-full lg:w-64 xl:w-72 border-t lg:border-t-0 lg:border-l border-zinc-200/80 dark:border-zinc-800 pt-5 lg:pt-0 lg:pl-6 shrink-0 flex flex-col">
                <div class="bg-primary/5 dark:bg-primary-dark/10 rounded-xl p-4 border border-primary/15 dark:border-primary-dark/20 mb-4 text-center">
                    <p id="detailPasaran" class="text-[10px] font-black text-primary dark:text-primary-dark uppercase tracking-wider mb-0.5">-
                    </p>
                    <h3 id="detailTanggal"
                        class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white tracking-tight mb-1">-</h3>
                    <div id="detailHijriyah" class="text-xs font-bold text-zinc-600 dark:text-zinc-300">-</div>
                </div>

                <div class="flex-1">
                    <h4
                        class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider border-b border-zinc-200/80 dark:border-zinc-800 pb-2 mb-3">
                        Agenda Hari Ini</h4>
                    <div id="detailAgendaList"
                        class="flex flex-col gap-2.5 overflow-y-auto max-h-[260px] agenda-scroll pr-1">
                        <div class="text-xs text-zinc-400 italic">Pilih tanggal pada kalender.</div>
                    </div>
                </div>
            </div>

        </div>


        <!-- PANEL KANAN: GRID KALENDER M3 & DETAIL -->
        <div id="data-grid-container" class="w-full xl:w-1/3 flex flex-col gap-5 shrink-0">

            <!-- 1. CARD KATEGORI KEGIATAN -->
            <div class="m3-glass-card flex flex-col overflow-hidden">
                <div
                    class="p-3.5 px-4 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/60 flex justify-between items-center">
                    <h3 class="text-xs font-black text-zinc-900 dark:text-white flex items-center gap-2 uppercase tracking-wider">
                        <i class="bi bi-tags-fill text-primary dark:text-primary-dark"></i>
                        <span>Kategori Kegiatan</span>
                    </h3>
                    @can('create kategori-kegiatan')
                        <a href="{{ route('kategori-kegiatan.create') }}"
                            class="w-6 h-6 rounded-lg bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark hover:bg-primary/20 flex items-center justify-center transition-colors action-modal shadow-2xs"
                            title="Tambah Kategori">
                            <i class="bi bi-plus-lg text-xs font-black"></i>
                        </a>
                    @endcan
                </div>

                <!-- List Kategori -->
                <div class="flex flex-col gap-1.5 p-3 max-h-auto overflow-y-auto agenda-scroll">
                    @forelse ($kategoris as $kat)
                        <div
                            class="flex items-center justify-between p-2 rounded-lg bg-zinc-50/60 dark:bg-zinc-900/60 border border-zinc-200/60 dark:border-zinc-800/80 group hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full shadow-2xs"
                                    style="background-color: {{ $kat->kode_warna }};"></span>
                                <span
                                    class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $kat->nama_kategori }}</span>
                            </div>

                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('update kegiatan-kategori')
                                    <a href="{{ route('kategori-kegiatan.edit', $kat->id) }}"
                                        class="action-modal w-6 h-6 flex items-center justify-center rounded-md bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 hover:bg-blue-100 transition-colors">
                                        <i class="bi bi-pencil text-[10px]"></i>
                                    </a>
                                @endcan
                                @can('delete kegiatan-kategori')
                                    <form action="{{ route('kategori-kegiatan.destroy', $kat->id) }}" method="POST"
                                        class="m-0 delete-ajax">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-6 h-6 flex items-center justify-center rounded-md bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition-colors">
                                            <i class="bi bi-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-[11px] font-bold text-zinc-400 italic">Belum ada kategori kegiatan.</div>
                    @endforelse
                </div>
            </div>

            <!-- 2. CARD DAFTAR AGENDA -->
            <div class="m3-glass-card flex flex-col overflow-hidden">
                <!-- HEADER & FILTER -->
                <div
                    class="p-3.5 px-4 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/60 flex justify-between items-center">
                    <h3 class="text-xs font-black text-zinc-900 dark:text-white flex items-center gap-2 uppercase tracking-wider">
                        <i class="bi bi-list-task text-primary dark:text-primary-dark"></i>
                        <span>Daftar Agenda (1 Tahun)</span>
                    </h3>
                    @can('create kalendar-pendidikan')
                        <a href="{{ route('kalendar-pendidikan.create') }}"
                            class="w-6 h-6 rounded-lg bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark hover:bg-primary/20 flex items-center justify-center transition-colors action-modal shadow-2xs"
                            title="Tambah Agenda">
                            <i class="bi bi-plus-lg text-xs font-black"></i>
                        </a>
                    @endcan
                </div>

                <!-- LIST AGENDA -->
                <div id="agendaListContainer"
                    class="flex flex-col gap-2.5 p-4 max-h-[460px] overflow-y-auto agenda-scroll">
                    @forelse ($events as $agenda)
                        @php
                            $rawId = str_replace(['kegiatan_', 'libur_', 'ujian_'], '', $agenda['id']);
                        @endphp

                        <div
                            class="flex flex-col p-3 rounded-xl border border-zinc-200/70 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 group relative overflow-hidden hover:border-zinc-300 dark:hover:border-zinc-700 transition-colors shadow-2xs">

                            <!-- Garis Indikator Warna Solid -->
                            <div class="absolute left-0 top-0 bottom-0 w-1"
                                style="background-color: {{ $agenda['hex_color'] ?? '#3b82f6' }};"></div>

                            <div class="pl-2">
                                <div class="flex justify-between items-start mb-1.5">
                                    <h4 class="text-xs font-black text-zinc-900 dark:text-zinc-100 leading-snug">
                                        {{ $agenda['title'] }}
                                    </h4>

                                    <!-- Aksi CRUD -->
                                    <div
                                        class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity pl-2">
                                        @can('update kalendar-pendidikan')
                                            <a href="{{ route('kalendar-pendidikan.edit', ['id' => $rawId, 'tipe' => $agenda['tipe']]) }}"
                                                class="action-modal text-blue-500 hover:text-blue-600 transition-colors p-0.5"
                                                title="Edit Agenda">
                                                <i class="bi bi-pencil text-[11px]"></i>
                                            </a>
                                        @endcan
                                        @can('delete kalendar-pendidikan')
                                            <form action="{{ route('kalendar-pendidikan.destroy', $rawId) }}"
                                                method="POST" class="m-0 delete-ajax">
                                                @csrf @method('DELETE')
                                                <input type="hidden" name="jenis_agenda" value="{{ $agenda['tipe'] }}">
                                                <button type="submit"
                                                    class="text-rose-500 hover:text-rose-600 transition-colors p-0.5"
                                                    title="Hapus Agenda">
                                                    <i class="bi bi-trash text-[11px]"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span
                                        class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-md border"
                                        style="background-color: {{ $agenda['hex_color'] }}1A; color: {{ $agenda['hex_color'] }}; border-color: {{ $agenda['hex_color'] }}4D;">
                                        {{ $agenda['kategori'] }}
                                    </span>
                                </div>

                                <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 flex items-center gap-1.5">
                                    <i class="bi bi-calendar2-week"></i>
                                    {{ \Carbon\Carbon::parse($agenda['start'])->format('d M') }}
                                    @if ($agenda['start'] != $agenda['end'])
                                        <span class="opacity-50">-</span>
                                        {{ \Carbon\Carbon::parse($agenda['end'])->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($agenda['start'])->format('Y') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Empty State -->
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div
                                class="w-10 h-10 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center mb-2 border border-zinc-200 dark:border-zinc-700 text-zinc-400">
                                <i class="bi bi-inbox text-lg"></i>
                            </div>
                            <p class="text-xs font-black text-zinc-500 dark:text-zinc-400">Belum ada agenda pendidikan</p>
                            <p class="text-[10px] font-semibold text-zinc-400 mt-0.5">Tambahkan melalui form atau matriks kalender.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>





    <!-- MUAT ENGINE FALAK MABIMS -->
    <script src="{{ asset('assets/js/falak-engine.js') }}"></script>
    <script id="agendaDataJson" type="application/json">
        @json($events ?? [])
        </script>
    <script>
        // Peta Warna Tailwind ke HEX (untuk titik kecil di bawah tanggal kalender)
        const twColorMap = {
            'rose': '#e11d48',
            'red': '#dc2626',
            'orange': '#ea580c',
            'amber': '#d97706',
            'yellow': '#ca8a04',
            'lime': '#65a30d',
            'emerald': '#10b981',
            'teal': '#0f766e',
            'cyan': '#0891b2',
            'sky': '#0284c7',
            'blue': '#2563eb',
            'indigo': '#4f46e5',
            'violet': '#7c3aed',
            'purple': '#9333ea',
            'fuchsia': '#c026d3',
            'pink': '#db2777'
        };

        document.body.dataset.page = 'custom';
        window.setLoading = function(show, text = "") {
            const loader = document.getElementById('calLoader');
            if (loader) show ? loader.classList.remove('hidden') : loader.classList.add('hidden');
        };
        window.toast = function(message) {
            console.log(message);
        };

        renderMainCalendar = async function() {
            let data;
            if (appState.calendarMode === "hijri") data = await buildHijriMonthRows(appState.viewHYear, appState
                .viewHMonth);
            else data = await buildGregorianMonthRows(appState.viewGDate);
            appState.currentCalendarRows = data.rows;
        };

        renderHomeCalendar = async function() {
            await renderMainCalendar();
        };
        renderSelectedDate = async function() {};

        // INJEKSI DATA ARRAY $EVENTS DARI CONTROLLER
        let agendaLaravel = [];
        try {
            agendaLaravel = JSON.parse(document.getElementById('agendaDataJson').textContent);
        } catch (e) {
            console.warn("Agenda JSON tidak ditemukan, menggunakan fallback.");
            agendaLaravel = @json($events ?? []);
        }

        const falakGetDateEvents = getDateEvents;

        getDateEvents = function(date, hijri) {
            let baseEvents = falakGetDateEvents(date, hijri);
            let cellTime = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();

            agendaLaravel.forEach(a => {
                let start = new Date(a.start).setHours(0, 0, 0, 0);
                let end = new Date(a.end).setHours(0, 0, 0, 0);

                if (cellTime >= start && cellTime <= end) {
                    baseEvents.push({
                        id: a.id,
                        name: a.title,
                        type: a.tipe,
                        types: [a.tipe],
                        isHoliday: (a.tipe === 'libur'),
                        importance: 'major',
                        kategori: a.kategori,
                        hexColor: a.hex_color // Ambil HEX dari controller
                    });
                }
            });
            return baseEvents;
        };

        // RENDER GRID KALENDER
        function renderM3Calendar() {
            const rows = appState.currentCalendarRows;
            if (!rows || rows.length === 0) return;

            const grid = document.getElementById('m3CalendarGrid');
            const firstDow = rows[0].date.getDay();
            let html = '';

            for (let i = 0; i < firstDow; i++) html += `<div class="aspect-square bg-transparent"></div>`;

            rows.forEach(row => {
                let isGregorian = appState.calendarMode === 'gregorian';
                let mainTgl = isGregorian ? row.date.getDate() : toArabicNum(row.hijri.day);
                let subTgl = isGregorian ? row.hijri.day : row.date.getDate();

                let isHariJumat = row.date.getDay() === 5;
                let isToday = sameDate(row.date, appState.today);
                let isSelected = sameDate(row.date, appState.selectedDate);

                let hasLibur = row.isHoliday || isHariJumat;

                let bgClass =
                    "bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-zinc-300 cursor-pointer transition-all";
                let textClass = "text-zinc-700 dark:text-zinc-200";
                let fontClass = isGregorian ? "text-sm md:text-base font-black" :
                    "text-xl md:text-2xl font-bold font-amiri leading-none pt-1";
                let markerHtml = "";

                if (isToday) {
                    bgClass = "bg-primary/10 border-primary/30 cursor-pointer";
                    textClass = "text-primary";
                } else if (isSelected) {
                    bgClass =
                        "bg-zinc-800 dark:bg-zinc-100 border-zinc-800 dark:border-zinc-100 cursor-pointer shadow-lg scale-105 z-10";
                    textClass = "text-white dark:text-zinc-900";
                }

                if (hasLibur && !isSelected && !isToday) {
                    textClass = "text-rose-600 dark:text-rose-400";
                    bgClass += " bg-rose-50/50 dark:bg-rose-900/10";
                }

                // EKSTRAK WARNA TITIK MARKER (Menggunakan HEX)
                if (row.events.length > 0) {
                    markerHtml += `<div class="flex gap-1 mt-1 justify-center flex-wrap px-1">`;
                    let uniqueColors = new Set();

                    row.events.forEach(e => {
                        if (e.isHoliday || e.types.includes('national')) {
                            uniqueColors.add('#e11d48'); // Merah Libur
                        } else if (e.types.includes('islamic')) {
                            uniqueColors.add('#10b981'); // Hijau MABIMS
                        } else if (e.hexColor) {
                            uniqueColors.add(e.hexColor); // Ambil HEX dari kategori DB
                        }
                    });

                    uniqueColors.forEach(c => {
                        markerHtml +=
                            `<span class="w-1.5 h-1.5 rounded-2xl" style="background-color: ${c};"></span>`;
                    });
                    markerHtml += `</div>`;
                }

                html += `
                    <button onclick="klikTanggalM3('${dateKey(row.date)}')" class="aspect-square flex flex-col items-center justify-center rounded-2xl md:rounded-2xl relative ${bgClass} outline-none group">
                        <span class="absolute top-1.5 right-1.5 text-[8px] font-bold text-zinc-400 opacity-70">${subTgl}</span>
                        <span class="${fontClass} ${textClass}">${mainTgl}</span>
                        ${markerHtml}
                    </button>
                `;
            });

            grid.innerHTML = html;

            const first = rows[0];
            const last = rows[rows.length - 1];
            if (appState.calendarMode === 'gregorian') {
                document.getElementById('calTitleMain').textContent =
                    `${APP_MONTHS[first.date.getMonth()]} ${first.date.getFullYear()}`;
                let hs = [...new Set(rows.map(r => `${getHijriMonthLabel(r.hijri.month, "id")} ${r.hijri.year} H`))];
                document.getElementById('calTitleSub').textContent = hs.join(" · ");
            } else {
                let hijriMeta = HIJRI_MONTHS[first.hijri.month];
                document.getElementById('calTitleMain').innerHTML =
                    `<span class="font-amiri font-bold text-2xl md:text-3xl leading-none">${hijriMeta.ar} ${toArabicNum(first.hijri.year)} هـ</span>`;
                document.getElementById('calTitleSub').textContent =
                    `${hijriMeta.id} ${first.hijri.year} H · ${first.date.getDate()} ${APP_MONTHS_SHORT[first.date.getMonth()]} - ${last.date.getDate()} ${APP_MONTHS_SHORT[last.date.getMonth()]} ${last.date.getFullYear()}`;
            }
        }

        async function klikTanggalM3(key) {
            appState.selectedDate = new Date(`${key}T12:00:00`);
            renderM3Calendar();
            tampilkanDetailHariIni();
        }

        async function tampilkanDetailHariIni() {
            const date = appState.selectedDate;
            const h = await locateHijri(date);

            document.getElementById('detailPasaran').textContent =
                `${APP_DAYS[date.getDay()]} ${getPasaran(formatDateDDMMYYYY(date))}`;
            document.getElementById('detailTanggal').textContent =
                `${date.getDate()} ${APP_MONTHS[date.getMonth()]} ${date.getFullYear()}`;

            let hijriMeta = HIJRI_MONTHS[h.month];
            document.getElementById('detailHijriyah').innerHTML = `
                <span class="text-xl md:text-2xl font-bold text-emerald-600 dark:text-emerald-400 block font-amiri leading-relaxed" dir="rtl">${toArabicNum(h.day)} ${hijriMeta.ar} ${toArabicNum(h.year)} هـ</span>
                <span class="text-[11px] text-zinc-500 dark:text-zinc-400 font-semibold">${h.day} ${hijriMeta.id} ${h.year} H</span>
            `;

            const events = getDateEvents(date, h);
            const list = document.getElementById('detailAgendaList');
            if (events.length === 0) {
                list.innerHTML =
                    `<div class="text-[11px] text-zinc-400 font-semibold italic text-center py-4">Kosong. Tidak ada agenda.</div>`;
                return;
            }

            let evHtml = '';
            events.forEach(e => {
                let icon = 'bi-calendar-check';
                let warnaUtama = e.hexColor || '#0ea5e9'; // Default biru

                // Override khusus hari besar
                if (e.isHoliday || e.types.includes('national')) {
                    icon = 'bi-calendar-x';
                    warnaUtama = '#e11d48';
                } else if (e.types.includes('islamic')) {
                    icon = 'bi-moon-stars-fill';
                    warnaUtama = '#10b981';
                } else if (e.type === 'ujian') {
                    icon = 'bi-pencil-square';
                }

                // Konversi warna HEX menjadi transparan (opacity 10%) untuk background list rincian
                evHtml += `
                    <div class="flex gap-3 items-start border p-2.5 rounded-lg shadow-sm" style="background-color: ${warnaUtama}1A; border-color: ${warnaUtama}4D; color: ${warnaUtama};">
                        <div class="w-8 h-8 rounded-2xl flex items-center justify-center shrink-0" style="background-color: ${warnaUtama}33;">
                            <i class="bi ${icon} text-[11px]"></i>
                        </div>
                        <div>
                            <h5 class="text-xs font-bold mb-0.5 leading-tight text-zinc-800 dark:text-zinc-100">${e.name}</h5>
                            <span class="text-[9px] font-bold uppercase tracking-widest opacity-80" style="color: ${warnaUtama};">${e.kategori || 'Hari Besar'}</span>
                        </div>
                    </div>
                `;
            });
            list.innerHTML = evHtml;
        }

        async function geserBulan(delta) {
            document.getElementById('calLoader').classList.remove('hidden');
            setTimeout(async () => {
                await shiftCalendar(delta);
                renderM3Calendar();
                document.getElementById('calLoader').classList.add('hidden');
            }, 50);
        }

        async function switchMode(mode) {
            const btnM = document.getElementById('btnModeMasehi');
            const btnH = document.getElementById('btnModeHijriyah');
            document.getElementById('calLoader').classList.remove('hidden');

            let activeClass =
                "px-4 py-1.5 rounded-2xl text-[11px] font-bold bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm transition-all";
            let inactiveClass =
                "px-4 py-1.5 rounded-2xl text-[11px] font-bold text-zinc-500 transition-all hover:bg-zinc-200 dark:hover:bg-zinc-700";

            if (mode === 'gregorian') {
                btnM.className = activeClass;
                btnH.className = inactiveClass;
            } else {
                btnH.className = activeClass;
                btnM.className = inactiveClass;
            }

            setTimeout(async () => {
                await setCalendarMode(mode);
                await renderMainCalendar();
                renderM3Calendar();
                document.getElementById('calLoader').classList.add('hidden');
            }, 50);
        }

        // ==============================================
        // FUNGSI AJAX FILTER TANPA RELOAD
        // ==============================================
        async function filterAgendaAjax(tahunId) {
            const container = document.getElementById('agendaListContainer');

            // 1. Tampilkan animasi loading cantik di Card Kiri
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <i class="bi bi-arrow-repeat animate-spin text-3xl text-primary mb-3 block opacity-80"></i>
                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Menyinkronkan Data...</span>
                </div>
            `;

            // Tampilkan loader kecil di kalender kanan
            document.getElementById('calLoader').classList.remove('hidden');

            try {
                // 2. Ambil halaman baru di latar belakang secara rahasia
                const url = window.location.pathname + '?tahun_id=' + tahunId;
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await response.text();

                // 3. Ekstrak DOM HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 4. Timpa isi Daftar Agenda (Kiri) dengan data yang baru
                const newListHTML = doc.getElementById('agendaListContainer').innerHTML;
                container.innerHTML = newListHTML;

                // 5. Ekstrak JSON terbaru untuk Kalender (Kanan)
                const newJson = doc.getElementById('agendaDataJson').textContent;
                agendaLaravel = JSON.parse(newJson);

                // 6. Update URL di Address Bar (agar jika di-refresh tidak hilang)
                window.history.pushState({}, '', url);

                // 7. Render ulang kalender agar titik warnanya sesuai dengan tahun ajaran
                if (typeof renderM3Calendar === 'function') {
                    renderM3Calendar();
                    tampilkanDetailHariIni();
                }

                document.getElementById('calLoader').classList.add('hidden');

            } catch (error) {
                console.error("Gagal memuat AJAX: ", error);
                container.innerHTML =
                    '<div class="text-center py-5 text-rose-500 text-xs font-bold">Koneksi terputus. Gagal memuat data.</div>';
                document.getElementById('calLoader').classList.add('hidden');
            }
        }

        // INIT
        document.addEventListener('DOMContentLoaded', async () => {
            document.getElementById('calLoader').classList.remove('hidden');
            appState.calendarMode = 'hijri'; // Default Hijriyah
            appState.today = new Date();
            appState.selectedDate = new Date();
            appState.viewGDate = new Date(appState.today.getFullYear(), appState.today.getMonth(), 1);

            currentLat = -7.6453;
            currentLng = 112.9075; // Koordinat default

            const h = await locateHijri(appState.today);
            appState.viewHYear = h.year;
            appState.viewHMonth = h.month;

            await buildHijriMonthRows(appState.viewHYear, appState.viewHMonth).then(data => {
                appState.currentCalendarRows = data.rows;
                renderM3Calendar();
                tampilkanDetailHariIni();
                document.getElementById('calLoader').classList.add('hidden');
            });
        });
    </script>



</x-app-layout>
