@section('title', isset($pengumuman) ? 'Edit Pengumuman' : 'Buat Pengumuman Baru')

<x-app-layout>

    <!-- Header Page -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-20">
        <div class="flex items-center gap-3">
            <a href="{{ route('pengumuman.index') }}"
                class="w-10 h-10 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 rounded-xl flex items-center justify-center transition-all shadow-2xs active:scale-95 shrink-0 outline-none"
                title="Kembali">
                <i class="bi bi-arrow-left text-base font-bold"></i>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                    {{ isset($pengumuman) ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}
                </h2>
                <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Tuliskan informasi penting lengkap dengan format dokumen dan lampiran PDF.
                </p>
            </div>
        </div>
    </div>

    <!-- MAIN FORM -->
    <form id="formPengumuman"
        action="{{ isset($pengumuman) ? route('pengumuman.update', $pengumuman->id) : route('pengumuman.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="relative z-10">
        @csrf
        @if (isset($pengumuman))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 md:gap-6">

            <!-- ================= KOLOM KIRI (KONTEN UTAMA & EDITOR WORD) ================= -->
            <div class="xl:col-span-8 flex flex-col gap-5 md:gap-6">

                <div class="m3-glass-card p-6 sm:p-7 flex-1 flex flex-col shadow-2xs">
                    <!-- Judul -->
                    <div class="mb-5">
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2 ml-1">
                            Judul Pengumuman <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul" value="{{ old('judul', $pengumuman->judul ?? '') }}"
                            placeholder="Contoh: Pemberitahuan Libur Akhir Semester & Rapat Wali Murid..."
                            class="m3-input-glass w-full text-base sm:text-lg font-black {{ $errors->has('judul') ? '!border-rose-500 !ring-rose-500/20' : '' }}" required>
                        @error('judul')
                            <p class="text-[11px] font-bold text-rose-500 mt-1.5 ml-1">
                                <i class="bi bi-exclamation-triangle-fill mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Editor Konten (Word-like WYSIWYG) -->
                    <div class="flex-1 flex flex-col mb-5">
                        <div class="flex items-center justify-between mb-2 ml-1">
                            <label class="text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">
                                Isi Pengumuman <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500">
                                Format: Bold (Ctrl+B), Italic (Ctrl+I), Underline (Ctrl+U), List, Align, dll.
                            </span>
                        </div>

                        <!-- Container Toolbar & Editor Quill -->
                        <div class="editor-wrapper rounded-2xl overflow-hidden border border-zinc-200/90 dark:border-zinc-700/80 bg-white/60 dark:bg-zinc-900/60 transition-all focus-within:ring-2 focus-within:ring-primary/20 dark:focus-within:ring-primary-dark/20 focus-within:border-primary dark:focus-within:border-primary-dark {{ $errors->has('konten') ? '!border-rose-500 !ring-rose-500/20' : '' }}">
                            <div id="quill-toolbar" class="border-b border-zinc-200/80 dark:border-zinc-700/80 bg-zinc-50/70 dark:bg-zinc-800/70 !border-0 !p-2 flex flex-wrap items-center gap-1">
                                <span class="ql-formats">
                                    <select class="ql-header">
                                        <option value="1">Heading 1</option>
                                        <option value="2">Heading 2</option>
                                        <option value="3">Heading 3</option>
                                        <option selected>Normal</option>
                                    </select>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-bold" title="Tebal (Ctrl+B)"></button>
                                    <button class="ql-italic" title="Miring (Ctrl+I)"></button>
                                    <button class="ql-underline" title="Garis Bawah (Ctrl+U)"></button>
                                    <button class="ql-strike" title="Coret"></button>
                                </span>
                                <span class="ql-formats">
                                    <select class="ql-color" title="Warna Teks"></select>
                                    <select class="ql-background" title="Warna Stabilo/Latar"></select>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-list" value="ordered" title="Penomoran (1, 2, 3)"></button>
                                    <button class="ql-list" value="bullet" title="Poin (Bullets)"></button>
                                </span>
                                <span class="ql-formats">
                                    <select class="ql-align" title="Perataan Paragraf"></select>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-script" value="sub" title="Subscript"></button>
                                    <button class="ql-script" value="super" title="Superscript"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-blockquote" title="Kutipan (Blockquote)"></button>
                                    <button class="ql-code-block" title="Kode"></button>
                                    <button class="ql-link" title="Sisipkan Link Web"></button>
                                    <button class="ql-clean" title="Hapus Format"></button>
                                </span>
                            </div>

                            <div id="quill-editor" class="min-h-[280px] p-4 text-sm md:text-[15px] font-normal leading-relaxed text-zinc-800 dark:text-zinc-200">
                                {!! old('konten', $pengumuman->konten ?? '') !!}
                            </div>
                        </div>

                        <!-- Hidden input untuk submit konten -->
                        <textarea name="konten" id="konten" class="hidden">{{ old('konten', $pengumuman->konten ?? '') }}</textarea>

                        @error('konten')
                            <p class="text-[11px] font-bold text-rose-500 mt-1.5 ml-1">
                                <i class="bi bi-exclamation-triangle-fill mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Lampiran Dokumen PDF -->
                    <div class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800">
                        <label class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2 ml-1">
                            <i class="bi bi-file-earmark-pdf-fill text-rose-500 mr-1 text-sm"></i> Lampiran Dokumen PDF (Opsional)
                        </label>

                        @if (isset($pengumuman) && $pengumuman->lampiran_pdf)
                            <!-- File PDF Saat Ini -->
                            <div id="current-pdf-box" class="mb-3 p-3.5 rounded-2xl bg-rose-500/5 dark:bg-rose-500/10 border border-rose-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <i class="bi bi-file-earmark-pdf text-lg"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-xs font-bold text-zinc-900 dark:text-white truncate">
                                            {{ $pengumuman->nama_file_pdf }}
                                        </p>
                                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400">
                                            Dokumen PDF terlampir
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ $pengumuman->lampiran_pdf_url }}" target="_blank"
                                        class="h-8 px-3 rounded-lg text-xs font-bold bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:text-rose-600 dark:hover:text-rose-400 border border-zinc-200 dark:border-zinc-700 flex items-center gap-1.5 shadow-2xs transition-all">
                                        <i class="bi bi-box-arrow-up-right text-[11px]"></i> Lihat PDF
                                    </a>
                                    <label class="cursor-pointer inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-bold bg-rose-100 hover:bg-rose-200 dark:bg-rose-900/40 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-700/50 transition-all">
                                        <input type="checkbox" name="hapus_lampiran" value="1" id="hapus_lampiran" class="rounded border-rose-400 text-rose-600 focus:ring-rose-500">
                                        <span>Hapus PDF</span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <!-- Input Upload PDF Baru -->
                        <div class="relative">
                            <input type="file" name="lampiran_pdf" id="lampiran_pdf" accept="application/pdf"
                                class="block w-full text-xs text-zinc-500 dark:text-zinc-400
                                file:mr-3 file:py-2.5 file:px-4
                                file:rounded-xl file:border-0
                                file:text-xs file:font-black
                                file:bg-primary/10 file:text-primary dark:file:text-primary-dark
                                hover:file:bg-primary/20
                                cursor-pointer rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 p-2 bg-zinc-50/50 dark:bg-black/20">
                        </div>
                        <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1.5 ml-1">
                            Format yang didukung: <strong>.PDF</strong> (Maksimal 10 MB). Lampiran ini dapat diunduh langsung oleh Ustadz dan Wali Murid.
                        </p>
                        @error('lampiran_pdf')
                            <p class="text-[11px] font-bold text-rose-500 mt-1.5 ml-1">
                                <i class="bi bi-exclamation-triangle-fill mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

            </div>

            <!-- ================= KOLOM KANAN (PENGATURAN) ================= -->
            <div class="xl:col-span-4 flex flex-col gap-4">

                <div class="m3-glass-card p-5 sm:p-6 shadow-2xs">
                    <h3
                        class="font-black text-zinc-900 dark:text-white text-base tracking-tight mb-5 flex items-center border-b border-zinc-200/80 dark:border-zinc-800 pb-3">
                        <i class="bi bi-sliders text-primary dark:text-primary-dark mr-2"></i> Pengaturan Publikasi
                    </h3>

                    <!-- Tipe Pengumuman (Custom Radio) -->
                    <div class="mb-5">
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2 ml-1">Kategori
                            Tipe</label>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Informasi -->
                            <label class="cursor-pointer group">
                                <input type="radio" name="tipe" value="Informasi" class="peer sr-only"
                                    {{ old('tipe', $pengumuman->tipe ?? 'Informasi') == 'Informasi' ? 'checked' : '' }}>
                                <div
                                    class="px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-center text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-blue-500/10 peer-checked:border-blue-500 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all shadow-2xs">
                                    Informasi
                                </div>
                            </label>
                            <!-- Penting -->
                            <label class="cursor-pointer group">
                                <input type="radio" name="tipe" value="Penting" class="peer sr-only"
                                    {{ old('tipe', $pengumuman->tipe ?? '') == 'Penting' ? 'checked' : '' }}>
                                <div
                                    class="px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-center text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-rose-500/10 peer-checked:border-rose-500 peer-checked:text-rose-600 dark:peer-checked:text-rose-400 transition-all shadow-2xs">
                                    Penting
                                </div>
                            </label>
                            <!-- Kegiatan -->
                            <label class="cursor-pointer group">
                                <input type="radio" name="tipe" value="Kegiatan" class="peer sr-only"
                                    {{ old('tipe', $pengumuman->tipe ?? '') == 'Kegiatan' ? 'checked' : '' }}>
                                <div
                                    class="px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-center text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-emerald-500/10 peer-checked:border-emerald-500 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 transition-all shadow-2xs">
                                    Kegiatan
                                </div>
                            </label>
                            <!-- Libur -->
                            <label class="cursor-pointer group">
                                <input type="radio" name="tipe" value="Libur" class="peer sr-only"
                                    {{ old('tipe', $pengumuman->tipe ?? '') == 'Libur' ? 'checked' : '' }}>
                                <div
                                    class="px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-center text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-amber-500/10 peer-checked:border-amber-500 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 transition-all shadow-2xs">
                                    Libur
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Target Audience (Custom Radio) -->
                    <div class="mb-5">
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2 ml-1">Target
                            Pembaca</label>
                        <div class="flex flex-col gap-2">
                            <!-- Semua -->
                            <label class="cursor-pointer">
                                <input type="radio" name="target_audience" value="Semua" class="peer sr-only"
                                    {{ old('target_audience', $pengumuman->target_audience ?? 'Semua') == 'Semua' ? 'checked' : '' }}>
                                <div
                                    class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-primary/10 peer-checked:border-primary dark:peer-checked:border-primary-dark peer-checked:text-primary dark:peer-checked:text-primary-dark transition-all shadow-2xs">
                                    <div class="flex items-center gap-2"><i class="bi bi-people-fill text-xs"></i>
                                        Semua Pengguna</div>
                                    <i
                                        class="bi bi-check-circle-fill opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </label>
                            <!-- Wali Murid -->
                            <label class="cursor-pointer">
                                <input type="radio" name="target_audience" value="Wali Murid" class="peer sr-only"
                                    {{ old('target_audience', $pengumuman->target_audience ?? '') == 'Wali Murid' ? 'checked' : '' }}>
                                <div
                                    class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-primary/10 peer-checked:border-primary dark:peer-checked:border-primary-dark peer-checked:text-primary dark:peer-checked:text-primary-dark transition-all shadow-2xs">
                                    <div class="flex items-center gap-2"><i class="bi bi-house-door-fill text-xs"></i>
                                        Wali Murid</div>
                                    <i
                                        class="bi bi-check-circle-fill opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </label>
                            <!-- Asatidz -->
                            <label class="cursor-pointer">
                                <input type="radio" name="target_audience" value="Ustadz" class="peer sr-only"
                                    {{ old('target_audience', $pengumuman->target_audience ?? '') == 'Ustadz' ? 'checked' : '' }}>
                                <div
                                    class="flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-primary/10 peer-checked:border-primary dark:peer-checked:border-primary-dark peer-checked:text-primary dark:peer-checked:text-primary-dark transition-all shadow-2xs">
                                    <div class="flex items-center gap-2"><i
                                            class="bi bi-person-badge-fill text-xs"></i> Ustadz (Guru)</div>
                                    <i
                                        class="bi bi-check-circle-fill opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Periode Tampil (Grid 2 Kolom) -->
                    <div class="grid grid-cols-2 gap-2.5 mb-5">
                        <div>
                            <label
                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-1">Tgl
                                Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', isset($pengumuman) && $pengumuman->tanggal_mulai ? $pengumuman->tanggal_mulai->format('Y-m-d') : date('Y-m-d')) }}"
                                class="m3-input-glass w-full text-xs font-bold !py-2 !px-2.5">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1 ml-1">Tgl
                                Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                value="{{ old('tanggal_selesai', isset($pengumuman) && $pengumuman->tanggal_selesai ? $pengumuman->tanggal_selesai->format('Y-m-d') : '') }}"
                                placeholder="Seterusnya"
                                class="m3-input-glass w-full text-xs font-bold !py-2 !px-2.5 {{ $errors->has('tanggal_selesai') ? '!border-rose-500' : '' }}">
                        </div>
                        @error('tanggal_selesai')
                            <p class="col-span-2 text-[10px] font-bold text-rose-500"><i
                                    class="bi bi-exclamation-triangle-fill mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Visibilitas (Custom Radio) -->
                    <div class="mb-6">
                        <label
                            class="block text-[11px] font-black text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-2 ml-1">Status
                            Visibilitas</label>
                        <div class="grid grid-cols-3 gap-2">
                            <!-- Terbit -->
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="Terbit" class="peer sr-only"
                                    {{ old('status', $pengumuman->status ?? 'Terbit') == 'Terbit' ? 'checked' : '' }}>
                                <div
                                    class="py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-center text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-emerald-500/10 peer-checked:border-emerald-500 peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 transition-all shadow-2xs">
                                    Terbit
                                </div>
                            </label>
                            <!-- Draft -->
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="Draft" class="peer sr-only"
                                    {{ old('status', $pengumuman->status ?? '') == 'Draft' ? 'checked' : '' }}>
                                <div
                                    class="py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-center text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-zinc-500/10 peer-checked:border-zinc-500 peer-checked:text-zinc-800 dark:peer-checked:text-white transition-all shadow-2xs">
                                    Draft
                                </div>
                            </label>
                            <!-- Arsip -->
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="Arsip" class="peer sr-only"
                                    {{ old('status', $pengumuman->status ?? '') == 'Arsip' ? 'checked' : '' }}>
                                <div
                                    class="py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white/40 dark:bg-black/40 text-center text-xs font-black text-zinc-500 dark:text-zinc-400 peer-checked:bg-amber-500/10 peer-checked:border-amber-500 peer-checked:text-amber-600 dark:peer-checked:text-amber-400 transition-all shadow-2xs">
                                    Arsip
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" class="m3-btn-primary w-full h-11 text-xs font-black shadow-2xs">
                        <i class="bi bi-send-fill mr-1.5"></i>
                        {{ isset($pengumuman) ? 'Simpan Perubahan' : 'Publikasikan Sekarang' }}
                    </button>

                </div>

                @if (isset($pengumuman))
                    <!-- Delete Button (Hanya tampil saat Edit) -->
                    <div class="text-center pt-2">
                        <button type="button" onclick="konfirmasiHapus()"
                            class="text-xs font-black text-rose-500 hover:text-rose-600 transition-colors uppercase tracking-wider outline-none">
                            <i class="bi bi-trash3-fill mr-1"></i> Hapus Pengumuman
                        </button>
                    </div>
                @endif

            </div>

        </div>
    </form>

    <!-- FORM HAPUS (RAHASIA) -->
    @if (isset($pengumuman))
        <form id="formHapusPengumuman" action="{{ route('pengumuman.destroy', $pengumuman->id) }}" method="POST"
            class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

    <!-- CSS & JS Quill.js CDN -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border: none !important;
            border-bottom: 1px solid rgba(228, 228, 231, 0.8) !important;
            font-family: inherit;
        }
        .dark .ql-toolbar.ql-snow {
            border-bottom: 1px solid rgba(63, 63, 70, 0.8) !important;
        }
        .ql-container.ql-snow {
            border: none !important;
            font-family: inherit;
            font-size: 0.95rem;
        }
        .dark .ql-toolbar .ql-stroke {
            stroke: #a1a1aa !important;
        }
        .dark .ql-toolbar .ql-fill {
            fill: #a1a1aa !important;
        }
        .dark .ql-toolbar .ql-picker {
            color: #d4d4d8 !important;
        }
        .dark .ql-toolbar .ql-picker-options {
            background-color: #18181b !important;
            border-color: #27272a !important;
        }
        .dark .ql-toolbar .ql-picker-item:hover {
            color: #38bdf8 !important;
        }
        .dark .ql-toolbar button:hover .ql-stroke {
            stroke: #38bdf8 !important;
        }
        .dark .ql-toolbar button.ql-active .ql-stroke {
            stroke: #38bdf8 !important;
        }
        .dark .ql-editor.ql-blank::before {
            color: #71717a !important;
        }
        .ql-editor {
            min-height: 280px;
            line-height: 1.75;
        }
        .ql-editor ol, .ql-editor ul {
            padding-left: 1.5rem !important;
        }
        .ql-editor ol li {
            list-style-type: decimal !important;
        }
        .ql-editor ul li {
            list-style-type: disc !important;
        }
    </style>

    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Quill Editor dengan full Office Word Toolbar
            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: '#quill-toolbar'
                },
                placeholder: 'Ketik isi pengumuman secara detail di sini... Anda dapat membuat tulisan tebal, miring, garis bawah, penomoran urut, poin, perataan, dan warna teks.'
            });

            const form = document.getElementById('formPengumuman');
            const hiddenInput = document.getElementById('konten');

            // Sinkronkan HTML Quill ke textarea setiap kali mengetik
            quill.on('text-change', function () {
                hiddenInput.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
            });

            // Sinkronkan saat form disubmit
            form.addEventListener('submit', function () {
                hiddenInput.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
            });
        });

        @if (isset($pengumuman))
            function konfirmasiHapus() {
                const isDark = document.documentElement.classList.contains('dark');
                Swal.fire({
                    title: '<span class="text-base font-black tracking-tight">Hapus Pengumuman?</span>',
                    html: '<p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-1">Tindakan ini tidak dapat dibatalkan. Pengumuman dan lampiran PDF akan dihapus permanen.</p>',
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
        @endif
    </script>

</x-app-layout>
