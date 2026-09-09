@section('title', 'Tarik Tunai Tabungan Madrasah')
<x-app-layout>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Tarik Tunai Tabungan
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Scan barcode buku fisik atau ketik 3 angka nomor rekening untuk pencatatan penarikan saldo nasabah.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <x-button :href="route('tabungan.rekening.index')" variant="secondary" size="sm" icon="bi-journal-bookmark-fill">
                Master Rekening
            </x-button>
            <a href="{{ route('tabungan.setor.index') }}"
                class="inline-flex items-center justify-center gap-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold rounded-xl md:rounded-2xl min-h-[40px] px-3.5 py-2 text-xs shadow-2xs active:scale-95 transition-all">
                <i class="bi bi-arrow-down-circle-fill text-sm"></i>
                <span>Setor Tunai</span>
            </a>
            <x-button :href="route('tabungan.rekening.create')" variant="secondary" size="sm" icon="bi-plus-circle">
                Buka Rekening
            </x-button>
        </div>
    </div>

    <!-- Alert Error -->
    @if (session('error'))
        <div
            class="mb-6 p-4 rounded-2xl bg-rose-500/10 border-2 border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs font-bold space-y-1 relative z-10 flex items-start gap-3 shadow-2xs">
            <div
                class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-base shrink-0 font-black border border-rose-500/30">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            <div class="flex-1">
                <div class="font-black text-sm text-rose-700 dark:text-rose-300">Pemberitahuan</div>
                <p class="text-xs font-semibold text-rose-700/90 dark:text-rose-300/90 mt-0.5">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    @endif

    <!-- Alert Validation Errors -->
    @if (isset($errors) && $errors->any())
        <div
            class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs font-bold space-y-1 relative z-10">
            <div class="flex items-center gap-2 font-black">
                <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                <span>Terdapat kesalahan pada input transaksi penarikan:</span>
            </div>
            <ul class="list-disc list-inside text-[11px] pl-2 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start relative z-10" x-data="tarikTunaiApp()">

        <!-- ========================================== -->
        <!-- KOLOM KIRI: FORMULIR TARIK TUNAI           -->
        <!-- ========================================== -->
        <div class="lg:col-span-5 space-y-6">
            <form action="{{ route('tabungan.tarik') }}" method="POST"
                class="m3-glass-card p-6 sm:p-7 rounded-3xl space-y-6 shadow-2xs relative z-10">
                @csrf

                <!-- Hidden Tabungan ID -->
                <input type="hidden" name="tabungan_id" :value="selectedTabunganId" required>

                <!-- ========================================== -->
                <!-- 1. IDENTIFIKASI REKENING / BARCODE         -->
                <!-- ========================================== -->
                <div
                    class="p-5 rounded-2xl bg-amber-500/5 border-2 border-amber-500/20 dark:border-amber-500/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-amber-800 dark:text-amber-400 flex items-center gap-2">
                            <i class="bi bi-upc-scan text-base"></i>
                            <span>Nomor Rekening / Barcode:</span>
                        </label>

                        <!-- Toggle Mode Input (3 Digit Cepat vs Scan Penuh) -->
                        <div
                            class="flex items-center p-0.5 rounded-xl bg-zinc-200/70 dark:bg-zinc-800 border border-zinc-300/60 dark:border-zinc-700 text-[10px] font-bold">
                            <button type="button" @click="switchMode('manual3')"
                                :class="mode === 'manual3' ?
                                    'bg-white dark:bg-zinc-900 text-amber-600 dark:text-amber-400 shadow-2xs' :
                                    'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 cursor-pointer">
                                <i class="bi bi-input-cursor-text"></i>
                                <span>3 Angka Manual</span>
                            </button>
                            <button type="button" @click="switchMode('full')"
                                :class="mode === 'full' ?
                                    'bg-white dark:bg-zinc-900 text-amber-600 dark:text-amber-400 shadow-2xs' :
                                    'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 cursor-pointer">
                                <i class="bi bi-upc"></i>
                                <span>Scan Penuh</span>
                            </button>
                        </div>
                    </div>

                    <!-- MODE A: INPUT 3 DIGIT CEPAT DENGAN PREFIX OTOMATIS -->
                    <div x-show="mode === 'manual3'" class="space-y-1.5" x-cloak>
                        <div class="flex items-center gap-2">
                            <!-- Prefix Box (Non-editable) -->
                            <div
                                class="h-12 px-3.5 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex flex-col justify-center items-center shrink-0">
                                <span class="text-[9px] font-black uppercase text-zinc-400 leading-none">Prefix
                                    Seri</span>
                                <span
                                    class="text-base font-mono font-black text-zinc-800 dark:text-zinc-200 tracking-wider"
                                    x-text="prefix"></span>
                            </div>

                            <span class="text-zinc-400 font-bold text-lg">-</span>

                            <!-- Input 3 Digit Terakhir -->
                            <div class="relative flex-1 flex items-center">
                                <input type="text" id="inputSuffix3" x-model="suffix3" @input="onSuffixInput()"
                                    @keydown.enter.prevent="onSuffixInput()" autofocus maxlength="4"
                                    placeholder="Ketik 3 angka (contoh: 001, 025, 142)..."
                                    class="m3-input-glass w-full text-base sm:text-lg font-mono font-black !py-3 !pl-4 tracking-wider text-zinc-900 dark:text-white uppercase placeholder:normal-case placeholder:text-xs placeholder:font-normal">
                                <template x-if="suffix3">
                                    <button type="button" @click="resetAccount()"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-rose-500 text-sm font-bold cursor-pointer">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                            *Cukup ketik nomor urut buku (1 s/d 500), sistem otomatis melengkapi prefix nomor seri
                            <strong class="text-amber-600 dark:text-amber-400" x-text="prefix"></strong>.
                        </p>
                    </div>

                    <!-- MODE B: SCAN BARCODE FULL / INPUT MANUAL BEBAS -->
                    <div x-show="mode === 'full'" class="space-y-1.5" x-cloak>
                        <div class="relative flex items-center">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-amber-600 dark:text-amber-400">
                                <i class="bi bi-barcode text-lg"></i>
                            </div>
                            <input type="text" id="inputBarcodeFull" x-model="barcodeFull" @input="onBarcodeInput()"
                                @keydown.enter.prevent="onBarcodeInput()"
                                placeholder="Scan barcode buku fisik atau ketik nomor rekening penuh..."
                                class="m3-input-glass w-full text-base sm:text-lg font-mono font-black !py-3 !pl-10 !pr-10 tracking-wider text-zinc-900 dark:text-white uppercase placeholder:normal-case placeholder:text-xs placeholder:font-normal">
                            <template x-if="barcodeFull">
                                <button type="button" @click="resetAccount()"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-rose-500 text-sm font-bold cursor-pointer">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </template>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                            *Gunakan scanner untuk membaca barcode fisik buku secara langsung.
                        </p>
                    </div>

                    <!-- 1. State: Loading -->
                    <div x-show="searchState === 'loading'"
                        class="p-3.5 rounded-2xl bg-zinc-100 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/60 flex items-center gap-2.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400 animate-pulse">
                        <div class="w-4 h-4 border-2 border-amber-500 border-t-transparent rounded-full animate-spin">
                        </div>
                        <span>Mencari data rekening tabungan di database...</span>
                    </div>

                    <!-- 2. State: Found (Informasi Rekening, Total Saldo & Saldo Dapat Ditarik) -->
                    <div x-show="searchState === 'found' && accountData"
                        class="p-4 rounded-2xl bg-amber-500/10 border-2 border-amber-500/30 dark:border-amber-500/40 space-y-3.5 shadow-2xs"
                        x-cloak>
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-amber-500/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg border border-amber-500/30 shrink-0">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-sm text-zinc-900 dark:text-white"
                                            x-text="accountData?.nama_nasabah"></span>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30"
                                            x-text="accountData?.jenis_nasabah"></span>
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mt-0.5"
                                        x-text="accountData?.identitas_tambahan || accountData?.identitas_nasabah"></p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <span
                                    class="text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block">
                                    Saldo Buku Saat Ini
                                </span>
                                <span class="text-lg font-mono font-black text-zinc-800 dark:text-zinc-200"
                                    x-text="accountData?.formatted_saldo"></span>
                            </div>
                        </div>

                        <!-- Card Rincian Potongan Total & Saldo Maksimal yang Dapat Ditarik -->
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div
                                class="p-2.5 rounded-xl bg-white/70 dark:bg-zinc-800/70 border border-amber-500/20 space-y-0.5">
                                <span class="text-[9px] font-extrabold uppercase text-zinc-400 block">
                                    Total Tabungan
                                </span>
                                <div class="font-mono font-bold text-zinc-800 dark:text-zinc-200 text-[11px]"
                                    x-text="accountData?.formatted_total_tabungan || 'Rp 0'"></div>
                            </div>
                            <div
                                class="p-2.5 rounded-xl bg-white/70 dark:bg-zinc-800/70 border border-amber-500/20 space-y-0.5">
                                <span class="text-[9px] font-extrabold uppercase text-zinc-400 block">
                                    Potongan (<span x-text="(accountData?.persentase_potongan || 0) + '%'"></span>)
                                </span>
                                <div class="font-mono font-bold text-rose-600 dark:text-rose-400 text-[11px]"
                                    x-text="'- ' + (accountData?.formatted_potongan || 'Rp 0')"></div>
                            </div>
                            <div
                                class="p-2.5 rounded-xl bg-amber-500/15 dark:bg-amber-500/20 border border-amber-500/30 space-y-0.5">
                                <span class="text-[9px] font-black uppercase text-amber-800 dark:text-amber-300 block">
                                    Bisa Ditarik (Maks)
                                </span>
                                <div class="text-right">
                                    <span class="font-mono font-black text-amber-700 dark:text-amber-300 text-xs"
                                        x-text="accountData?.formatted_saldo_dapat_ditarik || 'Rp 0'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-zinc-600 dark:text-zinc-400 pt-0.5">
                            <div>
                                <span class="font-semibold text-[11px]">No. Rekening:</span>
                                <span class="font-mono font-black text-zinc-900 dark:text-white ml-1"
                                    x-text="accountData?.nomor_rekening"></span>
                            </div>
                            <div>
                                <span class="font-semibold text-[11px]">Program:</span>
                                <span class="font-bold text-zinc-700 dark:text-zinc-300 ml-1"
                                    x-text="accountData?.periode_nama"></span>
                            </div>
                        </div>

                        <template x-if="accountData?.jenis_nasabah === 'Kas Ruangan'">
                            <div
                                class="p-2.5 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-800 dark:text-sky-300 text-[11px] font-semibold flex items-center gap-2">
                                <i class="bi bi-info-circle-fill text-sky-600 dark:text-sky-400 shrink-0 text-sm"></i>
                                <span>Penarikan kas ruangan diserahkan langsung ke Wali Ruangan dan otomatis
                                    mengembalikan uang fisik ke tangan Wali.</span>
                            </div>
                        </template>
                    </div>

                    <!-- 3. State: Not Found -->
                    <div x-show="searchState === 'not_found'"
                        class="p-4 rounded-2xl bg-rose-500/10 border-2 border-rose-500/20 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 text-xs font-semibold flex items-center justify-between gap-3"
                        x-cloak>
                        <div class="flex items-center gap-2.5">
                            <i class="bi bi-exclamation-triangle-fill text-base shrink-0"></i>
                            <span x-text="errorMessage"></span>
                        </div>
                        <a href="{{ route('tabungan.rekening.create') }}"
                            class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-black text-[11px] rounded-xl transition shrink-0">
                            Buka Rekening
                        </a>
                    </div>
                </div>

                <hr class="border-zinc-200/80 dark:border-zinc-800">

                <!-- ========================================== -->
                <!-- 2. NOMINAL PENARIKAN & LIVE BREAKDOWN      -->
                <!-- ========================================== -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                            <i class="bi bi-cash-stack text-sm text-amber-600"></i>
                            <span>Nominal Tarik Saldo (Rp):</span>
                        </label>
                        <template x-if="accountData && accountData.saldo_dapat_ditarik > 0">
                            <button type="button" @click="tarikSemuaSaldo()"
                                class="px-2.5 py-1 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 text-[11px] font-black border border-amber-500/30 transition cursor-pointer flex items-center gap-1">
                                <i class="bi bi-wallet2"></i>
                                <span>Tarik Maksimal (<span
                                        x-text="accountData.formatted_saldo_dapat_ditarik"></span>)</span>
                            </button>
                        </template>
                    </div>

                    <!-- Input Nominal Utama -->
                    <div class="relative flex items-center">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base font-black text-amber-600 dark:text-amber-400 font-mono">
                            <span>Rp</span>
                        </div>
                        <input type="number" id="inputNominalTarik" name="nominal" x-model="nominal"
                            min="1000" :max="accountData ? accountData.saldo_dapat_ditarik : null" step="500"
                            required
                            :placeholder="accountData ? 'Maks: ' + accountData.formatted_saldo_dapat_ditarik : '0'"
                            class="m3-input-glass w-full text-xl sm:text-2xl font-mono font-black !py-3.5 !pl-12 tracking-wide text-zinc-900 dark:text-white">
                        <template x-if="nominal">
                            <button type="button" @click="nominal = ''"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-rose-500 text-sm font-bold cursor-pointer">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </template>
                    </div>

                    <!-- Warning if nominal > saldo_dapat_ditarik -->
                    <template
                        x-if="accountData && nominal && parseFloat(nominal) > parseFloat(accountData.saldo_dapat_ditarik)">
                        <div
                            class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs font-bold flex items-center gap-2">
                            <i class="bi bi-exclamation-octagon-fill text-sm"></i>
                            <span>Nominal penarikan melebihi batas saldo yang dapat ditarik (Maksimal: <span
                                    x-text="accountData.formatted_saldo_dapat_ditarik"></span>, setelah alokasi
                                potongan
                                madrasah <span x-text="(accountData.persentase_potongan || 0) + '%'"></span> dari total
                                tabungan)!</span>
                        </div>
                    </template>

                    <!-- Live Breakdown Box Penarikan -->
                    <template
                        x-if="accountData && nominal && parseFloat(nominal) > 0 && parseFloat(nominal) <= parseFloat(accountData.saldo_dapat_ditarik)">
                        <div
                            class="p-4 rounded-2xl bg-zinc-100/80 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80 space-y-2.5">
                            <div
                                class="text-[11px] font-black uppercase text-zinc-500 tracking-wider flex items-center gap-1.5 pb-1.5 border-b border-zinc-200/60 dark:border-zinc-700/60">
                                <i class="bi bi-calculator"></i>
                                <span>Rincian Penarikan & Saldo:</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-zinc-600 dark:text-zinc-400">Total Akumulasi Tabungan:</span>
                                <span class="font-mono font-bold text-zinc-900 dark:text-white"
                                    x-text="formatRupiah(accountData.total_tabungan)"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-zinc-600 dark:text-zinc-400">Alokasi Potongan Madrasah (<span
                                        x-text="(accountData.persentase_potongan || 0) + '%'"></span>):</span>
                                <span class="font-mono font-bold text-rose-600 dark:text-rose-400"
                                    x-text="'- ' + formatRupiah(accountData.nominal_potongan)"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-zinc-600 dark:text-zinc-400">Hak Bersih Total:</span>
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400"
                                    x-text="formatRupiah(accountData.saldo_bersih_total)"></span>
                            </div>
                            <template x-if="accountData.total_tarik > 0">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-zinc-600 dark:text-zinc-400">Total yang Pernah Ditarik:</span>
                                    <span class="font-mono font-bold text-rose-600 dark:text-rose-400"
                                        x-text="'- ' + formatRupiah(accountData.total_tarik)"></span>
                                </div>
                            </template>
                            <div
                                class="flex items-center justify-between text-xs pt-1 border-t border-zinc-200/60 dark:border-zinc-700/60">
                                <span class="font-bold text-amber-800 dark:text-amber-300">Batas Maksimal Dapat
                                    Ditarik:</span>
                                <span class="font-mono font-black text-amber-700 dark:text-amber-300"
                                    x-text="formatRupiah(accountData.saldo_dapat_ditarik)"></span>
                            </div>
                            <div
                                class="pt-2 border-t border-zinc-200/80 dark:border-zinc-700 flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-black text-zinc-900 dark:text-white block">Uang Tunai
                                        Diserahkan:</span>
                                    <span class="text-[10px] text-zinc-400 block font-medium">Uang tunai fisik diterima
                                        nasabah (100% utuh)</span>
                                </div>
                                <span
                                    class="text-base sm:text-lg font-mono font-black text-emerald-600 dark:text-emerald-400"
                                    x-text="formatRupiah(nominal)"></span>
                            </div>
                            <div
                                class="pt-1.5 text-[11px] text-zinc-500 dark:text-zinc-400 flex items-center justify-between font-medium">
                                <span>Sisa Saldo Tabungan (Buku):</span>
                                <span class="font-mono font-bold text-zinc-700 dark:text-zinc-300"
                                    x-text="formatRupiah(accountData.saldo - parseFloat(nominal || 0))"></span>
                            </div>
                            <div
                                class="text-[11px] text-zinc-500 dark:text-zinc-400 flex items-center justify-between font-medium">
                                <span>Sisa yang Masih Dapat Ditarik:</span>
                                <span class="font-mono font-bold text-amber-600 dark:text-amber-400"
                                    x-text="formatRupiah(accountData.saldo_dapat_ditarik - parseFloat(nominal || 0))"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- ========================================== -->
                <!-- 3. KATEGORI, TANGGAL & CATATAN             -->
                <!-- ========================================== -->
                <div class="space-y-3.5">
                    @if (isset($kategoriPenarikans) && $kategoriPenarikans->isNotEmpty())
                        <div class="space-y-1.5">
                            <label
                                class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                                <i class="bi bi-tag-fill text-amber-600"></i>
                                <span>Kategori / Peruntukan Penarikan:</span>
                            </label>
                            <select name="kategori_penarikan_id" class="m3-input-glass w-full text-xs font-bold">
                                @foreach ($kategoriPenarikans as $kat)
                                    <option value="{{ $kat->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }} ({{ $kat->jenis_tujuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label
                                class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Tanggal Penarikan:
                            </label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                                class="m3-input-glass w-full text-xs font-bold">
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Keterangan / Catatan:
                            </label>
                            <input type="text" name="keterangan"
                                value="{{ old('keterangan', 'Penarikan Tunai') }}" placeholder="Catatan penarikan..."
                                class="m3-input-glass w-full text-xs font-medium">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-zinc-200/80 dark:border-zinc-800">
                    <button type="button" @click="resetAccount(); nominal = '';"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-center cursor-pointer">
                        Reset Form
                    </button>
                    <button type="submit"
                        :disabled="!selectedTabunganId || !nominal || nominal <= 0 || (accountData && parseFloat(nominal) >
                            parseFloat(accountData.saldo_dapat_ditarik))"
                        :class="(!selectedTabunganId || !nominal || nominal <= 0 || (accountData && parseFloat(nominal) >
                            parseFloat(accountData.saldo_dapat_ditarik))) ? 'opacity-50 cursor-not-allowed' :
                        'cursor-pointer'"
                        class="m3-btn-primary w-full sm:w-auto px-7 py-3 text-xs font-black rounded-xl flex items-center justify-center gap-2 shadow-sm">
                        <i class="bi bi-check-circle-fill text-sm"></i>
                        <span>Proses Penarikan Tunai</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: RIWAYAT PENARIKAN TERKINI     -->
        <!-- ========================================== -->
        <div class="lg:col-span-7 space-y-4">
            <div class="m3-glass-card p-5 sm:p-6 rounded-3xl space-y-4 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-black border border-amber-500/20">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                                Riwayat Tarik Terkini
                            </h3>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                10 transaksi penarikan terakhir
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('tabungan.rekening.index') }}"
                        class="text-xs font-bold text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                        <span>Master</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div id="data-grid-container" class="space-y-2.5 max-h-[720px] overflow-y-auto custom-scrollbar pr-1">
                    @php
                        $badgeColors = [
                            'Murid' => 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-500/20',
                            'Ustadz' => 'bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border-indigo-500/20',
                            'Kas Ruangan' =>
                                'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20',
                            'Umum' => 'bg-purple-500/10 text-purple-700 dark:text-purple-300 border-purple-500/20',
                        ];
                    @endphp

                    @forelse ($riwayatPenarikan as $item)
                        @php
                            $tab = $item->tabungan;
                            $colorClass = $tab
                                ? $badgeColors[$tab->jenis_nasabah] ?? 'bg-zinc-100 text-zinc-700 border-zinc-200'
                                : 'bg-zinc-100 text-zinc-700 border-zinc-200';
                        @endphp
                        <div
                            class="p-3.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/60 dark:border-zinc-800/80 hover:bg-zinc-100/80 dark:hover:bg-zinc-800/70 transition-all flex items-start justify-between gap-3 shadow-2xs">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <div
                                    class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-black shrink-0 border border-amber-500/20 mt-0.5">
                                    <i class="bi bi-arrow-up-right"></i>
                                </div>
                                <div class="space-y-0.5 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span
                                            class="font-mono font-black text-xs text-zinc-900 dark:text-white tracking-wider">
                                            {{ $tab?->nomor_rekening ?? '-' }}
                                        </span>
                                        @if ($tab)
                                            <span
                                                class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase border {{ $colorClass }}">
                                                {{ $tab->jenis_nasabah }}
                                            </span>
                                        @endif
                                        @if ($item->kategoriPenarikan)
                                            <span
                                                class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20">
                                                <i class="bi bi-tag-fill text-[8px]"></i>
                                                {{ $item->kategoriPenarikan->nama_kategori }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs font-black text-zinc-900 dark:text-white truncate">
                                        {{ $tab?->nama_nasabah ?? 'Nasabah' }}
                                    </div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">
                                        {{ $item->kode_transaksi }} &bull; Petugas:
                                        {{ $item->petugas?->name ?? 'Admin' }}
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-zinc-400 pt-1 flex-wrap">
                                        <span class="font-mono font-black text-amber-600 dark:text-amber-400 text-xs">
                                            - Rp {{ number_format($item->nominal_kotor, 0, ',', '.') }}
                                        </span>
                                        @if ($item->nominal_potongan > 0)
                                            <span class="text-[10px] text-rose-500 font-semibold">
                                                (Potongan: Rp
                                                {{ number_format($item->nominal_potongan, 0, ',', '.') }})
                                            </span>
                                            <span class="text-[10px] text-zinc-700 dark:text-zinc-300 font-bold">
                                                Bersih: Rp {{ number_format($item->nominal_bersih, 0, ',', '.') }}
                                            </span>
                                        @endif
                                        <span>&bull;</span>
                                        <span>{{ $item->created_at ? $item->created_at->diffForHumans() : $item->tanggal->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <!-- Edit Button (AJAX Action Modal via custom-script.js) -->
                                <a href="{{ route('tabungan.tarik.edit', $item->id) }}"
                                    class="action-modal p-2 rounded-xl bg-white dark:bg-zinc-700/60 hover:bg-amber-500/10 hover:text-amber-600 text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 transition-colors shrink-0 outline-none"
                                    title="Edit Penarikan">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </a>

                                <!-- Hapus Button (AJAX Delete via custom-script.js SweetAlert2) -->
                                <form action="{{ route('tabungan.tarik.destroy', $item->id) }}" method="POST"
                                    class="delete-ajax inline m-0 p-0" data-refresh-target="#data-grid-container">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-xl bg-white dark:bg-zinc-700/60 hover:bg-rose-500/10 hover:text-rose-600 text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 transition-colors shrink-0 outline-none cursor-pointer"
                                        title="Hapus Penarikan">
                                        <i class="bi bi-trash3 text-xs"></i>
                                    </button>
                                </form>

                                @if ($tab)
                                    <a href="{{ route('tabungan.rekening.detail', $tab->id) }}"
                                        class="p-2 rounded-xl bg-white dark:bg-zinc-700/60 hover:bg-primary/10 hover:text-primary text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 transition-colors shrink-0"
                                        title="Buka Mutasi Rekening">
                                        <i class="bi bi-arrow-up-right text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-zinc-400 text-xs">
                            <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                            Belum ada riwayat penarikan tunai.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            function tarikTunaiApp() {
                return {
                    mode: 'manual3',
                    prefix: '{{ $prefix }}',
                    suffix3: '{{ old('suffix_rekening') }}',
                    barcodeFull: '{{ old('nomor_rekening') }}',
                    selectedTabunganId: '{{ old('tabungan_id') }}',
                    accountData: null,
                    searchState: 'idle',
                    errorMessage: '',
                    searchTimeout: null,
                    nominal: '{{ old('nominal', '') }}',

                    init() {
                        if (this.selectedTabunganId) {
                            this.performSearch(this.selectedTabunganId);
                        } else if (this.barcodeFull) {
                            this.performSearch(this.barcodeFull);
                        } else if (this.suffix3) {
                            this.onSuffixInput();
                        }
                    },

                    switchMode(newMode) {
                        this.mode = newMode;
                        if (newMode === 'manual3') {
                            this.$nextTick(() => {
                                const el = document.getElementById('inputSuffix3');
                                if (el) el.focus();
                            });
                        } else {
                            this.$nextTick(() => {
                                const el = document.getElementById('inputBarcodeFull');
                                if (el) el.focus();
                            });
                        }
                    },

                    onSuffixInput() {
                        clearTimeout(this.searchTimeout);
                        let val = this.suffix3.trim();
                        if (!val) {
                            this.resetAccount();
                            return;
                        }
                        this.searchState = 'loading';
                        this.searchTimeout = setTimeout(() => {
                            this.performSearch(val);
                        }, 300);
                    },

                    onBarcodeInput() {
                        clearTimeout(this.searchTimeout);
                        let val = this.barcodeFull.trim();
                        if (!val) {
                            this.resetAccount();
                            return;
                        }
                        this.searchState = 'loading';
                        this.searchTimeout = setTimeout(() => {
                            this.performSearch(val);
                        }, 300);
                    },

                    async performSearch(q) {
                        if (!q) return;
                        this.searchState = 'loading';
                        try {
                            let res = await fetch(
                                `{{ route('tabungan.ajax.cari-rekening') }}?q=${encodeURIComponent(q)}`, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                });
                            let data = await res.json();
                            if (data.status === 'found') {
                                this.accountData = data.data;
                                this.selectedTabunganId = data.data.id;
                                this.searchState = 'found';
                                this.errorMessage = '';

                                this.$nextTick(() => {
                                    const nomInput = document.getElementById('inputNominalTarik');
                                    if (nomInput) nomInput.focus();
                                });
                            } else {
                                this.selectedTabunganId = '';
                                this.accountData = null;
                                this.searchState = 'not_found';
                                this.errorMessage = data.message || `Rekening dengan barcode/nomor '${q}' tidak ditemukan.`;
                            }
                        } catch (e) {
                            this.searchState = 'not_found';
                            this.errorMessage = 'Terjadi gangguan jaringan saat mencari rekening.';
                        }
                    },

                    resetAccount() {
                        this.suffix3 = '';
                        this.barcodeFull = '';
                        this.selectedTabunganId = '';
                        this.accountData = null;
                        this.searchState = 'idle';
                        this.errorMessage = '';
                        this.$nextTick(() => {
                            const el = this.mode === 'manual3' ? document.getElementById('inputSuffix3') : document
                                .getElementById('inputBarcodeFull');
                            if (el) el.focus();
                        });
                    },

                    tarikSemuaSaldo() {
                        if (this.accountData && this.accountData.saldo_dapat_ditarik > 0) {
                            this.nominal = this.accountData.saldo_dapat_ditarik;
                        }
                    },

                    formatRupiah(num) {
                        let val = parseFloat(num || 0);
                        return 'Rp ' + val.toLocaleString('id-ID');
                    }
                };
            }
        </script>
    @endpush

</x-app-layout>
