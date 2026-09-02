@section('title', 'Hak Akses (RBAC)')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Hak Akses (RBAC)
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-1">
                Kelola wewenang peran berdasarkan akses menu dan fitur sistem.
            </p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-6 relative z-10">

        <!-- ================= SIDEBAR: DAFTAR PERAN ================= -->
        <div class="lg:col-span-4 xl:col-span-3">
            <div class="m3-glass-card p-4 md:p-5 sticky top-6 rounded-3xl shadow-2xs">

                <div class="flex items-center justify-between mb-4 px-1">
                    <h3 class="font-black text-zinc-900 dark:text-white text-base tracking-tight">
                        Daftar Peran
                    </h3>
                    @can('create roles')
                        <a href="{{ route('roles.create') }}"
                            class="action-modal w-8 h-8 flex items-center justify-center bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl transition-colors shrink-0 shadow-2xs"
                            title="Buat Role Baru">
                            <i class="bi bi-plus-lg text-sm font-black"></i>
                        </a>
                    @endcan
                </div>

                <!-- List Roles -->
                <div class="space-y-1.5 max-h-[70vh] overflow-y-auto custom-scrollbar pr-1">
                    @forelse($roles as $role)
                        <div
                            class="relative flex items-center justify-between px-2 py-1.5 min-h-[42px] rounded-xl border transition-all group
                            {{ $activeRole && $activeRole->id == $role->id
                                ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30 shadow-2xs'
                                : 'bg-white/40 dark:bg-black/40 text-zinc-600 dark:text-zinc-400 border-zinc-200/60 dark:border-zinc-800 hover:bg-white/70 dark:hover:bg-zinc-800/80' }}">

                            <a href="{{ route('roles.index', ['role_id' => $role->id]) }}"
                                class="flex-1 flex items-center truncate px-2 h-full text-xs font-bold outline-none">
                                <span class="truncate">{{ $role->name }}</span>
                            </a>

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
                <div class="m3-glass-card rounded-3xl overflow-hidden shadow-2xs">

                    <!-- Header Matriks -->
                    <div
                        class="px-5 py-4 bg-zinc-100/60 dark:bg-zinc-800/60 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white flex items-center">
                                Hak Akses:
                                <span
                                    class="text-emerald-600 dark:text-emerald-400 ml-2 bg-emerald-500/10 px-2.5 py-0.5 rounded-xl border border-emerald-500/20 text-xs font-black">
                                    {{ $activeRole->name }}
                                </span>
                            </h3>
                            <p class="text-[10px] font-black text-zinc-400 mt-0.5 uppercase tracking-wider">
                                Toggle sakelar untuk memberikan/mencabut wewenang otomatis.
                            </p>
                        </div>
                        <div
                            class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20 shrink-0">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                    </div>

                    <!-- Table Form Dinamis -->
                    <form id="formMatriksAkses" action="{{ route('roles.give-permissions', $activeRole->id) }}"
                        method="POST">
                        @csrf
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead
                                    class="bg-zinc-100/60 dark:bg-zinc-800/60 border-b border-zinc-200/80 dark:border-zinc-800">
                                    <tr
                                        class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        <th
                                            class="py-3 px-4 border-r border-zinc-200/80 dark:border-zinc-800 w-1/3 min-w-[200px]">
                                            Menu / Modul Sistem</th>
                                        <th class="py-3 px-4 min-w-[300px]">Tindakan / Hak Akses (Permissions)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800 bg-transparent">

                                    @forelse($matrixMenus as $menu)
                                        <!-- BARIS MENU UTAMA -->
                                        <tr
                                            class="bg-zinc-50/30 dark:bg-zinc-900/30 group hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                                            <td
                                                class="py-3.5 px-4 border-l-[3px] border-emerald-500 border-r border-zinc-200/80 dark:border-zinc-800 align-top">
                                                <div class="flex items-center gap-2.5">
                                                    <div
                                                        class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-xs">
                                                        <i class="bi {{ $menu->icon ?? 'bi-folder-fill' }}"></i>
                                                    </div>
                                                    <span
                                                        class="font-black text-zinc-900 dark:text-white text-xs tracking-tight">
                                                        {{ $menu->name }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 align-top">
                                                <div class="flex flex-wrap gap-3.5">
                                                    @forelse($menu->permissions as $perm)
                                                        @php $isChecked = in_array($perm->name, $rolePermissions); @endphp
                                                        <label
                                                            class="relative inline-flex items-center cursor-pointer group"
                                                            title="Izin: {{ $perm->name }}">
                                                            <input type="checkbox" name="permissions[]"
                                                                value="{{ $perm->name }}" class="sr-only peer"
                                                                {{ $isChecked ? 'checked' : '' }}>
                                                            <div
                                                                class="relative w-7 h-3.5 bg-zinc-200 dark:bg-zinc-700 rounded-full peer peer-checked:after:translate-x-3.5 after:content-[''] after:absolute after:top-[1.5px] after:left-[1.5px] after:bg-white after:rounded-full after:h-2.5 after:w-2.5 after:transition-all peer-checked:bg-emerald-500 transition-colors">
                                                            </div>
                                                            <span
                                                                class="ml-2 text-[10px] font-bold text-zinc-600 dark:text-zinc-400 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 transition-colors">
                                                                {{ ucwords(str_replace(['-', '_'], ' ', $perm->name)) }}
                                                            </span>
                                                        </label>
                                                    @empty
                                                        <span class="text-[9px] font-semibold text-zinc-400 italic">--
                                                            Tidak ada aksi --</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- BARIS SUB-MENU -->
                                        @foreach ($menu->subMenus as $subMenu)
                                            <tr
                                                class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/40 transition-colors group">
                                                <td
                                                    class="py-3 px-4 pl-10 relative border-r border-zinc-200/80 dark:border-zinc-800 align-top">
                                                    <div
                                                        class="absolute left-6 top-0 bottom-1/2 w-px bg-zinc-300 dark:bg-zinc-700">
                                                    </div>
                                                    <div
                                                        class="absolute left-6 top-1/2 w-3 h-px bg-zinc-300 dark:bg-zinc-700">
                                                    </div>

                                                    <div class="flex items-center gap-2 relative z-10">
                                                        <div
                                                            class="w-1.5 h-1.5 rounded-full bg-zinc-400 dark:bg-zinc-500">
                                                        </div>
                                                        <span
                                                            class="font-bold text-zinc-700 dark:text-zinc-300 text-[11px] tracking-tight">
                                                            {{ $subMenu->name }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 align-top">
                                                    <div class="flex flex-wrap gap-3.5">
                                                        @forelse($subMenu->permissions as $subPerm)
                                                            @php $isChecked = in_array($subPerm->name, $rolePermissions); @endphp
                                                            <label
                                                                class="relative inline-flex items-center cursor-pointer group"
                                                                title="Izin: {{ $subPerm->name }}">
                                                                <input type="checkbox" name="permissions[]"
                                                                    value="{{ $subPerm->name }}" class="sr-only peer"
                                                                    {{ $isChecked ? 'checked' : '' }}>
                                                                <div
                                                                    class="relative w-7 h-3.5 bg-zinc-200 dark:bg-zinc-700 rounded-full peer peer-checked:after:translate-x-3.5 after:content-[''] after:absolute after:top-[1.5px] after:left-[1.5px] after:bg-white after:rounded-full after:h-2.5 after:w-2.5 after:transition-all peer-checked:bg-emerald-500 transition-colors">
                                                                </div>
                                                                <span
                                                                    class="ml-2 text-[10px] font-bold text-zinc-600 dark:text-zinc-400 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 transition-colors">
                                                                    {{ ucwords(str_replace(['-', '_'], ' ', $subPerm->name)) }}
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
                            message="Silakan pilih salah satu role di menu sebelah kiri untuk memuat matriks hak akses." />
                    </div>
                @endif

            </div>
        </div>

        @push('script')
            <script>
                const form = document.getElementById('formMatriksAkses');
                if (form) {
                    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
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

                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const formData = new FormData(form);
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
                                    if (data.status === 'success') {
                                        Toast.fire({
                                            icon: 'success',
                                            title: 'Izin Tersimpan!'
                                        });
                                    }
                                }).catch(() => {
                                    Toast.fire({
                                        icon: 'error',
                                        title: 'Gagal menyimpan!'
                                    });
                                    checkbox.checked = !checkbox.checked;
                                });
                        });
                    });
                }
            </script>
        @endpush
    </x-app-layout>
