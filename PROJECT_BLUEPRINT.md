# 🏛️ MDT HIDAYATUS SHIBYAN - MASTER PROJECT BLUEPRINT & GUIDELINES

> **Dokumen Panduan Arsitektur, Desain Sistem, dan Standar Alur Kerja**  
> *Versi 2.0 — Terakhir Diperbarui: September 2026*  
> *Dokumen ini adalah single-source-of-truth (SSOT) agar implementasi fitur, styling, tata letak UI, dan komunikasi API tetap konsisten.*

---

## 📂 1. STRUKTUR WORKSPACE & REPOSITORI

```text
D:\laragon\www\mdt_hidayatus_shibyan\
│
├── 🖥️ backend\                  # Laravel 11+ REST API & Admin Web Panel
│   ├── app\Http\Controllers\Api\ # REST API Controllers (Mobile Endpoints)
│   ├── app\Models\              # Eloquent Models & Business Logic
│   ├── routes\api.php           # Definisi Rute API Mobile
│   └── database\migrations\     # Struktur Skema Database
│
└── 📱 frontend\
    ├── 🧑‍🏫 app_ustadz\           # Flutter Mobile App: Khusus Ustadz & Wali Ruangan
    │   ├── lib\core\theme\      # Token Warna, Tipografi, Glassmorphism
    │   ├── lib\data\models\     # Data Models & Image Resolvers
    │   ├── lib\providers\       # State Management (Provider/ChangeNotifier)
    │   └── lib\ui\tabs\         # Layar & Bottom Navigation Tabs
    │
    └── 🎓 app_murid\            # Flutter Mobile App: Khusus Murid / Santri & Wali Murid
        ├── lib\core\theme\      # Tema Konsisten Berbasis Brand MDT
        ├── lib\data\models\     # Model Monitoring Tagihan, Nilai, & Presensi
        ├── lib\providers\       # State Management Provider
        └── lib\ui\              # Dashboard Wali/Murid, Rapor, Kartu SPP, dll.
```

---

## 🎨 2. DESIGN SYSTEM & ATURAN UI/UX

### 2.1. Standar Brand & Palette Warna
| Token Name | Light Theme | Dark Theme (AMOLED) | Kegunaan |
| :--- | :--- | :--- | :--- |
| `primary` | `#146C2E` (Forest Green) | `#3BC05B` (Bright Emerald) | Brand Utama, Tombol Utama, Active Tab |
| `primaryContainer` | `#DCFCE7` (Emerald 100) | `#00531E` (Emerald 950) | Background Badge, Highlight Card |
| `surface` | `#FAF9F6` (Warm Canvas) | `#000000` (True Black OLED) | Background Scaffold Utama |
| `cardGlass` | `rgba(255,255,255,0.85)` | `rgba(24,31,24,0.65)` | Kartu Glassmorphism & Kontainer |
| `outline` | `#E4E4E7` (Zinc 200) | `#272F27` (Greenish Zinc) | Border Garis Tipis & Divider |
| `amberAccent` | `#F59E0B` | `#FBBF24` | Poin Pelanggaran, Ranking, Warning |
| `skyBlueAccent` | `#0284C7` | `#38BDF8` | Kalender Pendidikan, Pengumuman |
| `violetAccent` | `#7C3AED` | `#A78BFA` | Leger Nilai, Ujian, Kenaikan Kelas |
| `roseDanger` | `#DC2626` | `#F87171` | Hapus Data, Status Alpha, Tunggakan |

### 2.2. Standar Presensi Status Badge
- **Hadir (H)**: Hijau (`#15803D` text / `#DCFCE7` bg)
- **Izin (I)**: Biru (`#1D4ED8` text / `#DBEAFE` bg)
- **Sakit (S)**: Kuning Amber (`#B45309` text / `#FEF3C7` bg)
- **Alpha (A)**: Merah (`#B91C1C` text / `#FEE2E2` bg)
- **Dispensasi (D)**: Ungu (`#6D28D9` text / `#EDE9FE` bg)

### 2.3. Glassmorphism & Micro-Interactions
- Radius kartu: **24px - 32px**.
- Border stroke: **1px** solid `outlineLight` / `outlineDark`.
- Haptic Feedback: Panggil `HapticHelper.light()` atau `HapticFeedback.lightImpact()` pada setiap aksi klik tombol penting, pull-to-refresh, dan bottom sheet trigger.
- Full Edge-to-Edge System Bar (Android 15+ compatible) dengan `transparent` statusBar & navigationBar.

---

## 🔤 3. STANDAR TERMINOLOGI RESMI (GLOSSARY)

