@php
    $isEdit = isset($user) && $user->exists;
    $actionUrl = $isEdit ? route('pengguna.update', $user->id) : route('pengguna.store');
    $userRole = $isEdit ? $user->roles->first()->name ?? '' : 'staff';
@endphp

@section('title', $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna Baru')

<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('pengguna.index') }}"
                    class="text-xs font-black text-primary dark:text-primary-dark hover:underline flex items-center gap-1.5 mb-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pengguna
                </a>
                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ $isEdit ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru' }}
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium mt-0.5">
                    {{ $isEdit ? 'Perbarui informasi akun dan wewenang pengguna sistem' : 'Daftarkan akun pengguna baru ke dalam sistem madrasah' }}
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div
            class="m3-glass-card p-6 md:p-8 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-xl bg-white dark:bg-zinc-900">
            <form action="{{ $actionUrl }}" method="POST" class="space-y-4">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                @if (isset($errors) && $errors->any())
                    <div
                        class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold space-y-1">
                        @foreach ($errors->all() as $error)
                            <div>• {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- 1. Nama Lengkap -->
                <div>
                    <label
                        class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-person-badge text-sm"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                            placeholder="Contoh: UST. AHMAD FAUZI"
                            class="m3-input-glass w-full !pl-10 text-xs font-bold uppercase tracking-wider">
                    </div>
                </div>

                <!-- 2. Username & Email (Grid 2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label
                            class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                            Username <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 font-mono text-xs">
                                @
                            </div>
                            <input type="text" name="username" value="{{ old('username', $user->username ?? '') }}"
                                required placeholder="ahmad_fauzi"
                                class="m3-input-glass w-full !pl-9 text-xs font-bold font-mono lowercase">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                            Alamat Email <span class="text-zinc-400 text-[10px] font-normal">(Opsional)</span>
                        </label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                                <i class="bi bi-envelope text-xs"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                                placeholder="ahmad@hidas.sch.id"
                                class="m3-input-glass w-full !pl-10 text-xs font-bold font-mono">
                        </div>
                    </div>
                </div>

                <!-- 3. Peran / Role (RBAC) -->
                <div>
                    <label
                        class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                        Peran Pengguna (Role RBAC) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-shield-lock text-sm"></i>
                        </div>
                        <select name="role" id="roleSelect" required onchange="handleRoleChange(this.value)"
                            class="m3-input-glass w-full !pl-10 text-xs font-black uppercase tracking-wider cursor-pointer">
                            <option value="">-- Pilih Peran / Role --</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->name }}"
                                    {{ old('role', $userRole) === $r->name ? 'selected' : '' }}>
                                    {{ strtoupper($r->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 4. Penugasan Tingkat (Kondisional) -->
                <div id="tingkatContainer"
                    class="{{ in_array(old('role', $userRole), ['administrator', 'petugas-tabungan', 'bendahara']) ? 'hidden' : '' }}">
                    <label
                        class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">
                        Penugasan Tingkat Madrasah
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-layers text-sm"></i>
                        </div>
                        <select name="tingkat_id" id="tingkatSelect"
                            class="m3-input-glass w-full !pl-10 text-xs font-bold cursor-pointer">
                            <option value="">-- Semua Tingkat (Global) --</option>
                            @foreach ($tingkats as $t)
                                <option value="{{ $t->id }}"
                                    {{ old('tingkat_id', $user->tingkat_id ?? '') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nama_tingkat }} ({{ $t->kode_tingkat }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 5. Password Section -->
                <div
                    class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/60 dark:border-zinc-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span
                            class="text-xs font-black text-zinc-800 dark:text-zinc-200 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="bi bi-key-fill text-amber-500"></i>
                            {{ $isEdit ? 'Ubah Password (Opsional)' : 'Password Akun' }}
                        </span>
                        <button type="button" onclick="generateRandomPassword()"
                            class="text-[11px] font-black text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                            <i class="bi bi-shuffle"></i> Acak Password
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <input type="password" name="password" id="inputPassword" {{ $isEdit ? '' : 'required' }}
                                placeholder="{{ $isEdit ? 'Kosongkan jika tidak diubah' : 'Password minimal 6 digit' }}"
                                class="m3-input-glass w-full text-xs font-mono">
                        </div>
                        <div>
                            <input type="password" name="password_confirmation" id="inputPasswordConfirm"
                                {{ $isEdit ? '' : 'required' }} placeholder="Ulangi password"
                                class="m3-input-glass w-full text-xs font-mono">
                        </div>
                    </div>
                </div>

                <!-- 6. Status Aktif Toggle -->
                <div
                    class="flex items-center justify-between p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/60 dark:border-zinc-800">
                    <div>
                        <h4 class="text-xs font-black text-zinc-900 dark:text-white">Status Akun Aktif</h4>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Pengguna nonaktif tidak akan dapat login
                            ke sistem</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-emerald-500">
                        </div>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div
                    class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-end gap-2.5">
                    <a href="{{ route('pengguna.index') }}"
                        class="h-10 px-5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-black transition-all flex items-center">
                        Batal
                    </a>
                    <button type="submit"
                        class="m3-btn-primary h-10 px-6 text-xs font-black shadow-md flex items-center gap-1.5">
                        <i class="bi {{ $isEdit ? 'bi-check2-circle' : 'bi-save-fill' }}"></i>
                        <span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pengguna' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleRoleChange(role) {
            const tingkatContainer = document.getElementById('tingkatContainer');
            const tingkatSelect = document.getElementById('tingkatSelect');
            if (!tingkatContainer) return;

            if (['administrator', 'petugas-tabungan', 'bendahara'].includes(role)) {
                tingkatContainer.classList.add('hidden');
                if (tingkatSelect) tingkatSelect.value = '';
            } else {
                tingkatContainer.classList.remove('hidden');
            }
        }

        function generateRandomPassword() {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
            let pass = '';
            for (let i = 0; i < 8; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('inputPassword').value = pass;
            document.getElementById('inputPasswordConfirm').value = pass;
        }
    </script>
</x-app-layout>
