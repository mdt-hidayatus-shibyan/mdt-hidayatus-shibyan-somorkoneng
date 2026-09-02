@section('title', 'Atur Ulang Password')

<x-auth-layout>

    <!-- Title Section -->
    <div class="text-center mb-6 relative z-10">
        <h2
            class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-900 dark:text-white mb-1.5 transition-colors duration-300">
            Atur Ulang Sandi
        </h2>
        <p
            class="text-zinc-500 dark:text-zinc-400 text-xs sm:text-[13px] font-semibold leading-relaxed transition-colors duration-300">
            Silakan buat kata sandi baru yang kuat untuk akun Anda.
        </p>
    </div>

    <!-- Form Section -->
    <form class="space-y-4 relative z-10" method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Hidden Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Field (Readonly) -->
        <div class="space-y-1.5">
            <label for="email"
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Email
            </label>
            <input
                class="m3-input-glass w-full opacity-70 cursor-not-allowed {{ $errors->has('email') ? '!border-red-500 !ring-red-500/20' : '' }}"
                id="email" type="email" name="email" value="{{ old('email', $request->email) }}" readonly
                autofocus>

            @error('email')
                <div class="text-red-500 dark:text-rose-400 text-[11px] font-bold px-1 mt-1 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- New Password Field -->
        <div class="space-y-1.5">
            <label for="password"
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Kata Sandi Baru
            </label>
            <div class="relative flex items-center">
                <input
                    class="m3-input-glass w-full pr-16 {{ $errors->has('password') ? '!border-red-500 !ring-red-500/20' : '' }}"
                    type="password" id="password" name="password" autocomplete="new-password" placeholder="••••••••">

                <button
                    class="absolute right-1.5 min-h-[32px] px-2.5 py-1 bg-zinc-100/80 hover:bg-zinc-200/80 dark:bg-zinc-800/80 dark:hover:bg-zinc-700/80 text-zinc-600 dark:text-zinc-300 rounded-lg text-[10px] font-extrabold tracking-wider transition-colors outline-none select-none"
                    type="button" id="togglePassword">
                    LIHAT
                </button>
            </div>
            @error('password')
                <div class="text-red-500 dark:text-rose-400 text-[11px] font-bold px-1 mt-1 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Confirm Password Field -->
        <div class="space-y-1.5">
            <label for="password_confirmation"
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Ulangi Kata Sandi
            </label>
            <div class="relative flex items-center">
                <input
                    class="m3-input-glass w-full pr-16 {{ $errors->has('password_confirmation') ? '!border-red-500 !ring-red-500/20' : '' }}"
                    type="password" id="password_confirmation" name="password_confirmation"
                    autocomplete="new-password_confirmation" placeholder="••••••••">

                <button
                    class="absolute right-1.5 min-h-[32px] px-2.5 py-1 bg-zinc-100/80 hover:bg-zinc-200/80 dark:bg-zinc-800/80 dark:hover:bg-zinc-700/80 text-zinc-600 dark:text-zinc-300 rounded-lg text-[10px] font-extrabold tracking-wider transition-colors outline-none select-none"
                    type="button" id="togglePasswordConfirm">
                    LIHAT
                </button>
            </div>
            @error('password_confirmation')
                <div class="text-red-500 dark:text-rose-400 text-[11px] font-bold px-1 mt-1 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-1">
            <button class="m3-btn-primary w-full !py-3 !text-[14px]" type="submit">
                <span>Simpan Kata Sandi Baru</span>
                <i class="bi bi-shield-lock-fill text-sm"></i>
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            // Logika Toggle Password (Baru)
            document.getElementById('togglePassword').addEventListener('click', function(e) {
                const passwordInput = document.getElementById('password');
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? 'LIHAT' : 'TUTUP';
            });

            // Logika Toggle Password (Konfirmasi)
            document.getElementById('togglePasswordConfirm').addEventListener('click', function(e) {
                const passwordConfirmInput = document.getElementById('password_confirmation');
                const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmInput.setAttribute('type', type);
                this.textContent = type === 'password' ? 'LIHAT' : 'TUTUP';
            });
        </script>
    @endpush

</x-auth-layout>
