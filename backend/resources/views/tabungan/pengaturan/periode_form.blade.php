<!-- Modal Form Periode Tabungan -->
<form
    action="{{ isset($periode) ? route('tabungan.pengaturan.periode.update', $periode->id) : route('tabungan.pengaturan.periode.store') }}"
    method="POST" class="ajax-form relative z-10 flex flex-col max-h-[90vh]">
    @csrf
    @if (isset($periode))
        @method('PUT')
    @endif

    <!-- Modal Header -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-b border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 flex items-center justify-between transition-colors duration-300 shrink-0">
        <div class="flex items-center gap-2.5">
            <div
                class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-sm">
                <i class="bi bi-calendar-event"></i>
            </div>
            <h3 class="text-base md:text-lg font-black text-zinc-900 dark:text-white tracking-tight">
                {{ isset($periode) ? 'Edit Periode Tabungan' : 'Buka Periode Tabungan Baru' }}
            </h3>
        </div>
        <!-- Touch Target 40px -->
        <button type="button" data-dismiss="modal" command="close" commandfor="dialog"
            class="min-w-[36px] min-h-[36px] flex items-center justify-center rounded-xl bg-transparent hover:bg-zinc-200/60 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 transition-colors duration-200 outline-none">
            <i class="bi bi-x-lg text-xs font-bold"></i>
        </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5 md:p-6 transition-colors duration-300 overflow-y-auto custom-scrollbar flex-1 space-y-4 text-xs">

        <!-- Tahun Pelajaran & Nama Periode -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Tahun Pelajaran
                </label>
                <select name="tahun_pelajaran_id" required class="m3-input-glass w-full text-xs font-bold">
                    @foreach ($tahunPelajarans as $tp)
                        <option value="{{ $tp->id }}"
                            {{ (isset($periode) && $periode->tahun_pelajaran_id == $tp->id) || (!isset($periode) && $tp->is_active) ? 'selected' : '' }}>
                            {{ $tp->nama_hijriyah }} H | {{ $tp->nama_masehi }} M
                            {{ $tp->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Nama Periode Program
                </label>
                <input type="text" name="nama_periode" value="{{ $periode->nama_periode ?? old('nama_periode') }}"
                    placeholder="Contoh: Tabungan Berjangka 2026/2027" required class="m3-input-glass w-full">
            </div>
        </div>

        <!-- Rentang Tanggal: Mulai, Tutup, Bagi -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Tanggal Mulai
                </label>
                <input type="date" name="tanggal_mulai" id="periode_tgl_mulai"
                    value="{{ isset($periode) ? \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d') : date('Y-m-d') }}"
                    required class="m3-input-glass w-full">
            </div>

            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Tanggal Penutupan
                </label>
                <input type="date" name="tanggal_penutupan" id="periode_tgl_penutupan"
                    value="{{ isset($periode) ? \Carbon\Carbon::parse($periode->tanggal_penutupan)->format('Y-m-d') : date('Y-m-d', strtotime('+10 months')) }}"
                    required class="m3-input-glass w-full">
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label
                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                        Tanggal Pembagian
                    </label>
                    <span class="text-[9px] font-bold text-purple-600 dark:text-purple-400">Otomatis +2 Minggu</span>
                </div>
                <input type="date" name="tanggal_pembagian" id="periode_tgl_pembagian"
                    value="{{ isset($periode) ? \Carbon\Carbon::parse($periode->tanggal_pembagian)->format('Y-m-d') : date('Y-m-d', strtotime('+10 months +14 days')) }}"
                    required class="m3-input-glass w-full">
            </div>
        </div>

        <!-- Status & Catatan -->
        <div class="grid grid-cols-1 {{ isset($periode) ? 'sm:grid-cols-2' : '' }} gap-3.5">
            @if (isset($periode))
                <div class="space-y-1.5">
                    <label
                        class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                        Status Siklus Periode
                    </label>
                    <select name="status" required class="m3-input-glass w-full text-xs font-bold">
                        <option value="Aktif" {{ $periode->status == 'Aktif' ? 'selected' : '' }}>Aktif (Menerima
                            Setoran & Penarikan)</option>
                        <option value="Ditutup" {{ $periode->status == 'Ditutup' ? 'selected' : '' }}>Ditutup
                            (Hanya Penarikan / Menjelang Pembagian)</option>
                        <option value="Selesai" {{ $periode->status == 'Selesai' ? 'selected' : '' }}>Selesai
                            (Sudah Dibagikan Semua)</option>
                    </select>
                </div>
            @endif

            <div class="space-y-1.5">
                <label
                    class="block text-[11px] font-extrabold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider ml-1">
                    Catatan Tambahan
                </label>
                <input type="text" name="catatan" value="{{ $periode->catatan ?? old('catatan') }}"
                    placeholder="Opsional (misal: Dibagikan sebelum Haflatul Imtihan)" class="m3-input-glass w-full">
            </div>
        </div>

        <!-- Checkbox Jadikan Utama -->
        <div class="flex items-center gap-2.5 pt-1">
            <input type="checkbox" name="is_active" id="modal_is_active" value="1"
                {{ (isset($periode) && $periode->is_active) || !isset($periode) ? 'checked' : '' }}
                class="rounded-lg border-zinc-300 text-primary focus:ring-primary h-4 w-4">
            <label for="modal_is_active"
                class="font-bold text-zinc-700 dark:text-zinc-300 cursor-pointer text-xs select-none">
                Jadikan sebagai Periode Tabungan Aktif Utama
            </label>
        </div>

    </div>

    <!-- Modal Footer / Actions -->
    <div
        class="bg-zinc-50/80 dark:bg-black/40 border-t border-zinc-100 dark:border-zinc-800/80 px-5 py-3.5 sm:flex sm:flex-row-reverse gap-2.5 transition-colors duration-300 shrink-0">
        <button type="submit" class="m3-btn-primary w-full sm:w-auto">
            <i class="bi bi-save2-fill text-sm"></i>
            <span>{{ isset($periode) ? 'Simpan Perubahan' : 'Buat Periode Baru' }}</span>
        </button>
        <button type="button" data-dismiss="modal" command="close"
            class="m3-btn-secondary w-full sm:w-auto mt-2 sm:mt-0">
            Batal
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.querySelector('#modal-content-wrapper form');
        if (!form) return;
        const inputPenutupan = form.querySelector('#periode_tgl_penutupan');
        const inputPembagian = form.querySelector('#periode_tgl_pembagian');

        if (inputPenutupan && inputPembagian) {
            inputPenutupan.addEventListener('change', function() {
                if (this.value) {
                    const date = new Date(this.value);
                    if (!isNaN(date.getTime())) {
                        date.setDate(date.getDate() + 14); // Otomatis +2 Minggu (14 Hari)
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        inputPembagian.value = `${year}-${month}-${day}`;
                    }
                }
            });
        }
    })();
</script>
