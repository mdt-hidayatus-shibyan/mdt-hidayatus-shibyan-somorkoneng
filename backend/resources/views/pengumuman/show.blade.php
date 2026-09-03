@section('title', $pengumuman->judul)
<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-20">
        <div class="flex items-center gap-3">
            <a href="{{ route('pengumuman.index') }}"
                class="w-10 h-10 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 rounded-xl flex items-center justify-center transition-all shadow-2xs active:scale-95 shrink-0 outline-none"
                title="Kembali">
                <i class="bi bi-arrow-left text-base font-bold"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    Detail Pengumuman
                </h2>
                <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Informasi resmi dan pemberitahuan madrasah.
                </p>
            </div>
        </div>

        <!-- Tombol Aksi (Edit & Hapus) -->
        <div class="flex gap-2">
            @can('update pengumuman')
                <a href="{{ route('pengumuman.edit', $pengumuman->id) }}"
                    class="h-10 px-4 rounded-xl font-black text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-2xs transition-all active:scale-95 flex items-center justify-center outline-none">
                    <i class="bi bi-pencil-fill mr-1.5"></i> Edit
                </a>
            @endcan

            @can('delete pengumuman')
                <button type="button" onclick="konfirmasiHapus()"
                    class="h-10 px-4 rounded-xl font-black text-xs bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 shadow-2xs transition-all active:scale-95 flex items-center justify-center outline-none">
                    <i class="bi bi-trash3-fill mr-1.5"></i> Hapus
                </button>
            @endcan
        </div>
    </div>

    <!-- Grid Utama -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-6 relative z-10">

        <!-- ================= KOLOM KIRI (KONTEN UTAMA) ================= -->
        <div class="lg:col-span-8 flex flex-col gap-5">
            <div class="m3-glass-card p-6 md:p-8 flex-1 shadow-2xs">

                @php
                    $colorClass = match ($pengumuman->tipe) {
                        'Penting'
                            => 'text-rose-600 dark:text-rose-400 bg-rose-500/10 border-rose-500/20',
                        'Kegiatan'
                            => 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                        'Libur'
                            => 'text-amber-600 dark:text-amber-400 bg-amber-500/10 border-amber-500/20',
                        default
                            => 'text-blue-600 dark:text-blue-400 bg-blue-500/10 border-blue-500/20',
                    };
                    $iconClass = match ($pengumuman->tipe) {
                        'Penting' => 'bi-exclamation-triangle-fill',
                        'Kegiatan' => 'bi-calendar-event-fill',
                        'Libur' => 'bi-calendar2-x-fill',
                        default => 'bi-info-circle-fill',
                    };
                @endphp

                <!-- Kategori & Waktu Dibuat -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-2xs {{ $colorClass }}">
                        <i class="bi {{ $iconClass }} text-sm"></i> {{ $pengumuman->tipe }}
                    </span>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 flex items-center">
                        <i class="bi bi-clock mr-1.5"></i> Dipublikasikan
                        {{ $pengumuman->created_at->translatedFormat('d F Y, H:i') }}
                    </span>
                    @if ($pengumuman->lampiran_pdf)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 shadow-2xs">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Ada Lampiran PDF
                        </span>
                    @endif
                </div>

                <!-- Judul Pengumuman -->
                <h1
                    class="text-xl md:text-2xl lg:text-3xl font-black text-zinc-900 dark:text-white tracking-tight leading-snug mb-5">
                    {{ $pengumuman->judul }}
                </h1>

                <!-- Garis Pembatas -->
                <hr class="border-zinc-200/80 dark:border-zinc-800 mb-6">

                <!-- Isi Konten (Rich Text HTML) -->
                <div class="rich-content text-sm md:text-[15px] leading-relaxed text-zinc-700 dark:text-zinc-200 font-normal">
                    {!! $pengumuman->konten !!}
                </div>

                <!-- ================= CARD LAMPIRAN PDF ================= -->
                @if ($pengumuman->lampiran_pdf)
                    <div class="mt-8 pt-6 border-t border-zinc-200/80 dark:border-zinc-800">
                        <h4 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="bi bi-paperclip text-rose-500 text-base"></i> Lampiran Dokumen Resmi
                        </h4>

                        <div class="p-4 sm:p-5 rounded-2xl bg-rose-500/5 dark:bg-rose-500/10 border border-rose-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-md">
                                    <i class="bi bi-file-earmark-pdf-fill text-2xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h5 class="text-sm font-black text-zinc-900 dark:text-white truncate">
                                        {{ $pengumuman->nama_file_pdf }}
                                    </h5>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                        Format Dokumen: Portable Document Format (.PDF)
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ $pengumuman->lampiran_pdf_url }}" target="_blank"
                                    class="h-9 px-4 rounded-xl text-xs font-black bg-rose-600 hover:bg-rose-700 text-white flex items-center gap-1.5 shadow-2xs transition-all active:scale-95">
                                    <i class="bi bi-eye-fill"></i> Buka PDF
                                </a>
                                <a href="{{ $pengumuman->lampiran_pdf_url }}" download="{{ $pengumuman->nama_file_pdf }}"
                                    class="h-9 px-3.5 rounded-xl text-xs font-bold bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:text-rose-600 dark:hover:text-rose-400 border border-zinc-200 dark:border-zinc-700 flex items-center gap-1.5 shadow-2xs transition-all active:scale-95"
                                    title="Download File">
                                    <i class="bi bi-download"></i> Unduh
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- ================= KOLOM KANAN (INFO & META) ================= -->
        <div class="lg:col-span-4 flex flex-col gap-4">

            <!-- Panel Informasi -->
            <div class="m3-glass-card p-5 md:p-6 shadow-2xs">
                <h3
                    class="font-black text-zinc-900 dark:text-white text-base tracking-tight mb-4 flex items-center border-b border-zinc-200/80 dark:border-zinc-800 pb-3">
                    <i class="bi bi-info-square-fill text-primary dark:text-primary-dark mr-2"></i> Informasi Publikasi
                </h3>

                <ul class="space-y-4">
                    <!-- Status -->
                    <li class="flex flex-col gap-1">
                        <span
                            class="text-[10px] uppercase font-black text-zinc-400 dark:text-zinc-500 tracking-wider">Status
                            Pengumuman</span>
                        <div>
                            @if ($pengumuman->status == 'Terbit')
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Terbit
                                </span>
                            @elseif($pengumuman->status == 'Draft')
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-700 shadow-2xs">
                                    <i class="bi bi-file-earmark-text"></i> Draft
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20 shadow-2xs">
                                    <i class="bi bi-archive-fill"></i> Diarsipkan
                                </span>
                            @endif
                        </div>
                    </li>

                    <!-- Target Audience -->
                    <li class="flex flex-col gap-1 border-t border-dashed border-zinc-200 dark:border-zinc-800 pt-3">
                        <span
                            class="text-[10px] uppercase font-black text-zinc-400 dark:text-zinc-500 tracking-wider">Target
                            Pembaca</span>
                        <span class="text-xs font-bold text-zinc-900 dark:text-white flex items-center">
                            <i class="bi bi-people-fill text-zinc-400 mr-1.5"></i> {{ $pengumuman->target_audience }}
                        </span>
                    </li>

                    <!-- Periode Berlaku -->
                    <li class="flex flex-col gap-1 border-t border-dashed border-zinc-200 dark:border-zinc-800 pt-3">
                        <span
                            class="text-[10px] uppercase font-black text-zinc-400 dark:text-zinc-500 tracking-wider">Periode
                            Berlaku</span>
                        @if ($pengumuman->tanggal_mulai || $pengumuman->tanggal_selesai)
                            <div class="text-xs font-bold text-zinc-900 dark:text-white">
                                {{ $pengumuman->tanggal_mulai ? $pengumuman->tanggal_mulai->translatedFormat('d M Y') : 'Seterusnya' }}
                                <span class="text-zinc-400 mx-1">-</span>
                                {{ $pengumuman->tanggal_selesai ? $pengumuman->tanggal_selesai->translatedFormat('d M Y') : 'Seterusnya' }}
                            </div>
                        @else
                            <div class="text-xs font-bold text-zinc-900 dark:text-white">
                                Selamanya (Tanpa Batas)
                            </div>
                        @endif
                    </li>

                    <!-- Pembuat / Penulis -->
                    <li class="flex flex-col gap-1 border-t border-dashed border-zinc-200 dark:border-zinc-800 pt-3">
                        <span
                            class="text-[10px] uppercase font-black text-zinc-400 dark:text-zinc-500 tracking-wider">Ditulis
                            Oleh</span>
                        <div class="flex items-center gap-2 mt-1">
                            <div
                                class="w-7 h-7 rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark flex items-center justify-center text-xs font-black border border-primary/20 shadow-2xs">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="text-xs font-bold text-zinc-900 dark:text-white">
                                {{ $pengumuman->user->name ?? 'Administrator' }}
                            </span>
                        </div>
                    </li>

                    <!-- Lampiran PDF Status di Meta -->
                    <li class="flex flex-col gap-1 border-t border-dashed border-zinc-200 dark:border-zinc-800 pt-3">
                        <span class="text-[10px] uppercase font-black text-zinc-400 dark:text-zinc-500 tracking-wider">
                            Lampiran File
                        </span>
                        @if ($pengumuman->lampiran_pdf)
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                                <i class="bi bi-file-earmark-pdf-fill"></i> Ada Dokumen PDF
                            </span>
                        @else
                            <span class="text-xs font-medium text-zinc-400 dark:text-zinc-500">
                                Tidak ada lampiran
                            </span>
                        @endif
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <!-- Styling Khusus Rich Content -->
    <style>
        .rich-content p {
            margin-bottom: 1rem;
            line-height: 1.8;
        }
        .rich-content h1 {
            font-size: 1.6rem;
            font-weight: 900;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }
        .rich-content h2 {
            font-size: 1.35rem;
            font-weight: 800;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        .rich-content h3 {
            font-size: 1.15rem;
            font-weight: 700;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
        .rich-content strong, .rich-content b {
            font-weight: 800;
        }
        .rich-content em, .rich-content i {
            font-style: italic;
        }
        .rich-content u {
            text-decoration: underline;
        }
        .rich-content s {
            text-decoration: line-through;
        }
        .rich-content ol {
            list-style-type: decimal !important;
            padding-left: 1.75rem !important;
            margin-bottom: 1rem;
        }
        .rich-content ul {
            list-style-type: disc !important;
            padding-left: 1.75rem !important;
            margin-bottom: 1rem;
        }
        .rich-content li {
            margin-bottom: 0.35rem;
            line-height: 1.6;
        }
        .rich-content blockquote {
            border-left: 4px solid #38bdf8;
            padding-left: 1rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
            font-style: italic;
            opacity: 0.9;
        }
        .rich-content a {
            color: #2563eb;
            text-decoration: underline;
            font-weight: 600;
        }
        .dark .rich-content a {
            color: #38bdf8;
        }
        .rich-content pre, .rich-content code {
            background-color: rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            padding: 0.2rem 0.4rem;
            font-family: monospace;
            font-size: 0.9em;
        }
        .dark .rich-content pre, .dark .rich-content code {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>

    <!-- FORM HAPUS (RAHASIA) -->
    @can('delete pengumuman')
        <form id="formHapusPengumuman" action="{{ route('pengumuman.destroy', $pengumuman->id) }}" method="POST"
            class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endcan

    <!-- Script SweetAlert Konfirmasi Hapus -->
    @push('script')
        <script>
            function konfirmasiHapus() {
                const isDark = document.documentElement.classList.contains('dark');
                Swal.fire({
                    title: '<span class="text-base font-black tracking-tight">Hapus Pengumuman?</span>',
                    html: '<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Pengumuman ini beserta lampiran PDF akan dihapus secara permanen dari sistem.</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    heightAuto: false,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#71717a',
                    reverseButtons: true,
                    background: isDark ? '#0c0c0e' : '#ffffff',
                    color: isDark ? '#f4f4f5' : '#18181b',
                    customClass: {
                        popup: '!rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-6',
                        confirmButton: "h-10 px-5 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none",
                        cancelButton: "h-10 px-5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-black text-xs rounded-xl shadow-2xs active:scale-95 transition-all outline-none"
                    },
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formHapusPengumuman').submit();
                    }
                });
            }
        </script>
    @endpush

</x-app-layout>
