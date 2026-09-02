@section('title', 'Harian - Pelanggaran Murid')

<x-app-layout>

    <div class="mb-6 md:mb-8 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div class="w-full xl:w-auto shrink-0">
            @include('pelanggaran-murid.menu')
        </div>
        <div class="w-full xl:w-auto flex-1 flex xl:justify-end">
            <form action="{{ route('pelanggaran-murid.index') }}" method="GET"
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
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-6 flex-1">

            <!-- FORM INPUT (KIRI) -->
            <div class="lg:col-span-5 xl:col-span-4">
                <div
                    class="m3-glass-card p-5 md:p-6 sticky top-6 relative overflow-hidden group/form">

                    <!-- Header -->
                    <div class="flex items-center gap-3 mb-5 relative z-10">
                        <div
                            class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-lg shrink-0">
                            <i class="bi bi-person-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-zinc-900 dark:text-white text-base tracking-tight leading-tight">
                                Input Kasus
                            </h3>
                            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">
                                Catat pelanggaran santri
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('pelanggaran-murid.storeHarian') }}" method="POST" class="relative z-10">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        <input type="hidden" name="ruangan_id" value="{{ $ruangan_id }}">
                        <input type="hidden" name="tahun_pelajaran_id" value="{{ $tahun_pelajaran_id }}">
                        <input type="hidden" name="semester_id" value="{{ $semester_id }}">

                        <div class="space-y-4">
                            <!-- Input NISM -->
                            <div>
                                <label
                                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                                    NISM Santri <span class="text-rose-500">*</span>
                                </label>
                                <select name="murid_id" id="cariNism" class="w-full select2-custom" required>
                                    <option value=""></option>
                                    @foreach ($murids as $murid)
                                        <option value="{{ $murid->id }}">[{{ $murid->nism }}] -
                                            {{ $murid->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Input Jenis Pelanggaran -->
                            <div>
                                <label
                                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                                    Kode / Jenis Pelanggaran <span class="text-rose-500">*</span>
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

                            <!-- Input Detail/Keterangan -->
                            <div>
                                <label
                                    class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">
                                    Detail / Keterangan
                                </label>
                                <textarea name="keterangan" rows="3" placeholder="Detail kejadian (opsional)..."
                                    class="m3-input-glass w-full !p-3 text-xs font-bold resize-none custom-scrollbar"></textarea>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="pt-2">
                                @can('tambah pelanggaran murid')
                                    <button type="submit"
                                        class="w-full h-11 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-2xs hover:shadow-sm active:scale-95 transition-all flex items-center justify-center gap-2 outline-none">
                                        <i class="bi bi-journal-plus text-sm"></i>
                                        <span>Catat Pelanggaran</span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIWAYAT KASUS (KANAN) -->
            <div class="lg:col-span-7 xl:col-span-8">
                <div
                    class="m3-glass-card p-5 md:p-6 flex flex-col min-h-[500px] relative overflow-hidden">

                    <!-- Header Riwayat -->
                    <div
                        class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-5 border-b border-zinc-200/80 dark:border-zinc-800 pb-4 relative z-10">
                        <div>
                            <h3 class="font-black text-base md:text-lg text-zinc-900 dark:text-white tracking-tight flex items-center">
                                <i class="bi bi-clock-history text-rose-500 mr-2 text-lg"></i>
                                Riwayat Hari Ini
                            </h3>
                            <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">
                                Daftar catatan pelanggaran santri terinput
                            </p>
                        </div>

                        <!-- Live Search -->
                        <div class="relative w-full sm:w-64 group/search">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 group-focus-within/search:text-rose-500 transition-colors">
                                <i class="bi bi-search text-xs"></i>
                            </div>
                            <input type="text" id="liveSearch" onkeyup="filterCards()"
                                placeholder="Cari Nama / NISM..."
                                class="m3-input-glass w-full !pl-9 !pr-3 !py-2 text-xs font-bold">
                        </div>
                    </div>

                    <!-- Daftar List Card Kasus -->
                    <div id="vioListContainer"
                        class="grid grid-cols-1 xl:grid-cols-2 gap-4 flex-1 content-start relative z-10">
                        @forelse ($riwayatPelanggaran as $p)
                            <div
                                class="vio-card bg-zinc-50/70 dark:bg-zinc-950/60 border border-zinc-200/80 dark:border-zinc-800 p-4 md:p-5 rounded-2xl hover:border-rose-500/30 transition-all flex flex-col justify-between group shadow-2xs relative overflow-hidden">

                                <div class="flex justify-between items-start mb-3 relative z-10">
                                    <div class="pr-2 flex-1">
                                        <div
                                            class="text-[9px] font-black text-rose-600 dark:text-rose-400 mb-1.5 flex items-center uppercase tracking-wider bg-rose-500/10 border border-rose-500/20 w-max px-2 py-0.5 rounded shadow-2xs">
                                            <i class="bi bi-calendar-event mr-1.5"></i>
                                            {{ date('d M Y', strtotime($p->tanggal)) }} &bull;
                                            +{{ $p->referensiPelanggaran->poin }} Poin
                                        </div>
                                        <h4
                                            class="card-title font-black text-zinc-900 dark:text-white text-sm leading-tight mb-1 break-words">
                                            {{ $p->murid->nama_lengkap }}
                                        </h4>
                                        <p
                                            class="card-nism text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider font-mono">
                                            NISM: {{ $p->murid->nism }}
                                        </p>
                                    </div>

                                    <div
                                        class="flex gap-1.5 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity shrink-0">
                                        @can('hapus pelanggaran-murid')
                                            <form action="{{ route('pelanggaran-murid.destroyHarian', $p->id) }}"
                                                method="POST" class="m-0" onsubmit="return false;">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="hapusData(this)"
                                                    class="w-7 h-7 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center hover:bg-rose-500/20 transition-all active:scale-95 shadow-2xs"
                                                    title="Hapus">
                                                    <i class="bi bi-trash3-fill text-xs"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>

                                <!-- Accordion Detail -->
                                <details
                                    class="group/accordion mt-auto pt-3 border-t border-zinc-200/80 dark:border-zinc-800 relative z-10">
                                    <summary
                                        class="flex justify-between items-center gap-2 cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                                        <div
                                            class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400 line-clamp-1 pr-2">
                                            {{ $p->referensiPelanggaran->nama_pelanggaran }}
                                        </div>
                                        <div
                                            class="bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 font-bold text-[9px] uppercase tracking-wider rounded px-2 py-1 transition-colors border border-zinc-200 dark:border-zinc-800 flex items-center shadow-2xs shrink-0 group-hover/accordion:bg-rose-500/10 group-hover/accordion:text-rose-600 dark:group-hover/accordion:text-rose-400 group-hover/accordion:border-rose-500/30">
                                            Detail <i
                                                class="bi bi-chevron-down ml-1 transition-transform duration-300 group-open/accordion:rotate-180"></i>
                                        </div>
                                    </summary>

                                    <!-- Detail Konten -->
                                    <div
                                        class="mt-3 text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white/80 dark:bg-zinc-900/80 p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
                                        <div
                                            class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-0.5">
                                            Kasus:</div>
                                        <p class="break-words mb-2 leading-snug font-black text-xs text-zinc-900 dark:text-white">
                                            {{ $p->referensiPelanggaran->nama_pelanggaran }}</p>

                                        <div
                                            class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-0.5">
                                            Keterangan Tambahan:</div>
                                        <p class="break-words leading-snug italic text-[11px] text-zinc-600 dark:text-zinc-400">
                                            {{ $p->keterangan ?: 'Tidak ada detail tambahan.' }}</p>

                                        <div
                                            class="mt-2.5 pt-2 border-t border-dashed border-zinc-200 dark:border-zinc-800 text-[9px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 flex items-center">
                                            <i class="bi bi-person-check-fill mr-1.5"></i> Diinput oleh:
                                            {{ $p->penginput ? $p->penginput->name : 'Sistem' }}
                                        </div>
                                    </div>
                                </details>
                            </div>
                        @empty
                            <div
                                class="col-span-full flex flex-col items-center justify-center py-16 text-center bg-zinc-50/50 dark:bg-zinc-950/40 border-2 border-dashed border-zinc-200/80 dark:border-zinc-800 rounded-2xl">
                                <div
                                    class="w-12 h-12 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-500 mb-3 mx-auto shadow-2xs">
                                    <i class="bi bi-shield-check text-2xl"></i>
                                </div>
                                <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight mb-0.5">
                                    Alhamdulillah
                                </h3>
                                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400">Tidak ada riwayat pelanggaran hari ini di kelas ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts Data -->
        <script>
            $(document).ready(function() {
                $('#cariNism').select2({
                    placeholder: "Ketik NISM / Nama Murid...",
                    width: '100%'
                });
                $('#multiPelanggaran').select2({
                    placeholder: "Pilih 1 atau banyak kasus...",
                    width: '100%',
                    closeOnSelect: false
                });
            });

            function filterCards() {
                let input = document.getElementById('liveSearch').value.toLowerCase();
                let cards = document.getElementsByClassName('vio-card');

                for (let i = 0; i < cards.length; i++) {
                    let title = cards[i].querySelector('.card-title').innerText.toLowerCase();
                    let nism = cards[i].querySelector('.card-nism').innerText.toLowerCase();

                    if (title.includes(input) || nism.includes(input)) {
                        cards[i].style.display = "";
                    } else {
                        cards[i].style.display = "none";
                    }
                }
            }

            // SweetAlert2 (Solid Zinc Style)
            function hapusData(button) {
                const isDarkMode = document.documentElement.classList.contains('dark');
                Swal.fire({
                    title: '<span class="text-lg font-bold text-zinc-900 dark:text-white">Hapus Riwayat?</span>',
                    html: '<p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-1">Data pelanggaran yang dihapus tidak dapat dikembalikan dan poin akan ditarik ulang.</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    heightAuto: false,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: isDarkMode ? '#27272a' : '#e4e4e7',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: '<span class="text-zinc-700 dark:text-zinc-300">Batal</span>',
                    background: isDarkMode ? '#121215' : '#ffffff',
                    customClass: {
                        popup: 'rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl',
                        confirmButton: 'rounded-xl font-bold px-5 py-2.5 text-xs',
                        cancelButton: 'rounded-xl font-bold px-5 py-2.5 text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }
        </script>
    @else
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
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            background-color: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(228, 228, 231, 0.8);
            border-radius: 0.75rem;
            min-height: 42px;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 700;
            color: #18181b;
        }

        .dark .select2-container--default .select2-selection--single,
        .dark .select2-container--default .select2-selection--multiple {
            background-color: rgba(24, 24, 27, 0.6);
            border: 1px solid rgba(39, 39, 42, 0.8);
            color: #fafafa;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
            color: inherit;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #f43f5e;
            box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.2);
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

