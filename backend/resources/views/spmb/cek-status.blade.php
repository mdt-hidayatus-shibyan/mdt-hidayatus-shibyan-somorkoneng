@section('title', 'Cek Status Pendaftaran SPMB')

<x-auth-layout maxWidth="max-w-2xl">
    <div class="space-y-6">
        <!-- Header -->
        <div class="text-center">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase bg-primary/10 text-primary dark:bg-primary-dark/15 dark:text-primary-dark border border-primary/20">
                <i class="bi bi-search"></i> Cek Status SPMB
            </span>
            <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight mt-2">
                Pencarian Status Pendaftaran
            </h2>
            <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5">
                Masukkan Nomor Pendaftaran (misal SPMB-2026-0001), Nomor KK, atau NIK Murid
            </p>
        </div>

        <!-- Search Form -->
        <form action="{{ route('spmb.cek-status') }}" method="GET" class="flex gap-2">
            <input type="text" name="keyword" value="{{ $keyword ?? '' }}"
                placeholder="Ketik No Pendaftaran / No KK / NIK..." class="m3-input-glass w-full text-xs font-bold"
                required>
            <button type="submit" class="m3-btn-primary h-10 px-5 text-xs font-bold shrink-0 gap-1.5">
                <i class="bi bi-search"></i>
                <span>Cari</span>
            </button>
        </form>

        <!-- Hasil Pencarian -->
        @if ($keyword)
            <div class="space-y-3">
                <h3 class="text-xs font-black text-zinc-400 uppercase tracking-wider">
                    Hasil Pencarian untuk: <span class="text-zinc-800 dark:text-zinc-200">"{{ $keyword }}"</span>
                </h3>

                @if ($hasil->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($hasil as $p)
                            <div
                                class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-black text-primary dark:text-primary-dark">
                                            {{ $p->nomor_pendaftaran }}
                                        </span>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                            {{ $p->status_pendaftaran == 'Diterima' ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400' : ($p->status_pendaftaran == 'Ditolak' ? 'bg-rose-500/15 text-rose-700 dark:text-rose-400' : 'bg-amber-500/15 text-amber-700 dark:text-amber-400') }}">
                                            {{ $p->status_pendaftaran }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-black text-zinc-900 dark:text-white mt-1 uppercase">
                                        {{ $p->nama_lengkap }}
                                    </h4>
                                    <p class="text-[11px] text-zinc-500 mt-0.5">
                                        Kelas: {{ $p->level->nama_level ?? '-' }} • Wali:
                                        {{ $p->waliMurid->nama_kepala_keluarga ?? '-' }}
                                    </p>
                                    @if ($p->nism_diberikan)
                                        <p class="text-xs font-black text-emerald-600 dark:text-emerald-400 mt-1">
                                            NISM Resmi: {{ $p->nism_diberikan }}
                                        </p>
                                    @endif
                                </div>

                                <div class="shrink-0 flex gap-2">
                                    <a href="{{ route('spmb.bukti', $p->nomor_pendaftaran) }}"
                                        class="m3-btn-primary h-9 px-4 text-xs font-bold gap-1.5 inline-flex items-center">
                                        <i class="bi bi-qr-code"></i>
                                        <span>Lihat Kartu</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div
                        class="p-8 text-center rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800">
                        <i class="bi bi-emoji-neutral text-3xl text-zinc-400 mb-2 inline-block"></i>
                        <h4 class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Data Tidak Ditemukan</h4>
                        <p class="text-xs text-zinc-400 mt-1">
                            Tidak ada data pendaftaran yang sesuai dengan kata kunci "{{ $keyword }}". Pastikan
                            nomor yang Anda masukkan benar.
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Footer Links -->
        <div
            class="pt-4 border-t border-zinc-200/70 dark:border-zinc-800 flex items-center justify-between text-xs font-semibold text-zinc-500">
            <a href="{{ route('spmb.form') }}"
                class="hover:text-primary dark:hover:text-primary-dark transition-colors flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Formulir SPMB
            </a>
            <a href="{{ route('login') }}"
                class="hover:text-primary dark:hover:text-primary-dark transition-colors flex items-center gap-1">
                <i class="bi bi-lock-fill"></i> Login Madrasah
            </a>
        </div>
    </div>
</x-auth-layout>
