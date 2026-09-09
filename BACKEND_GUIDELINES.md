# 🖥️ MDT HIDAYATUS SHIBYAN — MASTER BACKEND GUIDELINES & ARCHITECTURE

> **Dokumen Panduan Tunggal (Single Source of Truth) Backend Laravel 11/13+ (Web Admin & REST API)**  
> _Lokasi File: Root Workspace (`/BACKEND_GUIDELINES.md`)_  
> _Tujuan: Menjaga konsistensi arsitektur, konvensi database, logika bisnis, keamanan, dan standar UI Blade saat penambahan fitur atau redesign._

---

## 📑 DAFTAR ISI

1. [Tech Stack & Lingkungan Kerja](#1-tech-stack--lingkungan-kerja)
2. [Pilar Konsep & Logika Bisnis Inti](#2-pilar-konsep--logika-bisnis-inti)
3. [Struktur Direktori & Modularisasi Domain](#3-struktur-direktori--modularisasi-domain)
4. [Standar Penamaan & Konvensi Database](#4-standar-penamaan--konvensi-database)
5. [Sistem RBAC, Role & Menu Dinamis](#5-sistem-rbac-role--menu-dinamis)
6. [Katalog RESTful API Mobile & Kontrak JSON](#6-katalog-restful-api-mobile--kontrak-json)
7. [Penanganan File, Media Storage & URL Image](#7-penanganan-file-media-storage--url-image)
8. [Panduan Redesign UI Blade (Material 3 & Glassmorphism)](#8-panduan-redesign-ui-blade-material-3--glassmorphism)
9. [SOP Langkah Demi Langkah Menambah Fitur Baru](#9-sop-langkah-demi-langkah-menambah-fitur-baru)
10. [Aturan Keamanan & Best Practices Mutlak](#10-aturan-keamanan--best-practices-mutlak)

---

## 1. TECH STACK & LINGKUNGAN KERJA

- **Framework:** Laravel 11.x / 13.x (PHP 8.2+)
- **Database Engine:** MySQL / MariaDB (InnoDB Engine, utf8mb4)
- **Autentikasi:** Laravel Breeze / Fortify + Laravel Sanctum (Token Auth API)
- **Otorisasi & RBAC:** Spatie Laravel Permission
- **Frontend Web Admin:** Blade Views + Tailwind CSS v4 / Vite + Alpine.js
- **Iconography:** Bootstrap Icons (`bi bi-*`)
- **Asset Bundler:** Vite (`@vite(['resources/css/app.css', 'resources/js/app.js'])`)
- **Timezone Server:** `Asia/Jakarta` (WIB, UTC+7)

---

## 2. PILAR KONSEP & LOGIKA BISNIS INTI

### 2.1. Standar Terminologi Baku (Glossary)

Wajib menggunakan istilah baku berikut di seluruh model, controller, route, migration, view, dan API response:

1. **"Murid"** (BUKAN "Santri") ➔ Tabel `murids`, Model `Murid`, Kolom `murid_id`.
2. **"Ruangan"** (BUKAN "Kelas") ➔ Rombongan belajar fisik (contoh: _Ruangan 1 Ula A_).
3. **"Wali Ruangan"** (BUKAN "Wali Kelas") ➔ Penanggung jawab ruangan.
4. **"NIGM"** (Nomor Induk Guru Madin) ➔ Identitas Ustadz (bersifat Read-Only).
5. **"NISM"** (Nomor Induk Santri Madin) ➔ Nomor identitas resmi murid.
6. **"11 Bulan Hijriyah"** ➔ Siklus SPP tahunan madrasah (Syawwal s.d. Sya'ban).

### 2.2. Poros Tahun Pelajaran & Semester Aktif

- Semua modul transaksional (_Rombel, Ruangan, Jadwal, Presensi, Nilai, SPP, Tabungan_) **WAJIB** terikat pada `tahun_pelajaran_id` aktif (`tahun_pelajarans.is_active = 1`).
- Ketika tahun ajaran berganti, data historis tetap aman karena terikat dengan `tahun_pelajaran_id`.
- Jangan pernah mengupdate data dengan mengasumsikan ID statis; selalu panggil resolver helper/service untuk tahun pelajaran aktif.

### 2.3. Hirarki Tingkat & Ruangan

1. **Tingkat** (contoh: _Ula / Wustha_) -> Jenjang pendidikan global.
2. **Level** (contoh: _Kelas 1, Kelas 2, Kelas 3_) -> Memiliki atribut `urutan_level`.
3. **Ruangan / Rombongan Belajar** (contoh: _1 Ula A, 2 Wustha B_) -> Unit kelas fisik aktif.

### 2.4. Keamanan Finansial & Transaksi

- Setiap operasi transaksi keuangan (SPP, Kas Ruangan, Tabungan, Koperasi POS) **WAJIB** dibungkus dalam `DB::transaction(function () { ... })` dengan `lockForUpdate()` pada record saldo/rekening terkait untuk mencegah _race condition_.
- Kas Ruangan yang sudah disetor ke Bendahara (`is_disetor = 1` / `status = 'diterima'`) bersifat **IMMUTABLE** (tidak dapat diedit/dihapus oleh Ustadz via mobile).

---

## 3. STRUKTUR DIREKTORI & MODULARISASI DOMAIN

Arsitektur backend menggunakan modularisasi **Domain-Driven Grouping**:

```text
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                    # REST API Controllers khusus Mobile App
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── PresensiMuridController.php
│   │   │   │   ├── PresensiUstadzController.php
│   │   │   │   ├── PelanggaranMuridController.php
│   │   │   │   ├── NilaiUjianController.php
│   │   │   │   ├── KasRuanganController.php
│   │   │   │   ├── TagihanController.php
│   │   │   │   ├── WaliController.php
│   │   │   │   └── MuridController.php
│   │   │   ├── Akademik/               # Web: Rombel, Jadwal, Presensi, Kalender
│   │   │   ├── Arsip/                  # Web: Arsip Dokumen, SK, Rapor, Ijazah
│   │   │   ├── Bantuan/                # Web: Laporan & Tiket Bantuan
│   │   │   ├── KasRuangan/             # Web: Kas Ruangan Murid & Setoran
│   │   │   ├── Kepengurusan/           # Web: Struktur Organisasi & Pengurus
│   │   │   ├── Keuangan/               # Web: Tagihan SPP, Kasir Pembayaran
│   │   │   ├── Koperasi/               # Web: POS Kasir, Produk, Pembelian, Stok
│   │   │   ├── MasterData/             # Web: Murid, Ustadz, Wali, Ruangan, Mapel
│   │   │   ├── Pengaturan/             # Web: User, Tahun Pelajaran, Backup
│   │   │   ├── PengaturanMenu/         # Web: RBAC Dynamic Menu & Permissions
│   │   │   ├── Spmb/                   # Web: Pendaftaran Murid Baru
│   │   │   ├── Tabungan/               # Web: Master Rekening, Setor, Tarik, Pecahan
│   │   │   └── Ujian/                  # Web: Jadwal Ujian, Syarat, Nilai, Rapor
│   │   └── Requests/                   # Form Request Validation per domain
│   │       ├── Akademik/
│   │       ├── Keuangan/
│   │       ├── MasterData/
│   │       └── Tabungan/
│   ├── Models/                         # Eloquent Models (kelompok domain)
│   ├── Repositories/                   # Query builder kompleks & terisolasi
│   └── Services/                       # Pure Business Logic Layer
├── database/
│   ├── migrations/                     # Skema migrasi database
│   └── seeders/                        # RBAC, Menu, dan Data Awal
├── resources/
│   ├── css/app.css                     # Tailwind CSS & Custom M3 Glass Classes
│   └── views/
│       ├── components/                 # Reusable Blade UI Components
│       ├── layouts/                    # Layout Master (app, guest, cetak)
│       └── [domain_views]/             # View folder per fitur (murid, ustadz, dll.)
└── routes/
    ├── web.php                         # Rute Web Panel & Admin
    └── api.php                         # Rute RESTful API Mobile
```

---

## 4. STANDAR PENAMAAN & KONVENSI DATABASE

1. **Tabel Database:** Plural snake_case (`murids`, `ustadzs`, `tahun_pelajarans`, `tagihan_murids`, `kas_ruangans`).
2. **Primary Key:** `id` (BigIncrements / Unsigned BigInteger).
3. **Foreign Keys:** Single singular model + `_id` (`murid_id`, `ruangan_id`, `tahun_pelajaran_id`). Gunakan `.constrained()->cascadeOnDelete()` atau `.nullOnDelete()`.
4. **Status & Boolean Flags:** Gunakan prefix yang jelas (`is_active`, `is_locked`, `is_published`, `is_disetor`).
5. **Casting Tipe Data:** Selalu definisikan `$casts` di Eloquent Model (`'is_active' => 'boolean'`, `'tanggal' => 'date'`, `'nominal' => 'integer'`).
6. **Soft Deletes:** Gunakan `SoftDeletes` untuk data krusial (`Murid`, `Ustadz`, `TransaksiTabungan`).

---

## 5. SISTEM RBAC, ROLE & MENU DINAMIS

Aplikasi menerapkan **Role-Based Access Control** (Spatie Permission):

### 5.1. Matriks Role Web Admin:

- **`administrator`**: Akses penuh ke seluruh fitur dan konfigurasi sistem tanpa batasan `tingkat_id`.
- **`petugas-tabungan`**: Akses modul tabungan (buka rekening, setor/tarik tunai, verifikasi buku fisik, pembagian).
- **`bendahara`**: Akses modul keuangan SPP, non-SPP, kas ruangan, dan laporan keuangan.
- **`staff`**: Akses operasional administrasi yang dapat dibatasi berdasarkan `tingkat_id` tertentu.

### 5.2. Role Mobile App Gatekeeper:

- **`app_ustadz`**: Hanya mengizinkan user dengan role `ustadz`. Role `administrator` dan `staff` otomatis ditolak dengan status HTTP **403 Forbidden**.
- **`app_murid` (Wali Murid)**: Login menggunakan Nomor KK, NISM, atau No. Registrasi Wali.

### 5.3. Dynamic Menu System:

Sidebar web admin dirender dinamis dari tabel `menus` dan `menu_permissions`. Pengaturan hirarki menu dan permission dapat dikelola melalui `/pengaturan-menu/menu`.

---

## 6. KATALOG RESTFUL API MOBILE & KONTRAK JSON

### 6.1. Standar Format Response JSON

#### Response Sukses (HTTP 200 / 201):

```json
{
  "success": true,
  "message": "Data berhasil dimuat.",
  "data": { ... }
}
```

#### Response Validasi Gagal (HTTP 422):

```json
{
  "success": false,
  "message": "Input tidak valid.",
  "errors": {
    "field_name": ["Pesan error validasi."]
  }
}
```

#### Response Akses Ditolak (HTTP 401 / 403):

```json
{
  "success": false,
  "message": "Akses ditolak atau sesi telah berakhir."
}
```

### 6.2. Ringkasan Endpoint API Mobile (`routes/api.php`)

| Endpoint                         | Method | Fungsi & Modul                                   |
| :------------------------------- | :----: | :----------------------------------------------- |
| `/api/login`                     | `POST` | Login Ustadz (Sanctum Token + Info Wali Ruangan) |
| `/api/wali/login`                | `POST` | Login Wali Murid (Multi-Child Data + Token)      |
| `/api/logout`                    | `POST` | Revoke Token Bearer aktif                        |
| `/api/profile`                   | `GET`  | Profil user & biodata aktif                      |
| `/api/dashboard`                 | `GET`  | Statistik home, jadwal hari ini, pengumuman      |
| `/api/kalendar-pendidikan`       | `GET`  | Kalender akademik & hari libur                   |
| `/api/presensi-murid/sesi`       | `GET`  | Sesi mengajar hari ini (Proteksi Libur)          |
| `/api/presensi-murid/murid`      | `GET`  | Daftar murid per jadwal untuk absensi            |
| `/api/presensi-murid/simpan`     | `POST` | Simpan absensi massal (H/I/S/A/D)                |
| `/api/presensi-ustadz/checkin`   | `POST` | Check-in kehadiran ustadz / badal                |
| `/api/pelanggaran/harian`        | `GET`  | Log & statistik pelanggaran hari ini             |
| `/api/pelanggaran/simpan-massal` | `POST` | Catat pelanggaran banyak murid sekaligus         |
| `/api/ujian/input-data`          | `GET`  | Form nilai ujian + status lock tunggakan         |
| `/api/ujian/simpan-nilai`        | `POST` | Simpan nilai mapel (Draft / Publish)             |
| `/api/ujian/leger`               | `GET`  | Rekap leger & kalkulasi ranking 1 ruangan        |
| `/api/kas-ruangan/ringkasan`     | `GET`  | Saldo kas, target, dan rincian murid             |
| `/api/kas-ruangan/simpan-bayar`  | `POST` | Catat cicilan kas murid                          |
| `/api/tagihan/spp/kartu`         | `GET`  | Kartu SPP 11 Bulan Hijriyah per murid            |
| `/api/tagihan/non-spp/bayar`     | `POST` | Bayar tagihan non-SPP (Single/Massal)            |
| `/api/laporan/*`                 | `GET`  | Rekap presensi, pelanggaran, ujian, kenaikan     |
| `/api/wali/*`                    | `GET`  | Endpoint monitoring anak untuk Wali Murid        |

---

## 7. PENANGANAN FILE, MEDIA STORAGE & URL IMAGE

1. **Penyimpanan:** Selalu gunakan disk `public` (`Storage::disk('public')->putFile(...)`). File tersimpan di `storage/app/public/...`.
2. **Symlink:** Pastikan symlink aktif via `php artisan storage:link` sehingga path dapat diakses dari browser/mobile via `/storage/...`.
3. **URL Serializer API:**
   - Kirimkan URL lengkap menggunakan `asset('storage/' . $path)` atau path relatif bersih `/storage/...`.
   - Hindari hardcode IP atau domain di dalam controller; gunakan `Storage::url($path)` atau accessor model `foto_url`.

---

## 8. PANDUAN REDESIGN UI BLADE (MATERIAL 3 & GLASSMORPHISM)

Ketika melakukan pembaruan atau penambahan tampilan Blade di folder `resources/views/`:

### 8.1. Aturan Mutlak Refactoring Views:

1. **DILARANG MERUSAK LOGIKA BACKEND:** Jangan ubah atribut `action`, `method`, `name`, dan `id` pada form.
2. **PERTAHANKAN ALPINE.JS STATE:** Jangan ubah direktif `x-data`, `x-model`, `x-on`, `x-show`.
3. **DARK MODE OLED:** Gunakan kelas `dark:bg-black` untuk background utama dan `dark:bg-zinc-900/60` dengan `dark:backdrop-blur-md` untuk card kontainer.

### 8.2. Standar Utility Classes Tailwind:

- Kontainer Kaca: `.m3-glass-card` (`rounded-2xl` / `rounded-3xl`, `backdrop-blur-md`).
- Tombol Utama: `.m3-btn-primary` (`rounded-xl` / `rounded-2xl`, warna `#146C2E`).
- Input Form: `.m3-input-glass` (`rounded-xl`, border tipis, transparan).

---

## 9. SOP LANGKAH DEMI LANGKAH MENAMBAH FITUR BARU

```text
[1. Migration & Model] ➔ [2. Form Request] ➔ [3. Service Layer] ➔ [4. Controller (Web & API)]
         ⬇️
[8. Testing & QA] ⬅️ [7. RBAC & Seeder] ⬅️ [6. Blade / API Routes] ⬅️ [5. Views / Resource]
```

1. **Langkah 1: Buat Migration & Model**  
   `php artisan make:model Domain/NamaModel -m`  
   Definisikan relasi, foreign key, `$guarded = ['id']`, dan `$casts`.
2. **Langkah 2: Buat Form Request**  
   `php artisan make:request Domain/NamaRequest`  
   Tulis aturan validasi ketat dan pesan error Bahasa Indonesia.
3. **Langkah 3: Tulis Business Logic di Service**  
   Buat class di `app/Services/Domain/NamaService.php`. Bungkus transaksi finansial dalam `DB::transaction`.
4. **Langkah 4: Buat Controller**
   - Web Controller di `app/Http/Controllers/Domain/`.
   - API Controller di `app/Http/Controllers/Api/` jika fitur diakses mobile.
5. **Langkah 5: Daftarkan Rute**
   - Web routes di `routes/web.php` dengan middleware auth & permission.
   - API routes di `routes/api.php` dengan middleware `auth:sanctum`.
6. **Langkah 6: Buat Blade View / API Resource**  
   Gunakan komponen M3 Glassmorphism yang konsisten.
7. **Langkah 7: Konfigurasi RBAC & Menu**  
   Tambahkan permission baru dan tautkan ke menu navigasi dinamis.
8. **Langkah 8: Pengujian**  
   Uji CRUD di web dan verifikasi kontrak JSON endpoint di mobile.

---

## 10. ATURAN KEAMANAN & BEST PRACTICES MUTLAK

1. **Jangan Menghapus Data Transaksional Sembarangan:** Selalu cek relasi sebelum menghapus murid atau rekening.
2. **Gunakan Signed Route:** Untuk dokumen cetak publik atau link verifikasi QR Code ijazah/rapor.
3. **Sanitasi Upload File:** Batasi ekstensi gambar (`jpg,jpeg,png,webp`) dan ukuran maksimum (maks 2MB).
4. **Konsistensi HTTP Status Code:** `200` OK, `201` Created, `400` Bad Request, `401` Unauthorized, `403` Forbidden, `404` Not Found, `422` Unprocessable Entity, `500` Server Error.
