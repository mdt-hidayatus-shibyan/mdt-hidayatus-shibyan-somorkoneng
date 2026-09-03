@section('title', 'Pendaftaran Calon Murid Baru - SPMB')

<x-auth-layout maxWidth="max-w-4xl">
    <div class="space-y-6">
        <!-- Header & Title -->
        <div class="text-center">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black tracking-wider uppercase bg-primary/10 text-primary dark:bg-primary-dark/15 dark:text-primary-dark border border-primary/20">
                <i class="bi bi-mortarboard-fill"></i> SPMB Online
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight mt-2">
                Pendaftaran Calon Murid
            </h2>
            <p class="text-xs sm:text-sm font-semibold text-zinc-500 dark:text-zinc-400 mt-1 max-w-lg mx-auto">
                Tahun Pelajaran {{ $tahunAktif->nama_hijriyah ?? '-' }} ({{ $tahunAktif->nama_masehi ?? '-' }}) • MDT
                Hidayatus Shibyan
            </p>
        </div>

        @if (session('success'))
            <div
                class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold flex items-center gap-2">
                <i class="bi bi-check-circle-fill text-lg shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div
                class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-700 dark:text-blue-400 text-xs font-bold flex items-center gap-2">
                <i class="bi bi-info-circle-fill text-lg shrink-0"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold space-y-1">
                <div class="flex items-center gap-2 font-black">
                    <i class="bi bi-exclamation-octagon-fill text-base"></i>
                    <span>Terdapat beberapa isian yang perlu diperiksa:</span>
                </div>
                <ul class="list-disc list-inside pl-4 font-semibold text-[11px]">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Banner Info Terikat Wali Murid -->
        <div
            class="p-4 sm:p-5 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 dark:bg-emerald-500/15 dark:border-emerald-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div
                    class="w-11 h-11 rounded-2xl bg-emerald-500 text-white flex items-center justify-center font-black text-lg shadow-sm shrink-0">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <span
                        class="text-[10px] font-black tracking-wider uppercase text-emerald-700 dark:text-emerald-400 block">Keluarga
                        / Wali Terverifikasi:</span>
                    <h4 class="text-sm font-black text-zinc-900 dark:text-white uppercase">
                        {{ $wali->nama_kepala_keluarga }}
                    </h4>
                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mt-0.5">
                        No. KK: <strong class="font-mono">{{ $wali->no_kk }}</strong> • Dusun:
                        {{ $wali->kampung->nama_kampung ?? '-' }} • HP: {{ $wali->no_hp ?? '-' }}
                    </p>
                </div>
            </div>

            <a href="{{ route('spmb.index') }}"
                class="text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline flex items-center gap-1 shrink-0 self-end sm:self-center">
                <i class="bi bi-arrow-repeat"></i> Ganti No. KK
            </a>
        </div>

        <!-- Form Pendaftaran Murid -->
        <form action="{{ route('spmb.store-santri') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="wali_murid_id" value="{{ $wali->id }}">
            <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahunAktif->id ?? '' }}">

            <!-- ================= STEP 1: IDENTITAS MURID ================= -->
            <div
                class="p-5 sm:p-7 rounded-3xl bg-white/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 space-y-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center font-black text-sm border border-blue-500/20 shrink-0">
                            1
                        </div>
                        <div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white">Identitas Calon Murid Baru
                            </h3>
                            <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Lengkapi data diri anak
                                didik yang didaftarkan</p>
                        </div>
                    </div>
                </div>

                <!-- Nama Lengkap & Panggilan -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-2/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Nama Lengkap Calon Murid <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}"
                            placeholder="Nama Lengkap Sesuai Akta / KK"
                            class="m3-input-glass uppercase w-full !h-12 text-xs font-bold {{ $errors->has('nama_lengkap') ? '!border-rose-500' : '' }}"
                            required oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Nama Panggilan
                        </label>
                        <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}"
                            placeholder="Panggilan akrab" class="m3-input-glass w-full !h-12 text-xs font-bold">
                    </div>
                </div>

                <!-- Gender, NIK, NISN -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_kelamin" id="jenis_kelamin"
                            class="m3-input-glass w-full !h-12 text-xs font-bold" required>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                    </div>

                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            NIK Anak (16 Digit)
                        </label>
                        <input type="text" name="nik" maxlength="16" value="{{ old('nik') }}"
                            placeholder="Nomor Induk Kependudukan"
                            class="m3-input-glass font-mono w-full !h-12 text-xs font-bold">
                    </div>

                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            NISN (Opsional)
                        </label>
                        <input type="text" name="nisn" maxlength="20" value="{{ old('nisn') }}"
                            placeholder="Nomor Induk Siswa Nasional"
                            class="m3-input-glass font-mono w-full !h-12 text-xs font-bold">
                    </div>
                </div>

                <!-- Tempat, Tanggal Lahir, Anak Ke, Hub Kel -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Tempat Lahir
                        </label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                            placeholder="Kota/Kabupaten Lahir"
                            class="m3-input-glass uppercase w-full !h-12 text-xs font-bold"
                            oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Tanggal Lahir
                        </label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                            class="m3-input-glass w-full !h-12 text-xs font-bold cursor-pointer">
                    </div>

                    <div class="w-full sm:w-1/6">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Anak Ke-
                        </label>
                        <input type="number" name="anak_ke" min="1" value="{{ old('anak_ke', 1) }}"
                            class="m3-input-glass text-center w-full !h-12 text-xs font-bold">
                    </div>

                    <div class="w-full sm:w-1/6">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Hubungan
                        </label>
                        <select name="hub_kel" class="m3-input-glass w-full !h-12 text-xs font-bold">
                            <option value="Anak Kandung" {{ old('hub_kel') == 'Anak Kandung' ? 'selected' : '' }}>
                                Kandung</option>
                            <option value="Anak Tiri" {{ old('hub_kel') == 'Anak Tiri' ? 'selected' : '' }}>Tiri
                            </option>
                            <option value="Anak Angkat" {{ old('hub_kel') == 'Anak Angkat' ? 'selected' : '' }}>Angkat
                            </option>
                            <option value="Cucu" {{ old('hub_kel') == 'Cucu' ? 'selected' : '' }}>Cucu</option>
                            <option value="Lainnya" {{ old('hub_kel') == 'Lainnya' ? 'selected' : '' }}>Lainnya
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Data Orang Tua Biologis -->
                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-4">
                    <h4 class="text-xs font-black text-zinc-800 dark:text-zinc-200 uppercase tracking-wider">
                        Data Orang Tua Kandung
                    </h4>

                    <!-- Ayah -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Nama Ayah Kandung
                            </label>
                            <input type="text" name="nama_ayah" id="nama_ayah"
                                value="{{ old('nama_ayah', $wali->kepala_keluarga === 'Ayah' ? $wali->nama_kepala_keluarga : '') }}"
                                placeholder="Nama Lengkap Ayah"
                                class="m3-input-glass uppercase w-full !h-12 text-xs font-bold"
                                oninput="this.value = this.value.toUpperCase()">
                        </div>

                        <div class="w-full sm:w-1/4">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                NIK Ayah
                            </label>
                            <input type="text" name="nik_ayah" maxlength="16" value="{{ old('nik_ayah') }}"
                                placeholder="Opsional"
                                class="m3-input-glass font-mono w-full !h-12 text-xs font-bold">
                        </div>

                        <div class="w-full sm:w-1/4">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Status Ayah
                            </label>
                            <select name="status_ayah" class="m3-input-glass w-full !h-12 text-xs font-bold">
                                <option value="Hidup" {{ old('status_ayah') == 'Hidup' ? 'selected' : '' }}>Hidup
                                </option>
                                <option value="Meninggal" {{ old('status_ayah') == 'Meninggal' ? 'selected' : '' }}>
                                    Meninggal</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ibu -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Nama Ibu Kandung
                            </label>
                            <input type="text" name="nama_ibu" id="nama_ibu"
                                value="{{ old('nama_ibu', $wali->kepala_keluarga === 'Ibu' ? $wali->nama_kepala_keluarga : '') }}"
                                placeholder="Nama Lengkap Ibu"
                                class="m3-input-glass uppercase w-full !h-12 text-xs font-bold"
                                oninput="this.value = this.value.toUpperCase()">
                        </div>

                        <div class="w-full sm:w-1/4">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                NIK Ibu
                            </label>
                            <input type="text" name="nik_ibu" maxlength="16" value="{{ old('nik_ibu') }}"
                                placeholder="Opsional"
                                class="m3-input-glass font-mono w-full !h-12 text-xs font-bold">
                        </div>

                        <div class="w-full sm:w-1/4">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Status Ibu
                            </label>
                            <select name="status_ibu" class="m3-input-glass w-full !h-12 text-xs font-bold">
                                <option value="Hidup" {{ old('status_ibu') == 'Hidup' ? 'selected' : '' }}>Hidup
                                </option>
                                <option value="Meninggal" {{ old('status_ibu') == 'Meninggal' ? 'selected' : '' }}>
                                    Meninggal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Upload Foto -->
                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <label
                        class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                        Foto Murid (Opsional, Maks 2MB)
                    </label>
                    <input type="file" name="foto" accept="image/png, image/jpeg, image/jpg"
                        class="block w-full text-xs text-zinc-500 dark:text-zinc-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-primary/10 file:text-primary dark:file:bg-primary-dark/20 dark:file:text-primary-dark hover:file:bg-primary/20 cursor-pointer">
                </div>
            </div>

            <!-- ================= STEP 2: JENJANG & TAGIHAN ================= -->
            <div
                class="p-5 sm:p-7 rounded-3xl bg-white/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 space-y-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center font-black text-sm border border-emerald-500/20 shrink-0">
                            2
                        </div>
                        <div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white">Pilihan Jenjang & Informasi
                                Pembayaran</h3>
                            <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Pilih kelas masuk dan
                                rincian biaya pendaftaran madrasah</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-1/2">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Pilihan Jenjang / Kelas Masuk <span class="text-rose-500">*</span>
                        </label>
                        <select name="level_id" id="level_id"
                            class="m3-input-glass w-full !h-12 text-xs font-bold {{ $errors->has('level_id') ? '!border-rose-500' : '' }}"
                            required>
                            <option value="" disabled selected>-- Pilih Kelas / Jenjang --</option>
                            @foreach ($levels as $lvl)
                                <option value="{{ $lvl->id }}"
                                    {{ old('level_id') == $lvl->id ? 'selected' : '' }}>
                                    {{ $lvl->tingkat->nama_tingkat ?? $lvl->tingkat->kode_tingkat }} -
                                    {{ $lvl->nama_level }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-1/2">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Tahun Pelajaran Masuk
                        </label>
                        <div
                            class="m3-input-glass w-full !h-12 text-xs font-bold bg-zinc-100 dark:bg-zinc-800/80 flex items-center justify-between">
                            <span>{{ $tahunAktif->nama_hijriyah ?? '-' }}
                                ({{ $tahunAktif->nama_masehi ?? '-' }})</span>
                            <span
                                class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase">Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Rincian Biaya SPMB -->
                <div
                    class="p-4 rounded-2xl bg-zinc-50/80 dark:bg-zinc-950/60 border border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="bi bi-wallet2 text-primary dark:text-primary-dark"></i>
                            Rincian Biaya SPMB & Tagihan Terkait
                        </span>
                        <span class="text-xs font-bold text-zinc-400">TP {{ $tahunAktif->nama_hijriyah ?? '' }}</span>
                    </div>

                    @if ($biayaSpmb->isNotEmpty())
                        <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60 text-xs">
                            @foreach ($biayaSpmb as $tagihan)
                                <div class="py-2.5 flex items-center justify-between">
                                    <div>
                                        <span
                                            class="font-bold text-zinc-800 dark:text-zinc-200">{{ $tagihan->nama_tagihan }}</span>
                                        <span
                                            class="text-[10px] text-zinc-400 ml-1">({{ ucfirst($tagihan->tipe) }})</span>
                                    </div>
                                    <span class="font-mono font-black text-zinc-900 dark:text-white">
                                        Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-zinc-400 italic py-2">
                            Pengaturan tagihan SPMB belum diset. Rincian biaya akan diinformasikan saat verifikasi oleh
                            Panitia/Admin Madrasah.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2 space-y-4 text-center">
                <button type="submit"
                    class="m3-btn-primary w-full !h-14 text-sm font-black shadow-lg hover:shadow-primary/20 transition-all flex items-center justify-center gap-2">
                    <i class="bi bi-qr-code-scan text-lg"></i>
                    <span>Daftarkan Murid & Dapatkan Barcode Verifikasi</span>
                </button>

                <div class="flex items-center justify-center gap-4 text-xs font-semibold text-zinc-500">
                    <a href="{{ route('spmb.cek-status') }}"
                        class="hover:text-primary dark:hover:text-primary-dark transition-colors flex items-center gap-1">
                        <i class="bi bi-search"></i> Cek Status Pendaftaran
                    </a>
                    <span>•</span>
                    <a href="{{ route('login') }}"
                        class="hover:text-primary dark:hover:text-primary-dark transition-colors flex items-center gap-1">
                        <i class="bi bi-lock-fill"></i> Login Madrasah
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-auth-layout>
