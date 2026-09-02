@section('title', 'Mata Pelajaran Kelas ' . $level->nama_level)
<x-app-layout>
    <!-- Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-20">
        <div class="flex items-center gap-3">
            <!-- Back Button -->
            <a href="{{ route('mata-pelajaran.index') }}"
                class="w-10 h-10 bg-white/80 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-xl flex items-center justify-center transition-all duration-200 shadow-sm active:scale-95 shrink-0 outline-none"
                title="Kembali">
                <i class="bi bi-arrow-left text-base font-bold"></i>
            </a>
            <div>
                <h2
                    class="text-2xl md:text-3xl font-black text-zinc-900 dark:text-white tracking-tight transition-colors duration-300">
                    Mata Pelajaran Kelas {{ $level->nama_level }}
                </h2>
                <p class="text-[13px] font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5 transition-colors duration-300">
                    Kelola daftar kurikulum dan materi pelajaran untuk kelas ini.
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">

            <!-- Search Form -->
            <form action="{{ route('mata-pelajaran.level', $level->id) }}" method="GET"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full sm:w-auto">
                <div class="relative w-full sm:w-72 group/search">
                    <div
                        class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors duration-300 text-zinc-400 group-focus-within/search:text-primary dark:group-focus-within/search:text-primary-dark">
                        <i class="bi bi-search text-sm"></i>
                    </div>

                    <!-- Input Pencarian -->
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari mata pelajaran..."
                        class="m3-input-glass w-full !pl-10 !pr-10">

                    <!-- Tombol Reset -->
                    @if (request('search'))
                        <a href="{{ route('mata-pelajaran.level', $level->id) }}"
                            class="absolute inset-y-0 right-0 w-10 h-10 flex items-center justify-center text-zinc-400 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800 rounded-xl transition-colors duration-200 outline-none"
                            title="Reset Filter">
                            <i class="bi bi-x-lg text-xs font-bold"></i>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Add Button -->
            @can('create mata-pelajaran')
                <a href="{{ route('mata-pelajaran.level.create', ['level_id' => $level->id]) }}"
                    class="action-modal m3-btn-primary w-full sm:w-auto group/btn">
                    <i class="bi bi-patch-plus text-base transition-transform duration-300 group-hover/btn:scale-110"></i>
                    <span>Tambah Mapel</span>
                </a>
            @endcan

        </div>
    </div>

    <!-- Table Container -->
    <div
        class="m3-glass-card overflow-hidden flex flex-col relative z-10">
        <div class="overflow-x-auto custom-scrollbar relative z-10 w-full">
            <table id="data-grid-container" class="m3-table w-full text-left whitespace-nowrap">
                <thead>
                    <tr>
                        <th scope="col" class="text-center w-12">No</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Mata Pelajaran</th>
                        <th scope="col">Kelompok</th>
                        <th scope="col">Referensi</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($level->mataPelajarans as $mapel)
                        <tr class="mapel-item group">

                            <!-- Kolom 1: Nomor Urut -->
                            <td class="text-center">
                                <span class="w-8 h-8 mx-auto flex items-center justify-center bg-zinc-100/80 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 rounded-xl text-xs font-extrabold border border-zinc-200/80 dark:border-zinc-800 shrink-0">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- Kolom 2: Kode Pelajaran -->
                            <td>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black tracking-wider uppercase bg-zinc-100/80 dark:bg-zinc-900 text-primary dark:text-primary-dark border border-zinc-200/80 dark:border-zinc-800">
                                    {{ $mapel->kode_mapel }}
                                </span>
                            </td>

                            <!-- Kolom 3: Nama Pelajaran -->
                            <td>
                                <h4 class="font-black text-zinc-900 dark:text-white text-xs sm:text-sm tracking-tight leading-snug">
                                    {{ $mapel->nama_mapel }}
                                </h4>
                            </td>

                            <!-- Kolom 4: Kelompok -->
                            <td>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider border {{ $mapel->kelompok == 'Wajib' ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border-indigo-200/80 dark:border-indigo-800/40' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200/80 dark:border-amber-800/40' }}">
                                    {{ $mapel->kelompok }}
                                </span>
                            </td>

                            <!-- Kolom 5: Referensi -->
                            <td>
                                <span
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-zinc-600 dark:text-zinc-400 truncate max-w-[180px]"
                                    title="{{ $mapel->referensi }}">
                                    <i class="bi bi-book text-xs text-zinc-400"></i>
                                    {{ $mapel->referensi ?? '-' }}
                                </span>
                            </td>

                            <!-- Kolom 6: Status Aktif -->
                            <td class="text-center">
                                <div class="flex justify-center">
                                    <x-toggle-status :is-active="$mapel->is_active" :url="route('mata-pelajaran.toggle-status', $mapel->id)" />
                                </div>
                            </td>

                            <!-- Kolom 7: Aksi -->
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('update mata-pelajaran')
                                        <a href="{{ route('mata-pelajaran.level.edit', ['level_id' => $level->id, 'mapel_id' => $mapel->id]) }}"
                                            class="action-modal min-w-[34px] min-h-[34px] w-8.5 h-8.5 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200/60 dark:border-blue-800/40 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-all hover:scale-105 active:scale-95 shadow-2xs"
                                            title="Edit">
                                            <i class="bi bi-pencil-fill text-xs"></i>
                                        </a>
                                    @endcan

                                    @can('hapus mata-pelajaran')
                                        <form id="form-delete-{{ $mapel->id }}"
                                            action="{{ route('mata-pelajaran.destroy', $mapel->id) }}" method="POST"
                                            class="delete-ajax inline m-0 p-0">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="min-w-[34px] min-h-[34px] w-8.5 h-8.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-800/40 text-rose-600 dark:text-rose-400 flex items-center justify-center hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-all hover:scale-105 active:scale-95 shadow-2xs"
                                                title="Hapus">
                                                <i class="bi bi-trash-fill text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12">
                                <x-empty-state icon="bi-journal-x" title="Belum Ada Mata Pelajaran"
                                    message="Silakan tambahkan pelajaran khusus untuk tingkatan ini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

