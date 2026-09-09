# 🎓 MDT HIDAYATUS SHIBYAN — FRONTEND APP MURID & WALI GUIDELINES

> **Dokumen Panduan Tunggal (Single Source of Truth) Aplikasi Mobile Flutter Murid & Wali Murid (Wali MDTHS)**  
> _Lokasi File: Root Workspace (`/FRONTEND_APP_MURID.md`)_  
> _Target Project: `frontend/app_murid` (`lib/`)_  
> _Tujuan: Standar konsistensi arsitektur, multi-child state management, desain sistem M3 Expressive, token warna, monitoring keuangan 11 bulan hijriyah, dan alur integrasi API._

---

## 📑 DAFTAR ISI

1. [Tech Stack & Arsitektur Aplikasi](#1-tech-stack--arsitektur-aplikasi)
2. [Standar Terminologi Resmi (Glossary)](#2-standar-terminologi-resmi-glossary)
3. [Fitur Unggulan: Multi-Child Switcher](#3-fitur-unggulan-multi-child-switcher)
4. [Design System: Material 3 Expressive (Google Pixel Edition)](#4-design-system-material-3-expressive-google-pixel-edition)
5. [Spesifikasi 5 Tab Navigasi & Seluruh Modul Layar](#5-spesifikasi-5-tab-navigasi--seluruh-modul-layar)
6. [Alur Autentikasi & Keamanan Sesi Wali](#6-alur-autentikasi--keamanan-sesi-wali)
7. [Penanganan Jaringan, Storage & Image URL Resolver](#7-penanganan-jaringan-storage--image-url-resolver)
8. [SOP Langkah Demi Langkah Menambah Layar / Fitur Baru](#8-sop-langkah-demi-langkah-menambah-layar--fitur-baru)
9. [Aturan Mutlak Redesign UI (Anti-Break SOP)](#9-aturan-mutlak-redesign-ui-anti-break-sop)

---

## 1. TECH STACK & ARSITEKTUR APLIKASI

- **Framework:** Flutter 3.x+ (Dart SDK 3.x+)
- **Arsitektur State Management:** MVVM (Model-View-ViewModel) berbasis `Provider` (`ChangeNotifierProvider`, `Consumer`).
- **HTTP Client:** `Dio` dengan Interceptor Bearer Token & Auto Logout on 401.
- **Penyimpanan Lokal:** `shared_preferences` (Token, Active Child ID, Saved Settings, AMOLED Theme).
- **Tipografi:** Google Fonts (`Plus Jakarta Sans` / `Google Sans Flex` untuk teks Latin, `Amiri` untuk teks Arab).
- **Format Pertukaran Data:** RESTful JSON API via backend Laravel.

### 📂 Struktur Direktori Standar (`frontend/app_murid/lib/`):

```text
lib/
├── core/
│   ├── constants/        # ApiEndpoints, AppConstants
│   ├── network/          # ApiClient, ApiInterceptors, Error Handling
│   ├── storage/          # StorageService (Token, Session, Selected Child)
│   ├── theme/            # AppColors, AppTheme, AppTypography
│   └── utils/            # HapticHelper, DateFormatter, CurrencyFormatter
├── data/
│   ├── models/           # AnakModel, TagihanModel, PresensiModel, NilaiModel, dll.
│   └── repositories/     # WaliRepository, AuthRepository
├── providers/            # AuthProvider, DashboardProvider, KeuanganProvider, dll.
├── ui/
│   ├── auth/             # LoginScreen, SplashScreen, GantiPinAwalScreen
│   ├── main/             # MainScreen (Bottom Navigation Shell 5 Tab)
│   ├── tabs/             # 5 Tab Navigasi Utama
│   │   ├── home/         # HomeTab (Dashboard Monitoring)
│   │   ├── keuangan/     # KeuanganTab & TagihanTab (SPP & Non-SPP)
│   │   ├── presensi/     # PresensiTab (Statistik Kehadiran & Izin)
│   │   ├── akademik/     # AkademikTab (Rapor, Jadwal, Kenaikan Kelas)
│   │   ├── akun/         # AkunTab, BiodataAnakScreen, BukuKasusScreen
│   │   ├── tabungan/     # TabunganScreen (Buku Tabungan Murid)
│   │   └── koperasi/     # KoperasiScreen (Mutasi Transaksi Toko)
│   └── widgets/          # ChildSwitcherBar, GlassCard, EmptyState, Shimmer
└── main.dart
```

---

## 2. STANDAR TERMINOLOGI RESMI (GLOSSARY)

Seluruh antarmuka, string pesan, dan dokumentasi di `app_murid` **WAJIB** menggunakan:

1. **"Murid"** ➔ (Bukan "Santri"). Contoh: _Data Murid_, _Murid Aktif_, _Kartu Pelajar Murid_.
2. **"Ruangan"** ➔ (Bukan "Kelas"). Contoh: _Ruangan 2 Ula B_, _Wali Ruangan_.
3. **"Wali Ruangan"** ➔ (Bukan "Wali Kelas").
4. **"NISM"** ➔ Nomor Induk Santri Madin (Nomor identitas resmi murid).
5. **"11 Bulan Hijriyah"** ➔ Siklus SPP tahunan madrasah (Syawwal s.d. Sya'ban).
6. **"Wali Murid / Orang Tua"** ➔ Pengguna utama akun keluarga.

---

## 3. FITUR UNGGULAN: MULTI-CHILD SWITCHER

Aplikasi mendukung **Multi-Child Switcher** dalam 1 akun keluarga:

- Wali murid yang memiliki lebih dari 1 anak di madrasah dapat berganti profil anak secara instan tanpa perlu logout.
- Komponen `ChildSwitcherBar` (`lib/ui/widgets/child_switcher_bar.dart`) melayang di bagian atas layar dashboard.
- Ketika anak aktif berganti (`selectedAnakId`), seluruh provider (_Dashboard, Keuangan, Presensi, Akademik, Tabungan, Koperasi_) otomatis memuat ulang data sesuai ID anak yang dipilih.

---

## 4. DESIGN SYSTEM: MATERIAL 3 EXPRESSIVE (GOOGLE PIXEL EDITION)

Aplikasi mengadopsi estetika **Material 3 Expressive** bernuansa Google Pixel yang ramah, jelas, dan modern:

### 4.1. Token Palette Warna

| Token Name           | Light Mode                    | Super AMOLED Dark Mode             | Peran Visual                                    |
| :------------------- | :---------------------------- | :--------------------------------- | :---------------------------------------------- |
| `primary`            | `#146C2E` (Forest Green)      | `#3BC05B` (Bright Pixel Emerald)   | Brand Utama, Tombol Primer, Active Indicator    |
| `primaryContainer`   | `#DCFCE7` (Emerald 100)       | `#00531E` (Emerald 950 Deep)       | Background Badge, Highlight Card, Selected Chip |
| `onPrimaryContainer` | `#14532D`                     | `#A6FBAA`                          | Teks di dalam Primary Container                 |
| `surface`            | `#FAF9F6` (Warm Clean Canvas) | `#000000` (Pure AMOLED True Black) | Kanvas Latar Belakang Utama                     |
| `surfaceCard`        | `rgba(255,255,255,0.85)`      | `rgba(24,31,24,0.70)` / `#111611`  | Kartu Glassmorphism                             |
| `outline`            | `#E4E4E7` (Zinc 200)          | `#272F27` / `#222B22`              | Border Garis Tipis 1px Kontainer                |
| `amberAccent`        | `#F59E0B`                     | `#FBBF24`                          | Poin Pelanggaran, Ranking, Warning              |
| `skyBlueAccent`      | `#0284C7`                     | `#38BDF8`                          | Kalender Pendidikan, Pengumuman                 |
| `violetAccent`       | `#7C3AED`                     | `#A78BFA`                          | Leger Nilai, Rapor, Kenaikan Kelas              |
| `roseDanger`         | `#DC2626`                     | `#F87171`                          | Tunggakan Tagihan, Alpha, Poin Berat            |

### 4.2. Status Badge Presensi & Keuangan Semantik

- **Lunas / Hadir:** Hijau (`#15803D` / `#DCFCE7`)
- **Belum Lunas / Izin:** Biru (`#1D4ED8` / `#DBEAFE`)
- **Sakit / Peringatan:** Kuning Amber (`#B45309` / `#FEF3C7`)
- **Tunggakan / Alpha:** Merah Crimson (`#B91C1C` / `#FEE2E2`)
- **Bebas / Gratis / Dispensasi:** Ungu (`#6D28D9` / `#EDE9FE`)

### 4.3. Bentuk & Komponen Fisik (Expressive Shapes)

- **Glass Card:** `BorderRadius.circular(24.0)` dengan border tipis `1px` solid outline color.
- **Modal / Bottom Sheet:** `BorderRadius.vertical(top: Radius.circular(32.0))` dengan Pill Drag Handle.
- **Floating Bottom Bar:** Segmented Pill Dock melayang di atas konten dengan efek blur kaca.
- **Haptic Feedback:** Panggil `HapticHelper.light()` saat berganti anak, tap kartu tagihan, atau pull-to-refresh.

### 4.4. Mode Tema: Default Light Mode & Theme Persistence

- **Mode Default:** Aplikasi berjalan dalam **Mode Terang (Light Mode / `ThemeMode.light`)** secara default saat pertama kali diinstal/dibuka.
- **Opsi Tema:** Pengguna dapat beralih tema secara fleksibel pada menu **Akun ➔ Pengaturan & Bantuan ➔ Tema Tampilan**:
  1. **Mode Terang (Default):** Tampilan cerah, bersih, kanvas _Warm Clean Canvas_ (`#FAF9F6`), dan kartu kontras tinggi.
  2. **Mode Gelap (Super AMOLED):** Latar hitam murni (`#000000`) dengan kartu deep glass (`#101710` / `#182218`) untuk hemat daya baterai.
  3. **Ikuti Sistem:** Mengikuti konfigurasi tema terang/gelap pada OS perangkat pengguna.
- **Penyimpanan:** Preferensi mode tema disimpan secara permanen di `StorageService` (`shared_preferences`) menggunakan key `theme_mode`.

---

## 5. SPESIFIKASI 5 TAB NAVIGASI & SELURUH MODUL LAYAR

### 1. 🏠 Tab Beranda (`HomeTab`)

- **Header:** Nama Kepala Keluarga, No. Registrasi Wali, dan Notifikasi.
- **Child Switcher Bar:** Selector anak aktif dengan avatar, nama panggilan, dan badge ruangan.
- **Hero Card Profil Anak:** Foto Anak, NISM, Ruangan Kelas Aktif, dan Nama Ustadz Wali Ruangan.
- **Status Kehadiran Hari Ini:** Badge status real-time (_Hadir_, _Sakit_, _Izin_, _Belum Ada Sesi_).
- **Indikator Keuangan Ringkas:** Total Tagihan, Terbayar, Sisa Tunggakan, dan Saldo Tabungan.
- **Akses Cepat 6-Grid:**
  1. **Kartu SPP:** Monitoring SPP 11 bulan hijriyah.
  2. **Tabungan:** Buku tabungan dan mutasi saldo murid.
  3. **Koperasi:** Riwayat belanja di POS koperasi madrasah.
  4. **Presensi:** Statistik dan log absensi harian.
  5. **Rapor Nilai:** Leger nilai IMDA / IMNI & arsip dokumen rapor.
  6. **Buku Kasus:** Rekap poin sanksi dan catatan kedisiplinan murid lengkap dengan indikator badge poin real-time.
- **Jadwal Pelajaran Hari Ini / Jadwal Ujian:**
  - **Mode Ujian:** Tampil otomatis jika hari ini ada jadwal ujian madrasah (IMDA 1, IMDA 2, IMNI) lengkap dengan mata pelajaran ujian, waktu, dan nama ustadz pengawas.
  - **Mode Libur:** Banner informatif jika hari ini libur madrasah atau libur rutin mingguan (Hari Jumat).
  - **Mode KBM Reguler:** Daftar jam pelajaran hari ini beserta mata pelajaran, jam ke-x, dan ustadz pengampu.
  - **Lihat Semua Jadwal (`SemuaJadwalScreen`):** Tombol pintasan langsung di bawah jadwal hari ini dan link header "Lihat Semua >" yang membuka layar jadwal pelajaran lengkap mingguan (Sabtu–Kamis) dilengkapi filter tab per hari, child switcher bar, info sesi KBM, dan penanda "Hari Ini".
- **Pengumuman Madrasah (Wali Murid):**
  - Hanya menampilkan pengumuman dengan target audience `Wali Murid` / `Semua`.
  - Disaring otomatis berdasarkan rentang tanggal aktif (`tanggal_mulai <= hari ini` dan `tanggal_selesai >= hari ini`). Pengumuman yang telah melewati tanggal selesai otomatis tertutup/sembunyi.
  - Detail dialog modal bottom sheet dan pintasan lampiran dokumen PDF.

### 2. 💳 Tab Keuangan & SPP (`KeuanganTab` / `TagihanTab`)

- **Sub-Tab 1: Kartu SPP Digital (11 Bulan Hijriyah):**
  - Visualisasi 11 Bulan Hijriyah: _Syawwal, Dzulqa'dah, Dzulhijjah, Muharram, Safar, Rabi'ul Awwal, Rabi'ul Akhir, Jumadil Awwal, Jumadil Akhir, Rajab, Sya'ban_.
  - Status tiap bulan: **Lunas** (Hijau + tgl bayar & no kwitansi), **Belum Lunas** (Abu-abu), **Bebas/Gratis** (Biru).
- **Sub-Tab 2: Tagihan Non-SPP:**
  - Tagihan berkala/insidental (_Kitab, Pembangunan, Ujian IMDA/IMNI, Seragam_).
- **Sub-Tab 3: Riwayat Pembayaran:**
  - Riwayat seluruh kwitansi pembayaran resmi dari loket bendahara madrasah.
- **Komplain Setoran (`FormKomplainSetoranSheet`):**
  - Form pengajuan verifikasi jika terdapat ketidaksesuaian catatan pembayaran fisik dengan sistem.

### 3. 📅 Tab Presensi & Kehadiran (`PresensiTab`)

- **Statistik Kehadiran:** Tingkat persentase kehadiran semester berjalan.
- **Metrik Counter:** Total Hadir, Izin, Sakit, Alpha.
- **Filter Tanggal Presensi (Date Switcher Bar):** Komponen pemilih tanggal aktif seperti pada `app_ustadz` dilengkapi nama hari & tanggal format Indonesia, dialog pemilih kalender (_Ganti Tanggal_), dan tombol kembali ke _Semua Riwayat_.
- **Log Riwayat Presensi per Sesi:** Catatan kehadiran terperinci per sesi mata pelajaran (Nama Mapel, Hari, Tanggal, dan Badge Status Kehadiran).

### 4. 📊 Tab Akademik & Rapor (`AkademikTab`)

- **Sub-Tab 1: Rapor Ujian:**
  - Arsip resmi rapor IMDA 1, IMDA 2, IMNI siap unduh/cetak PDF.
  - Rincian nilai ujian tiap mata pelajaran, KKM, predikat, rata-rata, dan banner catatan kedisiplinan (buku kasus).
- **Sub-Tab 2: Kenaikan Kelas (Hasil Pleno & SK Resmi):**
  - **Status Disahkan:** Tampilan keputusan resmi madrasah (_Naik Kelas_, _Lulus_, atau _Tinggal Kelas_), Nomor SK Keputusan, Nilai Akumulasi Akhir, Ruangan & Tingkat Tujuan, Catatan Ustadz Wali Ruangan / Dewan Asatidz, serta Tanggal dan Pejabat Pengesah.
  - **Status Belum Disahkan:** Banner informatif pending bahwa keputusan kenaikan kelas tahun pelajaran berjalan sedang dalam proses rapat pleno dan belum disahkan oleh pihak madrasah.
- **Sub-Tab 3: SK & Ijazah:**
  - Arsip Surat Keterangan Kelulusan (SK / SKTB) dan Ijazah Madrasah resmi.

### 5. 👤 Tab Akun & Informasi (`AkunTab`)

- **Biodata Murid (`BiodataAnakScreen`):** NISM, NISN, NIK, Tempat/Tgl Lahir, Orang Tua, Alamat Kampung.
- **Hubungi Admin (`HubungiAdminScreen`):** Pusat kontak bantuan WhatsApp dan layanan madrasah.
- **Pengaturan Server & Tema:** Ubah Host/IP API Backend, Pilihan Tema (Terang, AMOLED, Sistem), Ubah PIN Keamanan, dan Logout.
- _(Catatan: Tabungan, Koperasi, dan Buku Kasus kini terpusat di menu Akses Cepat pada `HomeTab`)_.

---

## 6. ALUR AUTENTIKASI & KEAMANAN SESI WALI

1. **Input Login:** Wali dapat login menggunakan **No. Kartu Keluarga (KK)**, **No. Registrasi Wali**, atau **NISM Murid** + Password/PIN.
2. **Endpoint:** `POST /api/wali/login`.
3. **Payload Response:** Sanctum Bearer Token + Data Objek Wali + Array seluruh anak aktif yang terdaftar dalam satu keluarga.
4. **Auto-Set Active Child:** Aplikasi otomatis menyetel anak pertama sebagai active child di `StorageService`.

---

## 7. PENANGANAN JARINGAN, STORAGE & IMAGE URL RESOLVER

### 7.1. Image URL Resolver (`ApiConstants.formatImageUrl` / `ApiClient.resolveImageUrl`)

Mencegah kegagalan load foto murid pada jaringan lokal Laragon / IP LAN:

```dart
final fotoUrl = ApiClient.resolveImageUrl(anak.fotoUrl);

CircleAvatar(
  backgroundImage: fotoUrl != null ? NetworkImage(fotoUrl) : null,
  child: fotoUrl == null ? Text(anak.initials) : null,
)
```

### 7.2. Storage & Session Handling

- Token disimpan aman di `StorageService` dan disertakan di header `Authorization: Bearer <token>`.
- Jika API mengembalikan error `401 Unauthorized`, sesi dibersihkan dan user diarahkan ke `LoginScreen`.

---

## 8. SOP LANGKAH DEMI LANGKAH MENAMBAH LAYAR / FITUR BARU

1. **Langkah 1: Tambahkan Model di `lib/data/models/`**  
   Implementasikan parsing `fromJson` dan helper getter.
2. **Langkah 2: Tambahkan Endpoint di `lib/core/constants/api_endpoints.dart`**  
   Daftarkan konstanta string route `/wali/...`.
3. **Langkah 3: Perluas `WaliRepository` di `lib/data/repositories/`**  
   Sediakan method fetch data dengan parameter `anakId`.
4. **Langkah 4: Perluas Provider di `lib/providers/`**  
   Pastikan provider mendengarkan perubahan `selectedAnakId` dan memicu refresh data.
5. **Langkah 5: Buat UI Component / Screen di `lib/ui/`**  
   Gunakan komponen M3 Expressive (`GlassCard`, `EmptyState`, `AppColors`).
6. **Langkah 6: Verifikasi Kualitas**  
   Jalankan `flutter analyze` dan pastikan tidak ada warning atau error.

---

## 9. ATURAN MUTLAK REDESIGN UI (ANTI-BREAK SOP)

1. **SELALU PERTAHANKAN MULTI-CHILD REACTIVITY:** Saat mendesain ulang tampilan apa pun, pastikan listener pergantian anak tetap berfungsi normal.
2. **SUPER AMOLED TRUE BLACK:** Gunakan `#000000` pekat murni pada background dark mode untuk efisiensi layar OLED.
3. **TOUCH TARGET RAMAH ORANG TUA:** Pastikan tombol, tab, dan kartu memiliki tinggi minimal `48dp` agar mudah ditekan oleh orang tua/wali murid.
4. **KONSISTENSI TERMINOLOGI:** Jangan gunakan istilah "Santri" atau "Kelas" pada UI.
