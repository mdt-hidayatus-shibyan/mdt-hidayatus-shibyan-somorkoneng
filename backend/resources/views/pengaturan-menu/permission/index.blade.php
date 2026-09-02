@section('title', 'Manajemen Hak Akses (Permissions)')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Hak Akses Dasar (Permissions)
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Kelola daftar nama hak akses yang akan disematkan ke modul dan role.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <!-- Form Pencarian -->
            <form action="{{ route('permissions.index') }}" method="GET" class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari hak akses..."
                    class="m3-input-glass w-full !pl-9 text-xs font-bold">
            </form>

            <!-- Tombol Tambah (AJAX Modal) -->
            @can('create permissions')
                <a href="{{ route('permissions.create') }}"
                    class="action-modal m3-btn-primary shrink-0 h-10 px-4 rounded-xl flex items-center gap-1.5 text-xs font-black shadow-2xs">
                    <i class="bi bi-plus-lg text-sm"></i>
                    <span class="hidden sm:inline">Tambah Data</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Table Card -->
    <div class="m3-glass-card rounded-3xl overflow-hidden shadow-2xs relative z-10">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr
                        class="bg-zinc-100/60 dark:bg-zinc-800/60 border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        <th class="px-4 py-3.5 whitespace-nowrap w-14 text-center">No</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Nama Izin (Permission)</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-center">Guard</th>
                        <th class="px-4 py-3.5 whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800">
                    @forelse ($permissions as $index => $item)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors group">
                            <td class="px-4 py-3 text-center align-middle font-bold text-zinc-400">
                                {{ $permissions->firstItem() + $index }}
                            </td>

                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 text-xs">
                                        <i class="bi bi-key-fill"></i>
                                    </div>
                                    <span class="font-black text-xs text-zinc-900 dark:text-white tracking-tight">
                                        {{ $item->name }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-middle text-center">
                                <span
                                    class="px-2 py-0.5 rounded-lg bg-white/40 dark:bg-black/40 text-[10px] font-mono font-bold text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 shadow-2xs">
                                    {{ $item->guard_name }}
                                </span>
                            </td>

                            <td class="px-4 py-3 align-middle text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @can('update permissions')
                                        <a href="{{ route('permissions.edit', $item->id) }}"
                                            class="action-modal w-7 h-7 rounded-lg text-amber-500 hover:bg-amber-500/10 flex items-center justify-center transition-colors outline-none"
                                            title="Edit">
                                            <i class="bi bi-pencil-square text-xs"></i>
                                        </a>
                                    @endcan
                                    @can('delete permissions')
                                        <form action="{{ route('permissions.destroy', $item->id) }}" method="POST"
                                            class="m-0 delete-ajax">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-7 h-7 rounded-lg text-rose-500 hover:bg-rose-500/10 flex items-center justify-center transition-colors outline-none"
                                                title="Hapus">
                                                <i class="bi bi-trash3-fill text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center">
                                <x-empty-state icon="bi-shield-x" title="Data Kosong"
                                    message="Belum ada hak akses (permission) yang didaftarkan." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($permissions->hasPages())
        <div class="mt-4 m3-glass-card p-4 rounded-2xl relative z-10 shadow-2xs">
            {{ $permissions->links('vendor.pagination.custom') }}
        </div>
    @endif

</x-app-layout>
