<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.head')
    <link href="{{ asset('assets/css/custome-style.css') }}" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body
    class="min-h-screen bg-zinc-50 dark:bg-black text-zinc-900 dark:text-zinc-100 font-sans antialiased selection:bg-primary/20 selection:text-primary dark:selection:bg-primary-dark/30 dark:selection:text-primary-dark transition-colors duration-300 relative overflow-x-hidden overflow-y-auto">

    <!-- Global Ambient Blurs (M3 Organic Glow - OLED Background) -->
    <div
        class="fixed top-[-10%] right-[-10%] w-[50vw] h-[50vw] max-w-[500px] max-h-[500px] bg-primary/10 dark:bg-primary-dark/10 rounded-full blur-[100px] pointer-events-none transition-colors duration-500 z-0 animate-blob">
    </div>
    <div class="fixed bottom-[-10%] left-[-10%] w-[50vw] h-[50vw] max-w-[500px] max-h-[500px] bg-primary-container/20 dark:bg-primary/10 rounded-full blur-[100px] pointer-events-none transition-colors duration-500 z-0 animate-blob"
        style="animation-delay: 3s;">
    </div>

    <div
        class="min-h-screen w-full flex flex-col justify-center items-center py-6 sm:py-10 px-3 sm:px-6 lg:px-8 relative z-10">

        <!-- Content Slot Container -->
        <div class="w-full {{ $maxWidth ?? 'max-w-[440px]' }} transition-all duration-300">
            <!-- Main Auth Glass Card -->
            <div
                class="m3-glass-card w-full mb-2 !p-6 sm:!p-8 !rounded-[2rem] sm:!rounded-[2.5rem] shadow-xl dark:shadow-none relative overflow-hidden transition-all duration-300">

                <!-- Decorative M3 Corner Accents -->
                <div
                    class="absolute -top-12 -right-12 w-40 h-40 bg-primary/10 dark:bg-primary-dark/10 rounded-full blur-[30px] pointer-events-none transition-colors duration-300">
                </div>
                <div
                    class="absolute -bottom-12 -left-12 w-40 h-40 bg-primary/10 dark:bg-primary-dark/10 rounded-full blur-[30px] pointer-events-none transition-colors duration-300">
                </div>

                <!-- Brand/Logo Section -->
                <div class="flex justify-center items-center mb-6 relative z-10">
                    <a href="#"
                        class="min-h-[48px] min-w-[48px] flex items-center justify-center transition-transform duration-300 hover:scale-105 active:scale-95 outline-none"
                        aria-label="Kembali ke Beranda">
                        <img src="{{ asset('assets/LOGO MDT.png') }}" alt="Logo MDT Hidayatus Shibyan"
                            class="h-16 sm:h-20 w-auto drop-shadow-sm dark:drop-shadow-none">
                    </a>
                </div>

                <!-- Form Container (Slot) -->
                <div class="relative z-10">
                    {{ $slot }}
                </div>

            </div>



        </div>
    </div>

    @stack('scripts')
    {{-- @stack('script') --}}

</body>

</html>
