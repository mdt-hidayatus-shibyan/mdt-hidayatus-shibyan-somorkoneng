@forelse($users as $user)
    @php
        $roleName = $user->roles->first()->name ?? 'Tanpa Role';
        $roleColors = [
            'administrator'    => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
            'staff'            => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
            'petugas-tabungan' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
            'bendahara'        => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
            'ustadz'           => 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
            'petugas-cetak'    => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
            'wali-murid'       => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20',
        ];
        $roleBadgeClass = $roleColors[$roleName] ?? 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20';
        $isCurrent = auth()->id() === $user->id;
    @endphp

    <div class="m3-glass-card p-4 md:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 group relative overflow-hidden transition-all shadow-2xs hover:border-primary/40 dark:hover:border-primary-dark/40 {{ !$user->is_active ? 'opacity-70 bg-zinc-50/60 dark:bg-zinc-900/40' : '' }}">

        <!-- Kolom Kiri: Identitas Pengguna -->
        <div class="flex items-start sm:items-center gap-3.5 relative z-10 w-full lg:w-auto">

            <!-- Avatar & Online Pulse Indicator -->
            <div class="relative flex-shrink-0">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center border border-zinc-200 dark:border-zinc-700 bg-gradient-to-br from-zinc-100 to-zinc-200 dark:from-zinc-800 dark:to-zinc-850 shadow-2xs text-zinc-800 dark:text-zinc-200 font-black text-sm tracking-wider font-mono">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <!-- Status Dot -->
                @if ($user->isOnline())
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-zinc-900 rounded-full animate-pulse shadow-xs" title="Sedang Online"></span>
                @else
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-zinc-300 dark:bg-zinc-600 border-2 border-white dark:border-zinc-900 rounded-full" title="Offline"></span>
                @endif
            </div>

            <!-- Detail Info -->
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h4 class="text-sm md:text-base font-black text-zinc-900 dark:text-white tracking-tight leading-tight truncate">
                        {{ $user->name }}
                    </h4>
                    @if ($isCurrent)
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-primary/10 text-primary dark:text-primary-dark border border-primary/20">
                            Akun Anda
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Username -->
                    <span class="inline-flex items-center text-xs font-mono font-bold text-zinc-500 dark:text-zinc-400">
                        <span class="text-zinc-400 dark:text-zinc-500 mr-0.5">@</span>{{ $user->username }}
                    </span>

                    <span class="text-zinc-300 dark:text-zinc-700">•</span>

                    <!-- Role Badge -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border shadow-2xs {{ $roleBadgeClass }}">
                        {{ strtoupper($roleName) }}
                    </span>

                    <!-- Tingkat Badge (jika ada) -->
                    @if ($user->tingkat)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-2xs">
                            <i class="bi bi-layers mr-1 text-[9px]"></i> {{ $user->tingkat->nama_tingkat }}
                        </span>
                    @endif

                    <!-- Linked Profile Badge -->
                    @if ($user->administrator)
                        <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 flex items-center gap-1" title="Terhubung ke Biodata Administrator">
                            <i class="bi bi-link-45deg text-emerald-500"></i> Biodata Admin
                        </span>
                    @elseif ($user->ustadz)
                        <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 flex items-center gap-1" title="Terhubung ke Biodata Ustadz">
                            <i class="bi bi-link-45deg text-teal-500"></i> Biodata Guru
                        </span>
                    @endif
                </div>

                <!-- Email (jika ada) -->
                @if ($user->email)
                    <div class="text-[11px] text-zinc-400 dark:text-zinc-500 font-mono truncate mt-0.5">
                        <i class="bi bi-envelope text-[10px] mr-1"></i>{{ $user->email }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Status & Action Buttons -->
        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between lg:justify-end gap-3 sm:gap-4 relative z-10 w-full lg:w-auto border-t lg:border-none border-zinc-200/60 dark:border-zinc-800 pt-3 lg:pt-0">

            <!-- Status Online & Terakhir Dilihat -->
            <div class="flex flex-col items-start lg:items-end gap-0.5">
                @if ($user->isOnline())
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Online
                    </span>
                @else
                    <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 flex items-center gap-1 font-mono"
                        title="{{ $user->last_seen_at ? \Carbon\Carbon::parse($user->last_seen_at)->translatedFormat('d M Y H:i') : 'Belum pernah login' }}">
                        <i class="bi bi-clock-history text-[10px]"></i> {{ $user->lastSeenText() }}
                    </span>
                @endif

                <!-- Status Aktif Toggle Switch -->
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $user->is_active ? 'text-emerald-600 dark:text-emerald-400 font-black' : 'text-zinc-400' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    @if (!$isCurrent)
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" onchange="toggleUserStatus({{ $user->id }}, this)" {{ $user->is_active ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-8 h-4 bg-zinc-200 peer-focus:outline-none rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-zinc-600 peer-checked:bg-emerald-500"></div>
                        </label>
                    @endif
                </div>
            </div>

            <!-- Divider -->
            <div class="hidden sm:block w-px h-8 bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

            <!-- Action Buttons Group -->
            <div class="flex items-center gap-1.5">

                <!-- Hubungi WhatsApp -->
                <a href="{{ route('pengguna.whatsapp', $user->id) }}" target="_blank"
                    class="w-8.5 h-8.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center hover:bg-emerald-500/20 border border-emerald-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                    title="Hubungi via WhatsApp">
                    <i class="bi bi-whatsapp text-xs"></i>
                </a>

                <!-- Reset Password Cepat -->
                <button type="button" onclick="openResetPasswordModal({{ $user->id }}, '{{ $user->name }}')"
                    class="w-8.5 h-8.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center hover:bg-amber-500/20 border border-amber-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                    title="Reset Password Akun">
                    <i class="bi bi-key-fill text-xs"></i>
                </button>

                <!-- Force Logout (Keluarkan Paksa) -->
                @if (!$isCurrent)
                    <button type="button" onclick="confirmForceLogout({{ $user->id }}, '{{ $user->name }}')"
                        class="w-8.5 h-8.5 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center hover:bg-orange-500/20 border border-orange-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                        title="Force Logout (Keluarkan Paksa Sesi)">
                        <i class="bi bi-box-arrow-right text-xs"></i>
                    </button>
                @endif

                <!-- Edit Pengguna -->
                <button type="button" onclick="openEditUserModal({{ $user->id }})"
                    class="w-8.5 h-8.5 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center hover:bg-sky-500/20 border border-sky-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                    title="Edit Data Pengguna">
                    <i class="bi bi-pencil-square text-xs"></i>
                </button>

                <!-- Hapus Pengguna -->
                @if (!$isCurrent)
                    <button type="button" onclick="confirmDeleteUser({{ $user->id }}, '{{ $user->name }}')"
                        class="w-8.5 h-8.5 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center hover:bg-rose-500/20 border border-rose-500/20 transition-all hover:scale-105 active:scale-90 shadow-2xs outline-none"
                        title="Hapus Akun Pengguna">
                        <i class="bi bi-trash3-fill text-xs"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
@empty
    <!-- Empty State -->
    <x-empty-state icon="bi-people" title="Data Pengguna Tidak Ditemukan"
        message="Tidak ada akun pengguna yang cocok dengan kriteria pencarian / filter yang dipilih." />
@endforelse

<!-- Pagination Links -->
@if($users->hasPages())
    <div class="mt-4 pt-2">
        {{ $users->links() }}
    </div>
@endif