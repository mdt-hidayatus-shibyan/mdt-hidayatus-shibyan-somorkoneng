@section('title', 'Tambah Ruangan')

<x-app-layout>

    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('ruangan.index') }}"
                class="w-10 h-10 bg-white/80 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-xl flex items-center justify-center transition-all duration-200 shadow-sm active:scale-95 shrink-0 outline-none"
                title="Kembali">
                <i class="bi bi-arrow-left text-base font-bold"></i>
            </a>
            <div>
                <h2
                    class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                    Tambah Data Ruangan
                </h2>
                <p
                    class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                    Anda dapat menambahkan satu atau banyak ruangan sekaligus.
                </p>
            </div>
        </div>

        <button type="button" onclick="tambahBaris()" class="m3-btn-primary w-full sm:w-auto px-5 group/btn">
            <i class="bi bi-plus-lg text-base"></i>
            <span>Tambah Baris</span>
        </button>
    </div>

    <!-- Main Form -->
    <form action="{{ route('ruangan.store') }}" method="POST" class="relative z-10 space-y-4">
        @csrf

        <!-- SECTION: PENGATURAN GLOBAL (Tahun Pelajaran) -->
        <div
            class="m3-glass-card p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-20">

            <!-- Sisi Kiri: Ikon & Label -->
            <div class="flex items-center gap-3.5">
                <div
                    class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-200/60 dark:border-amber-800/40">
                    <i class="bi bi-calendar-event-fill text-lg"></i>
                </div>
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-zinc-900 dark:text-white tracking-wider uppercase">
                        Tahun Pelajaran <span class="text-rose-500">*</span>
                    </h3>
                    <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                        Kaitkan ruangan dengan tahun pelajaran aktif.
                    </p>
                </div>
            </div>

            <!-- Sisi Kanan: Input Select2 -->
            <div class="w-full md:w-80 shrink-0">
                <div class="relative group">
                    <select name="tahun_pelajaran_id"
                        class="m3-select2 w-full {{ $errors->has('tahun_pelajaran_id') ? '!border-red-500 !ring-red-500/20' : '' }}">
                        @foreach ($tahunPelajarans as $tp)
                            <option value="{{ $tp->id }}"
                                {{ old('tahun_pelajaran_id') == $tp->id || $tp->is_active ? 'selected' : '' }}>
                                {{ $tp->nama_hijriyah }} - {{ $tp->nama_masehi }}
                                {{ $tp->is_active ? '  [Aktif]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @error('tahun_pelajaran_id')
                    <p class="text-[11px] font-bold text-rose-500 dark:text-rose-400 mt-1.5 ml-1 flex items-center absolute">
                        <i class="bi bi-exclamation-circle-fill mr-1.5"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- SECTION: CONTAINER BARIS RUANGAN -->
        <div id="container-baris" class="space-y-3.5">
            @php
                $oldRuangans = old('ruangan', [0 => []]);
            @endphp

            @foreach ($oldRuangans as $index => $item)
                <!-- CARD TEMPLATE BARIS -->
                <div
                    class="baris-ruangan m3-glass-card p-4 sm:p-5 relative overflow-hidden group">

                    <!-- Header Kartu Baris -->
                    <div
                        class="flex items-center justify-between mb-3.5 border-b border-zinc-100 dark:border-zinc-800/80 pb-2.5">
                        <div class="flex items-center gap-2">
                            <span
                                class="nomor-urut w-7 h-7 flex items-center justify-center bg-zinc-100/80 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-lg text-xs font-black shrink-0">
                                {{ $loop->iteration }}
                            </span>
                            <span class="text-xs font-extrabold text-zinc-900 dark:text-white uppercase tracking-wider">Data
                                Ruangan</span>
                        </div>

                        <button type="button" onclick="hapusBaris(this)"
                            class="tombol-hapus h-7 px-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-[11px] font-bold transition-all outline-none flex items-center gap-1">
                            <i class="bi bi-trash-fill text-xs"></i> <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </div>

                    <!-- Isi Input Kartu Baris -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">

                        <!-- Kelas / Level -->
                        <div class="space-y-1.5">
                            <label
                                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                                Kelas / Level <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative group">
                                <select name="ruangan[{{ $index }}][level_id]"
                                    class="m3-select2 w-full {{ $errors->has("ruangan.$index.level_id") ? '!border-red-500 !ring-red-500/20' : '' }}">
                                    <option value="" disabled
                                        {{ old("ruangan.$index.level_id") ? '' : 'selected' }}>Pilih Level...</option>
                                    @foreach ($levels as $lvl)
                                        <option value="{{ $lvl->id }}"
                                            {{ old("ruangan.$index.level_id") == $lvl->id ? 'selected' : '' }}>
                                            {{ $lvl->nama_level }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error("ruangan.$index.level_id")
                                <p
                                    class="error-msg text-[11px] font-bold text-rose-500 dark:text-rose-400 mt-1 ml-1 flex items-center">
                                    <i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Nama Ruangan -->
                        <div class="space-y-1.5">
                            <label
                                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                                Nama Ruangan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="ruangan[{{ $index }}][nama_ruangan]"
                                value="{{ old("ruangan.$index.nama_ruangan") }}" placeholder="Cth: 1-A TPQ"
                                class="m3-input-glass w-full uppercase {{ $errors->has("ruangan.$index.nama_ruangan") ? '!border-red-500 !ring-red-500/20' : '' }}"
                                oninput="this.value = this.value.toUpperCase()">
                            @error("ruangan.$index.nama_ruangan")
                                <p
                                    class="error-msg text-[11px] font-bold text-rose-500 dark:text-rose-400 mt-1 ml-1 flex items-center">
                                    <i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Wali Ruangan -->
                        <div class="space-y-1.5">
                            <label
                                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                                Wali Ruangan (Opsional)
                            </label>
                            <div class="relative group">
                                <select name="ruangan[{{ $index }}][asatidz_id]"
                                    class="m3-select2 w-full {{ $errors->has("ruangan.$index.asatidz_id") ? '!border-red-500 !ring-red-500/20' : '' }}">
                                    <option value="">-- Belum Ditunjuk --</option>
                                    @foreach ($dataAsatidz as $guru)
                                        <option value="{{ $guru->id }}"
                                            {{ old("ruangan.$index.asatidz_id") == $guru->id ? 'selected' : '' }}>
                                            {{ $guru->nigm }} - {{ $guru->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error("ruangan.$index.asatidz_id")
                                <p
                                    class="error-msg text-[11px] font-bold text-rose-500 dark:text-rose-400 mt-1 ml-1 flex items-center">
                                    <i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Kapasitas -->
                        <div class="space-y-1.5">
                            <label
                                class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                                Kapasitas
                            </label>
                            <input type="number" name="ruangan[{{ $index }}][kapasitas]"
                                value="{{ old("ruangan.$index.kapasitas", 30) }}" min="1"
                                class="m3-input-glass w-full {{ $errors->has("ruangan.$index.kapasitas") ? '!border-red-500 !ring-red-500/20' : '' }}">
                            @error("ruangan.$index.kapasitas")
                                <p
                                    class="error-msg text-[11px] font-bold text-rose-500 dark:text-rose-400 mt-1 ml-1 flex items-center">
                                    <i class="bi bi-exclamation-circle-fill mr-1"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="m3-btn-primary w-full sm:w-auto px-8">
                <i class="bi bi-save2-fill text-sm"></i>
                <span>Simpan Semua Ruangan</span>
            </button>
        </div>
    </form>



    @push('script')
        <script>
            let rowIndexCounter = {{ count($oldRuangans) > 0 ? max(array_keys($oldRuangans)) + 1 : 1 }};

            // Fungsi Inisialisasi Select2
            function initSelect2(elements) {
                $(elements).select2({
                    width: '100%',
                    dropdownAutoWidth: true,
                    language: {
                        noResults: function() {
                            return "Data tidak ditemukan";
                        }
                    }
                });
            }

            // Jalankan Select2 pertama kali saat halaman dimuat
            $(document).ready(function() {
                initSelect2('.m3-select2');
                perbaruiTombolHapus();
                perbaruiNomorUrut();
            });

            // FUNGSI CLONE (TAMBAH BARIS) DENGAN DUKUNGAN SELECT2
            function tambahBaris() {
                const $container = $('#container-baris');
                // Ambil baris pertama sebagai template
                const $templateRow = $container.find('.baris-ruangan').first();

                // Clone Template
                const $newRow = $templateRow.clone();

                // 1. BERSIHKAN SELECT2 DARI HASIL CLONING
                // Hapus elemen DOM tambahan yang di-generate oleh Select2
                $newRow.find('.select2-container').remove();
                // Hapus atribut sisa pada select aslinya agar bersih
                $newRow.find('.m3-select2').removeClass('select2-hidden-accessible').removeAttr(
                    'data-select2-id tabindex aria-hidden');
                $newRow.find('option').removeAttr('data-select2-id');

                // 2. BERSIHKAN PESAN ERROR
                $newRow.find('.error-msg').remove();

                // 3. RESET INPUT & GANTI INDEX ARRAY
                $newRow.find('input, select').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        // Ubah [0] menjadi [indexTerbaru]
                        $(this).attr('name', name.replace(/\[\d+\]/, `[${rowIndexCounter}]`));
                    }

                    // Hapus class error (border merah)
                    $(this).removeClass('!border-red-500 !ring-red-500/20');

                    // Kosongkan nilai input
                    if ($(this).is('input[type="text"]')) $(this).val('');
                    if ($(this).is('select')) $(this).prop('selectedIndex', 0); // Kembali ke default
                    if ($(this).is('input[type="number"]')) $(this).val('30');
                });

                // 4. APPEND KE CONTAINER & INIT SELECT2 PADA BARIS BARU
                $container.append($newRow);
                initSelect2($newRow.find('.m3-select2')); // Init ulang Select2 khusus di baris baru

                rowIndexCounter++;
                perbaruiNomorUrut();
                perbaruiTombolHapus();
            }

            function hapusBaris(button) {
                const $container = $('#container-baris');

                // Pastikan tidak menghapus baris terakhir
                if ($container.children('.baris-ruangan').length > 1) {
                    $(button).closest('.baris-ruangan').remove();
                }

                perbaruiNomorUrut();
                perbaruiTombolHapus();
            }

            function perbaruiNomorUrut() {
                $('#container-baris .baris-ruangan').each(function(index) {
                    $(this).find('.nomor-urut').text(index + 1);
                });
            }

            function perbaruiTombolHapus() {
                const $rows = $('#container-baris .baris-ruangan');
                if ($rows.length === 1) {
                    $rows.find('.tombol-hapus').addClass('hidden');
                } else {
                    $rows.find('.tombol-hapus').removeClass('hidden');
                }
            }
        </script>
    @endpush
</x-app-layout>
