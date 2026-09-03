@section('title', 'Penerimaan Murid Baru (SPMB)')

<x-auth-layout maxWidth="max-w-4xl">
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
            <p class="text-xs sm:text-sm font-semibold text-zinc-500 dark:text-zinc-400 mt-1 max-w-lg mx-auto">
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

        <!-- Form SPMB -->
        <form action="{{ route('spmb.store') }}" method="POST" enctype="multipart/form-data" id="formSpmb"
            class="space-y-6">
            @csrf
            <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahunAktif->id ?? '' }}">

            <!-- ================= STEP 1: VALIDASI & PENCARIAN NO KK ================= -->
            <div
                class="p-5 sm:p-7 rounded-3xl bg-white/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 space-y-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark flex items-center justify-center font-black text-sm border border-primary/20 shrink-0">
                            1
                        </div>
                        <div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white">Pemeriksaan Nomor Kartu
                                Keluarga (KK)</h3>
                            <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Cek data keluarga atau
                                daftarkan jika belum tercatat di madrasah</p>
                        </div>
                    </div>
                </div>

                <!-- Input No KK & Tombol Cek -->
                <div>
                    <label
                        class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                        Nomor Kartu Keluarga (16 Digit) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex flex-col sm:flex-row items-stretch gap-3">
                        <div class="flex-1 relative">
                            <input type="text" name="no_kk" id="no_kk" maxlength="16"
                                value="{{ old('no_kk') }}" placeholder="Masukkan 16 digit Nomor KK..."
                                class="m3-input-glass w-full !h-12 font-mono text-sm font-bold uppercase tracking-wider {{ $errors->has('no_kk') ? '!border-rose-500' : '' }}"
                                required>
                            <input type="hidden" name="wali_murid_id" id="wali_murid_id"
                                value="{{ old('wali_murid_id') }}">
                        </div>
                        <button type="button" onclick="cariNomorKK()" id="btnCariKK"
                            class="m3-btn-primary h-12 px-6 text-xs font-bold gap-2 shrink-0 justify-center">
                            <i class="bi bi-search text-sm"></i>
                            <span>Cek No KK</span>
                        </button>
                    </div>
                    @error('no_kk')
                        <p class="text-xs font-bold text-rose-500 mt-1.5 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info KK Sudah Terdaftar (Success State) -->
                <div id="boxKkTerdaftar"
                    class="{{ old('wali_murid_id') ? '' : 'hidden' }} p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 transition-all">
                    <div class="flex items-start gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/30">
                            <i class="bi bi-check-circle-fill text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">
                                    KK Sudah Terdaftar
                                </span>
                            </div>
                            <h4 id="teksNamaKepala" class="text-sm font-black text-zinc-900 dark:text-white mt-1">
                                {{ old('nama_kepala_keluarga_terdaftar', 'Keluarga Bpk/Ibu') }}
                            </h4>
                            <p id="teksAlamatKk" class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                                Zonasi: {{ old('nama_kampung_terdaftar', '-') }}
                            </p>
                        </div>
                        <button type="button" onclick="resetKK()"
                            class="text-zinc-400 hover:text-rose-500 text-sm font-bold p-1 transition-colors"
                            title="Ganti / Ubah No KK">
                            <i class="bi bi-x-circle text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Form Input KK Baru (Muncul jika KK belum ada) -->
                <div id="boxFormKkBaru"
                    class="{{ old('no_kk') && !old('wali_murid_id') ? '' : 'hidden' }} space-y-4 pt-4 border-t border-dashed border-zinc-200 dark:border-zinc-800">
                    <div
                        class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-xs font-bold">
                        <i class="bi bi-info-circle-fill shrink-0 text-sm"></i>
                        <span>Nomor KK belum terdaftar di sistem. Silakan lengkapi data profil keluarga baru di bawah
                            ini:</span>
                    </div>

                    <!-- Status Kepala & Nama Kepala -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/3">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Status Kepala Keluarga <span class="text-rose-500">*</span>
                            </label>
                            <select name="kepala_keluarga" id="kepala_keluarga"
                                class="m3-input-glass w-full !h-11 text-xs font-bold">
                                <option value="Ayah" {{ old('kepala_keluarga') == 'Ayah' ? 'selected' : '' }}>Ayah
                                </option>
                                <option value="Ibu" {{ old('kepala_keluarga') == 'Ibu' ? 'selected' : '' }}>Ibu
                                </option>
                                <option value="Wali" {{ old('kepala_keluarga') == 'Wali' ? 'selected' : '' }}>Wali /
                                    Lainnya</option>
                            </select>
                        </div>

                        <div class="w-full sm:w-2/3">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Nama Kepala Keluarga <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_kepala_keluarga" id="nama_kepala_keluarga"
                                value="{{ old('nama_kepala_keluarga') }}" placeholder="Nama sesuai di Kartu Keluarga"
                                class="m3-input-glass w-full !h-11 text-xs font-bold uppercase"
                                oninput="this.value = this.value.toUpperCase(); syncNamaOrtuDariKK();">
                        </div>
                    </div>

                    <!-- No HP & Dusun -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Nomor HP / WhatsApp Aktif <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}"
                                placeholder="Contoh: 081234567890"
                                class="m3-input-glass font-mono w-full !h-11 text-xs font-bold">
                        </div>

                        <div class="w-full sm:w-1/2">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Zonasi / Dusun Kampung <span class="text-rose-500">*</span>
                            </label>
                            <select name="kampung_id" id="kampung_id"
                                class="m3-input-glass w-full !h-11 text-xs font-bold">
                                <option value="" disabled selected>-- Pilih Dusun/Kampung --</option>
                                @foreach ($kampungs as $kpg)
                                    <option value="{{ $kpg->id }}"
                                        {{ old('kampung_id') == $kpg->id ? 'selected' : '' }}>
                                        {{ $kpg->nama_kampung }} ({{ $kpg->kode ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Alamat Lengkap / Keterangan Rumah
                        </label>
                        <textarea name="alamat_detail" id="alamat_detail" rows="2"
                            placeholder="Contoh: RT 02 RW 01, Dekat Musholla..." class="m3-input-glass w-full text-xs font-semibold !p-3">{{ old('alamat_detail') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ================= STEP 2: DATA CALON Murid ================= -->
            <div
                class="p-5 sm:p-7 rounded-3xl bg-white/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 space-y-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center font-black text-sm border border-blue-500/20 shrink-0">
                            2
                        </div>
                        <div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white">Identitas Calon Murid Baru
                            </h3>
                            <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Lengkapi data pribadi
                                anak didik yang didaftarkan</p>
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
                        <input type="text" name="nama_lengkap" id="nama_lengkap"
                            value="{{ old('nama_lengkap') }}" placeholder="Nama Lengkap Sesuai Akta / KK"
                            class="m3-input-glass uppercase w-full !h-11 text-xs font-bold {{ $errors->has('nama_lengkap') ? '!border-rose-500' : '' }}"
                            required oninput="this.value = this.value.toUpperCase()">
                        @error('nama_lengkap')
                            <p class="text-xs font-bold text-rose-500 mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Nama Panggilan
                        </label>
                        <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}"
                            placeholder="Panggilan akrab" class="m3-input-glass w-full !h-11 text-xs font-bold">
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
                            class="m3-input-glass w-full !h-11 text-xs font-bold" required>
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
                            class="m3-input-glass font-mono w-full !h-11 text-xs font-bold">
                    </div>

                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            NISN (Opsional)
                        </label>
                        <input type="text" name="nisn" maxlength="20" value="{{ old('nisn') }}"
                            placeholder="Nomor Induk Siswa Nasional"
                            class="m3-input-glass font-mono w-full !h-11 text-xs font-bold">
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
                            class="m3-input-glass uppercase w-full !h-11 text-xs font-bold"
                            oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Tanggal Lahir
                        </label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                            class="m3-input-glass w-full !h-11 text-xs font-bold cursor-pointer">
                    </div>

                    <div class="w-full sm:w-1/6">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Anak Ke-
                        </label>
                        <input type="number" name="anak_ke" min="1" value="{{ old('anak_ke', 1) }}"
                            class="m3-input-glass text-center w-full !h-11 text-xs font-bold">
                    </div>

                    <div class="w-full sm:w-1/6">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Hubungan
                        </label>
                        <select name="hub_kel" class="m3-input-glass w-full !h-11 text-xs font-bold">
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
                            <input type="text" name="nama_ayah" id="nama_ayah" value="{{ old('nama_ayah') }}"
                                placeholder="Nama Lengkap Ayah"
                                class="m3-input-glass uppercase w-full !h-11 text-xs font-bold"
                                oninput="this.value = this.value.toUpperCase()">
                        </div>

                        <div class="w-full sm:w-1/4">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                NIK Ayah
                            </label>
                            <input type="text" name="nik_ayah" maxlength="16" value="{{ old('nik_ayah') }}"
                                placeholder="Opsional"
                                class="m3-input-glass font-mono w-full !h-11 text-xs font-bold">
                        </div>

                        <div class="w-full sm:w-1/4">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Status Ayah
                            </label>
                            <select name="status_ayah" class="m3-input-glass w-full !h-11 text-xs font-bold">
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
                            <input type="text" name="nama_ibu" id="nama_ibu" value="{{ old('nama_ibu') }}"
                                placeholder="Nama Lengkap Ibu"
                                class="m3-input-glass uppercase w-full !h-11 text-xs font-bold"
                                oninput="this.value = this.value.toUpperCase()">
                        </div>

                        <div class="w-full sm:w-1/4">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                NIK Ibu
                            </label>
                            <input type="text" name="nik_ibu" maxlength="16" value="{{ old('nik_ibu') }}"
                                placeholder="Opsional"
                                class="m3-input-glass font-mono w-full !h-11 text-xs font-bold">
                        </div>

                        <div class="w-full sm:w-1/4">
                            <label
                                class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                                Status Ibu
                            </label>
                            <select name="status_ibu" class="m3-input-glass w-full !h-11 text-xs font-bold">
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

            <!-- ================= STEP 3: PILIHAN JENJANG & INFORMASI BIAYA ================= -->
            <div
                class="p-5 sm:p-7 rounded-3xl bg-white/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 space-y-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center font-black text-sm border border-emerald-500/20 shrink-0">
                            3
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
                            class="m3-input-glass w-full !h-11 text-xs font-bold {{ $errors->has('level_id') ? '!border-rose-500' : '' }}"
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
                        @error('level_id')
                            <p class="text-xs font-bold text-rose-500 mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="w-full sm:w-1/2">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Tahun Pelajaran Masuk
                        </label>
                        <div
                            class="m3-input-glass w-full !h-11 text-xs font-bold bg-zinc-100 dark:bg-zinc-800/80 flex items-center justify-between">
                            <span>{{ $tahunAktif->nama_hijriyah ?? '-' }}
                                ({{ $tahunAktif->nama_masehi ?? '-' }})</span>
                            <span
                                class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase">Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Rincian Biaya SPMB Sesuai Pengaturan Tagihan -->
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

            <!-- Submit Button & Disclaimer -->
            <div class="pt-2 space-y-4 text-center">
                <button type="submit" id="btnSubmitSpmb"
                    class="m3-btn-primary w-full h-14 text-sm font-black shadow-lg hover:shadow-primary/20 transition-all flex items-center justify-center gap-2">
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
                        <i class="bi bi-lock-fill"></i> Login Admin / Ustadz
                    </a>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function cariNomorKK() {
                const noKk = document.getElementById('no_kk').value.trim();
                const btn = document.getElementById('btnCariKK');

                if (noKk.length !== 16) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nomor KK Tidak Valid',
                        text: 'Nomor Kartu Keluarga (KK) harus terdiri dari 16 digit angka.',
                        customClass: {
                            popup: 'm3-glass-card !rounded-2xl',
                            confirmButton: 'm3-btn-primary h-10 px-6 text-xs'
                        }
                    });
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '<span class="inline-block animate-spin mr-1">↻</span> Mencari...';

                fetch(`{{ route('spmb.search-kk') }}?no_kk=${noKk}`)
                    .then(res => res.json())
                    .then(res => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-search text-sm"></i> <span>Cek No KK</span>';

                        if (res.status === 'success') {
                            // KK Ditemukan
                            document.getElementById('wali_murid_id').value = res.data.id;
                            document.getElementById('teksNamaKepala').innerText =
                                `Keluarga ${res.data.kepala_keluarga}: ${res.data.nama_kepala_keluarga}`;
                            document.getElementById('teksAlamatKk').innerText =
                                `Zonasi: ${res.data.nama_kampung} • No HP: ${res.data.no_hp || '-'}`;

                            document.getElementById('boxKkTerdaftar').classList.remove('hidden');
                            document.getElementById('boxFormKkBaru').classList.add('hidden');

                            // Auto-fill nama ayah jika kepala keluarga Ayah
                            if (res.data.kepala_keluarga === 'Ayah' && !document.getElementById('nama_ayah').value) {
                                document.getElementById('nama_ayah').value = res.data.nama_kepala_keluarga;
                            } else if (res.data.kepala_keluarga === 'Ibu' && !document.getElementById('nama_ibu').value) {
                                document.getElementById('nama_ibu').value = res.data.nama_kepala_keluarga;
                            }

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Nomor KK Terdaftar!',
                                text: `Keluarga ${res.data.nama_kepala_keluarga}`,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            // KK Belum Ada -> Tampilkan Form Baru
                            document.getElementById('wali_murid_id').value = '';
                            document.getElementById('boxKkTerdaftar').classList.add('hidden');
                            document.getElementById('boxFormKkBaru').classList.remove('hidden');

                            Swal.fire({
                                icon: 'info',
                                title: 'Nomor KK Belum Terdaftar',
                                text: 'Nomor KK belum tercatat di data madrasah. Silakan lengkapi formulir identitas keluarga baru yang muncul di bawah.',
                                customClass: {
                                    popup: 'm3-glass-card !rounded-2xl',
                                    confirmButton: 'm3-btn-primary h-10 px-6 text-xs'
                                }
                            });
                        }
                    })
                    .catch(err => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-search text-sm"></i> <span>Cek No KK</span>';
                        alert('Terjadi kesalahan koneksi server saat mencari No KK.');
                    });
            }

            function resetKK() {
                document.getElementById('no_kk').value = '';
                document.getElementById('wali_murid_id').value = '';
                document.getElementById('boxKkTerdaftar').classList.add('hidden');
                document.getElementById('boxFormKkBaru').classList.add('hidden');
            }

            function syncNamaOrtuDariKK() {
                const status = document.getElementById('kepala_keluarga').value;
                const nama = document.getElementById('nama_kepala_keluarga').value;

                if (status === 'Ayah') {
                    document.getElementById('nama_ayah').value = nama;
                } else if (status === 'Ibu') {
                    document.getElementById('nama_ibu').value = nama;
                }
            }

            // Validasi submit form
            document.getElementById('formSpmb').addEventListener('submit', function(e) {
                const noKk = document.getElementById('no_kk').value.trim();
                const waliId = document.getElementById('wali_murid_id').value;
                const namaKepala = document.getElementById('nama_kepala_keluarga').value.trim();

                if (noKk.length !== 16) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nomor KK Wajib 16 Digit',
                        text: 'Silakan isi 16 digit nomor Kartu Keluarga terlebih dahulu.',
                        customClass: {
                            confirmButton: 'm3-btn-primary h-10 px-6 text-xs'
                        }
                    });
                    return;
                }

                if (!waliId && !namaKepala) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Periksa Data KK',
                        text: 'Harap klik tombol "Cek No KK" dan lengkapi data keluarga.',
                        customClass: {
                            confirmButton: 'm3-btn-primary h-10 px-6 text-xs'
                        }
                    });
                    return;
                }
            });
        </script>
    @endpush
</x-auth-layout>
