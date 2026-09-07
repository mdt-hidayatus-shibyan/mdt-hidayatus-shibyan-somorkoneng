@section('title', 'Daftar Pengajuan Penarikan Dana Tabungan')
<x-app-layout>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Pengajuan Penarikan Dana
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Alur persetujuan (approval workflow) dan pencairan penarikan tabungan nasabah.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto">
            <form action="{{ route('tabungan.pengajuan.index') }}" method="GET"
                class="flex items-center gap-2 w-full sm:w-auto">
                <select name="status" onchange="this.form.submit()" class="m3-input-glass text-xs font-bold py-1.5 px-3">
                    <option value="">-- Semua Status --</option>
                    @foreach (['Menunggu', 'Disetujui', 'Dicairkan', 'Ditolak'] as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                            {{ $st }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if (session('success'))
        <div
            class="mb-5 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div
            class="mb-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 text-xs font-bold flex items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- SUMMARY CARD PENGAJUAN -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="m3-glass-card rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 text-xl font-black">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-zinc-400">Menunggu Persetujuan</span>
                <h3 class="text-base font-black text-amber-600 dark:text-amber-400">{{ $countMenunggu }} Pengajuan</h3>
                <p class="text-[10px] text-zinc-500">Perlu diverifikasi admin</p>
            </div>
        </div>

        <div class="m3-glass-card rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 text-xl font-black">
                <i class="bi bi-check2-all"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-zinc-400">Siap Dicairkan</span>
                <h3 class="text-base font-black text-blue-600 dark:text-blue-400">{{ $countDisetujui }} Pengajuan</h3>
                <p class="text-[10px] text-zinc-500">Telah disetujui admin</p>
            </div>
        </div>

        <div class="m3-glass-card rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
            <div
                class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-xl font-black">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase text-zinc-400">Total Telah Dicairkan</span>
                <h3 class="text-base font-black text-emerald-600 dark:text-emerald-400">
                    Rp {{ number_format($totalDicairkan, 0, ',', '.') }}
                </h3>
                <p class="text-[10px] text-zinc-500">Dana telah diserahkan</p>
            </div>
        </div>
    </div>

    <!-- TABEL PENGAJUAN PENARIKAN -->
    @if ($pengajuans->isNotEmpty())
        <div class="m3-glass-card rounded-2xl overflow-hidden shadow-2xs">
            <div
                class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                <span
                    class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-cash-stack text-amber-600 text-sm"></i>
                    Daftar Pengajuan Penarikan Tabungan (Total {{ $pengajuans->total() }} Data)
                </span>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr
                            class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-3">Kode & Tanggal</th>
                            <th class="py-3 px-3">Rekening & Nasabah</th>
                            <th class="py-3 px-3 text-right">Nominal Pengajuan</th>
                            <th class="py-3 px-3 text-right">Potongan</th>
                            <th class="py-3 px-3 text-right">Estimasi Bersih</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3">Alasan / Catatan</th>
                            <th class="py-3 px-4 text-center">Aksi / Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium text-zinc-700 dark:text-zinc-300">
                        @foreach ($pengajuans as $p)
                            @php
                                $tab = $p->tabungan;
                            @endphp
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="py-3 px-4 text-center font-bold text-zinc-400">
                                    {{ ($pengajuans->currentPage() - 1) * $pengajuans->perPage() + $loop->iteration }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-mono font-bold text-zinc-900 dark:text-white block">
                                        {{ $p->kode_pengajuan }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400">
                                        {{ $p->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <x-avatar :name="$tab->nama_nasabah" size="sm" />
                                        <div>
                                            <a href="{{ route('tabungan.rekening.detail', $tab->id) }}"
                                                class="font-bold text-primary dark:text-primary-dark hover:underline block leading-tight">
                                                {{ $tab->nama_nasabah }}
                                            </a>
                                            <span class="text-[10px] text-zinc-400 font-mono">
                                                {{ $tab->nomor_rekening }} ({{ $tab->jenis_nasabah }})
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold text-zinc-900 dark:text-white">
                                    Rp {{ number_format($p->nominal_pengajuan, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-rose-600 dark:text-rose-400">
                                    Rp {{ number_format($p->nominal_potongan, 0, ',', '.') }}
                                    <span
                                        class="text-[10px] block text-zinc-400">({{ $p->persentase_potongan }}%)</span>
                                </td>
                                <td
                                    class="py-3 px-3 text-right font-mono font-black text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($p->nominal_bersih_diterima, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if ($p->status == 'Menunggu')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                            Menunggu
                                        </span>
                                    @elseif ($p->status == 'Disetujui')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                            Disetujui
                                        </span>
                                    @elseif ($p->status == 'Dicairkan')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            Dicairkan
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-zinc-500 text-[11px] max-w-xs">
                                    <div>{{ $p->alasan_penarikan ?? '-' }}</div>
                                    @if ($p->catatan_admin)
                                        <div class="text-[10px] text-zinc-400 italic mt-0.5">Admin:
                                            {{ $p->catatan_admin }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if ($p->status == 'Menunggu')
                                            <!-- Tombol Setujui -->
                                            <form action="{{ route('tabungan.pengajuan.setujui', $p->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan penarikan ini?')">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] px-2.5 py-1 rounded-lg transition shadow-xs flex items-center gap-1 cursor-pointer">
                                                    <i class="bi bi-check-lg"></i> Setujui
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak -->
                                            <button type="button"
                                                onclick="openModalTolak({{ $p->id }}, '{{ $p->kode_pengajuan }}')"
                                                class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-[10px] px-2.5 py-1 rounded-lg transition shadow-xs flex items-center gap-1 cursor-pointer">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        @elseif ($p->status == 'Disetujui')
                                            <!-- Tombol Cairkan Dana -->
                                            <form action="{{ route('tabungan.pengajuan.cairkan', $p->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin mencairkan dana penarikan ini sekarang? Saldo tabungan akan otomatis terpotong.')">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[10px] px-3 py-1 rounded-lg transition shadow-xs flex items-center gap-1 cursor-pointer">
                                                    <i class="bi bi-cash"></i> Cairkan Dana
                                                </button>
                                            </form>
                                        @elseif ($p->status == 'Dicairkan')
                                            <x-button :href="route('tabungan.pengajuan.kwitansi', $p->id)" target="_blank" variant="secondary"
                                                size="sm" icon="bi-printer">
                                                Kwitansi
                                            </x-button>
                                        @else
                                            <span class="text-[10px] text-zinc-400 italic">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($pengajuans->hasPages())
                <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
                    {{ $pengajuans->links() }}
                </div>
            @endif
        </div>
    @else
        <x-empty-state icon="bi-cash-coin" title="Tidak Ada Pengajuan Penarikan"
            message="Belum ada permohonan penarikan tabungan yang diajukan." />
    @endif

    <!-- MODAL TOLAK PENGAJUAN -->
    <div id="modalTolak"
        class="fixed inset-0 z-50 hidden bg-zinc-900/60 backdrop-blur-xs items-center justify-center p-4">
        <div class="m3-glass-card rounded-2xl w-full max-w-md p-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-x-circle-fill text-rose-600"></i>
                    Penolakan Pengajuan Penarikan
                </h3>
                <button type="button" onclick="closeModalTolak()"
                    class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form id="formTolak" method="POST">
                @csrf
                <div class="space-y-4 text-xs">
                    <p class="text-zinc-600 dark:text-zinc-300">
                        Kode Pengajuan: <strong id="kodeTolakTxt"
                            class="text-zinc-900 dark:text-white font-mono"></strong>
                    </p>

                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Alasan Penolakan <span
                                class="text-rose-500">*</span></label>
                        <textarea name="alasan_penolakan" rows="3" required
                            placeholder="Jelaskan alasan mengapa pengajuan ini ditolak..." class="m3-input-glass w-full"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <x-button type="button" onclick="closeModalTolak()" variant="secondary" size="sm">
                        Batal
                    </x-button>
                    <button type="submit"
                        class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl text-xs font-black shadow-xs cursor-pointer">
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openModalTolak(id, kode) {
                document.getElementById('formTolak').action = '/tabungan/pengajuan/' + id + '/tolak';
                document.getElementById('kodeTolakTxt').innerText = kode;
                document.getElementById('modalTolak').classList.remove('hidden');
                document.getElementById('modalTolak').classList.add('flex');
            }

            function closeModalTolak() {
                document.getElementById('modalTolak').classList.remove('flex');
                document.getElementById('modalTolak').classList.add('hidden');
            }
        </script>
    @endpush

</x-app-layout>
