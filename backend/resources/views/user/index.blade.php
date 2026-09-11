@section('title', 'Manajemen Akun Pengguna')

<x-app-layout>
    <!-- 1. Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-primary/10 text-primary dark:text-primary-dark border border-primary/20 inline-flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-shield-lock-fill text-xs"></i>
                    <span>Sistem & Hak Akses</span>
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Manajemen Akun Pengguna
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Monitoring status online, manajemen sesi login aktif, reset kredensial, dan hak akses pengguna sistem.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button type="button" onclick="openCreateUserModal()"
                class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-md flex items-center justify-center gap-2 transition-all active:scale-95 group">
                <i class="bi bi-person-plus-fill text-sm transition-transform group-hover:scale-110"></i>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Ringkasan Metrik Akun (4 Interactive Quick-Filter Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Card 1: Total Users (Klik untuk Reset Semua Filter) -->
        <button type="button" onclick="filterByStat('all')"
            class="stat-filter-card text-left m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border transition-all duration-200 flex items-center gap-3.5 shadow-2xs hover:scale-[1.02] active:scale-[0.98] {{ !request('status') && !request('online') ? 'border-indigo-500/50 dark:border-indigo-500/60 ring-2 ring-indigo-500/10' : 'border-zinc-200/80 dark:border-zinc-800 hover:border-indigo-500/30' }}">
            <div
                class="w-11 h-11 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-black border border-indigo-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <span
                        class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                        Total Pengguna
                    </span>
                    <i class="bi bi-funnel text-[10px] text-zinc-400 opacity-0 group-hover:opacity-100"></i>
                </div>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white"
                    id="statTotalUsers">{{ $totalUsers }}</span>
            </div>
        </button>

        <!-- Card 2: Sedang Online (Klik untuk Filter Online) -->
        <button type="button" onclick="filterByStat('online')"
            class="stat-filter-card text-left m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border transition-all duration-200 flex items-center gap-3.5 shadow-2xs hover:scale-[1.02] active:scale-[0.98] {{ request('online') === 'online' ? 'border-emerald-500/50 dark:border-emerald-500/60 ring-2 ring-emerald-500/10 bg-emerald-500/5' : 'border-zinc-200/80 dark:border-zinc-800 hover:border-emerald-500/30' }}">
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 flex-shrink-0 relative shadow-2xs">
                <i class="bi bi-broadcast"></i>
                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Sedang Online
                </span>
                <span class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400"
                    id="statOnlineUsers">{{ $onlineUsers }}</span>
            </div>
        </button>

        <!-- Card 3: Akun Aktif (Klik untuk Filter Aktif) -->
        <button type="button" onclick="filterByStat('active')"
            class="stat-filter-card text-left m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border transition-all duration-200 flex items-center gap-3.5 shadow-2xs hover:scale-[1.02] active:scale-[0.98] {{ request('status') === '1' ? 'border-sky-500/50 dark:border-sky-500/60 ring-2 ring-sky-500/10 bg-sky-500/5' : 'border-zinc-200/80 dark:border-zinc-800 hover:border-sky-500/30' }}">
            <div
                class="w-11 h-11 rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xl font-black border border-sky-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Akun Aktif
                </span>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white"
                    id="statActiveUsers">{{ $activeUsers }}</span>
            </div>
        </button>

        <!-- Card 4: Akun Nonaktif (Klik untuk Filter Nonaktif) -->
        <button type="button" onclick="filterByStat('inactive')"
            class="stat-filter-card text-left m3-glass-card p-4 md:p-4.5 rounded-2xl md:rounded-3xl border transition-all duration-200 flex items-center gap-3.5 shadow-2xs hover:scale-[1.02] active:scale-[0.98] {{ request('status') === '0' ? 'border-rose-500/50 dark:border-rose-500/60 ring-2 ring-rose-500/10 bg-rose-500/5' : 'border-zinc-200/80 dark:border-zinc-800 hover:border-rose-500/30' }}">
            <div
                class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-black border border-rose-500/20 flex-shrink-0 shadow-2xs">
                <i class="bi bi-dash-circle-fill"></i>
            </div>
            <div class="min-w-0 flex-1">
                <span
                    class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block truncate">
                    Nonaktif
                </span>
                <span class="text-xl md:text-2xl font-black text-rose-600 dark:text-rose-400"
                    id="statInactiveUsers">{{ $inactiveUsers }}</span>
            </div>
        </button>
    </div>

    <!-- 3. Toolbar & Filter Section -->
    <div
        class="m3-glass-card p-3.5 md:p-4 rounded-2xl md:rounded-3xl border border-zinc-200/80 dark:border-zinc-800 mb-5 relative z-10 shadow-2xs">
        <form id="filterForm" action="{{ route('pengguna.index') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">

            <!-- Pencarian Teks (4 Kolom) -->
            <div class="relative lg:col-span-4">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                    placeholder="Cari nama, username, atau email..."
                    class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold">
                <button type="button" id="btnClearSearch" onclick="clearSearch()"
                    class="{{ request('search') ? '' : 'hidden' }} absolute inset-y-0 right-0 w-8 flex items-center justify-center text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Filter Role (3 Kolom) -->
            <div class="lg:col-span-3">
                <select name="role" id="roleFilter" onchange="applyFilters()"
                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                    <option value="">-- Semua Peran (Role) --</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>
                            {{ strtoupper(str_replace('-', ' ', $r->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tingkat (2 Kolom) -->
            <div class="lg:col-span-2">
                <select name="tingkat_id" id="tingkatFilter" onchange="applyFilters()"
                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                    <option value="">-- Semua Tingkat --</option>
                    @foreach ($tingkats as $t)
                        <option value="{{ $t->id }}" {{ request('tingkat_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->nama_tingkat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status & Online Quick Switch (3 Kolom) -->
            <div class="lg:col-span-3 flex items-center gap-2">
                <select name="status" id="statusFilter" onchange="applyFilters()"
                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                    <option value="">-- Status Akun --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>

                <select name="online" id="onlineFilter" onchange="applyFilters()"
                    class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                    <option value="">-- Aktivitas --</option>
                    <option value="online" {{ request('online') === 'online' ? 'selected' : '' }}>🟢 Online</option>
                    <option value="offline" {{ request('online') === 'offline' ? 'selected' : '' }}>⚪ Offline</option>
                </select>

                @php
                    $hasFilter = request()->hasAny(['search', 'role', 'status', 'online', 'tingkat_id']);
                @endphp
                <button type="button" id="btnResetFilter" onclick="resetAllFilters()" title="Reset Semua Filter"
                    class="{{ $hasFilter ? '' : 'hidden' }} w-9 h-9 rounded-xl md:rounded-2xl flex items-center justify-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 transition-all flex-shrink-0 shadow-2xs active:scale-90">
                    <i class="bi bi-arrow-counterclockwise text-sm font-bold"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- 4. Data Grid Container -->
    <div id="data-grid-container" class="flex flex-col gap-3 relative z-10 transition-opacity duration-200">
        @include('user.list', ['users' => $users])
    </div>

    <!-- 5. Modal Containers -->
    <div id="modalContainer"></div>
</x-app-layout>

<!-- 6. Interactive Scripts -->
<script>
    const csrfToken = '{{ csrf_token() }}';

    // BUKA MODAL TAMBAH USER
    function openCreateUserModal() {
        showLoadingState();
        fetch('{{ route('pengguna.create') }}?modal=1', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                hideLoadingState();
                document.getElementById('modalContainer').innerHTML = html;
                bindUserFormSubmit();
            })
            .catch(err => {
                hideLoadingState();
                Swal.fire('Error', 'Gagal memuat form pengguna: ' + err, 'error');
            });
    }

    // BUKA MODAL EDIT USER
    function openEditUserModal(userId) {
        showLoadingState();
        fetch(`{{ url('pengguna') }}/${userId}/edit?modal=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                hideLoadingState();
                document.getElementById('modalContainer').innerHTML = html;
                bindUserFormSubmit();
            })
            .catch(err => {
                hideLoadingState();
                Swal.fire('Error', 'Gagal memuat form edit: ' + err, 'error');
            });
    }

    // TUTUP MODAL USER
    function closeUserModal() {
        const modal = document.getElementById('userFormModal');
        if (modal) {
            modal.classList.add('opacity-0');
            setTimeout(() => modal.remove(), 200);
        }
    }

    // BIND AJAX SUBMIT FORM USER (TAMBAH / EDIT)
    function bindUserFormSubmit() {
        const form = document.getElementById('userForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitUser');
            const alertBox = document.getElementById('formErrorAlert');
            const origContent = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML =
                '<span class="inline-block animate-spin mr-1.5"><i class="bi bi-arrow-repeat"></i></span> Menyimpan...';
            alertBox.classList.add('hidden');
            alertBox.innerHTML = '';

            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw data;
                    return data;
                })
                .then(data => {
                    closeUserModal();
                    showToast(data.message || 'Data berhasil disimpan!', 'success');
                    refreshGridData();
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = origContent;

                    let errorsHtml = '';
                    if (err.errors) {
                        for (const key in err.errors) {
                            errorsHtml += `<div>• ${err.errors[key][0]}</div>`;
                        }
                    } else {
                        errorsHtml = `<div>• ${err.message || 'Terjadi kesalahan sistem.'}</div>`;
                    }
                    alertBox.innerHTML = errorsHtml;
                    alertBox.classList.remove('hidden');
                });
        });
    }

    // TOGGLE KONDISIONAL TINGKAT BERDASARKAN ROLE
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

    // GENERATOR PASSWORD ACAK
    function generateRandomPassword() {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%';
        let pass = '';
        for (let i = 0; i < 8; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const input1 = document.getElementById('inputPassword');
        const input2 = document.getElementById('inputPasswordConfirm');
        if (input1) input1.value = pass;
        if (input2) input2.value = pass;
        showToast('Password acak dibuat: ' + pass, 'info');
    }

    // BUKA MODAL RESET PASSWORD
    function openResetPasswordModal(userId, userName) {
        const modalHtml = `
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md transition-opacity duration-300 animate-in fade-in" id="userResetPasswordModal">
            <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-2xl border border-zinc-200/80 dark:border-zinc-800/80 rounded-3xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden transform transition-all animate-in zoom-in-95 duration-200">
                <div class="px-6 py-5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-lg border border-amber-500/20 shadow-2xs">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">Reset Password</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Akun: <b class="text-zinc-900 dark:text-white">${userName}</b></p>
                        </div>
                    </div>
                    <button type="button" onclick="closeResetModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </button>
                </div>
                <form id="resetPasswordForm" action="{{ url('pengguna') }}/${userId}/reset-password" method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <div id="resetErrorAlert" class="hidden p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold"></div>
                    <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-xs font-medium leading-relaxed flex items-start gap-2">
                        <i class="bi bi-exclamation-circle-fill text-amber-600 mt-0.5 flex-shrink-0"></i>
                        <span>Sesi login user ini akan otomatis diputus setelah reset password dan wajib login ulang.</span>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Password Baru</label>
                            <button type="button" onclick="generateResetPassword()" class="text-[11px] font-black text-primary dark:text-primary-dark hover:underline flex items-center gap-1">
                                <i class="bi bi-shuffle"></i> Acak
                            </button>
                        </div>
                        <input type="text" name="password" id="resetNewPassword" required placeholder="Minimal 6 digit" class="m3-input-glass w-full text-xs font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                        <input type="text" name="password_confirmation" id="resetNewPasswordConfirm" required placeholder="Ulangi password baru" class="m3-input-glass w-full text-xs font-mono font-bold">
                    </div>
                    <div class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-end gap-2.5">
                        <button type="button" onclick="closeResetModal()" class="h-10 px-5 rounded-xl md:rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-xs font-black text-zinc-700 dark:text-zinc-300 transition-all">Batal</button>
                        <button type="submit" id="btnSubmitReset" class="h-10 px-6 rounded-xl md:rounded-2xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-md flex items-center gap-1.5 transition-all active:scale-95">
                            <i class="bi bi-key-fill"></i> Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
        `;
        document.getElementById('modalContainer').innerHTML = modalHtml;

        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitReset');
            const alertBox = document.getElementById('resetErrorAlert');
            const origText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML =
                '<span class="inline-block animate-spin mr-1"><i class="bi bi-arrow-repeat"></i></span> Memproses...';

            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: new FormData(this)
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw data;
                    return data;
                })
                .then(data => {
                    closeResetModal();
                    showToast(data.message || 'Password berhasil direset!', 'success');
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = origText;
                    alertBox.innerHTML = err.message || (err.errors ? Object.values(err.errors)[0][0] :
                        'Gagal reset password.');
                    alertBox.classList.remove('hidden');
                });
        });
    }

    function closeResetModal() {
        const modal = document.getElementById('userResetPasswordModal');
        if (modal) {
            modal.classList.add('opacity-0');
            setTimeout(() => modal.remove(), 200);
        }
    }

    function generateResetPassword() {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#';
        let pass = '';
        for (let i = 0; i < 8; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('resetNewPassword').value = pass;
        document.getElementById('resetNewPasswordConfirm').value = pass;
        showToast('Password acak dibuat: ' + pass, 'info');
    }

    // TOGGLE STATUS AKTIF INSTAN VIA AJAX
    function toggleUserStatus(userId, checkbox) {
        const originalState = !checkbox.checked;
        fetch(`{{ url('pengguna') }}/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                showToast(data.message, 'success');
                refreshGridData();
            })
            .catch(err => {
                checkbox.checked = originalState;
                Swal.fire('Gagal', err.message || 'Gagal mengubah status aktif pengguna.', 'error');
            });
    }

    // FORCE LOGOUT KONFIRMASI
    function confirmForceLogout(userId, userName) {
        Swal.fire({
            title: '<span class="text-base font-black">Keluarkan Paksa Sesi?</span>',
            html: `<p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Sesi login <b class="text-amber-600 dark:text-amber-400">${userName}</b> akan segera diputus secara paksa dari sistem.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-box-arrow-right mr-1"></i> Ya, Keluarkan!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: '!rounded-3xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xl p-6 !bg-white/90 dark:!bg-zinc-900/90 !backdrop-blur-2xl',
                confirmButton: 'h-10 px-5 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs rounded-xl ml-2 shadow-md transition-all active:scale-95',
                cancelButton: 'h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl transition-all'
            },
            buttonsStyling: false
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`{{ url('pengguna') }}/${userId}/force-logout`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        showToast(data.message || 'Sesi berhasil diputus!', 'success');
                        refreshGridData();
                    })
                    .catch(err => {
                        Swal.fire('Error', err.message || 'Gagal memutus sesi.', 'error');
                    });
            }
        });
    }

    // HAPUS PENGGUNA KONFIRMASI
    function confirmDeleteUser(userId, userName) {
        Swal.fire({
            title: '<span class="text-base font-black text-rose-600">Hapus Akun Pengguna?</span>',
            html: `<p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Anda yakin ingin menghapus akun <b class="text-zinc-900 dark:text-white">${userName}</b>? Tindakan ini tidak dapat dibatalkan.</p>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash3-fill mr-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: '!rounded-3xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xl p-6 !bg-white/90 dark:!bg-zinc-900/90 !backdrop-blur-2xl',
                confirmButton: 'h-10 px-5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl ml-2 shadow-md transition-all active:scale-95',
                cancelButton: 'h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl transition-all'
            },
            buttonsStyling: false
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`{{ url('pengguna') }}/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        showToast(data.message || 'Pengguna berhasil dihapus!', 'success');
                        refreshGridData();
                    })
                    .catch(err => {
                        Swal.fire('Gagal', err.message || 'Gagal menghapus pengguna.', 'error');
                    });
            }
        });
    }

    // QUICK STAT FILTER
    function filterByStat(type) {
        const statusSelect = document.getElementById('statusFilter');
        const onlineSelect = document.getElementById('onlineFilter');
        const searchInput = document.getElementById('searchInput');
        const roleSelect = document.getElementById('roleFilter');
        const tingkatSelect = document.getElementById('tingkatFilter');

        if (type === 'all') {
            statusSelect.value = '';
            onlineSelect.value = '';
            roleSelect.value = '';
            tingkatSelect.value = '';
            searchInput.value = '';
        } else if (type === 'online') {
            onlineSelect.value = 'online';
            statusSelect.value = '';
        } else if (type === 'active') {
            statusSelect.value = '1';
            onlineSelect.value = '';
        } else if (type === 'inactive') {
            statusSelect.value = '0';
            onlineSelect.value = '';
        }

        applyFilters();
    }

    // RESET ALL FILTERS
    function resetAllFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('roleFilter').value = '';
        document.getElementById('tingkatFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('onlineFilter').value = '';
        applyFilters();
    }

    // APPLY FILTERS & REFRESH GRID
    let searchTimeout = null;
    document.getElementById('searchInput')?.addEventListener('input', function() {
        const btnClear = document.getElementById('btnClearSearch');
        if (btnClear) {
            if (this.value) btnClear.classList.remove('hidden');
            else btnClear.classList.add('hidden');
        }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 350);
    });

    function applyFilters() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        const url = `${form.action}?${params}`;

        window.history.replaceState({}, '', url);

        const gridContainer = document.getElementById('data-grid-container');
        if (gridContainer) gridContainer.classList.add('opacity-50', 'pointer-events-none');

        // Toggle reset button visibility
        const hasFilter = formData.get('search') || formData.get('role') || formData.get('status') !== '' || formData
            .get('online') || formData.get('tingkat_id');
        const btnReset = document.getElementById('btnResetFilter');
        if (btnReset) {
            if (hasFilter) btnReset.classList.remove('hidden');
            else btnReset.classList.add('hidden');
        }

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (gridContainer) {
                    gridContainer.innerHTML = data.html;
                    gridContainer.classList.remove('opacity-50', 'pointer-events-none');
                }
                if (data.metrics) {
                    document.getElementById('statTotalUsers').innerText = data.metrics.total;
                    document.getElementById('statOnlineUsers').innerText = data.metrics.online;
                    document.getElementById('statActiveUsers').innerText = data.metrics.active;
                    document.getElementById('statInactiveUsers').innerText = data.metrics.inactive;
                }
            })
            .catch(() => {
                if (gridContainer) gridContainer.classList.remove('opacity-50', 'pointer-events-none');
            });
    }

    function clearSearch() {
        const input = document.getElementById('searchInput');
        input.value = '';
        document.getElementById('btnClearSearch')?.classList.add('hidden');
        applyFilters();
    }

    function refreshGridData() {
        applyFilters();
    }

    function showLoadingState() {
        // Optional placeholder
    }

    function hideLoadingState() {
        // Optional placeholder
    }

    // TOGGLE PASSWORD VISIBILITY
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function showToast(message, icon = 'success') {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: isDark ? '#18181b' : '#ffffff',
            color: isDark ? '#f4f4f5' : '#18181b',
        });
    }
</script>
