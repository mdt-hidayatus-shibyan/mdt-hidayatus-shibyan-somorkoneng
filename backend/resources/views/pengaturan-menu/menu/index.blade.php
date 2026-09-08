@section('title', 'Manajemen Menu & Navigasi')

<x-app-layout>
    <!-- Header Page & Stats -->
    <div class="mb-6 md:mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2
                    class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <div
                        class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xl shrink-0">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <span>Menu & Navigasi</span>
                </h2>
                <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-1">
                    Atur struktur menu, kategori, route, hak akses, dan hierarki drag-and-drop navigasi sistem.
                </p>
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <button type="button" id="btnSaveOrder"
                    class="m3-btn-primary shrink-0 h-10 px-4 rounded-xl flex items-center gap-2 opacity-50 cursor-not-allowed text-xs font-black shadow-2xs"
                    disabled>
                    <i class="bi bi-layer-forward text-sm"></i>
                    <span>Simpan Susunan</span>
                </button>
                <a href="{{ route('menu.create') }}"
                    class="action-modal shrink-0 h-10 px-4 rounded-xl flex items-center justify-center bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors shadow-2xs text-xs font-black">
                    <i class="bi bi-plus-lg text-sm"></i>
                    <span class="ml-1.5">Tambah Menu</span>
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
            <div class="m3-glass-card p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800">
                <div class="text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">Total Menu Utama</div>
                <div class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white">{{ $totalMenus ?? 0 }}</div>
            </div>
            <div class="m3-glass-card p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800">
                <div class="text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">Total Sub-Menu</div>
                <div class="text-xl md:text-2xl font-black text-emerald-600 dark:text-emerald-400">
                    {{ $totalSubMenus ?? 0 }}</div>
            </div>
            <div class="m3-glass-card p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800">
                <div class="text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">Kategori Terdaftar</div>
                <div class="text-xl md:text-2xl font-black text-blue-600 dark:text-blue-400">
                    {{ count($categories ?? []) }}</div>
            </div>
            <div class="m3-glass-card p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800">
                <div class="text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">Status Sesi Menu</div>
                <div class="flex items-center gap-1.5 mt-1">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Tersinkron
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div
            class="m3-glass-card p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 flex flex-col md:flex-row items-center justify-between gap-3">
            <!-- Category Pills Filter -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 custom-scrollbar">
                <a href="{{ route('menu.index') }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-colors {{ empty($selectedCategory) ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-2xs' : 'bg-zinc-100 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                    Semua Kategori
                </a>
                @if (isset($categories))
                    @foreach ($categories as $cat)
                        <a href="{{ route('menu.index', ['category' => $cat]) }}"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-colors {{ $selectedCategory === $cat ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-zinc-100 dark:bg-zinc-800/60 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                @endif
            </div>

            <!-- Search Form -->
            <form action="{{ route('menu.index') }}" method="GET" class="w-full md:w-72 shrink-0">
                @if ($selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                    <input type="text" name="q" value="{{ $searchQuery ?? '' }}"
                        placeholder="Cari nama atau route menu..."
                        class="m3-input-glass w-full pl-8 pr-8 py-2 text-xs font-bold rounded-xl">
                    @if (!empty($searchQuery))
                        <a href="{{ route('menu.index', array_filter(['category' => $selectedCategory])) }}"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 text-xs">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Drag & Drop Container -->
    <div class="m3-glass-card rounded-3xl p-5 md:p-7 min-h-[500px] shadow-2xs" id="data-grid-container">

        @if (isset($menus) && $menus->count() > 0)
            <div class="dd" id="nestable-menu">
                <ol class="dd-list">
                    @foreach ($menus as $menu)
                        @php
                            $catName = $menu->category ?: 'UTAMA';
                            $badgeColor = match ($catName) {
                                'KEUANGAN'
                                    => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                                'AKADEMIK' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
                                'MASTER DATA'
                                    => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
                                'PENGATURAN'
                                    => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                'ARSIP DOKUMEN' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-500/20',
                                'KESEKRETARISAN'
                                    => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                                'TAGIHAN & PEMBAYARAN'
                                    => 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-500/20',
                                'LAYANAN & BANTUAN'
                                    => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                                default => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20',
                            };
                            $isRouteValid = $menu->url === '#' || Route::has($menu->url);
                        @endphp

                        <!-- ================= MENU UTAMA ================= -->
                        <li class="dd-item group" data-id="{{ $menu->id }}">

                            <div
                                class="menu-row bg-white/70 dark:bg-zinc-900/70 backdrop-blur-md border border-zinc-200/80 dark:border-zinc-800 rounded-2xl flex items-stretch mb-3 shadow-2xs hover:border-emerald-500/40 transition-colors {{ !$menu->is_active ? 'opacity-60 bg-zinc-100/50' : '' }}">
                                <!-- Handle Drag -->
                                <div
                                    class="dd-handle w-11 flex-shrink-0 flex items-center justify-center cursor-grab bg-zinc-100/60 dark:bg-zinc-800/50 rounded-l-2xl border-r border-zinc-200/80 dark:border-zinc-800 text-zinc-400 m-0 hover:bg-emerald-500/10 hover:text-emerald-600 transition-colors">
                                    <i class="bi bi-grip-vertical text-lg pointer-events-none"></i>
                                </div>

                                <!-- Konten Menu Utama -->
                                <div
                                    class="flex-1 p-3 flex flex-col md:flex-row md:items-center justify-between gap-2.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="menu-number text-xs font-black text-zinc-400 w-6 text-right select-none shrink-0">
                                        </div>
                                        <div
                                            class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm shrink-0">
                                            <i class="bi {{ $menu->icon ?? 'bi-folder' }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4
                                                    class="font-black text-xs md:text-sm text-zinc-900 dark:text-white truncate">
                                                    {{ $menu->name }}
                                                </h4>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase border {{ $badgeColor }}">
                                                    {{ $catName }}
                                                </span>
                                                @if ($menu->subMenus && $menu->subMenus->count() > 0)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border border-zinc-200 dark:border-zinc-700">
                                                        <i class="bi bi-folder2-open"></i>
                                                        {{ $menu->subMenus->count() }} Sub-Menu
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5 text-[10px]">
                                                <span
                                                    class="font-mono font-semibold {{ $isRouteValid ? 'text-zinc-500 dark:text-zinc-400' : 'text-rose-500' }}">
                                                    {{ $menu->url }}
                                                </span>
                                                @if ($menu->permissions && $menu->permissions->count() > 0)
                                                    <span class="text-zinc-400">•</span>
                                                    <span class="text-zinc-400 font-medium">
                                                        <i class="bi bi-shield-check text-[10px] text-emerald-500"></i>
                                                        {{ $menu->permissions->count() }} Hak Akses
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-1.5 shrink-0 self-end md:self-center">
                                        <!-- Quick Active Toggle -->
                                        <button type="button" onclick="toggleMenuActive({{ $menu->id }}, this)"
                                            class="px-2 py-1 rounded-lg text-[10px] font-extrabold border transition-colors {{ $menu->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 border-zinc-300 dark:border-zinc-700 hover:bg-zinc-300' }}"
                                            title="Klik untuk ubah status aktif/non-aktif">
                                            <i
                                                class="bi {{ $menu->is_active ? 'bi-toggle-on text-emerald-500' : 'bi-toggle-off text-zinc-400' }} text-xs mr-1"></i>
                                            <span>{{ $menu->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                                        </button>

                                        @can('update menu')
                                            <a href="{{ route('menu.edit', $menu->id) }}"
                                                class="action-modal w-8 h-8 flex items-center justify-center rounded-xl bg-amber-500/10 text-amber-500 hover:bg-amber-500/20 border border-amber-500/20 transition-colors outline-none"
                                                title="Edit Menu">
                                                <i class="bi bi-pencil-square text-xs"></i>
                                            </a>
                                        @endcan
                                        @can('delete menu')
                                            <form action="{{ route('menu.destroy', $menu->id) }}" method="POST"
                                                class="m-0 delete-ajax">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 hover:bg-rose-500/20 border border-rose-500/20 transition-colors outline-none"
                                                    title="Hapus Menu">
                                                    <i class="bi bi-trash3-fill text-xs"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUB MENU ================= -->
                            @if ($menu->subMenus && $menu->subMenus->count() > 0)
                                <ol class="dd-list">
                                    @foreach ($menu->subMenus as $sub)
                                        @php
                                            $isSubRouteValid = $sub->url === '#' || Route::has($sub->url);
                                        @endphp
                                        <li class="dd-item group" data-id="{{ $sub->id }}">

                                            <div
                                                class="menu-row bg-white/50 dark:bg-zinc-900/50 backdrop-blur-md border border-zinc-200/60 dark:border-zinc-800/80 rounded-xl flex items-stretch mb-2.5 shadow-2xs hover:border-emerald-500/30 transition-colors {{ !$sub->is_active ? 'opacity-60 bg-zinc-100/40' : '' }}">
                                                <!-- Handle Drag Sub-Menu -->
                                                <div
                                                    class="dd-handle w-9 flex-shrink-0 flex items-center justify-center cursor-grab bg-zinc-100/40 dark:bg-zinc-900/40 rounded-l-xl border-r border-zinc-200/60 dark:border-zinc-800 text-zinc-400 m-0 hover:bg-emerald-500/10 hover:text-emerald-600 transition-colors">
                                                    <i class="bi bi-grip-vertical text-base pointer-events-none"></i>
                                                </div>

                                                <!-- Konten Sub-Menu -->
                                                <div
                                                    class="flex-1 p-2.5 flex flex-col md:flex-row md:items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="menu-number text-[11px] font-black text-zinc-400 w-5 text-right select-none shrink-0">
                                                        </div>
                                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <h4
                                                                class="font-bold text-xs text-zinc-800 dark:text-zinc-200">
                                                                {{ $sub->name }}
                                                            </h4>
                                                            <span
                                                                class="text-[9px] font-mono font-semibold {{ $isSubRouteValid ? 'text-zinc-400 bg-zinc-200/60 dark:bg-zinc-800/80' : 'text-rose-500 bg-rose-500/10 border border-rose-500/20' }} px-1.5 py-0.5 rounded-md">
                                                                {{ $sub->url }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="flex items-center justify-end gap-1.5 shrink-0 self-end md:self-center">
                                                        <!-- Quick Active Toggle Sub -->
                                                        <button type="button"
                                                            onclick="toggleMenuActive({{ $sub->id }}, this)"
                                                            class="px-2 py-0.5 rounded-md text-[9px] font-bold border transition-colors {{ $sub->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500 border-zinc-300 dark:border-zinc-700' }}"
                                                            title="Ubah status aktif">
                                                            {{ $sub->is_active ? 'Aktif' : 'Off' }}
                                                        </button>

                                                        @can('update menu')
                                                            <a href="{{ route('menu.edit', $sub->id) }}"
                                                                class="action-modal w-7 h-7 flex items-center justify-center rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500/20 transition-colors"
                                                                title="Edit Sub-Menu">
                                                                <i class="bi bi-pencil-square text-xs"></i>
                                                            </a>
                                                        @endcan
                                                        @can('delete menu')
                                                            <form action="{{ route('menu.destroy', $sub->id) }}"
                                                                method="POST" class="m-0 delete-ajax">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-rose-500/10 text-rose-500 hover:bg-rose-500/20 transition-colors"
                                                                    title="Hapus Sub-Menu">
                                                                    <i class="bi bi-trash3-fill text-xs"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>

                                        </li>
                                    @endforeach
                                </ol>
                            @endif

                        </li>
                    @endforeach
                </ol>
            </div>
        @else
            <div class="text-center py-12 flex flex-col items-center justify-center h-full min-h-[300px]">
                <x-empty-state icon="bi-menu-app" title="Belum Ada Menu"
                    message="Belum ada menu navigasi yang cocok dengan filter atau kata kunci yang dicari. Silakan sesuaikan filter atau tambah menu baru." />
            </div>
        @endif

    </div>

    @push('style')
        <style>
            .dd {
                position: relative;
                display: block;
                margin: 0;
                padding: 0;
                max-width: 100%;
                list-style: none;
                font-size: 13px;
                line-height: 20px;
            }

            .dd-list {
                display: block;
                position: relative;
                margin: 0;
                padding: 0;
                list-style: none;
            }

            .dd-list .dd-list {
                padding-left: 36px;
            }

            .dd-item,
            .dd-empty,
            .dd-placeholder {
                display: block;
                position: relative;
                margin: 0;
                padding: 0;
                min-height: 20px;
            }

            .dd-placeholder {
                margin-bottom: 12px;
                min-height: 60px;
                background: rgba(16, 185, 129, 0.05);
                border: 2px dashed rgba(16, 185, 129, 0.4);
                border-radius: 1rem;
            }

            .dd-dragel {
                position: absolute;
                pointer-events: none !important;
                z-index: 9999;
            }

            .dd-dragel * {
                transition: none !important;
                animation: none !important;
            }

            .dd-dragel>.dd-item>.menu-row {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
                border-color: rgba(16, 185, 129, 0.5);
                margin-bottom: 0 !important;
                cursor: grabbing;
            }

            .dd-handle {
                cursor: grab;
            }

            .dd-handle:active {
                cursor: grabbing !important;
            }

            .dd-item>button {
                display: none !important;
            }

            .sortable-list,
            .dd-list {
                counter-reset: nomor-menu;
            }

            .sortable-item,
            .dd-item {
                counter-increment: nomor-menu;
            }

            .menu-number::before {
                content: counters(nomor-menu, ".");
            }
        </style>
    @endpush

    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nestable2/1.6.0/jquery.nestable.min.js"></script>

        <script>
            function toggleMenuActive(menuId, buttonEl) {
                const btn = $(buttonEl);
                btn.prop('disabled', true).addClass('opacity-50');

                $.ajax({
                    url: `/menu/${menuId}/toggle-active`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        btn.prop('disabled', false).removeClass('opacity-50');
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500,
                            icon: 'success',
                            title: res.message
                        });
                        setTimeout(() => window.location.reload(), 500);
                    },
                    error: function() {
                        btn.prop('disabled', false).removeClass('opacity-50');
                        Swal.fire('Oops!', 'Gagal mengubah status menu.', 'error');
                    }
                });
            }

            $(document).ready(function() {
                if ($('#nestable-menu').length) {
                    $('#nestable-menu').nestable({
                        maxDepth: 2,
                        scroll: true
                    });

                    const btnSave = $('#btnSaveOrder');

                    $('#nestable-menu').on('change', function() {
                        btnSave.removeClass('opacity-50 cursor-not-allowed')
                            .addClass('cursor-pointer shadow-lg')
                            .prop('disabled', false);
                    });

                    btnSave.on('click', function() {
                        const button = $(this);
                        const serializedData = JSON.stringify($('#nestable-menu').nestable('serialize'));
                        const originalHtml = button.html();

                        button.html(
                            '<i class="bi bi-hourglass-split animate-spin text-sm"></i><span>Menyimpan...</span>'
                        );
                        button.prop('disabled', true);

                        $.ajax({
                            url: "{{ route('menu.update-order') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                menu_order: serializedData
                            },
                            success: function(response) {
                                button.html(originalHtml)
                                    .addClass('opacity-50 cursor-not-allowed')
                                    .removeClass('shadow-lg');

                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    icon: 'success',
                                    title: response.message,
                                    customClass: {
                                        popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 !p-3'
                                    }
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(err) {
                                button.html(originalHtml).prop('disabled', false);
                                Swal.fire('Oops!', 'Gagal menyimpan susunan menu.', 'error');
                            }
                        });
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
