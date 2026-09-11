<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Akun Keuangan (Pos Kas / Buku Keuangan)
        Schema::create('akun_keuangans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun', 50)->unique();
            $table->string('nama_akun', 150);
            $table->enum('tipe_akun', ['kas', 'bank', 'operasional', 'investasi', 'kewajiban', 'ekuitas'])->default('kas');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('saldo_berjalan', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Rekening Bank Madrasah
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank', 100);
            $table->string('kode_bank', 20)->nullable();
            $table->string('nomor_rekening', 50);
            $table->string('atas_nama', 150);
            $table->string('cabang', 150)->nullable();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Kategori & Subkategori Keuangan
        Schema::create('kategori_keuangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('kategori_keuangans')->onDelete('cascade');
            $table->string('kode_kategori', 50)->unique();
            $table->string('nama_kategori', 150);
            $table->enum('jenis', ['pemasukan', 'pengeluaran', 'simpanan']);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Transaksi Keuangan
        Schema::create('transaksi_keuangans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->onDelete('cascade');
            $table->foreignId('akun_keuangan_id')->constrained('akun_keuangans')->onDelete('cascade');
            $table->foreignId('kategori_keuangan_id')->nullable()->constrained('kategori_keuangans')->onDelete('set null');
            $table->enum('jenis_transaksi', ['pemasukan', 'pengeluaran', 'mutasi', 'simpanan']);
            $table->enum('metode_pembayaran', ['tunai', 'transfer_bank'])->default('tunai');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->foreignId('akun_tujuan_id')->nullable()->constrained('akun_keuangans')->onDelete('set null');
            $table->foreignId('bank_tujuan_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_transaksi');
            $table->string('nomor_referensi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti_transaksi', 255)->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['draft', 'sukses', 'batal'])->default('sukses');
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Data Nasabah Peminjam
        Schema::create('nasabah_pinjamans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_nasabah', 50)->unique();
            $table->enum('tipe_nasabah', ['ustadz', 'pengurus', 'wali_murid', 'umum'])->default('umum');
            $table->foreignId('ustadz_id')->nullable()->constrained('ustadzs')->onDelete('set null');
            $table->foreignId('wali_murid_id')->nullable()->constrained('wali_murids')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_lengkap', 150);
            $table->string('nik_ktp', 30)->nullable();
            $table->string('no_hp', 30);
            $table->text('alamat');
            $table->string('pekerjaan', 100)->nullable();
            $table->string('foto_ktp', 255)->nullable();
            $table->string('foto_nasabah', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Data Pinjaman
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjaman', 50)->unique();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->onDelete('cascade');
            $table->foreignId('nasabah_pinjaman_id')->constrained('nasabah_pinjamans')->onDelete('cascade');
            $table->foreignId('akun_keuangan_id')->constrained('akun_keuangans')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->decimal('nominal_pinjaman', 15, 2);
            $table->decimal('biaya_administrasi', 15, 2)->default(0);
            $table->decimal('nominal_pencairan', 15, 2);
            $table->unsignedInteger('tenor_bulan');
            $table->decimal('margin_infaq_persen', 5, 2)->default(0);
            $table->decimal('nominal_infaq_bulanan', 15, 2)->default(0);
            $table->decimal('nominal_angsuran_pokok', 15, 2);
            $table->decimal('nominal_angsuran_total', 15, 2);
            $table->decimal('total_pinjaman_dikembalikan', 15, 2);
            $table->decimal('total_terbayar', 15, 2)->default(0);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_pencairan')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->text('keperluan_pinjaman');
            $table->enum('status', ['pengajuan', 'disetujui', 'dicairkan', 'lunas', 'macet', 'ditolak'])->default('pengajuan');
            $table->text('catatan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('dicairkan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Jaminan / Agunan Pinjaman
        Schema::create('jaminan_pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjamans')->onDelete('cascade');
            $table->enum('jenis_jaminan', [
                'bpkb_motor',
                'bpkb_mobil',
                'sertifikat_tanah',
                'emas_perhiasan',
                'ijazah',
                'buku_tabungan',
                'elektronik',
                'lainnya'
            ]);
            $table->string('nama_barang_jaminan', 200);
            $table->string('nomor_dokumen_jaminan', 100)->nullable();
            $table->string('atas_nama_dokumen', 150)->nullable();
            $table->decimal('taksiran_nilai', 15, 2)->default(0);
            $table->text('deskripsi_kondisi')->nullable();
            $table->string('lokasi_penyimpanan', 150)->nullable();
            $table->string('foto_dokumen', 255)->nullable();
            $table->string('foto_barang', 255)->nullable();
            $table->enum('status_jaminan', ['ditahan_madrasah', 'dikembalikan', 'disita_dilelang'])->default('ditahan_madrasah');
            $table->date('tanggal_diserahkan');
            $table->date('tanggal_dikembalikan')->nullable();
            $table->foreignId('penerima_jaminan_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('pengembali_jaminan_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 8. Angsuran Pinjaman
        Schema::create('angsuran_pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjamans')->onDelete('cascade');
            $table->unsignedInteger('angsuran_ke');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('nominal_pokok', 15, 2);
            $table->decimal('nominal_infaq_margin', 15, 2)->default(0);
            $table->decimal('nominal_denda', 15, 2)->default(0);
            $table->decimal('total_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['tunai', 'transfer_bank'])->default('tunai');
            $table->foreignId('akun_keuangan_id')->nullable()->constrained('akun_keuangans')->onDelete('set null');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null');
            $table->string('nomor_bukti_bayar', 100)->nullable();
            $table->string('bukti_bayar', 255)->nullable();
            $table->enum('status', ['belum_bayar', 'lunas', 'terlambat'])->default('belum_bayar');
            $table->text('catatan')->nullable();
            $table->foreignId('diterima_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsuran_pinjamans');
        Schema::dropIfExists('jaminan_pinjamans');
        Schema::dropIfExists('pinjamans');
        Schema::dropIfExists('nasabah_pinjamans');
        Schema::dropIfExists('transaksi_keuangans');
        Schema::dropIfExists('kategori_keuangans');
        Schema::dropIfExists('banks');
        Schema::dropIfExists('akun_keuangans');
    }
};
