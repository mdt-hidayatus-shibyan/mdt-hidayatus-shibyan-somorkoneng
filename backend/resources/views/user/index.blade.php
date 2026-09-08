@section('title', 'Manajemen Akun Pengguna')

<x-app-layout>
    <!-- 1. Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-primary/10 text-primary dark:text-primary-dark border border-primary/20">
                    Sistem & Keamanan
                </span>
            </div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Manajemen Akun Pengguna
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Monitoring status online, manajemen sesi login, kredensial, dan hak akses seluruh akun pengguna sistem.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button type="button" onclick="openCreateUserModal()"
                class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-md flex items-center justify-center gap-2 transition-all active:scale-95">
                <i class="bi bi-person-plus-fill text-sm"></i>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Ringkasan Metrik Akun (4 Card Stats) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Total Users -->
        <div class="m3-glass-card p-4.5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div class="w-11 h-11 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-black border border-indigo-500/20 flex-shrink-0">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Total Pengguna</span>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white" id="statTotalUsers">{{ $totalUsers }}</span>
            </div>
        </div>

        <!-- Online Users -->
        <div class="m3-glass-card p-4.5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black border border-emerald-500/20 flex-shrink-0 relative">
                <i class="bi bi-broadcast"></i>
                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
            </div>
            <div>
                <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Sedang Online</span>
                <span class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400" id="statOnlineUsers">{{ $onlineUsers }}</span>
            </div>
        </div>

        <!-- Active Users -->
        <div class="m3-glass-card p-4.5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div class="w-11 h-11 rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xl font-black border border-sky-500/20 flex-shrink-0">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Akun Aktif</span>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white" id="statActiveUsers">{{ $activeUsers }}</span>
            </div>
        </div>

        <!-- Inactive Users -->
        <div class="m3-glass-card p-4.5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-3.5 shadow-2xs">
            <div class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl font-black border border-rose-500/20 flex-shrink-0">
                <i class="bi bi-dash-circle-fill"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Nonaktif</span>
                <span class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white" id="statInactiveUsers">{{ $inactiveUsers }}</span>
            </div>
        </div>
    </div>

    <!-- 3. Toolbar & Filter Section -->
    <div class="m3-glass-card p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 mb-5 relative z-10 shadow-2xs">
        <form id="filterForm" action="{{ route('pengguna.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
            
            <!-- Pencarian Teks -->
            <div class="relative lg:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                    placeholder="Cari nama, username, atau email..."
                    class="m3-input-glass w-full !pl-9 !pr-8 text-xs font-bold">
                @if (request('search'))
                    <button type="button" onclick="clearSearch()" class="absolute inset-y-0 right-0 w-8 flex items-center justify-center text-zinc-400 hover:text-rose-600 transition-colors">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                @endif
            </div>

            <!-- Filter Role -->
            <div>
                <select name="role" id="roleFilter" onchange="applyFilters()" class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                    <option value="">-- Semua Peran (Role) --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>
                            {{ strtoupper($r->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <select name="status" id="statusFilter" onchange="applyFilters()" class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <!-- Filter Aktivitas Online -->
            <div class="flex items-center gap-2">
                <select name="online" id="onlineFilter" onchange="applyFilters()" class="m3-input-glass w-full text-xs font-bold cursor-pointer">
                    <option value="">-- Semua Aktivitas --</option>
                    <option value="online" {{ request('online') === 'online' ? 'selected' : '' }}>🟢 Sedang Online</option>
                    <option value="offline" {{ request('online') === 'offline' ? 'selected' : '' }}>⚪ Offline</option>
                </select>

                @if(request()->hasAny(['search', 'role', 'status', 'online', 'tingkat_id']))
                    <a href="{{ route('pengguna.index') }}" title="Reset Filter"
                        class="w-9 h-9 rounded-xl flex items-center justify-center bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 hover:text-rose-600 transition-all flex-shrink-0 shadow-2xs">
                        <i class="bi bi-arrow-counterclockwise text-sm font-bold"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Data Grid Container -->
    <div id="data-grid-container" class="flex flex-col gap-3 relative z-10">
        @include('user.list', ['users' => $users])
    </div>

    <!-- 5. Modal Containers -->
    <div id="modalContainer"></div>
</x-app-layout>

<!-- 6. Interactive Scripts -->
<script>
    // Inisialisasi CSRF token untuk AJAX request
    const csrfToken = '{{ csrf_token() }}';

    // BUKA MODAL TAMBAH USER
    function openCreateUserModal() {
        showLoadingModal();
        fetch('{{ route("pengguna.create") }}?modal=1', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            bindUserFormSubmit();
        })
        .catch(err => {
            Swal.fire('Error', 'Gagal memuat form pengguna: ' + err, 'error');
        });
    }

    // BUKA MODAL EDIT USER
    function openEditUserModal(userId) {
        showLoadingModal();
        fetch(`{{ url('pengguna') }}/${userId}/edit?modal=1`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            bindUserFormSubmit();
        })
        .catch(err => {
            Swal.fire('Error', 'Gagal memuat form edit: ' + err, 'error');
        });
    }

    // TUTUP MODAL
    function closeUserModal() {
        const modal = document.getElementById('userFormModal');
        if (modal) {
            modal.classList.add('opacity-0');
            setTimeout(() => modal.remove(), 200);
        }
    }

    // BIND AJAX SUBMIT FORM USER
    function bindUserFormSubmit() {
        const form = document.getElementById('userForm');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitUser');
            const alertBox = document.getElementById('formErrorAlert');
            const origText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="inline-block animate-spin mr-1.5"><i class="bi bi-arrow-repeat"></i></span> Menyimpan...';
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
                if (!res.ok) {
                    throw data;
                }
                return data;
            })
            .then(data => {
                closeUserModal();
                showToast(data.message || 'Data berhasil disimpan!', 'success');
                refreshGridData();
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origText;

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
        fetch('{{ url("pengguna") }}/' + userId + '?modal=reset', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .catch(() => {});

        const modalHtml = `
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-300 animate-in fade-in" id="userResetPasswordModal">
            <div class="m3-glass-card !bg-white dark:!bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-3xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden transform transition-all animate-in zoom-in-95 duration-200">
                <div class="px-6 py-5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-lg border border-amber-500/20">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">Reset Password</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Akun: <b>${userName}</b></p>
                        </div>
                    </div>
                    <button type="button" onclick="closeResetModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                        <i class="bi bi-x-lg text-xs font-bold"></i>
                    </button>
                </div>
                <form id="resetPasswordForm" action="{{ url('pengguna') }}/${userId}/reset-password" method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <div id="resetErrorAlert" class="hidden p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold"></div>
                    <div class="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-300 text-[11px] font-medium leading-relaxed">
                        <i class="bi bi-exclamation-circle-fill mr-1"></i> Sesi login user ini akan otomatis diputus setelah reset password.
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Password Baru</label>
                            <button type="button" onclick="generateResetPassword()" class="text-[11px] font-black text-primary dark:text-primary-dark hover:underline">
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
                        <button type="button" onclick="closeResetModal()" class="h-10 px-5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-xs font-black">Batal</button>
                        <button type="submit" id="btnSubmitReset" class="h-10 px-6 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-md flex items-center gap-1.5">
                            <i class="bi bi-key-fill"></i> Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
        `;
        document.getElementById('modalContainer').innerHTML = modalHtml;

        document.getElementById('resetPasswordForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitReset');
            const alertBox = document.getElementById('resetErrorAlert');
            btn.disabled = true;
            btn.innerHTML = 'Memproses...';

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
                btn.innerHTML = '<i class="bi bi-key-fill"></i> Reset Password';
                alertBox.innerHTML = err.message || (err.errors ? Object.values(err.errors)[0][0] : 'Gagal reset password.');
                alertBox.classList.remove('hidden');
            });
        });
    }

    function closeResetModal() {
        const modal = document.getElementById('userResetPasswordModal');
        if (modal) modal.remove();
    }

    function generateResetPassword() {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#';
        let pass = '';
        for (let i = 0; i < 8; i++) pass += chars.charAt(Math.floor(Math.random() * chars.length));
        document.getElementById('resetNewPassword').value = pass;
        document.getElementById('resetNewPasswordConfirm').value = pass;
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
            html: `<p class="text-xs text-zinc-500 mt-1">Sesi login <b class="text-amber-600">${userName}</b> akan segera diputus secara paksa dari sistem.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-box-arrow-right mr-1"></i> Ya, Keluarkan!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                confirmButton: 'h-10 px-5 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs rounded-xl ml-2',
                cancelButton: 'h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl'
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
                .then(res => res.json())
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
            html: `<p class="text-xs text-zinc-500 mt-1">Anda yakin ingin menghapus akun <b class="text-zinc-900 dark:text-white">${userName}</b>? Tindakan ini tidak dapat dibatalkan.</p>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-trash3-fill mr-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                confirmButton: 'h-10 px-5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl ml-2',
                cancelButton: 'h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl'
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
                .then(res => res.json())
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

    // APPLY FILTERS & REFRESH GRID
    let searchTimeout = null;
    document.getElementById('searchInput')?.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 400);
    });

    function applyFilters() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        const url = `${form.action}?${params}`;

        window.history.replaceState({}, '', url);

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('data-grid-container').innerHTML = data.html;
            if (data.metrics) {
                document.getElementById('statTotalUsers').innerText = data.metrics.total;
                document.getElementById('statOnlineUsers').innerText = data.metrics.online;
                document.getElementById('statActiveUsers').innerText = data.metrics.active;
                document.getElementById('statInactiveUsers').innerText = data.metrics.inactive;
            }
        });
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        applyFilters();
    }

    function refreshGridData() {
        applyFilters();
    }

    function showLoadingModal() {
        // Placeholder loading
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