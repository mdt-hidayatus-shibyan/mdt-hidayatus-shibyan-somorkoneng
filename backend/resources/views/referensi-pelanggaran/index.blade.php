@section('title', 'Referensi Pelanggaran')

<x-app-layout>
    <div class="mb-6 md:mb-8 flex flex-col lg:flex-row lg:items-end justify-between gap-5 relative z-10">
        <!-- Teks Header -->
        <div class="flex-1">
            <h2 class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                Referensi Pelanggaran
            </h2>
            <p class="text-xs md:text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-1">
                Kelola daftar pelanggaran kedisiplinan santri beserta skor poinnya.
            </p>
        </div>

        <!-- Actions (Pencarian & Tombol) -->
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto items-stretch sm:items-center">
            <form action="{{ route('referensi-pelanggaran.index') }}" method="GET"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">

                <!-- Search Input -->
                <div class="relative w-full sm:w-72 group/search">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors duration-300 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                        <i class="bi bi-search text-zinc-400 text-xs"></i>
                    </div>

                    <!-- Input Pencarian -->
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari Referensi/Kategori/Poin..."
                        class="m3-input-glass w-full !pl-9 !pr-9 text-xs font-bold">

                    @if (request('search'))
                        <a href="{{ route('referensi-pelanggaran.index') }}"
                            class="absolute inset-y-0 right-0 w-9 h-9 my-auto mr-1 flex items-center justify-center text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800 rounded-full transition-colors outline-none"
                            title="Reset Filter">
                            <i class="bi bi-x-lg text-xs font-bold"></i>
                        </a>
                    @endif
                </div>
            </form>

            @can('create referensi-pelanggaran')
                <div class="flex gap-2 w-full sm:w-auto">
                    <!-- Tombol Import -->
                    <a href="{{ route('referensi-pelanggaran.import') }}"
                        class="action-modal flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-xl text-xs font-black transition-all active:scale-95 shadow-2xs">
                        <i class="bi bi-file-earmark-spreadsheet-fill mr-1.5 text-sm"></i> Import
                    </a>

                    <!-- Tombol Tambah Data -->
                    <a href="{{ route('referensi-pelanggaran.create') }}"
                        class="action-modal m3-btn-primary flex-1 sm:flex-none h-10 px-5 text-xs font-black shadow-2xs group/btn">
                        <i class="bi bi-plus-lg mr-1.5 text-sm"></i>
                        <span>Tambah Data</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <!-- CONTAINER TABEL -->
    <div
        class="relative z-10 w-full overflow-x-auto rounded-2xl m3-glass-card shadow-2xs">
        <table id="data-grid-container" class="m3-table w-full text-left border-collapse whitespace-nowrap">

            <!-- KEPALA TABEL (Header) -->
            <thead>
                <tr
                    class="bg-zinc-50/80 dark:bg-zinc-950/60 border-b border-zinc-200/80 dark:border-zinc-800 text-[10px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-black">
                    <th class="px-4 py-3.5 rounded-tl-2xl w-12 text-center">No</th>
                    <th class="px-4 py-3.5 min-w-[250px]">Nama Pelanggaran</th>
                    <th class="px-4 py-3.5 text-center">Kategori</th>
                    <th class="px-4 py-3.5 text-center">Skor Poin</th>
                    <th class="px-4 py-3.5 text-right rounded-tr-2xl">Aksi</th>
                </tr>
            </thead>

            <!-- ISI TABEL (Body) -->
            <tbody class="divide-y divide-zinc-200/60 dark:divide-zinc-800/80 text-xs">

                @forelse($pelanggarans as $item)
                    @php
                        $badgeColor = match ($item->kategori) {
                            'Ringan'
                                => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
                            'Sedang'
                                => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                            'Berat'
                                => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
                            default
                                => 'bg-zinc-100 text-zinc-600 border-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700',
                        };
                    @endphp

                    <tr class="hover:bg-zinc-500/5 transition-colors group">

                        <!-- Kolom 1: No -->
                        <td class="px-4 py-3 text-center align-middle">
                            <span class="text-xs font-bold text-zinc-400 dark:text-zinc-500">
                                {{ $loop->iteration }}
                            </span>
                        </td>

                        <!-- Kolom 2: Nama Pelanggaran -->
                        <td class="px-4 py-3 align-middle">
                            <h4
                                class="font-black text-zinc-900 dark:text-zinc-100 text-xs tracking-tight leading-snug whitespace-normal line-clamp-2 max-w-md">
                                {{ $item->nama_pelanggaran }}
                            </h4>
                        </td>

                        <!-- Kolom 3: Kategori -->
                        <td class="px-4 py-3 text-center align-middle">
                            <span
                                class="inline-block px-2.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider border shadow-2xs {{ $badgeColor }}">
                                {{ $item->kategori }}
                            </span>
                        </td>

                        <!-- Kolom 4: Skor Poin -->
                        <td class="px-4 py-3 text-center align-middle">
                            <span
                                class="font-black text-sm {{ $item->poin > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-400' }}">
                                {{ $item->poin > 0 ? '+' . $item->poin : '-' }}
                            </span>
                        </td>

                        <!-- Kolom 5: Aksi -->
                        <td class="px-4 py-3 align-middle">
                            <div class="flex items-center justify-end gap-1.5">

                                @can('update referensi-pelanggaran')
                                    <a href="{{ route('referensi-pelanggaran.edit', $item->id) }}"
                                        class="action-modal w-8 h-8 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/20 flex items-center justify-center shadow-2xs transition-all active:scale-95"
                                        title="Edit">
                                        <i class="bi bi-pencil-fill text-xs"></i>
                                    </a>
                                @endcan

                                @can('delete referensi-pelanggaran')
                                    <form id="form-delete-{{ $item->id }}"
                                        action="{{ route('referensi-pelanggaran.destroy', $item->id) }}" method="POST"
                                        class="delete-ajax m-0 p-0 inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/20 flex items-center justify-center transition-all hover:scale-105 active:scale-90 shadow-2xs"
                                            title="Hapus">
                                            <i class="bi bi-trash-fill text-xs"></i>
                                        </button>
                                    </form>
                                @endcan

                            </div>
                        </td>
                    </tr>
                @empty
                    <!-- Data Kosong -->
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div
                                    class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800/80 rounded-2xl flex items-center justify-center text-zinc-400 dark:text-zinc-500 mb-3 shadow-2xs">
                                    <i class="bi bi-shield-x text-2xl"></i>
                                </div>
                                <h3 class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">
                                    Belum ada referensi pelanggaran
                                </h3>
                                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    Tambahkan daftar pelanggaran kedisiplinan santri pertama Anda.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>

