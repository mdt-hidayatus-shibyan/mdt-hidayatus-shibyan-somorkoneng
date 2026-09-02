@section('title', 'Wali Murid')

<x-app-layout>

    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div class="flex items-center gap-3">
            <a href="{{ route('wali-murid.index') }}"
                class="w-10 h-10 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 rounded-xl flex items-center justify-center transition-all shadow-2xs active:scale-95 shrink-0 outline-none border border-zinc-200 dark:border-zinc-700"
                title="Kembali">
                <i class="bi bi-arrow-left text-base"></i>
            </a>
            <div>
                <h2
                    class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ isset($wali) ? 'Edit Profil Keluarga' : 'Tambah Kepala Keluarga (KK)' }}
                </h2>
                <p
                    class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Lengkapi formulir di bawah ini untuk mengelola data penanggung jawab santri.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="m3-glass-card p-5 sm:p-8 relative z-10">

        <form action="{{ isset($wali) ? route('wali-murid.update', $wali->id) : route('wali-murid.store') }}"
            method="POST" class="space-y-6">
            @csrf
            @if (isset($wali))
                @method('PUT')
            @endif

            <!-- Grid Input -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">

                <!-- No KK -->
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                        No. KK (16 Digit)
                    </label>
                    <input type="text" name="no_kk" maxlength="16" value="{{ old('no_kk', $wali->no_kk ?? '') }}"
                        placeholder="Opsional (Kosongkan jika belum ada)"
                        class="m3-input-glass w-full text-xs font-bold font-mono {{ $errors->has('no_kk') ? '!border-red-500 !ring-red-500/20' : '' }}">
                    @error('no_kk')
                        <p class="text-[11px] font-bold text-red-500 dark:text-red-400 mt-1.5 ml-1 flex items-center">
                            <i class="bi bi-exclamation-circle-fill mr-1.5"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status Penanggung Jawab -->
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                        Status Penanggung Jawab <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group/select">
                        <select name="kepala_keluarga"
                            class="m3-input-glass w-full !pr-9 text-xs font-bold cursor-pointer appearance-none {{ $errors->has('kepala_keluarga') ? '!border-red-500 !ring-red-500/20' : '' }}">
                            <option value="Ayah" class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ old('kepala_keluarga', $wali->kepala_keluarga ?? '') == 'Ayah' ? 'selected' : '' }}>
                                Ayah</option>
                            <option value="Ibu" class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ old('kepala_keluarga', $wali->kepala_keluarga ?? '') == 'Ibu' ? 'selected' : '' }}>
                                Ibu</option>
                            <option value="Wali" class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ old('kepala_keluarga', $wali->kepala_keluarga ?? '') == 'Wali' ? 'selected' : '' }}>
                                Wali / Keluarga Lainnya</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                            <i class="bi bi-chevron-down text-xs font-bold"></i>
                        </div>
                    </div>
                    @error('kepala_keluarga')
                        <p class="text-[11px] font-bold text-red-500 dark:text-red-400 mt-1.5 ml-1 flex items-center">
                            <i class="bi bi-exclamation-circle-fill mr-1.5"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                        Nama Lengkap Penanggung Jawab <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_kepala_keluarga"
                        value="{{ old('nama_kepala_keluarga', $wali->nama_kepala_keluarga ?? '') }}"
                        placeholder="Nama sesuai KK / KTP..." required
                        class="m3-input-glass w-full text-xs font-bold uppercase {{ $errors->has('nama_kepala_keluarga') ? '!border-red-500 !ring-red-500/20' : '' }}"
                        oninput="this.value = this.value.toUpperCase()">
                    @error('nama_kepala_keluarga')
                        <p class="text-[11px] font-bold text-red-500 dark:text-red-400 mt-1.5 ml-1 flex items-center">
                            <i class="bi bi-exclamation-circle-fill mr-1.5"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- No HP / WA -->
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                        No. HP / WhatsApp
                    </label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $wali->no_hp ?? '') }}"
                        placeholder="Cth: 08123456789"
                        class="m3-input-glass w-full text-xs font-bold font-mono {{ $errors->has('no_hp') ? '!border-red-500 !ring-red-500/20' : '' }}">
                    @error('no_hp')
                        <p class="text-[11px] font-bold text-red-500 dark:text-red-400 mt-1.5 ml-1 flex items-center">
                            <i class="bi bi-exclamation-circle-fill mr-1.5"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Zonasi Kampung -->
                <div class="md:col-span-2">
                    <label
                        class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                        Zonasi Kampung <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group/select">
                        <select name="kampung_id" required
                            class="m3-input-glass w-full !pr-9 text-xs font-bold cursor-pointer appearance-none {{ $errors->has('kampung_id') ? '!border-red-500 !ring-red-500/20' : '' }}">
                            <option value="" disabled class="bg-white dark:bg-zinc-900 text-zinc-500"
                                {{ !old('kampung_id', $wali->kampung_id ?? '') ? 'selected' : '' }}>-- Pilih Kampung --
                            </option>
                            @foreach ($kampungs as $kp)
                                <option value="{{ $kp->id }}" class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                    {{ old('kampung_id', $wali->kampung_id ?? '') == $kp->id ? 'selected' : '' }}>
                                    {{ $kp->nama_kampung }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                            <i class="bi bi-chevron-down text-xs font-bold"></i>
                        </div>
                    </div>
                    @error('kampung_id')
                        <p class="text-[11px] font-bold text-red-500 dark:text-red-400 mt-1.5 ml-1 flex items-center">
                            <i class="bi bi-exclamation-circle-fill mr-1.5"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Alamat Detail -->
                <div class="md:col-span-2">
                    <label
                        class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                        Alamat Detail
                    </label>
                    <textarea name="alamat_detail" rows="2" placeholder="Nama jalan, RT/RW, gang, atau patokan rumah..."
                        class="m3-input-glass w-full !p-3 text-xs font-bold custom-scrollbar {{ $errors->has('alamat_detail') ? '!border-red-500 !ring-red-500/20' : '' }}">{{ old('alamat_detail', $wali->alamat_detail ?? '') }}</textarea>
                    @error('alamat_detail')
                        <p class="text-[11px] font-bold text-red-500 dark:text-red-400 mt-1.5 ml-1 flex items-center">
                            <i class="bi bi-exclamation-circle-fill mr-1.5"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Area Toggles (Pengaturan Ekstra) -->
            <div
                class="mt-6 flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 p-4 md:p-5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">

                <!-- Toggle Asatidz -->
                <div class="flex items-center gap-3.5 w-full">
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="is_asatidz" value="1" id="is_asatidz" class="sr-only peer"
                            {{ old('is_asatidz', $wali->is_asatidz ?? false) ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-zinc-200 dark:bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-amber-500 transition-colors shadow-2xs">
                        </div>
                    </label>
                    <div>
                        <label for="is_asatidz"
                            class="text-xs font-black text-amber-600 dark:text-amber-400 cursor-pointer block uppercase tracking-wider">
                            Keluarga Asatidz
                        </label>
                        <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400">
                            Tandai jika penanggung jawab ini juga guru/pengajar di madrasah.
                        </p>
                    </div>
                </div>

                <!-- Pembatas -->
                <div class="hidden sm:block w-px h-10 bg-zinc-200 dark:border-zinc-800 mx-2 shrink-0"></div>

                <!-- Toggle Aktif -->
                <div class="flex items-center gap-3.5 w-full">
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="is_active" value="1" id="is_active" class="sr-only peer"
                            {{ old('is_active', $wali->is_active ?? true) ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-zinc-200 dark:bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-emerald-500 transition-colors shadow-2xs">
                        </div>
                    </label>
                    <div>
                        <label for="is_active"
                            class="text-xs font-black text-emerald-600 dark:text-emerald-400 cursor-pointer block uppercase tracking-wider">
                            Status KK Aktif
                        </label>
                        <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400">
                            Hapus centang jika keluarga ini sudah tidak memiliki santri aktif.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="m3-btn-primary w-full sm:w-auto h-11 px-8 text-xs font-black shadow-2xs group/btn">
                    <i class="bi bi-save2-fill mr-1.5 text-sm"></i>
                    <span>{{ isset($wali) ? 'Simpan Perubahan' : 'Simpan Data Keluarga' }}</span>
                </button>
            </div>

        </form>
    </div>
</x-app-layout>

