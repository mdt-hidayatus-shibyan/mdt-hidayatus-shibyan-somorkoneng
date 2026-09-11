<form
    action="{{ isset($kategori) ? route('keuangan.kategori.update', $kategori->id) : route('keuangan.kategori.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($kategori))
        @method('PUT')
    @endif

    <!-- Modal Header (Compact M3) -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 px-5 py-4 flex items-center justify-between transition-colors">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg font-black border border-purple-500/20 shadow-2xs">
                <i class="bi bi-tags-fill"></i>
            </div>
            <div>
                <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    {{ isset($kategori) ? 'Edit Kategori Keuangan' : 'Tambah Kategori Keuangan' }}
                </h3>
                <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest mt-0.5">
                    Klasifikasi Pos Transaksi & Aliran Dana
                </p>
            </div>
        </div>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shadow-2xs">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">
        <!-- Induk Kategori (Parent) -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Induk Kategori (Parent)
            </label>
            <select name="parent_id" id="form_kategori_parent_id" onchange="syncParentJenis(this)"
                class="m3-input-glass w-full text-xs font-bold">
                <option value="" data-jenis="">-- Kategori Utama (Tanpa Induk) --</option>
                @foreach ($parentList as $p)
                    <option value="{{ $p->id }}" data-jenis="{{ $p->jenis }}"
                        {{ (isset($kategori) && $kategori->parent_id == $p->id) || old('parent_id') == $p->id ? 'selected' : '' }}>
                        [{{ strtoupper($p->jenis) }}] {{ $p->nama_kategori }} ({{ $p->kode_kategori }})
                    </option>
                @endforeach
            </select>
            <span class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500 mt-1 ml-1 block">
                Pilih kategori induk jika ingin membuat subkategori turunan.
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Kode Kategori -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Kode Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kode_kategori"
                    value="{{ $kategori->kode_kategori ?? old('kode_kategori') }}" required
                    placeholder="Contoh: KAT-IN-01, KAT-OUT-02"
                    class="m3-input-glass w-full text-xs font-bold font-mono uppercase">
            </div>

            <!-- Jenis Aliran -->
            <div>
                <label
                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                    Jenis Aliran Dana <span class="text-rose-500">*</span>
                </label>
                <select name="jenis" id="form_kategori_jenis" required
                    class="m3-input-glass w-full text-xs font-bold">
                    <option value="pemasukan"
                        {{ (isset($kategori) && $kategori->jenis == 'pemasukan') || old('jenis') == 'pemasukan' ? 'selected' : '' }}>
                        Pemasukan (Cash In)
                    </option>
                    <option value="pengeluaran"
                        {{ (isset($kategori) && $kategori->jenis == 'pengeluaran') || old('jenis') == 'pengeluaran' ? 'selected' : '' }}>
                        Pengeluaran (Cash Out)
                    </option>
                    <option value="simpanan"
                        {{ (isset($kategori) && $kategori->jenis == 'simpanan') || old('jenis') == 'simpanan' ? 'selected' : '' }}>
                        Simpanan / Tabungan
                    </option>
                </select>
            </div>
        </div>

        <!-- Nama Kategori -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Nama Kategori / Subkategori <span class="text-rose-500">*</span>
            </label>
            <input type="text" name="nama_kategori"
                value="{{ $kategori->nama_kategori ?? old('nama_kategori') }}" required
                placeholder="Contoh: Listrik & Air, Honor Ustadz, Infaq Donatur"
                class="m3-input-glass w-full text-xs font-bold">
        </div>

        <!-- Deskripsi -->
        <div>
            <label
                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                Deskripsi / Alokasi
            </label>
            <textarea name="deskripsi" rows="3" placeholder="Keterangan alokasi pos transaksi ini..."
                class="m3-input-glass w-full text-xs font-medium resize-none">{{ $kategori->deskripsi ?? old('deskripsi') }}</textarea>
        </div>

        <!-- Toggle Status -->
        <div class="pt-2">
            <div
                class="flex items-center justify-between p-3.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-950/40 border border-zinc-200/70 dark:border-zinc-800/70">
                <div>
                    <label class="text-xs font-black text-zinc-900 dark:text-white block">Status Kategori</label>
                    <span class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500">
                        Kategori aktif dapat dipilih saat pencatatan transaksi kas keuangan.
                    </span>
                </div>
                <x-toggle name="is_active" :checked="isset($kategori) ? $kategori->is_active : true" />
            </div>
        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/70 dark:bg-zinc-950/50 border-t border-zinc-200/80 dark:border-zinc-800 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs font-black shadow-2xs">
            <i class="bi bi-save2-fill mr-1.5"></i> {{ isset($kategori) ? 'Simpan Perubahan' : 'Simpan Kategori' }}
        </button>
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="mt-2 sm:mt-0 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all">
            Batal
        </button>
    </div>
</form>

<script>
    function syncParentJenis(el) {
        const selectedOption = el.options[el.selectedIndex];
        const parentJenis = selectedOption ? selectedOption.getAttribute('data-jenis') : null;
        const jenisSelect = document.getElementById('form_kategori_jenis');

        if (parentJenis && jenisSelect) {
            jenisSelect.value = parentJenis;
            jenisSelect.classList.add('pointer-events-none', 'opacity-70');
        } else if (jenisSelect) {
            jenisSelect.classList.remove('pointer-events-none', 'opacity-70');
        }
    }

    (function() {
        const parentSelect = document.getElementById('form_kategori_parent_id');
        if (parentSelect && parentSelect.value) {
            syncParentJenis(parentSelect);
        }
    })();
</script>
