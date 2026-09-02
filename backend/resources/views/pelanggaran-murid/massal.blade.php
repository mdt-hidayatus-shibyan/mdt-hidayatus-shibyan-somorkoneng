@section('title', 'Kolektif - Pelanggaran Murid')

<x-app-layout>

    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div class="w-full xl:w-auto shrink-0">
            @include('pelanggaran-murid.menu')
        </div>
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('pelanggaran-murid.massal') }}" method="GET"
                class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto m3-glass-card p-1.5 shadow-2xs">

                <!-- Filter Tanggal -->
                <div class="relative w-full sm:w-44 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-calendar-date text-xs"></i>
                    </div>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" required
                        class="m3-input-glass w-full !pl-9 !pr-3 text-xs font-bold cursor-pointer">
                </div>

                <!-- Filter Ruangan -->
                <div class="relative w-full sm:w-48 group/select">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/select:text-primary dark:group-focus-within/select:text-primary-dark transition-colors">
                        <i class="bi bi-door-open text-xs"></i>
                    </div>
                    <select name="ruangan_id" required
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold cursor-pointer appearance-none">
                        <option value="" class="bg-white dark:bg-zinc-900 text-zinc-500">-- Ruangan --</option>
                        @foreach ($ruangans as $r)
                            <option value="{{ $r->id }}"
                                class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-white"
                                {{ $ruangan_id == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-chevron-down text-xs font-bold"></i>
                    </div>
                </div>

                <!-- Tombol Submit Tampilkan -->
                <div class="w-full sm:w-auto shrink-0">
                    <button type="submit"
                        class="m3-btn-primary w-full sm:w-auto h-10 px-5 text-xs group/btn">
                        <i class="bi bi-search text-xs mr-1"></i>
                        <span>Tampilkan</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    @if ($ruangan_id)
        <form action="{{ route('pelanggaran-murid.storeMassal') }}" method="POST" id="formKolektif"
            onsubmit="return konfirmasiSimpanKolektif(event)" class="relative z-10">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="ruangan_id" value="{{ $ruangan_id }}">
            <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahun_pelajaran_id }}">
            <input type="hidden" name="semester_id" value="{{ $semester_id }}">

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 md:gap-6 items-start">

                <!-- KOLOM KIRI (TENTUKAN KASUS) -->
                <div
                    class="xl:col-span-4 m3-glass-card p-5 md:p-6 sticky top-6 relative overflow-hidden group/form">

                    <div class="flex items-center gap-3 mb-5 relative z-10">
                        <span
                            class="w-8 h-8 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-black shadow-2xs">1</span>
                        <div>
                            <h3 class="font-black text-zinc-900 dark:text-white text-base tracking-tight leading-tight">
                                Tentukan Kasus
                            </h3>
                            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">
                                Pilih jenis pelanggaran kolektif
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4 relative z-10">
                        <div>
                            <label
                                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                                Jenis Pelanggaran (Bisa >1) <span class="text-rose-500">*</span>
                            </label>
                            <select name="referensi_pelanggaran_ids[]" id="multiPelanggaran"
                                class="w-full select2-custom" multiple required>
                                @foreach ($referensiPelanggarans->groupBy('kategori') as $kategori => $items)
                                    <optgroup label="{{ $kategori }}">
                                        @foreach ($items as $ref)
                                            <option value="{{ $ref->id }}">
                                                {{ $ref->id }} - {{ $ref->nama_pelanggaran }}
                                                (+{{ $ref->poin }} Poin)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                                Keterangan Khusus
                            </label>
                            <textarea name="keterangan" rows="3"
                                placeholder="Contoh: Jadwal piket regu B, namun malah bermain bola..."
                                class="m3-input-glass w-full !p-3 text-xs font-bold resize-none custom-scrollbar"></textarea>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN (TANDAI MURID PELAKU) -->
                <div
                    class="xl:col-span-8 m3-glass-card relative overflow-hidden flex flex-col h-[calc(100vh-140px)] min-h-[600px]">

                    <!-- Header Area Kanan -->
                    <div
                        class="px-5 md:px-6 py-4 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-950/50 relative z-10 shrink-0">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-black shadow-2xs">2</span>
                                <div>
                                    <h3 class="font-black text-zinc-900 dark:text-white text-base tracking-tight leading-tight">
                                        Tandai Santri Pelaku
                                    </h3>
                                    <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">
                                        Pilih santri yang terlibat
                                    </p>
                                </div>
                            </div>

                            <div
                                class="m3-glass-card p-1 rounded-xl flex gap-1 shadow-2xs">
                                <button type="button" onclick="toggleSemua(true)"
                                    class="px-3 py-1 rounded-lg text-xs font-black bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 hover:text-primary dark:hover:text-primary-dark transition-colors border border-zinc-200 dark:border-zinc-700 shadow-2xs outline-none">
                                    <i class="bi bi-check2-all mr-1"></i> Pilih Semua
                                </button>
                                <button type="button" onclick="toggleSemua(false)"
                                    class="px-3 py-1 rounded-lg text-xs font-black text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 transition-colors outline-none">
                                    <i class="bi bi-x-lg mr-1"></i> Kosongkan
                                </button>
                            </div>
                        </div>

                        <!-- Live Search Card List -->
                        <div class="relative w-full group/search">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/search:text-rose-500 transition-colors">
                                <i class="bi bi-search text-xs"></i>
                            </div>
                            <input type="text" id="liveSearch" onkeyup="filterCards()"
                                placeholder="Cari Nama / NISM santri dalam daftar ini..."
                                class="m3-input-glass w-full !pl-9 !pr-3 !py-2 text-xs font-bold">
                        </div>
                    </div>

                    <!-- Grid Daftar Murid (Scrollable) -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 md:p-5 relative z-10">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" id="muridContainer">
                            @foreach ($murids as $murid)
                                <label
                                    class="check-card relative flex flex-row items-center p-3 border border-zinc-200/80 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 rounded-xl cursor-pointer hover:border-rose-500/40 has-[:checked]:border-rose-500 has-[:checked]:bg-rose-500/10 transition-all gap-3 group/item shadow-2xs">
                                    <input type="checkbox" name="murid_ids[]" value="{{ $murid->id }}"
                                        class="sr-only murid-checkbox peer">

                                    <!-- Custom Circle Checkbox -->
                                    <div class="relative w-7 h-7 flex items-center justify-center shrink-0">
                                        <div
                                            class="w-5 h-5 rounded-md border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 peer-checked:bg-rose-600 peer-checked:border-rose-600 transition-all shadow-2xs flex items-center justify-center">
                                            <i class="bi bi-check text-white opacity-0 peer-checked:opacity-100 text-sm font-black leading-none"></i>
                                        </div>
                                    </div>

                                    <div class="min-w-0 flex-1 relative z-10">
                                        <h4 class="text-xs font-black text-zinc-900 dark:text-white truncate tracking-tight mb-0.5"
                                            title="{{ $murid->nama_lengkap }}">
                                            {{ $murid->nama_lengkap }}
                                        </h4>
                                        <p
                                            class="text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider truncate font-mono">
                                            NISM: {{ $murid->nism }}
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer Action Bar -->
                    @can('tambah pelanggaran murid')
                        <div
                            class="px-5 md:px-6 py-3.5 border-t border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-950/70 flex flex-col sm:flex-row justify-between items-center gap-4 relative z-10 shrink-0">
                            <div
                                class="text-[11px] font-black text-zinc-600 dark:text-zinc-400 uppercase tracking-wider text-center sm:text-left">
                                Total dipilih: <span id="counterPilihan"
                                    class="text-lg font-black text-rose-600 dark:text-rose-400 px-1">0</span> Santri
                            </div>
                            <button type="submit"
                                class="w-full sm:w-auto h-11 px-6 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-2xs hover:shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2 outline-none">
                                <i class="bi bi-hammer text-sm"></i>
                                <span>Jatuhkan Sanksi</span>
                            </button>
                        </div>
                    @endcan

                </div>
            </div>
        </form>

        <!-- Script & Dependencies -->
        <script>
            $(document).ready(function() {
                $('#multiPelanggaran').select2({
                    placeholder: "Pilih 1 atau banyak kasus...",
                    width: '100%',
                    closeOnSelect: false,
                    dropdownCssClass: "select2-zinc-dropdown"
                });

                $('.murid-checkbox').on('change', function() {
                    updateCounter();
                });
            });

            function toggleSemua(status) {
                $('.murid-checkbox').prop('checked', status);
                updateCounter();
            }

            function updateCounter() {
                const total = $('.murid-checkbox:checked').length;
                $('#counterPilihan').text(total);
            }

            function filterCards() {
                let input = document.getElementById('liveSearch').value.toLowerCase();
                let cards = document.querySelectorAll('.check-card');

                for (let i = 0; i < cards.length; i++) {
                    let title = cards[i].querySelector('h4').innerText.toLowerCase();
                    let nism = cards[i].querySelector('p').innerText.toLowerCase();

                    if (title.includes(input) || nism.includes(input)) {
                        cards[i].style.display = "";
                    } else {
                        cards[i].style.display = "none";
                    }
                }
            }

            function konfirmasiSimpanKolektif(e) {
                e.preventDefault();

                const totalMurid = $('.murid-checkbox:checked').length;
                const totalKasus = $('#multiPelanggaran').val() ? $('#multiPelanggaran').val().length : 0;
                const isDark = document.documentElement.classList.contains('dark');

                if (totalKasus === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Tunggu Dulu!</span>',
                        html: '<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-1">Anda belum memilih jenis pelanggaran apa pun di Kolom Kiri.</p>',
                        background: isDark ? '#121215' : '#ffffff',
                        confirmButtonColor: '#e11d48',
                        customClass: {
                            popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl',
                            confirmButton: 'rounded-xl font-bold px-5 py-2 text-xs'
                        }
                    });
                    return false;
                }

                if (totalMurid === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Tunggu Dulu!</span>',
                        html: '<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-1">Pilih minimal 1 santri pelaku pada daftar di Kolom Kanan.</p>',
                        background: isDark ? '#121215' : '#ffffff',
                        confirmButtonColor: '#e11d48',
                        customClass: {
                            popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl',
                            confirmButton: 'rounded-xl font-bold px-5 py-2 text-xs'
                        }
                    });
                    return false;
                }

                Swal.fire({
                    title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Jatuhkan Sanksi?</span>',
                    html: `<p class="text-xs font-medium text-zinc-600 dark:text-zinc-300 mt-1">Anda akan menjatuhkan <b class="text-rose-500">${totalKasus} pelanggaran</b> kepada <b class="text-rose-500">${totalMurid} santri</b> secara bersamaan.<br><br>Data tidak dapat dibatalkan secara massal setelah disimpan.</p>`,
                    icon: 'question',
                    showCancelButton: true,
                    heightAuto: false,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: isDark ? '#27272a' : '#e4e4e7',
                    confirmButtonText: 'Ya, Catat!',
                    cancelButtonText: '<span class="text-zinc-700 dark:text-zinc-300">Batal</span>',
                    background: isDark ? '#121215' : '#ffffff',
                    customClass: {
                        popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl',
                        confirmButton: 'rounded-xl font-bold px-5 py-2 text-xs',
                        cancelButton: 'rounded-xl font-bold px-5 py-2 text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: '<span class="text-base font-bold text-zinc-900 dark:text-white">Menyimpan Sanksi...</span>',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            background: isDark ? '#121215' : '#ffffff',
                            customClass: {
                                popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl'
                            },
                            didOpen: () => Swal.showLoading()
                        });
                        document.getElementById('formKolektif').submit();
                    }
                });
            }
        </script>
    @else
        <!-- State Awal saat halaman baru dibuka -->
        <div class="py-16 text-center m3-glass-card relative z-10">
            <div
                class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800/80 rounded-2xl flex items-center justify-center text-zinc-400 dark:text-zinc-500 text-2xl mb-3 mx-auto shadow-2xs">
                <i class="bi bi-people"></i>
            </div>
            <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight mb-0.5">Pilih Ruangan/Kelas</h3>
            <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                Tentukan ruangan di filter atas untuk memunculkan daftar santri yang akan ditandai.
            </p>
        </div>
    @endif

    <style>
        /* Customizing Select2 to match Zinc M3 inputs */
        .select2-container--default .select2-selection--multiple {
            background-color: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(228, 228, 231, 0.8);
            border-radius: 0.75rem;
            min-height: 42px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: 700;
            color: #18181b;
        }

        .dark .select2-container--default .select2-selection--multiple {
            background-color: rgba(24, 24, 27, 0.6);
            border: 1px solid rgba(39, 39, 42, 0.8);
            color: #fafafa;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #f43f5e;
            box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.2);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: rgba(244, 63, 94, 0.1);
            border: 1px solid rgba(244, 63, 94, 0.2);
            color: #e11d48;
            border-radius: 6px;
            margin-top: 5px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .dark .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: rgba(225, 29, 72, 0.2);
            border: 1px solid rgba(225, 29, 72, 0.3);
            color: #fb7185;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: inherit;
            margin-right: 5px;
            border-right: 1px solid inherit;
            padding-right: 5px;
        }

        .select2-dropdown {
            border-color: #e4e4e7;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .dark .select2-dropdown {
            background-color: #121215;
            border-color: #27272a;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #f4f4f5;
        }

        .dark .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #18181b;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #f43f5e;
            color: white;
        }
    </style>
</x-app-layout>

