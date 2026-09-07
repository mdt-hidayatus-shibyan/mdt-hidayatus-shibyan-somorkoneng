@section('title', 'Simulasi & Eksekusi Pembagian Tabungan Murid')
<x-app-layout>

    <!-- Header Section -->
    <div class="mb-6 flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 relative z-10">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Pembagian Akhir Tabungan
            </h2>
            <p class="text-xs md:text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Simulasi rekapitulasi, cetak laporan, dan eksekusi tutup buku tabungan berjangka murid.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-2.5 w-full xl:w-auto">
            <form action="{{ route('tabungan.pembagian.index') }}" method="GET"
                class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <select name="periode_id" onchange="this.form.submit()"
                    class="m3-input-glass text-xs font-bold py-1.5 px-3">
                    @foreach ($periodes as $per)
                        <option value="{{ $per->id }}" {{ $periodeId == $per->id ? 'selected' : '' }}>
                            {{ $per->nama_periode }} {{ $per->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                <select name="ruangan_id" onchange="this.form.submit()"
                    class="m3-input-glass text-xs font-bold py-1.5 px-3">
                    <option value="">-- Semua Kelas / Ruangan --</option>
                    @foreach ($daftarRuangan as $r)
                        <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                            Kelas: {{ $r->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if ($simulasi && $simulasi['total_rekening'] > 0)
                <x-button :href="route('tabungan.pembagian.cetak') . '?periode_id=' . $periodeId . '&ruangan_id=' . request('ruangan_id')" target="_blank" variant="secondary" size="sm" icon="bi-printer">
                    Cetak Laporan
                </x-button>

                <button type="button" onclick="openModalEksekusi()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-3.5 py-1.5 text-xs font-black rounded-xl shrink-0 flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <i class="bi bi-gift-fill"></i>
                    <span>Eksekusi Pembagian</span>
                </button>
            @endif
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

    @if ($simulasi)
        <!-- SUMMARY CARD SIMULASI PEMBAGIAN -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="m3-glass-card rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
                <div
                    class="w-11 h-11 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 text-xl font-black">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-zinc-400">Total Murid Berisi</span>
                    <h3 class="text-base font-black text-zinc-900 dark:text-white">{{ $simulasi['total_rekening'] }}
                        Rekening</h3>
                    <p class="text-[10px] text-zinc-500">Memiliki saldo tabungan</p>
                </div>
            </div>

            <div class="m3-glass-card rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
                <div
                    class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 text-xl font-black">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-zinc-400">Total Tabungan Kotor</span>
                    <h3 class="text-base font-black text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($simulasi['total_saldo_kotor'], 0, ',', '.') }}
                    </h3>
                    <p class="text-[10px] text-zinc-500">Saldo tabungan tersimpan</p>
                </div>
            </div>

            <div class="m3-glass-card rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
                <div
                    class="w-11 h-11 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 text-xl font-black">
                    <i class="bi bi-percent"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-zinc-400">Total Potongan
                        ({{ $simulasi['persentase_potongan'] }}%)</span>
                    <h3 class="text-base font-black text-rose-600 dark:text-rose-400">
                        Rp {{ number_format($simulasi['total_potongan'], 0, ',', '.') }}
                    </h3>
                    <p class="text-[10px] text-zinc-500">Masuk kas/infaq madrasah</p>
                </div>
            </div>

            <div class="m3-glass-card rounded-2xl p-4 flex items-center gap-3.5 shadow-2xs">
                <div
                    class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-xl font-black">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase text-zinc-400">Total Bersih Diserahkan</span>
                    <h3 class="text-base font-black text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($simulasi['total_saldo_bersih'], 0, ',', '.') }}
                    </h3>
                    <p class="text-[10px] text-zinc-500">Uang siap dibagikan</p>
                </div>
            </div>
        </div>

        @if (!empty($simulasi['rincian']) && count($simulasi['rincian']) > 0)
            <!-- TABEL RINCIAN SIMULASI PEMBAGIAN -->
            <div class="m3-glass-card rounded-2xl overflow-hidden shadow-2xs">
                <div
                    class="p-4 bg-zinc-50/80 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-800 flex justify-between items-center">
                    <span
                        class="font-black text-xs text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="bi bi-gift text-purple-600 text-sm"></i>
                        Rincian Pembagian Tabungan Murid (Periode: {{ $simulasi['periode']->nama_periode }})
                    </span>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] font-black uppercase text-zinc-400 dark:text-zinc-500">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-3">Identitas Murid</th>
                                <th class="py-3 px-3">Kelas / Ruangan</th>
                                <th class="py-3 px-3">Nomor Rekening</th>
                                <th class="py-3 px-3 text-right">Saldo Kotor</th>
                                <th class="py-3 px-3 text-right">Potongan ({{ $simulasi['persentase_potongan'] }}%)
                                </th>
                                <th class="py-3 px-3 text-right">Uang Bersih Diterima</th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-zinc-100 dark:divide-zinc-800/60 font-medium text-zinc-700 dark:text-zinc-300">
                            @foreach ($simulasi['rincian'] as $r)
                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="py-3 px-4 text-center font-bold text-zinc-400">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2.5">
                                            <x-avatar :name="$r['murid']?->nama_lengkap ?? $r['nama_murid']" size="sm" />
                                            <div>
                                                <span
                                                    class="font-bold text-zinc-900 dark:text-white text-xs block leading-tight">
                                                    {{ $r['murid']?->nama_lengkap ?? $r['nama_murid'] }}
                                                </span>
                                                <span class="text-[10px] font-mono text-zinc-400">
                                                    NISM: {{ $r['nism'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-zinc-600 dark:text-zinc-400">
                                        {{ $r['ruangan']?->nama_ruangan ?? $r['nama_ruangan'] }}
                                    </td>
                                    <td class="py-3 px-3 font-mono font-bold text-primary dark:text-primary-dark">
                                        {{ $r['nomor_rekening'] }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono font-bold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($r['saldo_kotor'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-mono text-rose-600 dark:text-rose-400">
                                        Rp {{ number_format($r['potongan'], 0, ',', '.') }}
                                    </td>
                                    <td
                                        class="py-3 px-3 text-right font-mono font-black text-xs text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($r['saldo_bersih'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr
                                class="bg-zinc-50 dark:bg-zinc-900 font-bold border-t border-zinc-200 dark:border-zinc-800">
                                <td colspan="4"
                                    class="py-3 px-4 text-right font-black uppercase text-zinc-600 dark:text-zinc-300">
                                    TOTAL KESELURUHAN:</td>
                                <td class="py-3 px-3 text-right font-mono text-zinc-900 dark:text-white">Rp
                                    {{ number_format($simulasi['total_saldo_kotor'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono text-rose-600">Rp
                                    {{ number_format($simulasi['total_potongan'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-black text-emerald-600">Rp
                                    {{ number_format($simulasi['total_saldo_bersih'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @else
            <x-empty-state icon="bi-gift" title="Belum Ada Rekening Bersaldo"
                message="Tidak ada rekening tabungan murid yang memiliki saldo aktif pada periode ini." />
        @endif

        <!-- MODAL EKSEKUSI PEMBAGIAN MASSAL -->
        <div id="modalEksekusi"
            class="fixed inset-0 z-50 hidden bg-zinc-900/60 backdrop-blur-xs items-center justify-center p-4">
            <div class="m3-glass-card rounded-2xl w-full max-w-md p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-gift-fill text-purple-600"></i>
                        Konfirmasi Eksekusi Pembagian Tabungan
                    </h3>
                    <button type="button" onclick="closeModalEksekusi()"
                        class="text-zinc-400 hover:text-zinc-700 dark:hover:text-white">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form action="{{ route('tabungan.pembagian.eksekusi') }}" method="POST">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $periodeId }}">
                    <input type="hidden" name="ruangan_id" value="{{ request('ruangan_id') }}">

                    <div class="space-y-4 text-xs">
                        <div
                            class="p-3.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-800 dark:text-purple-300">
                            <p class="font-bold mb-1">Perhatian:</p>
                            <p class="text-[11px]">Proses ini akan mendebit saldo
                                <strong>{{ $simulasi['total_rekening'] }} rekening murid</strong> menjadi Rp 0,
                                mencatat mutasi penarikan dan potongan, serta mengunci periode tabungan ini.
                            </p>
                        </div>

                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Tanggal Eksekusi
                                Pembagian</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                class="m3-input-glass w-full">
                        </div>

                        <div
                            class="p-3 rounded-xl bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-1">
                            <div class="flex justify-between text-zinc-500">
                                <span>Total Tabungan Kotor:</span>
                                <span class="font-bold">Rp
                                    {{ number_format($simulasi['total_saldo_kotor'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-rose-600">
                                <span>Total Potongan ({{ $simulasi['persentase_potongan'] }}%):</span>
                                <span class="font-bold">Rp
                                    {{ number_format($simulasi['total_potongan'], 0, ',', '.') }}</span>
                            </div>
                            <div
                                class="flex justify-between font-black text-emerald-600 pt-1 border-t border-zinc-200 dark:border-zinc-800">
                                <span>Total Uang Bersih:</span>
                                <span class="font-black">Rp
                                    {{ number_format($simulasi['total_saldo_bersih'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <x-button type="button" onclick="closeModalEksekusi()" variant="secondary" size="sm">
                            Batal
                        </x-button>
                        <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-xl text-xs font-black shadow-xs cursor-pointer">
                            Ya, Eksekusi Pembagian
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
            <script>
                function openModalEksekusi() {
                    document.getElementById('modalEksekusi').classList.remove('hidden');
                    document.getElementById('modalEksekusi').classList.add('flex');
                }

                function closeModalEksekusi() {
                    document.getElementById('modalEksekusi').classList.remove('flex');
                    document.getElementById('modalEksekusi').classList.add('hidden');
                }
            </script>
        @endpush
    @else
        <x-empty-state icon="bi-calendar-x" title="Pilih Periode Tabungan"
            message="Silakan pilih periode tabungan untuk melihat simulasi pembagian." />
    @endif

</x-app-layout>
