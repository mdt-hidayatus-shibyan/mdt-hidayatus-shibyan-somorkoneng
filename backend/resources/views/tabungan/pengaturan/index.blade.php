@section('title', 'Master Periode & Pengaturan Tabungan Madrasah')
<x-app-layout>

    <!-- Alert Success / Error (Fallback for non-AJAX) -->
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

    <!-- Data Grid Container (Target AJAX Auto-Refresh) -->
    <div id="data-grid-container" class="space-y-6 relative z-10">

        <div class="m3-glass-card rounded-2xl p-6 shadow-2xs">

            <!-- Card Header Section -->
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-5 border-b border-zinc-200/80 dark:border-zinc-800">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-lg shrink-0">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                            Periode Tabungan Berjangka
                        </h3>
                        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                            Kelola jadwal siklus periode (mulai, tutup, pembagian) dan konfigurasi potongan penarikan
                        </p>
                    </div>
                </div>

                <!-- Tombol Buka Periode Baru AJAX Modal -->
                <a href="{{ route('tabungan.pengaturan.periode.create') }}"
                    class="action-modal m3-btn-primary px-4 py-2 rounded-xl text-xs font-black flex items-center gap-2 shrink-0">
                    <i class="bi bi-plus-lg text-sm"></i>
                    <span>Buka Periode Tabungan Baru</span>
                </a>
            </div>

            <!-- Daftar Periode Tabungan -->
            <div class="grid grid-cols-1 gap-4">
                @forelse ($periodes as $per)
                    <div
                        class="p-5 rounded-2xl border {{ $per->is_active ? 'bg-primary/5 border-primary/30 dark:border-primary-dark/30 shadow-xs' : 'bg-zinc-50/70 dark:bg-zinc-900/50 border-zinc-200/80 dark:border-zinc-800' }} flex flex-col justify-between gap-4 transition-all duration-300">

                        <!-- Baris Atas: Info Periode & Aksi Utama -->
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">

                            <!-- Info Periode -->
                            <div class="flex-1 space-y-2">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <h4 class="font-black text-zinc-900 dark:text-white text-base tracking-tight">
                                        {{ $per->nama_periode }}
                                    </h4>

                                    @if ($per->is_active)
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            ★ Periode Aktif Utama
                                        </span>
                                    @endif

                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $per->status == 'Aktif' ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20' : ($per->status == 'Ditutup' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20') }}">
                                        {{ $per->status }}
                                    </span>
                                </div>

                                <!-- Detail Tanggal & Tahun Pelajaran -->
                                <div
                                    class="text-xs text-zinc-500 dark:text-zinc-400 flex flex-wrap items-center gap-x-3 gap-y-1.5 pt-0.5">
                                    <span class="flex items-center gap-1.5">
                                        <i class="bi bi-mortarboard text-zinc-400"></i>
                                        Tahun: <strong
                                            class="text-zinc-700 dark:text-zinc-200">{{ $per->tahunPelajaran ? $per->tahunPelajaran->nama_hijriyah . ' H | ' . $per->tahunPelajaran->nama_masehi . ' M' : '-' }}</strong>
                                    </span>
                                    <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="bi bi-calendar-play text-emerald-600"></i>
                                        Mulai: <strong
                                            class="text-zinc-700 dark:text-zinc-200">{{ \Carbon\Carbon::parse($per->tanggal_mulai)->format('d/m/Y') }}</strong>
                                    </span>
                                    <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="bi bi-calendar-x text-amber-600"></i>
                                        Tutup: <strong
                                            class="text-zinc-700 dark:text-zinc-200">{{ \Carbon\Carbon::parse($per->tanggal_penutupan)->format('d/m/Y') }}</strong>
                                    </span>
                                    <span class="text-zinc-300 dark:text-zinc-700">&bull;</span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="bi bi-gift text-purple-600"></i>
                                        Bagi: <strong
                                            class="text-zinc-700 dark:text-zinc-200">{{ \Carbon\Carbon::parse($per->tanggal_pembagian)->format('d/m/Y') }}</strong>
                                    </span>
                                </div>

                                @if ($per->catatan)
                                    <div
                                        class="text-[11px] text-zinc-400 dark:text-zinc-500 italic flex items-center gap-1.5">
                                        <i class="bi bi-chat-left-text"></i>
                                        <span>{{ $per->catatan }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Tombol Aksi Periode -->
                            <div
                                class="flex items-center justify-between lg:justify-end gap-3 shrink-0 pt-3 lg:pt-0 border-t lg:border-none border-zinc-100 dark:border-zinc-800">

                                <!-- Status Toggle AJAX -->
                                <x-toggle-status :is-active="$per->is_active" :url="route('tabungan.pengaturan.periode.toggle-status', $per->id)" />

                                <!-- Divider Garis -->
                                <div class="w-px h-6 bg-zinc-200 dark:bg-zinc-800"></div>

                                <div class="flex items-center gap-2">
                                    <!-- Tombol Edit Modal AJAX -->
                                    <a href="{{ route('tabungan.pengaturan.periode.edit', $per->id) }}"
                                        class="action-modal min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 border border-blue-200/60 dark:border-blue-800/40 transition-all hover:scale-105 active:scale-95 outline-none"
                                        title="Edit Jadwal Periode">
                                        <i class="bi bi-pencil-fill text-xs"></i>
                                    </a>

                                    <!-- Tombol Hapus AJAX SweetAlert -->
                                    <form action="{{ route('tabungan.pengaturan.periode.destroy', $per->id) }}"
                                        method="POST" class="delete-ajax inline m-0 p-0"
                                        data-refresh-target="#data-grid-container">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="min-w-[36px] min-h-[36px] w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-900/50 border border-red-200/60 dark:border-red-800/40 transition-all hover:scale-105 active:scale-95 outline-none cursor-pointer"
                                            title="Hapus Periode">
                                            <i class="bi bi-trash-fill text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Baris Bawah: Rincian Potongan & Tombol Edit Potongan Terpisah -->
                        <div
                            class="pt-3 border-t border-zinc-200/70 dark:border-zinc-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">

                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    class="text-[11px] font-black text-zinc-400 uppercase tracking-wider flex items-center gap-1">
                                    <i class="bi bi-percent text-rose-500"></i> Potongan:
                                </span>
                                <span
                                    class="px-2.5 py-1 rounded-lg bg-zinc-200/60 dark:bg-zinc-800/90 font-bold text-zinc-700 dark:text-zinc-300 text-[11px]">
                                    Murid: <strong
                                        class="text-rose-600 dark:text-rose-400">{{ \App\Models\Tabungan\PengaturanPotonganTabungan::getPersentase('Murid', $per->id) }}%</strong>
                                </span>
                                <span
                                    class="px-2.5 py-1 rounded-lg bg-zinc-200/60 dark:bg-zinc-800/90 font-bold text-zinc-700 dark:text-zinc-300 text-[11px]">
                                    Ustadz: <strong
                                        class="text-blue-600 dark:text-blue-400">{{ \App\Models\Tabungan\PengaturanPotonganTabungan::getPersentase('Ustadz', $per->id) }}%</strong>
                                </span>
                                <span
                                    class="px-2.5 py-1 rounded-lg bg-zinc-200/60 dark:bg-zinc-800/90 font-bold text-zinc-700 dark:text-zinc-300 text-[11px]">
                                    Kas Ruangan: <strong
                                        class="text-emerald-600 dark:text-emerald-400">{{ \App\Models\Tabungan\PengaturanPotonganTabungan::getPersentase('Kas Ruangan', $per->id) }}%</strong>
                                </span>
                                <span
                                    class="px-2.5 py-1 rounded-lg bg-zinc-200/60 dark:bg-zinc-800/90 font-bold text-zinc-700 dark:text-zinc-300 text-[11px]">
                                    Umum: <strong
                                        class="text-purple-600 dark:text-purple-400">{{ \App\Models\Tabungan\PengaturanPotonganTabungan::getPersentase('Umum', $per->id) }}%</strong>
                                </span>
                            </div>

                            <!-- Tombol Kelola/Edit Potongan Khusus Modal Terpisah -->
                            <a href="{{ route('tabungan.pengaturan.periode.potongan.edit', $per->id) }}"
                                class="action-modal px-3.5 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-200/60 dark:border-rose-800/40 font-black text-[11px] flex items-center justify-center gap-1.5 transition-all outline-none shrink-0"
                                title="Edit Persentase Potongan Periode Ini">
                                <i class="bi bi-sliders text-xs"></i>
                                <span>Ubah Potongan Periode</span>
                            </a>

                        </div>

                    </div>
                @empty
                    <x-empty-state icon="bi-calendar-x" title="Belum Ada Periode Tabungan"
                        message="Silakan buat periode tabungan berjangka baru dengan tombol di atas." />
                @endforelse
            </div>

        </div>

    </div>

</x-app-layout>
