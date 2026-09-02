<form action="{{ isset($menu) ? route('menu.update', $menu->id) : route('menu.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]" data-refresh-target="#data-grid-container">

    @csrf
    @if (isset($menu))
        @method('PUT')
    @endif

    <!-- Header Modal -->
    <div
        class="bg-white dark:bg-[#0c0c0e] border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between rounded-t-3xl">
        <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
            <div
                class="w-7 h-7 rounded-xl {{ isset($menu) ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }} flex items-center justify-center text-xs shrink-0">
                <i class="bi {{ isset($menu) ? 'bi-pencil-square' : 'bi-plus-lg' }}"></i>
            </div>
            {{ isset($menu) ? 'Edit Menu Navigasi' : 'Tambah Menu Baru' }}
        </h3>
        <button type="button" data-dismiss="modal"
            class="w-7 h-7 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 hover:bg-rose-500/10 hover:text-rose-500 transition-colors outline-none">
            <i class="bi bi-x-lg text-xs font-black"></i>
        </button>
    </div>

    <!-- Body Modal -->
    <div class="p-5 overflow-y-auto custom-scrollbar flex-1 space-y-4 bg-white dark:bg-[#0c0c0e]">

        <!-- Grid Atas: Nama Menu & Kategori -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <div>
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Nama Menu <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ isset($menu) ? $menu->name : old('name') }}"
                    placeholder="Cth: Data Santri" class="m3-input-glass w-full text-xs font-bold">
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Kategori / Grup (Opsional)
                </label>
                <input type="text" name="category" value="{{ isset($menu) ? $menu->category : old('category') }}"
                    placeholder="Cth: Master Data" class="m3-input-glass w-full text-xs font-bold">
            </div>
        </div>

        <!-- URL & Ikon -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    URL / Route <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="url" value="{{ isset($menu) ? $menu->url : old('url', '#') }}"
                    placeholder="Cth: murid.index atau /murid"
                    class="m3-input-glass w-full font-mono text-xs font-bold">
                <p class="text-[9px] font-semibold text-zinc-400 mt-1 ml-1">Gunakan "#" jika ini adalah Menu Utama
                    dengan Sub-Menu.</p>
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Ikon (Opsional)
                </label>
                <input type="text" name="icon" value="{{ isset($menu) ? $menu->icon : old('icon', 'bi-circle') }}"
                    placeholder="Cth: bi-people" class="m3-input-glass w-full text-xs font-bold">
            </div>
        </div>

        <hr class="border-zinc-200/80 dark:border-zinc-800">

        <!-- Induk Menu (Parent) & Urutan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Jadikan Sub-Menu Dari (Opsional)
                </label>
                <div class="relative">
                    <select name="main_menu_id" class="m3-select2 w-full text-xs font-bold">
                        <option value="">-- Menu Tingkat Pertama (Bukan Sub-Menu) --</option>
                        @foreach ($mainMenus as $parent)
                            <option value="{{ $parent->id }}"
                                {{ isset($menu) && $menu->main_menu_id == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-wider mb-1.5 ml-1">
                    Urutan
                </label>
                <input type="number" name="orders" value="{{ isset($menu) ? $menu->orders : old('orders', 0) }}"
                    class="m3-input-glass w-full text-xs font-bold">
            </div>
        </div>

        <!-- MAPPING PERMISSIONS (Checkbox M3) -->
        <div class="bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/20 p-3.5 rounded-2xl">
            <label
                class="block text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1 ml-1">
                <i class="bi bi-shield-lock-fill mr-1"></i> Level Menu (Hak Akses)
            </label>
            <p class="text-[10px] font-medium text-zinc-500 dark:text-zinc-400 mb-3 ml-1">
                Pilih tindakan apa saja yang diizinkan pada menu ini.
            </p>

            <div class="flex flex-wrap items-center gap-4 ml-1">
                @php
                    $attachedPermNames = isset($menu) ? $menu->permissions->pluck('name')->toArray() : [];
                @endphp

                @foreach (['create', 'read', 'update', 'delete'] as $item)
                    @php
                        $identifier = isset($menu)
                            ? ($menu->url === '#'
                                ? \Illuminate\Support\Str::slug($menu->name)
                                : $menu->url)
                            : '';

                        $expectedPerm = strtolower($item . ' ' . $identifier);
                        $isChecked = in_array($expectedPerm, $attachedPermNames);
                    @endphp
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="permissions[]" value="{{ $item }}"
                            class="w-4 h-4 rounded-md border-zinc-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500/30 bg-white dark:bg-zinc-900 cursor-pointer shadow-2xs"
                            {{ $isChecked ? 'checked' : '' }}>
                        <span
                            class="text-xs font-bold text-zinc-700 dark:text-zinc-300 group-hover:text-emerald-600 transition-colors">
                            {{ ucfirst($item) }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Status Aktif -->
        <div class="flex items-center gap-2.5 ml-1">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="isActiveCheck"
                class="w-4.5 h-4.5 rounded-md border-zinc-300 dark:border-zinc-600 text-emerald-600 focus:ring-emerald-500/30 cursor-pointer"
                {{ !isset($menu) || (isset($menu) && $menu->is_active) ? 'checked' : '' }}>
            <label for="isActiveCheck"
                class="text-xs font-bold text-zinc-700 dark:text-zinc-300 cursor-pointer select-none">
                Aktifkan Menu Ini
            </label>
        </div>

    </div>

    <!-- Footer Modal -->
    <div
        class="bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 flex justify-end gap-2.5 rounded-b-3xl">
        <button type="button" data-dismiss="modal"
            class="px-4 py-2 rounded-xl text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors outline-none shadow-2xs">
            Batal
        </button>
        <button type="submit"
            class="{{ isset($menu) ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'm3-btn-primary' }} px-4 py-2 rounded-xl text-xs font-black shadow-2xs flex items-center gap-1.5">
            <i class="bi bi-save2-fill text-xs"></i>
            <span>{{ isset($menu) ? 'Simpan Perubahan' : 'Simpan Menu' }}</span>
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.ajax-form .m3-select2').select2({
            width: '100%',
            dropdownParent: $('#modal-action'),
        });

        $('.ajax-form .m3-select2-multi').select2({
            width: '100%',
            dropdownParent: $('#modal-action'),
            placeholder: "  Klik di sini untuk memilih permissions...",
            allowClear: true,
            closeOnSelect: false
        });
    });
</script>
