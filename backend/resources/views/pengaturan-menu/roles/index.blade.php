@section('title', 'Hak Akses & Matriks RBAC')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Hak Akses & Matriks RBAC
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-1">
                Kelola hak akses dan wewenang operasional berdasarkan peran pengguna sistem.
            </p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-6 relative z-10">

        <!-- ================= SIDEBAR: DAFTAR PERAN ================= -->
        <div class="lg:col-span-4 xl:col-span-3">
            <div class="m3-glass-card p-4 md:p-5 sticky top-6 rounded-3xl shadow-2xs">

                <div class="flex items-center justify-between mb-4 px-1">
                    <div>
                        <h3 class="font-black text-zinc-900 dark:text-white text-base tracking-tight">
                            Daftar Peran
                        </h3>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-0.5">
                            {{ $roles->count() }} Role Terdaftar
                        </p>
                    </div>
                    @can('create roles')
                        <a href="{{ route('roles.create') }}"
                            class="action-modal w-8 h-8 flex items-center justify-center bg-primary/10 hover:bg-primary/20 text-primary dark:text-primary-dark border border-primary/20 rounded-xl transition-all shrink-0 shadow-2xs"
                            title="Buat Role Baru">
                            <i class="bi bi-plus-lg text-sm font-black"></i>
                        </a>
                    @endcan
                </div>

                <!-- List Roles -->
                <div class="space-y-1.5 max-h-[70vh] overflow-y-auto custom-scrollbar pr-1">
                    @forelse($roles as $role)
                        @php
                            $isSelected = $activeRole && $activeRole->id == $role->id;
                            $isSuperAdmin = $role->name === 'administrator';
                        @endphp
                        <div
                            class="relative flex items-center justify-between px-2.5 py-2 min-h-[44px] rounded-2xl border transition-all group
                            {{ $isSelected
                                ? 'bg-primary/10 dark:bg-primary-dark/15 text-primary dark:text-primary-dark border-primary/30 dark:border-primary-dark/35 shadow-2xs'
                                : 'bg-white/40 dark:bg-zinc-900/40 text-zinc-600 dark:text-zinc-400 border-zinc-200/60 dark:border-zinc-800 hover:bg-white/70 dark:hover:bg-zinc-800/80 hover:text-zinc-900 dark:hover:text-white' }}">

                            <a href="{{ route('roles.index', ['role_id' => $role->id]) }}"
                                class="flex-1 flex items-center gap-2 truncate px-1 h-full text-xs font-black outline-none">
                                <i
                                    class="bi {{ $isSuperAdmin ? 'bi-shield-fill-check text-amber-500' : 'bi-shield-lock' }} text-sm"></i>
                                <span class="truncate">{{ $role->name }}</span>
                                @if ($isSuperAdmin)
                                    <span
                                        class="ml-auto text-[9px] font-black px-1.5 py-0.5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        SUPER
                                    </span>
                                @endif
                            </a>

                            @if (!$isSuperAdmin)
                                <div
                                    class="flex items-center gap-0.5 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-200 pr-1">
                                    @can('update roles')
                                        <a href="{{ route('roles.edit', $role->id) }}"
                                            class="action-modal w-6 h-6 flex items-center justify-center rounded-lg text-amber-500 hover:bg-amber-500/10 transition-colors outline-none"
                                            title="Edit Role">
                                            <i class="bi bi-pencil-square text-[11px]"></i>
                                        </a>
                                    @endcan

                                    @can('delete roles')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                            class="m-0 delete-ajax">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-6 h-6 flex items-center justify-center rounded-lg text-rose-500 hover:bg-rose-500/10 transition-colors outline-none"
                                                title="Hapus Role">
                                                <i class="bi bi-trash3-fill text-[11px]"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    @empty
                        <div
                            class="text-center py-6 bg-white/40 dark:bg-black/40 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800">
                            <i class="bi bi-shield-x text-xl text-zinc-400 mb-1 block"></i>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-wider">Belum ada role</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

        <!-- ================= MAIN CONTENT: MATRIKS AKSES ================= -->
        <div class="lg:col-span-8 xl:col-span-9">

            @if ($activeRole)
                @php
                    $isSuperAdminActive = $activeRole->name === 'administrator';
                    $groupedMatrix = $matrixMenus->groupBy('category');
                @endphp

                <div class="m3-glass-card rounded-3xl overflow-hidden shadow-2xs">

                    <!-- Header Matriks -->
                    <div
                        class="px-5 py-4 bg-zinc-100/60 dark:bg-zinc-800/60 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3
                                    class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                                    Matriks Hak Akses:
                                </h3>
                                <span
                                    class="px-2.5 py-0.5 rounded-xl bg-primary/10 dark:bg-primary-dark/20 border border-primary/20 text-primary dark:text-primary-dark text-xs font-black">
                                    {{ $activeRole->name }}
                                </span>
                            </div>
                            <p class="text-[10px] font-bold text-zinc-400 mt-0.5 uppercase tracking-wider">
                                Centang sakelar untuk memberikan atau mencabut wewenang secara otomatis.
                            </p>
                        </div>

                        <!-- Search Box in Matrix -->
                        <div class="relative w-full sm:w-56">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400"></i>
                            <input type="text" id="filterMatrixInput" placeholder="Cari modul / izin..."
                                class="m3-input-glass w-full !pl-8 text-xs font-bold !py-1.5 rounded-xl">
                        </div>
                    </div>

                    @if ($isSuperAdminActive)
                        <!-- Banner Super Admin -->
                        <div
                            class="p-4 mx-5 my-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-start gap-3 shadow-2xs">
                            <i class="bi bi-shield-fill-check text-amber-600 dark:text-amber-400 text-lg mt-0.5"></i>
                            <div>
                                <h4
                                    class="text-xs font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider">
                                    Hak Akses Penuh Super Admin
                                </h4>
                                <p
                                    class="text-[11px] font-medium text-amber-700 dark:text-amber-400/80 mt-0.5 leading-relaxed">
                                    Peran <span class="font-bold">administrator</span> memiliki wewenang tak terbatas ke
                                    seluruh modul sistem secara otomatis melalui <em>Gate Authorization Bypass</em>.
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Global Bulk Actions Toolbar -->
                    @if (!$isSuperAdminActive)
                        <div
                            class="px-5 py-2.5 bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-200/60 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-2">
                            <span
                                class="text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                Aksi Masal Cepat:
                            </span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="bulkToggleAll(true)"
                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-primary/10 text-primary dark:text-primary-dark hover:bg-primary/20 border border-primary/20 transition-all shadow-2xs">
                                    <i class="bi bi-check-all mr-1"></i> Pilih Semua
                                </button>
                                <button type="button" onclick="bulkToggleReadOnly()"
                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 transition-all shadow-2xs">
                                    <i class="bi bi-eye mr-1"></i> Hanya Baca (Read)
                                </button>
                                <button type="button" onclick="bulkToggleAll(false)"
                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 transition-all shadow-2xs">
                                    <i class="bi bi-x-lg mr-1"></i> Batal Semua
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Form Matriks Akses -->
                    <form id="formMatriksAkses" action="{{ route('roles.give-permissions', $activeRole->id) }}"
                        method="POST">
                        @csrf
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left text-xs border-collapse" id="matrixTable">
                                <thead
                                    class="bg-zinc-100/70 dark:bg-zinc-800/70 border-b border-zinc-200/80 dark:border-zinc-800">
                                    <tr
                                        class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        <th
                                            class="py-3 px-4 border-r border-zinc-200/80 dark:border-zinc-800 w-2/5 min-w-[220px]">
                                            Menu / Modul Sistem
                                        </th>
                                        <th class="py-3 px-4 min-w-[320px]">
                                            Tindakan & Hak Akses (Permissions)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800 bg-transparent">

                                    @forelse($groupedMatrix as $categoryName => $catMenus)
                                        <!-- HEADER KATEGORI -->
                                        <tr
                                            class="category-header-row bg-zinc-100/90 dark:bg-zinc-800/90 border-y border-zinc-200 dark:border-zinc-700">
                                            <td colspan="2" class="py-2.5 px-4">
                                                <div class="flex items-center justify-between">
                                                    <span
                                                        class="text-[11px] font-black text-zinc-700 dark:text-zinc-200 uppercase tracking-widest flex items-center gap-2">
                                                        <i
                                                            class="bi bi-folder2-open text-primary dark:text-primary-dark"></i>
                                                        {{ $categoryName ?: 'UMUM' }}
                                                    </span>
                                                    @if (!$isSuperAdminActive)
                                                        <div class="flex items-center gap-1">
                                                            <button type="button"
                                                                onclick="bulkCategoryToggle(this, true)"
                                                                class="px-2 py-0.5 text-[9px] font-bold rounded bg-primary/10 text-primary dark:text-primary-dark hover:bg-primary/20">
                                                                Semua
                                                            </button>
                                                            <button type="button"
                                                                onclick="bulkCategoryToggle(this, false)"
                                                                class="px-2 py-0.5 text-[9px] font-bold rounded bg-zinc-200 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-300">
                                                                Batal
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        @foreach ($catMenus as $menu)
                                            <!-- BARIS MENU UTAMA -->
                                            <tr class="matrix-row bg-zinc-50/30 dark:bg-zinc-900/30 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-colors"
                                                data-search="{{ strtolower($menu->name) }}">
                                                <td
                                                    class="py-3 px-4 border-l-[3px] border-primary border-r border-zinc-200/80 dark:border-zinc-800 align-top">
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary dark:text-primary-dark border border-primary/20 text-xs shrink-0">
                                                            <i class="bi {{ $menu->icon ?? 'bi-folder-fill' }}"></i>
                                                        </div>
                                                        <div class="overflow-hidden">
                                                            <span
                                                                class="font-black text-zinc-900 dark:text-white text-xs tracking-tight block truncate">
                                                                {{ $menu->name }}
                                                            </span>
                                                            @if ($menu->url && $menu->url !== '#')
                                                                <span
                                                                    class="text-[9px] font-mono text-zinc-400 block truncate">
                                                                    {{ $menu->url }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 align-top">
                                                    <div class="flex flex-wrap gap-3">
                                                        @forelse($menu->permissions as $perm)
                                                            @php $isChecked = in_array($perm->name, $rolePermissions) || $isSuperAdminActive; @endphp
                                                            <label
                                                                class="relative inline-flex items-center cursor-pointer group"
                                                                title="Izin: {{ $perm->name }}">
                                                                <input type="checkbox" name="permissions[]"
                                                                    value="{{ $perm->name }}"
                                                                    class="sr-only peer perm-checkbox"
                                                                    data-perm="{{ $perm->name }}"
                                                                    {{ $isChecked ? 'checked' : '' }}
                                                                    {{ $isSuperAdminActive ? 'disabled' : '' }}>
                                                                <div
                                                                    class="relative w-7 h-3.5 bg-zinc-200 dark:bg-zinc-700 rounded-full peer peer-checked:after:translate-x-3.5 after:content-[''] after:absolute after:top-[1.5px] after:left-[1.5px] after:bg-white after:rounded-full after:h-2.5 after:w-2.5 after:transition-all peer-checked:bg-primary dark:peer-checked:bg-primary-dark transition-colors">
                                                                </div>
                                                                <span
                                                                    class="ml-2 text-[10px] font-bold text-zinc-600 dark:text-zinc-400 peer-checked:text-primary dark:peer-checked:text-primary-dark transition-colors">
                                                                    {{ ucwords(str_replace(['-', '_', '.'], ' ', $perm->name)) }}
                                                                </span>
                                                            </label>
                                                        @empty
                                                            <span
                                                                class="text-[9px] font-semibold text-zinc-400 italic">--
                                                                Sub-menu di bawah --</span>
                                                        @endforelse
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- BARIS SUB-MENU -->
                                            @foreach ($menu->subMenus as $subMenu)
                                                <tr class="matrix-row hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors"
                                                    data-search="{{ strtolower($menu->name . ' ' . $subMenu->name) }}">
                                                    <td
                                                        class="py-2.5 px-4 pl-10 relative border-r border-zinc-200/80 dark:border-zinc-800 align-top">
                                                        <div
                                                            class="absolute left-6 top-0 bottom-1/2 w-px bg-zinc-300 dark:bg-zinc-700">
                                                        </div>
                                                        <div
                                                            class="absolute left-6 top-1/2 w-3 h-px bg-zinc-300 dark:bg-zinc-700">
                                                        </div>

                                                        <div class="flex items-center gap-2 relative z-10">
                                                            <i
                                                                class="bi {{ $subMenu->icon ?? 'bi-circle' }} text-zinc-400 text-xs shrink-0"></i>
                                                            <div class="overflow-hidden">
                                                                <span
                                                                    class="font-bold text-zinc-800 dark:text-zinc-200 text-[11px] tracking-tight block truncate">
                                                                    {{ $subMenu->name }}
                                                                </span>
                                                                @if ($subMenu->url && $subMenu->url !== '#')
                                                                    <span
                                                                        class="text-[9px] font-mono text-zinc-400 block truncate">
                                                                        {{ $subMenu->url }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-2.5 px-4 align-top">
                                                        <div class="flex flex-wrap gap-3">
                                                            @forelse($subMenu->permissions as $subPerm)
                                                                @php $isChecked = in_array($subPerm->name, $rolePermissions) || $isSuperAdminActive; @endphp
                                                                <label
                                                                    class="relative inline-flex items-center cursor-pointer group"
                                                                    title="Izin: {{ $subPerm->name }}">
                                                                    <input type="checkbox" name="permissions[]"
                                                                        value="{{ $subPerm->name }}"
                                                                        class="sr-only peer perm-checkbox"
                                                                        data-perm="{{ $subPerm->name }}"
                                                                        {{ $isChecked ? 'checked' : '' }}
                                                                        {{ $isSuperAdminActive ? 'disabled' : '' }}>
                                                                    <div
                                                                        class="relative w-7 h-3.5 bg-zinc-200 dark:bg-zinc-700 rounded-full peer peer-checked:after:translate-x-3.5 after:content-[''] after:absolute after:top-[1.5px] after:left-[1.5px] after:bg-white after:rounded-full after:h-2.5 after:w-2.5 after:transition-all peer-checked:bg-primary dark:peer-checked:bg-primary-dark transition-colors">
                                                                    </div>
                                                                    <span
                                                                        class="ml-2 text-[10px] font-bold text-zinc-600 dark:text-zinc-400 peer-checked:text-primary dark:peer-checked:text-primary-dark transition-colors">
                                                                        {{ ucwords(str_replace(['-', '_', '.'], ' ', $subPerm->name)) }}
                                                                    </span>
                                                                </label>
                                                            @empty
                                                                <span
                                                                    class="text-[9px] font-semibold text-zinc-400 italic">--
                                                                    Tidak ada aksi --</span>
                                                            @endforelse
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-10">
                                                <x-empty-state icon="bi-menu-button-wide-fill" title="Belum Ada Menu"
                                                    message="Silakan buat menu terlebih dahulu." />
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                </div>
            @else
                <!-- State: Belum Pilih Role -->
                <div
                    class="m3-glass-card rounded-3xl p-10 text-center flex flex-col items-center justify-center min-h-[400px] shadow-2xs">
                    <x-empty-state icon="bi-shield-lock-fill" title="Pilih Role Pengguna"
                        message="Silakan pilih salah satu peran di daftar sebelah kiri untuk memuat matriks hak akses." />
                </div>
            @endif

        </div>
    </div>

    @push('script')
        <script>
            const form = document.getElementById('formMatriksAkses');
            const filterInput = document.getElementById('filterMatrixInput');

            // 1. Filter / Search Realtime di Matriks
            if (filterInput) {
                filterInput.addEventListener('input', function() {
                    const q = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('.matrix-row');
                    rows.forEach(row => {
                        const searchData = row.getAttribute('data-search') || '';
                        if (searchData.includes(q) || q === '') {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // 2. Submit Auto-Save AJAX
            function submitPermissionsAjax() {
                if (!form) return;
                const formData = new FormData(form);
                const isDark = document.documentElement.classList.contains('dark');

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    background: isDark ? '#18181b' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b',
                    customClass: {
                        popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 !p-3 shadow-lg',
                        title: 'text-[13px] font-bold'
                    }
                });

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success' || data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: 'Izin Berhasil Disimpan!'
                            });
                        }
                    })
                    .catch(() => {
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal menyimpan konfigurasi hak akses!'
                        });
                    });
            }

            // 3. Attach Change Listener ke Checkbox
            if (form) {
                const checkboxes = form.querySelectorAll('input.perm-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        submitPermissionsAjax();
                    });
                });
            }

            // 4. Bulk Action Global (Semua)
            function bulkToggleAll(checked) {
                if (!form) return;
                const checkboxes = form.querySelectorAll('input.perm-checkbox:not(:disabled)');
                checkboxes.forEach(cb => cb.checked = checked);
                submitPermissionsAjax();
            }

            // 5. Bulk Action Read-Only
            function bulkToggleReadOnly() {
                if (!form) return;
                const checkboxes = form.querySelectorAll('input.perm-checkbox:not(:disabled)');
                checkboxes.forEach(cb => {
                    const perm = cb.getAttribute('data-perm') || '';
                    if (perm.startsWith('read')) {
                        cb.checked = true;
                    } else {
                        cb.checked = false;
                    }
                });
                submitPermissionsAjax();
            }

            // 6. Bulk Action Per Category
            function bulkCategoryToggle(btn, checked) {
                const tr = btn.closest('tr');
                let next = tr.nextElementSibling;
                while (next && !next.classList.contains('category-header-row')) {
                    const cbs = next.querySelectorAll('input.perm-checkbox:not(:disabled)');
                    cbs.forEach(cb => cb.checked = checked);
                    next = next.nextElementSibling;
                }
                submitPermissionsAjax();
            }
        </script>
    @endpush
</x-app-layout>
