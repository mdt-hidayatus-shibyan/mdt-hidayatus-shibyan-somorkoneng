@section('title', 'Kertas - Pelanggaran Murid')

<x-app-layout>
    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div class="w-full xl:w-auto shrink-0">
            @include('pelanggaran-murid.menu')
        </div>
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('pelanggaran-murid.adminMode') }}" method="GET"
                class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto m3-glass-card p-1.5 shadow-2xs">

                <!-- Filter Ruangan -->
                <div class="relative w-full sm:w-48 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-door-open text-xs"></i>
                    </div>
                    <select name="ruangan_id" required
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Ruangan --</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ $ruangan_id == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Filter Bulan Hijriyah -->
                <div class="relative w-full sm:w-56 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-moon-stars text-xs"></i>
                    </div>
                    <select name="bulan_id" required
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">Pilih Bulan Hijriyah...
                        </option>
                        @foreach ($bulans as $b)
                            <option value="{{ $b->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ $bulan_id == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_bulan }} {{ $b->tahun_hijriyah }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Tombol Submit Tampilkan -->
                <div class="w-full sm:w-auto shrink-0">
                    <button type="submit"
                        class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs group/btn">
                        <i class="bi bi-search text-xs mr-1"></i>
                        <span>Tampilkan</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    @if ($ruangan_id && $bulan_id)
        <!-- AREA SPREADSHEET KERTAS KERJA (M3 GLASS) -->
        <div
            class="m3-glass-card flex flex-col flex-1 min-h-[400px] relative overflow-hidden">

            <!-- Header Panel -->
            <div
                class="px-5 md:px-6 py-4 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10 shrink-0">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-lg shadow-2xs shrink-0">
                        <i class="bi bi-grid-3x2-gap"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-zinc-900 dark:text-white text-base tracking-tight flex items-center gap-2 leading-tight">
                            Kertas Kerja Pelanggaran
                            <span
                                class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 shadow-2xs">
                                Live Sync
                            </span>
                        </h3>
                        <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">
                            Catat kasus cepat bergaya spreadsheet
                        </p>
                    </div>
                </div>

                <button onclick="simpanPelanggaranReaktif()"
                    class="w-full sm:w-auto h-10 px-5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-2xs hover:shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2 outline-none shrink-0">
                    <i class="bi bi-cloud-arrow-up-fill text-sm"></i>
                    <span>Simpan Sinkronisasi</span>
                </button>
            </div>

            <!-- Area Table Scrollable -->
            <div class="flex-1 overflow-auto custom-scrollbar relative z-10 p-0">
                <table class="w-full min-w-[950px] text-left border-collapse text-xs">
                    <thead
                        class="bg-zinc-50/90 dark:bg-zinc-950/80 border-b border-zinc-200/80 dark:border-zinc-800 sticky top-0 z-20 backdrop-blur-md">
                        <tr class="text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            <th class="px-3 py-3 border-r border-zinc-200/60 dark:border-zinc-800/80 text-center w-12">No</th>
                            <th class="px-3 py-3 border-r border-zinc-200/60 dark:border-zinc-800/80 text-center w-[130px]">Tanggal</th>
                            <th class="px-4 py-3 border-r border-zinc-200/60 dark:border-zinc-800/80 w-[140px] text-center">NISM</th>
                            <th class="px-4 py-3 border-r border-zinc-200/60 dark:border-zinc-800/80 min-w-[240px]">Nama Santri</th>
                            <th class="px-4 py-3 border-r border-zinc-200/60 dark:border-zinc-800/80 w-[110px] text-center">Kode Kasus</th>
                            <th class="px-4 py-3 border-r border-zinc-200/60 dark:border-zinc-800/80 min-w-[260px]">Kasus / Skor Poin</th>
                            <th class="px-4 py-3 border-r border-zinc-200/60 dark:border-zinc-800/80 min-w-[180px]">Keterangan</th>
                            <th class="px-3 py-3 text-center w-12"><i class="bi bi-eraser-fill text-xs opacity-70"></i></th>
                        </tr>
                    </thead>
                    <tbody id="vioGridTbody"
                        class="divide-y divide-zinc-200/60 dark:divide-zinc-800/80 bg-white/40 dark:bg-zinc-900/40">
                        <!-- Diisi via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Footer Toolbar -->
            <div
                class="px-5 py-3.5 flex flex-wrap justify-center gap-3 border-t border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/50 relative z-10 shrink-0">
                <button onclick="tambahBarisPelanggaran(5)"
                    class="px-4 py-2 bg-white/80 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-500/30 rounded-xl text-xs uppercase tracking-wider font-black transition-all shadow-2xs flex items-center gap-1.5 outline-none active:scale-95">
                    <i class="bi bi-plus-lg text-xs"></i> Tambah 5 Baris
                </button>
                <button onclick="hapusBarisKosong()"
                    class="px-4 py-2 bg-white/80 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:text-rose-600 dark:hover:text-rose-400 hover:border-rose-500/30 rounded-xl text-xs uppercase tracking-wider font-black transition-all shadow-2xs flex items-center gap-1.5 outline-none active:scale-95">
                    <i class="bi bi-dash-lg text-xs"></i> Bersihkan Baris Kosong
                </button>
            </div>
        </div>

        <datalist id="listMuridVioGrid">
            @foreach ($allMurids as $m)
                <option value="{{ $m['nism'] }}">{{ $m['nama'] }}</option>
            @endforeach
        </datalist>
    @else
        <!-- State Awal saat halaman baru dibuka -->
        <div class="py-16 text-center m3-glass-card relative z-10">
            <div
                class="w-12 h-12 bg-rose-500/10 border border-rose-500/20 text-rose-500 dark:text-rose-400 rounded-2xl flex items-center justify-center text-2xl mb-3 mx-auto shadow-2xs">
                <i class="bi bi-grid-3x2-gap"></i>
            </div>
            <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight mb-0.5">Kertas Kerja Belum Terbuka</h3>
            <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                Tentukan Ruangan dan Bulan Hijriyah pada filter di atas untuk menampilkan lembar kerja terintegrasi.
            </p>
        </div>
    @endif

    <script>
        const masterMurids = @json($allMurids ?? []);
        const masterPelanggarans = @json($allPelanggarans ?? []);
        const dbExistingData = @json($existingData ?? []);

        let barisVioCount = 0;

        document.addEventListener('DOMContentLoaded', function() {
            if (dbExistingData.length >= 0 && document.getElementById('vioGridTbody')) {
                renderTabelPelanggaranReaktif();
            }
        });

        function renderTabelPelanggaranReaktif() {
            const tb = document.getElementById("vioGridTbody");
            if (!tb) return;

            tb.innerHTML = "";
            barisVioCount = 0;
            let html = "";

            dbExistingData.forEach((p) => {
                barisVioCount++;
                html += `
                <tr class="hover:bg-zinc-500/5 transition-colors baris-vio group/row" data-id="${p.id}" data-valid-nism="true" data-valid-kode="true" data-ref-id="${p.referensi_pelanggaran_id}" data-ref-poin="${p.poin}">
                    <td class="px-2 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 text-center font-bold text-zinc-400 dark:text-zinc-500 text-xs align-middle">${barisVioCount}</td>
                    
                    <!-- INPUT TANGGAL (DATA EXISTING) -->
                    <td class="px-2.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <input type="date" value="${p.tanggal}" data-field="tanggal" required
                            class="w-full bg-white/70 dark:bg-zinc-900/70 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 px-2 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-bold text-[11px] text-center shadow-2xs transition-all cursor-pointer">
                    </td>
                    
                    <td class="px-2.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <input type="text" list="listMuridVioGrid" oninput="checkNimmGrid(this)" value="${p.nism}" 
                            class="w-full bg-white/70 dark:bg-zinc-900/70 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 px-2.5 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-mono font-bold text-xs text-center shadow-2xs transition-all" 
                            placeholder="NISM..." data-field="nism" autocomplete="off">
                    </td>
                    
                    <td class="px-3.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <div class="display-murid">
                            <div class="font-black text-xs text-zinc-900 dark:text-zinc-100 truncate max-w-[220px]" title="${p.nama_murid}">${p.nama_murid}</div>
                            <div class="text-[9px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider mt-0.5 truncate"><i class="bi bi-door-open mr-1 opacity-70"></i>${p.nama_ruangan}</div>
                        </div>
                    </td>
                    
                    <td class="px-2.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <input type="number" oninput="checkNomorGrid(this)" value="${p.referensi_pelanggaran_id}" 
                            class="w-full bg-white/70 dark:bg-zinc-900/70 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 px-2.5 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-bold text-xs text-center shadow-2xs transition-all" 
                            placeholder="No..." data-field="kode">
                    </td>
                    
                    <td class="px-3.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <div class="display-vio">
                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-200 truncate max-w-[260px] leading-tight" title="${p.nama_pelanggaran}">${p.nama_pelanggaran}</div>
                            <div class="text-[9px] text-rose-600 dark:text-rose-400 bg-rose-500/10 border border-rose-500/20 font-black mt-1 inline-block px-1.5 py-0.5 rounded uppercase tracking-wider shadow-2xs">Poin: ${p.poin}</div>
                        </div>
                    </td>
                    
                    <td class="px-2.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <input type="text" value="${p.keterangan || ''}" 
                            class="w-full bg-white/70 dark:bg-zinc-900/70 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 px-2.5 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs shadow-2xs transition-all placeholder-zinc-400" 
                            placeholder="Catatan..." data-field="keterangan">
                    </td>
                    
                    <td class="px-2 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 text-center align-middle">
                        <button type="button" onclick="resetBarisGrid(this)" 
                            class="text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 bg-zinc-100 dark:bg-zinc-800 hover:bg-rose-500/10 dark:hover:bg-rose-500/20 w-7 h-7 rounded-lg transition-colors mx-auto flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-2xs active:scale-95 outline-none" 
                            title="Kosongkan / Batalkan">
                            <i class="bi bi-eraser-fill text-xs"></i>
                        </button>
                    </td>
                </tr>`;
            });

            tb.innerHTML = html;
            tambahBarisPelanggaran(5);
        }

        function tambahBarisPelanggaran(jumlahBaris) {
            const tb = document.getElementById("vioGridTbody");
            let html = "";
            const tglHariIni = new Date().toISOString().split("T")[0];

            for (let i = 0; i < jumlahBaris; i++) {
                barisVioCount++;
                const virtualId = "VIO-" + Date.now() + "-" + Math.floor(Math.random() * 1000);
                html += `
                <tr class="hover:bg-zinc-500/5 transition-colors baris-vio group/row" data-id="${virtualId}">
                    <td class="px-2 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 text-center font-bold text-zinc-300 dark:text-zinc-600 text-xs align-middle">${barisVioCount}</td>
                    
                    <!-- INPUT TANGGAL (BARIS BARU) -->
                    <td class="px-2.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <input type="date" value="${tglHariIni}" data-field="tanggal" required
                            class="w-full bg-white/70 dark:bg-zinc-900/70 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 px-2 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-bold text-[11px] text-center shadow-2xs transition-all cursor-pointer">
                    </td>

                    <td class="px-2.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <input type="text" list="listMuridVioGrid" oninput="checkNimmGrid(this)" 
                            class="w-full bg-white/70 dark:bg-zinc-900/70 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 px-2.5 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-mono font-bold text-xs text-center shadow-2xs transition-all placeholder-zinc-400" 
                            placeholder="NISM..." data-field="nism" autocomplete="off">
                    </td>
                    
                    <td class="px-3.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <div class="display-murid text-zinc-400 dark:text-zinc-500 italic text-[10px] font-bold tracking-wider uppercase">Ketik NISM...</div>
                    </td>
                    
                    <td class="px-2.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <input type="number" oninput="checkNomorGrid(this)" 
                            class="w-full bg-white/70 dark:bg-zinc-900/70 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 px-2.5 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 font-bold text-xs text-center shadow-2xs transition-all placeholder-zinc-400" 
                            placeholder="No..." data-field="kode">
                    </td>
                    
                    <td class="px-3.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <div class="display-vio text-zinc-400 dark:text-zinc-500 italic text-[10px] font-bold tracking-wider uppercase">Ketik Kode...</div>
                    </td>
                    
                    <td class="px-2.5 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 border-r border-r-zinc-200/60 dark:border-r-zinc-800/80 align-middle">
                        <input type="text" class="w-full bg-white/70 dark:bg-zinc-900/70 border border-zinc-200/80 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 px-2.5 py-1.5 rounded-lg outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs shadow-2xs transition-all placeholder-zinc-400" 
                            placeholder="Catatan..." data-field="keterangan">
                    </td>
                    
                    <td class="px-2 py-2.5 border-b border-zinc-200/60 dark:border-zinc-800/80 text-center align-middle">
                        <button type="button" onclick="resetBarisGrid(this)" 
                            class="text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 bg-zinc-100 dark:bg-zinc-800 hover:bg-rose-500/10 dark:hover:bg-rose-500/20 w-7 h-7 rounded-lg transition-colors mx-auto flex items-center justify-center border border-zinc-200 dark:border-zinc-700 shadow-2xs active:scale-95 outline-none" 
                            title="Kosongkan / Batalkan">
                            <i class="bi bi-eraser-fill text-xs"></i>
                        </button>
                    </td>
                </tr>`;
            }
            tb.insertAdjacentHTML("beforeend", html);
        }

        function checkNimmGrid(inputEl) {
            const tr = inputEl.closest("tr");
            const displayEl = tr.querySelector(".display-murid");
            const n = inputEl.value.trim();

            if (!n) {
                displayEl.innerHTML =
                    `<span class="text-zinc-400 dark:text-zinc-500 italic text-[10px] font-bold tracking-wider uppercase">Ketik NISM...</span>`;
                tr.removeAttribute("data-valid-nism");
                return;
            }

            const matchMurid = masterMurids.find((m) => String(m.nism) === n);

            if (matchMurid) {
                displayEl.innerHTML =
                    `
                    <div class="font-black text-xs text-zinc-900 dark:text-zinc-100 truncate max-w-[220px]" title="${matchMurid.nama}">${matchMurid.nama}</div>
                    <div class="text-[9px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider mt-0.5"><i class="bi bi-door-open mr-1 opacity-70"></i>${matchMurid.nama_ruangan}</div>`;
                tr.setAttribute("data-valid-nism", "true");
            } else {
                displayEl.innerHTML =
                    `<span class="text-rose-600 dark:text-rose-400 text-[9px] font-black uppercase tracking-wider bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20 shadow-2xs"><i class="bi bi-x-circle mr-1"></i> Tidak ditemukan</span>`;
                tr.setAttribute("data-valid-nism", "false");
            }
        }

        function checkNomorGrid(inputEl) {
            const tr = inputEl.closest("tr");
            const displayEl = tr.querySelector(".display-vio");
            const n = inputEl.value.trim();

            if (!n) {
                displayEl.innerHTML =
                    `<span class="text-zinc-400 dark:text-zinc-500 italic text-[10px] font-bold tracking-wider uppercase">Ketik Kode...</span>`;
                tr.removeAttribute("data-valid-kode");
                return;
            }

            const matchRef = masterPelanggarans.find((r) => String(r.kode) === n);

            if (matchRef) {
                displayEl.innerHTML =
                    `
                    <div class="text-xs font-bold text-zinc-900 dark:text-zinc-200 truncate max-w-[260px] leading-tight" title="${matchRef.ket}">${matchRef.ket}</div>
                    <div class="text-[9px] text-rose-600 dark:text-rose-400 bg-rose-500/10 border border-rose-500/20 font-black mt-1 inline-block px-1.5 py-0.5 rounded uppercase tracking-wider shadow-2xs">Poin: ${matchRef.poin}</div>`;
                tr.setAttribute("data-valid-kode", "true");
                tr.setAttribute("data-ref-id", matchRef.id);
                tr.setAttribute("data-ref-poin", matchRef.poin);
            } else {
                displayEl.innerHTML =
                    `<span class="text-rose-600 dark:text-rose-400 text-[9px] font-black uppercase tracking-wider bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20 shadow-2xs"><i class="bi bi-x-circle mr-1"></i> Kode Salah</span>`;
                tr.setAttribute("data-valid-kode", "false");
            }
        }

        function resetBarisGrid(btn) {
            const tr = btn.closest("tr");

            tr.querySelectorAll("input[type='text'], input[type='number']").forEach((inp) => {
                inp.value = "";
            });

            const tglHariIni = new Date().toISOString().split("T")[0];
            tr.querySelector('[data-field="tanggal"]').value = tglHariIni;

            tr.querySelector('[data-field="nism"]').dispatchEvent(new Event("input"));
            tr.querySelector('[data-field="kode"]').dispatchEvent(new Event("input"));
        }

        function hapusBarisKosong() {
            const tb = document.getElementById("vioGridTbody");
            const rows = tb.querySelectorAll(".baris-vio");
            let adaHapus = false;

            rows.forEach(tr => {
                const rowId = tr.getAttribute("data-id");
                const inpNism = tr.querySelector('[data-field="nism"]').value.trim();
                const inpKode = tr.querySelector('[data-field="kode"]').value.trim();

                if (!isNumeric(rowId) && !inpNism && !inpKode) {
                    tr.remove();
                    adaHapus = true;
                }
            });

            if (adaHapus) {
                let index = 1;
                document.querySelectorAll("#vioGridTbody tr").forEach(tr => {
                    tr.firstElementChild.innerText = index++;
                });
                barisVioCount = index - 1;
            }
        }

        function isNumeric(str) {
            if (typeof str != "string") return false;
            return !isNaN(str) && !isNaN(parseFloat(str));
        }

        function simpanPelanggaranReaktif() {
            const rows = document.querySelectorAll("#vioGridTbody .baris-vio");
            const dataToSave = [];
            const dataToDelete = [];
            let adaError = false;

            rows.forEach((tr) => {
                const rowId = tr.getAttribute("data-id");
                const inpTanggal = tr.querySelector('[data-field="tanggal"]').value;
                const inpNism = tr.querySelector('[data-field="nism"]').value.trim();
                const inpKode = tr.querySelector('[data-field="kode"]').value.trim();
                const inpKeterangan = tr.querySelector('[data-field="keterangan"]').value.trim();

                const isEmpty = !inpNism && !inpKode;

                if (isOriginalDatabaseId(rowId) && isEmpty) {
                    dataToDelete.push(rowId);
                } else if (!isEmpty) {
                    const isNismValid = tr.getAttribute("data-valid-nism") === "true";
                    const isKodeValid = tr.getAttribute("data-valid-kode") === "true";

                    if (isNismValid && isKodeValid && inpTanggal !== "") {
                        dataToSave.push({
                            id: rowId,
                            tanggal: inpTanggal,
                            nism: inpNism,
                            referensi_pelanggaran_id: tr.getAttribute("data-ref-id"),
                            keterangan: inpKeterangan
                        });
                    } else {
                        adaError = true;
                    }
                }
            });

            const isDark = document.documentElement.classList.contains('dark');

            if (adaError) {
                return Swal.fire({
                    icon: "error",
                    title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Data Belum Valid</span>',
                    html: '<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-1">Ada baris dengan NISM atau Kode Kasus yang salah. Perbaiki atau kosongkan baris tersebut.</p>',
                    confirmButtonColor: '#e11d48',
                    background: isDark ? '#121215' : '#ffffff',
                    heightAuto: false,
                    customClass: {
                        popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl',
                        confirmButton: 'rounded-xl font-bold px-6 py-2.5 text-xs'
                    }
                });
            }

            if (dataToSave.length === 0 && dataToDelete.length === 0) {
                return Swal.fire({
                    icon: "info",
                    title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Tidak Ada Perubahan</span>',
                    html: '<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-1">Seluruh lembar kerja Anda masih sama dengan data di server.</p>',
                    confirmButtonColor: '#0ea5e9',
                    background: isDark ? '#121215' : '#ffffff',
                    heightAuto: false,
                    customClass: {
                        popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl',
                        confirmButton: 'rounded-xl font-bold px-6 py-2.5 text-xs'
                    }
                });
            }

            Swal.fire({
                title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Konfirmasi Sinkronisasi</span>',
                html: `<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-3">Sistem akan memproses lembar kerja Anda:</p>
                       <div class="text-xs font-black text-emerald-600 dark:text-emerald-400 mb-1"><i class="bi bi-check-circle-fill mr-1"></i> ${dataToSave.length} Data Disimpan/Diubah</div>
                       <div class="text-xs font-black text-rose-600 dark:text-rose-400"><i class="bi bi-x-circle-fill mr-1"></i> ${dataToDelete.length} Data Dikosongkan (Dihapus)</div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: isDark ? '#27272a' : '#e4e4e7',
                confirmButtonText: '<i class="bi bi-cloud-arrow-up-fill mr-1"></i> Sinkronisasikan',
                cancelButtonText: '<span class="text-zinc-700 dark:text-zinc-300">Batal</span>',
                background: isDark ? '#121215' : '#ffffff',
                heightAuto: false,
                customClass: {
                    popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl',
                    confirmButton: 'rounded-xl font-bold px-5 py-2.5 text-xs',
                    cancelButton: 'rounded-xl font-bold px-5 py-2.5 text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '<span class="text-base font-bold text-zinc-900 dark:text-white">Menyinkronkan Data...</span>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        background: isDark ? '#121215' : '#ffffff',
                        heightAuto: false,
                        customClass: {
                            popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl'
                        },
                        didOpen: () => Swal.showLoading()
                    });

                    fetch("{{ route('pelanggaran-murid.syncAdminMode') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                ruangan_id: "{{ $ruangan_id }}",
                                tahun_pelajaran_id: "{{ $tahun_pelajaran_id }}",
                                semester_id: "{{ $semester_id }}",
                                data_to_save: dataToSave,
                                data_to_delete: dataToDelete
                            })
                        })
                        .then(response => response.json())
                        .then(res => {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: "success",
                                    title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Berhasil!</span>',
                                    html: `<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">${res.message}</p>`,
                                    timer: 2000,
                                    showConfirmButton: false,
                                    background: isDark ? '#121215' : '#ffffff',
                                    heightAuto: false,
                                    customClass: {
                                        popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl'
                                    }
                                });
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                throw new Error('Terjadi kegagalan sistem internal');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: "error",
                                title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Koneksi Terputus</span>',
                                html: '<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Gagal mengirimkan sinkronisasi ke server. Periksa jaringan Anda.</p>',
                                confirmButtonColor: '#e11d48',
                                background: isDark ? '#121215' : '#ffffff',
                                heightAuto: false,
                                customClass: {
                                    popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl',
                                    confirmButton: 'rounded-xl font-bold px-6 py-2.5 text-xs'
                                }
                            });
                        });
                }
            });
        }

        function isOriginalDatabaseId(id) {
            return id && !id.startsWith("VIO-");
        }
    </script>
</x-app-layout>

