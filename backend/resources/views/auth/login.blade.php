@section('title', 'Login')

<x-auth-layout>
    <!-- Title Section -->
    <div class="text-center mb-6 relative z-10">
        <h2
            class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-900 dark:text-white mb-1.5 transition-colors duration-300">
            Selamat Datang
        </h2>
        <p class="text-zinc-500 dark:text-zinc-400 text-xs sm:text-[13px] font-semibold transition-colors duration-300">
            Masukkan kredensial akun Anda untuk masuk
        </p>
    </div>

    <!-- Form Section -->
    <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-4 relative z-10">
        @csrf

        <!-- Username / Email Field -->
        <div class="space-y-1.5">
            <label for="login"
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Username / Email
            </label>
            <div class="relative">
                <input
                    class="m3-input-glass w-full {{ $errors->has('login') ? '!border-red-500 !ring-red-500/20' : '' }}"
                    id="login" name="login" value="{{ old('login') }}" placeholder="Ketik email atau username"
                    autofocus autocomplete="username">
            </div>
            @error('login')
                <div class="text-red-500 dark:text-rose-400 text-[11px] font-bold px-1 mt-1 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password Field -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between ml-1 pr-1">
                <label for="password"
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] font-bold text-primary dark:text-primary-dark hover:underline transition-colors outline-none"
                        href="{{ route('password.request') }}">
                        Lupa Password?
                    </a>
                @endif
            </div>

            <div class="relative flex items-center">
                <input
                    class="m3-input-glass w-full pr-16 {{ $errors->has('password') ? '!border-red-500 !ring-red-500/20' : '' }}"
                    type="password" name="password" id="password" placeholder="Masukkan password"
                    autocomplete="current-password">
                <!-- Toggle Password Button -->
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

        <!-- Remember Me -->
        <div class="flex items-center gap-3 pt-1 pb-2 ml-1">
            <!-- Checkbox M3 Style -->
            <label class="relative inline-flex items-center cursor-pointer group select-none min-h-[32px]">
                <div class="relative flex items-center justify-center">
                    <input type="checkbox" name="remember" id="checkbox-signin" class="peer sr-only" checked>
                    <div
                        class="w-4 h-4 rounded-md border-2 border-zinc-300 dark:border-zinc-700 peer-checked:bg-primary dark:peer-checked:bg-primary-dark peer-checked:border-primary dark:peer-checked:border-primary-dark transition-all duration-200 bg-white dark:bg-zinc-950">
                    </div>
                    <i
                        class="bi bi-check-lg absolute text-white dark:text-zinc-950 opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 text-xs font-black transition-all duration-200 pointer-events-none"></i>
                </div>
                <span
                    class="ml-2.5 text-[12px] font-bold text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">
                    Ingat Saya
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-1">
            <button class="m3-btn-primary w-full !py-3 !text-[14px]" type="submit">
                <span>Masuk ke Akun</span>
                <i class="bi bi-box-arrow-in-right text-base font-bold"></i>
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('togglePassword').addEventListener('click', function(e) {
                const passwordInput = document.getElementById('password');
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? 'LIHAT' : 'TUTUP';
            });
        </script>
    @endpush

</x-auth-layout>
