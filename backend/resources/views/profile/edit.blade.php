@section('title', 'Profil Pengguna')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 relative z-10">
        <div
            class="flex items-center gap-1.5 text-[10px] md:text-[11px] font-black text-zinc-400 uppercase tracking-widest mb-1.5">
            <span class="text-emerald-600 dark:text-emerald-400">Pengaturan</span>
            <i class="bi bi-chevron-right text-[8px] opacity-60"></i>
            <span>Akun Saya</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
            Profil Pengguna
        </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 md:gap-6 relative z-10">

        <!-- ================= Left Column: Profile Summary ================= -->
        <div class="lg:col-span-1">
            <div class="m3-glass-card p-5 md:p-6 flex flex-col items-center text-center rounded-3xl shadow-2xs">

                <div
                    class="relative w-24 h-24 rounded-3xl p-1 border-2 border-emerald-500/20 mb-4 group cursor-pointer z-10 shadow-2xs">
                    <div
                        class="w-full h-full rounded-2xl overflow-hidden bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl font-black">
                        @if (auth()->user()?->administrator?->foto)
                            <img src="{{ asset('storage/' . auth()->user()->administrator->foto) }}" alt="Foto Profil"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        @endif
                    </div>
                </div>

                <div class="relative z-10 w-full">
                    <h3 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight truncate">
                        {{ auth()->user()->name }}
                    </h3>
                    <div
                        class="inline-block px-3 py-0.5 mt-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider">
                        {{ auth()->user()?->roles->first()->name ?? 'Pengguna' }}
                    </div>
                </div>

                <hr class="w-full border-zinc-200/80 dark:border-zinc-800 my-5 relative z-10">

                <div class="w-full space-y-2.5 relative z-10 text-left">
                    <!-- Detail Username -->
                    <div
                        class="flex items-center gap-3 text-xs p-2.5 rounded-2xl bg-white/40 dark:bg-black/40 border border-zinc-200/60 dark:border-zinc-800">
                        <div
                            class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 text-sm">
                            <i class="bi bi-at"></i>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-wider">
                                Username
                            </p>
                            <p class="text-xs font-bold text-zinc-900 dark:text-white truncate">
                                {{ auth()->user()->username }}
                            </p>
                        </div>
                    </div>

                    <!-- Detail Email -->
                    <div
                        class="flex items-center gap-3 text-xs p-2.5 rounded-2xl bg-white/40 dark:bg-black/40 border border-zinc-200/60 dark:border-zinc-800">
                        <div
                            class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 border border-sky-500/20 text-sm">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-wider">
                                Email
                            </p>
                            <p class="text-xs font-bold text-zinc-900 dark:text-white truncate">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $isAdminOrStaff = auth()
                ->user()
                ?->hasAnyRole(['administrator', 'staff']);

            $defaultTab = $isAdminOrStaff ? 'biodata' : 'personal';

            if ($errors->has('current_password') || $errors->has('password')) {
                $defaultTab = 'keamanan';
            } elseif ($errors->has('username') || $errors->has('name')) {
                $defaultTab = 'personal';
            }
        @endphp

        <!-- ================= Right Column: Tabbed Content ================= -->
        <div class="lg:col-span-2" x-data="{ activeTab: '{{ $defaultTab }}' }">

            <!-- Tab Navigation Menu -->
            <div class="flex flex-wrap gap-2 mb-4 relative z-10">

                @if ($isAdminOrStaff)
                    <button @click="activeTab = 'biodata'"
                        :class="activeTab === 'biodata' ?
                            'bg-emerald-600 text-white border-emerald-600 shadow-2xs' :
                            'bg-white/60 dark:bg-zinc-900/60 text-zinc-600 dark:text-zinc-400 border-zinc-200/80 dark:border-zinc-800 hover:bg-white dark:hover:bg-zinc-800'"
                        class="h-9 px-4 rounded-xl text-xs font-black uppercase tracking-wider transition-all border outline-none flex items-center gap-1.5">
                        <i class="bi bi-card-heading text-xs"></i> <span>Biodata</span>
                    </button>
                @endif

                <button @click="activeTab = 'personal'"
                    :class="activeTab === 'personal' ?
                        'bg-emerald-600 text-white border-emerald-600 shadow-2xs' :
                        'bg-white/60 dark:bg-zinc-900/60 text-zinc-600 dark:text-zinc-400 border-zinc-200/80 dark:border-zinc-800 hover:bg-white dark:hover:bg-zinc-800'"
                    class="h-9 px-4 rounded-xl text-xs font-black uppercase tracking-wider transition-all border outline-none flex items-center gap-1.5">
                    <i class="bi bi-person-lines-fill text-xs"></i> <span>Data Akun</span>
                </button>

                <button @click="activeTab = 'keamanan'"
                    :class="activeTab === 'keamanan' ?
                        'bg-emerald-600 text-white border-emerald-600 shadow-2xs' :
                        'bg-white/60 dark:bg-zinc-900/60 text-zinc-600 dark:text-zinc-400 border-zinc-200/80 dark:border-zinc-800 hover:bg-white dark:hover:bg-zinc-800'"
                    class="h-9 px-4 rounded-xl text-xs font-black uppercase tracking-wider transition-all border outline-none flex items-center gap-1.5">
                    <i class="bi bi-shield-lock-fill text-xs"></i> <span>Keamanan</span>
                </button>
            </div>

            <!-- Tab Content Wrapper -->
            <div class="relative w-full">

                <!-- 1. TAB: Biodata Administrator -->
                @if ($isAdminOrStaff)
                    <div x-show="activeTab === 'biodata'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0" style="display: none;"
                        class="m3-glass-card p-5 md:p-7 relative overflow-hidden rounded-3xl shadow-2xs">

                        <div class="flex items-center gap-3 mb-6 relative z-10">
                            <div
                                class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/20">
                                <i class="bi bi-card-heading"></i>
                            </div>
                            <div>
                                <h3
                                    class="font-black text-zinc-900 dark:text-white text-base md:text-lg tracking-tight">
                                    Biodata Administrator
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    Lengkapi identitas, kontak, dan unggah foto profil.
                                </p>
                            </div>
                        </div>

                        <form method="post" action="{{ route('profile.administrator.update') }}"
                            enctype="multipart/form-data" class="space-y-4 relative z-10">
                            @csrf
                            @method('patch')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- NIK -->
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                        NIK (16 Digit)
                                    </label>
                                    <input type="text" name="nik"
                                        value="{{ old('nik', auth()->user()->administrator?->nik) }}" maxlength="16"
                                        class="m3-input-glass w-full text-xs font-bold">
                                </div>

                                <!-- Jenis Kelamin -->
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                        Jenis Kelamin
                                    </label>
                                    <div class="relative">
                                        <select name="jenis_kelamin" required
                                            class="m3-input-glass w-full appearance-none cursor-pointer text-xs font-bold">
                                            <option value="L"
                                                {{ old('jenis_kelamin', auth()->user()->administrator?->jenis_kelamin) == 'L' ? 'selected' : '' }}>
                                                Laki-laki (L)
                                            </option>
                                            <option value="P"
                                                {{ old('jenis_kelamin', auth()->user()->administrator?->jenis_kelamin) == 'P' ? 'selected' : '' }}>
                                                Perempuan (P)
                                            </option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                                            <i class="bi bi-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tempat Lahir -->
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                        Tempat Lahir
                                    </label>
                                    <input type="text" name="tempat_lahir"
                                        value="{{ old('tempat_lahir', auth()->user()->administrator?->tempat_lahir) }}"
                                        class="m3-input-glass w-full text-xs font-bold">
                                </div>

                                <!-- Tanggal Lahir -->
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                        Tanggal Lahir
                                    </label>
                                    <input type="date" name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir', auth()->user()->administrator?->tanggal_lahir?->format('Y-m-d')) }}"
                                        class="m3-input-glass w-full text-xs font-bold uppercase">
                                </div>

                                <!-- No HP -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                        Nomor HP / WhatsApp
                                    </label>
                                    <input type="text" name="no_hp"
                                        value="{{ old('no_hp', auth()->user()->administrator?->no_hp) }}"
                                        class="m3-input-glass w-full text-xs font-bold">
                                </div>

                                <!-- Alamat -->
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                        Alamat Lengkap
                                    </label>
                                    <textarea name="alamat" rows="2" class="m3-input-glass w-full resize-none text-xs font-bold">{{ old('alamat', auth()->user()->administrator?->alamat) }}</textarea>
                                </div>

                                <!-- Foto Profil -->
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                        Upload Foto Profil
                                    </label>
                                    <input type="file" name="foto" accept="image/png, image/jpeg, image/jpg"
                                        class="w-full text-xs text-zinc-500 dark:text-zinc-400 file:cursor-pointer file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-emerald-500/10 file:text-emerald-600 dark:file:text-emerald-400 hover:file:bg-emerald-500/20 transition-all outline-none">
                                </div>

                                <!-- Tanda Tangan -->
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                        Upload Tanda Tangan
                                    </label>

                                    @if (auth()->user()->administrator?->tanda_tangan)
                                        <div
                                            class="mb-2 relative w-28 h-auto rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white/40 dark:bg-black/40 p-2 flex items-center justify-center">
                                            <img src="{{ asset('storage/' . auth()->user()->administrator->tanda_tangan) }}"
                                                alt="Tanda Tangan" class="max-w-full max-h-12 object-contain">
                                        </div>
                                    @endif

                                    <input type="file" name="tanda_tangan"
                                        accept="image/png, image/jpeg, image/jpg"
                                        class="w-full text-xs text-zinc-500 dark:text-zinc-400 file:cursor-pointer file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-emerald-500/10 file:text-emerald-600 dark:file:text-emerald-400 hover:file:bg-emerald-500/20 transition-all outline-none">

                                    @if (auth()->user()->administrator?->tanda_tangan)
                                        <p class="text-[9px] font-semibold text-zinc-400 mt-1 ml-1">
                                            *Pilih file baru jika ingin mengubah.
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Submit Biodata -->
                            <div
                                class="pt-4 flex items-center gap-3 border-t border-zinc-200/80 dark:border-zinc-800 mt-4">
                                <button type="submit"
                                    class="m3-btn-primary h-10 px-5 text-xs font-black rounded-xl shadow-2xs flex items-center gap-1.5">
                                    <i class="bi bi-save2-fill text-xs"></i> <span>Simpan Biodata</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- 2. TAB: Data Personal -->
                <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;"
                    class="m3-glass-card p-5 md:p-7 relative overflow-hidden rounded-3xl shadow-2xs">

                    <div class="flex items-center gap-3 mb-6 relative z-10">
                        <div
                            class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/20">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-zinc-900 dark:text-white text-base md:text-lg tracking-tight">
                                Data Akun
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                Perbarui nama lengkap dan username akun Anda.
                            </p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4 relative z-10">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama Lengkap Akun -->
                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400">
                                        <i class="bi bi-person text-xs"></i>
                                    </div>
                                    <input type="text" name="name"
                                        value="{{ old('name', auth()->user()->name) }}" required
                                        class="m3-input-glass w-full !pl-9 text-xs font-bold">
                                </div>
                            </div>

                            <!-- Username -->
                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Username <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400">
                                        <i class="bi bi-at text-xs"></i>
                                    </div>
                                    <input type="text" name="username"
                                        value="{{ old('username', auth()->user()->username) }}" required
                                        class="m3-input-glass w-full !pl-9 text-xs font-bold">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Personal -->
                        <div
                            class="pt-4 flex items-center gap-3 border-t border-zinc-200/80 dark:border-zinc-800 mt-4">
                            <button type="submit"
                                class="m3-btn-primary h-10 px-5 text-xs font-black rounded-xl shadow-2xs flex items-center gap-1.5">
                                <i class="bi bi-save2-fill text-xs"></i> <span>Simpan Data Akun</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 3. TAB: Keamanan Akun -->
                <div x-show="activeTab === 'keamanan'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" style="display: none;"
                    class="m3-glass-card p-5 md:p-7 relative overflow-hidden rounded-3xl shadow-2xs">

                    <div class="flex items-center gap-3 mb-6 relative z-10">
                        <div
                            class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/20">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-zinc-900 dark:text-white text-base md:text-lg tracking-tight">
                                Keamanan Akun
                            </h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                Pastikan Anda menggunakan kata sandi yang kuat dan unik.
                            </p>
                        </div>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-4 relative z-10">
                        @csrf
                        @method('put')

                        <!-- Sandi Saat Ini -->
                        <div>
                            <label
                                class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                Sandi Saat Ini <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative max-w-md">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400">
                                    <i class="bi bi-key text-xs"></i>
                                </div>
                                <input type="password" id="current_password" name="current_password" required
                                    placeholder="••••••••"
                                    class="m3-input-glass w-full !pl-9 !pr-10 text-xs font-bold">
                                <button type="button" onclick="togglePassword('current_password', this)"
                                    class="absolute inset-y-0 right-1 w-8 flex items-center justify-center text-zinc-400 hover:text-emerald-600 transition-colors outline-none">
                                    <i class="bi bi-eye-fill text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Sandi Baru & Konfirmasi -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-zinc-200/80 dark:border-zinc-800">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Sandi Baru <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" required
                                        placeholder="••••••••" class="m3-input-glass w-full !pr-10 text-xs font-bold">
                                    <button type="button" onclick="togglePassword('password', this)"
                                        class="absolute inset-y-0 right-1 w-8 flex items-center justify-center text-zinc-400 hover:text-emerald-600 transition-colors outline-none">
                                        <i class="bi bi-eye-fill text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                                    Konfirmasi Sandi <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        required placeholder="••••••••"
                                        class="m3-input-glass w-full !pr-10 text-xs font-bold">
                                    <button type="button" onclick="togglePassword('password_confirmation', this)"
                                        class="absolute inset-y-0 right-1 w-8 flex items-center justify-center text-zinc-400 hover:text-emerald-600 transition-colors outline-none">
                                        <i class="bi bi-eye-fill text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Keamanan -->
                        <div
                            class="pt-4 flex items-center gap-3 border-t border-zinc-200/80 dark:border-zinc-800 mt-4">
                            <button type="submit"
                                class="m3-btn-primary h-10 px-5 text-xs font-black rounded-xl shadow-2xs flex items-center gap-1.5">
                                <i class="bi bi-key-fill text-xs"></i> <span>Ubah Kata Sandi</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    @push('script')
        <script>
            function togglePassword(inputId, button) {
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye-fill');
                    icon.classList.add('bi-eye-slash-fill');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash-fill');
                    icon.classList.add('bi-eye-fill');
                }
            }
        </script>
    @endpush

</x-app-layout>
