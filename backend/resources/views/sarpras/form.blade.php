<!-- Modal Form Sarpras -->
<form action="{{ isset($sarpras) ? route('sarpras.update', $sarpras->id) : route('sarpras.store') }}" method="POST"
    enctype="multipart/form-data" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($sarpras))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            {{ isset($sarpras) ? 'Edit Sarana & Prasarana' : 'Tambah Sarana & Prasarana Baru' }}
        </h3>
        <!-- Close Button -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1 space-y-4">

        <!-- Baris 1: Kode & Nama Barang -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <!-- Kode Sarpras -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kode Barang <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kode_sarpras"
                    value="{{ $sarpras->kode_sarpras ?? old('kode_sarpras', $autoKode ?? '') }}"
                    placeholder="Contoh: SPR-2026-0001" class="m3-input-glass w-full uppercase"
                    oninput="this.value = this.value.toUpperCase()" required>
            </div>

            <!-- Nama Sarpras -->
            <div class="sm:col-span-2 space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Barang / Sarpras <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_sarpras" value="{{ $sarpras->nama_sarpras ?? old('nama_sarpras') }}"
                    placeholder="Contoh: Papan Tulis Whiteboard 120x240 / Meja Guru" class="m3-input-glass w-full"
                    required>
            </div>
        </div>

        <!-- Baris 2: Kategori & Jumlah & Satuan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <!-- Kategori -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <div class="relative group">
                    <select name="kategori" class="m3-input-glass w-full appearance-none cursor-pointer !pr-8" required>
                        <option value="" disabled {{ !isset($sarpras) && !old('kategori') ? 'selected' : '' }}>--
                            Pilih Kategori --</option>
                        @foreach ($kategoriList as $kat)
                            <option value="{{ $kat }}"
                                {{ (isset($sarpras) && $sarpras->kategori == $kat) || old('kategori') == $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Jumlah -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Jumlah <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="jumlah" value="{{ $sarpras->jumlah ?? old('jumlah', 1) }}" min="1"
                    class="m3-input-glass w-full" required>
            </div>

            <!-- Satuan -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Satuan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="satuan" value="{{ $sarpras->satuan ?? old('satuan', 'Unit') }}"
                    placeholder="Cth: Unit, Pcs, Set, Buah" class="m3-input-glass w-full" required>
            </div>
        </div>

        <!-- Baris 3: Lokasi Gedung & Ruangan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Gedung -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Lokasi Gedung (Opsional)
                </label>
                <div class="relative group">
                    <select name="gedung_id" id="modal-sarpras-gedung"
                        class="m3-input-glass w-full appearance-none cursor-pointer !pr-8">
                        <option value="">-- Tanpa Gedung / Gudang Umum --</option>
                        @foreach ($gedungs as $gd)
                            <option value="{{ $gd->id }}"
                                {{ (isset($sarpras) && $sarpras->gedung_id == $gd->id) || old('gedung_id') == $gd->id ? 'selected' : '' }}>
                                {{ $gd->nama_gedung }} ({{ $gd->kode_gedung }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Ruangan -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Lokasi Ruangan / Kamar (Opsional)
                </label>
                <div class="relative group">
                    <select name="ruangan_id" id="modal-sarpras-ruangan"
                        class="m3-input-glass w-full appearance-none cursor-pointer !pr-8">
                        <option value="">-- Tanpa Ruangan Tertentu --</option>
                        @foreach ($ruangans as $rg)
                            <option value="{{ $rg->id }}"
                                {{ (isset($sarpras) && $sarpras->ruangan_id == $rg->id) || old('ruangan_id') == $rg->id ? 'selected' : '' }}>
                                {{ $rg->nama_ruangan }} {{ $rg->nama_kamar ? "($rg->nama_kamar)" : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris 4: Status Kondisi Barang -->
        <div class="space-y-2">
            <label
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Status Kondisi Barang <span class="text-rose-500">*</span>
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <!-- Tersedia / Baik -->
                <label class="relative cursor-pointer">
                    <input type="radio" name="kondisi" value="tersedia" class="peer sr-only"
                        {{ (!isset($sarpras) && !old('kondisi')) || (isset($sarpras) && $sarpras->kondisi == 'tersedia') || old('kondisi') == 'tersedia' ? 'checked' : '' }}>
                    <div
                        class="p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 dark:peer-checked:bg-emerald-950/30 text-center transition-all">
                        <i
                            class="bi bi-check-circle-fill text-lg text-emerald-600 dark:text-emerald-400 block mb-1"></i>
                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Tersedia / Baik</span>
                    </div>
                </label>

                <!-- Rusak Ringan -->
                <label class="relative cursor-pointer">
                    <input type="radio" name="kondisi" value="rusak_ringan" class="peer sr-only"
                        {{ (isset($sarpras) && $sarpras->kondisi == 'rusak_ringan') || old('kondisi') == 'rusak_ringan' ? 'checked' : '' }}>
                    <div
                        class="p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 peer-checked:border-amber-500 peer-checked:bg-amber-50/50 dark:peer-checked:bg-amber-950/30 text-center transition-all">
                        <i
                            class="bi bi-exclamation-circle-fill text-lg text-amber-600 dark:text-amber-400 block mb-1"></i>
                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Rusak Ringan</span>
                    </div>
                </label>

                <!-- Rusak Berat -->
                <label class="relative cursor-pointer">
                    <input type="radio" name="kondisi" value="rusak_berat" class="peer sr-only"
                        {{ (isset($sarpras) && $sarpras->kondisi == 'rusak_berat') || old('kondisi') == 'rusak_berat' ? 'checked' : '' }}>
                    <div
                        class="p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 peer-checked:border-rose-500 peer-checked:bg-rose-50/50 dark:peer-checked:bg-rose-950/30 text-center transition-all">
                        <i class="bi bi-x-circle-fill text-lg text-rose-600 dark:text-rose-400 block mb-1"></i>
                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Rusak Berat</span>
                    </div>
                </label>

                <!-- Rusak Total -->
                <label class="relative cursor-pointer">
                    <input type="radio" name="kondisi" value="rusak" class="peer sr-only"
                        {{ (isset($sarpras) && $sarpras->kondisi == 'rusak') || old('kondisi') == 'rusak' ? 'checked' : '' }}>
                    <div
                        class="p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 peer-checked:border-red-600 peer-checked:bg-red-50/50 dark:peer-checked:bg-red-950/30 text-center transition-all">
                        <i class="bi bi-trash3-fill text-lg text-red-600 dark:text-red-400 block mb-1"></i>
                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Rusak Total</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Baris 5: Sumber Dana & Tanggal Pengadaan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Sumber Dana -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Sumber Dana (Opsional)
                </label>
                <input type="text" name="sumber_dana" value="{{ $sarpras->sumber_dana ?? old('sumber_dana') }}"
                    placeholder="Contoh: Kas Madrasah / Wakaf / Hibah" class="m3-input-glass w-full">
            </div>

            <!-- Tanggal Pengadaan -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Tanggal Pengadaan (Opsional)
                </label>
                <input type="date" name="tanggal_pengadaan"
                    value="{{ isset($sarpras->tanggal_pengadaan) ? $sarpras->tanggal_pengadaan->format('Y-m-d') : old('tanggal_pengadaan') }}"
                    class="m3-input-glass w-full">
            </div>
        </div>

        <!-- Baris 6: Upload Foto & Preview -->
        <div class="space-y-1.5">
            <label
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Foto Barang (Opsional, Max: 2MB)
            </label>
            <div class="flex items-center gap-3">
                @if (isset($sarpras) && $sarpras->foto)
                    <img src="{{ route('storage.local', $sarpras->foto) }}" alt="Foto"
                        class="w-12 h-12 rounded-xl object-cover border border-zinc-200 dark:border-zinc-800 shrink-0">
                @endif
                <input type="file" name="foto" accept="image/*"
                    class="m3-input-glass w-full text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary dark:file:bg-primary-dark/20 dark:file:text-primary-dark cursor-pointer">
            </div>
        </div>

        <!-- Baris 7: Keterangan -->
        <div class="space-y-1.5">
            <label
                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                Keterangan / Catatan Inventaris (Opsional)
            </label>
            <textarea name="keterangan" rows="2" placeholder="Catatan spesifikasi, nomor seri, atau kondisi detail..."
                class="m3-input-glass w-full resize-none">{{ $sarpras->keterangan ?? old('keterangan') }}</textarea>
        </div>

    </div>

    <!-- Modal Footer -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto px-8">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($sarpras) ? 'Simpan Perubahan' : 'Simpan Sarpras' }}</span>
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Cascading select: Update list ruangan ketika gedung dipilih
        $('#modal-sarpras-gedung').on('change', function() {
            const gedungId = $(this).val();
            const $ruanganSelect = $('#modal-sarpras-ruangan');

            $ruanganSelect.html('<option value="">Memuat ruangan...</option>');

            const url = gedungId ? `{{ url('sarpras/get-ruangan') }}/${gedungId}` :
                `{{ url('sarpras/get-ruangan') }}`;
            $.getJSON(url, function(data) {
                let options = '<option value="">-- Tanpa Ruangan Tertentu --</option>';
                data.forEach(function(item) {
                    const kamarLabel = item.nama_kamar ? ` (${item.nama_kamar})` : '';
                    options +=
                        `<option value="${item.id}">${item.nama_ruangan}${kamarLabel}</option>`;
                });
                $ruanganSelect.html(options);
            }).fail(function() {
                $ruanganSelect.html('<option value="">-- Gagal memuat ruangan --</option>');
            });
        });
    });
</script>
