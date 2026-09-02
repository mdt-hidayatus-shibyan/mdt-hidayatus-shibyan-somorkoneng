<x-guest-layout>
    @php
        // Normalisasi Data Dinamis Berdasarkan Tipe
        $namaLengkap = '';
        $fotoPath = null;
        $ttdPath = null;
        $keteranganJabatan = $roleName; // Fallback ke roleName dari Controller
        $keteranganTambahan = 'MDT Hidayatus Shibyan';

        if (strtolower($tipe) === 'pengurus') {
            $namaLengkap = $profil->anggota->nama_lengkap ?? 'Tanpa Nama';
            $fotoPath = $profil->anggota->foto_utama ?? null;
            $ttdPath = $profil->anggota->ttd_utama ?? null;
            $keteranganJabatan = $profil->jabatan->nama_jabatan ?? $roleName;
            if ($profil->periode) {
                $keteranganTambahan = 'Periode ' . $profil->periode->nama_periode;
            }
        } else {
            // Untuk Ustadz dan Administrator
            $namaLengkap = $profil->nama_lengkap ?? 'Tanpa Nama';
            $fotoPath = $profil->foto ?? null;
            $ttdPath = $profil->tanda_tangan ?? null;
        }

        // Setup Avatar Fallback
        $fotoUrl = $fotoPath
            ? asset('storage/' . $fotoPath)
            : 'https://ui-avatars.com/api/?name=' .
                urlencode($namaLengkap) .
                '&background=27272a&color=fff&size=256&bold=true';
    @endphp

    <section class="min-h-[80vh] flex items-center justify-center py-12 px-4 relative z-10">

        <!-- Ambient Blurs (Subtle Zinc/Gray) -->
        <div
            class="absolute top-1/4 left-10 md:left-1/4 w-64 h-64 bg-zinc-300/30 dark:bg-zinc-700/20 rounded-2xl blur-[80px] pointer-events-none -z-10">
        </div>
        <div
            class="absolute bottom-1/4 right-10 md:right-1/4 w-64 h-64 bg-zinc-200/40 dark:bg-zinc-800/30 rounded-2xl blur-[80px] pointer-events-none -z-10">
        </div>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-[2rem] shadow-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden relative">

            <!-- Cover / Header -->
            <div class="h-32 bg-zinc-800 dark:bg-zinc-950 relative overflow-hidden flex justify-end p-5">
                <!-- Texture & Decor -->
                <div
                    class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')] mix-blend-overlay">
                </div>
                <div
                    class="absolute -bottom-16 -left-16 w-40 h-40 bg-zinc-600/30 rounded-2xl blur-2xl pointer-events-none">
                </div>

                <!-- Verified Badge -->
                <div
                    class="relative z-10 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-black px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-500/20 flex items-center gap-1.5 shadow-sm h-max uppercase tracking-widest">
                    <i class="bi bi-shield-fill-check text-sm"></i>
                    Verified TTE
                </div>
            </div>

            <!-- Profile Picture -->
            <div class="flex justify-center -mt-14 relative z-10 px-6">
                <!-- Outer Frame -->
                <div
                    class="w-28 h-28 bg-white dark:bg-zinc-900 rounded-2xl p-1.5 shadow-sm border border-zinc-200 dark:border-zinc-800 rotate-3 hover:rotate-0 transition-transform duration-300">
                    <div
                        class="w-full h-full rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 -rotate-3 hover:rotate-0 transition-transform duration-300">
                        <img src="{{ $fotoUrl }}" alt="Foto {{ $namaLengkap }}"
                            class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="text-center px-6 pt-4 pb-6">
                <h1 class="text-xl md:text-2xl font-black text-zinc-900 dark:text-white mb-1 tracking-tight">
                    {{ $namaLengkap }}
                </h1>
                <p class="text-zinc-500 dark:text-zinc-400 font-bold text-[11px] uppercase tracking-widest mb-4">
                    {{ $keteranganJabatan }}
                </p>

                <p
                    class="text-zinc-700 dark:text-zinc-300 text-xs font-bold bg-zinc-100 dark:bg-zinc-800/80 inline-block px-4 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    {{ $keteranganTambahan }}
                </p>

                <div class="w-full h-px bg-zinc-200 dark:bg-zinc-800 my-6"></div>

                <!-- Signature Section -->
                <div class="mt-2 text-left">
                    <p
                        class="text-[10px] text-zinc-400 dark:text-zinc-500 uppercase font-black tracking-widest mb-2 ml-1">
                        Tanda Tangan Elektronik
                    </p>

                    <div
                        class="bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-4 flex justify-center items-center h-32 relative overflow-hidden group">
                        <!-- Watermark -->
                        <div
                            class="absolute text-5xl text-zinc-900/5 dark:text-white/5 font-black uppercase tracking-widest select-none -rotate-12 group-hover:scale-110 transition-transform duration-500">
                            SAH
                        </div>

                        <!-- Render Tanda Tangan -->
                        @if ($ttdPath)
                            <img src="{{ asset('storage/' . $ttdPath) }}"
                                class="max-h-full max-w-full object-contain relative z-10 drop-shadow-sm"
                                alt="Tanda Tangan">
                        @else
                            <span class="text-zinc-400 dark:text-zinc-500 font-bold text-xs italic relative z-10">
                                Data tanda tangan belum diunggah
                            </span>
                        @endif
                    </div>

                    <p
                        class="text-[11px] text-zinc-500 dark:text-zinc-400 font-medium mt-4 leading-relaxed text-center px-2">
                        Dokumen ini telah ditandatangani secara digital. Jika Anda diarahkan ke halaman ini setelah
                        men-scan QR Code, maka dokumen tersebut adalah <strong
                            class="text-emerald-600 dark:text-emerald-400 font-black uppercase">ASLI</strong> dan diakui
                        oleh pihak Madrasah.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-zinc-50 dark:bg-zinc-800/40 border-t border-zinc-200 dark:border-zinc-800 p-4 text-center">
                <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">
                    MDT Hidayatus Shibyan &copy; {{ date('Y') }}
                </p>
            </div>
        </div>

    </section>
</x-guest-layout>
