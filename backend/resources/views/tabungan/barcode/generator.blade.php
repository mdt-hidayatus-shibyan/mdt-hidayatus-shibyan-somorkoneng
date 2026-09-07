@section('title', 'Generator Barcode Buku Tabungan')
<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-20 print:hidden">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Generator Barcode Buku Tabungan
            </h2>
            <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5 uppercase tracking-wider">
                Generate 7 digit nomor barcode unik sesuai tahun pelajaran untuk dikirim ke percetakan
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto shrink-0">
            <a href="{{ route('tabungan.barcode.export', ['prefix' => $prefix, 'mulai' => $mulaiDari, 'jumlah' => $jumlah]) }}"
                class="m3-btn-primary h-10 px-4 group/btn shadow-2xs">
                <i class="bi bi-file-earmark-spreadsheet-fill text-sm"></i>
                <span>Download CSV (Excel)</span>
            </a>

            <button type="button" onclick="copyAllBarcodes()"
                class="h-10 inline-flex items-center justify-center px-4 rounded-xl md:rounded-2xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-xs font-black transition-all shadow-2xs active:scale-95 cursor-pointer">
                <i class="bi bi-clipboard-check-fill mr-1.5 text-sm"></i>
                <span id="copyBtnText">Copy {{ count($barcodes) }} Barcode</span>
            </button>

            <button type="button" onclick="window.print()"
                class="m3-btn-secondary h-10 px-3.5 shadow-2xs cursor-pointer" title="Cetak / Simpan PDF">
                <i class="bi bi-printer-fill text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Parameter Config Card -->
    <div class="m3-glass-card p-5 mb-6 rounded-3xl relative z-10 shadow-2xs print:hidden">
        <form action="{{ route('tabungan.barcode.generator') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            
            <!-- 1. Tahun Pelajaran Hijriyah -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Tahun Pelajaran:
                </label>
                <select name="tahun_id" class="m3-input-glass w-full text-xs font-bold">
                    @foreach ($daftarTahun as $t)
                        <option value="{{ $t->id }}" {{ $selectedTahun->id == $t->id ? 'selected' : '' }}>
                            {{ $t->nama_hijriyah }} ({{ $t->nama_masehi }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Mulai Nomor Urut -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Nomor Urut Awal:
                </label>
                <input type="number" name="mulai" value="{{ $mulaiDari }}" min="1" max="999"
                    class="m3-input-glass w-full text-xs font-mono font-bold">
            </div>

            <!-- 3. Jumlah Barcode -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-1.5">
                    Jumlah Barcode:
                </label>
                <input type="number" name="jumlah" value="{{ $jumlah }}" min="10" max="1000" step="10"
                    class="m3-input-glass w-full text-xs font-mono font-bold">
            </div>

            <!-- 4. Submit Button -->
            <div>
                <button type="submit" class="m3-btn-primary w-full h-10 text-xs font-black">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Generate Ulang</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Overview Barcode Info Card -->
    <div class="p-5 rounded-3xl bg-primary/10 border border-primary/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 shadow-2xs">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-primary/20 text-primary dark:text-primary-dark flex items-center justify-center text-xl font-bold border border-primary/30 shrink-0">
                <i class="bi bi-upc-scan"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-zinc-900 dark:text-white">
                    Format 7 Digit: <span class="font-mono text-primary dark:text-primary-dark text-base">{{ $prefix }}001</span> s.d. <span class="font-mono text-primary dark:text-primary-dark text-base">{{ $prefix }}{{ str_pad($mulaiDari + $jumlah - 1, 3, '0', STR_PAD_LEFT) }}</span>
                </h3>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                    Prefix Tahun Hijriyah: <strong class="font-mono font-black text-primary">{{ $prefix }}</strong> &bull; Total Dihasilkan: <strong class="text-zinc-900 dark:text-white">{{ count($barcodes) }} Baris Barcode</strong>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-center">
            <span class="px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-black border border-emerald-500/20">
                Siap Kirim ke Percetakan
            </span>
        </div>
    </div>

    <!-- TABEL LIST BARCODE -->
    <div class="m3-glass-card overflow-hidden rounded-3xl shadow-2xs relative z-10">
        <div class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-950/40 flex justify-between items-center">
            <span class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-list-ol text-primary text-sm"></i>
                Daftar Barcode ({{ count($barcodes) }} Baris)
            </span>
            <span class="text-[11px] font-semibold text-zinc-400">
                *Klik nomor barcode untuk copy satu per satu
            </span>
        </div>

        <div class="max-h-[600px] overflow-y-auto custom-scrollbar p-3">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5">
                @foreach ($barcodes as $item)
                    <div onclick="copySingleBarcode('{{ $item['barcode'] }}', this)"
                        class="p-3 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 hover:border-primary hover:bg-primary/5 transition-all cursor-pointer flex flex-col justify-between gap-2 group/card">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-zinc-400">#{{ $item['no'] }}</span>
                            @if ($item['is_registered'])
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                    Dipakai
                                </span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                    Tersedia
                                </span>
                            @endif
                        </div>
                        <div class="text-center py-1">
                            <span class="font-mono font-black text-sm tracking-wider text-zinc-900 dark:text-white group-hover/card:text-primary transition-colors block">
                                {{ $item['barcode'] }}
                            </span>
                            <span class="text-[9px] text-zinc-400 font-semibold mt-0.5 block">
                                7 Digit
                            </span>
                        </div>
                        <div class="text-[9px] text-center text-primary dark:text-primary-dark font-bold opacity-0 group-hover/card:opacity-100 transition-opacity">
                            <i class="bi bi-clipboard"></i> Klik Copy
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Hidden Raw Barcode Textarea for Copying -->
    <textarea id="rawBarcodesText" class="sr-only">@foreach($barcodes as $b){{ $b['barcode'] }}
@endforeach</textarea>

    <script>
        function copyAllBarcodes() {
            const rawText = document.getElementById('rawBarcodesText').value;
            navigator.clipboard.writeText(rawText).then(() => {
                const btnText = document.getElementById('copyBtnText');
                const orig = btnText.innerText;
                btnText.innerText = 'Tersalin ke Clipboard!';
                setTimeout(() => {
                    btnText.innerText = orig;
                }, 2000);
            });
        }

        function copySingleBarcode(code, el) {
            navigator.clipboard.writeText(code).then(() => {
                const originalBorder = el.style.borderColor;
                el.style.borderColor = '#10b981';
                setTimeout(() => {
                    el.style.borderColor = originalBorder;
                }, 1000);
            });
        }
    </script>

</x-app-layout>