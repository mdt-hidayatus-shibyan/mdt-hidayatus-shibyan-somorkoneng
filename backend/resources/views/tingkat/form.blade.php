<!-- Modal Form -->
<form action="{{ isset($tingkat) ? route('tingkat.update', $tingkat->id) : route('tingkat.store') }}" method="POST"
    class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($tingkat))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300">
        <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
            {{ isset($tingkat) ? 'Edit Tingkat' : 'Tambah Tingkat' }}
        </h3>
        <!-- Touch Target 40px -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1">

        <!-- Wrapper Form Input -->
        <div class="space-y-4">

            <!-- Kode Tingkat -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kode Tingkat
                </label>
                <input type="text" name="kode_tingkat" value="{{ $tingkat->kode_tingkat ?? old('kode_tingkat') }}"
                    placeholder="Contoh: TPQ/IBT/TSA" class="m3-input-glass w-full">
            </div>

            <!-- Kode MDT Tingkat -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Kode MDT Tingkat
                </label>
                <input type="text" name="kode_mdt_tingkat"
                    value="{{ $tingkat->kode_mdt_tingkat ?? old('kode_mdt_tingkat') }}" placeholder="Contoh: RA/MI/MTS"
                    class="m3-input-glass w-full">
            </div>

            <!-- Urutan -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Urutan
                </label>
                <input type="number" name="urutan_tingkat"
                    value="{{ $tingkat->urutan_tingkat ?? old('urutan_tingkat') }}" placeholder="Contoh: 1/2/3"
                    class="m3-input-glass w-full">
            </div>

            <!-- Nama Tingkat -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Tingkat
                </label>
                <input type="text" name="nama_tingkat" value="{{ $tingkat->nama_tingkat ?? old('nama_tingkat') }}"
                    placeholder="Contoh: Taman Pendidikan Al-Qur'an" class="m3-input-glass w-full">
            </div>

            <!-- Nama MDT Tingkat -->
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama MDT Tingkat
                </label>
                <input type="text" name="nama_mdt_tingkat"
                    value="{{ $tingkat->nama_mdt_tingkat ?? old('nama_mdt_tingkat') }}"
                    placeholder="Contoh: Raudlatul Athfal" class="m3-input-glass w-full" />
            </div>

            <!-- Warna Label -->
            <div class="space-y-2">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Warna Label
                </label>
                <div class="flex items-center gap-3 px-1">
                    <!-- Hijau -->
                    <label class="relative cursor-pointer hover:scale-105 transition-transform" title="Hijau">
                        <input type="radio" name="kode_warna" value="#10B981" class="peer sr-only"
                            {{ (isset($tingkat) && $tingkat->kode_warna == '#10B981') || old('kode_warna') == '#10B981' ? 'checked' : '' }}>
                        <div class="min-w-[38px] min-h-[38px] w-9.5 h-9.5 rounded-xl border-2 border-transparent peer-checked:border-white dark:peer-checked:border-zinc-900 peer-checked:ring-2 peer-checked:ring-emerald-500/50 transition-all flex items-center justify-center shadow-sm"
                            style="background-color: #10B981;">
                            <i
                                class="bi bi-check-lg text-white opacity-0 peer-checked:opacity-100 font-black text-base transition-opacity"></i>
                        </div>
                    </label>

                    <!-- Biru -->
                    <label class="relative cursor-pointer hover:scale-105 transition-transform" title="Biru">
                        <input type="radio" name="kode_warna" value="#3B82F6" class="peer sr-only"
                            {{ (isset($tingkat) && $tingkat->kode_warna == '#3B82F6') || old('kode_warna') == '#3B82F6' ? 'checked' : '' }}>
                        <div class="min-w-[38px] min-h-[38px] w-9.5 h-9.5 rounded-xl border-2 border-transparent peer-checked:border-white dark:peer-checked:border-zinc-900 peer-checked:ring-2 peer-checked:ring-blue-500/50 transition-all flex items-center justify-center shadow-sm"
                            style="background-color: #3B82F6;">
                            <i
                                class="bi bi-check-lg text-white opacity-0 peer-checked:opacity-100 font-black text-base transition-opacity"></i>
                        </div>
                    </label>

                    <!-- Orange -->
                    <label class="relative cursor-pointer hover:scale-105 transition-transform" title="Orange">
                        <input type="radio" name="kode_warna" value="#F97316" class="peer sr-only"
                            {{ (isset($tingkat) && $tingkat->kode_warna == '#F97316') || old('kode_warna') == '#F97316' ? 'checked' : '' }}>
                        <div class="min-w-[38px] min-h-[38px] w-9.5 h-9.5 rounded-xl border-2 border-transparent peer-checked:border-white dark:peer-checked:border-zinc-900 peer-checked:ring-2 peer-checked:ring-orange-500/50 transition-all flex items-center justify-center shadow-sm"
                            style="background-color: #F97316;">
                            <i
                                class="bi bi-check-lg text-white opacity-0 peer-checked:opacity-100 font-black text-base transition-opacity"></i>
                        </div>
                    </label>

                    <!-- Ungu -->
                    <label class="relative cursor-pointer hover:scale-105 transition-transform" title="Ungu">
                        <input type="radio" name="kode_warna" value="#8B5CF6" class="peer sr-only"
                            {{ (isset($tingkat) && $tingkat->kode_warna == '#8B5CF6') || old('kode_warna') == '#8B5CF6' ? 'checked' : '' }}>
                        <div class="min-w-[38px] min-h-[38px] w-9.5 h-9.5 rounded-xl border-2 border-transparent peer-checked:border-white dark:peer-checked:border-zinc-900 peer-checked:ring-2 peer-checked:ring-purple-500/50 transition-all flex items-center justify-center shadow-sm"
                            style="background-color: #8B5CF6;">
                            <i
                                class="bi bi-check-lg text-white opacity-0 peer-checked:opacity-100 font-black text-base transition-opacity"></i>
                        </div>
                    </label>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($tingkat) ? 'Simpan Perubahan' : 'Simpan Data' }}</span>
        </button>
    </div>
</form>

