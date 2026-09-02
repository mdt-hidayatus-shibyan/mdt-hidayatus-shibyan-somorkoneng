<x-app-layout>

    <!-- HEADER (Struktur Sejajar) -->
    <div
        class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-20 print:hidden">

        <!-- Sisi Kiri: Tombol Back & Judul -->
        <div class="flex items-center gap-3">
            <a href="{{ route('setoran-kas-ruangan.index') }}"
                class="w-10 h-10 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 rounded-xl flex items-center justify-center transition-all shadow-2xs active:scale-95 shrink-0"
                title="Kembali">
                <i class="bi bi-arrow-left text-base font-bold"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    Riwayat Setoran {{ $ruangan->nama_ruangan }}
                </h2>
                <p class="text-xs md:text-[13px] text-zinc-500 dark:text-zinc-400 font-medium mt-0.5">
                    Catatan histori uang fisik kas yang disetor ke brankas tabungan.
                </p>
            </div>
        </div>

        <!-- Sisi Kanan: Tombol Cetak -->
        <div class="w-full sm:w-auto shrink-0 flex items-center gap-2.5">
            <button type="button" onclick="window.print()"
                class="w-full sm:w-auto h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl text-xs font-black uppercase tracking-wider transition-all active:scale-95 flex items-center justify-center gap-2 shadow-2xs outline-none">
                <i class="bi bi-printer-fill text-xs"></i> Cetak PDF
            </button>
        </div>
    </div>

    <!-- AREA TABEL CETAK -->
    <div id="printArea"
        class="m3-glass-card relative z-10 p-5 md:p-6 print:p-0 print:border-none print:shadow-none print:bg-transparent shadow-2xs">

        <!-- Header Khusus Print -->
        <div class="hidden print:block mb-6 text-center text-black">
            <h1 class="text-2xl font-black uppercase tracking-widest mb-1">Riwayat Setoran Kas</h1>
            <h2 class="text-lg font-bold">Ruangan: {{ $ruangan->nama_ruangan }}</h2>
            <p class="text-sm mt-1 text-gray-600">Dicetak pada: {{ date('d M Y H:i') }}</p>
            <hr class="my-4 border-gray-400 border-2">
        </div>

        <!-- Wrapper Tabel -->
        <div
            class="rounded-xl overflow-hidden print:border-gray-400 print:shadow-none">
            <div class="overflow-x-auto w-full custom-scrollbar">
                <table class="m3-table w-full print:text-black">
                    <thead>
                        <tr>
                            <th class="text-center w-32">
                                Tanggal
                            </th>
                            <th class="text-right">
                                Nominal
                            </th>
                            <th class="hidden md:table-cell print:table-cell">
                                Penerima
                            </th>
                            <th class="hidden md:table-cell print:table-cell">
                                Catatan
                            </th>
                            <th class="text-center w-28 print:hidden">
                                Opsi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($setorans as $item)
                            <tr>
                                <td class="text-center font-mono font-bold text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ \Carbon\Carbon::parse($item->tanggal_setor)->format('Y-m-d') }}
                                </td>
                                <td class="text-right font-black font-mono text-zinc-900 dark:text-white text-base">
                                    Rp {{ number_format($item->jumlah_setor, 0, ',', '.') }}
                                </td>
                                <td class="font-bold text-xs text-zinc-700 dark:text-zinc-300 hidden md:table-cell print:table-cell">
                                    {{ $item->penerima->name ?? 'Admin' }}
                                </td>
                                <td class="text-zinc-500 dark:text-zinc-400 text-xs hidden md:table-cell print:table-cell italic">
                                    {{ $item->keterangan ?: '-' }}
                                </td>
                                <td class="text-center print:hidden">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button"
                                            onclick="bukaModalEditSetor({{ $item->id }}, '{{ \Carbon\Carbon::parse($item->tanggal_setor)->format('Y-m-d') }}', {{ $item->jumlah_setor }}, '{{ $item->keterangan }}')"
                                            class="w-8 h-8 flex items-center justify-center bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded-lg transition-all shadow-2xs active:scale-90 outline-none"
                                            title="Koreksi">
                                            <i class="bi bi-pencil-fill text-xs"></i>
                                        </button>
                                        <button type="button"
                                            onclick="kembalikanSetoran({{ $item->id }}, '{{ number_format($item->jumlah_setor, 0, ',', '.') }}')"
                                            class="w-8 h-8 flex items-center justify-center bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 rounded-lg transition-all shadow-2xs active:scale-90 outline-none"
                                            title="Kembalikan Uang ke Wali">
                                            <i class="bi bi-arrow-return-left text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center">
                                    <x-empty-state icon="bi-receipt" title="Belum Ada Catatan Setoran" message="Data setoran kas untuk ruangan ini belum tersedia." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT SETORAN -->
    <div id="modalEditSetor"
        class="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center hidden backdrop-blur-sm p-4 transition-all print:hidden">
        <div class="m3-glass-card !bg-white dark:!bg-[#0c0c0e] w-full max-w-sm p-6 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800 mx-auto relative overflow-hidden transform scale-95 opacity-0 transition-all duration-300"
            id="modalEditSetorContent">

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-5">
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shadow-2xs shrink-0">
                        <i class="bi bi-pencil-fill text-base"></i>
                    </div>
                    <h3 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                        Koreksi Berkas Setoran
                    </h3>
                </div>

                <form id="formEditSetor" method="POST" class="space-y-4">
                    @csrf @method('PUT')

                    <!-- Tanggal Bayar -->
                    <div>
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Tanggal
                            Terima</label>
                        <input type="date" name="tanggal_setor" id="editTanggalSetor" required
                            class="m3-input-glass w-full text-xs font-bold">
                    </div>

                    <!-- Jumlah Setor -->
                    <div>
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Jumlah
                            Setor (Rp)</label>
                        <div class="relative group">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-zinc-400 pointer-events-none text-xs font-mono">Rp</span>
                            <input type="number" name="jumlah_setor" id="editJumlahSetor" required min="1"
                                oninput="validasiKapasitasEdit(this)"
                                class="m3-input-glass w-full !pl-10 font-mono font-black text-base text-zinc-900 dark:text-white">
                        </div>
                        <p id="teksInfoKapasitas"
                            class="text-[10px] font-black text-amber-600 dark:text-amber-400 mt-1.5 ml-1 uppercase tracking-wider">
                        </p>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Catatan</label>
                        <input type="text" name="keterangan" id="editKeteranganSetor"
                            class="m3-input-glass w-full text-xs font-bold">
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2.5 pt-3 border-t border-zinc-200/80 dark:border-zinc-800">
                        <button type="button" onclick="tutupModalEditSetor()"
                            class="flex-1 h-10 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl shadow-2xs transition-all outline-none active:scale-95">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 h-10 bg-amber-500 hover:bg-amber-600 text-white font-black text-xs rounded-xl shadow-2xs transition-all active:scale-95 outline-none">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Hidden untuk Hapus Riwayat -->
    <form id="formHapusSetor" method="POST" class="hidden print:hidden">
        @csrf @method('DELETE')
    </form>

    <script>
        const baseUrlSetoran = "{{ url('setoran-kas-ruangan') }}";

        // Data dari Controller: Total uang yang SEKARANG sedang dipegang fisik oleh Wali Kelas
        const maxUangDiWaliSaatIni = {{ $diWali }};
        let limitEditDinamis = 0;

        function bukaModalEditSetor(id, tgl, jml, ket) {
            const form = document.getElementById('formEditSetor');
            form.action = `${baseUrlSetoran}/${id}`;
            document.getElementById('editTanggalSetor').value = tgl;
            document.getElementById('editJumlahSetor').value = jml;
            document.getElementById('editKeteranganSetor').value = ket;

            // Batas maksimal = uang di wali saat ini + nominal setoran yang sedang diedit
            limitEditDinamis = jml + maxUangDiWaliSaatIni;

            document.getElementById('editJumlahSetor').max = limitEditDinamis;
            document.getElementById('teksInfoKapasitas').innerText = `* Max Tarik: Rp ` + limitEditDinamis.toLocaleString(
                'id-ID');

            const modal = document.getElementById('modalEditSetor');
            const content = document.getElementById('modalEditSetorContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function validasiKapasitasEdit(input) {
            if (parseFloat(input.value) > limitEditDinamis) {
                input.value = limitEditDinamis;
            }
        }

        function tutupModalEditSetor() {
            const modal = document.getElementById('modalEditSetor');
            const content = document.getElementById('modalEditSetorContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function kembalikanSetoran(id, nominal) {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: '<span class="text-base font-black tracking-tight">Kembalikan Uang?</span>',
                html: `<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Anda akan mengembalikan dana <b>Rp ${nominal}</b> ini kepada Wali Kelas.<br>Status setoran ini akan dibatalkan/dihapus.</p>`,
                icon: 'warning',
                showCancelButton: true,
                heightAuto: false,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#71717a',
                confirmButtonText: 'Ya, Kembalikan!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                background: isDark ? '#0c0c0e' : '#ffffff',
                color: isDark ? '#f4f4f5' : '#18181b',
                customClass: {
                    popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                    confirmButton: 'h-10 px-5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none',
                    cancelButton: 'h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '<span class="text-base font-black tracking-tight">Memproses...</span>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        background: isDark ? '#0c0c0e' : '#ffffff',
                        color: isDark ? '#f4f4f5' : '#18181b',
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    const form = document.getElementById('formHapusSetor');
                    form.action = `${baseUrlSetoran}/${id}`;
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>

