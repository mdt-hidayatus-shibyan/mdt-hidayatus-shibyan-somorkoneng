@section('title', 'Pembayaran Donatur')

<x-app-layout>
    <!-- HEADER & TAB MENU -->
    <div
        class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-20 print:hidden">

        <!-- Area Tab Menu -->
        <div class="w-full xl:w-auto shrink-0">
            @include('tagihan-murid.menu-pembayaran')
        </div>

        <!-- Filter Tahun & Bulan -->
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ request()->url() }}" method="GET"
                class="flex flex-col sm:flex-row items-center gap-2 w-full xl:w-auto m3-glass-card p-2 shadow-2xs">

                <!-- Filter Tahun Pelajaran -->
                <div class="relative w-full sm:w-[200px] h-10">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none z-10 text-zinc-400">
                        <i class="bi bi-calendar-range text-xs"></i>
                    </div>
                    <select name="tahun_id" onchange="this.form.submit()"
                        class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none">
                        @foreach ($daftarTahun as $tahun)
                            <option value="{{ $tahun->id }}" {{ $tahunPelajaranId == $tahun->id ? 'selected' : '' }}>
                                {{ $tahun->nama_hijriyah }} | {{ $tahun->nama_masehi }}
                            </option>
                        @endforeach
                    </select>
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none z-10 text-zinc-400">
                        <i class="bi bi-chevron-down text-[10px] font-black"></i>
                    </div>
                </div>

                <!-- Divider -->
                <div class="hidden sm:block w-px h-5 bg-zinc-200 dark:bg-zinc-800 shrink-0"></div>

                <!-- Filter Bulan -->
                <div class="relative w-full sm:w-[240px] h-10">
                    <div
                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400 z-10">
                        <i class="bi bi-calendar-month text-xs"></i>
                    </div>
                    <select name="bulan" onchange="this.form.submit()"
                        class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold cursor-pointer appearance-none">
                        <option value="">-- Pilih Bulan Tagihan --</option>
                        @foreach ($daftarBulan as $bln)
                            <option value="{{ $bln->id }}" {{ $bulanTerpilih == $bln->id ? 'selected' : '' }}>
                                {{ $bln->urutan }}. {{ $bln->nama_bulan }} {{ $bln->tahun_hijriyah }}
                            </option>
                        @endforeach
                    </select>
                    <div
                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400 z-10">
                        <i class="bi bi-chevron-down text-[10px] font-black"></i>
                    </div>
                </div>

            </form>
        </div>

    </div>

    <!-- PANEL UTAMA -->
    @if ($bulanTerpilih)
        @php
            $totalTagihan = count($tagihanPending);
            $totalLunas = collect($tagihanPending)->where('status_bayar', 'Lunas')->count();
            $isLunasSemua = $totalTagihan > 0 && $totalLunas === $totalTagihan;
            $idKwitansi = $isLunasSemua ? $tagihanPending->first()->pembayaran_tagihan_id : null;
        @endphp

        <form action="{{ route('pembayaran-tagihan.donatur.proses') }}" method="POST" id="formDonatur"
            class="relative z-10">
            @csrf
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 relative z-10">

                <!-- KIRI: IDENTITAS DONATUR -->
                <div class="xl:col-span-4">
                    <div
                        class="m3-glass-card px-5 py-4 shadow-2xs sticky top-24 relative overflow-hidden group {{ $isLunasSemua ? 'opacity-80 pointer-events-none' : '' }} shrink-0">

                        <!-- Overlay Lunas -->
                        @if ($isLunasSemua)
                            <div
                                class="absolute inset-0 z-50 flex flex-col items-center justify-center bg-white/80 dark:bg-black/80 backdrop-blur-[2px]">
                                <div
                                    class="w-12 h-12 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-2xl mb-2 border border-emerald-500/20 shadow-2xs">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                                <h3 class="font-black text-zinc-900 dark:text-white text-sm tracking-tight">Selesai
                                    Dibayar</h3>
                            </div>
                        @endif

                        <h3
                            class="font-black text-zinc-900 dark:text-white text-sm mb-4 tracking-tight flex items-center">
                            <div
                                class="w-7 h-7 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center mr-2.5 shrink-0 border border-purple-500/20">
                                <i class="bi bi-person-hearts text-xs"></i>
                            </div>
                            Identitas Donatur
                        </h3>

                        <div class="space-y-3.5">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Nama Donatur / Hamba Allah *
                                </label>
                                <input type="text" name="nama_pembayar" required
                                    placeholder="Contoh: Bapak H. Abdullah"
                                    class="m3-input-glass w-full text-xs font-bold">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Tanggal Terima Dana *
                                </label>
                                <input type="date" name="tanggal_bayar" required value="{{ date('Y-m-d') }}"
                                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Metode Penerimaan *
                                </label>
                                <div class="relative">
                                    <select name="metode_pembayaran" required
                                        class="m3-input-glass w-full !pr-8 text-xs font-bold cursor-pointer appearance-none">
                                        <option value="Tunai">Uang Tunai / Cash</option>
                                        <option value="Transfer Bank Jatim">Transfer - Bank Jatim</option>
                                        <option value="Transfer BSI">Transfer - BSI</option>
                                        <option value="Transfer BRI">Transfer - BRI</option>
                                        <option value="E-Wallet">E-Wallet (Dana/Ovo/Gopay)</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                                        <i class="bi bi-chevron-down text-[10px] font-black"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Alamat (Opsional)
                                </label>
                                <input type="text" name="alamat_pembayar" placeholder="Kota atau Alamat Donatur"
                                    class="m3-input-glass w-full text-xs font-bold">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Catatan / Doa Titipan (Opsional)
                                </label>
                                <textarea name="catatan" rows="3" placeholder="Tuliskan pesan/doa dari donatur"
                                    class="m3-input-glass w-full text-xs font-bold resize-none custom-scrollbar"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KANAN: LIST DAFTAR YATIM YANG DIDONATURI -->
                <div class="xl:col-span-8 flex flex-col h-[calc(100vh-140px)] min-h-[600px] gap-3.5">

                    <!-- ACTION BAR ATAS -->
                    <div
                        class="m3-glass-card px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0 shadow-2xs">

                        @if ($isLunasSemua)
                            <!-- Info Sudah Lunas Semua -->
                            <div
                                class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full p-2.5 bg-sky-500/10 rounded-xl border border-sky-500/20 gap-3 shadow-2xs">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm shrink-0 border border-sky-500/30">
                                        <i class="bi bi-patch-check-fill"></i>
                                    </div>
                                    <div>
                                        <h5
                                            class="text-xs font-black text-sky-900 dark:text-sky-300 uppercase tracking-wider mb-0.5">
                                            Semua Telah Terbayar
                                        </h5>
                                        <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400/80">
                                            Tagihan yatim bulan {{ $bulanTerpilih }} sudah Lunas.
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('pembayaran-tagihan.cetak-donatur', $idKwitansi) }}" target="_blank"
                                    class="w-full sm:w-auto px-4 py-2 bg-white dark:bg-sky-900/40 hover:bg-sky-50 dark:hover:bg-sky-800 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-700 font-black text-[10px] uppercase tracking-wider rounded-xl transition-colors flex items-center justify-center gap-1.5 shrink-0 active:scale-95 outline-none shadow-2xs">
                                    <i class="bi bi-printer-fill text-xs"></i> Cetak Kwitansi
                                </a>
                            </div>
                        @else
                            <!-- Bar Pilih Semua & Proses -->
                            <label
                                class="flex items-center gap-2.5 cursor-pointer select-none group w-full sm:w-auto justify-center sm:justify-start">
                                <input type="checkbox" id="chkMasterDonatur" onchange="centangSemuaDonatur(this)"
                                    class="sr-only peer">
                                <div
                                    class="w-5 h-5 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white/40 dark:bg-black/40 text-purple-600 peer-checked:bg-purple-600 peer-checked:border-purple-600 flex items-center justify-center transition-all group-hover:border-purple-600/50 relative shadow-2xs">
                                    <i
                                        class="bi bi-check-lg text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none font-black"></i>
                                </div>
                                <span
                                    class="text-xs font-black text-zinc-800 dark:text-zinc-200 group-hover:text-purple-600 transition-colors uppercase tracking-wider">
                                    Pilih Semua
                                </span>
                            </label>

                            <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="text-right flex flex-col justify-center mr-2">
                                    <p
                                        class="text-[9px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                                        TOTAL PENCAIRAN</p>
                                    <p class="text-xl font-black text-zinc-900 dark:text-white leading-none mt-0.5"
                                        id="teksTotalDonatur">Rp 0</p>
                                </div>
                                <button type="submit" id="btnProsesDonatur" disabled
                                    class="m3-btn-primary h-10 px-5 text-xs font-black shadow-2xs flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="bi bi-wallet2 text-xs"></i> <span>Proses</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- DAFTAR YATIM SCROLLABLE -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar space-y-2.5 pb-10 pr-1">
                        @forelse($tagihanPending as $tagihan)
                            @if ($tagihan->status_bayar === 'Lunas')
                                <!-- ITEM LUNAS -->
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 m3-glass-card border-dashed !border-emerald-500/30 rounded-2xl shadow-2xs gap-2.5 opacity-80 hover:opacity-100 transition-opacity">
                                    <div class="flex items-center gap-3 w-full sm:w-auto">
                                        <div
                                            class="w-8 h-8 flex items-center justify-center bg-emerald-500/10 rounded-xl border border-emerald-500/20 text-emerald-500 text-sm shrink-0">
                                            <i class="bi bi-check-lg font-black"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span
                                                    class="text-xs font-bold text-zinc-500 dark:text-zinc-400 line-through decoration-zinc-400/50 decoration-2 tracking-tight truncate">{{ $tagihan->murid->nama_lengkap ?? 'NN' }}</span>
                                                <span
                                                    class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-1.5 py-0.5 rounded shrink-0">{{ $tagihan->ruangan->nama_ruangan ?? '-' }}</span>
                                            </div>
                                            <p
                                                class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500 line-through decoration-zinc-400/50 truncate">
                                                {{ str_replace(' (Dibayarkan donatur jika ada)', '', $tagihan->nama_tagihan_spesifik) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-between sm:justify-end ml-11 sm:ml-0 shrink-0">
                                        <span
                                            class="text-[9px] font-black text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 uppercase tracking-wider px-2.5 py-1 rounded-xl shadow-2xs">SUDAH
                                            DIDONATURI</span>
                                    </div>
                                </div>
                            @else
                                <!-- ITEM BELUM LUNAS -->
                                <label
                                    class="relative flex flex-col sm:flex-row sm:items-center justify-between p-3.5 m3-glass-card rounded-2xl shadow-2xs cursor-pointer transition-all duration-200 gap-2.5 group hover:border-purple-500/40 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/5">

                                    <div class="flex items-center gap-3 w-full sm:w-auto flex-1">
                                        <!-- Checkbox -->
                                        <div class="relative w-5 h-5 flex items-center justify-center shrink-0">
                                            <input type="checkbox" name="tagihan_ids[]" value="{{ $tagihan->id }}"
                                                data-nominal="{{ $tagihan->nominal_tagihan }}"
                                                onchange="hitungTotalDonatur()" class="chk-donatur sr-only peer">
                                            <div
                                                class="absolute inset-0 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white/40 dark:bg-black/40 peer-checked:bg-purple-600 peer-checked:border-purple-600 transition-colors shadow-2xs group-hover:border-purple-500/50">
                                            </div>
                                            <i
                                                class="bi bi-check-lg text-white opacity-0 peer-checked:opacity-100 transition-opacity text-xs leading-none font-black pointer-events-none"></i>
                                        </div>

                                        <!-- Data Yatim -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span
                                                    class="text-xs font-black text-zinc-900 dark:text-white tracking-tight leading-tight truncate group-has-[:checked]:text-purple-600 dark:group-has-[:checked]:text-purple-400 transition-colors">{{ $tagihan->murid->nama_lengkap ?? 'NN' }}</span>
                                                <span
                                                    class="text-[9px] font-bold text-zinc-500 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-1.5 py-0.5 rounded shrink-0">{{ $tagihan->ruangan->nama_ruangan ?? '-' }}</span>
                                            </div>
                                            <p
                                                class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 truncate">
                                                {{ str_replace(' (Dibayarkan donatur jika ada)', '', $tagihan->nama_tagihan_spesifik) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="text-xs font-black text-purple-600 dark:text-purple-400 text-left sm:text-right ml-8 sm:ml-0 relative z-10 shrink-0">
                                        <span
                                            class="text-[9px] text-zinc-400 dark:text-zinc-500 mr-0.5">Rp</span>{{ number_format($tagihan->nominal_tagihan, 0, ',', '.') }}
                                    </div>
                                </label>
                            @endif
                        @empty
                            <div class="col-span-full">
                                <x-empty-state icon="bi-emoji-smile" title="Alhamdulillah!"
                                    message="Tidak ada tunggakan anak yatim di bulan {{ $bulanTerpilih }}." />
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </form>
    @else
        <!-- State Awal Belum Pilih Bulan -->
        <div class="col-span-full">
            <x-empty-state icon="bi-calendar2-heart" title="Pilih Bulan Tagihan"
                message="Silakan pilih bulan tagihan di atas untuk melihat daftar santri yatim yang belum terlunasi pada bulan tersebut." />
        </div>
    @endif

    <!-- SCRIPTS -->
    <script>
        function hitungTotalDonatur() {
            let total = 0;
            let adaYangDicentang = false;
            const checkboxes = document.querySelectorAll('.chk-donatur');

            checkboxes.forEach(chk => {
                if (chk.checked) {
                    total += parseInt(chk.getAttribute('data-nominal'));
                    adaYangDicentang = true;
                }
            });

            document.getElementById('teksTotalDonatur').innerText = 'Rp ' + total.toLocaleString('id-ID');
            const btn = document.getElementById('btnProsesDonatur');
            if (btn) btn.disabled = !adaYangDicentang;
        }

        function centangSemuaDonatur(masterCheckbox) {
            const checkboxes = document.querySelectorAll('.chk-donatur');
            checkboxes.forEach(chk => {
                chk.checked = masterCheckbox.checked;
            });
            hitungTotalDonatur();
        }
    </script>

</x-app-layout>
