# 🧑‍🏫 SPESIFIKASI & ARSITEKTUR APLIKASI USTADZ (APP_USTADZ)
> **MDT Hidayatus Shibyan — Portal Pengajar & Wali Ruangan**

---

## 🧭 1. IKHTISAR SISTEM
Aplikasi mobile Flutter khusus untuk Ustadz, Ustadzah, dan Wali Ruangan MDT Hidayatus Shibyan.
- Arsitektur: **Provider (MVVM Architecture)**
- Theme: **Material 3 Expressive + OLED Super AMOLED True Black**
- Terminologi Baku: **Murid**, **Ruangan**, **Wali Ruangan**, **NIGM**.

---

## 📱 2. MODUL UTAMA APLIKASI

### 1. Home Tab (`lib/ui/tabs/home/home_tab.dart`)
- **Header Profile**: Foto, Nama Ustadz, NIGM, Jabatan (Wali Ruangan / Guru Mapel).
- **Statistik Cepat**: Total Murid Binaan (Putra/Putri), Sesi Mengajar Hari Ini, Saldo Kas Ruangan, Tagihan Belum Lunas.
- **Jadwal Mengajar**: Sesi mengajar hari ini dengan aksi cepat tombol "Mulai Presensi".
- **Kalender Pendidikan & Pengumuman**.

### 2. Presensi Tab (`lib/ui/tabs/presensi/presensi_tab.dart`)
- **Presensi Murid**:
  - Pilihan Ruangan & Mata Pelajaran.
  - Form checklist status (H, I, S, A, D) per murid dengan field keterangan/catatan.
  - Tombol "Set Semua Hadir" untuk kecepatan input.
- **Presensi Ustadz**:
  - Tombol Check-in & Check-out ustadz dengan foto & geolokasi / validasi sesi.
  - Riwayat presensi ustadz bulanan.

### 3. Kas Ruangan Screen (`lib/ui/tabs/kas/kas_ruangan_screen.dart`)
- **Sub-Tab 1: Bayar (Catat Iuran)**:
  - Daftar Murid dengan status kas, total target, total bayar, sisa.
  - Modal Catat Bayar Kas: Pilihan nominal cepat (Rp 2.000, Rp 5.000, Rp 10.000, dll) atau custom amount.
  - Riwayat pembayaran iuran per murid (dengan opsi batalkan transaksi jika belum disetor).
- **Sub-Tab 2: Setor (Setoran ke Bendahara)**:
  - Form Setor Kas: Pilih penerima (Bendahara / Kepala Madrasah), nominal setor, metode (Tunai/Transfer), upload foto bukti transfer, catatan.
  - Riwayat setoran kas dengan badge status (**Menunggu Konfirmasi**, **Diterima**, **Ditolak**).

### 4. Tagihan Screen (`lib/ui/tabs/tagihan/tagihan_screen.dart`)
- **Sub-Tab 1: Tagihan SPP**:
  - Monitoring 11 Bulan Hijriyah per murid.
  - Kartu SPP Digital popup untuk melihat rincian bulan lunas / nunggak / gratis.
- **Sub-Tab 2: Tagihan Non-SPP**:
  - Filter jenis tagihan (Kitab, Pembangunan, Ujian, dll).
  - List status murid (Lunas / Belum Lunas).
  - Modal Bayar Tagihan Non-SPP (Input tanggal, metode bayar, tipe pembayar, no kwitansi otomatis).
  - Fitur Batal Bayar dengan konfirmasi dialog.
  - Fitur Pembayaran Massal (Centang beberapa murid sekaligus untuk tagihan yang sama).

### 5. Pelanggaran Tab (`lib/ui/tabs/pelanggaran/`)
- **Sub-Tab 1: Harian**: Log catatan pelanggaran hari ini.
- **Sub-Tab 2: Ruangan**: Daftar murid per ruangan dan total poin akumulasi.
- **Sub-Tab 3: Massal**: Catat satu jenis pelanggaran untuk banyak murid sekaligus.
- **Modal Catat Pelanggaran**: Pilih murid, pilih jenis pelanggaran dari master referensi (poin otomatis terisi), tanggal, saksi, tindakan/hukuman.

### 6. Pusat Laporan Screen (`lib/ui/tabs/laporan/pusat_laporan_screen.dart`)
- **Laporan 1: Presensi Murid** (Filter Ruangan & Bulan Hijriyah, persentase kehadiran, predikat).
- **Laporan 2: Presensi Ustadz** (Filter Ustadz & Tahun Pelajaran, total sesi, total hadir, terlambat).
- **Laporan 3: Pelanggaran Murid** (Filter Ruangan & Status Disiplin, total kasus & total poin).
- **Laporan 4: Hasil Ujian** (Filter Ruangan, Semester, Ujian; nilai total, rata-rata, ranking, status tuntas).
- **Laporan 5: Kenaikan Kelas** (Filter Ruangan; skor Sem 1, Sem 2, nilai akumulasi, rekomendasi & keputusan final).

### 7. Akun Tab (`lib/ui/tabs/akun/akun_tab.dart`)
- **Profil Ustadz**:
  - Hero Profile Avatar (menggunakan `ApiConstants.formatImageUrl` dan initial fallback).
  - Edit Foto Profil via Galeri/Kamera.
  - Edit Tanda Tangan Digital via Signature Canvas.
- **Edit Biodata**:
  - **NIGM** (Read-Only dengan ikon gembok).
  - Nama, NIK, Jenis Kelamin, Tempat/Tgl Lahir, Alamat, No HP.
- **Pengaturan & Keamanan**:
  - Ubah Password.
  - Konfigurasi Alamat Server API (mendukung IP LAN / Domain).
  - Toggle Mode Gelap (OLED True Black).
  - Logout.
