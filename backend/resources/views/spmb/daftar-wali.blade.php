@section('title', 'Pendaftaran Profil Keluarga Baru - SPMB')

<x-auth-layout maxWidth="max-w-2xl">
    <div class="space-y-6">
        <!-- Header & Title -->
        <div class="text-center">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black tracking-wider uppercase bg-amber-500/10 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400 border border-amber-500/20">
                <i class="bi bi-person-plus-fill"></i> Pendaftaran Profil Keluarga Baru
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight mt-2">
                Identitas Wali Murid
            </h2>
            <p class="text-xs sm:text-sm font-semibold text-zinc-500 dark:text-zinc-400 mt-1 max-w-md mx-auto">
                Nomor KK <span class="font-mono font-bold text-zinc-800 dark:text-zinc-200">{{ $noKk }}</span>
                belum tercatat di madrasah. Silakan lengkapi data profil keluarga berikut:
            </p>
        </div>

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

        <!-- Card Form Registrasi Wali Murid -->
        <div
            class="p-6 sm:p-7 rounded-3xl bg-white/80 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 space-y-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center font-black text-base border border-amber-500/20 shrink-0">
                        <i class="bi bi-house-door-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white">Data Kepala Keluarga / Wali</h3>
                        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Data ini akan digunakan
                            sebagai identitas keluarga murid</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('spmb.store-wali') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="no_kk" value="{{ $noKk }}">

                <!-- Nomor KK (Readonly) -->
                <div>
                    <label
                        class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                        Nomor Kartu Keluarga (KK)
                    </label>
                    <div
                        class="flex items-center justify-between p-3.5 rounded-2xl bg-zinc-100 dark:bg-zinc-800/70 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-credit-card-2-front text-lg text-primary dark:text-primary-dark"></i>
                            <span
                                class="font-mono text-sm font-black text-zinc-900 dark:text-white tracking-widest">{{ $noKk }}</span>
                        </div>
                        <a href="{{ route('spmb.index') }}"
                            class="text-xs font-bold text-rose-500 hover:underline flex items-center gap-1">
                            <i class="bi bi-pencil-square"></i> Ubah KK
                        </a>
                    </div>
                </div>

                <!-- Status Kepala Keluarga & Nama -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-1/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Status Kepala <span class="text-rose-500">*</span>
                        </label>
                        <select name="kepala_keluarga" id="kepala_keluarga"
                            class="m3-input-glass w-full !h-12 text-xs font-bold" required>
                            <option value="Ayah" {{ old('kepala_keluarga') == 'Ayah' ? 'selected' : '' }}>Ayah
                            </option>
                            <option value="Ibu" {{ old('kepala_keluarga') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                            <option value="Wali" {{ old('kepala_keluarga') == 'Wali' ? 'selected' : '' }}>Wali /
                                Lainnya</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-2/3">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Nama Lengkap Kepala Keluarga <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_kepala_keluarga" id="nama_kepala_keluarga"
                            value="{{ old('nama_kepala_keluarga') }}" placeholder="Nama Sesuai di Kartu Keluarga"
                            class="m3-input-glass w-full !h-12 text-xs font-bold uppercase" required
                            oninput="this.value = this.value.toUpperCase()">
                    </div>
                </div>

                <!-- No HP/WA & Dusun Kampung -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full sm:w-1/2">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Nomor HP / WhatsApp Aktif <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}"
                                placeholder="Contoh: 081234567890"
                                class="m3-input-glass font-mono w-full !h-12 text-xs font-bold pl-10" required>
                            <i
                                class="bi bi-whatsapp text-emerald-500 text-base absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="w-full sm:w-1/2">
                        <label
                            class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                            Zonasi / Dusun Kampung <span class="text-rose-500">*</span>
                        </label>
                        <select name="kampung_id" id="kampung_id" class="m3-input-glass w-full !h-12 text-xs font-bold"
                            required>
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

                <!-- Alamat Lengkap -->
                <div>
                    <label
                        class="block text-xs font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider mb-1.5 ml-0.5">
                        Alamat Lengkap / Keterangan Rumah
                    </label>
                    <textarea name="alamat_detail" id="alamat_detail" rows="2" placeholder="Contoh: RT 02 RW 01, Samping Masjid..."
                        class="m3-input-glass w-full text-xs font-semibold !p-3">{{ old('alamat_detail') }}</textarea>
                </div>

                <!-- Tombol Lanjut -->
                <div class="pt-3">
                    <button type="submit"
                        class="m3-btn-primary w-full !h-12 text-sm font-black shadow-lg hover:shadow-primary/20 transition-all flex items-center justify-center gap-2">
                        <span>Simpan Data Keluarga & Lanjut Isi Data Murid</span>
                        <i class="bi bi-arrow-right text-base"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Links -->
        <div class="pt-2 flex items-center justify-between text-xs font-semibold text-zinc-500">
            <a href="{{ route('spmb.index') }}"
                class="hover:text-primary dark:hover:text-primary-dark transition-colors flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Cek KK
            </a>
            <a href="{{ route('login') }}"
                class="hover:text-primary dark:hover:text-primary-dark transition-colors flex items-center gap-1">
                <i class="bi bi-lock-fill"></i> Login Admin
            </a>
        </div>
    </div>
</x-auth-layout>
