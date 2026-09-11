@php
    $isEdit = isset($nasabah);
@endphp

<form id="nasabahForm" onsubmit="submitNasabahForm(event)" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if ($isEdit)
        @method('PUT')
        <input type="hidden" id="nasabah_id" value="{{ $nasabah->id }}">
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Tipe Nasabah <span class="text-rose-500">*</span>
            </label>
            <select name="tipe_nasabah" id="nasabah_tipe" onchange="handleTipeNasabahChange(this.value)" required
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="umum" {{ ($nasabah->tipe_nasabah ?? 'umum') === 'umum' ? 'selected' : '' }}>Masyarakat
                    Umum / Pihak Luar</option>
                <option value="ustadz" {{ ($nasabah->tipe_nasabah ?? '') === 'ustadz' ? 'selected' : '' }}>Ustadz /
                    Tenaga Pendidik</option>
                <option value="wali_murid" {{ ($nasabah->tipe_nasabah ?? '') === 'wali_murid' ? 'selected' : '' }}>Wali
                    Murid</option>
                <option value="pengurus" {{ ($nasabah->tipe_nasabah ?? '') === 'pengurus' ? 'selected' : '' }}>Pengurus
                    Madrasah</option>
            </select>
        </div>

        <!-- Opsi Autocomplete Ustadz / Wali -->
        <div id="containerPilihUstadz" class="hidden">
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Pilih Data Ustadz
            </label>
            <select name="ustadz_id" id="nasabah_ustadz_id" onchange="autofillNasabahFromLookup('ustadz', this.value)"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="">-- Pilih Ustadz Terdaftar --</option>
                @foreach ($ustadzs as $u)
                    <option value="{{ $u->id }}" {{ ($nasabah->ustadz_id ?? '') == $u->id ? 'selected' : '' }}>
                        {{ $u->nama_lengkap }} ({{ $u->nigm ?? 'Ustadz' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div id="containerPilihWali" class="hidden">
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Pilih Data Wali Murid
            </label>
            <select name="wali_murid_id" id="nasabah_wali_murid_id"
                onchange="autofillNasabahFromLookup('wali_murid', this.value)"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="">-- Pilih Wali Murid --</option>
                @foreach ($waliMurids as $w)
                    <option value="{{ $w->id }}"
                        {{ ($nasabah->wali_murid_id ?? '') == $w->id ? 'selected' : '' }}>
                        {{ $w->nama_lengkap }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Nama Lengkap Nasabah <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_lengkap" id="nasabah_nama_lengkap" required
                value="{{ old('nama_lengkap', $nasabah->nama_lengkap ?? '') }}" placeholder="Contoh: Ahmad Fauzi"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                NIK KTP
            </label>
            <input type="text" name="nik_ktp" id="nasabah_nik_ktp"
                value="{{ old('nik_ktp', $nasabah->nik_ktp ?? '') }}" placeholder="16 digit nomor KTP"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-mono">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                No. HP / WhatsApp <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="no_hp" id="nasabah_no_hp" required
                value="{{ old('no_hp', $nasabah->no_hp ?? '') }}" placeholder="Contoh: 08123456789"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-mono">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Pekerjaan / Usaha
            </label>
            <input type="text" name="pekerjaan" id="nasabah_pekerjaan"
                value="{{ old('pekerjaan', $nasabah->pekerjaan ?? '') }}"
                placeholder="Contoh: Wiraswasta, Pedagang, Petani"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
            Alamat Domisili Lengkap <span class="text-rose-500">*</span>
        </label>
        <textarea name="alamat" id="nasabah_alamat" rows="2" required
            placeholder="Dusun, Desa/Kelurahan, RT/RW, Kecamatan, Kabupaten..."
            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all resize-none">{{ old('alamat', $nasabah->alamat ?? '') }}</textarea>
    </div>

    <!-- Upload Foto KTP & Nasabah -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Foto KTP
            </label>
            <input type="file" name="foto_ktp" id="nasabah_foto_ktp" accept="image/*"
                class="w-full text-xs text-zinc-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Foto Profil Nasabah
            </label>
            <input type="file" name="foto_nasabah" id="nasabah_foto_nasabah" accept="image/*"
                class="w-full text-xs text-zinc-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
            Catatan Tambahan
        </label>
        <textarea name="catatan" id="nasabah_catatan" rows="2"
            placeholder="Catatan penjamin, reputasi, atau info kontak darurat..."
            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all resize-none">{{ old('catatan', $nasabah->catatan ?? '') }}</textarea>
    </div>

    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-zinc-200/80 dark:border-zinc-800">
        <button type="button" onclick="closeNasabahModal()"
            class="px-4 py-2.5 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all">
            Batal
        </button>
        <button type="submit" id="btnSubmitNasabah"
            class="m3-btn-primary px-5 py-2.5 text-xs font-bold shadow-md flex items-center gap-2">
            <i class="bi bi-check2-circle text-sm"></i>
            <span>{{ $isEdit ? 'Simpan Perubahan' : 'Daftarkan Nasabah' }}</span>
        </button>
    </div>
</form>

<script>
    function handleTipeNasabahChange(val) {
        const cUstadz = document.getElementById('containerPilihUstadz');
        const cWali = document.getElementById('containerPilihWali');

        if (val === 'ustadz') {
            cUstadz.classList.remove('hidden');
            cWali.classList.add('hidden');
        } else if (val === 'wali_murid') {
            cWali.classList.remove('hidden');
            cUstadz.classList.add('hidden');
        } else {
            cUstadz.classList.add('hidden');
            cWali.classList.add('hidden');
        }
    }

    function autofillNasabahFromLookup(type, id) {
        if (!id) return;

        fetch(`{{ route('keuangan.nasabah.ajax-lookup') }}?type=${type}&id=${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const data = res.data;
                    document.getElementById('nasabah_nama_lengkap').value = data.nama_lengkap || '';
                    document.getElementById('nasabah_nik_ktp').value = data.nik_ktp || '';
                    document.getElementById('nasabah_no_hp').value = data.no_hp || '';
                    document.getElementById('nasabah_alamat').value = data.alamat || '';
                    document.getElementById('nasabah_pekerjaan').value = data.pekerjaan || '';
                }
            });
    }

    // Initialize on load
    handleTipeNasabahChange(document.getElementById('nasabah_tipe').value);
</script>
