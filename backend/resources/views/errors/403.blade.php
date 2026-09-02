@section('title', 'Akses Ditolak!')
<x-app-layout>
    <div class="min-h-[80vh] w-full flex items-center justify-center p-4 relative z-10">

        <!-- Main Card M3 Glass -->
        <div
            class="m3-glass-card !border-rose-500/30 w-full max-w-md p-6 md:p-8 text-center relative overflow-hidden rounded-3xl shadow-2xs">

            <div class="relative z-10">

                <!-- Alert Icon -->
                <div
                    class="w-16 h-16 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-500/20 text-3xl shadow-2xs">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>

                <!-- Title -->
                <h1 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight mb-2">
                    Akses Ditolak!
                </h1>

                <!-- Description -->
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-6 leading-relaxed px-2">
                    Maaf, Anda tidak memiliki hak akses (permission) untuk melihat atau memodifikasi modul ini. Silakan
                    hubungi Administrator jika Anda merasa ini adalah kesalahan.
                </p>

                <!-- Actions -->
                <div class="flex flex-col gap-2.5">
                    <button onclick="window.history.back()"
                        class="m3-btn-primary w-full h-10 rounded-xl text-xs font-black shadow-2xs flex items-center justify-center gap-2">
                        <i class="bi bi-arrow-left text-sm"></i> <span>Kembali ke Halaman Sebelumnya</span>
                    </button>

                    <a href="{{ route('dashboard') }}"
                        class="w-full h-10 bg-white/60 dark:bg-zinc-800/60 hover:bg-white dark:hover:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-2 shadow-2xs outline-none">
                        <i class="bi bi-grid-1x2-fill text-xs"></i> <span>Ke Dashboard Utama</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