Seluruh antarmuka mobile **WAJIB** mematuhi terminologi baku berikut:
1. **"Murid"** (Bukan "Santri" di UI Mobile) ➔ contoh: *Direktori Murid*, *Kas Murid*, *Murid Binaan*.
2. **"Ruangan"** (Bukan "Kelas") ➔ contoh: *Kas Ruangan*, *Ruangan 1 Ula*, *Wali Ruangan*.
3. **"Wali Ruangan"** (Bukan "Wali Kelas").
4. **"NIGM"** (Nomor Induk Guru Madin) ➔ Read-only untuk ustadz.
5. **"NISM"** (Nomor Induk Santri Madin) ➔ Nomor identitas murid.
6. **"11 Bulan Hijriyah"** ➔ Standar perhitungan SPP tahunan di madrasah (Syawwal s.d. Sya'ban).

---

## 🌐 4. ATURAN PENANGANAN URL GAMBAR & JARINGAN

Untuk mencegah error `HTTP statusCode: 0` pada perangkat fisik dan emulator:
- **Backend**: Menyimpan file di `storage/app/public/...` dan dapat diakses via `/storage/...`.
- **Frontend Resolver**: Gunakan `ApiConstants.formatImageUrl(url)` atau `ApiClient.resolveImageUrl(url)`.
- Resolver ini secara otomatis mendeteksi URL yang mengandung `localhost` / `127.0.0.1` atau relative path `/storage/...` dan menggantinya dengan Host/IP API yang aktif digunakan aplikasi.
- Selalu sediakan **Avatar Initial** (huruf pertama nama) sebagai `child` dari `CircleAvatar` agar tetap ada visual fallback jika foto gagal dimuat.

---

## 📑 5. SPESIFIKASI MODUL & FITUR

### 🧑‍🏫 A. APLIKASI USTADZ (`app_ustadz`)
1. **Dashboard Home**: Jadwal Mengajar Hari Ini, Presensi Cepat, Statistik Murid Binaan, Ringkasan Kas & SPP Ruangan.
2. **Presensi**:
   - Presensi Murid per Sesi Mengajar (H/I/S/A/D dengan catatan).
   - Presensi Masuk & Pulang Ustadz.
3. **Kas Ruangan**:
   - **Tab Bayar**: Catat iuran murid dengan opsi Nominal Cepat & custom amount.
   - **Tab Setor**: Form setor kas ke Bendahara/Pimpinan, upload bukti transfer, dan riwayat status setoran.
4. **Tagihan**:
   - **Tagihan SPP**: Monitoring 11 Bulan Hijriyah (Lunas, Belum Lunas, Bebas/Gratis).
   - **Tagihan Non-SPP**: Monitoring tagihan insidental (Pembangunan, Kitab, Seragam), fitur Bayar & Batal Bayar.
5. **Kedisiplinan / Pelanggaran**:
   - Catat pelanggaran per murid / massal dengan poin otomatis dari referensi.
   - Histori poin akumulasi murid.
6. **Penilaian & Akademik**:
   - Input nilai per mata pelajaran & ujian (UTS/UAS).
   - Leger Nilai Ruangan.
7. **Pusat Laporan**:
   - Rekap Presensi Murid & Ustadz.
   - Rekap Pelanggaran Murid.
   - Laporan Hasil Ujian & Kenaikan Kelas (Rekomendasi & Keputusan Final).
8. **Akun & Profil**:
   - Biodata Ustadz (NIGM bersifat Read-Only).
   - Ubah Foto Profil & Tanda Tangan Digital.
   - Ganti Password & Konfigurasi Server IP.

---

### 🎓 B. APLIKASI MURID & WALI MURID (`app_murid`)
1. **Autentikasi**:
   - Login menggunakan **NISM + Tanggal Lahir / PIN / Password / No HP**.
2. **Dashboard Murid / Wali**:
   - Header Profil Murid, Ruangan, & Wali Ruangan.
   - Kartu Indikator Cepat: Persentase Kehadiran, Status SPP Bulan Berjalan, Poin Kedisiplinan.
   - Pengumuman Madrasah & Kalender Pendidikan.
3. **Menu Monitoring SPP & Keuangan**:
   - **Kartu SPP Digital**: Visualisasi 11 Bulan Hijriyah dengan status hijau (Lunas), abu-abu (Belum), biru (Bebas).
   - **Rincian Tagihan Non-SPP**: Rincian tagihan kitab, ujian, daftar ulang, beserta nomor kwitansi pembayaran.
   - **Riwayat Iuran Kas Ruangan**.
4. **Menu Kehadiran (Presensi)**:
   - Kalender Presensi Murid harian.
   - Statistik Total Hadir, Izin, Sakit, Alpha.
   - Pengajuan Izin/Sakit online oleh Wali Murid (dengan lampiran surat/foto).
5. **Menu Kedisiplinan**:
   - Catatan rekam jejak pelanggaran & total poin.
   - Kategori kedisiplinan (Sangat Baik / Baik / Perlu Pembinaan).
6. **Menu Akademik & Rapor**:
   - Jadwal Pelajaran Mingguan & Ustadz Pengampu.
   - Rapor Digital Ujian (Nilai per Mapel, Rata-rata, Peringkat Ruangan).
   - Status Kenaikan Kelas & Rekomendasi Wali Ruangan.
7. **Profil & Bantuan**:
   - Kartu Pelajar Digital (QR Code NISM).
   - Data Pribadi & Kontak Madrasah.

---

## 🔌 6. STANDAR FORMAT RESPONSE API (LARAVEL)

Semua endpoint API Laravel wajib mengembalikan JSON berformat standar:

### Success Response:
```json
{
  "success": true,
  "message": "Data berhasil dimuat.",
  "data": {
    ...
  }
}
```

### Error Response:
```json
{
  "success": false,
  "message": "Kredensial tidak cocok.",
  "errors": {
    "nism": ["NISM wajib diisi."]
  }
}
```
