<header
    class="flex-shrink-0 bg-white/75 dark:bg-black/80 backdrop-blur-xl p-2 md:py-2.5 md:px-4 flex justify-between items-center sticky top-0 z-40 border-b border-zinc-200/80 dark:border-zinc-800/80 transition-colors duration-300 gap-2 md:gap-3">

    <!-- BAGIAN KIRI: Toggle Sidebar & Tanggal Hari Ini -->
    <div class="flex items-center gap-2 md:gap-3 flex-grow shrink-0">

        <!-- Toggle Sidebar (Utama) -->
        <button type="button" onclick="toggleSidebar()" aria-label="Buka/Tutup Sidebar"
            class="min-w-[40px] min-h-[40px] w-10 h-10 rounded-2xl bg-zinc-100/80 hover:bg-zinc-200/80 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center flex-shrink-0 active:scale-95 transition-all outline-none border border-zinc-200/50 dark:border-zinc-800/50">
            <i class="bi bi-list text-lg" aria-hidden="true"></i>
        </button>

        @php
            $dateInfo = getTodayDateInfo();
        @endphp

        <!-- Tanggal Masehi & Hijriyah (Desktop & Tablet) -->
        <div
            class="hidden sm:flex items-center gap-2.5 px-3 py-1 rounded-2xl bg-zinc-100/70 dark:bg-zinc-900/70 border border-zinc-200/60 dark:border-zinc-800/60 text-zinc-800 dark:text-zinc-200 transition-colors">
            <div
                class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-sm shrink-0">
                <i class="bi bi-calendar2-event-fill"></i>
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-xs font-black text-zinc-900 dark:text-white leading-tight truncate">
                    {{ $dateInfo['masehi'] }}
                </span>
                @if ($dateInfo['hijri'])
                    <span
                        class="text-xs font-bold text-emerald-600 dark:text-emerald-400 leading-tight mt-0.5 truncate flex items-center gap-1">
                        <i class="bi bi-moon-stars-fill text-[8px]"></i>
                        {{ $dateInfo['hijri'] }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Tombol Tanggal Dropdown (Khusus Mode HP / Mobile) -->
        <div class="relative sm:hidden" x-data="{ openDate: false }">
            <button type="button" @click="openDate = !openDate" @click.outside="openDate = false"
                title="Tanggal Hari Ini" aria-label="Lihat Tanggal Hari Ini"
                class="min-w-[40px] min-h-[40px] w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 active:scale-95 transition-all outline-none border border-zinc-200/50 dark:border-zinc-800/50"
                :class="openDate
                    ?
                    'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' :
                    'bg-zinc-100/80 hover:bg-zinc-200/80 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300'">
                <i class="bi bi-calendar-event text-sm"></i>
            </button>

            <!-- Panel Dropdown Tanggal Mobile -->
            <div x-show="openDate" x-cloak style="display: none;" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="m3-dropdown-menu left-0 top-full mt-2 w-[240px] !p-3 overflow-hidden origin-top-left z-[9999] shadow-2xl">

                <div class="flex items-center gap-2.5 mb-2 pb-2 border-b border-zinc-100 dark:border-zinc-800/80">
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-sm shrink-0">
                        <i class="bi bi-calendar2-week"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest leading-none">Masehi</p>
                        <p class="text-xs font-black text-zinc-900 dark:text-white leading-tight mt-1">
                            {{ $dateInfo['masehi'] }}</p>
                    </div>
                </div>

                @if ($dateInfo['hijri'])
                    <div
                        class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center gap-2 text-emerald-700 dark:text-emerald-400">
                        <i class="bi bi-moon-stars-fill text-xs shrink-0"></i>
                        <div class="flex flex-col min-w-0 flex-1">
                            <span
                                class="text-[12px] font-bold text-emerald-600/80 dark:text-emerald-400/80 uppercase tracking-wider leading-none">Hijriyah</span>
                            <span
                                class="text-xs font-black leading-tight mt-0.5 truncate">{{ $dateInfo['hijri'] }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- BAGIAN KANAN: Tools (Pencarian, Notif, Fullscreen, Darkmode) -->
    <div class="flex items-center gap-1.5 md:gap-2 shrink-0 justify-end">

        <!-- Search Bar -->
        <div class="relative w-full max-w-[140px] sm:max-w-[200px] lg:max-w-xs group shrink">
            <div
                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400 group-focus-within:text-primary dark:group-focus-within:text-primary-dark transition-colors">
                <i class="bi bi-search text-xs md:text-sm"></i>
            </div>
            <input type="text" id="menuSearchInput" onkeyup="searchMenu()" placeholder="Cari menu..."
                autocomplete="off"
                class="m3-input-glass w-full !pl-8 md:!pl-9 !pr-3 md:!pr-4 !py-1.5 !min-h-[40px] !text-[12px] md:!text-[13px] font-semibold !rounded-2xl">

            <div id="searchMenuDropdown"
                class="m3-glass-card absolute right-0 top-full mt-2 w-[260px] lg:w-full hidden z-[9999] max-h-64 overflow-y-auto flex-col divide-y divide-zinc-100 dark:divide-zinc-800/60 p-1">
            </div>
        </div>

        <!-- Notifikasi -->
        <div class="relative sm:block" x-data="{ openNotif: false }">

            <!-- Tombol Pemicu -->
            <button type="button" @click="openNotif = !openNotif" @click.outside="openNotif = false" title="Notifikasi"
                aria-label="Lihat Notifikasi"
                class="min-w-[40px] min-h-[40px] w-10 h-10 rounded-2xl text-zinc-700 dark:text-zinc-300 flex items-center justify-center flex-shrink-0 active:scale-95 transition-all outline-none relative border border-zinc-200/50 dark:border-zinc-800/50"
                :class="openNotif
                    ?
                    'bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark border-primary/30' :
                    'bg-zinc-100/80 hover:bg-zinc-200/80 dark:bg-zinc-900/80 dark:hover:bg-zinc-800'">
                <i class="bi bi-bell-fill text-sm"></i>

                <!-- Dot Indikator Merah (Tampil HANYA jika ada notif belum dibaca) -->
                @if (auth()->user()->unreadNotifications->count() > 0)
                    <span
                        class="absolute top-2 right-2.5 w-2 h-2 bg-rose-500 border-2 border-white dark:border-black rounded-full animate-pulse"></span>
                @endif
            </button>

            <!-- Panel Dropdown Notifikasi -->
            <div x-show="openNotif" x-cloak style="display: none;" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="m3-dropdown-menu right-0 top-full mt-2 w-[320px] sm:w-[360px] !p-0 overflow-hidden origin-top-right">

                <!-- Header Notifikasi -->
                <div
                    class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800/80 flex justify-between items-center bg-zinc-50/70 dark:bg-zinc-900/50">
                    <h3 class="text-[13px] font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        Notifikasi
                        @if (auth()->user()->unreadNotifications->count() > 0)
                            <span
                                class="bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400 py-0.5 px-2 rounded-full text-[9px] font-extrabold">
                                {{ auth()->user()->unreadNotifications->count() }} Baru
                            </span>
                        @endif
                    </h3>

                    <!-- Tombol Tandai Semua Dibaca -->
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifikasi.markAllRead') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit"
                                class="text-[11px] font-bold text-primary dark:text-primary-dark hover:underline transition-colors outline-none">
                                Tandai semua dibaca
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Daftar Notifikasi (Bisa di-scroll) -->
                <div
                    class="max-h-[340px] overflow-y-auto custom-scrollbar flex flex-col divide-y divide-zinc-100 dark:divide-zinc-800/60">

                    @forelse(auth()->user()->notifications->take(5) as $notification)
                        @php
                            $isUnread = $notification->unread();
                        @endphp

                        <a href="{{ route('notifikasi.show', $notification->id) }}"
                            class="p-3.5 flex gap-3 hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors {{ $isUnread ? 'bg-primary/5 dark:bg-primary-dark/5 group relative' : 'group' }}">

                            <div
                                class="w-9 h-9 rounded-xl {{ $isUnread ? 'bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400' }} flex items-center justify-center shrink-0 mt-0.5 font-bold">
                                <i class="bi bi-info-circle-fill text-sm"></i>
                            </div>

                            <div class="pr-4 flex-1 min-w-0">
                                <p
                                    class="text-[12px] font-bold {{ $isUnread ? 'text-zinc-900 dark:text-white' : 'text-zinc-700 dark:text-zinc-300' }} leading-snug truncate">
                                    {{ $notification->data['title'] ?? 'Pengumuman' }}
                                </p>
                                <p
                                    class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 line-clamp-2 leading-relaxed">
                                    {{ $notification->data['body'] ?? '' }}
                                </p>
                                <p
                                    class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 mt-1 flex items-center gap-1">
                                    <i class="bi bi-clock text-[9px]"></i>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <!-- Titik penanda belum dibaca -->
                            @if ($isUnread)
                                <div class="w-2 h-2 bg-primary dark:bg-primary-dark rounded-full shrink-0 self-center">
                                </div>
                            @endif
                        </a>
                    @empty
                        <!-- Tampilan jika tidak ada notifikasi -->
                        <div class="p-8 text-center flex flex-col items-center justify-center">
                            <div
                                class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800/50 flex items-center justify-center mb-2.5 text-zinc-400">
                                <i class="bi bi-bell-slash text-xl"></i>
                            </div>
                            <p class="text-[12px] font-bold text-zinc-700 dark:text-zinc-300">Belum ada notifikasi</p>
                            <p class="text-[11px] text-zinc-500 mt-0.5">Semua pemberitahuan sudah dibaca.</p>
                        </div>
                    @endforelse

                </div>

                <!-- Footer / Link ke semua notifikasi -->
                <a href="#"
                    class="p-2.5 text-center text-[11px] font-bold text-zinc-500 hover:text-primary dark:hover:text-primary-dark bg-zinc-50/70 dark:bg-zinc-900/50 border-t border-zinc-100 dark:border-zinc-800/80 transition-colors block">
                    Lihat Semua Notifikasi
                </a>
            </div>
        </div>

        <!-- Fullscreen -->
        <button type="button" onclick="toggleFullscreen()" id="btnFullscreen" title="Layar Penuh"
            aria-label="Aktifkan mode layar penuh"
            class="hidden sm:flex min-w-[40px] min-h-[40px] w-10 h-10 rounded-2xl bg-zinc-100/80 hover:bg-zinc-200/80 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 items-center justify-center flex-shrink-0 active:scale-95 transition-all outline-none border border-zinc-200/50 dark:border-zinc-800/50">
            <i id="iconFullscreen" class="bi bi-arrows-fullscreen text-xs" aria-hidden="true"></i>
        </button>

        <!-- Dark Mode -->
        <button type="button" onclick="toggleDarkMode()" id="btnDarkMode" title="Mode Gelap"
            aria-label="Aktifkan atau matikan mode gelap"
            class="min-w-[40px] min-h-[40px] w-10 h-10 rounded-2xl bg-zinc-100/80 hover:bg-zinc-200/80 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center flex-shrink-0 active:scale-95 transition-all outline-none border border-zinc-200/50 dark:border-zinc-800/50">
            <i id="iconDarkMode" class="bi bi-moon-fill text-sm" aria-hidden="true"></i>
        </button>

        <div class="h-6 w-px bg-zinc-200 dark:border-zinc-800 mx-0.5 hidden sm:block"></div>

        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ openProfile: false }">
            <!-- Trigger Button -->
            <button type="button" @click="openProfile = !openProfile" @click.outside="openProfile = false"
                class="min-h-[40px] h-10 pl-1 pr-2 md:pr-3 rounded-2xl flex items-center gap-2 bg-zinc-100/80 hover:bg-zinc-200/80 dark:bg-zinc-900/80 dark:hover:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-800/50 transition-all active:scale-95 outline-none group"
                :class="openProfile ? 'ring-2 ring-emerald-500/30 border-emerald-500/50' : ''"
                aria-label="Menu Pengguna">

                <x-avatar :src="auth()->user()?->administrator?->foto
                    ? asset('storage/' . auth()->user()->administrator->foto)
                    : null" :name="auth()->user()->name ?? 'Administrator'" size="sm" shape="squircle" />

                <div class="hidden md:flex flex-col text-left">
                    <span
                        class="text-xs font-bold text-zinc-900 dark:text-white leading-tight truncate max-w-[110px] lg:max-w-[140px]">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </span>
                    <span
                        class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider leading-tight mt-0.5">
                        @if (session('is_waliruangan'))
                            WR ({{ session('wali_ruangan') }})
                        @else
                            {{ session('akses_sebagai') ?? (auth()->user()?->roles->first()->name ?? 'Admin') }}
                        @endif
                    </span>
                </div>

                <i class="bi bi-chevron-down text-[10px] text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-200 transition-transform duration-200"
                    :class="openProfile ? 'rotate-180 text-emerald-500 dark:text-emerald-400' : ''"></i>
            </button>

            <!-- Dropdown Menu Panel -->
            <div x-show="openProfile" x-cloak style="display: none;"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="m3-dropdown-menu right-0 top-full mt-2 w-[270px] sm:w-[290px] !p-2 overflow-hidden origin-top-right z-[9999] shadow-2xl">

                <!-- Header Info Akun di dalam Dropdown -->
                <div
                    class="p-3 bg-zinc-50/80 dark:bg-black/50 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80 flex items-center gap-3 mb-2">
                    <x-avatar :src="auth()->user()?->administrator?->foto
                        ? asset('storage/' . auth()->user()->administrator->foto)
                        : null" :name="auth()->user()->name ?? 'Administrator'" size="md" shape="squircle" />

                    <div class="overflow-hidden flex flex-col justify-center flex-1 min-w-0">
                        <div class="text-xs font-black text-zinc-900 dark:text-white truncate tracking-tight">
                            {{ auth()->user()->name ?? 'Administrator' }}
                        </div>
                        <div class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 truncate uppercase tracking-wider mt-0.5"
                            id="userRoleDisplay">
                            @if (session('is_waliruangan'))
                                WR ({{ session('wali_ruangan') }})
                            @else
                                {{ session('akses_sebagai') ?? (auth()->user()?->roles->first()->name ?? 'Administrator') }}
                                {{ auth()->user()?->tingkat->kode_tingkat ?? '' }}
                            @endif
                        </div>
                        <div class="text-[10px] font-semibold text-zinc-400 dark:text-zinc-500 truncate mt-0.5">
                            {{ auth()->user()->email ?? auth()->user()->username }}
                        </div>
                    </div>
                </div>

                <div class="space-y-1">
                    <!-- Edit Profil -->
                    <a href="{{ route('profile.edit') }}"
                        class="m3-dropdown-item !py-2 !px-2.5 hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400 group">
                        <div
                            class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 group-hover:bg-emerald-500/20 flex items-center justify-center text-xs text-zinc-500 dark:text-zinc-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors shrink-0">
                            <i class="bi bi-person-gear"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold leading-none">Pengaturan Profil</p>
                            <p class="text-[10px] text-zinc-400 font-medium leading-none mt-1">Biodata & kata sandi</p>
                        </div>
                    </a>

                    <!-- Backup Database (Superadmin only) -->
                    @role('administrator')
                        <a href="{{ route('backup.database') }}"
                            class="m3-dropdown-item !py-2 !px-2.5 hover:bg-sky-500/10 hover:text-sky-600 dark:hover:text-sky-400 group">
                            <div
                                class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 group-hover:bg-sky-500/20 flex items-center justify-center text-xs text-zinc-500 dark:text-zinc-400 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors shrink-0">
                                <i class="bi bi-database-down"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold leading-none">Backup Database</p>
                                <p class="text-[10px] text-zinc-400 font-medium leading-none mt-1">Cadangkan data sistem
                                </p>
                            </div>
                        </a>
                    @endrole

                    <div class="m3-dropdown-divider"></div>

                    <!-- Tombol Keluar (Logout) -->
                    <form action="{{ route('logout') }}" method="POST" id="form-logout" class="m-0 p-0">
                        @csrf
                        <button type="button" onclick="konfirmasiLogout()"
                            class="w-full m3-dropdown-item !py-2 !px-2.5 hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 text-rose-600 dark:text-rose-400 group cursor-pointer">
                            <div
                                class="w-7 h-7 rounded-lg bg-rose-500/10 group-hover:bg-rose-500/20 flex items-center justify-center text-xs text-rose-600 dark:text-rose-400 transition-colors shrink-0">
                                <i class="bi bi-box-arrow-left font-bold"></i>
                            </div>
                            <div class="flex-1 text-left min-w-0">
                                <p class="text-xs font-bold leading-none">Keluar Aplikasi</p>
                                <p class="text-[10px] text-rose-400/80 font-medium leading-none mt-1">Akhiri sesi login
                                </p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</header>
