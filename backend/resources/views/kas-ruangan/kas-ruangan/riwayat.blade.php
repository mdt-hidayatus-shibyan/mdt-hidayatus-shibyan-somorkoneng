<x-app-layout>

    <!-- HEADER -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-20">
        <div class="flex items-center gap-3">
            <!-- Tombol Kembali -->
            <a href="{{ route('kas-ruangan.show', $ruangan->id) }}"
                class="w-10 h-10 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 rounded-xl flex items-center justify-center transition-all shadow-2xs active:scale-95 shrink-0"
                title="Kembali">
                <i class="bi bi-arrow-left text-base font-bold"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ $murid->nama_lengkap ?? $murid->nama }}
                </h2>
                <p class="text-xs md:text-[13px] text-zinc-500 dark:text-zinc-400 font-medium mt-0.5">
                    Riwayat Pembayaran Kas Kelas: <span class="text-primary dark:text-primary-dark font-black">{{ $ruangan->nama_ruangan }}</span>
                </p>
            </div>
        </div>
    </div>

    @php
        $totalTerkumpul = $riwayats->sum('jumlah_bayar');
        $jumlahTransaksi = $riwayats->count();
    @endphp

    <!-- RINGKASAN PEMBAYARAN (M3 Glass Card) -->
    <div
        class="mb-6 m3-glass-card p-5 md:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10 shadow-2xs">
        <div class="flex items-center gap-3.5">
            <div
                class="w-12 h-12 rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark border border-primary/20 flex items-center justify-center shadow-2xs shrink-0">
                <i class="bi bi-wallet2 text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">Total Telah Dibayar</p>
                <h3 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight leading-none font-mono">
                    Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <div
            class="w-full sm:w-auto flex items-center justify-between sm:justify-end gap-6 border-t sm:border-t-0 sm:border-l border-zinc-200/80 dark:border-zinc-800 pt-3 sm:pt-0 sm:pl-6">
            <div>
                <p
                    class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5 sm:text-right">
                    Total Transaksi</p>
                <h4
                    class="text-base md:text-lg font-black text-zinc-800 dark:text-zinc-200 tracking-tight leading-none sm:text-right">
                    {{ $jumlahTransaksi }} Kali
                </h4>
            </div>
        </div>
    </div>

    <!-- DAFTAR RIWAYAT (M3 Glass Cards) -->
    <div class="flex flex-col gap-3 relative z-10">
        @forelse ($riwayats as $item)
            <div
                class="m3-glass-card p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all shadow-2xs hover:border-primary/40 dark:hover:border-primary-dark/40 group">

                <div class="flex items-center gap-3.5 md:w-[40%] shrink-0">
                    <div
                        class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 flex items-center justify-center shadow-2xs shrink-0">
                        <i class="bi bi-calendar2-check text-lg"></i>
                    </div>
                    <div>
                        <p
                            class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-0.5">
                            {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y') }}
                        </p>
                        <h4 class="font-black text-zinc-900 dark:text-white text-base tracking-tight leading-tight font-mono">
                            Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>

                <div
                    class="flex-1 flex items-center md:justify-center border-t border-b md:border-none border-zinc-200/80 dark:border-zinc-800 py-2.5 md:py-0">
                    @if ($item->is_disetor)
                        <span
                            class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl text-[10px] font-black uppercase tracking-wider shadow-2xs inline-flex items-center">
                            <i class="bi bi-lock-fill mr-1 text-xs"></i> Disetor ke Brankas
                        </span>
                    @else
                        <span
                            class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 rounded-xl text-[10px] font-black uppercase tracking-wider shadow-2xs inline-flex items-center">
                            <i class="bi bi-wallet2 mr-1 text-xs"></i> Masih di Wali
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2 md:w-[120px] shrink-0 justify-end">
                    @if ($item->is_disetor)
                        <span
                            class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 italic uppercase tracking-wider bg-zinc-100 dark:bg-zinc-800 px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-2xs w-full text-center">
                            Terkunci
                        </span>
                    @else
                        <button type="button"
                            onclick="bukaModalEdit({{ $item->id }}, '{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('Y-m-d') }}', {{ $item->jumlah_bayar }})"
                            class="w-9 h-9 flex items-center justify-center bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/20 rounded-xl transition-all shadow-2xs active:scale-90 outline-none"
                            title="Edit Data">
                            <i class="bi bi-pencil-fill text-xs"></i>
                        </button>
                        <button type="button" onclick="hapusRiwayat({{ $item->id }})"
                            class="w-9 h-9 flex items-center justify-center bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 rounded-xl transition-all shadow-2xs active:scale-90 outline-none"
                            title="Hapus Data">
                            <i class="bi bi-trash3-fill text-xs"></i>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <x-empty-state icon="bi-receipt" title="Belum Ada Transaksi" message="Catatan pembayaran kas dari santri ini akan muncul di sini." />
        @endforelse
    </div>

    <!-- ========================================== -->
    <!-- MODAL EDIT CICILAN                         -->
    <!-- ========================================== -->
    <div id="modalEdit"
        class="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center hidden backdrop-blur-sm p-4 transition-all">
        <div class="m3-glass-card !bg-white dark:!bg-[#0c0c0e] w-full max-w-sm p-6 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800 mx-auto relative overflow-hidden transform scale-95 opacity-0 transition-all duration-300"
            id="modalEditContent">

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-5">
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shadow-2xs">
                        <i class="bi bi-pencil-fill text-base"></i>
                    </div>
                    <h3 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Edit Cicilan</h3>
                </div>

                <form id="formEdit" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Tanggal Bayar -->
                    <div>
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Tanggal
                            Bayar</label>
                        <input type="date" name="tanggal_bayar" id="editTanggal" required
                            class="m3-input-glass w-full text-xs font-bold">
                    </div>

                    <!-- Jumlah Bayar -->
                    <div>
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-1.5 ml-1">Jumlah
                            Bayar (Rp)</label>
                        <div class="relative group">
                            <span
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-black text-zinc-400 pointer-events-none text-xs font-mono">Rp</span>
                            <input type="number" name="jumlah_bayar" id="editJumlah" required min="1"
                                class="m3-input-glass w-full !pl-10 font-mono font-black text-base text-zinc-900 dark:text-white">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2.5 pt-3 border-t border-zinc-200/80 dark:border-zinc-800">
                        <button type="button" onclick="tutupModalEdit()"
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
    <form id="formHapus" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        const baseRouteUrl = "{{ url('kas-ruangan/bayar') }}";

        function bukaModalEdit(id, tgl, jml) {
            const form = document.getElementById('formEdit');
            form.action = `${baseRouteUrl}/${id}`;

            document.getElementById('editTanggal').value = tgl;
            document.getElementById('editJumlah').value = jml;

            const modal = document.getElementById('modalEdit');
            const content = document.getElementById('modalEditContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function tutupModalEdit() {
            const modal = document.getElementById('modalEdit');
            const content = document.getElementById('modalEditContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function hapusRiwayat(id) {
            const isDark = document.documentElement.classList.contains('dark');
            Swal.fire({
                title: '<span class="text-base font-black tracking-tight">Hapus Catatan?</span>',
                html: '<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Total kas akan otomatis berkurang. Tindakan ini tidak dapat dibatalkan!</p>',
                icon: 'warning',
                showCancelButton: true,
                heightAuto: false,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#71717a',
                confirmButtonText: 'Ya, Hapus!',
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
                        title: '<span class="text-base font-black tracking-tight">Menghapus...</span>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        background: isDark ? '#0c0c0e' : '#ffffff',
                        color: isDark ? '#f4f4f5' : '#18181b',
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const form = document.getElementById('formHapus');
                    form.action = `${baseRouteUrl}/${id}`;
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>

