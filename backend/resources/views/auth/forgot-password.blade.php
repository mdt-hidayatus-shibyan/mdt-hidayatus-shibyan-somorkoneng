@section('title', 'Lupa Password')

<x-auth-layout>

    <!-- Title Section -->
    <div class="text-center mb-6 relative z-10">
        <h2
            class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-900 dark:text-white mb-1.5 transition-colors duration-300">
            Lupa Password
        </h2>
        <p
            class="text-zinc-500 dark:text-zinc-400 text-xs sm:text-[13px] font-semibold leading-relaxed transition-colors duration-300">
            Masukkan email Anda untuk menerima tautan pemulihan kata sandi.
        </p>
    </div>

    <!-- Session Status Alert -->
    @if (session('status'))
        <div class="mb-4 p-3.5 bg-emerald-50/80 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 rounded-2xl text-[12px] font-bold border border-emerald-200/80 dark:border-emerald-800/40 flex items-start gap-2.5 relative z-10 transition-colors duration-300"
            role="alert">
            <i class="bi bi-check-circle-fill text-emerald-600 dark:text-emerald-400 text-base shrink-0 mt-0.5"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Form Section -->
    <form class="space-y-4 relative z-10" method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Field -->
        <div class="space-y-1.5">
            <label for="email"
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Email Terdaftar
            </label>
            <input class="m3-input-glass w-full {{ $errors->has('email') ? '!border-red-500 !ring-red-500/20' : '' }}"
                id="email" type="email" name="email" value="{{ old('email') }}" autofocus
                placeholder="nama@email.com">

            @error('email')
                <div class="text-red-500 dark:text-rose-400 text-[11px] font-bold px-1 mt-1 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-1">
            <button class="m3-btn-primary w-full !py-3 !text-[14px]" type="submit">
                <span>Kirim Tautan Reset</span>
                <i class="bi bi-send-fill text-sm"></i>
            </button>
        </div>
    </form>

</x-auth-layout>
