@section('title', 'Pengajuan Pinjaman & Agunan Baru')

<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('keuangan.pinjaman.index') }}"
                    class="text-xs font-bold text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 inline-flex items-center gap-1.5 mb-1.5 transition-colors">
                    <i class="bi bi-arrow-left"></i>
                    <span>Kembali ke Daftar Pinjaman</span>
                </a>
                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                    Formulir Pengajuan Pinjaman Madrasah
                </h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                    Pengajuan pinjaman, kalkulasi tenor angsuran bulanan, dan pendaftaran agunan/jaminan fisik.
                </p>
            </div>
        </div>

        <form id="pengajuanPinjamanForm" onsubmit="submitPengajuanPinjaman(event)" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <!-- Section 1: Data Nasabah & Akad -->
            <div
                class="m3-glass-card p-6 md:p-8 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div
                        class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-sm">
                        1
                    </div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                        Data Nasabah & Sumber Dana
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Nasabah Peminjam <span class="text-rose-500">*</span>
                        </label>
                        <select name="nasabah_pinjaman_id" id="pj_nasabah_id" required
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <option value="">-- Pilih Data Nasabah --</option>
                            @foreach ($nasabahs as $n)
                                <option value="{{ $n->id }}"
                                    {{ ($selectedNasabahId ?? '') == $n->id ? 'selected' : '' }}>
                                    [{{ $n->kode_nasabah }}] {{ $n->nama_lengkap }} ({{ strtoupper($n->tipe_nasabah) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Pos Akun Kas Sumber Dana <span class="text-rose-500">*</span>
                        </label>
                        <select name="akun_keuangan_id" id="pj_akun_id" required
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            @foreach ($akuns as $a)
                                <option value="{{ $a->id }}">
                                    [{{ $a->kode_akun }}] {{ $a->nama_akun }} (Saldo: Rp
                                    {{ number_format($a->saldo_berjalan, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Tanggal Pengajuan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_pengajuan" id="pj_tanggal_pengajuan" required
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Keperluan / Alasan Pinjaman <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="keperluan_pinjaman" id="pj_keperluan" required
                            placeholder="Contoh: Modal Usaha Dagang, Biaya Renovasi Rumah, Pengobatan"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Nominal, Tenor & Simulasi Hitung -->
            <div
                class="m3-glass-card p-6 md:p-8 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div
                        class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-black text-sm">
                        2
                    </div>
                    <h3 class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                        Nominal Pinjaman & Simulasi Angsuran
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Nominal Pinjaman (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="nominal_pinjaman" id="pj_nominal" required min="50000"
                            step="50000" placeholder="Contoh: 5000000" oninput="hitungSimulasiPinjaman()"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-sm font-black focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-mono">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Tenor Cicilan (Bulan) <span class="text-rose-500">*</span>
                        </label>
                        <select name="tenor_bulan" id="pj_tenor" onchange="hitungSimulasiPinjaman()" required
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <option value="1">1 Bulan (Pelunasan 1x)</option>
                            <option value="2">2 Bulan</option>
                            <option value="3">3 Bulan</option>
                            <option value="4">4 Bulan</option>
                            <option value="5">5 Bulan</option>
                            <option value="6" selected>6 Bulan</option>
                            <option value="10">10 Bulan</option>
                            <option value="12">12 Bulan (1 Tahun)</option>
                            <option value="18">18 Bulan</option>
                            <option value="24">24 Bulan (2 Tahun)</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Biaya Administrasi (Rp)
                        </label>
                        <input type="number" name="biaya_administrasi" id="pj_biaya_admin" min="0"
                            step="5000" value="0" oninput="hitungSimulasiPinjaman()"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Infaq Sukarela / Margin (%)
                        </label>
                        <input type="number" name="margin_infaq_persen" id="pj_margin" min="0" max="100"
                            step="0.5" value="0" oninput="hitungSimulasiPinjaman()"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-mono">
                        <span class="text-[11px] text-zinc-400 mt-1 block">Infaq sukarela / sumbangan jariyah madrasah
                            tanpa bunga riba.</span>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                            Catatan Pengajuan
                        </label>
                        <input type="text" name="catatan" id="pj_catatan"
                            placeholder="Catatan persetujuan khusus atau kesepakatan..."
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <!-- Simulation Live Result Box -->
                <div
                    class="p-4 md:p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 space-y-3">
                    <span
                        class="text-[11px] font-black uppercase tracking-wider text-primary dark:text-primary-dark block">
                        📊 Ringkasan Simulasi Akad Pinjaman:
                    </span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Nominal Pencairan:</span>
                            <span class="font-black text-sm text-zinc-900 dark:text-white font-mono"
                                id="sim_pencairan">Rp 0</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Angsuran Pokok /
                                Bln:</span>
                            <span class="font-black text-sm text-zinc-900 dark:text-white font-mono" id="sim_pokok">Rp
                                0</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Infaq Sukarela /
                                Bln:</span>
                            <span class="font-black text-sm text-emerald-600 dark:text-emerald-400 font-mono"
                                id="sim_infaq">Rp 0</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-zinc-400 uppercase font-bold block">Total Cicilan /
                                Bln:</span>
                            <span class="font-black text-sm text-primary dark:text-primary-dark font-mono"
                                id="sim_total_bulanan">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Data Agunan / Jaminan Fisik (Dynamic Repeater) -->
            <div
                class="m3-glass-card p-6 md:p-8 rounded-3xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-black text-sm">
                            3
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-wider">
                                Agunan / Jaminan Fisik
                            </h3>
                            <p class="text-[11px] text-zinc-400">Data fisik jaminan yang dititipkan di madrasah (BPKB,
                                Sertifikat, Emas, Ijazah, dll)</p>
                        </div>
                    </div>

                    <button type="button" onclick="addJaminanItem()"
                        class="px-3 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-xs font-bold transition-all flex items-center gap-1.5">
                        <i class="bi bi-plus-circle"></i>
                        <span>Tambah Agunan</span>
                    </button>
                </div>

                <div id="jaminanListContainer" class="space-y-4">
                    <!-- Jaminan Row Item 0 (Default Initial) -->
                    <div class="jaminan-row p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 space-y-3 relative"
                        data-index="0">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="bi bi-shield-lock-fill text-amber-500"></i>
                                <span>Item Agunan #1</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Jenis
                                    Jaminan</label>
                                <select name="jaminan[0][jenis_jaminan]"
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                                    <option value="bpkb_motor">BPKB Sepeda Motor</option>
                                    <option value="bpkb_mobil">BPKB Mobil</option>
                                    <option value="sertifikat_tanah">Sertifikat Tanah / Rumah (SHM/AJB)</option>
                                    <option value="emas_perhiasan">Emas / Logam Mulia</option>
                                    <option value="ijazah">Ijazah Asli</option>
                                    <option value="buku_tabungan">Buku Tabungan / Bilyet Deposito</option>
                                    <option value="elektronik">Barang Elektronik</option>
                                    <option value="lainnya">Barang Berharga Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Nama /
                                    Merk Barang Agunan</label>
                                <input type="text" name="jaminan[0][nama_barang_jaminan]"
                                    placeholder="Contoh: Honda Vario 125 2022"
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">No.
                                    Dokumen / No. Seri</label>
                                <input type="text" name="jaminan[0][nomor_dokumen_jaminan]"
                                    placeholder="No. BPKB / No. Sertifikat"
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Atas
                                    Nama Dokumen</label>
                                <input type="text" name="jaminan[0][atas_nama_dokumen]"
                                    placeholder="Nama pemilik dokumen"
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Taksiran
                                    Nilai Agunan (Rp)</label>
                                <input type="number" name="jaminan[0][taksiran_nilai]" placeholder="0"
                                    min="0" step="50000"
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Lokasi
                                    Penyimpanan di Madrasah</label>
                                <input type="text" name="jaminan[0][lokasi_penyimpanan]"
                                    value="Brankas MDT Hidayatus Shibyan"
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Foto
                                    Dokumen Agunan (Kertas/Sertifikat)</label>
                                <input type="file" name="jaminan[0][foto_dokumen]" accept="image/*"
                                    class="w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-200 file:text-zinc-700">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Foto
                                    Fisik Barang Agunan</label>
                                <input type="file" name="jaminan[0][foto_barang]" accept="image/*"
                                    class="w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-200 file:text-zinc-700">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('keuangan.pinjaman.index') }}"
                    class="px-5 py-3 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" id="btnSubmitPengajuan"
                    class="m3-btn-primary px-7 py-3 text-xs font-black shadow-lg flex items-center gap-2">
                    <i class="bi bi-check2-circle text-base"></i>
                    <span>Simpan Pengajuan Pinjaman</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let jaminanIndex = 1;

            function hitungSimulasiPinjaman() {
                const nominal = parseFloat(document.getElementById('pj_nominal').value) || 0;
                const tenor = parseInt(document.getElementById('pj_tenor').value) || 1;
                const biayaAdmin = parseFloat(document.getElementById('pj_biaya_admin').value) || 0;
                const marginPersen = parseFloat(document.getElementById('pj_margin').value) || 0;

                const pencairan = Math.max(0, nominal - biayaAdmin);
                const angsuranPokok = nominal > 0 ? Math.round(nominal / tenor) : 0;
                const infaqBulanan = nominal > 0 ? Math.round((nominal * (marginPersen / 100)) / tenor) : 0;
                const angsuranTotal = angsuranPokok + infaqBulanan;

                document.getElementById('sim_pencairan').innerText = 'Rp ' + pencairan.toLocaleString('id-ID');
                document.getElementById('sim_pokok').innerText = 'Rp ' + angsuranPokok.toLocaleString('id-ID');
                document.getElementById('sim_infaq').innerText = 'Rp ' + infaqBulanan.toLocaleString('id-ID');
                document.getElementById('sim_total_bulanan').innerText = 'Rp ' + angsuranTotal.toLocaleString('id-ID');
            }

            function addJaminanItem() {
                const container = document.getElementById('jaminanListContainer');
                const idx = jaminanIndex++;

                const html = `
            <div class="jaminan-row p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800 space-y-3 relative" data-index="${idx}">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="bi bi-shield-lock-fill text-amber-500"></i>
                        <span>Item Agunan #${idx + 1}</span>
                    </span>
                    <button type="button" onclick="this.closest('.jaminan-row').remove()"
                        class="text-rose-500 hover:text-rose-700 text-xs font-bold flex items-center gap-1">
                        <i class="bi bi-trash3"></i> Hapus Agunan
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Jenis Jaminan</label>
                        <select name="jaminan[${idx}][jenis_jaminan]" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                            <option value="bpkb_motor">BPKB Sepeda Motor</option>
                            <option value="bpkb_mobil">BPKB Mobil</option>
                            <option value="sertifikat_tanah">Sertifikat Tanah / Rumah (SHM/AJB)</option>
                            <option value="emas_perhiasan">Emas / Logam Mulia</option>
                            <option value="ijazah">Ijazah Asli</option>
                            <option value="buku_tabungan">Buku Tabungan / Bilyet Deposito</option>
                            <option value="elektronik">Barang Elektronik</option>
                            <option value="lainnya">Barang Berharga Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Nama / Merk Barang Agunan</label>
                        <input type="text" name="jaminan[${idx}][nama_barang_jaminan]" placeholder="Contoh: Sertifikat Tanah 200m2" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">No. Dokumen / No. Seri</label>
                        <input type="text" name="jaminan[${idx}][nomor_dokumen_jaminan]" placeholder="No. Sertifikat / Dokumen" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Atas Nama Dokumen</label>
                        <input type="text" name="jaminan[${idx}][atas_nama_dokumen]" placeholder="Nama pemilik dokumen" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Taksiran Nilai Agunan (Rp)</label>
                        <input type="number" name="jaminan[${idx}][taksiran_nilai]" placeholder="0" min="0" step="50000" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Lokasi Penyimpanan di Madrasah</label>
                        <input type="text" name="jaminan[${idx}][lokasi_penyimpanan]" value="Brankas MDT Hidayatus Shibyan" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Foto Dokumen Agunan (Kertas/Sertifikat)</label>
                        <input type="file" name="jaminan[${idx}][foto_dokumen]" accept="image/*" class="w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-200 file:text-zinc-700">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mb-1">Foto Fisik Barang Agunan</label>
                        <input type="file" name="jaminan[${idx}][foto_barang]" accept="image/*" class="w-full text-xs text-zinc-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-200 file:text-zinc-700">
                    </div>
                </div>
            </div>
            `;

                container.insertAdjacentHTML('beforeend', html);
            }

            function submitPengajuanPinjaman(e) {
                e.preventDefault();
                const form = e.target;
                const btn = document.getElementById('btnSubmitPengajuan');
                btn.disabled = true;
                btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin text-sm"></i> Menyimpan...`;

                const formData = new FormData(form);

                fetch("{{ route('keuangan.pinjaman.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect || "{{ route('keuangan.pinjaman.index') }}";
                        });
                    })
                    .catch(err => {
                        let msg = err.message || 'Terjadi kesalahan validasi.';
                        if (err.errors) {
                            msg = Object.values(err.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan Pengajuan',
                            html: msg
                        });
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML =
                            `<i class="bi bi-check2-circle text-base"></i> <span>Simpan Pengajuan Pinjaman</span>`;
                    });
            }

            // Initialize calculation on page load
            hitungSimulasiPinjaman();
        </script>
    @endpush
</x-app-layout>
