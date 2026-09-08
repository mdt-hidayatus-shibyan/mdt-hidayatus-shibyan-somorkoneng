<form action="{{ isset($menu) ? route('menu.update', $menu->id) : route('menu.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]" data-refresh-target="#data-grid-container">

    @csrf
    @if (isset($menu))
        @method('PUT')
    @endif

    <!-- Header Modal -->
    <div
        class="bg-white dark:bg-[#0c0c0e] border-b border-zinc-200/80 dark:border-zinc-800 px-6 py-4 flex items-center justify-between rounded-t-3xl">
        <h3
            class="text-sm md:text-base font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-xl {{ isset($menu) ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center text-sm shrink-0">
                <i class="bi {{ isset($menu) ? 'bi-pencil-square' : 'bi-plus-lg' }}"></i>
            </div>
            <span>{{ isset($menu) ? 'Edit Menu Navigasi' : 'Tambah Menu Baru' }}</span>
        </h3>
        <button type="button" data-dismiss="modal"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 hover:bg-rose-500/10 hover:text-rose-500 transition-colors outline-none">
            <i class="bi bi-x-lg text-xs font-black"></i>
        </button>
    </div>

    <!-- Body Modal -->
    <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4 bg-white dark:bg-[#0c0c0e]">

        <!-- Grid Atas: Nama Menu & Kategori -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-0.5">
                    Nama Menu <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="menuNameInput"
                    value="{{ isset($menu) ? $menu->name : old('name') }}" placeholder="Cth: Data Murid atau Tabungan"
                    required class="m3-input-glass w-full text-xs font-bold px-3.5 py-2.5 rounded-xl">
            </div>
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-0.5">
                    Kategori / Grup Menu
                </label>
                <div class="relative">
                    <input type="text" name="category" id="menuCategoryInput" list="categoryList"
                        value="{{ isset($menu) ? $menu->category : old('category') }}"
                        placeholder="Pilih atau ketik kategori..."
                        class="m3-input-glass w-full text-xs font-bold uppercase px-3.5 py-2.5 rounded-xl">
                    <datalist id="categoryList">
                        @if (isset($categories))
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        @endif
                    </datalist>
                </div>
                <p class="text-[10px] font-medium text-zinc-400 mt-1 ml-0.5">
                    Cth: UTAMA, AKADEMIK, KEUANGAN, MASTER DATA, PENGATURAN
                </p>
            </div>
        </div>

        <!-- URL / Route Name & Ikon -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-1.5 ml-0.5">
                    <label
                        class="block text-[11px] font-black text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                        URL / Route Name <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" onclick="$('#menuUrlInput').val('#').trigger('change')"
                        class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                        Set "#" (Dropdown Parent)
                    </button>
                </div>
                <div class="relative">
                    <input type="text" name="url" id="menuUrlInput" list="routeList"
                        value="{{ isset($menu) ? $menu->url : old('url', '#') }}"
                        placeholder="Cth: tabungan.rekening.index atau /murid atau #" required
                        class="m3-input-glass w-full font-mono text-xs font-bold px-3.5 py-2.5 rounded-xl">
                    <datalist id="routeList">
                        <option value="#"># (Menu Induk / Dropdown Sub-Menu)</option>
                        @if (isset($routes))
                            @foreach ($routes as $r)
                                <option value="{{ $r['name'] }}">{{ $r['name'] }} ({{ $r['uri'] }})</option>
                            @endforeach
                        @endif
                    </datalist>
                </div>
                <p class="text-[10px] font-medium text-zinc-400 mt-1 ml-0.5">
                    Ketik nama route (autocomplete tersedia) atau <code class="font-mono text-emerald-500">#</code> jika
                    menu ini memiliki sub-menu.
                </p>
            </div>

            <div>
                <label
                    class="block text-[11px] font-black text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-0.5">
                    Ikon Bootstrap
                </label>
                <div class="flex items-center gap-2">
                    <div id="iconPreview"
                        class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-lg text-emerald-600 dark:text-emerald-400 shrink-0">
                        <i class="bi {{ isset($menu) && $menu->icon ? $menu->icon : 'bi-circle' }}"></i>
                    </div>
                    <input type="text" name="icon" id="menuIconInput"
                        value="{{ isset($menu) ? $menu->icon : old('icon', 'bi-circle') }}"
                        placeholder="Cth: bi-wallet2"
                        class="m3-input-glass w-full text-xs font-bold px-3 py-2.5 rounded-xl">
                </div>
                <p class="text-[10px] font-medium text-zinc-400 mt-1 ml-0.5">
                    Prefix class <a href="https://icons.getbootstrap.com" target="_blank"
                        class="text-emerald-500 underline">Bootstrap Icons</a>
                </p>
            </div>
        </div>

        <hr class="border-zinc-200/80 dark:border-zinc-800">

        <!-- Induk Menu (Parent) & Urutan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label
                    class="block text-[11px] font-black text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-0.5">
                    Jadikan Sub-Menu Dari (Induk)
                </label>
                <div class="relative">
                    <select name="main_menu_id" id="mainMenuSelect" class="m3-select2 w-full text-xs font-bold">
                        <option value="">-- Menu Tingkat Pertama (Menu Utama / Mandiri) --</option>
                        @foreach ($mainMenus as $parent)
                            <option value="{{ $parent->id }}" data-category="{{ $parent->category }}"
                                {{ isset($menu) && $menu->main_menu_id == $parent->id ? 'selected' : '' }}>
                                [{{ $parent->category ?: 'UTAMA' }}] {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="text-[10px] font-medium text-zinc-400 mt-1 ml-0.5">
                    Kosongkan jika ini menu tingkat atas / utama.
                </p>
            </div>
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-600 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-0.5">
                    Nomor Urutan
                </label>
                <input type="number" name="orders" min="0"
                    value="{{ isset($menu) ? $menu->orders : old('orders', 0) }}"
                    class="m3-input-glass w-full text-xs font-bold px-3.5 py-2.5 rounded-xl">
                <p class="text-[10px] font-medium text-zinc-400 mt-1 ml-0.5">
                    Urutan tampil (ASC).
                </p>
            </div>
        </div>

        <!-- MAPPING PERMISSIONS (Checkbox M3) -->
        <div class="bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl">
            <div class="flex items-center justify-between mb-1.5">
                <label
                    class="block text-[11px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                    <i class="bi bi-shield-lock-fill mr-1"></i> Level Menu & Hak Akses (Permissions)
                </label>
                <span class="text-[10px] font-bold text-zinc-400">Otomatis sinkron ke Role Admin</span>
            </div>
            <p class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 mb-3">
                Pilih tindakan apa saja yang diatur hak aksesnya untuk menu ini:
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @php
                    $attachedPermNames = isset($menu) ? $menu->permissions->pluck('name')->toArray() : [];
                @endphp

                @foreach ([
        'read' => ['label' => 'Lihat / Read', 'icon' => 'bi-eye-fill', 'color' => 'text-blue-500'],
        'create' => ['label' => 'Tambah / Create', 'icon' => 'bi-plus-circle-fill', 'color' => 'text-emerald-500'],
        'update' => ['label' => 'Ubah / Update', 'icon' => 'bi-pencil-square', 'color' => 'text-amber-500'],
        'delete' => ['label' => 'Hapus / Delete', 'icon' => 'bi-trash3-fill', 'color' => 'text-rose-500'],
    ] as $actKey => $actMeta)
                    @php
                        $identifier = isset($menu)
                            ? ($menu->url === '#'
                                ? \Illuminate\Support\Str::slug($menu->name)
                                : $menu->url)
                            : '';

                        $expectedPerm = strtolower($actKey . ' ' . $identifier);
                        $isChecked =
                            !isset($menu) ||
                            in_array($expectedPerm, $attachedPermNames) ||
                            in_array($actKey, $attachedPermNames);
                    @endphp
                    <label
                        class="flex items-center gap-2 p-2.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white/70 dark:bg-zinc-900/60 cursor-pointer hover:border-emerald-500/40 transition-colors shadow-2xs">
                        <input type="checkbox" name="permissions[]" value="{{ $actKey }}"
                            class="w-4 h-4 rounded-md border-zinc-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500/30 bg-white dark:bg-zinc-900 cursor-pointer"
                            {{ $isChecked ? 'checked' : '' }}>
                        <div class="flex items-center gap-1.5">
                            <i class="bi {{ $actMeta['icon'] }} {{ $actMeta['color'] }} text-xs"></i>
                            <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">
                                {{ $actMeta['label'] }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Status Aktif -->
        <div
            class="flex items-center gap-3 p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200/60 dark:border-zinc-800">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="isActiveCheck"
                class="w-4.5 h-4.5 rounded-md border-zinc-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500/30 cursor-pointer"
                {{ !isset($menu) || (isset($menu) && $menu->is_active) ? 'checked' : '' }}>
            <label for="isActiveCheck"
                class="text-xs font-bold text-zinc-700 dark:text-zinc-300 cursor-pointer select-none">
                Aktifkan Menu Ini (Tampilkan di Sidebar Navigasi)
            </label>
        </div>

    </div>

    <!-- Footer Modal -->
    <div
        class="bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200/80 dark:border-zinc-800 px-6 py-4 flex justify-end gap-2.5 rounded-b-3xl">
        <button type="button" data-dismiss="modal"
            class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors outline-none shadow-2xs">
            Batal
        </button>
        <button type="submit"
            class="{{ isset($menu) ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'm3-btn-primary' }} px-5 py-2 rounded-xl text-xs font-black shadow-2xs flex items-center gap-1.5">
            <i class="bi {{ isset($menu) ? 'bi-check2-circle' : 'bi-plus-lg' }} text-xs"></i>
            <span>{{ isset($menu) ? 'Simpan Perubahan' : 'Simpan Menu' }}</span>
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.ajax-form .m3-select2').select2({
            width: '100%',
            dropdownParent: $('#modal-action'),
        });

        // Live Icon Preview
        $('#menuIconInput').on('input change', function() {
            const iconClass = $(this).val().trim() || 'bi-circle';
            $('#iconPreview i').attr('class', 'bi ' + iconClass);
        });

        // Auto-inherit category when parent is selected if category is blank
        $('#mainMenuSelect').on('change', function() {
            const selectedOption = $(this).find(':selected');
            const parentCategory = selectedOption.data('category');
            const catInput = $('#menuCategoryInput');
            if (parentCategory && (!catInput.val() || catInput.val() === 'UTAMA')) {
                catInput.val(parentCategory);
            }
        });
    });
</script>
