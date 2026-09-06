# 📱 SPESIFIKASI & PANDUAN PENGEMBANGAN APLIKASI WALI MURID (WALI MDTHS)

> **MDT Hidayatus Shibyan — Portal Monitoring Murid & Wali Murid**

---

## 📌 1. RINGKASAN EKSEKUTIF & TUJUAN

Aplikasi Mobile **Wali MDTHS** dirancang khusus untuk memfasilitasi para **Wali Murid / Orang Tua Murid** MDT Hidayatus Shibyan dalam memantau perkembangan akademik, kehadiran, kedisiplinan, dan administrasi keuangan Murid secara mudah, transparan, dan real-time.

### 🎯 Fitur Unggulan:

1. **Multi-Child Switcher (Satu Akun untuk Semua Anak)**: Wali murid yang memiliki lebih dari satu anak dapat beralih antar anak secara instan dalam 1 aplikasi.
2. **Kartu SPP Digital 11 Bulan Hijriyah**: Status pembayaran SPP Syahriyah dari bulan Syawwal hingga Sya'ban terpantau secara transparan lengkap dengan riwayat pembayaran dan kwitansi digital.
3. **Monitoring Presensi & Izin Sakit**: Rekapitulasi kehadiran harian serta perizinan langsung ke wali kelas / pengurus.
4. **Rapor Nilai Ujian & Status Kenaikan Kelas**: Rekap nilai IMDA 1, IMDA 2, IMNI, ranking kelas, dan catatan kenaikan kelas.
5. **Buku Kasus & Catatan Kedisiplinan**: Transparansi catatan pelanggaran dan akumulasi poin Murid.

---

## 🎨 2. DESAIN SISTEM (MATERIAL DESIGN 3 EXPRESSIVE)

- **Primary Color**: `#146C2E` (Madrasah Forest Green) / `#3BC05B` (Bright Pixel Emerald).
- **Surface Background**: `#FAF9F6` (Warm Clean Canvas) & `#000000` (Super AMOLED True Black).
- **Aesthetic**: Modern Glassmorphism (`GlassCard`, rounded radius 24px-32px, 1px soft border stroke).
- **Haptic Feedback**: Integrasi `HapticHelper` untuk setiap aksi penting.
- **Ergonomi**: Floating Segmented Pill Tab Bar di bagian bawah layar.

---

## 📱 3. STRUKTUR 5 TAB NAVIGASI UTAMA

### 1. 🏠 Tab Beranda (Home)

- **Header Wali**: Menampilkan nama Kepala Keluarga & No. Registrasi Keluarga.
- **Multi-Child Switcher**: Carousel/Selector memilih Murid aktif yang dipantau.
- **Kartu Profil Murid**: Foto Murid, NISM, Ruangan kelas aktif, & Nama Ustadz Wali Ruangan.
- **Status Kehadiran Hari Ini**: Badge status real-time (_Hadir_, _Sakit_, _Izin_, _Belum Ada Sesi_).
- **Ringkasan Tagihan & Keuangan**: Total tagihan, total terbayar, dan sisa tunggakan.
- **Menu Akses Cepat**: Kartu SPP, Rapor Nilai, Izin Sakit, Jadwal Pelajaran.
- **Pengumuman Madrasah**: Banner & pengumuman terkini dari pihak madrasah.

### 2. 💳 Tab Keuangan & SPP

- **Sub-Tab 1: Kartu SPP Digital**:
  - Grid 11 Bulan Hijriyah (Syawal, Dzulqa'dah, Dzulhijjah, Muharram, Safar, Rabi'ul Awwal, Rabi'ul Akhir, Jumadil Awwal, Jumadil Akhir, Rajab, Sya'ban).
  - Status per bulan: **Lunas** (Hijau + tgl bayar & no. kwitansi), **Belum Lunas** (Abu-abu), **Bebas/Gratis** (Biru).
- **Sub-Tab 2: Tagihan Non-SPP**:
  - Tagihan berkala/insidental (IMDA, Kitab, Seragam, Pembangunan).
- **Sub-Tab 3: Riwayat Pembayaran**:
  - List seluruh riwayat transaksi kwitansi yang tercatat di bendahara.

### 3. 📅 Tab Presensi (Kehadiran)

- **Donut Chart & Statistik**: Total hadir, sakit, izin, alpha, dan dispensasi.
- **Persentase Kehadiran**: Persentase kehadiran Murid di semester aktif.
- **Riwayat Presensi Harian**: Daftar kehadiran per tanggal dan mata pelajaran.
- **Permohonan Izin / Sakit**: Akses cepat kirim format surat izin via WhatsApp ke wali ruangan.

### 4. 📊 Tab Akademik & Nilai

- **Rapor Nilai Ujian**: Pilihan ujian (IMDA 1, IMDA 2, IMNI).
- **Tabel Nilai**: Nilai per mapel, KKM, Nilai Angka, Nilai Huruf, dan Keterangan.
- **Status Kenaikan Kelas**: Keputusan kenaikan kelas, ranking di ruangan, dan pesan wali kelas.
- **Jadwal Pelajaran**: Jadwal mapel mingguan Murid (Sabtu s.d. Kamis).

### 5. 👤 Tab Akun & Informasi

- **Biodata Murid & Keluarga**: Data NISM, NISN, NIK, Tempat/Tgl Lahir, Nama Ayah, Nama Ibu, Kampung.
- **Buku Pelanggaran**: Riwayat pelanggaran Murid beserta tanggal, kasus, dan bobot poin.
- **Layanan Kontak Madrasah**: Hubungi Admin / Bantuan resmi madrasah.
- **Pengaturan**: Ganti Alamat Server API, Toggle Dark Mode AMOLED, dan Logout.

---

## 🔐 4. ALUR AUTENTIKASI (LOGIN WALI)

1. **Input**: **No. Kartu Keluarga (KK)**, **No. Registrasi Wali**, atau **NISM Murid**.
2. **Endpoint**: `POST /api/wali/login`
3. **Response**: Sanctum Bearer Token + Objek Wali + Daftar Anak Aktif.
4. **Penyimpanan**: Disimpan di `StorageService` (SharedPreferences).
