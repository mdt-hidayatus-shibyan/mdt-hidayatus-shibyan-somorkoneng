@section('title', 'Laporan & Kendala Ustadz')

<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-20">
        <div class="flex items-center gap-3.5">
            <div
                class="w-10 h-10 rounded-2xl bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark flex items-center justify-center border border-primary/20 shrink-0">
                <i class="bi bi-headset text-lg"></i>
            </div>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    Laporan & Kendala Ustadz
                </h2>
                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5 uppercase tracking-wider">
                    Pusat Layanan Pengaduan Masalah Teknis, Kendala Data, & Rekomendasi Fitur Asatidz
                </p>
            </div>
        </div>

        <!-- Tombol Aksi Header -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', getSetting('app_phone', '6281234567890')) }}"
                target="_blank" class="m3-btn-secondary h-10 px-4 text-xs font-black gap-2">
                <i class="bi bi-whatsapp text-emerald-600 text-sm"></i>
                <span>Hubungi WA Admin</span>
            </a>
        </div>
    </div>

    <!-- Statistik Laporan -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 md:gap-4 mb-6 relative z-10">
        <!-- Total Laporan -->
        <div class="m3-glass-card p-4 sm:p-5 flex items-center justify-between shadow-2xs">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400 block">Total Tiket</span>
                <span class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white mt-0.5 block font-mono">
                    {{ $stats['total'] }}
                </span>
            </div>
            <div
                class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center text-lg border border-blue-500/20">
                <i class="bi bi-inbox-fill"></i>
            </div>
        </div>

        <!-- Menunggu -->
        <div
            class="m3-glass-card p-4 sm:p-5 flex items-center justify-between border-amber-500/30 bg-amber-500/5 shadow-2xs">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400 block">
                    Menunggu Respon
                </span>
                <span class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-0.5 block font-mono">
                    {{ $stats['menunggu'] }}
                </span>
            </div>
            <div
                class="w-11 h-11 rounded-2xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg border border-amber-500/30">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <!-- Diproses -->
        <div
            class="m3-glass-card p-4 sm:p-5 flex items-center justify-between border-sky-500/30 bg-sky-500/5 shadow-2xs">
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-sky-600 dark:text-sky-400 block">
                    Sedang Diproses
                </span>
                <span class="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 mt-0.5 block font-mono">
                    {{ $stats['diproses'] }}
                </span>
            </div>
            <div
                class="w-11 h-11 rounded-2xl bg-sky-500/20 text-sky-600 dark:text-sky-400 flex items-center justify-center text-lg border border-sky-500/30">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
        </div>

        <!-- Selesai -->
        <div
            class="m3-glass-card p-4 sm:p-5 flex items-center justify-between border-emerald-500/30 bg-emerald-500/5 shadow-2xs">
            <div>
                <span
                    class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">
                    Selesai / Tuntas
                </span>
                <span
                    class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 block font-mono">
                    {{ $stats['selesai'] }}
                </span>
            </div>
            <div
                class="w-11 h-11 rounded-2xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/30">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian Toolbar -->
    <div class="m3-glass-card p-4 sm:p-5 mb-6 relative z-10 shadow-2xs">
        <form method="GET" action="{{ route('laporan-kendala-admin.index') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-3.5 items-end">
            <!-- Filter Status -->
            <div>
                <label
                    class="block text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Status Tiket
                </label>
                <select name="status" class="m3-input-glass w-full text-xs font-bold" onchange="this.form.submit()">
                    <option value="Semua" {{ $status == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="Menunggu" {{ $status == 'Menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="Diproses" {{ $status == 'Diproses' ? 'selected' : '' }}>⚙️ Diproses</option>
                    <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label
                    class="block text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Kategori Laporan
                </label>
                <select name="kategori" class="m3-input-glass w-full text-xs font-bold" onchange="this.form.submit()">
                    <option value="Semua" {{ $kategori == 'Semua' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="Laporan Kendala" {{ $kategori == 'Laporan Kendala' ? 'selected' : '' }}>Laporan
                        Kendala</option>
                    <option value="Rekomendasi Fitur" {{ $kategori == 'Rekomendasi Fitur' ? 'selected' : '' }}>
                        Rekomendasi Fitur</option>
                    <option value="Konsultasi & Bantuan" {{ $kategori == 'Konsultasi & Bantuan' ? 'selected' : '' }}>
                        Konsultasi & Bantuan</option>
                </select>
            </div>

            <!-- Pencarian -->
            <div class="md:col-span-2">
                <label
                    class="block text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Cari Kata Kunci / Nama Ustadz
                </label>
                <div class="flex gap-2">
                    <div class="relative w-full">
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i class="bi bi-search text-xs"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Ketik kata kunci judul, deskripsi, atau nama ustadz..."
                            class="m3-input-glass w-full !pl-9 text-xs font-bold">
                        @if ($search)
                            <a href="{{ route('laporan-kendala-admin.index', ['status' => $status, 'kategori' => $kategori]) }}"
                                class="absolute inset-y-0 right-0 w-8 h-8 my-auto mr-1 flex items-center justify-center text-zinc-400 hover:text-rose-500 rounded-full transition-colors"
                                title="Reset Pencarian">
                                <i class="bi bi-x-lg text-xs font-bold"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="m3-btn-primary h-10 px-4 text-xs font-black shadow-2xs shrink-0">
                        Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- CONTAINER TABEL LAPORAN -->
    <div class="relative z-10 w-full overflow-x-auto rounded-2xl m3-glass-card shadow-2xs">
        <table class="m3-table w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr
                    class="bg-zinc-50/80 dark:bg-zinc-950/60 border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-black">
                    <th class="px-4 py-3.5 rounded-tl-2xl w-24 text-center">No. Tiket</th>
                    <th class="px-4 py-3.5 min-w-[180px]">Pengirim (Ustadz)</th>
                    <th class="px-4 py-3.5 min-w-[200px]">Kategori & Judul</th>
                    <th class="px-4 py-3.5 min-w-[280px]">Keterangan / Deskripsi</th>
                    <th class="px-4 py-3.5 text-center">Status</th>
                    <th class="px-4 py-3.5 text-right rounded-tr-2xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/80 text-xs">
                @forelse ($laporans as $laporan)
                    @php
                        $katColor = match ($laporan->kategori) {
                            'Laporan Kendala' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                            'Rekomendasi Fitur'
                                => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                            'Konsultasi & Bantuan',
                            'Konsultasi'
                                => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                            default => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                        };

                        $statusBadge = match ($laporan->status) {
                            'Selesai'
                                => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                            'Diproses' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20',
                            'Ditolak' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                            default => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                        };
                    @endphp

                    <tr class="hover:bg-zinc-500/5 transition-colors group">
                        <!-- Kolom No Tiket & Waktu -->
                        <td class="px-4 py-3 text-center align-middle">
                            <span class="font-mono font-black text-xs text-primary dark:text-primary-dark block">
                                #LP-{{ $laporan->id }}
                            </span>
                            <span class="text-[10px] text-zinc-400 block font-mono mt-0.5">
                                {{ $laporan->created_at->format('d/m/Y H:i') }}
                            </span>
                        </td>

                        <!-- Kolom Pengirim -->
                        <td class="px-4 py-3 align-middle">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="w-8 h-8 rounded-full bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                    {{ substr($laporan->ustadz->nama_lengkap ?? ($laporan->user->name ?? 'U'), 0, 1) }}
                                </div>
                                <div>
                                    <strong class="text-zinc-900 dark:text-white block font-bold text-xs">
                                        {{ $laporan->ustadz->nama_lengkap ?? ($laporan->user->name ?? '-') }}
                                    </strong>
                                    <span class="text-[11px] text-zinc-400 font-mono">
                                        NIGM: {{ $laporan->ustadz->nigm ?? ($laporan->user->email ?? '-') }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Kolom Kategori & Judul -->
                        <td class="px-4 py-3 align-middle">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9.5pt] font-black uppercase tracking-wider border shadow-2xs {{ $katColor }} mb-1">
                                {{ $laporan->kategori }}
                            </span>
                            <p class="font-bold text-zinc-900 dark:text-white leading-tight mt-0.5">
                                {{ $laporan->judul }}
                            </p>
                        </td>

                        <!-- Kolom Keterangan / Deskripsi -->
                        <td class="px-4 py-3 align-middle whitespace-normal max-w-xs md:max-w-md">
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                                {{ $laporan->deskripsi }}
                            </p>
                            @if ($laporan->respon_admin)
                                <div
                                    class="mt-1.5 p-2 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-xl border border-emerald-500/20 text-[11px] text-emerald-800 dark:text-emerald-300">
                                    <strong class="font-black">Respon Admin:</strong>
                                    {{ Str::limit($laporan->respon_admin, 80) }}
                                </div>
                            @endif
                        </td>

                        <!-- Kolom Status -->
                        <td class="px-4 py-3 text-center align-middle">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-2xs {{ $statusBadge }}">
                                {{ $laporan->status }}
                            </span>
                        </td>

                        <!-- Kolom Aksi -->
                        <td class="px-4 py-3 text-right align-middle">
                            <button type="button" onclick="bukaModalTanggapan({{ json_encode($laporan) }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary hover:bg-primary hover:text-white dark:bg-primary-dark/20 dark:text-primary-dark dark:hover:bg-primary-dark dark:hover:text-zinc-900 rounded-xl text-xs font-black transition-all active:scale-95 border border-primary/20 shadow-2xs">
                                <i class="bi bi-chat-left-dots-fill text-xs"></i>
                                <span>Tanggapi</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-zinc-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="bi bi-inbox text-3xl mb-2 text-zinc-300 dark:text-zinc-600"></i>
                                <span class="font-bold text-xs">Belum ada laporan atau rekomendasi yang sesuai
                                    filter.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($laporans->hasPages())
        <div class="mt-4">
            {{ $laporans->links() }}
        </div>
    @endif

    <!-- MODAL TANGGAPAN & UPDATE STATUS -->
    <div id="modalTanggapan"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs hidden p-4">
        <div
            class="m3-glass-card rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white/95 dark:bg-zinc-900/95 relative animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3.5 border-b border-zinc-200/80 dark:border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark flex items-center justify-center">
                        <i class="bi bi-chat-square-text-fill text-sm"></i>
                    </div>
                    <h3 class="font-black text-base text-zinc-900 dark:text-white" id="modalTiketLabel">
                        Tanggapi Laporan Tiket
                    </h3>
                </div>
                <button type="button" onclick="tutupModalTanggapan()"
                    class="text-zinc-400 hover:text-zinc-600 dark:hover:text-white p-1 rounded-lg">
                    <i class="bi bi-x-lg font-bold"></i>
                </button>
            </div>

            <form id="formTanggapanSubmit" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <!-- Detail Pengirim Box -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">
                        Informasi Pengirim & Laporan
                    </label>
                    <div class="p-3 bg-zinc-500/5 dark:bg-zinc-800/50 rounded-2xl border border-zinc-200/60 dark:border-zinc-800 text-xs"
                        id="modalDetailBox"></div>
                </div>

                <!-- Select Status -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">
                        Status Laporan / Tiket
                    </label>
                    <select name="status" id="modalSelectStatus" class="m3-input-glass w-full text-xs font-bold"
                        required>
                        <option value="Menunggu">⏳ Menunggu</option>
                        <option value="Diproses">⚙️ Diproses</option>
                        <option value="Selesai">✅ Selesai</option>
                        <option value="Ditolak">❌ Ditolak</option>
                    </select>
                </div>

                <!-- Textarea Respon Admin -->
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-zinc-400 mb-1">
                        Respon / Tindak Lanjut Administrator
                    </label>
                    <textarea name="respon_admin" id="modalResponText" rows="4"
                        placeholder="Tuliskan jawaban, solusi, atau keterangan perbaikan untuk ustadz pengirim..."
                        class="m3-input-glass w-full text-xs font-medium"></textarea>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-2 pt-3 border-t border-zinc-200/80 dark:border-zinc-800">
                    <button type="button" onclick="tutupModalTanggapan()"
                        class="m3-btn-secondary h-10 px-4 text-xs font-black">
                        Batal
                    </button>
                    <button type="submit" class="m3-btn-primary h-10 px-5 text-xs font-black shadow-2xs">
                        <i class="bi bi-check2-circle mr-1"></i> Simpan Tanggapan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('script')
        <script>
            function bukaModalTanggapan(data) {
                document.getElementById('modalTiketLabel').innerText = `Tanggapi Tiket #LP-${data.id}`;
                document.getElementById('modalDetailBox').innerHTML = `
                <div class="font-black text-zinc-900 dark:text-white text-xs">${data.ustadz ? data.ustadz.nama_lengkap : (data.user ? data.user.name : '-')}</div>
                <div class="text-[11px] text-primary dark:text-primary-dark font-bold mt-0.5">[${data.kategori}] ${data.judul}</div>
                <div class="text-xs text-zinc-600 dark:text-zinc-400 mt-2 p-2.5 bg-white/70 dark:bg-zinc-900/70 rounded-xl border border-zinc-200/60 dark:border-zinc-800/60 leading-relaxed">${data.deskripsi}</div>
            `;
                document.getElementById('modalSelectStatus').value = data.status || 'Menunggu';
                document.getElementById('modalResponText').value = data.respon_admin || '';
                document.getElementById('formTanggapanSubmit').action = `{{ url('laporan-kendala-admin') }}/${data.id}/status`;
                document.getElementById('modalTanggapan').classList.remove('hidden');
            }

            function tutupModalTanggapan() {
                document.getElementById('modalTanggapan').classList.add('hidden');
            }
        </script>
    @endpush
</x-app-layout>
