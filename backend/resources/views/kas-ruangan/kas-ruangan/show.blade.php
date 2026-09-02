<x-app-layout>

    <!-- HEADER (Struktur Sejajar) -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-20">

        <!-- Sisi Kiri: Tombol Back & Judul -->
        <div class="flex items-center gap-3">
            <a href="{{ route('kas-ruangan.index') }}"
                class="w-10 h-10 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 rounded-xl flex items-center justify-center transition-all shadow-2xs active:scale-95 shrink-0"
                title="Kembali">
                <i class="bi bi-arrow-left text-base font-bold"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    Kas {{ $ruangan->nama_ruangan }}
                </h2>
                <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Daftar santri & status pelunasan kas ruangan akhir tahun.
                </p>
            </div>
        </div>
    </div>

    <!-- DAFTAR MURID (M3 Glass Cards) -->
    <div class="flex flex-col gap-3.5 relative z-10">
        @foreach ($murids as $murid)
            @php
                $isLaki = strtolower($murid->jenis_kelamin ?? 'l') === 'l';
                $target = $isLaki
                    ? $ruangan->pengaturanKas->nominal_laki ?? 0
                    : $ruangan->pengaturanKas->nominal_perempuan ?? 0;

                $sudahBayar = $murid->pembayaranKas->sum('jumlah_bayar');
                $sisa = $target - $sudahBayar;
                $sisa = $sisa < 0 ? 0 : $sisa;
            @endphp

            <div
                class="m3-glass-card p-4 md:p-5 flex flex-col xl:flex-row xl:items-center justify-between gap-4 transition-all shadow-2xs hover:border-primary/40 dark:hover:border-primary-dark/40 group">

                <!-- 1. Identitas Santri -->
                <div class="flex items-center gap-3.5 xl:w-[30%] shrink-0">
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-xs font-black shadow-2xs shrink-0 {{ $isLaki ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' }}">
                        {{ strtoupper($murid->jenis_kelamin ?? 'L') }}
                    </span>
                    <div>
                        <h3 class="font-black text-zinc-900 dark:text-white text-sm md:text-base tracking-tight leading-tight">
                            {{ $murid->nama_lengkap ?? $murid->nama }}
                        </h3>
                        <p
                            class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mt-0.5">
                            NIS: {{ $murid->nism ?? '-' }}
                        </p>
                    </div>
                </div>

                <!-- 2. Box Statistik -->
                <div
                    class="flex-1 flex flex-col sm:flex-row items-center gap-2 sm:gap-4 bg-zinc-500/5 dark:bg-zinc-800/40 p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-700/60 shadow-2xs w-full xl:w-auto justify-around font-mono">

                    <div
                        class="flex flex-row sm:flex-col justify-between sm:justify-center items-center w-full sm:w-auto gap-1">
                        <span
                            class="text-zinc-500 dark:text-zinc-400 font-black text-[10px] uppercase tracking-wider font-sans">Target</span>
                        <span class="font-black text-zinc-700 dark:text-zinc-300 text-xs md:text-sm">Rp
                            {{ number_format($target, 0, ',', '.') }}</span>
                    </div>

                    <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-700"></div>

                    <div
                        class="flex flex-row sm:flex-col justify-between sm:justify-center items-center w-full sm:w-auto gap-1">
                        <span
                            class="text-emerald-600 dark:text-emerald-400 font-black text-[10px] uppercase tracking-wider font-sans">Dibayar</span>
                        <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs md:text-sm">Rp
                            {{ number_format($sudahBayar, 0, ',', '.') }}</span>
                    </div>

                    <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-700"></div>

                    <div
                        class="flex flex-row sm:flex-col justify-between sm:justify-center items-center w-full sm:w-auto gap-1">
                        <span
                            class="text-amber-600 dark:text-amber-400 font-black text-[10px] uppercase tracking-wider font-sans">Kekurangan</span>
                        <span class="font-black text-amber-600 dark:text-amber-400 text-xs md:text-sm">
                            {{ $sisa > 0 ? 'Rp ' . number_format($sisa, 0, ',', '.') : 'LUNAS' }}
                        </span>
                    </div>
                </div>

                <!-- 3. Action Buttons -->
                <div class="flex items-center gap-2 xl:w-[200px] shrink-0 justify-end w-full mt-1 xl:mt-0">

                    @if ($sudahBayar > 0)
                        <a href="{{ route('kas-ruangan.riwayat', ['ruangan' => $ruangan->id, 'murid' => $murid->id]) }}"
                            class="w-10 h-10 shrink-0 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-400 hover:text-primary dark:hover:text-primary-dark rounded-xl border border-zinc-200/80 dark:border-zinc-700 shadow-2xs transition-all flex items-center justify-center active:scale-95 outline-none"
                            title="Lihat Riwayat Cicilan">
                            <i class="bi bi-clock-history text-sm font-bold"></i>
                        </a>
                    @endif

                    @if ($sisa > 0)
                        <button type="button"
                            onclick="bukaModalBayar({{ $murid->id }}, '{{ $murid->nama_lengkap ?? $murid->nama }}', {{ $sisa }}, '{{ date('Y-m-d') }}')"
                            class="m3-btn-primary flex-1 xl:flex-none xl:w-32 h-10 text-xs font-black shadow-2xs">
                            <i class="bi bi-wallet2 text-xs mr-1"></i> Bayar
                        </button>
                    @else
                        <div
                            class="flex-1 xl:flex-none xl:w-32 h-10 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl flex items-center justify-center gap-1.5 text-xs font-black uppercase tracking-wider shadow-2xs">
                            <i class="bi bi-check-all text-base"></i> Lunas
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- MODAL CATAT PEMBAYARAN -->
    <div id="modalBayar"
        class="fixed inset-0 bg-black/60 z-[99] flex items-center justify-center hidden backdrop-blur-sm p-4 transition-all">
        <div class="m3-glass-card !bg-white dark:!bg-[#0c0c0e] w-full max-w-md p-6 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800 mx-auto relative overflow-hidden transform scale-95 opacity-0 transition-all duration-300"
            id="modalBayarContent">

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-1">
                    <div
                        class="w-10 h-10 rounded-xl bg-primary/10 border border-primary/20 text-primary dark:text-primary-dark flex items-center justify-center shadow-2xs">
                        <i class="bi bi-wallet2 text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Catat Pembayaran</h3>
                        <p id="modalNamaMurid" class="text-xs font-bold text-zinc-500 dark:text-zinc-400"></p>
                    </div>
                </div>

                <form action="{{ route('kas-ruangan.bayar') }}" method="POST" class="space-y-4 mt-5">
                    @csrf
                    <input type="hidden" name="ruangan_id" value="{{ $ruangan->id }}">
                    <input type="hidden" name="murid_id" id="inputMuridId">

                    <!-- Tanggal Bayar -->
                    <div>
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Tanggal
                            Bayar</label>
                        <input type="date" name="tanggal_bayar" id="inputTanggal" required
                            class="m3-input-glass w-full text-xs font-bold">
                    </div>

                    <!-- Jumlah Pembayaran -->
                    <div>
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Jumlah
                            Pembayaran</label>
                        <div class="relative group">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-zinc-400 pointer-events-none text-xs font-mono">Rp</span>
                            <input type="number" name="jumlah_bayar" id="inputJumlah" required min="1"
                                class="m3-input-glass w-full !pl-10 font-mono font-black text-base text-zinc-900 dark:text-white"
                                placeholder="0">
                        </div>
                        <p id="modalSisaTeks"
                            class="text-[10px] font-black text-amber-600 dark:text-amber-400 mt-1.5 ml-1 uppercase tracking-wider">
                        </p>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex gap-2.5 pt-3">
                        <button type="button" onclick="tutupModalBayar()"
                            class="flex-1 h-10 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl shadow-2xs transition-all outline-none active:scale-95">
                            Batal
                        </button>
                        <button type="submit"
                            class="m3-btn-primary flex-1 h-10 text-xs font-black shadow-2xs">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script & Style -->
    <script>
        function bukaModalBayar(muridId, nama, sisa, tgl) {
            document.getElementById('inputMuridId').value = muridId;
            document.getElementById('modalNamaMurid').innerText = nama;
            document.getElementById('inputJumlah').value = sisa;
            document.getElementById('inputJumlah').max = sisa;
            document.getElementById('inputTanggal').value = tgl;
            document.getElementById('modalSisaTeks').innerText = `* Sisa pelunasan: Rp ` + sisa.toLocaleString('id-ID');

            const modal = document.getElementById('modalBayar');
            const content = document.getElementById('modalBayarContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function tutupModalBayar() {
            const modal = document.getElementById('modalBayar');
            const content = document.getElementById('modalBayarContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</x-app-layout>

