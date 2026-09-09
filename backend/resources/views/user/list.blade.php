@forelse($users as $user)
    @php
        $roleName = $user->roles->first()->name ?? 'Tanpa Role';

        // Warna badge role
        $roleColors = [
            'administrator' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
            'staff' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
            'petugas-tabungan' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
            'bendahara' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
            'ustadz' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
            'petugas-cetak' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
            'wali-murid' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20',
        ];
        $roleBadgeClass =
            $roleColors[$roleName] ?? 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20';

        // Warna avatar gradient per role
        $avatarColors = [
            'administrator' => 'from-purple-500 to-violet-600 text-white border-purple-400/30',
            'staff' => 'from-sky-500 to-blue-600 text-white border-sky-400/30',
            'petugas-tabungan' => 'from-emerald-500 to-green-600 text-white border-emerald-400/30',
            'bendahara' => 'from-amber-500 to-orange-600 text-white border-amber-400/30',
            'ustadz' => 'from-teal-500 to-cyan-600 text-white border-teal-400/30',
            'petugas-cetak' => 'from-indigo-500 to-blue-600 text-white border-indigo-400/30',
            'wali-murid' => 'from-zinc-400 to-zinc-500 text-white border-zinc-400/30',
        ];
        $avatarClass = $avatarColors[$roleName] ?? 'from-zinc-400 to-zinc-500 text-white border-zinc-400/30';

        // Ikon role
        $roleIcons = [
            'administrator' => 'bi-shield-check',
            'staff' => 'bi-person-workspace',
            'petugas-tabungan' => 'bi-wallet2',
            'bendahara' => 'bi-cash-stack',
            'ustadz' => 'bi-mortarboard-fill',
            'petugas-cetak' => 'bi-printer-fill',
            'wali-murid' => 'bi-people-fill',
        ];
        $roleIcon = $roleIcons[$roleName] ?? 'bi-person';

        $isCurrent = auth()->id() === $user->id;
    @endphp

    <div
        class="m3-glass-card p-4 md:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 group relative overflow-hidden transition-all duration-200 shadow-2xs hover:shadow-sm hover:border-primary/30 dark:hover:border-primary-dark/30 {{ !$user->is_active ? 'opacity-60 bg-zinc-50/60 dark:bg-zinc-900/40' : '' }}">

        <!-- Kolom Kiri: Identitas Pengguna -->
        <div class="flex items-start sm:items-center gap-3.5 relative z-10 w-full lg:w-auto min-w-0">

            <!-- Avatar dengan warna gradient sesuai role -->
            <div class="relative flex-shrink-0">
                <div
                    class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br {{ $avatarClass }} shadow-md border font-black text-sm tracking-wider select-none">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <!-- Online Status Dot -->
                @if ($user->isOnline())
                    <span
                        class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-zinc-900 rounded-full shadow-xs"
                        title="Sedang Online">
                        <span class="absolute inset-0 rounded-full bg-emerald-500 animate-ping opacity-75"></span>
                    </span>
                @else
                    <span
                        class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-zinc-300 dark:bg-zinc-600 border-2 border-white dark:border-zinc-900 rounded-full"
                        title="Offline"></span>
                @endif
            </div>

            <!-- Detail Info -->
            <div class="flex-1 min-w-0">
                <!-- Baris 1: Nama + Badge -->
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-0.5">
                    <h4
                        class="text-sm md:text-[15px] font-black text-zinc-900 dark:text-white tracking-tight leading-tight truncate max-w-[200px] md:max-w-none">
                        {{ $user->name }}
                    </h4>
                    @if ($isCurrent)
                        <span
                            class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-primary/10 text-primary dark:text-primary-dark border border-primary/20 inline-flex items-center gap-0.5">
                            <i class="bi bi-check-circle-fill text-[8px]"></i> Anda
                        </span>
                    @endif
                    @if (!$user->is_active)
                        <span
                            class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <!-- Baris 2: Username + Role + Tingkat -->
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <!-- Username -->
                    <span class="inline-flex items-center text-xs font-mono font-bold text-zinc-500 dark:text-zinc-400">
                        <span class="text-zinc-400 dark:text-zinc-500 mr-0.5">@</span>{{ $user->username }}
                    </span>

                    <span class="text-zinc-300 dark:text-zinc-700 text-[10px]">•</span>

                    <!-- Role Badge dengan ikon -->
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border shadow-2xs {{ $roleBadgeClass }}">
                        <i class="bi {{ $roleIcon }} text-[9px]"></i>
                        {{ strtoupper(str_replace('-', ' ', $roleName)) }}
                    </span>

                    <!-- Tingkat Badge -->
                    @if ($user->tingkat)
                        <span
                            class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-2xs">
                            <i class="bi bi-layers text-[9px]"></i> {{ $user->tingkat->nama_tingkat }}
                        </span>
                    @endif
                </div>

                <!-- Baris 3: Email + Linked Profile -->
                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5">
                    @if ($user->email)
                        <span
                            class="text-[11px] text-zinc-400 dark:text-zinc-500 font-mono truncate inline-flex items-center gap-1">
                            <i class="bi bi-envelope text-[10px]"></i>{{ $user->email }}
                        </span>
                    @endif

                    @if ($user->administrator)
                        <span
                            class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 inline-flex items-center gap-0.5"
                            title="Terhubung ke Biodata Administrator">
                            <i class="bi bi-link-45deg text-emerald-500 text-xs"></i> Biodata Admin
                        </span>
                    @elseif ($user->ustadz)
                        <span
                            class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 inline-flex items-center gap-0.5"
                            title="Terhubung ke Biodata Ustadz">
                            <i class="bi bi-link-45deg text-teal-500 text-xs"></i> Biodata Guru
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Status & Action Buttons -->
        <div
            class="flex flex-wrap sm:flex-nowrap items-center justify-between lg:justify-end gap-3 sm:gap-4 relative z-10 w-full lg:w-auto border-t lg:border-none border-zinc-200/60 dark:border-zinc-800 pt-3 lg:pt-0">

            <!-- Status Online & Toggle Aktif -->
            <div class="flex flex-col items-start lg:items-end gap-1 min-w-[100px]">
                @if ($user->isOnline())
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Online
                    </span>
                @else
                    <span
                        class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 inline-flex items-center gap-1 font-mono"
                        title="{{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->translatedFormat('d M Y H:i') : 'Belum pernah login' }}">
                        <i class="bi bi-clock-history text-[10px]"></i> {{ $user->lastSeenText() }}
                    </span>
                @endif

                <!-- Status Aktif Toggle Switch -->
                @if (!$isCurrent)
                    <div class="flex items-center gap-2 mt-0.5">
                        <span
                            class="text-[10px] font-bold uppercase tracking-wider {{ $user->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-zinc-400' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" onchange="toggleUserStatus({{ $user->id }}, this)"
                                {{ $user->is_active ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-8 h-4 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-zinc-600 peer-checked:bg-emerald-500">
                            </div>
                        </label>
                    </div>
                @endif
            </div>

            <!-- Vertical Divider -->
            <div class="hidden sm:block w-px h-10 bg-zinc-200/80 dark:bg-zinc-800"></div>

            <!-- Action Buttons Group -->
            <div class="flex items-center gap-1.5">

                <!-- Hubungi WhatsApp -->
                <a href="{{ route('pengguna.whatsapp', $user->id) }}" target="_blank"
                    class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-150 hover:scale-105 active:scale-90 shadow-2xs outline-none"
                    title="Hubungi via WhatsApp">
                    <i class="bi bi-whatsapp text-sm"></i>
                </a>

                <!-- Reset Password -->
                <button type="button"
                    onclick="openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                    class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-150 hover:scale-105 active:scale-90 shadow-2xs outline-none"
                    title="Reset Password Akun">
                    <i class="bi bi-key-fill text-sm"></i>
                </button>

                <!-- Force Logout -->
                @if (!$isCurrent)
                    <button type="button"
                        onclick="confirmForceLogout({{ $user->id }}, '{{ addslashes($user->name) }}')"
                        class="w-9 h-9 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center hover:bg-orange-500/20 border border-orange-500/20 transition-all duration-150 hover:scale-105 active:scale-90 shadow-2xs outline-none"
                        title="Force Logout (Keluarkan Paksa)">
                        <i class="bi bi-box-arrow-right text-sm"></i>
                    </button>
                @endif

                <!-- Edit Pengguna -->
                <button type="button" onclick="openEditUserModal({{ $user->id }})"
                    class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center hover:bg-sky-500/20 border border-sky-500/20 transition-all duration-150 hover:scale-105 active:scale-90 shadow-2xs outline-none"
                    title="Edit Data Pengguna">
                    <i class="bi bi-pencil-square text-sm"></i>
                </button>

                <!-- Hapus Pengguna -->
                @if (!$isCurrent)
                    <button type="button"
                        onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                        class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center hover:bg-rose-500/20 border border-rose-500/20 transition-all duration-150 hover:scale-105 active:scale-90 shadow-2xs outline-none"
                        title="Hapus Akun Pengguna">
                        <i class="bi bi-trash3-fill text-sm"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
@empty
    <!-- Empty State -->
    <div
        class="m3-glass-card p-10 md:p-14 flex flex-col items-center justify-center text-center rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
        <div
            class="w-16 h-16 rounded-3xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-4 border border-zinc-200/80 dark:border-zinc-700">
            <i class="bi bi-people text-2xl text-zinc-400 dark:text-zinc-500"></i>
        </div>
        <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight mb-1">Data Pengguna Tidak Ditemukan
        </h3>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium max-w-sm">
            Tidak ada akun pengguna yang cocok dengan kriteria pencarian / filter yang dipilih.
        </p>
        <button type="button" onclick="resetAllFilters()"
            class="mt-4 h-9 px-4 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-xs font-black text-zinc-700 dark:text-zinc-300 transition-all inline-flex items-center gap-1.5 active:scale-95">
            <i class="bi bi-arrow-counterclockwise text-sm"></i> Reset Filter
        </button>
    </div>
@endforelse

<!-- Pagination Links -->
@if ($users->hasPages())
    <div class="mt-5 pt-3 border-t border-zinc-200/60 dark:border-zinc-800">
        {{ $users->links() }}
    </div>
@endif
