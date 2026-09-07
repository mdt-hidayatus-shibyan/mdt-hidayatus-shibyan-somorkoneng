<!-- Modal Form Edit Potongan Penarikan Tabungan per Periode -->
<form action="{{ route('tabungan.pengaturan.periode.potongan.update', $periode->id) }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @method('PUT')

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                <i class="bi bi-percent"></i>
            </div>
            <div>
                <h3 class="text-base font-black text-zinc-900 dark:text-white tracking-tight">
                    Konfigurasi Potongan Penarikan
                </h3>
                <p class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400">
                    Periode: <span class="text-primary font-bold">{{ $periode->nama_periode }}</span>
                </p>
            </div>
        </div>
        <!-- Touch Target 40px -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1 space-y-4">

        <div
            class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-xs flex items-start gap-2">
            <i class="bi bi-info-circle-fill text-sm mt-0.5 shrink-0"></i>
            <div>
                <strong>Keputusan Musyawarah:</strong> Persentase potongan ini akan berlaku untuk setiap transaksi
                penarikan maupun pembagian akhir tabungan pada periode <strong>{{ $periode->nama_periode }}</strong>.
            </div>
        </div>

        @php
            $kategoris = [
                'Murid' => [
                    'default' => 10.0,
                    'desc' => 'Dikenakan saat penarikan / pembagian tabungan murid',
                    'icon' => 'bi-mortarboard',
                    'color' => 'rose',
                ],
                'Ustadz' => [
                    'default' => 2.5,
                    'desc' => 'Infaq sukarela dewan asatidz madrasah',
                    'icon' => 'bi-person-badge',
                    'color' => 'blue',
                ],
                'Kas Ruangan' => [
                    'default' => 0.0,
                    'desc' => 'Kas operasional kelas (bebas potongan)',
                    'icon' => 'bi-door-closed',
                    'color' => 'emerald',
                ],
                'Umum' => [
                    'default' => 10.0,
                    'desc' => 'Infaq pembangunan dari nasabah umum / donatur',
                    'icon' => 'bi-people',
                    'color' => 'purple',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            @foreach ($kategoris as $jenis => $info)
                @php
                    $cfg = $periode->potongans ? $periode->potongans->where('jenis_nasabah', $jenis)->first() : null;
                    $valPersen = $cfg ? $cfg->persentase_potongan : $info['default'];
                    $musyawarah = $cfg?->dasar_musyawarah;
                @endphp
                <div
                    class="p-3.5 rounded-2xl bg-zinc-50/70 dark:bg-zinc-900/50 border border-zinc-200/80 dark:border-zinc-800 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-6 h-6 rounded-lg bg-{{ $info['color'] }}-500/10 text-{{ $info['color'] }}-600 dark:text-{{ $info['color'] }}-400 flex items-center justify-center text-xs">
                                <i class="bi {{ $info['icon'] }}"></i>
                            </div>
                            <span
                                class="font-black text-zinc-900 dark:text-white uppercase tracking-wider text-xs">{{ $jenis }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="number" name="potongan[{{ $jenis }}][persentase]"
                                value="{{ $valPersen }}" step="0.01" min="0" max="100" required
                                class="m3-input-glass w-20 text-center font-mono font-bold text-xs !py-1 text-rose-600 dark:text-rose-400">
                            <span class="font-bold text-zinc-500">%</span>
                        </div>
                    </div>

                    <p class="text-[10px] text-zinc-400">{{ $info['desc'] }}</p>

                    <div>
                        <label class="block text-[10px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">
                            Dasar Keputusan / Hasil Musyawarah
                        </label>
                        <input type="text" name="potongan[{{ $jenis }}][dasar_musyawarah]"
                            value="{{ $musyawarah }}" placeholder="Contoh: Rapat Pengurus Tabungan 2026"
                            class="m3-input-glass w-full text-[11px] !py-1">
                    </div>

                    @if ($cfg?->userPengubah)
                        <span
                            class="text-[9px] text-zinc-400 block pt-1 border-t border-zinc-200/50 dark:border-zinc-800/50">
                            Diubah: {{ $cfg->userPengubah->name }} ({{ $cfg->updated_at->format('d/m/Y H:i') }})
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300 shrink-0">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>Simpan Potongan Periode</span>
        </button>
        <button type="button" data-dismiss="modal" command="close"
            class="m3-btn-secondary w-full sm:w-auto mt-2 sm:mt-0">
            Batal
        </button>
    </div>
</form>
