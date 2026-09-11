<form id="transaksiForm" onsubmit="submitTransaksiForm(event)" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Jenis Transaksi <span class="text-rose-500">*</span>
            </label>
            <select name="jenis_transaksi" id="trx_jenis_transaksi" onchange="handleJenisTransaksiChange(this.value)"
                required
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="pemasukan" {{ ($defaultJenis ?? 'pemasukan') === 'pemasukan' ? 'selected' : '' }}>
                    Pemasukan (Cash In)</option>
                <option value="pengeluaran" {{ ($defaultJenis ?? '') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran
                    (Cash Out)</option>
                <option value="mutasi" {{ ($defaultJenis ?? '') === 'mutasi' ? 'selected' : '' }}>Mutasi Kas / Bank
                </option>
                <option value="simpanan" {{ ($defaultJenis ?? '') === 'simpanan' ? 'selected' : '' }}>Simpanan /
                    Tabungan</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Tanggal Transaksi <span class="text-rose-500">*</span>
            </label>
            <input type="date" name="tanggal_transaksi" id="trx_tanggal_transaksi" required
                value="{{ date('Y-m-d') }}"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
        </div>
    </div>

    <!-- Pos Akun Sumber & Tujuan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label id="lblAkunAsal"
                class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Pos Akun Kas <span class="text-rose-500">*</span>
            </label>
            <select name="akun_keuangan_id" id="trx_akun_keuangan_id" required
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="">-- Pilih Pos Akun Kas --</option>
                @foreach ($akuns as $a)
                    <option value="{{ $a->id }}">
                        [{{ $a->kode_akun }}] {{ $a->nama_akun }} (Saldo: Rp
                        {{ number_format($a->saldo_berjalan, 0, ',', '.') }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Akun Tujuan (Khusus Mutasi) -->
        <div id="containerAkunTujuan" class="hidden">
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Pos Akun Tujuan Mutasi <span class="text-rose-500">*</span>
            </label>
            <select name="akun_tujuan_id" id="trx_akun_tujuan_id"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="">-- Pilih Pos Akun Tujuan --</option>
                @foreach ($akuns as $a)
                    <option value="{{ $a->id }}">
                        [{{ $a->kode_akun }}] {{ $a->nama_akun }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Kategori (Untuk non-mutasi) -->
        <div id="containerKategori">
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Kategori Keuangan
            </label>
            <select name="kategori_keuangan_id" id="trx_kategori_keuangan_id"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoris as $k)
                    <option value="{{ $k->id }}" data-jenis="{{ $k->jenis }}">
                        [{{ strtoupper($k->jenis) }}] {{ $k->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Metode Pembayaran & Bank -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Metode Pembayaran <span class="text-rose-500">*</span>
            </label>
            <select name="metode_pembayaran" id="trx_metode_pembayaran"
                onchange="handleMetodePembayaranChange(this.value)" required
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="tunai">Tunai (Kas Fisik)</option>
                <option value="transfer_bank">Transfer Bank</option>
            </select>
        </div>

        <div id="containerBank" class="hidden">
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Rekening Bank <span class="text-rose-500">*</span>
            </label>
            <select name="bank_id" id="trx_bank_id"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                <option value="">-- Pilih Rekening Bank --</option>
                @foreach ($banks as $b)
                    <option value="{{ $b->id }}">
                        {{ $b->nama_bank }} - {{ $b->nomor_rekening }} (a.n {{ $b->atas_nama }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Nominal & Nomor Referensi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Nominal Transaksi (Rp) <span class="text-rose-500">*</span>
            </label>
            <input type="number" name="nominal" id="trx_nominal" required min="1" step="1" placeholder="0"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-sm font-black focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-mono">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
                Nomor Referensi / No. Nota
            </label>
            <input type="text" name="nomor_referensi" id="trx_nomor_referensi"
                placeholder="Contoh: NOTA-1234, KW-556"
                class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all font-mono">
        </div>
    </div>

    <!-- Keterangan -->
    <div>
        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
            Keterangan / Uraian Transaksi
        </label>
        <textarea name="keterangan" id="trx_keterangan" rows="2"
            placeholder="Deskripsi detail peruntukan atau sumber dana transaksi ini..."
            class="w-full px-3.5 py-2.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900/80 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all resize-none"></textarea>
    </div>

    <!-- Upload Bukti Transaksi -->
    <div>
        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-1.5 uppercase tracking-wider">
            Upload Bukti Transaksi / Nota (Opsional)
        </label>
        <input type="file" name="bukti_transaksi" id="trx_bukti_transaksi" accept="image/*,application/pdf"
            class="w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
    </div>

    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-zinc-200/80 dark:border-zinc-800">
        <button type="button" onclick="closeTransaksiModal()"
            class="px-4 py-2.5 rounded-2xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all">
            Batal
        </button>
        <button type="submit" id="btnSubmitTransaksi"
            class="m3-btn-primary px-5 py-2.5 text-xs font-bold shadow-md flex items-center gap-2">
            <i class="bi bi-check2-circle text-sm"></i>
            <span>Simpan Transaksi</span>
        </button>
    </div>
</form>

<script>
    function handleJenisTransaksiChange(val) {
        const lblAsal = document.getElementById('lblAkunAsal');
        const cTujuan = document.getElementById('containerAkunTujuan');
        const cKategori = document.getElementById('containerKategori');
        const katSelect = document.getElementById('trx_kategori_keuangan_id');

        if (val === 'mutasi') {
            lblAsal.innerText = 'Pos Akun Asal *';
            cTujuan.classList.remove('hidden');
            document.getElementById('trx_akun_tujuan_id').required = true;
            cKategori.classList.add('hidden');
        } else {
            lblAsal.innerText = 'Pos Akun Kas *';
            cTujuan.classList.add('hidden');
            document.getElementById('trx_akun_tujuan_id').required = false;
            cKategori.classList.remove('hidden');

            // Filter opsi kategori sesuai jenis
            if (katSelect) {
                Array.from(katSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    const optJenis = opt.getAttribute('data-jenis');
                    if (optJenis === val) {
                        opt.style.display = '';
                    } else {
                        opt.style.display = 'none';
                    }
                });
            }
        }
    }

    function handleMetodePembayaranChange(val) {
        const cBank = document.getElementById('containerBank');
        const bankSelect = document.getElementById('trx_bank_id');

        if (val === 'transfer_bank') {
            cBank.classList.remove('hidden');
            bankSelect.required = true;
        } else {
            cBank.classList.add('hidden');
            bankSelect.required = false;
        }
    }
</script>
