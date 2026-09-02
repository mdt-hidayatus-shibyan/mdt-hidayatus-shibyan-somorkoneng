@section('title', isset($murid) ? 'Edit Data Murid' : 'Tambah Murid Baru')

<x-app-layout>

    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-20">
        <div class="flex items-center gap-3.5">
            <!-- Back Button -->
            <a href="{{ route('murid.index') }}"
                class="m3-btn-secondary w-10 h-10 !p-0 inline-flex items-center justify-center shadow-2xs shrink-0"
                title="Kembali">
                <i class="bi bi-arrow-left text-base"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ isset($murid) ? 'Edit Data Murid' : 'Tambah Murid Baru' }}
                </h2>
                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5 uppercase tracking-wider">
                    Lengkapi identitas pribadi santri dan hubungkan dengan Kartu Keluarga
                </p>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ isset($murid) ? route('murid.update', $murid->id) : route('murid.store') }}" method="POST"
        enctype="multipart/form-data" class="relative z-10">
        @csrf
        @if (isset($murid))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 md:gap-6">

            <!-- ================= KOLOM KIRI (FOTO & KK) ================= -->
            <div class="xl:col-span-4 space-y-5 md:space-y-6">

                <!-- Card Foto -->
                <div class="m3-glass-card p-6 text-center">
                    <!-- Preview Box -->
                    <div
                        class="w-32 h-32 mx-auto rounded-2xl overflow-hidden border border-zinc-200/80 dark:border-zinc-700/80 bg-zinc-100 dark:bg-zinc-800/80 mb-4 relative group p-1 shadow-2xs">
                        @php
                            $jkAwal = old('jenis_kelamin', $murid->jenis_kelamin ?? 'L');
                            $defaultFoto = $jkAwal == 'L' ? 'laki-default.png' : 'perempuan-default.png';
                            $fotoPath = isset($murid) && $murid->foto ? $murid->foto : $defaultFoto;
                        @endphp
                        <img id="fotoPreview" src="{{ asset('storage/' . $fotoPath) }}"
                            class="w-full h-full object-cover rounded-xl transition-opacity duration-300 bg-white dark:bg-zinc-900">

                        <label for="fotoInput"
                            class="absolute inset-1 rounded-xl bg-black/60 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 cursor-pointer transition-all duration-300 scale-95 group-hover:scale-100 backdrop-blur-xs">
                            <i class="bi bi-camera text-2xl mb-1"></i>
                            <span class="text-[9px] font-black uppercase tracking-wider">Ubah Foto</span>
                        </label>
                    </div>

                    <input type="file" name="foto" id="fotoInput" accept="image/png, image/jpeg, image/jpg"
                        class="hidden" onchange="previewImage(this)">

                    @error('foto')
                        <p class="text-[11px] font-bold text-rose-500 mt-2 flex items-center justify-center">
                            <i class="bi bi-exclamation-triangle-fill mr-1"></i> {{ $message }}
                        </p>
                    @enderror
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mt-2">
                        Klik foto untuk unggah (Maks 2MB)
                    </p>
                </div>

                <!-- Card Cari KK -->
                <div class="m3-glass-card p-6 bg-primary/5 dark:bg-primary-dark/5 border-primary/20 dark:border-primary-dark/20 relative">
                    <label
                        class="text-[11px] font-black text-primary dark:text-primary-dark uppercase tracking-wider mb-3 ml-1 flex items-center">
                        <i class="bi bi-search mr-1.5"></i> Cari & Tautkan KK <span class="text-rose-500 ml-0.5">*</span>
                    </label>

                    <div class="flex gap-2 mb-2">
                        <input type="text" id="input_cari_kk" name="input_cari_kk" placeholder="Ketik 16 Digit KK..."
                            value="{{ old('input_cari_kk', isset($murid) ? $murid->waliMurid->no_kk : '') }}"
                            class="m3-input-glass w-full font-mono text-xs">

                        <button type="button" onclick="cariKK()"
                            class="m3-btn-primary w-11 h-10 !p-0 shrink-0 inline-flex items-center justify-center"
                            title="Cari KK">
                            <i class="bi bi-search text-sm font-bold"></i>
                        </button>
                    </div>
                    <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 mb-4 ml-1">
                        Masukkan Nomor KK lalu klik tombol cari
                    </p>

                    <input type="hidden" name="wali_murid_id" id="wali_murid_id"
                        value="{{ old('wali_murid_id', $murid->wali_murid_id ?? '') }}">

                    @php $hasWali = isset($murid) || old('wali_murid_id'); @endphp

                    <!-- Hasil Pencarian KK -->
                    <div id="hasil_pencarian_kk"
                        class="{{ $hasWali ? '' : 'hidden' }} p-3.5 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/15 border border-emerald-500/20 transition-all mt-2">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <i class="bi bi-check-lg text-lg font-bold"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-wider mb-0.5">
                                    Keluarga Terhubung
                                </p>
                                <p id="teks_hasil_kk"
                                    class="text-xs font-black text-zinc-900 dark:text-white leading-tight truncate">
                                    {{ $hasWali && isset($murid) ? 'Keluarga ' . $murid->waliMurid->nama_kepala_keluarga : '' }}
                                </p>
                                <p id="teks_kampung_kk"
                                    class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 mt-0.5 truncate">
                                    {{ $hasWali && isset($murid) ? 'Zonasi: ' . $murid->waliMurid->kampung->nama_kampung : '' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @error('wali_murid_id')
                        <p class="text-[11px] font-bold text-rose-500 mt-3 ml-1 flex items-center">
                            <i class="bi bi-exclamation-triangle-fill mr-1"></i> Harap cari dan tautkan data keluarga.
                        </p>
                    @enderror
                </div>
            </div>

            <!-- ================= KOLOM KANAN (FORM IDENTITAS) ================= -->
            <div class="xl:col-span-8 flex flex-col gap-5 md:gap-6">
                <!-- Identitas Utama -->
                <div class="m3-glass-card p-6 sm:p-7">
                    <h3
                        class="text-sm md:text-base font-black text-zinc-900 dark:text-white mb-5 flex items-center border-b border-zinc-200/80 dark:border-zinc-800 pb-3 gap-2">
                        <div class="w-7 h-7 rounded-lg bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center border border-primary/20 shrink-0">
                            <i class="bi bi-person-vcard text-xs"></i>
                        </div>
                        <span>Identitas Utama Santri</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <!-- NISM -->
                        <div>
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                                NISM (Madrasah) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nism" value="{{ old('nism', $murid->nism ?? '') }}"
                                class="m3-input-glass font-mono w-full text-xs font-bold {{ $errors->has('nism') ? '!border-rose-500' : '' }}">
                            @error('nism')
                                <p class="text-[10px] font-bold text-rose-500 mt-1 ml-1 flex items-center"><i
                                        class="bi bi-exclamation-triangle-fill mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        <!-- NISN -->
                        <div>
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">NISN
                                (Nasional)</label>
                            <input type="text" name="nisn" value="{{ old('nisn', $murid->nisn ?? '') }}"
                                class="m3-input-glass font-mono w-full text-xs font-bold {{ $errors->has('nisn') ? '!border-rose-500' : '' }}">
                        </div>
                        <!-- NIK -->
                        <div>
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">NIK
                                (16 Digit)</label>
                            <input type="text" name="nik" maxlength="16"
                                value="{{ old('nik', $murid->nik ?? '') }}"
                                class="m3-input-glass font-mono w-full text-xs font-bold {{ $errors->has('nik') ? '!border-rose-500' : '' }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 mb-4">
                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-6">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_lengkap"
                                value="{{ old('nama_lengkap', $murid->nama_lengkap ?? '') }}"
                                class="m3-input-glass uppercase w-full text-xs font-bold {{ $errors->has('nama_lengkap') ? '!border-rose-500' : '' }}"
                                oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <!-- Nama Panggilan -->
                        <div class="sm:col-span-3">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Panggilan</label>
                            <input type="text" name="nama_panggilan"
                                value="{{ old('nama_panggilan', $murid->nama_panggilan ?? '') }}"
                                class="m3-input-glass w-full text-xs font-bold">
                        </div>
                        <!-- Gender -->
                        <div class="sm:col-span-3 relative group/select">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                                Gender <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="jenis_kelamin"
                                    class="m3-input-glass w-full !pr-9 text-xs font-bold appearance-none cursor-pointer">
                                    <option value="L"
                                        {{ old('jenis_kelamin', $murid->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="P"
                                        {{ old('jenis_kelamin', $murid->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-chevron-down text-xs font-bold"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Tpt Lahir -->
                        <div class="lg:col-span-1">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Tpt
                                Lahir</label>
                            <input type="text" name="tempat_lahir"
                                value="{{ old('tempat_lahir', $murid->tempat_lahir ?? '') }}"
                                oninput="this.value = this.value.toUpperCase()"
                                class="m3-input-glass w-full text-xs font-bold">
                        </div>
                        <!-- Tgl Lahir -->
                        <div class="lg:col-span-1">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Tgl
                                Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $murid->tanggal_lahir ?? '') }}"
                                class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                        </div>
                        <!-- Anak Ke -->
                        <div class="lg:col-span-1">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Anak
                                Ke-</label>
                            <input type="number" name="anak_ke" min="1"
                                value="{{ old('anak_ke', $murid->anak_ke ?? '') }}"
                                class="m3-input-glass w-full text-center text-xs font-bold">
                        </div>
                        <!-- Status Anak -->
                        <div class="lg:col-span-1 relative group/select">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Status
                                Anak</label>
                            <div class="relative">
                                <select name="hub_kel"
                                    class="m3-input-glass w-full !pr-9 text-xs font-bold appearance-none cursor-pointer">
                                    <option value="Anak Kandung"
                                        {{ old('hub_kel', $murid->hub_kel ?? '') == 'Anak Kandung' ? 'selected' : '' }}>
                                        Kandung</option>
                                    <option value="Anak Tiri"
                                        {{ old('hub_kel', $murid->hub_kel ?? '') == 'Anak Tiri' ? 'selected' : '' }}>
                                        Tiri</option>
                                    <option value="Anak Angkat"
                                        {{ old('hub_kel', $murid->hub_kel ?? '') == 'Anak Angkat' ? 'selected' : '' }}>
                                        Angkat</option>
                                    <option value="Cucu"
                                        {{ old('hub_kel', $murid->hub_kel ?? '') == 'Cucu' ? 'selected' : '' }}>Cucu
                                    </option>
                                    <option value="Lainnya"
                                        {{ old('hub_kel', $murid->hub_kel ?? '') == 'Lainnya' ? 'selected' : '' }}>
                                        Lainnya</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-chevron-down text-xs font-bold"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orang Tua & Kelas Masuk -->
                <div class="m3-glass-card p-6 sm:p-7">
                    <h3
                        class="text-sm md:text-base font-black text-zinc-900 dark:text-white mb-4 flex items-center border-b border-zinc-200/80 dark:border-zinc-800 pb-3 gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-500/20 shrink-0">
                            <i class="bi bi-people-fill text-xs"></i>
                        </div>
                        <span>Data Orang Tua Kandung</span>
                    </h3>

                    <!-- Ayah -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 mb-4">
                        <div class="sm:col-span-4">
                            <label
                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">NIK
                                Ayah</label>
                            <input type="text" name="nik_ayah" maxlength="16"
                                value="{{ old('nik_ayah', $murid->nik_ayah ?? '') }}" placeholder="Opsional"
                                class="m3-input-glass font-mono w-full text-xs font-bold">
                        </div>
                        <div class="sm:col-span-5">
                            <label
                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Nama
                                Ayah</label>
                            <input type="text" name="nama_ayah"
                                value="{{ old('nama_ayah', $murid->nama_ayah ?? '') }}" placeholder="Nama Ayah"
                                class="m3-input-glass w-full text-xs font-bold">
                        </div>
                        <div class="sm:col-span-3 relative group/select">
                            <label
                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Status
                                Ayah</label>
                            <div class="relative">
                                <select name="status_ayah"
                                    class="m3-input-glass w-full !pr-8 text-xs font-bold appearance-none cursor-pointer">
                                    <option value="Hidup"
                                        {{ old('status_ayah', $murid->status_ayah ?? '') == 'Hidup' ? 'selected' : '' }}>
                                        Hidup</option>
                                    <option value="Meninggal"
                                        {{ old('status_ayah', $murid->status_ayah ?? '') == 'Meninggal' ? 'selected' : '' }}>
                                        Meninggal</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-chevron-down text-[10px] font-bold"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ibu -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 mb-6">
                        <div class="sm:col-span-4">
                            <label
                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">NIK
                                Ibu</label>
                            <input type="text" name="nik_ibu" maxlength="16"
                                value="{{ old('nik_ibu', $murid->nik_ibu ?? '') }}" placeholder="Opsional"
                                class="m3-input-glass font-mono w-full text-xs font-bold">
                        </div>
                        <div class="sm:col-span-5">
                            <label
                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Nama
                                Ibu</label>
                            <input type="text" name="nama_ibu"
                                value="{{ old('nama_ibu', $murid->nama_ibu ?? '') }}" placeholder="Nama Ibu"
                                class="m3-input-glass w-full text-xs font-bold">
                        </div>
                        <div class="sm:col-span-3 relative group/select">
                            <label
                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Status
                                Ibu</label>
                            <div class="relative">
                                <select name="status_ibu"
                                    class="m3-input-glass w-full !pr-8 text-xs font-bold appearance-none cursor-pointer">
                                    <option value="Hidup"
                                        {{ old('status_ibu', $murid->status_ibu ?? '') == 'Hidup' ? 'selected' : '' }}>
                                        Hidup</option>
                                    <option value="Meninggal"
                                        {{ old('status_ibu', $murid->status_ibu ?? '') == 'Meninggal' ? 'selected' : '' }}>
                                        Meninggal</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-chevron-down text-[10px] font-bold"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kelas Masuk -->
                    <h3
                        class="text-sm md:text-base font-black text-zinc-900 dark:text-white mb-4 flex items-center border-b border-zinc-200/80 dark:border-zinc-800 pb-3 gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0">
                            <i class="bi bi-door-open-fill text-xs"></i>
                        </div>
                        <span>Data Kelas Masuk Awal</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative group/select">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Tahun
                                Masuk <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select name="tahun_masuk"
                                    class="m3-input-glass w-full !pr-9 text-xs font-bold appearance-none cursor-pointer {{ $errors->has('tahun_masuk') ? '!border-rose-500' : '' }}">
                                    <option value="" disabled selected>-- Pilih Tahun --</option>
                                    @foreach ($tahunPelajaranMasuk as $tp)
                                        <option value="{{ $tp->id }}"
                                            {{ old('tahun_masuk', $murid->tahun_masuk ?? '') == $tp->id || $tp->is_active ? 'selected' : '' }}>
                                            {{ $tp->nama_hijriyah }} ({{ $tp->nama_masehi }})
                                            {{ $tp->is_active ? ' - [Aktif]' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-chevron-down text-xs font-bold"></i>
                                </div>
                            </div>
                        </div>
                        <div class="relative group/select">
                            <label
                                class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">Kelas
                                Masuk <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select name="level_masuk"
                                    class="m3-input-glass w-full !pr-9 text-xs font-bold appearance-none cursor-pointer {{ $errors->has('level_masuk') ? '!border-rose-500' : '' }}">
                                    <option value="" disabled selected>-- Pilih Kelas --</option>
                                    @foreach ($levelMasuk as $lvl)
                                        <option value="{{ $lvl->id }}"
                                            {{ old('level_masuk', $murid->level_masuk ?? '') == $lvl->id ? 'selected' : '' }}>
                                            {{ $lvl->tingkat->kode_tingkat }} - {{ $lvl->nama_level }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                                    <i class="bi bi-chevron-down text-xs font-bold"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit Form Identitas -->
                    <div class="flex justify-end pt-5 border-t border-zinc-200/80 dark:border-zinc-800 mt-6">
                        <input type="hidden" name="status" value="{{ old('status', $murid->status ?? 'Aktif') }}">
                        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-8 group/btn">
                            <i class="bi bi-save2-fill text-xs mr-1"></i>
                            <span>{{ isset($murid) ? 'Simpan Identitas' : 'Simpan Murid Baru' }}</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </form>

    <!-- KOTAK EDIT STATUS & HAPUS (HANYA MUNCUL SAAT EDIT) -->
    @if (isset($murid))
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 md:gap-6 mt-5 md:mt-6 relative z-10">
            <div class="xl:col-span-4 hidden xl:block"></div>

            <div class="xl:col-span-8 space-y-5 md:space-y-6">
                <!-- Status Keaktifan -->
                <div class="m3-glass-card p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="font-black text-zinc-900 dark:text-white text-sm md:text-base tracking-tight flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-md bg-primary/10 text-primary dark:text-primary-dark flex items-center justify-center">
                                <i class="bi bi-activity text-xs"></i>
                            </div>
                            <span>Status Keaktifan Murid</span>
                        </h3>
                        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-lg">
                            Jika dinonaktifkan (Lulus/Pindah/Berhenti) dan ini adalah anak aktif terakhir, <strong
                                class="text-rose-500 dark:text-rose-400">akun Keluarga otomatis
                                dinonaktifkan</strong> dari sistem.
                        </p>
                    </div>

                    <form id="formStatusMurid" action="{{ route('murid.updateStatus', $murid->id) }}" method="POST"
                        class="flex items-center gap-2 w-full md:w-auto shrink-0">
                        @csrf @method('PATCH')

                        <div class="relative w-full md:w-44 group/select">
                            <select name="status"
                                class="m3-input-glass w-full !pr-8 font-black text-xs uppercase tracking-wider {{ $murid->status == 'Aktif' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30' }}">
                                <option value="Aktif" {{ $murid->status == 'Aktif' ? 'selected' : '' }}>AKTIF
                                </option>
                                <option value="Lulus" {{ $murid->status == 'Lulus' ? 'selected' : '' }}>LULUS
                                </option>
                                <option value="Pindah" {{ $murid->status == 'Pindah' ? 'selected' : '' }}>PINDAH
                                </option>
                                <option value="Berhenti" {{ $murid->status == 'Berhenti' ? 'selected' : '' }}>BERHENTI
                                </option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-zinc-400">
                                <i class="bi bi-chevron-down text-xs font-bold"></i>
                            </div>
                        </div>

                        <button type="button" onclick="konfirmasiStatus('formStatusMurid')"
                            class="m3-btn-primary w-9 h-9 !p-0 shrink-0 inline-flex items-center justify-center shadow-2xs"
                            title="Terapkan Status">
                            <i class="bi bi-check2-circle text-base"></i>
                        </button>
                    </form>
                </div>

                @can('hapus murid')
                    <!-- Danger Zone -->
                    <div class="bg-rose-500/5 dark:bg-rose-950/20 rounded-2xl md:rounded-3xl p-5 sm:p-6 border border-rose-300/40 dark:border-rose-800/40">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                            <div class="flex items-start gap-3.5 flex-1">
                                <div
                                    class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/30">
                                    <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm md:text-base font-black text-rose-700 dark:text-rose-400 tracking-tight">
                                        Hapus Data Murid
                                    </h3>
                                    <p class="text-xs font-semibold text-rose-900/80 dark:text-rose-300/80 mt-0.5 max-w-md mb-3">
                                        Tindakan ini permanen dan tidak dapat dibatalkan. Seluruh rekaman nilai, presensi, dan data murid ini akan dihapus.
                                    </p>

                                    <!-- Checkbox Konfirmasi -->
                                    <label class="inline-flex items-center gap-2.5 cursor-pointer select-none group">
                                        <div class="relative flex items-center justify-center">
                                            <input type="checkbox" id="verify_delete_murid" class="peer sr-only">
                                            <div
                                                class="w-4 h-4 rounded border-2 border-rose-400/60 peer-checked:bg-rose-500 peer-checked:border-rose-500 transition-all bg-white dark:bg-zinc-900">
                                            </div>
                                            <i
                                                class="bi bi-check absolute text-white opacity-0 peer-checked:opacity-100 text-xs font-black transition-opacity pointer-events-none"></i>
                                        </div>
                                        <span
                                            class="text-[11px] font-black text-rose-700 dark:text-rose-400 uppercase tracking-wider group-hover:text-rose-800 dark:group-hover:text-rose-300 transition-colors">
                                            Saya mengerti dan ingin menghapus data ini
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Tombol Hapus -->
                            @can('delete murid')
                                <div class="w-full sm:w-auto shrink-0 mt-2 sm:mt-0">
                                    <form action="{{ route('murid.destroy', $murid->id) }}" method="POST"
                                        class="delete-ajax inline m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" id="btn_delete_murid_permanen" disabled
                                            class="h-10 w-full sm:w-auto px-5 bg-rose-600 hover:bg-rose-700 dark:bg-rose-700 dark:hover:bg-rose-600 text-white rounded-xl text-xs font-black shadow-2xs transition-all active:scale-95 flex items-center justify-center gap-1.5 outline-none disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100 border border-rose-500/40">
                                            <i class="bi bi-trash-fill text-xs"></i>
                                            <span>Hapus Permanen</span>
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan
            </div>
        </div>
    @endif


    @push('script')
        <script>
            const defaultL = "{{ asset('laki-default.png') }}";
            const defaultP = "{{ asset('perempuan-default.png') }}";
            const preview = document.getElementById('fotoPreview');
            let isCustom = {{ isset($murid) && $murid->foto ? 'true' : 'false' }};

            function previewImage(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        isCustom = true;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            document.querySelector('select[name="jenis_kelamin"]').addEventListener('change', function() {
                if (!isCustom) {
                    preview.style.opacity = '0';
                    setTimeout(() => {
                        preview.src = this.value === 'L' ? defaultL : defaultP;
                        preview.style.opacity = '1';
                    }, 150);
                }
            });

            function cariKK() {
                const noKk = document.getElementById('input_cari_kk').value.trim();
                const isDark = document.documentElement.classList.contains('dark');

                if (!noKk) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<span class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">Kolom Kosong</span>',
                        html: '<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-1">Silakan ketik 16 Digit Nomor KK terlebih dahulu!</p>',
                        buttonsStyling: false,
                        color: isDark ? '#f4f4f5' : '#18181b',
                        customClass: {
                            popup: 'm3-glass-card border border-zinc-200/80 dark:border-zinc-800 shadow-2xl !bg-white/95 dark:!bg-zinc-900/95',
                            confirmButton: "m3-btn-primary h-10 px-6 text-xs"
                        }
                    });
                    return;
                }

                // Panggil API Pencarian
                fetch(`{{ route('wali-murid.searchKk') }}?no_kk=${noKk}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById('wali_murid_id').value = data.data.id;
                            document.getElementById('teks_hasil_kk').innerText = `Keluarga ${data.data.nama_kepala}`;
                            document.getElementById('teks_kampung_kk').innerText = `Zonasi: ${data.data.kampung}`;
                            document.getElementById('hasil_pencarian_kk').classList.remove('hidden');

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: '<span class="text-xs font-black text-zinc-900 dark:text-white">Data KK Ditemukan!</span>',
                                showConfirmButton: false,
                                timer: 2000,
                                customClass: {
                                    popup: 'm3-glass-card !bg-white/95 dark:!bg-zinc-900/95 border border-zinc-200 dark:border-zinc-800 shadow-lg'
                                }
                            });
                        } else {
                            document.getElementById('wali_murid_id').value = '';
                            document.getElementById('hasil_pencarian_kk').classList.add('hidden');

                            Swal.fire({
                                icon: 'error',
                                title: '<span class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">Data Tidak Ditemukan!</span>',
                                html: '<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-2">Nomor KK tersebut belum terdaftar. Anda harus menambahkan profil keluarga baru terlebih dahulu.</p>',
                                showCancelButton: true,
                                buttonsStyling: false,
                                heightAuto: false,
                                color: isDark ? '#f4f4f5' : '#18181b',
                                customClass: {
                                    popup: 'm3-glass-card border border-zinc-200/80 dark:border-zinc-800 shadow-2xl !bg-white/95 dark:!bg-zinc-900/95',
                                    actions: "gap-2.5 mt-5",
                                    confirmButton: "m3-btn-primary h-10 px-5 text-xs",
                                    cancelButton: "m3-btn-secondary h-10 px-5 text-xs"
                                },
                                confirmButtonText: 'Daftarkan KK Baru <i class="bi bi-arrow-right ml-1"></i>',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open('{{ route('wali-murid.create') }}', '_blank');
                                }
                            });
                        }
                    })
                    .catch(error => {
                        alert('Terjadi kesalahan koneksi.');
                    });
            }

            function konfirmasiStatus(formId) {
                const isDark = document.documentElement.classList.contains('dark');

                Swal.fire({
                    title: '<span class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">Ubah Status Keaktifan?</span>',
                    html: '<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-1">Status santri ini dan akses akun Keluarga (jika anak terakhir) akan diperbarui oleh sistem.</p>',
                    icon: 'warning',
                    heightAuto: false,
                    showCancelButton: true,
                    buttonsStyling: false,
                    color: isDark ? '#f4f4f5' : '#18181b',
                    reverseButtons: true,
                    customClass: {
                        popup: 'm3-glass-card border border-zinc-200/80 dark:border-zinc-800 shadow-2xl !bg-white/95 dark:!bg-zinc-900/95',
                        actions: "gap-2.5 mt-5",
                        confirmButton: "m3-btn-primary h-10 px-6 text-xs",
                        cancelButton: "m3-btn-secondary h-10 px-5 text-xs"
                    },
                    confirmButtonText: '<i class="bi bi-check2-circle mr-1"></i> Ya, Terapkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            html: '<p class="text-xs font-bold text-zinc-500">Menerapkan perubahan status ke database.</p>',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'm3-glass-card !bg-white/95 dark:!bg-zinc-900/95 border border-zinc-200 dark:border-zinc-800 shadow-lg'
                            },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        document.getElementById(formId).submit();
                    }
                });
            }

            const verifyDeleteCheckbox = document.getElementById('verify_delete_murid');
            const btnDeletePermanen = document.getElementById('btn_delete_murid_permanen');

            if (verifyDeleteCheckbox && btnDeletePermanen) {
                verifyDeleteCheckbox.addEventListener('change', function() {
                    btnDeletePermanen.disabled = !this.checked;
                });
            }
        </script>
    @endpush

</x-app-layout>

