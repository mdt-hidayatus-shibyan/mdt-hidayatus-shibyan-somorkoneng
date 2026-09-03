@section('title', 'Pembayaran Reguler')

<x-app-layout>
    <!-- HEADER & TAB MENU -->
    <div class="mb-6 flex flex-col xl:flex-row xl:items-start justify-between gap-4 relative z-10 print:hidden">

        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('tagihan-murid.menu-pembayaran')
        </div>

        <!-- Filter Tahun Pelajaran -->
        <div class="w-full xl:w-auto shrink-0">
            <form action="{{ request()->url() }}" method="GET" id="formTahun" class="m-0 relative group h-10">
                @if (request('search_nism'))
                    <input type="hidden" name="search_nism" value="{{ request('search_nism') }}">
                @endif
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10 text-zinc-400">
                    <i class="bi bi-calendar-range text-xs"></i>
                </div>
                <select name="tahun_id" onchange="document.getElementById('formTahun').submit()"
                    class="m3-input-glass w-full xl:w-56 !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none">
                    @foreach ($daftarTahun as $tahun)
                        <option value="{{ $tahun->id }}" {{ $tahunPelajaranId == $tahun->id ? 'selected' : '' }}>
                            {{ $tahun->nama_hijriyah }} | {{ $tahun->nama_masehi }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none z-10 text-zinc-400">
                    <i class="bi bi-chevron-down text-[10px] font-black"></i>
                </div>
            </form>
        </div>
    </div>

    <!-- PANEL PENCARIAN NISM -->
    <div class="m3-glass-card p-3 md:p-3.5 mb-6 shadow-2xs relative z-10 print:hidden">
        <form action="{{ request()->url() }}" method="GET" class="w-full flex flex-col sm:flex-row gap-2.5">
            <input type="hidden" name="tahun_id" value="{{ $tahunPelajaranId }}">

            <div class="relative w-full flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <input type="text" name="search_nism" value="{{ request('search_nism') }}"
                    placeholder="Ketik Nomor Induk Murid (NISM)..." required autofocus autocomplete="off"
                    class="m3-input-glass w-full !pl-9 text-xs font-bold">
            </div>

            <button type="submit"
                class="m3-btn-primary w-full sm:w-auto px-5 h-10 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5 shrink-0">
                <i class="bi bi-search"></i> <span>Cari</span>
            </button>
        </form>
    </div>

    @if ($muridTerpilih && $ruanganTerpilih)
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 md:gap-6 relative z-10">

            <!-- KIRI: KARTU PROFIL Murid -->
            <div class="xl:col-span-4">
                <div class="m3-glass-card p-5 shadow-2xs flex flex-col items-center text-center sticky top-20">

                    <!-- Foto -->
                    <div class="mb-3">
                        @if ($muridTerpilih->foto)
                            <img src="{{ asset('storage/' . $muridTerpilih->foto) }}" alt="Foto"
                                class="w-20 h-20 rounded-2xl object-cover border border-zinc-200/80 dark:border-zinc-700/80 bg-zinc-100 dark:bg-zinc-800 p-1 shadow-2xs">
                        @else
                            <div
                                class="w-20 h-20 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 font-black text-xl flex items-center justify-center border border-zinc-200/80 dark:border-zinc-700/80 p-1 shadow-2xs">
                                {{ substr($muridTerpilih->nama_lengkap, 0, 2) }}
                            </div>
                        @endif
                    </div>

                    <!-- Info Utama -->
                    <div class="mb-3.5 w-full">
                        <h2
                            class="text-base font-black text-zinc-900 dark:text-white mb-0.5 tracking-tight leading-tight">
                            {{ $muridTerpilih->nama_lengkap }}
                        </h2>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-2.5">
                            NISM: <span
                                class="text-zinc-800 dark:text-zinc-200 font-black">{{ $muridTerpilih->nism }}</span>
                        </p>
                        <div
                            class="px-2.5 py-1 bg-primary/10 text-primary dark:text-primary-dark font-black text-[10px] uppercase tracking-wider rounded-lg border border-primary/20 inline-flex items-center shadow-2xs">
                            <i class="bi bi-door-open-fill mr-1.5"></i> Kelas: {{ $ruanganTerpilih->nama_ruangan }}
                        </div>
                    </div>

                    <div class="w-full border-t border-zinc-200/60 dark:border-zinc-800 my-1"></div>

                    @php
                        $wali = $muridTerpilih->waliMurid ?? $muridTerpilih->wali_murid;
                        $isAsatidz = $wali ? (bool) ($wali->is_asatidz ?? ($wali->is_ustadz ?? false)) : false;
                        $isYatim = $muridTerpilih
                            ? strtolower($muridTerpilih->status_ayah ?? '') === 'meninggal'
                            : false;
                    @endphp

                    <!-- Info Wali & Status -->
                    <div class="w-full text-left space-y-2 pt-2.5">
                        <div
                            class="bg-zinc-100/40 dark:bg-zinc-800/30 p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">

                            <!-- Orang Tua -->
                            <div
                                class="grid grid-cols-2 gap-3 mb-2 pb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                                <div>
                                    <span
                                        class="text-[9px] font-black text-zinc-400 uppercase tracking-wider block mb-0.5">Ayah</span>
                                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 truncate block"
                                        title="{{ $wali->nama_wali ?? ($muridTerpilih->nama_ayah ?? 'Belum Diisi') }}">
                                        {{ $wali->nama_wali ?? ($muridTerpilih->nama_ayah ?? 'Belum Diisi') }}
                                    </span>
                                </div>
                                <div>
                                    <span
                                        class="text-[9px] font-black text-zinc-400 uppercase tracking-wider block mb-0.5">Ibu</span>
                                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 truncate block"
                                        title="{{ $wali->nama_wali ?? ($muridTerpilih->nama_ibu ?? 'Belum Diisi') }}">
                                        {{ $wali->nama_wali ?? ($muridTerpilih->nama_ibu ?? 'Belum Diisi') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Kampung -->
                            <div class="mb-2 pb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                                <span
                                    class="text-[9px] font-black text-zinc-400 uppercase tracking-wider block mb-0.5">Dusun/Kampung</span>
                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 block truncate">
                                    {{ $wali->kampung->nama_kampung ?? 'Belum Diisi' }}
                                </span>
                            </div>

                            <!-- Badges Status -->
                            <div>
                                <span
                                    class="text-[9px] font-black text-zinc-400 uppercase tracking-wider block mb-1.5">Status
                                    Pembayaran</span>
                                <div class="flex gap-1.5 flex-wrap">
                                    @if ($isAsatidz)
                                        <span
                                            class="bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider shadow-2xs">
                                            <i class="bi bi-star-fill mr-0.5"></i> Asatidz
                                        </span>
                                    @endif
                                    @if ($isYatim)
                                        <span
                                            class="bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider shadow-2xs">
                                            <i class="bi bi-heart-fill mr-0.5"></i> Yatim
                                        </span>
                                    @endif
                                    @if (!$isAsatidz && !$isYatim)
                                        <span
                                            class="bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider shadow-2xs">
                                            Reguler
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Tombol Cetak Rekap Lunas SPP -->
                            <div class="pt-2.5 border-t border-zinc-200/60 dark:border-zinc-800">
                                <a href="{{ route('pembayaran-tagihan.cetak-rekap-spp', [$muridTerpilih->id, $tahunPelajaranId]) }}"
                                    target="_blank"
                                    class="w-full flex items-center justify-center gap-2 h-8 px-3 rounded-xl bg-emerald-500/10 hover:bg-emerald-500 hover:text-white border border-emerald-500/25 text-emerald-600 dark:text-emerald-400 dark:hover:text-white text-[10px] font-black uppercase tracking-wider transition-all shadow-2xs group outline-none">
                                    <i class="bi bi-file-earmark-check-fill text-xs"></i>
                                    <span>Cetak Rekap SPP Lunas</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- KANAN: PANEL TAGIHAN -->
            <div class="xl:col-span-8 flex flex-col h-full">
                <form action="{{ route('pembayaran-tagihan.proses') }}" method="POST"
                    class="h-full flex flex-col gap-4">
                    @csrf
                    <input type="hidden" name="murid_id" value="{{ $muridTerpilih->id }}">
                    <input type="hidden" name="ruangan_id" value="{{ $ruanganTerpilih->id }}">
                    <input type="hidden" name="total_bayar_hidden" id="inputTotalHidden" value="0">

                    <!-- CARD 1: HEADER (Centang Semua & Total) -->
                    <div
                        class="m3-glass-card p-4 md:px-5 flex flex-col md:flex-row items-center justify-between gap-3 shrink-0 shadow-2xs">

                        <!-- Master Checkbox -->
                        <label
                            class="flex items-center gap-2.5 cursor-pointer select-none group w-full md:w-auto justify-center md:justify-start">
                            <input type="checkbox" id="chkMasterSemua" onchange="centangSemuaTagihan(this)"
                                class="sr-only peer">
                            <div
                                class="w-5 h-5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white/40 dark:bg-black/40 text-primary peer-checked:bg-primary peer-checked:border-primary flex items-center justify-center transition-all group-hover:border-primary/50 relative shadow-2xs">
                                <i
                                    class="bi bi-check-lg text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none font-black"></i>
                            </div>
                            <span
                                class="text-xs font-black text-zinc-800 dark:text-zinc-200 group-hover:text-primary transition-colors uppercase tracking-wider">
                                Centang Semua
                            </span>
                        </label>

                        <!-- Kalkulator & Tombol Bayar -->
                        <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
                            <div class="text-right flex flex-col justify-center mr-2">
                                <p
                                    class="text-[9px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                                    TOTAL BAYAR</p>
                                <p class="text-xl font-black text-zinc-900 dark:text-white leading-none mt-0.5"
                                    id="teksTotal">Rp 0</p>
                            </div>
                            <button type="submit" id="btnProses" disabled
                                class="m3-btn-primary h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="bi bi-wallet2 text-xs"></i> <span>Proses Bayar</span>
                            </button>
                        </div>
                    </div>

                    <!-- DAFTAR TAGIHAN -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar space-y-3 pb-10 mt-1"
                        style="max-height: calc(100vh - 220px);">
                        @php
                            $tagihanSPP = [];
                            $tagihanLainnya = [];
                            foreach ($semuaTagihan as $tagihan) {
                                $namaClean = strtolower(trim($tagihan->nama_tagihan_spesifik));
                                if (str_contains($namaClean, 'spp') || str_contains($namaClean, 'syahriyah')) {
                                    $tagihanSPP[] = $tagihan;
                                } else {
                                    $tagihanLainnya[] = $tagihan;
                                }
                            }
                        @endphp

                        @if (count($semuaTagihan) === 0)
                            <div class="col-span-full">
                                <x-empty-state icon="bi-receipt" title="Belum Ada Faktur"
                                    message="Sistem belum menerbitkan tagihan untuk Murid ini." />
                            </div>
                        @else
                            <!-- CARD DAFTAR SPP -->
                            @if (count($tagihanSPP) > 0)
                                @php
                                    $adaTunggakanSPP = collect($tagihanSPP)->contains('status_bayar', 'Belum Lunas');
                                @endphp
                                <div class="m3-glass-card overflow-hidden shadow-2xs">

                                    <!-- Header Card SPP -->
                                    <div
                                        class="flex items-center px-4 py-3 bg-zinc-100/50 dark:bg-zinc-800/40 border-b border-zinc-200/80 dark:border-zinc-800">

                                        <div class="flex-1 flex items-center gap-2.5">
                                            <div
                                                class="w-8 h-8 rounded-xl bg-primary/10 text-primary dark:text-primary-dark border border-primary/20 flex items-center justify-center shrink-0">
                                                <i class="bi bi-calendar2-check-fill text-sm"></i>
                                            </div>
                                            <div class="text-left">
                                                <span
                                                    class="font-black text-zinc-900 dark:text-white text-xs block leading-tight uppercase tracking-wider">Tagihan
                                                    Syahriyah/SPP</span>
                                                <span
                                                    class="text-[10px] font-bold text-zinc-400 mt-0.5 block">{{ count($tagihanSPP) }}
                                                    Item</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Isi Daftar SPP -->
                                    <div class="p-3 space-y-2">
                                        @foreach ($tagihanSPP as $tagihan)
                                            @if ($tagihan->status_bayar === 'Belum Lunas')
                                                <!-- ROW: TUNGGAKAN -->
                                                <label
                                                    class="relative flex flex-col sm:flex-row sm:items-center justify-between px-3 py-2 bg-white/40 dark:bg-zinc-900/30 border border-zinc-200/80 dark:border-zinc-800 rounded-xl cursor-pointer hover:border-primary/50 transition-all gap-2 group has-[:checked]:bg-primary/5 has-[:checked]:border-primary dark:has-[:checked]:bg-primary/10 shadow-2xs">
                                                    <div class="flex items-center gap-2.5 flex-1">
                                                        <input type="checkbox" name="tagihan_ids[]"
                                                            value="{{ $tagihan->id }}"
                                                            data-nominal="{{ $tagihan->nominal_tagihan }}"
                                                            onchange="hitungTotal()" class="chk-bayar sr-only peer">
                                                        <div
                                                            class="w-5 h-5 rounded-lg flex items-center justify-center border border-zinc-300 dark:border-zinc-600 bg-white/40 dark:bg-black/40 peer-checked:bg-primary peer-checked:border-primary transition-all shrink-0 group-hover:border-primary/50 relative shadow-2xs">
                                                            <i
                                                                class="bi bi-check-lg text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none font-black"></i>
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-1.5 mb-0.5">
                                                                <p
                                                                    class="text-xs font-black text-zinc-900 dark:text-white leading-tight">
                                                                    {{ $tagihan->nama_tagihan_spesifik }}
                                                                </p>

                                                                @if ($isAsatidz)
                                                                    <span
                                                                        class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[8px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-md border border-emerald-500/20"
                                                                        title="Tagihan atas permintaan/kesukarelaan wali">
                                                                        SUKARELA
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <span
                                                                class="bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-md">Tunggakan</span>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="text-xs font-black text-zinc-800 dark:text-zinc-200 text-left sm:text-right ml-7 sm:ml-0 shrink-0">
                                                        <span
                                                            class="text-[9px] text-zinc-400 mr-0.5">Rp</span>{{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}
                                                    </div>
                                                </label>
                                            @else
                                                <!-- ROW: SUDAH LUNAS -->
                                                <div
                                                    class="flex flex-col sm:flex-row sm:items-center justify-between px-3 py-2 bg-emerald-500/5 border border-emerald-500/20 rounded-xl gap-2 shadow-2xs">

                                                    <!-- Kiri: Info Nama Tagihan & No Kwitansi -->
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="w-5 h-5 flex items-center justify-center text-emerald-500 shrink-0">
                                                            <i class="bi bi-check-circle-fill text-sm"></i>
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-1.5 mb-0.5">
                                                                <p
                                                                    class="text-xs font-bold text-zinc-900 dark:text-zinc-300 line-through decoration-zinc-400/50 decoration-2">
                                                                    {{ $tagihan->nama_tagihan_spesifik }}
                                                                </p>

                                                                @if ($isAsatidz)
                                                                    <span
                                                                        class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[8px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-md border border-emerald-500/20"
                                                                        title="Tagihan atas permintaan/kesukarelaan wali">
                                                                        SUKARELA
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <span
                                                                    class="bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-md border border-emerald-500/20">Lunas</span>

                                                                @if ($tagihan->pembayaranTagihan)
                                                                    <span
                                                                        class="text-[9px] font-bold text-zinc-400 uppercase tracking-wider border-l border-zinc-300 dark:border-zinc-700 pl-2">
                                                                        {{ $tagihan->pembayaranTagihan->no_kwitansi ?? ($tagihan->pembayaranTagihan->no_transaksi ?? '-') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Kanan: Nominal & Tombol Aksi -->
                                                    <div
                                                        class="flex flex-col sm:items-end gap-1 w-full sm:w-auto ml-7 sm:ml-0">
                                                        <span
                                                            class="text-[11px] font-bold text-zinc-400 dark:text-zinc-500 line-through decoration-zinc-400/50">
                                                            Rp
                                                            {{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}
                                                        </span>

                                                        @if ($tagihan->pembayaranTagihan)
                                                            <div class="flex items-center gap-1.5 w-full shrink-0">
                                                                <!-- Cetak -->
                                                                <a href="{{ route('pembayaran-tagihan.cetak', $tagihan->pembayaran_tagihan_id) }}"
                                                                    target="_blank"
                                                                    class="flex-1 sm:flex-none flex items-center justify-center gap-1 h-6 px-2.5 rounded-lg bg-sky-500/10 hover:bg-sky-500 hover:text-white border border-sky-500/20 text-sky-600 dark:text-sky-400 dark:hover:text-white transition-all shadow-2xs group/btn outline-none text-[10px] font-black uppercase tracking-wider"
                                                                    title="Cetak Kwitansi">
                                                                    <i class="bi bi-printer-fill text-[10px]"></i>
                                                                    <span class="sm:hidden">Cetak</span>
                                                                </a>

                                                                <!-- Batal -->
                                                                <button type="button"
                                                                    onclick="konfirmasiBatal({{ $tagihan->id }}, '{{ addslashes($tagihan->nama_tagihan_spesifik) }}')"
                                                                    class="flex-1 sm:flex-none flex items-center justify-center gap-1 h-6 px-2.5 rounded-lg bg-rose-500/10 hover:bg-rose-500 hover:text-white border border-rose-500/20 text-rose-500 dark:text-rose-400 dark:hover:text-white transition-all shadow-2xs outline-none group/btn text-[10px] font-black uppercase tracking-wider"
                                                                    title="Batalkan Pembayaran">
                                                                    <i class="bi bi-x-lg text-[10px]"></i>
                                                                    <span class="sm:hidden">Batal</span>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @elseif(request('search_nism') && (!$muridTerpilih || !$ruanganTerpilih))
        <!-- ERROR STATE: NISM Tidak Valid -->
        <div class="col-span-full">
            <x-empty-state icon="bi-exclamation-triangle" title="Murid Tidak Ditemukan"
                message="NISM yang dimasukkan salah, atau Murid tersebut belum memiliki catatan penempatan kelas pada Tahun Pelajaran yang Anda pilih saat ini." />
        </div>
    @endif

    <form id="formBatal" method="POST" class="hidden">
        @csrf
    </form>

    <!-- SCRIPTS -->
    <script>
        function hitungTotal() {
            let total = 0;
            let checkedCount = 0;
            const checkboxes = document.querySelectorAll('.chk-bayar');
            const totalCheckboxes = checkboxes.length;

            checkboxes.forEach(chk => {
                if (chk.checked) {
                    total += parseInt(chk.getAttribute('data-nominal') || 0);
                    checkedCount++;
                }
            });

            document.getElementById('teksTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('inputTotalHidden').value = total;

            const btn = document.getElementById('btnProses');
            if (btn) btn.disabled = (checkedCount === 0);

            const chkMasterSemua = document.getElementById('chkMasterSemua');
            if (chkMasterSemua && totalCheckboxes > 0) {
                chkMasterSemua.checked = (checkedCount === totalCheckboxes);
            }
        }

        function centangSemuaTagihan(masterCheckbox) {
            const checkboxes = document.querySelectorAll('.chk-bayar');
            checkboxes.forEach(chk => {
                chk.checked = masterCheckbox.checked;
            });
            hitungTotal();
        }

        function konfirmasiBatal(id, nama) {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: '<span class="text-base font-black tracking-tight">Batalkan Pelunasan?</span>',
                html: `<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Apakah Anda yakin ingin membatalkan kwitansi pelunasan <b class="text-rose-500">${nama}</b>?<br>Status tagihan akan dikembalikan menjadi Tunggakan.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#71717a',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tutup',
                background: isDark ? '#0c0c0e' : '#ffffff',
                color: isDark ? '#f4f4f5' : '#18181b',
                heightAuto: false,
                customClass: {
                    popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                    confirmButton: "h-10 px-5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none",
                    cancelButton: "h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formBatal');
                    if (form) {
                        form.action = `/pembayaran-tagihan/batal/${id}`;
                        form.submit();
                    } else {
                        window.location.href = `/pembayaran-tagihan/batal/${id}`;
                    }
                }
            });
        }
    </script>

</x-app-layout>
