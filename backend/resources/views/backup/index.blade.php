@section('title', 'Backup & Restore Database')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 relative z-20">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Backup & Restore
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-1">
                Kelola pencadangan dan pemulihan data sistem secara berkala.
            </p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-6 relative z-10">

        <!-- ================= CARD BACKUP (KIRI) ================= -->
        <div
            class="lg:col-span-7 m3-glass-card p-6 md:p-8 flex flex-col items-center justify-center text-center min-h-[340px] relative overflow-hidden group rounded-3xl shadow-2xs">

            <div class="relative z-10 w-full max-w-sm flex flex-col items-center">
                <div
                    class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mb-5 shadow-2xs group-hover:scale-105 transition-transform">
                    <i class="bi bi-cloud-arrow-down-fill"></i>
                </div>

                <h3 class="text-lg md:text-xl font-black text-zinc-900 dark:text-white mb-2 tracking-tight">
                    Unduh Cadangan Sistem
                </h3>
                <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 leading-relaxed mb-6">
                    Sistem akan mengekspor seluruh data transaksi dan master menjadi satu file <strong
                        class="text-emerald-600 dark:text-emerald-400 font-black">(.sql)</strong>.
                </p>

                @can('update backup')
                    <form action="{{ route('backup.process') }}" method="POST" id="form-backup" class="w-full">
                        @csrf
                        <button type="button" onclick="konfirmasiBackup()"
                            class="m3-btn-primary w-full h-11 text-xs font-black shadow-2xs flex items-center justify-center gap-2">
                            <i class="bi bi-database-down text-sm"></i> <span>Mulai Backup Sekarang</span>
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- ================= INFO CARDS (KANAN) ================= -->
        <div class="lg:col-span-5 flex flex-col gap-4">

            <!-- Card Peringatan -->
            <div class="m3-glass-card !border-amber-500/30 rounded-3xl p-5 shadow-2xs">
                <div class="flex items-center gap-2.5 mb-3">
                    <div
                        class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm shrink-0">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                    <h4 class="font-black text-amber-600 dark:text-amber-400 tracking-tight text-xs uppercase">
                        Peringatan Keamanan
                    </h4>
                </div>
                <p class="text-xs font-medium text-zinc-600 dark:text-zinc-300 leading-relaxed text-justify">
                    File SQL yang diunduh mengandung informasi sensitif. <strong
                        class="text-amber-600 dark:text-amber-400 font-bold">Simpan file tersebut di tempat yang aman
                        (Flashdisk/Google Drive)</strong>. Jangan pernah mengirim file ini melalui media publik yang
                    tidak terenkripsi.
                </p>
            </div>

            <!-- Card Protokol -->
            <div class="m3-glass-card p-5 flex-1 rounded-3xl shadow-2xs">
                <h4
                    class="font-black text-zinc-900 dark:text-white mb-3.5 tracking-tight flex items-center text-xs uppercase gap-2">
                    <div
                        class="w-7 h-7 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs shrink-0">
                        <i class="bi bi-life-preserver"></i>
                    </div>
                    Protokol Pemulihan
                </h4>
                <ul class="space-y-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                    <li class="flex items-start gap-2.5">
                        <div
                            class="w-5 h-5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 text-[10px] mt-0.5">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <span class="leading-relaxed">Gunakan fitur <strong
                                class="text-zinc-900 dark:text-white font-bold">Restore</strong> di bawah jika data
                            rusak, atau unggah via phpMyAdmin.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <div
                            class="w-5 h-5 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 border border-sky-500/20 text-[10px] mt-0.5">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <span class="leading-relaxed">Lakukan backup rutin (minimal 1 bulan sekali atau pasca pembagian
                            rapor).</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <!-- ================= ZONA RESTORE (BAWAH) ================= -->
    <div
        class="mt-6 md:mt-8 m3-glass-card !border-rose-500/30 rounded-3xl p-5 md:p-7 relative overflow-hidden shadow-2xs">

        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 md:gap-8">

            <!-- Teks Restore -->
            <div class="flex-1 text-center md:text-left">
                <div class="inline-flex items-center gap-2.5 mb-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm border border-rose-500/20">
                        <i class="bi bi-exclamation-triangle-fill animate-pulse"></i>
                    </div>
                    <h3 class="text-base md:text-lg font-black text-rose-600 dark:text-rose-400 tracking-tight">
                        Zona Pemulihan Data (Restore)
                    </h3>
                </div>
                <p
                    class="text-xs font-medium text-zinc-600 dark:text-zinc-400 leading-relaxed max-w-lg mx-auto md:mx-0">
                    Mengunggah file <strong class="text-rose-600 dark:text-rose-400 font-bold">.sql</strong> akan
                    <strong class="uppercase text-rose-600 dark:text-rose-400 font-black">menghapus secara
                        permanen</strong> seluruh data saat ini dan menggantinya dengan data cadangan.
                </p>
            </div>

            <!-- Form Restore -->
            <div
                class="w-full md:w-[380px] m3-glass-card p-4 border border-rose-500/30 rounded-2xl relative shadow-2xs">
                @can('update backup')
                    <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data"
                        id="form-restore">
                        @csrf

                        <label
                            class="block text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-wider mb-1.5 ml-1">
                            Pilih File Cadangan (.sql)
                        </label>

                        <div class="relative mb-3">
                            <input type="file" name="file_sql" id="file_sql" accept=".sql" required
                                class="w-full text-xs text-zinc-500 dark:text-zinc-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-rose-500/10 file:text-rose-600 dark:file:text-rose-400 hover:file:bg-rose-500/20 transition-all cursor-pointer outline-none">
                        </div>

                        <button type="button" onclick="konfirmasiRestore()"
                            class="w-full h-10 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-2xs hover:shadow-md transition-all active:scale-95 outline-none flex items-center justify-center gap-1.5">
                            <i class="bi bi-database-up text-sm"></i> <span>Pulihkan Data</span>
                        </button>
                    </form>
                @endcan
            </div>

        </div>
    </div>

    <script>
        const isDark = document.documentElement.classList.contains('dark');
        const swalBg = isDark ? '#0c0c0e' : '#ffffff';
        const swalColor = isDark ? '#f4f4f5' : '#09090b';

        function konfirmasiBackup() {
            Swal.fire({
                title: '<span class="text-xl font-black tracking-tight">Mulai Backup Sistem?</span>',
                html: '<p class="text-xs font-medium text-zinc-400 mt-2">Apakah Anda yakin ingin mengunduh cadangan database? <br><br>Proses ini mungkin memakan waktu beberapa saat.</p>',
                icon: 'question',
                showCancelButton: true,
                heightAuto: false,
                buttonsStyling: false,
                background: swalBg,
                color: swalColor,
                customClass: {
                    popup: "rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6",
                    actions: "gap-3 mt-6 flex flex-wrap justify-center",
                    confirmButton: "min-h-[40px] rounded-xl px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-2xs active:scale-95 transition-all outline-none",
                    cancelButton: "min-h-[40px] rounded-xl px-5 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs border border-zinc-200 dark:border-zinc-700 active:scale-95 transition-all outline-none"
                },
                confirmButtonText: '<i class="bi bi-cloud-arrow-down-fill mr-1.5"></i> Ya, Mulai Backup!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '<span class="text-xl font-black tracking-tight">Sedang Memproses...</span>',
                        html: '<p class="text-xs font-medium text-zinc-400 mt-2">Menyiapkan file cadangan. <br><b class="text-rose-500">Mohon jangan tutup halaman ini.</b></p>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        background: swalBg,
                        color: swalColor,
                        customClass: {
                            popup: "rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6",
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    document.getElementById('form-backup').submit();

                    setTimeout(() => {
                        Swal.close();
                    }, 3500);
                }
            });
        }

        function konfirmasiRestore() {
            const fileInput = document.getElementById('file_sql');

            if (fileInput.files.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: '<span class="text-xl font-black tracking-tight">File Belum Dipilih</span>',
                    html: '<p class="text-xs font-medium text-zinc-400 mt-2">Silakan pilih file database (.sql) terlebih dahulu!</p>',
                    background: swalBg,
                    color: swalColor,
                    buttonsStyling: false,
                    heightAuto: false,
                    customClass: {
                        popup: "rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-2xl p-6",
                        confirmButton: "min-h-[40px] rounded-xl px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-2xs active:scale-95 transition-all outline-none mt-4"
                    }
                });
                return;
            }

            Swal.fire({
                title: '<span class="text-xl font-black text-rose-600 dark:text-rose-400 tracking-tight">PERINGATAN KRITIS!</span>',
                html: '<p class="text-xs font-medium text-zinc-400 mt-2">Tindakan ini akan <b>MENIMPA</b> seluruh data saat ini dengan data cadangan.<br><br>Proses ini <strong class="text-rose-500 font-bold">TIDAK DAPAT DIBATALKAN</strong>. Yakin?</p>',
                icon: 'warning',
                showCancelButton: true,
                heightAuto: false,
                buttonsStyling: false,
                background: swalBg,
                color: swalColor,
                customClass: {
                    popup: "rounded-3xl border border-rose-500/30 shadow-2xl p-6",
                    actions: "gap-3 mt-6 flex flex-wrap justify-center",
                    confirmButton: "min-h-[40px] rounded-xl px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-2xs active:scale-95 transition-all outline-none",
                    cancelButton: "min-h-[40px] rounded-xl px-5 py-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs border border-zinc-200 dark:border-zinc-700 active:scale-95 transition-all outline-none"
                },
                confirmButtonText: '<i class="bi bi-exclamation-triangle-fill mr-1.5"></i> Ya, Timpa Data!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '<span class="text-xl font-black tracking-tight">Memulihkan Database...</span>',
                        html: '<p class="text-xs font-medium text-zinc-400 mt-2">Membaca dan mengeksekusi file SQL. <br><b class="text-rose-500">JANGAN TUTUP HALAMAN INI!</b></p>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        background: swalBg,
                        color: swalColor,
                        customClass: {
                            popup: "rounded-3xl border border-rose-500/30 shadow-2xl p-6",
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    document.getElementById('form-restore').submit();
                }
            });
        }
    </script>
</x-app-layout>
