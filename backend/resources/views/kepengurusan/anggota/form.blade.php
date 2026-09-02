<form action="{{ isset($anggota) ? route('anggota.update', $anggota->id) : route('anggota.store') }}" method="POST"
    enctype="multipart/form-data" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($anggota))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div>
            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                {{ isset($anggota) ? 'Edit Data Anggota' : 'Tambah Anggota Baru' }}
            </h3>
            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                Data personel kepengurusan yayasan
            </p>
        </div>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shadow-2xs">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">
        <!-- Sinkronisasi Ustadz -->
        <div class="bg-blue-500/10 p-4 rounded-2xl border border-blue-500/20 shadow-2xs">
            <label
                class="block text-[11px] font-black text-blue-700 dark:text-blue-300 uppercase tracking-wider mb-1.5 ml-1">
                Hubungkan ke Data Ustadz (Opsional)
            </label>
            <div class="relative">
                <select name="ustadz_id" class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer">
                    <option value="">-- Bukan Ustadz (Staf / Umum) --</option>
                    @foreach ($ustadzs as $ustadz)
                        <option value="{{ $ustadz->id }}"
                            {{ (isset($anggota) && $anggota->ustadz_id == $ustadz->id) || old('ustadz_id') == $ustadz->id ? 'selected' : '' }}>
                            {{ $ustadz->nigm }} - {{ $ustadz->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                    <i class="bi bi-chevron-down text-xs"></i>
                </div>
            </div>
            <p class="text-[11px] font-bold text-blue-800/80 dark:text-blue-300/80 mt-1.5 ml-1">
                Jika dipilih, foto dan tanda tangan otomatis mengambil dari profil Ustadz.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- NIK -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    NIK (16 Digit)
                </label>
                <input type="text" name="nik" value="{{ $anggota->nik ?? old('nik') }}" maxlength="16"
                    placeholder="16 Digit NIK" class="m3-input-glass w-full text-xs font-bold font-mono">
            </div>

            <!-- Nama Lengkap -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_lengkap" required
                    value="{{ $anggota->nama_lengkap ?? old('nama_lengkap') }}" placeholder="Nama beserta gelar"
                    class="m3-input-glass w-full text-xs font-bold uppercase">
            </div>
        </div>

        <!-- Jenis Kelamin & No HP -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Jenis Kelamin <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="jenis_kelamin" class="m3-input-glass w-full text-xs font-bold appearance-none cursor-pointer" required>
                        <option value="L"
                            {{ (isset($anggota) && $anggota->jenis_kelamin == 'L') || old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                            Laki-laki</option>
                        <option value="P"
                            {{ (isset($anggota) && $anggota->jenis_kelamin == 'P') || old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                            Perempuan</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    No. WhatsApp / HP
                </label>
                <input type="text" name="no_hp" value="{{ $anggota->no_hp ?? old('no_hp') }}"
                    placeholder="08..." class="m3-input-glass w-full text-xs font-bold font-mono">
            </div>
        </div>

        <!-- Tempat & Tanggal Lahir -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Tempat
                    Lahir</label>
                <input type="text" name="tempat_lahir"
                    value="{{ $anggota->tempat_lahir ?? old('tempat_lahir') }}"
                    placeholder="Kota/Kabupaten kelahiran"
                    class="m3-input-glass w-full text-xs font-bold uppercase">
            </div>
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Tanggal
                    Lahir</label>
                <input type="date" name="tanggal_lahir"
                    value="{{ $anggota->tanggal_lahir ?? old('tanggal_lahir') }}" class="m3-input-glass w-full text-xs font-bold cursor-pointer">
            </div>
        </div>

        <!-- Alamat -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Alamat Lengkap
            </label>
            <textarea name="alamat" rows="2" placeholder="Nama kampung, RT/RW, atau jalan..."
                class="m3-input-glass w-full !p-3 text-xs font-bold custom-scrollbar resize-none">{{ $anggota->alamat ?? old('alamat') }}</textarea>
        </div>

        <!-- File Uploads -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-zinc-200/80 dark:border-zinc-800">
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Upload Foto (Jika Bukan Ustadz)
                </label>
                <input type="file" name="foto" accept="image/*"
                    class="block w-full text-xs font-bold text-zinc-500 dark:text-zinc-400
                    file:mr-3 file:py-1.5 file:px-3
                    file:rounded-xl file:border-0
                    file:text-[10px] file:font-black file:uppercase file:tracking-wider
                    file:bg-primary/10 dark:file:bg-primary-dark/20 file:text-primary dark:file:text-primary-dark
                    hover:file:bg-primary/20 dark:hover:file:bg-primary-dark/30 file:transition-colors file:cursor-pointer
                    cursor-pointer m3-input-glass !p-1 shadow-2xs">
            </div>
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Upload TTD (Jika Bukan Ustadz)
                </label>
                <input type="file" name="tanda_tangan" accept="image/*"
                    class="block w-full text-xs font-bold text-zinc-500 dark:text-zinc-400
                    file:mr-3 file:py-1.5 file:px-3
                    file:rounded-xl file:border-0
                    file:text-[10px] file:font-black file:uppercase file:tracking-wider
                    file:bg-primary/10 dark:file:bg-primary-dark/20 file:text-primary dark:file:text-primary-dark
                    hover:file:bg-primary/20 dark:hover:file:bg-primary-dark/30 file:transition-colors file:cursor-pointer
                    cursor-pointer m3-input-glass !p-1 shadow-2xs">
            </div>
        </div>
    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-save2-fill mr-1.5"></i> {{ isset($anggota) ? 'Simpan Perubahan' : 'Simpan Data' }}
        </button>
    </div>
</form>

