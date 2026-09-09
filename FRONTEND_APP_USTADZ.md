# 🧑‍🏫 MDT HIDAYATUS SHIBYAN — FRONTEND APP USTADZ GUIDELINES

> **Dokumen Panduan Tunggal (Single Source of Truth) Aplikasi Mobile Flutter Ustadz & Wali Ruangan**  
> _Lokasi File: Root Workspace (`/FRONTEND_APP_USTADZ.md`)_  
> _Target Project: `frontend/app_ustadz` (`lib/`)_  
> _Tujuan: Standar konsistensi arsitektur, state management, desain sistem M3 Expressive, token warna, aturan wewenang wali ruangan, dan alur integrasi API._

---

## 📑 DAFTAR ISI

1. [Tech Stack & Arsitektur Aplikasi](#1-tech-stack--arsitektur-aplikasi)
2. [Standar Terminologi Resmi (Glossary)](#2-standar-terminologi-resmi-glossary)
3. [Design System: Material 3 Expressive (Google Pixel Edition)](#3-design-system-material-3-expressive-google-pixel-edition)
4. [Matriks Wewenang & Hak Akses Ustadz](#4-matriks-wewenang--hak-akses-ustadz)
5. [Spesifikasi 5 Tab Navigasi & Seluruh Modul Layar](#5-spesifikasi-5-tab-navigasi--seluruh-modul-layar)
6. [Penanganan Jaringan, Storage & Image URL Resolver](#6-penanganan-jaringan-storage--image-url-resolver)
7. [SOP Langkah Demi Langkah Menambah Layar / Fitur Baru](#7-sop-langkah-demi-langkah-menambah-layar--fitur-baru)
8. [Aturan Mutlak Redesign UI (Anti-Break SOP)](#8-aturan-mutlak-redesign-ui-anti-break-sop)

---

## 1. TECH STACK & ARSITEKTUR APLIKASI

- **Framework:** Flutter 3.x+ (Dart SDK 3.x+)
- **Arsitektur State Management:** MVVM (Model-View-ViewModel) berbasis `Provider` (`ChangeNotifierProvider`, `Consumer`).
- **HTTP Client:** `Dio` dengan Bearer Token Interceptor & Auto 401 Logout.
- **Penyimpanan Lokal:** `shared_preferences` / `flutter_secure_storage` (Token, User Profile, Server IP, Theme Mode).
- **Tipografi:** Google Fonts (`Plus Jakarta Sans` / `Google Sans Flex` untuk Latin, `Amiri` untuk teks Arab/doa).
- **Format Pertukaran Data:** RESTful JSON API.

### 📂 Struktur Direktori Standar (`frontend/app_ustadz/lib/`):

```text
lib/
├── core/
│   ├── constants/        # ApiConstants, AppConstants
│   ├── network/          # ApiClient, Interceptors, Error Handling
│   ├── storage/          # StorageService (Token, Preferences)
│   ├── theme/            # AppColors, AppTheme, AppTypography
│   └── utils/            # HapticHelper, DateHelper, HijriHelper, CurrencyHelper
├── data/
│   ├── models/           # Dart Data Classes (fromJson, toJson)
│   └── repositories/     # Repository Layer (Komunikasi ApiClient -> Model)
├── providers/            # State Management Provider (Auth, Presensi, Kas, dll.)
├── ui/
│   ├── auth/             # Login, ForgotPassword, VerifyOtp, NewPassword
│   ├── tabs/             # 5 Tab Navigasi Utama (Home, Presensi, Pelanggaran, dll.)
│   │   ├── home/
│   │   ├── presensi/
│   │   ├── pelanggaran/
│   │   ├── penilaian/
│   │   ├── kas/
│   │   ├── tagihan/
│   │   ├── laporan/
│   │   └── akun/
│   └── widgets/          # Reusable Components (GlassCard, CustomButton, Shimmer)
└── main.dart
```

---

## 2. STANDAR TERMINOLOGI RESMI (GLOSSARY)

Seluruh teks string, label tombol, dialog, dan model di `app_ustadz` **WAJIB** mematuhi:

1. **"Murid"** ➔ (Bukan "Santri"). Contoh: _Direktori Murid_, _Kas Murid_, _Murid Binaan_.
2. **"Ruangan"** ➔ (Bukan "Kelas"). Contoh: _Ruangan 1 Ula_, _Kas Ruangan_.
3. **"Wali Ruangan"** ➔ (Bukan "Wali Kelas").
4. **"NIGM"** ➔ Nomor Induk Guru Madin (Identitas Ustadz, bersifat _Read-Only_).
5. **"NISM"** ➔ Nomor Induk Santri Madin (Identitas Murid).
6. **"11 Bulan Hijriyah"** ➔ Siklus SPP madrasah (Syawwal s.d. Sya'ban).

---

## 3. DESIGN SYSTEM: MATERIAL 3 EXPRESSIVE (GOOGLE PIXEL EDITION)

Aplikasi mengadopsi estetika **Material 3 Expressive** dengan nuansa murni Google Pixel dan nilai-nilai madrasah yang teduh:

### 3.1. Token Palette Warna

| Token Name           | Light Mode               | Super AMOLED Dark Mode             | Peran Visual                                    |
| :------------------- | :----------------------- | :--------------------------------- | :---------------------------------------------- |
| `primary`            | `#146C2E` (Forest Green) | `#3BC05B` (Bright Pixel Emerald)   | Brand Utama, Tombol Primer, Active Tab          |
| `primaryContainer`   | `#DCFCE7` (Emerald 100)  | `#00531E` (Emerald 950 Deep)       | Background Badge, Highlight Card, Selected Chip |
| `onPrimaryContainer` | `#14532D`                | `#A6FBAA`                          | Teks di dalam Primary Container                 |
| `surface`            | `#FAF9F6` (Warm Canvas)  | `#000000` (Pure AMOLED True Black) | Kanvas Latar Belakang Scaffold                  |
| `surfaceCard`        | `rgba(255,255,255,0.85)` | `rgba(24,31,24,0.70)` / `#111611`  | Kartu Glassmorphism                             |
| `outline`            | `#E4E4E7` (Zinc 200)     | `#272F27` / `#222B22`              | Border Garis Tipis 1px Kontainer                |
| `amberAccent`        | `#F59E0B`                | `#FBBF24`                          | Poin Pelanggaran, Ranking, Warning              |
| `skyBlueAccent`      | `#0284C7`                | `#38BDF8`                          | Kalender Pendidikan, Pengumuman                 |
| `violetAccent`       | `#7C3AED`                | `#A78BFA`                          | Leger Nilai, Ujian, Kenaikan Kelas              |
| `roseDanger`         | `#DC2626`                | `#F87171`                          | Hapus Data, Status Alpha, Tunggakan             |

### 3.2. Status Badge Presensi Semantik

- **Hadir (H):** Teks `#15803D` / Background `#DCFCE7` (Dark: Teks `#4ADE80`)
- **Izin (I):** Teks `#1D4ED8` / Background `#DBEAFE` (Dark: Teks `#60A5FA`)
- **Sakit (S):** Teks `#B45309` / Background `#FEF3C7` (Dark: Teks `#FBBF24`)
- **Alpha (A):** Teks `#B91C1C` / Background `#FEE2E2` (Dark: Teks `#F87171`)
- **Dispensasi (D):** Teks `#6D28D9` / Background `#EDE9FE` (Dark: Teks `#C084FC`)

### 3.3. Bentuk & Komponen Fisik (Expressive Shapes)

- **Glass Card:** `BorderRadius.circular(24.0)` dengan border `1px` solid outline color.
- **Modal / Bottom Sheet:** `BorderRadius.vertical(top: Radius.circular(32.0))` dengan Pill Drag Handle di atas.
- **Button & Input:** `BorderRadius.circular(18.0 - 22.0)`.
- **Status Chip / Filter:** Pill Shape (`BorderRadius.circular(999.0)`).
- **Haptic Feedback:** Panggil `HapticHelper.light()` pada setiap pemilihan status chip, `HapticHelper.success()` saat simpan berhasil, dan `HapticHelper.warning()` saat validasi gagal/hapus data.

### 3.4. Mode Tema: Default Light Mode & Theme Persistence

- **Mode Default:** Aplikasi berjalan dalam **Mode Terang (Light Mode / `ThemeMode.light`)** secara default saat pertama kali diinstal/dibuka.
- **Opsi Tema:** Pengguna ustadz dapat beralih tema secara fleksibel pada menu **Akun ➔ Tampilan & Sistem ➔ Tema Tampilan**:
  1. **Mode Terang (Default):** Tampilan cerah, bersih, kanvas _Warm Clean Canvas_ (`#FAF9F6`), dan kartu kontras tinggi.
  2. **Mode Gelap (Super AMOLED):** Latar hitam murni (`#000000`) dengan kartu deep glass (`#101710` / `#182218`) untuk hemat daya baterai.
  3. **Ikuti Sistem:** Mengikuti konfigurasi tema terang/gelap pada OS perangkat pengguna.
- **Penyimpanan:** Preferensi mode tema disimpan secara permanen di `StorageService` (`shared_preferences`) menggunakan key `app_theme_mode` yang dikelola oleh `ThemeProvider`.

---

## 4. MATRIKS WEWENANG & HAK AKSES USTADZ

Backend memfilter akses berdasarkan status Ustadz di Tahun Pelajaran aktif:

| Fitur / Modul                   | Ustadz Reguler (Guru Mapel) |                    Ustadz Wali Ruangan                    |
| :------------------------------ | :-------------------------: | :-------------------------------------------------------: |
| **Login & Biodata Pribadi**     |       ✅ Akses Penuh        |                      ✅ Akses Penuh                       |
| **Jadwal Mengajar**             |  ✅ Sesuai Jadwal Pribadi   |     ✅ Jadwal Pribadi + Seluruh Jadwal Ruangan Binaan     |
| **Presensi Murid KBM**          |    ✅ Jadwal Yang Diampu    | ✅ Jadwal Pribadi + **Bypass Semua Jadwal di Ruangannya** |
| **Presensi Ustadz & Badal**     |     ✅ Check-In Mandiri     | ✅ Check-In Mandiri + Monitor Guru Pengajar di Ruangannya |
| **Buku Kasus / Pelanggaran**    | ✅ Murid di jam mengajarnya |            ✅ **Seluruh Murid di Ruangannya**             |
| **Input Nilai Ujian Mapel**     |    ✅ Mapel yang diampu     |     ✅ Mapel yang diampu + Semua Mapel di Ruangannya      |
| **Leger Nilai & Ranking**       |       ❌ Sembunyikan        |     ✅ **Penuh (Leger 1 Ruangan & Bintang Pelajar)**      |
| **Kas Ruangan (Bayar & Setor)** |       ❌ Sembunyikan        |    ✅ **Penuh (Pencatatan Iuran & Setoran Bendahara)**    |
| **Tagihan SPP & Non-SPP**       |       ❌ Sembunyikan        |      ✅ **Monitoring SPP 11 Bulan & Bayar Non-SPP**       |
| **Direktori Murid Binaan**      |    ✅ Murid yang diajar     |            ✅ **Seluruh Murid Ruangan Binaan**            |

---

## 5. SPESIFIKASI 5 TAB NAVIGASI & SELURUH MODUL LAYAR

### 1. 🏠 Tab Home (`HomeTab`)

- **Header:** Salam Islami, Foto Profil Avatar (dengan initial fallback), Badge Tahun Pelajaran Aktif (misal: _1447/1448 H_), dan Badge _Wali Ruangan: [Nama Ruangan]_.
- **Quick 4-Grid Menu (Umum):** Kalender Pendidikan, Referensi Pelanggaran, Master Mapel, Jadwal Pelajaran.
- **Quick Wali Menu (Khusus Wali Ruangan):** SPP Ruangan, Tagihan Non-SPP, Kas Ruangan, Pusat Laporan, Anggota Ruangan.
- **Carousel Pengumuman:** Banner pengumuman resmi madrasah.
- **Jadwal Mengajar Hari Ini:** Kartu real-time sesi KBM dengan tombol cepat **"Mulai Presensi"**.

### 2. 📋 Tab Presensi (`PresensiTab`)

- **Sub-Tab 1: Presensi Murid KBM:**
  - Date Picker interaktif.
  - **Proteksi Hari Libur:** Jika hari Jumat atau hari libur kalender, tampilkan Card "Libur Madrasah" dan sembunyikan form KBM.
  - Form Absensi Cepat: Tombol **1-Tap "Hadirkan Semua"**, chip status H/I/S/A/D per murid, dan **Sticky Bottom Action Bar** dengan counter live.
- **Sub-Tab 2: Presensi Ustadz & Badal:**
  - Kartu sesi mengajar ustadz hari ini dengan status _Belum Check-In_ / _Hadir_ / _Izin (Badal)_.
  - Form Check-In: Pilihan status, dropdown guru pengganti (Badal), dan catatan.
- **Sub-Tab 3: Leger Presensi Bulanan:**
  - Rekap persentase kehadiran per bulan Hijriyah.

### 3. 💰 Modul Kas Ruangan (`KasRuanganScreen`)

- **Tab 1: Bayar (Catat Iuran Murid):**
  - Ringkasan target kas, total terkumpul, dan sisa.
  - Modal Bayar Kas: Tombol nominal cepat (_Rp 2.000, Rp 5.000, Rp 10.000_) atau custom amount.
  - Riwayat pembayaran iuran per murid (Bisa dibatalkan selama belum disetor).
- **Tab 2: Setor (Setoran ke Bendahara):**
  - Form Setor: Nominal setor, pilihan penerima (Bendahara/Pimpinan), upload foto bukti transfer.
  - Riwayat status setoran: _Menunggu Konfirmasi_, _Diterima_, _Ditolak_.
  - **Immutable Rule:** Setoran yang berstatus _Diterima_ tidak dapat diedit/dihapus via mobile.

### 4. 🧾 Modul Tagihan (`TagihanScreen`)

- **Sub-Tab 1: Tagihan SPP (11 Bulan Hijriyah):**
  - Monitoring matriks murid vs bulan Syawwal s.d. Sya'ban.
  - Modal Kartu SPP Digital popup (_Lunas: Hijau, Belum: Abu-abu, Bebas: Biru_).
- **Sub-Tab 2: Tagihan Non-SPP:**
  - Filter jenis tagihan (Kitab, Pembangunan, Ujian, dll.).
  - Modal Bayar & Fitur Bayar Massal (centang beberapa murid sekaligus).
  - Fitur Batal Bayar dengan dialog konfirmasi.

### 5. ⚖️ Tab Pelanggaran & Kedisiplinan (`PelanggaranTab`)

- **Sub-Tab 1: Harian:** Feed catatan pelanggaran hari ini dengan Date Navigator.
- **Sub-Tab 2: Ruangan:** List murid per ruangan dengan total poin akumulasi sanksi.
- **Sub-Tab 3: Massal:** Catat pelanggaran untuk banyak murid sekaligus:
  1. Pilih Ruangan ➔ 2. Centang Murid ➔ 3. Input Nomor Poin/ID Pelanggaran (Auto lookup nama & bobot poin) ➔ 4. Simpan Massal.
- Fitur Hapus Catatan Kasus dengan konfirmasi dialog.

### 6. 📊 Modul Penilaian & Leger Ujian (`PenilaianTab` / `LegerRuanganScreen`)

- Pilihan agenda ujian aktif: `IMDA 1`, `IMDA 2`, `IMNI`.
- **Administration Lock:** Murid yang memiliki tunggakan tanpa dispensasi berstatus _is_locked = true_ (input nilai dinonaktifkan).
- **Mode Draf vs Publikasi:** Simpan draf penilaian atau langsung publikasikan ke Rapor resmi.
- **Leger 1 Ruangan:** Rekap seluruh mapel + kalkulasi ranking kelas + podium Bintang Pelajar (Juara 1, 2, 3).

### 7. 📈 Pusat Laporan Terpadu (`PusatLaporanScreen`)

- Rekap Presensi Murid, Rekap Presensi Ustadz, Rekap Pelanggaran Murid, Laporan Hasil Ujian, dan Laporan Kenaikan Kelas.

### 8. 👤 Tab Akun & Pengaturan (`AkunTab`)

- **Profil Ustadz:** Foto Profil Avatar, Nama, dan NIGM (Read-Only dengan ikon gembok).
- **Biodata:** Edit NIK, Tempat/Tgl Lahir, Jenis Kelamin, No HP, Alamat.
- **Media:** Ganti Foto Profil & Tanda Tangan Digital via Signature Pad Canvas.
- **Keamanan & Sistem:** Ganti Password, Konfigurasi Alamat Server IP API, Toggle Super AMOLED True Black Mode, Logout.

---

## 6. PENANGANAN JARINGAN, STORAGE & IMAGE URL RESOLVER

### 6.1. Image URL Resolver (`ApiConstants.formatImageUrl`)

Untuk mencegah error gambar gagal dimuat akibat perbedaan host emulator/fisik (`localhost` vs `10.0.2.2` vs `IP LAN`):

```dart
// Selalu bungkus URL gambar dari backend dengan formatImageUrl:
final resolvedUrl = ApiConstants.formatImageUrl(user.fotoUrl);

// Gunakan ImageResolver dengan Fallback Inisial Huruf:
CircleAvatar(
  backgroundImage: resolvedUrl != null ? NetworkImage(resolvedUrl) : null,
  child: resolvedUrl == null ? Text(user.initials) : null,
)
```

### 6.2. Network Error Handling

- Response `401 Unauthorized` ➔ Hapus token lokal dan navigasikan user ke `LoginScreen`.
- Response `422 Unprocessable Entity` ➔ Tangkap object `errors` dan tampilkan Snackbar/Toast pesan kesalahan field.
- Response `403 Forbidden` ➔ Tampilkan dialog penolakan hak akses.

---

## 7. SOP LANGKAH DEMI LANGKAH MENAMBAH LAYAR / FITUR BARU

Saat menambahkan fitur baru pada `app_ustadz`:

1. **Langkah 1: Buat Model Dart di `lib/data/models/`**  
   Implementasikan method factory `fromJson(Map<String, dynamic> json)` dan `toJson()`.
2. **Langkah 2: Tambahkan Endpoint di `lib/core/constants/api_constants.dart`**  
   Definisikan rute string API baru.
3. **Langkah 3: Buat / Perluas Repository di `lib/data/repositories/`**  
   Panggil `ApiClient.instance` dan return Model yang sudah diparsing.
4. **Langkah 4: Buat / Daftarkan Provider di `lib/providers/`**  
   Gunakan `ChangeNotifier` untuk mengelola state loading, error, dan data payload. Daftarkan di `main.dart` (`MultiProvider`).
5. **Langkah 5: Buat Layar UI di `lib/ui/`**  
   Gunakan komponen standar (`GlassCard`, `CustomButton`, token `AppColors`).
6. **Langkah 6: Uji Coba & Analisis**  
   Jalankan `flutter analyze` dan pastikan 0 error / warning.

---

## 8. ATURAN MUTLAK REDESIGN UI (ANTI-BREAK SOP)

1. **JANGAN MERUSAK STATE MANAGEMENT:** Pertahankan nama method controller, provider listener, dan aliran data repository.
2. **SUPER AMOLED TRUE BLACK:** Pada dark mode, latar scaffold harus `#000000` murni, bukan abu-abu atau biru gelap.
3. **ERGONOMI SATU TANGAN:** Letakkan tombol aksi primer (Simpan, Bayar, Filter) pada 40% area bawah layar (_Thumb-Zone Priority_).
4. **HAPTIC FEEDBACK:** Selalu sematkan `HapticHelper` pada interaksi penting agar sensasi sentuhan konsisten.
