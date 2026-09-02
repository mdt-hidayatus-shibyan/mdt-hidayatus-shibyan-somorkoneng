@forelse($users as $user)
    <div
        class="m3-glass-card p-4 md:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group relative overflow-hidden transition-all shadow-2xs hover:border-primary/40 dark:hover:border-primary-dark/40">

        <!-- Card Info Section (Compact) -->
        <div class="flex items-center gap-3 md:gap-4 relative z-10 w-full sm:w-auto">

            <!-- Badge Nomor -->
            <span
                class="w-9 h-9 flex items-center justify-center bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 rounded-xl text-xs font-black border border-zinc-200 dark:border-zinc-700 flex-shrink-0 shadow-2xs">
                {{ $loop->iteration }}
            </span>

            <!-- Kotak Inisial Nama -->
            <div
                class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center border border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 shadow-2xs">
                <span class="text-zinc-800 dark:text-zinc-200 font-black text-sm tracking-wider font-mono">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
            </div>

            <div class="flex-1 overflow-hidden">
                <h4
                    class="text-sm md:text-base font-black text-zinc-900 dark:text-white tracking-tight leading-tight truncate mb-1">
                    {{ $user->name }}
                </h4>

                <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                    <!-- Role Badge -->
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[10px] font-bold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider shadow-2xs">
                        <span class="text-zinc-900 dark:text-white font-black">
                            {{ $user->roles->first()->name ?? 'Tanpa Role' }}
                        </span>
                    </span>

                    @if ($user->email)
                        <span class="text-zinc-400 dark:text-zinc-500 text-xs truncate max-w-[150px] sm:max-w-xs font-mono">
                            {{ $user->email }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status & Action Buttons Section -->
        <div
            class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4 relative z-10 w-full sm:w-auto border-t sm:border-none border-zinc-200/60 dark:border-zinc-800 pt-3 sm:pt-0 mt-1 sm:mt-0">

            <!-- Status Badges Column -->
            <div class="flex flex-col items-start sm:items-end gap-1">

                <!-- 1. Status Aktif / Tidak Aktif Akun -->
                @if ($user->is_active)
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Aktif
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 shadow-2xs">
                        Tidak Aktif
                    </span>
                @endif

                <!-- 2. Status Online / Waktu Terakhir Dilihat -->
                @if ($user->isOnline())
                    <span
                        class="inline-flex items-center text-[9px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        <span
                            class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Online
                    </span>
                @else
                    <span class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 flex items-center gap-1 font-mono"
                        title="{{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->translatedFormat('d M Y H:i') : 'Belum pernah login' }}">
                        <i class="bi bi-clock-history text-[9px]"></i> {{ $user->lastSeenText() }}
                    </span>
                @endif
            </div>

            <!-- Divider -->
            <div class="hidden sm:block w-px h-8 bg-zinc-200/80 dark:border-zinc-800 mx-1"></div>

            <!-- Action Buttons Column -->
            <div class="flex items-center gap-1.5">

                <!-- Hubungi WhatsApp -->
                <a href="{{ route('pengguna.whatsapp', $user->id) }}" target="_blank"
                    class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center hover:bg-emerald-500/20 border border-emerald-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                    title="Hubungi via WhatsApp">
                    <i class="bi bi-whatsapp text-xs"></i>
                </a>

                <!-- Force Logout -->
                @if (auth()->id() !== $user->id)
                    <form action="{{ route('pengguna.force-logout', $user->id) }}" method="POST"
                        class="inline m-0 p-0">
                        @csrf
                        <button type="button" onclick="confirmForceLogout(this, '{{ $user->name }}')"
                            class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center hover:bg-amber-500/20 border border-amber-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                            title="Force Logout (Keluarkan Paksa)">
                            <i class="bi bi-box-arrow-right text-xs font-black"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@empty
    <!-- Empty State -->
    <x-empty-state icon="bi-people" title="Data Pengguna Masih Kosong"
        message="Anda belum menambahkan Pengguna sistem." />
@endforelse

<script>
    function confirmForceLogout(button, userName) {
        const isDark = document.documentElement.classList.contains('dark');
        const swalBg = isDark ? '#0c0c0e' : '#ffffff';
        const swalColor = isDark ? '#f4f4f5' : '#18181b';

        Swal.fire({
            title: '<span class="text-base font-black tracking-tight">Keluarkan Paksa?</span>',
            html: `<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-1">Apakah Anda yakin ingin memutus sesi login <b class="text-amber-600 dark:text-amber-400">${userName}</b> secara paksa?</p>`,
            icon: 'warning',
            background: swalBg,
            color: swalColor,
            showCancelButton: true,
            heightAuto: false,
            confirmButtonText: '<i class="bi bi-box-arrow-right mr-1.5"></i> Ya, Keluarkan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                confirmButton: 'h-10 px-5 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none ml-2',
                cancelButton: 'h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }
</script>

