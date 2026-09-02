# 🎓 SPESIFIKASI PENGEMBANGAN APLIKASI MURID (APP_MURID)
> **MDT Hidayatus Shibyan — Portal Monitoring Murid & Wali Murid**

---

## 🎯 1. TUJUAN APLIKASI
Memberikan akses transparansi bagi Murid dan Wali Murid dalam memantau:
1. Kehadiran / Presensi harian dan pengajuan izin.
2. Status pembayaran SPP (11 Bulan Hijriyah) dan Tagihan Non-SPP secara real-time.
3. Rekapitulasi poin kedisiplinan dan catatan pelanggaran.
4. Nilai ujian, peringkat, rapor digital, dan status kenaikan kelas.
5. Jadwal pelajaran, kalender pendidikan, dan pengumuman madrasah.

---

## 🎨 2. TEMA & DESAIN SISTEM
- Mengikuti Design System di `PROJECT_BLUEPRINT.md`:
  - **Primary**: Hijau Hutan Madrasah (`#146C2E` Light / `#3BC05B` OLED Dark).
  - **Surface**: Warm Canvas (`#FAF9F6`) & Super AMOLED True Black (`#000000`).
  - **Style**: Modern Glassmorphism (`GlassCard`, rounded radius 24px-32px, border stroke 1px).
  - **Terminologi**: Gunakan kata **"Murid"**, **"Ruangan"**, **"Wali Ruangan"**, **"NISM"**.

---

## 📱 3. STRUKTUR MENU & FITUR (BOTTOM NAVIGATION)

### 1. Tab Home (Beranda)
- **Header Profile Card**: Foto Murid, Nama Lengkap, NISM, Ruangan, & Nama Wali Ruangan.
- **Kartu Ringkasan Cepat**:
  - Kehadiran (% Hadir semester berjalan).
  - Status SPP (Lunas / Menunggak bulan berjalan).
  - Poin Disiplin (Total poin pelanggaran).
- **Jadwal Pelajaran Hari Ini**: Daftar mapel, waktu, dan Ustadz pengampu.
- **Pengumuman Madrasah Terkini**: Card pengumuman penting dari pihak madrasah.
- **Quick Action Buttons**: Kartu SPP, Rapor Digital, Ajukan Izin, Jadwal Lengkap.

### 2. Tab Keuangan (SPP & Tagihan)
- **Sub-Tab 1: Kartu SPP Digital**:
  - Ringkasan total target, total dibayar, dan sisa tunggakan SPP.
  - Grid 11 Bulan Hijriyah (Syawwal, Dzulqa'dah, Dzulhijjah, Muharram, Safar, Rabi'ul Awwal, Rabi'ul Akhir, Jumadil Awwal, Jumadil Akhir, Rajab, Sya'ban).
  - Status per bulan: **Lunas** (Hijau dengan tanggal bayar), **Belum Lunas** (Abu-abu), **Bebas/Gratis** (Biru).
- **Sub-Tab 2: Tagihan Non-SPP**:
  - Daftar tagihan berkala (Pembangunan, Kitab, Seragam, Ujian, dll).
  - Status bayar, nominal, nomor kwitansi, tanggal transaksi, dan catatan.
- **Sub-Tab 3: Iuran Kas Ruangan**:
  - Riwayat pembayaran kas mingguan/bulanan ke wali ruangan.

### 3. Tab Presensi (Kehadiran)
- **Statistik Kehadiran**: Donut chart / Ringkasan Hadir, Izin, Sakit, Alpha, Dispensasi.
- **Kalender Presensi**: Tampilan kalender dengan penanda warna status per hari.
- **Tombol Ajukan Izin/Sakit**:
  - Form tanggal izin, alasan izin, dan upload foto surat dokter / keterangan wali.

### 4. Tab Akademik & Nilai
- **Jadwal Pelajaran**: Tampilan jadwal per hari (Sabtu s.d. Kamis).
- **Rapor Nilai Ujian**:
  - Pemilihan Semester / Ujian (UTS Ganjil, UAS Ganjil, UTS Genap, UAS Genap).
  - Tabel nilai per mata pelajaran, nilai huruf, KKTP, rata-rata, dan peringkat ruangan.
- **Status Kenaikan Kelas**:
  - Informasi akumulasi nilai, status kenaikan (Naik ke Ruangan X / Tinggal), dan catatan wali ruangan.

### 5. Tab Akun / Profil
- **Kartu Identitas Digital**: Kartu pelajar dengan barcode / QR code NISM.
- **Biodata Murid & Orang Tua**: NISM, NISN, NIK, Tempat/Tgl Lahir, Nama Ayah, Nama Ibu, Alamat.
- **Riwayat Catatan Pelanggaran**: List pelanggaran yang pernah tercatat beserta tanggal & poin.
- **Pengaturan Akun**: Ganti Password, Mode Gelap/Terang, Ganti Alamat Server API, Logout.

---

## 🔐 4. ALUR AUTENTIKASI (LOGIN MURID)
1. Input: **NISM** dan **Password / Tanggal Lahir (Format: DDMMYYYY)**.
2. Endpoint: `POST /api/murid/login`
3. Response mengembalikan Bearer Token Sanctum + Objek Data Murid.
4. Token disimpan aman di `StorageService` (SharedPreferences / FlutterSecureStorage).

---

## 🛠️ 5. STATE MANAGEMENT & ARSITEKTUR
- Pattern: **Provider (MVVM Architecture)**
- `lib/core/` (Network, Constants, Theme, Storage, Utils)
- `lib/data/` (Models, Repositories, Services)
- `lib/providers/` (AuthProvider, DashboardProvider, TagihanProvider, PresensiProvider, NilaiProvider)
- `lib/ui/` (Widgets, Common, Tabs, Sheets)
