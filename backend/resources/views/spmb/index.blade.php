@section('title', 'SPMB Online - Pengecekan Kartu Keluarga')

<x-auth-layout maxWidth="max-w-xl">
    <div class="space-y-6">
        <!-- Header & Title -->
        <div class="text-center">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black tracking-wider uppercase bg-primary/10 text-primary dark:bg-primary-dark/15 dark:text-primary-dark border border-primary/20">
                <i class="bi bi-mortarboard-fill"></i> SPMB Online
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight mt-2">
                Penerimaan Murid Baru
            </h2>
            <p class="text-xs sm:text-sm font-semibold text-zinc-500 dark:text-zinc-400 mt-1 max-w-md mx-auto">
                Tahun Pelajaran {{ $tahunAktif->nama_hijriyah ?? '-' }} ({{ $tahunAktif->nama_masehi ?? '-' }}) • MDT
                Hidayatus Shibyan
            </p>
        </div>

        @if (session('error'))
            <div
                class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center gap-2">
                <i class="bi bi-exclamation-octagon-fill text-lg shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div
                class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-xs font-bold flex items-center gap-2">
                <i class="bi bi-info-circle-fill text-lg shrink-0"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Card Pengecekan KK -->
        <div
            class="p-6 sm:p-7 rounded-3xl bg-white/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 space-y-5 shadow-sm">
            <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <div
                    class="w-10 h-10 rounded-2xl bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark flex items-center justify-center font-black text-base border border-primary/20 shrink-0">
                    <i class="bi bi-person-vcard-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-zinc-900 dark:text-white">Langkah 1: Masukkan Nomor KK</h3>
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Sistem akan memeriksa apakah data
                        keluarga sudah tercatat</p>
                </div>
            </div>

            <form action="{{ route('spmb.check-kk') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label
                        class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-2 ml-0.5">
                        Nomor Kartu Keluarga (16 Digit) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="no_kk" id="no_kk" maxlength="16" value="{{ old('no_kk') }}"
                            placeholder="Contoh: 3526110102030001"
                            class="m3-input-glass w-full !h-14 font-mono text-base font-bold uppercase tracking-wider pl-12 {{ $errors->has('no_kk') ? '!border-rose-500' : '' }}"
                            autofocus required>
                        <i
                            class="bi bi-card-text text-xl text-zinc-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                    @error('no_kk')
                        <p class="text-xs font-bold text-rose-500 mt-1.5 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="m3-btn-primary w-full !h-12 text-sm font-black shadow-lg hover:shadow-primary/20 transition-all flex items-center justify-center gap-2">
                    <span>Periksa & Lanjutkan Pendaftaran</span>
                    <i class="bi bi-arrow-right text-base"></i>
                </button>
            </form>
        </div>

        <!-- Panduan Singkat SPMB -->
        <div
            class="p-5 rounded-2xl bg-zinc-100/70 dark:bg-zinc-900/50 border border-zinc-200/70 dark:border-zinc-800/80 space-y-3">
            <h4
                class="text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider flex items-center gap-1.5">
                <i class="bi bi-info-circle text-primary dark:text-primary-dark"></i>
                Alur Pendaftaran Murid Baru
            </h4>
            <div class="space-y-2 text-xs text-zinc-600 dark:text-zinc-400">
                <div class="flex items-start gap-2.5">
                    <span
                        class="w-5 h-5 rounded-full bg-primary/10 text-primary text-[10px] font-black flex items-center justify-center shrink-0 mt-0.5">1</span>
                    <p><strong class="text-zinc-800 dark:text-zinc-200">Cek Nomor KK:</strong> Masukkan 16 digit No KK
                        pada form di atas.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span
                        class="w-5 h-5 rounded-full bg-primary/10 text-primary text-[10px] font-black flex items-center justify-center shrink-0 mt-0.5">2</span>
                    <p><strong class="text-zinc-800 dark:text-zinc-200">Isi Data Murid:</strong> Jika KK sudah ada,
                        Anda langsung melengkapi identitas anak. Jika belum, input profil keluarga terlebih dahulu.</p>
                </div>
                <div class="flex items-start gap-2.5">
                    <span
                        class="w-5 h-5 rounded-full bg-primary/10 text-primary text-[10px] font-black flex items-center justify-center shrink-0 mt-0.5">3</span>
                    <p><strong class="text-zinc-800 dark:text-zinc-200">Dapatkan Barcode:</strong> Simpan kartu bukti
                        pendaftaran dan tunjukkan Barcode/QR Code ke Admin untuk verifikasi dan penerbitan NISM.</p>
                </div>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="pt-2 flex items-center justify-center gap-4 text-xs font-semibold text-zinc-500">
            <a href="{{ route('spmb.cek-status') }}"
                class="hover:text-primary dark:hover:text-primary-dark transition-colors flex items-center gap-1">
                <i class="bi bi-search"></i> Cek Status Pendaftaran
            </a>
            <span>•</span>
            <a href="{{ route('login') }}"
                class="hover:text-primary dark:hover:text-primary-dark transition-colors flex items-center gap-1">
                <i class="bi bi-lock-fill"></i> Login Admin
            </a>
        </div>
    </div>
</x-auth-layout>
