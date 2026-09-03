@section('title', 'Pengumuman')

<x-app-layout>

    <!-- Header Page & Actions -->
    <div class="mb-6 md:mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 relative z-10">

        <div>
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Pengumuman
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-0.5">
                Kelola informasi, agenda kegiatan, dan pemberitahuan penting madrasah.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full lg:w-auto">

            <!-- Form Filter/Search -->
            <form action="{{ route('pengumuman.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <i class="bi bi-search text-xs"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..."
                        class="m3-input-glass w-full !pl-9 text-xs font-bold">
                </div>
            </form>

            @can('create pengumuman')
                <a href="{{ route('pengumuman.create') }}"
                    class="m3-btn-primary h-10 px-5 text-xs font-black shadow-2xs shrink-0 flex items-center justify-center">
                    <i class="bi bi-megaphone-fill mr-1.5"></i>
                    Buat Pengumuman
                </a>
            @endcan
        </div>
    </div>

    <!-- Grid Pengumuman -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 relative z-10">
        @forelse($pengumumans as $pengumuman)
            @php
                $colorClass = match ($pengumuman->tipe) {
                    'Penting' => 'text-rose-600 dark:text-rose-400 bg-rose-500/10 border-rose-500/20',
                    'Kegiatan' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                    'Libur' => 'text-amber-600 dark:text-amber-400 bg-amber-500/10 border-amber-500/20',
                    default => 'text-blue-600 dark:text-blue-400 bg-blue-500/10 border-blue-500/20',
                };

                $iconClass = match ($pengumuman->tipe) {
                    'Penting' => 'bi-exclamation-triangle-fill',
                    'Kegiatan' => 'bi-calendar-event-fill',
                    'Libur' => 'bi-calendar2-x-fill',
                    default => 'bi-info-circle-fill',
                };
            @endphp

            <div
                class="m3-glass-card p-5 flex flex-col justify-between transition-all shadow-2xs hover:border-primary/40 dark:hover:border-primary-dark/40 group relative">

                <div>
                    <!-- Badge & Status -->
                    <div class="flex justify-between items-start mb-3">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-2xs {{ $colorClass }}">
                            <i class="bi {{ $iconClass }}"></i> {{ $pengumuman->tipe }}
                        </span>

                        <div class="flex items-center gap-1.5">
                            @if ($pengumuman->lampiran_pdf)
                                <span
                                    class="px-2 py-0.5 rounded-lg bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-black uppercase tracking-wider border border-rose-500/20 shadow-2xs flex items-center gap-1"
                                    title="Ada lampiran PDF">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                                </span>
                            @endif

                            @if ($pengumuman->status == 'Draft')
                                <span
                                    class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] font-black uppercase tracking-wider border border-zinc-200 dark:border-zinc-700 shadow-2xs">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Judul & Konten Singkat -->
                    <h3
                        class="text-base font-black text-zinc-900 dark:text-white leading-tight mb-1.5 tracking-tight group-hover:text-primary dark:group-hover:text-primary-dark transition-colors line-clamp-2">
                        {{ $pengumuman->judul }}
                    </h3>
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 line-clamp-3 mb-4 leading-relaxed">
                        {{ Str::limit(strip_tags($pengumuman->konten), 120) }}
                    </p>
                </div>

                <!-- Footer Card (Tanggal & Aksi) -->
                <div class="pt-3.5 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                    <div
                        class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-wider flex items-center">
                        <i class="bi bi-clock-history mr-1"></i>
                        {{ $pengumuman->created_at->diffForHumans() }}
                    </div>

                    <div class="flex gap-1.5">
                        @can('update pengumuman')
                            <a href="{{ route('pengumuman.edit', $pengumuman->id) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-blue-600 dark:hover:text-blue-400 border border-zinc-200/80 dark:border-zinc-700 transition-all shadow-2xs active:scale-95 outline-none"
                                title="Edit">
                                <i class="bi bi-pencil-fill text-xs"></i>
                            </a>
                        @endcan
                        @can('read pengumuman')
                            <a href="{{ route('pengumuman.show', $pengumuman->id) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-primary/10 dark:bg-primary-dark/20 text-primary dark:text-primary-dark border border-primary/20 transition-all shadow-2xs active:scale-95 outline-none"
                                title="Baca Detail">
                                <i class="bi bi-chevron-right text-xs font-bold"></i>
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-empty-state icon="bi-megaphone" title="Belum Ada Pengumuman"
                    message="Papan mading masih kosong. Silakan buat pengumuman baru." />
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($pengumumans->hasPages())
        <div class="mt-5 m3-glass-card p-4 sm:p-5 relative z-10 shadow-2xs">
            {{ $pengumumans->links('vendor.pagination.custom') }}
        </div>
    @endif

</x-app-layout>
