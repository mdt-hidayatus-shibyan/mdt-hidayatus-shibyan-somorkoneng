@section('title', 'Pendaftaran & Buka Rekening Tabungan Baru')
<x-app-layout>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Pendaftaran Buku Tabungan
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Scan barcode nomor buku fisik dan hubungkan dengan nasabah (Murid, Ustadz, Kas Ruangan, atau Umum).
            </p>
        </div>
        <x-button :href="route('tabungan.rekening.index')" variant="secondary" size="sm" icon="bi-arrow-left">
            Kembali ke Master Rekening
        </x-button>
    </div>

    <!-- Alert Error -->
    @if (session('error'))
        <div
            class="mb-6 p-4 rounded-2xl bg-rose-500/10 border-2 border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs font-bold space-y-1 relative z-10 flex items-start gap-3 shadow-2xs">
            <div
                class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-base shrink-0 font-black">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            <div class="flex-1">
                <div class="font-black text-sm text-rose-700 dark:text-rose-300">Gagal Membuka Rekening</div>
                <p class="text-xs font-semibold text-rose-700/90 dark:text-rose-300/90 mt-0.5">{{ session('error') }}
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
                <span>Terdapat kesalahan pada formulir pendaftaran:</span>
            </div>
            <ul class="list-disc list-inside text-[11px] pl-2 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start relative z-10">
        <!-- ========================================== -->
        <!-- KOLOM KIRI: FORMULIR PENDAFTARAN REKENING -->
        <!-- ========================================== -->
        <div class="lg:col-span-7 space-y-6" x-data="{
            jenis: '{{ old('jenis_nasabah', request('jenis', 'Murid')) }}',
        
            // 1. STATE PENCARIAN MURID (NISM)
            inputNism: '{{ old('nism_input') }}',
            selectedMuridId: '{{ old('murid_id') }}',
            selectedMuridNism: '',
            selectedMuridNama: '',
            selectedMuridKelas: '',
            selectedMuridStatus: '',
            searchState: 'idle', // 'idle' | 'loading' | 'found' | 'not_found' | 'multiple'
            errorMessage: '',
            multipleList: [],
            searchTimeout: null,
        
            // 2. STATE PENCARIAN USTADZ (NIGM)
            inputNigm: '{{ old('nigm_input') }}',
            selectedUstadzId: '{{ old('ustadz_id') }}',
            selectedUstadzNigm: '',
            selectedUstadzNama: '',
            selectedUstadzKode: '',
            selectedUstadzStatus: '',
            ustadzSearchState: 'idle', // 'idle' | 'loading' | 'found' | 'not_found' | 'multiple'
            ustadzErrorMessage: '',
            ustadzMultipleList: [],
            ustadzSearchTimeout: null,
        
            init() {
                if (this.selectedMuridId) {
                    this.performSearch(this.selectedMuridId);
                }
                if (this.selectedUstadzId) {
                    this.performSearchUstadz(this.selectedUstadzId);
                }
            },
        
            // --- HANDLER MURID ---
            onNismInput() {
                clearTimeout(this.searchTimeout);
                let q = this.inputNism.trim();
                if (!q) {
                    this.selectedMuridId = '';
                    this.searchState = 'idle';
                    this.errorMessage = '';
                    this.multipleList = [];
                    return;
                }
                this.searchState = 'loading';
                this.searchTimeout = setTimeout(() => {
                    this.performSearch(q);
                }, 300);
            },
        
            async performSearch(q) {
                if (!q) return;
                this.searchState = 'loading';
                try {
                    let res = await fetch(`{{ route('tabungan.ajax.cari-murid') }}?nism=${encodeURIComponent(q)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    let data = await res.json();
                    if (data.status === 'found') {
                        this.selectMurid(data.data.id, data.data.nama, data.data.nism, data.data.kelas, data.data.status);
                    } else if (data.status === 'multiple') {
                        this.selectedMuridId = '';
                        this.searchState = 'multiple';
                        this.multipleList = data.data;
                    } else {
                        this.selectedMuridId = '';
                        this.searchState = 'not_found';
                        this.errorMessage = data.message || `Murid dengan NISM '${q}' tidak ditemukan atau belum terdaftar.`;
                    }
                } catch (e) {
                    this.searchState = 'not_found';
                    this.errorMessage = 'Terjadi gangguan koneksi saat mencari data murid.';
                }
            },
        
            selectMurid(id, nama, nism, kelas, status) {
                this.selectedMuridId = id;
                this.selectedMuridNama = nama;
                this.selectedMuridNism = nism;
                this.selectedMuridKelas = kelas;
                this.selectedMuridStatus = status;
                this.searchState = 'found';
                this.errorMessage = '';
                this.multipleList = [];
            },
        
            resetMurid() {
                this.inputNism = '';
                this.selectedMuridId = '';
                this.selectedMuridNama = '';
                this.selectedMuridNism = '';
                this.selectedMuridKelas = '';
                this.selectedMuridStatus = '';
                this.searchState = 'idle';
                this.errorMessage = '';
                this.multipleList = [];
            },
        
            // --- HANDLER USTADZ ---
            onNigmInput() {
                clearTimeout(this.ustadzSearchTimeout);
                let q = this.inputNigm.trim();
                if (!q) {
                    this.selectedUstadzId = '';
                    this.ustadzSearchState = 'idle';
                    this.ustadzErrorMessage = '';
                    this.ustadzMultipleList = [];
                    return;
                }
                this.ustadzSearchState = 'loading';
                this.ustadzSearchTimeout = setTimeout(() => {
                    this.performSearchUstadz(q);
                }, 300);
            },
        
            async performSearchUstadz(q) {
                if (!q) return;
                this.ustadzSearchState = 'loading';
                try {
                    let res = await fetch(`{{ route('tabungan.ajax.cari-ustadz') }}?nigm=${encodeURIComponent(q)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    let data = await res.json();
                    if (data.status === 'found') {
                        this.selectUstadz(data.data.id, data.data.nama, data.data.nigm, data.data.kode_ustadz, data.data.status);
                    } else if (data.status === 'multiple') {
                        this.selectedUstadzId = '';
                        this.ustadzSearchState = 'multiple';
                        this.ustadzMultipleList = data.data;
                    } else {
                        this.selectedUstadzId = '';
                        this.ustadzSearchState = 'not_found';
                        this.ustadzErrorMessage = data.message || `Ustadz dengan NIGM '${q}' tidak ditemukan atau belum terdaftar.`;
                    }
                } catch (e) {
                    this.ustadzSearchState = 'not_found';
                    this.ustadzErrorMessage = 'Terjadi gangguan koneksi saat mencari data ustadz.';
                }
            },
        
            selectUstadz(id, nama, nigm, kode, status) {
                this.selectedUstadzId = id;
                this.selectedUstadzNama = nama;
                this.selectedUstadzNigm = nigm;
                this.selectedUstadzKode = kode;
                this.selectedUstadzStatus = status;
                this.ustadzSearchState = 'found';
                this.ustadzErrorMessage = '';
                this.ustadzMultipleList = [];
            },
        
            resetUstadz() {
                this.inputNigm = '';
                this.selectedUstadzId = '';
                this.selectedUstadzNama = '';
                this.selectedUstadzNigm = '';
                this.selectedUstadzKode = '';
                this.selectedUstadzStatus = '';
                this.ustadzSearchState = 'idle';
                this.ustadzErrorMessage = '';
                this.ustadzMultipleList = [];
            }
        }">
            <form action="{{ route('tabungan.rekening.store') }}" method="POST"
                class="m3-glass-card p-6 sm:p-8 rounded-3xl space-y-6 shadow-2xs relative z-10">
                @csrf

                <!-- ========================================== -->
                <!-- 1. SCAN BARCODE BUKU TABUNGAN -->
                <!-- ========================================== -->
                <div
                    class="p-5 rounded-2xl bg-primary/5 border-2 border-primary/30 dark:border-primary-dark/30 space-y-2">
                    <div class="flex items-center justify-between">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-primary dark:text-primary-dark flex items-center gap-2">
                            <i class="bi bi-upc-scan text-base"></i>
                            <span>Nomor Rekening / Barcode Buku Tabungan:</span>
                        </label>
                        <span
                            class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark border border-primary/20">
                            Wajib Diisi / Discan
                        </span>
                    </div>

                    <div class="relative">
                        <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening') }}" required
                            autofocus placeholder="Scan barcode buku fisik atau ketik nomor unik rekening di sini..."
                            class="m3-input-glass w-full text-base sm:text-lg font-mono font-black !py-3 !pl-11 tracking-wider text-zinc-900 dark:text-white uppercase placeholder:normal-case placeholder:text-xs placeholder:font-normal">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary dark:text-primary-dark">
                            <i class="bi bi-barcode text-xl"></i>
                        </div>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                        *Gunakan barcode scanner atau masukkan nomor barcode unik yang tertera pada buku tabungan fisik.
                    </p>
                </div>

                <!-- ========================================== -->
                <!-- 2. PILIHAN KATEGORI NASABAH -->
                <!-- ========================================== -->
                <div>
                    <label
                        class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-2.5">
                        Pilih Kategori Nasabah:
                    </label>
                    <div class="grid grid-cols-4 gap-2 sm:gap-2.5">
                        @php
                            $kategoriCards = [
                                'Murid' => [
                                    'icon' => 'bi-mortarboard-fill',
                                    'label' => 'Murid',
                                    'desc' => 'Cari NISM',
                                ],
                                'Ustadz' => [
                                    'icon' => 'bi-person-badge-fill',
                                    'label' => 'Ustadz',
                                    'desc' => 'Cari NIGM',
                                ],
                                'Kas Ruangan' => [
                                    'icon' => 'bi-door-closed-fill',
                                    'label' => 'Kas Ruangan',
                                    'desc' => 'Kelas / Kamar',
                                ],
                                'Umum' => [
                                    'icon' => 'bi-people-fill',
                                    'label' => 'Umum',
                                    'desc' => 'Wali / Lainnya',
                                ],
                            ];
                        @endphp

                        @foreach ($kategoriCards as $val => $opt)
                            <label class="cursor-pointer">
                                <input type="radio" name="jenis_nasabah" value="{{ $val }}" x-model="jenis"
                                    class="peer sr-only">
                                <div
                                    class="p-2.5 sm:p-3 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary dark:peer-checked:text-primary-dark peer-checked:ring-2 peer-checked:ring-primary/20 transition-all flex flex-col items-center text-center gap-1 hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                                    <div
                                        class="w-7 h-7 rounded-xl bg-zinc-100 dark:bg-zinc-800 peer-checked:bg-primary/20 flex items-center justify-center text-sm shrink-0">
                                        <i class="bi {{ $opt['icon'] }}"></i>
                                    </div>
                                    <div class="min-w-0 w-full">
                                        <span class="text-xs font-black block truncate">{{ $opt['label'] }}</span>
                                        <span
                                            class="text-[10px] text-zinc-400 font-semibold block truncate">{{ $opt['desc'] }}</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <hr class="border-zinc-200/80 dark:border-zinc-800">

                <!-- ========================================== -->
                <!-- 3. DETAIL FORM DINAMIS PER KATEGORI -->
                <!-- ========================================== -->

                <!-- FORM KHUSUS MURID (CARI DENGAN NISM) -->
                <div x-show="jenis === 'Murid'" class="space-y-4" x-cloak>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label
                                class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Cari Murid via NISM:
                            </label>
                            <span class="text-[11px] font-semibold text-zinc-400">
                                *Ketik NISM atau Scan Kartu Pelajar
                            </span>
                        </div>

                        <!-- Hidden Input untuk Simpan ID Murid -->
                        <input type="hidden" name="murid_id" :value="selectedMuridId" :required="jenis === 'Murid'">

                        <!-- Search Box Filter Murid -->
                        <div class="relative flex items-center">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                <i class="bi bi-search"></i>
                            </div>
                            <input type="text" x-model="inputNism" @input="onNismInput()"
                                @keydown.enter.prevent="performSearch(inputNism)"
                                placeholder="Ketik NISM murid di sini (contoh: 2024001)..."
                                class="m3-input-glass w-full text-xs font-bold !pl-9 !pr-10">
                            <template x-if="inputNism">
                                <button type="button" @click="resetMurid()"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-rose-500 text-xs font-bold cursor-pointer">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </template>
                        </div>

                        <!-- 1. State: Loading -->
                        <div x-show="searchState === 'loading'"
                            class="p-3.5 rounded-2xl bg-zinc-100 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/60 flex items-center gap-2.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400 animate-pulse">
                            <div class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin">
                            </div>
                            <span>Memeriksa data NISM di database kesiswaan...</span>
                        </div>

                        <!-- 2. State: Found (Hijau / Sukses) -->
                        <div x-show="searchState === 'found' && selectedMuridId"
                            class="p-4 rounded-2xl bg-emerald-500/10 border-2 border-emerald-500/30 dark:border-emerald-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3.5">
                                <div
                                    class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-500/30 shrink-0">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black text-emerald-700 dark:text-emerald-400"
                                            x-text="selectedMuridNama"></span>
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-700 dark:text-emerald-300"
                                            x-text="selectedMuridStatus || 'Aktif'"></span>
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                        NISM: <strong class="font-mono text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedMuridNism"></strong> &bull;
                                        Kelas: <strong class="text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedMuridKelas"></strong>
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="resetMurid()"
                                class="px-3 py-1.5 rounded-xl bg-white/80 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 flex items-center gap-1.5 transition-all self-end sm:self-center cursor-pointer">
                                <i class="bi bi-x-circle text-xs"></i>
                                <span>Ganti Murid</span>
                            </button>
                        </div>

                        <!-- 3. State: Not Found (Merah / Warning) -->
                        <div x-show="searchState === 'not_found'"
                            class="p-4 rounded-2xl bg-rose-500/10 border-2 border-rose-500/30 dark:border-rose-500/40 flex items-start gap-3 shadow-2xs">
                            <div
                                class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-base border border-rose-500/30 shrink-0 mt-0.5">
                                <i class="bi bi-exclamation-octagon-fill"></i>
                            </div>
                            <div>
                                <span class="text-xs font-black text-rose-700 dark:text-rose-400 block">
                                    Murid Tidak Ditemukan / Tidak Terdaftar
                                </span>
                                <p class="text-[11px] text-rose-600/90 dark:text-rose-400/90 mt-0.5 font-medium"
                                    x-text="errorMessage">
                                </p>
                                <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1">
                                    Pastikan NISM atau nama yang diketik sudah benar dan berstatus murid aktif.
                                </p>
                            </div>
                        </div>

                        <!-- 4. State: Multiple Results (Rekomendasi) -->
                        <div x-show="searchState === 'multiple' && multipleList.length > 0"
                            class="rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 p-3 space-y-2 shadow-2xs">
                            <span class="text-[11px] font-black uppercase text-zinc-500 dark:text-zinc-400 block">
                                Pilih Murid yang Sesuai:
                            </span>
                            <div
                                class="divide-y divide-zinc-100 dark:divide-zinc-800/60 max-h-48 overflow-y-auto custom-scrollbar">
                                <template x-for="item in multipleList" :key="item.id">
                                    <div @click="selectMurid(item.id, item.nama, item.nism, item.kelas, item.status)"
                                        class="p-2.5 flex items-center justify-between hover:bg-zinc-100/70 dark:hover:bg-zinc-800/70 rounded-xl cursor-pointer transition-colors">
                                        <div>
                                            <span class="font-bold text-xs text-zinc-900 dark:text-white block"
                                                x-text="item.nama"></span>
                                            <span class="text-[10px] text-zinc-500">
                                                NISM: <strong class="font-mono text-zinc-700 dark:text-zinc-300"
                                                    x-text="item.nism"></strong> &bull;
                                                Kelas: <span x-text="item.kelas"></span>
                                            </span>
                                        </div>
                                        <span
                                            class="text-[10px] font-black px-2 py-0.5 rounded bg-primary/10 text-primary dark:text-primary-dark">
                                            Pilih
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FORM KHUSUS USTADZ (CARI DENGAN NIGM) -->
                <div x-show="jenis === 'Ustadz'" class="space-y-4" x-cloak>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label
                                class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Cari Ustadz via NIGM:
                            </label>
                            <span class="text-[11px] font-semibold text-zinc-400">
                                *Ketik NIGM atau Kode Ustadz
                            </span>
                        </div>

                        <!-- Hidden Input untuk Simpan ID Ustadz -->
                        <input type="hidden" name="ustadz_id" :value="selectedUstadzId"
                            :required="jenis === 'Ustadz'">

                        <!-- Search Box Filter Ustadz -->
                        <div class="relative flex items-center">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-xs">
                                <i class="bi bi-search"></i>
                            </div>
                            <input type="text" x-model="inputNigm" @input="onNigmInput()"
                                @keydown.enter.prevent="performSearchUstadz(inputNigm)"
                                placeholder="Ketik NIGM / Kode Ustadz di sini (contoh: UST-001)..."
                                class="m3-input-glass w-full text-xs font-bold !pl-9 !pr-10">
                            <template x-if="inputNigm">
                                <button type="button" @click="resetUstadz()"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-rose-500 text-xs font-bold cursor-pointer">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </template>
                        </div>

                        <!-- 1. State: Loading -->
                        <div x-show="ustadzSearchState === 'loading'"
                            class="p-3.5 rounded-2xl bg-zinc-100 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/60 flex items-center gap-2.5 text-xs font-semibold text-zinc-500 dark:text-zinc-400 animate-pulse">
                            <div
                                class="w-4 h-4 border-2 border-primary border-t-transparent rounded-full animate-spin">
                            </div>
                            <span>Memeriksa data NIGM di database kepegawaian...</span>
                        </div>

                        <!-- 2. State: Found (Indigo / Sukses) -->
                        <div x-show="ustadzSearchState === 'found' && selectedUstadzId"
                            class="p-4 rounded-2xl bg-indigo-500/10 border-2 border-indigo-500/30 dark:border-indigo-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3.5">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-lg border border-indigo-500/30 shrink-0">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black text-indigo-700 dark:text-indigo-400"
                                            x-text="selectedUstadzNama"></span>
                                        <span
                                            class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-indigo-500/20 text-indigo-700 dark:text-indigo-300"
                                            x-text="selectedUstadzStatus || 'Aktif'"></span>
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                        NIGM: <strong class="font-mono text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedUstadzNigm"></strong> &bull;
                                        Kode: <strong class="font-mono text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedUstadzKode"></strong>
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="resetUstadz()"
                                class="px-3 py-1.5 rounded-xl bg-white/80 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 flex items-center gap-1.5 transition-all self-end sm:self-center cursor-pointer">
                                <i class="bi bi-x-circle text-xs"></i>
                                <span>Ganti Ustadz</span>
                            </button>
                        </div>

                        <!-- 3. State: Not Found (Merah / Warning) -->
                        <div x-show="ustadzSearchState === 'not_found'"
                            class="p-4 rounded-2xl bg-rose-500/10 border-2 border-rose-500/30 dark:border-rose-500/40 flex items-start gap-3 shadow-2xs">
                            <div
                                class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-base border border-rose-500/30 shrink-0 mt-0.5">
                                <i class="bi bi-exclamation-octagon-fill"></i>
                            </div>
                            <div>
                                <span class="text-xs font-black text-rose-700 dark:text-rose-400 block">
                                    Ustadz Tidak Ditemukan / Tidak Terdaftar
                                </span>
                                <p class="text-[11px] text-rose-600/90 dark:text-rose-400/90 mt-0.5 font-medium"
                                    x-text="ustadzErrorMessage">
                                </p>
                                <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1">
                                    Pastikan NIGM, kode, atau nama yang diketik sudah benar dan berstatus ustadz aktif.
                                </p>
                            </div>
                        </div>

                        <!-- 4. State: Multiple Results (Rekomendasi) -->
                        <div x-show="ustadzSearchState === 'multiple' && ustadzMultipleList.length > 0"
                            class="rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 p-3 space-y-2 shadow-2xs">
                            <span class="text-[11px] font-black uppercase text-zinc-500 dark:text-zinc-400 block">
                                Pilih Ustadz yang Sesuai:
                            </span>
                            <div
                                class="divide-y divide-zinc-100 dark:divide-zinc-800/60 max-h-48 overflow-y-auto custom-scrollbar">
                                <template x-for="item in ustadzMultipleList" :key="item.id">
                                    <div @click="selectUstadz(item.id, item.nama, item.nigm, item.kode_ustadz, item.status)"
                                        class="p-2.5 flex items-center justify-between hover:bg-zinc-100/70 dark:hover:bg-zinc-800/70 rounded-xl cursor-pointer transition-colors">
                                        <div>
                                            <span class="font-bold text-xs text-zinc-900 dark:text-white block"
                                                x-text="item.nama"></span>
                                            <span class="text-[10px] text-zinc-500">
                                                NIGM: <strong class="font-mono text-zinc-700 dark:text-zinc-300"
                                                    x-text="item.nigm"></strong> &bull;
                                                Kode: <span x-text="item.kode_ustadz"></span>
                                            </span>
                                        </div>
                                        <span
                                            class="text-[10px] font-black px-2 py-0.5 rounded bg-primary/10 text-primary dark:text-primary-dark">
                                            Pilih
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FORM KHUSUS KAS RUANGAN (PILIH RUANGAN KELAS) -->
                <div x-show="jenis === 'Kas Ruangan'" class="space-y-4" x-cloak>
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Pilih Ruangan / Kelas:
                        </label>
                        <select name="ruangan_id" class="m3-input-glass w-full text-xs font-bold"
                            :required="jenis === 'Kas Ruangan'">
                            <option value="">-- Pilih Ruangan Kelas --</option>
                            @foreach ($ruangans as $r)
                                <option value="{{ $r->id }}"
                                    {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                    Ruangan {{ $r->nama_ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- FORM KHUSUS UMUM (NAMA, KONTAK, ALAMAT) -->
                <div x-show="jenis === 'Umum'" class="space-y-4" x-cloak>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label
                                class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                Nama Lengkap Nasabah:
                            </label>
                            <input type="text" name="nama_nasabah_umum" value="{{ old('nama_nasabah_umum') }}"
                                placeholder="Contoh: H. Ahmad Subarjo" class="m3-input-glass w-full text-xs font-bold"
                                :required="jenis === 'Umum'">
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                No. WhatsApp / Kontak Telepon:
                            </label>
                            <input type="text" name="kontak_umum" value="{{ old('kontak_umum') }}"
                                placeholder="Contoh: 08123456789" class="m3-input-glass w-full text-xs font-bold">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Alamat Domisili:
                        </label>
                        <textarea name="alamat_umum" rows="2" placeholder="Alamat lengkap domisili nasabah..."
                            class="m3-input-glass w-full text-xs">{{ old('alamat_umum') }}</textarea>
                    </div>
                </div>

                <hr class="border-zinc-200/80 dark:border-zinc-800">

                <!-- ========================================== -->
                <!-- 4. PERUNTUKAN REKENING & SETORAN AWAL -->
                <!-- ========================================== -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Nama Rekening / Peruntukan -->
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Nama Rekening / Peruntukan:
                        </label>
                        <input type="text" name="nama_rekening"
                            value="{{ old('nama_rekening', 'Tabungan Utama') }}" required
                            placeholder="Contoh: Tabungan Utama, Tabungan Qurban, Wisata..."
                            class="m3-input-glass w-full text-xs font-bold">
                        <p class="text-[10px] text-zinc-400">
                            *1 nasabah dapat memiliki lebih dari 1 rekening tabungan.
                        </p>
                    </div>

                    <!-- Periode Tabungan Berjangka -->
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Periode Tabungan Berjangka:
                        </label>
                        <select name="periode_tabungan_id" class="m3-input-glass w-full text-xs font-bold">
                            <option value="">-- Tanpa Periode (Tabungan Bebas) --</option>
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}"
                                    {{ old('periode_tabungan_id') == $p->id || (!old('periode_tabungan_id') && $p->id == $periodeAktifId) ? 'selected' : '' }}>
                                    {{ $p->nama_periode }} {{ $p->is_active ? '★ (Aktif Utama)' : "({$p->status})" }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Setoran Awal (Opsional) -->
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Setoran Awal (Rp - Opsional):
                        </label>
                        <div class="relative flex items-center">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-black text-zinc-400">
                                <span>Rp</span>
                            </div>
                            <input type="number" name="setoran_awal" value="{{ old('setoran_awal', 0) }}"
                                min="0" step="1000" placeholder="0"
                                class="m3-input-glass w-full text-xs font-mono font-bold !pl-10">
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Catatan Khusus:
                        </label>
                        <input type="text" name="catatan" value="{{ old('catatan') }}"
                            placeholder="Catatan opsional..." class="m3-input-glass w-full text-xs">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-zinc-200/80 dark:border-zinc-800">
                    <a href="{{ route('tabungan.rekening.index') }}"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-center">
                        Batal
                    </a>
                    <button type="submit"
                        class="m3-btn-primary w-full sm:w-auto px-6 py-2.5 text-xs font-black rounded-xl flex items-center justify-center gap-2">
                        <i class="bi bi-check-circle-fill text-sm"></i>
                        <span>Daftarkan & Buka Rekening</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: RIWAYAT PENDAFTARAN TERKINI   -->
        <!-- ========================================== -->
        <div class="lg:col-span-5 space-y-4">
            <div class="m3-glass-card p-5 sm:p-6 rounded-3xl space-y-4 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-xl bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center text-sm font-black border border-primary/20">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                                Riwayat Pendaftaran
                            </h3>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                10 rekening terakhir yang didaftarkan
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('tabungan.rekening.index') }}"
                        class="text-xs font-bold text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                        <span>Semua</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="space-y-2.5 max-h-[720px] overflow-y-auto custom-scrollbar pr-1">
                    @php
                        $badgeColors = [
                            'Murid' => 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-500/20',
                            'Ustadz' => 'bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border-indigo-500/20',
                            'Kas Ruangan' =>
                                'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/20',
                            'Umum' => 'bg-purple-500/10 text-purple-700 dark:text-purple-300 border-purple-500/20',
                        ];
                    @endphp

                    @forelse ($riwayatPendaftaran as $item)
                        @php
                            $colorClass =
                                $badgeColors[$item->jenis_nasabah] ?? 'bg-zinc-100 text-zinc-700 border-zinc-200';
                        @endphp
                        <div
                            class="p-3.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/60 dark:border-zinc-800/80 hover:bg-zinc-100/80 dark:hover:bg-zinc-800/70 transition-all flex items-start justify-between gap-3 shadow-2xs">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <div
                                    class="w-8 h-8 rounded-xl bg-zinc-200/60 dark:bg-zinc-700/60 text-zinc-700 dark:text-zinc-200 flex items-center justify-center text-xs font-black shrink-0 border border-zinc-300/60 dark:border-zinc-700 mt-0.5">
                                    <i class="bi bi-upc"></i>
                                </div>
                                <div class="space-y-0.5 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span
                                            class="font-mono font-black text-xs text-zinc-900 dark:text-white tracking-wider">
                                            {{ $item->nomor_rekening }}
                                        </span>
                                        <span
                                            class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase border {{ $colorClass }}">
                                            {{ $item->jenis_nasabah }}
                                        </span>
                                    </div>
                                    <div class="text-xs font-black text-zinc-900 dark:text-white truncate">
                                        {{ $item->nama_nasabah }}
                                    </div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">
                                        {{ $item->identitas_nasabah }} &bull; {{ $item->nama_rekening }}
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-zinc-400 pt-1">
                                        <span
                                            class="font-mono font-black text-emerald-600 dark:text-emerald-400 text-xs">
                                            Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                        </span>
                                        <span>&bull;</span>
                                        <span>{{ $item->created_at ? $item->created_at->diffForHumans() : '' }}</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('tabungan.rekening.detail', $item->id) }}"
                                class="p-2 rounded-xl bg-white dark:bg-zinc-700/60 hover:bg-primary/10 hover:text-primary text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 transition-colors shrink-0"
                                title="Buka Detail & Mutasi">
                                <i class="bi bi-arrow-up-right text-xs"></i>
                            </a>
                        </div>
                    @empty
                        <div class="py-12 text-center text-zinc-400 text-xs">
                            <i class="bi bi-inbox text-3xl block mb-2 opacity-50"></i>
                            Belum ada buku tabungan yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
