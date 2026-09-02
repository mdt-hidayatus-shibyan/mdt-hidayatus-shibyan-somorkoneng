@section('title', 'Manajemen Menu')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Menu & Navigasi
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Drag and drop untuk mengatur urutan dan hierarki menu sidebar.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <button type="button" id="btnSaveOrder"
                class="m3-btn-primary shrink-0 h-10 px-4 rounded-xl flex items-center gap-1.5 opacity-50 cursor-not-allowed text-xs font-black shadow-2xs"
                disabled>
                <i class="bi bi-layer-forward text-sm"></i>
                <span>Simpan Urutan</span>
            </button>
            <a href="{{ route('menu.create') }}"
                class="action-modal shrink-0 h-10 px-4 rounded-xl flex items-center justify-center bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-colors shadow-2xs text-xs font-black">
                <i class="bi bi-plus-lg text-sm"></i>
                <span class="ml-1.5">Tambah Menu</span>
            </a>
        </div>
    </div>

    <!-- Drag & Drop Container -->
    <div class="m3-glass-card rounded-3xl p-5 md:p-7 min-h-[500px] shadow-2xs">

        @if (isset($menus) && $menus->count() > 0)
            <div class="dd" id="nestable-menu">
                <ol class="dd-list">
                    @foreach ($menus as $menu)
                        <!-- ================= MENU UTAMA ================= -->
                        <li class="dd-item group" data-id="{{ $menu->id }}">

                            <div
                                class="menu-row bg-white/60 dark:bg-zinc-900/60 backdrop-blur-md border border-zinc-200/80 dark:border-zinc-800 rounded-2xl flex items-stretch mb-3 shadow-2xs hover:border-emerald-500/40 transition-colors">
                                <!-- Handle Drag -->
                                <div
                                    class="dd-handle w-11 flex-shrink-0 flex items-center justify-center cursor-grab bg-zinc-100/50 dark:bg-zinc-800/40 rounded-l-2xl border-r border-zinc-200/80 dark:border-zinc-800 text-zinc-400 m-0 hover:bg-emerald-500/10 hover:text-emerald-600 transition-colors">
                                    <i class="bi bi-grip-vertical text-lg pointer-events-none"></i>
                                </div>

                                <!-- Konten Menu Utama -->
                                <div class="flex-1 p-3 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="menu-number text-xs font-black text-zinc-400 w-5 text-right select-none">
                                        </div>
                                        <div
                                            class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm shrink-0">
                                            <i class="bi {{ $menu->icon ?? 'bi-folder' }}"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-black text-xs md:text-sm text-zinc-900 dark:text-white">
                                                {{ $menu->name }}</h4>
                                            <span
                                                class="text-[10px] font-mono font-semibold text-zinc-400">{{ $menu->url }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        @can('update menu')
                                            <a href="{{ route('menu.edit', $menu->id) }}"
                                                class="action-modal w-7 h-7 flex items-center justify-center rounded-xl text-amber-500 hover:bg-amber-500/10 transition-colors outline-none"
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
                                                    class="w-7 h-7 flex items-center justify-center rounded-xl text-rose-500 hover:bg-rose-500/10 transition-colors outline-none"
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
                                        <li class="dd-item group" data-id="{{ $sub->id }}">

                                            <div
                                                class="menu-row bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md border border-zinc-200/60 dark:border-zinc-800/80 rounded-xl flex items-stretch mb-2.5 shadow-2xs hover:border-emerald-500/30 transition-colors">
                                                <!-- Handle Drag Sub-Menu -->
                                                <div
                                                    class="dd-handle w-9 flex-shrink-0 flex items-center justify-center cursor-grab bg-zinc-100/40 dark:bg-zinc-900/40 rounded-l-xl border-r border-zinc-200/60 dark:border-zinc-800 text-zinc-400 m-0 hover:bg-emerald-500/10 hover:text-emerald-600 transition-colors">
                                                    <i class="bi bi-grip-vertical text-base pointer-events-none"></i>
                                                </div>

                                                <!-- Konten Sub-Menu -->
                                                <div class="flex-1 p-2.5 flex items-center justify-between">
                                                    <div class="flex items-center gap-2.5">
                                                        <div
                                                            class="menu-number text-[11px] font-black text-zinc-400 w-5 text-right select-none">
                                                        </div>
                                                        <div class="w-1.5 h-1.5 rounded-full bg-zinc-400"></div>
                                                        <h4 class="font-black text-xs text-zinc-700 dark:text-zinc-300">
                                                            {{ $sub->name }}</h4>
                                                        <span
                                                            class="text-[9px] font-mono font-semibold text-zinc-400 bg-zinc-200/60 dark:bg-zinc-800/80 px-1.5 py-0.5 rounded-md">{{ $sub->url }}</span>
                                                    </div>

                                                    <div class="flex items-center gap-1">
                                                        @can('update menu')
                                                            <a href="{{ route('menu.edit', $sub->id) }}"
                                                                class="action-modal w-6 h-6 flex items-center justify-center rounded-lg text-amber-500 hover:bg-amber-500/10 transition-colors"
                                                                title="Edit Sub-Menu">
                                                                <i class="bi bi-pencil-square text-[10px]"></i>
                                                            </a>
                                                        @endcan
                                                        @can('delete menu')
                                                            <form action="{{ route('menu.destroy', $sub->id) }}"
                                                                method="POST" class="m-0 delete-ajax">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="w-6 h-6 flex items-center justify-center rounded-lg text-rose-500 hover:bg-rose-500/10 transition-colors"
                                                                    title="Hapus Sub-Menu">
                                                                    <i class="bi bi-trash3-fill text-[10px]"></i>
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
                    message="Belum ada menu navigasi yang terdaftar. Silakan tambah menu baru." />
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
                                Swal.fire('Oops!', 'Gagal menyimpan.', 'error');
                            }
                        });
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
