<form action="{{ route('kalendar-pendidikan.matriks.store-bulanbymatriks') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh] bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl rounded-2xl md:rounded-3xl overflow-hidden border border-zinc-200/80 dark:border-zinc-800 shadow-2xl">
    @csrf

    <input type="hidden" name="nama_bulan" id="bl_nama_bulan" value="{{ $namaBulan }}">
    <input type="hidden" name="urutan" id="bl_urutan" value="{{ $urutan }}">
    <input type="hidden" name="tahun_pelajaran_id" value="{{ $tp->id ?? '' }}">

    <!-- Modal Header -->
    <div
        class="px-5 py-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/80 dark:bg-zinc-950/60 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-9 h-9 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 shrink-0 shadow-2xs">
                <i class="bi bi-calendar-range text-base"></i>
            </div>
            <div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Plotting Periode Bulan
                </h3>
                <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mt-0.5">
                    Penjadwalan Matriks Cepat
                </p>
            </div>
        </div>

        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-9 min-h-9 flex items-center justify-center rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-500 dark:text-zinc-400 transition-colors outline-none shrink-0"
            title="Tutup">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
        <div class="space-y-4">

            <!-- Info Data yang Sedang Diplot -->
            <div class="p-3.5 bg-zinc-50/80 dark:bg-zinc-800/40 rounded-xl border border-zinc-200/80 dark:border-zinc-800">
                <div
                    class="flex justify-between items-center mb-2 pb-2 border-b border-zinc-200/80 dark:border-zinc-800">
                    <span class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Bulan Hijriyah</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-primary dark:text-primary-dark font-black" id="text_nama_bulan">
                            {{ request('nama_bulan', $namaBulan) }}
                        </span>
                        <input type="number" name="tahun_hijriyah"
                            value="{{ request('tahun_hijriyah', $tahunHijriiahAwal ?? '') }}"
                            class="w-18 px-2 py-1 m3-input-glass text-xs font-bold text-center" placeholder="1448" required>
                        <span class="text-xs font-bold text-zinc-400">H</span>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Urutan Sortir</span>
                    <span
                        class="text-xs text-zinc-800 dark:text-white font-black bg-zinc-200/80 dark:bg-zinc-700/80 px-2 py-0.5 rounded-md"
                        id="text_urutan">
                        {{ request('urutan', $urutan) }}
                    </span>
                </div>
            </div>

            <!-- Grid Mulai & Selesai Masehi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                        Mulai Masehi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_mulai_masehi" id="bl_mulai" required
                        value="{{ request('mulai') }}" class="m3-input-glass w-full font-bold text-xs cursor-pointer">
                </div>
                <div>
                    <label
                        class="block text-[11px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-0.5">
                        Selesai Masehi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_selesai_masehi" id="bl_selesai" required
                        value="{{ request('selesai') }}"
                        class="m3-input-glass w-full font-bold text-xs cursor-pointer border-emerald-500/50 focus:border-emerald-500">
                </div>
            </div>

            <!-- Status Aktif (M3 Toggle Switch) -->
            <label
                class="flex items-center justify-between p-3.5 bg-zinc-50/80 dark:bg-zinc-800/40 rounded-xl border border-zinc-200/80 dark:border-zinc-800 cursor-pointer group">
                <div>
                    <span class="text-xs font-black text-zinc-800 dark:text-zinc-200 block mb-0.5">
                        Jadikan Bulan Aktif?
                    </span>
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                        Akan menggantikan bulan aktif saat ini
                    </span>
                </div>
                <div class="relative inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer">
                    <!-- Switch Background -->
                    <div
                        class="w-10 h-5.5 bg-zinc-300 dark:bg-zinc-700 peer-focus:outline-none rounded-full peer 
                            peer-checked:after:translate-x-full peer-checked:after:border-white 
                            after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                            after:bg-white after:rounded-full after:h-4.5 after:w-4.5 after:transition-all 
                            peer-checked:bg-emerald-500 shadow-2xs">
                    </div>
                </div>
            </label>

        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="px-5 py-3.5 bg-zinc-50/80 dark:bg-zinc-950/60 border-t border-zinc-200/80 dark:border-zinc-800 flex flex-col-reverse sm:flex-row justify-end gap-2.5 shrink-0">
        <button type="button" data-dismiss="modal"
            class="m3-btn-secondary w-full sm:w-auto h-10 px-5">
            Batal
        </button>
        <button type="submit"
            class="m3-btn-primary w-full sm:w-auto h-10 px-6 group/btn">
            <i class="bi bi-cloud-arrow-up-fill text-xs"></i>
            <span>Daftarkan Periode</span>
        </button>
    </div>

</form>

