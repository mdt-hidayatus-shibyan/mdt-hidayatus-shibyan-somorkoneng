@section('title', 'Setor Tunai Tabungan Madrasah')
<x-app-layout>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Setor Tunai Tabungan
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Scan barcode buku fisik atau ketik 3 angka nomor rekening untuk pencatatan setoran cepat.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-button :href="route('tabungan.rekening.index')" variant="secondary" size="sm" icon="bi-journal-bookmark-fill">
                Master Rekening
            </x-button>
            <x-button :href="route('tabungan.rekening.create')" variant="secondary" size="sm" icon="bi-plus-circle">
                Buka Rekening
            </x-button>
        </div>
    </div>



    <!-- Alert Success -->
    @if (session('success'))
        <div
            class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border-2 border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs font-bold space-y-1 relative z-10 flex items-start gap-3 shadow-2xs">
            <div
                class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-base shrink-0 font-black border border-emerald-500/30">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="flex-1">
                <div class="font-black text-sm text-emerald-700 dark:text-emerald-300">Berhasil</div>
                <p class="text-xs font-semibold text-emerald-700/90 dark:text-emerald-300/90 mt-0.5">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

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
                <span>Terdapat kesalahan pada input transaksi:</span>
            </div>
            <ul class="list-disc list-inside text-[11px] pl-2 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start relative z-10" x-data="setorTunaiApp()">

        <!-- ========================================== -->
        <!-- KOLOM KIRI: FORMULIR SETOR TUNAI           -->
        <!-- ========================================== -->
        <div class="lg:col-span-7 space-y-6">
            <form action="{{ route('tabungan.setor') }}" method="POST"
                class="m3-glass-card p-6 sm:p-7 rounded-3xl space-y-6 shadow-2xs relative z-10">
                @csrf

                <!-- Hidden Tabungan ID -->
                <input type="hidden" name="tabungan_id" :value="selectedTabunganId" required>

                <!-- ========================================== -->
                <!-- 1. IDENTIFIKASI REKENING / BARCODE         -->
                <!-- ========================================== -->
                <div
                    class="p-5 rounded-2xl bg-emerald-500/5 border-2 border-emerald-500/20 dark:border-emerald-500/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-emerald-800 dark:text-emerald-400 flex items-center gap-2">
                            <i class="bi bi-upc-scan text-base"></i>
                            <span>Nomor Rekening / Barcode:</span>
                        </label>

                        <!-- Toggle Mode Input (3 Digit Cepat vs Scan Penuh) -->
                        <div
                            class="flex items-center p-0.5 rounded-xl bg-zinc-200/70 dark:bg-zinc-800 border border-zinc-300/60 dark:border-zinc-700 text-[10px] font-bold">
                            <button type="button" @click="switchMode('manual3')"
                                :class="mode === 'manual3' ?
                                    'bg-white dark:bg-zinc-900 text-emerald-600 dark:text-emerald-400 shadow-2xs' :
                                    'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200'"
                                class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1 cursor-pointer">
                                <i class="bi bi-input-cursor-text"></i>
                                <span>3 Angka Manual</span>
                            </button>
                            <button type="button" @click="switchMode('full')"
                                :class="mode === 'full' ?
                                    'bg-white dark:bg-zinc-900 text-emerald-600 dark:text-emerald-400 shadow-2xs' :
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
                                    Tahun</span>
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
                            *Cukup ketik nomor urut buku (1 s/d 500), sistem otomatis melengkapi prefix tahun <strong
                                class="text-emerald-600 dark:text-emerald-400" x-text="prefix"></strong>.
                        </p>
                    </div>

                    <!-- MODE B: SCAN BARCODE FULL / INPUT MANUAL BEBAS -->
                    <div x-show="mode === 'full'" class="space-y-1.5" x-cloak>
                        <div class="relative flex items-center">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600 dark:text-emerald-400">
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
                        <div class="w-4 h-4 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin">
                        </div>
                        <span>Mencari data rekening tabungan di database...</span>
                    </div>

                    <!-- 2. State: Found (Informasi Rekening & Saldo) -->
                    <div x-show="searchState === 'found' && accountData"
                        class="p-4 rounded-2xl bg-emerald-500/10 border-2 border-emerald-500/30 dark:border-emerald-500/40 space-y-3 shadow-2xs"
                        x-cloak>
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-emerald-500/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg border border-emerald-500/30 shrink-0">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-sm text-zinc-900 dark:text-white"
                                            x-text="accountData?.nama_nasabah"></span>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30"
                                            x-text="accountData?.jenis_nasabah"></span>
                                    </div>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mt-0.5"
                                        x-text="accountData?.identitas_tambahan || accountData?.identitas_nasabah"></p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <span
                                    class="text-[10px] font-black uppercase tracking-wider text-zinc-400 dark:text-zinc-500 block">
                                    Saldo Saat Ini
                                </span>
                                <span class="text-lg font-mono font-black text-emerald-600 dark:text-emerald-400"
                                    x-text="accountData?.formatted_saldo"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-zinc-600 dark:text-zinc-400 pt-1">
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
                <!-- 2. NOMINAL SETORAN & QUICK BUTTONS         -->
                <!-- ========================================== -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                            <i class="bi bi-cash-stack text-sm text-emerald-600"></i>
                            <span>Nominal Setor Tunai (Rp):</span>
                        </label>
                        <span class="text-[10px] font-bold text-zinc-400">
                            *Minimal Rp 1.000
                        </span>
                    </div>

                    <!-- Input Nominal Utama -->
                    <div class="relative flex items-center">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base font-black text-emerald-600 dark:text-emerald-400 font-mono">
                            <span>Rp</span>
                        </div>
                        <input type="number" id="inputNominalSetor" name="nominal" x-model="nominal"
                            min="1000" step="500" required placeholder="0"
                            class="m3-input-glass w-full text-xl sm:text-2xl font-mono font-black !py-3.5 !pl-12 tracking-wide text-zinc-900 dark:text-white">
                        <template x-if="nominal">
                            <button type="button" @click="nominal = ''"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-rose-500 text-sm font-bold cursor-pointer">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </template>
                    </div>

                    <!-- Quick Nominal Buttons -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 block">Pilihan
                            Nominal Cepat:</span>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                            @php
                                $quickNominals = [5000, 10000, 20000, 50000, 100000, 200000];
                            @endphp
                            @foreach ($quickNominals as $qNom)
                                <button type="button" @click="setNominal({{ $qNom }})"
                                    class="px-2.5 py-2 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-800/40 hover:bg-emerald-50 hover:border-emerald-300 dark:hover:bg-emerald-950/30 text-xs font-mono font-bold text-zinc-800 dark:text-zinc-200 transition-all text-center cursor-pointer">
                                    {{ number_format($qNom / 1000, 0) }}k
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- 3. TANGGAL & CATATAN TRANSAKSI             -->
                <!-- ========================================== -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Tanggal Setoran:
                        </label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                            class="m3-input-glass w-full text-xs font-bold">
                    </div>
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                            Keterangan / Catatan:
                        </label>
                        <input type="text" name="keterangan" value="{{ old('keterangan', 'Setoran Tunai') }}"
                            placeholder="Catatan setoran..." class="m3-input-glass w-full text-xs font-medium">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div
                    class="pt-4 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-zinc-200/80 dark:border-zinc-800">
                    <button type="button" @click="resetAccount(); nominal = '';"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-bold text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-center cursor-pointer">
                        Reset Form
                    </button>
                    <button type="submit" :disabled="!selectedTabunganId || !nominal || nominal <= 0"
                        :class="(!selectedTabunganId || !nominal || nominal <= 0) ? 'opacity-50 cursor-not-allowed' :
                        'cursor-pointer'"
                        class="m3-btn-primary w-full sm:w-auto px-7 py-3 text-xs font-black rounded-xl flex items-center justify-center gap-2 shadow-sm">
                        <i class="bi bi-check-circle-fill text-sm"></i>
                        <span>Simpan Setoran Tunai</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: RIWAYAT SETORAN TERKINI       -->
        <!-- ========================================== -->
        <div class="lg:col-span-5 space-y-4">
            <div class="m3-glass-card p-5 sm:p-6 rounded-3xl space-y-4 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-black border border-emerald-500/20">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                                Riwayat Setor Terkini
                            </h3>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                10 transaksi setoran terakhir
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

                    @forelse ($riwayatSetoran as $item)
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
                                    class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-black shrink-0 border border-emerald-500/20 mt-0.5">
                                    <i class="bi bi-arrow-down-left"></i>
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
                                    </div>
                                    <div class="text-xs font-black text-zinc-900 dark:text-white truncate">
                                        {{ $tab?->nama_nasabah ?? 'Nasabah' }}
                                    </div>
                                    <div class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">
                                        {{ $item->kode_transaksi }} &bull; Petugas:
                                        {{ $item->petugas?->name ?? 'Admin' }}
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] text-zinc-400 pt-1">
                                        <span
                                            class="font-mono font-black text-emerald-600 dark:text-emerald-400 text-xs">
                                            + Rp {{ number_format($item->nominal_bersih, 0, ',', '.') }}
                                        </span>
                                        <span>&bull;</span>
                                        <span>{{ $item->created_at ? $item->created_at->diffForHumans() : $item->tanggal->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <!-- Edit Button (AJAX Action Modal via custom-script.js) -->
                                <a href="{{ route('tabungan.setor.edit', $item->id) }}"
                                    class="action-modal p-2 rounded-xl bg-white dark:bg-zinc-700/60 hover:bg-amber-500/10 hover:text-amber-600 text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 transition-colors shrink-0 outline-none"
                                    title="Edit Setoran">
                                    <i class="bi bi-pencil-square text-xs"></i>
                                </a>

                                <!-- Hapus Button (AJAX Delete via custom-script.js SweetAlert2) -->
                                <form action="{{ route('tabungan.setor.destroy', $item->id) }}" method="POST"
                                    class="delete-ajax inline m-0 p-0" data-refresh-target="#data-grid-container">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-xl bg-white dark:bg-zinc-700/60 hover:bg-rose-500/10 hover:text-rose-600 text-zinc-600 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 transition-colors shrink-0 outline-none cursor-pointer"
                                        title="Hapus Setoran">
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
                            Belum ada riwayat setoran tunai.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            function setorTunaiApp() {
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
                                    const nomInput = document.getElementById('inputNominalSetor');
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

                    setNominal(val) {
                        this.nominal = val;
                    },

                    addNominal(val) {
                        let cur = parseInt(this.nominal || 0);
                        this.nominal = cur + val;
                    }
                };
            }
        </script>
    @endpush

</x-app-layout>
