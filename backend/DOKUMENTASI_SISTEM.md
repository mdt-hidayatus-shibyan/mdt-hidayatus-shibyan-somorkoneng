# 📚 DOKUMENTASI SISTEM & PANDUAN PENGEMBANGAN (DEVELOPER GUIDE)

## Sistem Informasi Manajemen Madrasah Diniyah Takmiliyah (MDT) Hidayatus Shibyan

Dokumentasi ini disusun untuk memandu pengembang (developer) dalam memahami arsitektur, alur kerja bisnis, struktur kode, serta Standar Operasional Prosedur (SOP) ketika ingin melakukan penambahan atau modifikasi fitur baru.

---

## 📑 DAFTAR ISI

1. [Gambaran Umum & Tech Stack](#1-gambaran-umum--tech-stack)
2. [Pilar Konsep & Logika Bisnis Inti](#2-pilar-konsep--logika-bisnis-inti)
3. [Arsitektur Direktori & Modularisasi Domain](#3-arsitektur-direktori--modularisasi-domain)
4. [Sistem RBAC, Role & Menu Dinamis](#4-sistem-rbac-role--menu-dinamis)
5. [Panduan Langkah Demi Langkah Menambah Fitur Baru](#5-panduan-langkah-demi-langkah-menambah-fitur-baru)
6. [Daftar Modul & Fitur yang Sudah Ada](#6-daftar-modul--fitur-yang-sudah-ada)
7. [Konvensi Kode & Standar Praktik Terbaik (Best Practices)](#7-konvensi-kode--standar-praktik-terbaik-best-practices)

---

## 1. GAMBARAN UMUM & TECH STACK

Aplikasi ini adalah platform sistem informasi terintegrasi untuk Madrasah Diniyah Takmiliyah yang mencakup manajemen data murid, akademik, penilaian rapor, kesekretariatan, tagihan SPP, hingga perbankan mikro madrasah (Tabungan Berjangka).

### 🛠️ Stack Teknologi:

- **Framework Utama:** Laravel 11.x (PHP 8.2+)
- **Basis Data:** MySQL / MariaDB (InnoDB Engine)
- **Autentikasi & Otorisasi:** Laravel Breeze + Spatie Laravel Permission (RBAC)
- **Frontend & UI:** Blade Template + Tailwind CSS (Material 3 Theme System) + Alpine.js
- **Ikonografi:** Bootstrap Icons
- **Manajemen Sesi:** Database Driver (`sessions` table) untuk pelacakan user online & force logout.

---

## 2. PILAR KONSEP & LOGIKA BISNIS INTI

Sebelum memodifikasi atau menambah fitur, pahami aturan dasar berikut:

### A. Tahun Pelajaran & Semester sebagai Poros Utama

- Hampir seluruh modul transaksional (Rombongan Belajar, Ruangan, Jadwal, Presensi, Nilai Leger, Tagihan SPP, dan Periode Tabungan) bergantung pada **Tahun Pelajaran Aktif** (`tahun_pelajarans.is_active = 1`).
- Ketika tahun pelajaran berganti, data historis tetap aman karena terikat dengan `tahun_pelajaran_id`.

### B. Penyeragaman Istilah "Murid"

- **Wajib menggunakan istilah "Murid"** (bukan "Santri") di seluruh kode, controller, model, migration, rute, dan tampilan view agar konsisten dengan database.

### C. Hirarki Struktur Kelas

1. **Tingkat** (Contoh: _Ula / Wustha_) -> Mengelompokkan level jenjang pendidikan.
2. **Level** (Contoh: _Kelas 1, Kelas 2, Kelas 3_) -> Jenjang kelas yang memiliki `urutan_level`.
3. **Ruangan / Rombongan Belajar** (Contoh: _Ruangan 1A, Ruangan 1B_) -> Kelas fisik tempat belajar mengajar pada tahun ajaran aktif.

### D. Sistem Tabungan Madrasah Berjangka

- **Kategori Nasabah:** `Murid`, `Ustadz`, `Kas Ruangan`, dan `Umum`.
- **Nomor Rekening & Barcode:** Format barcode unik (otomatis atau scan barcode buku fisik).
- **Periode Tabungan:** Setiap siklus tabungan berelasi dengan `tahun_pelajaran_id`.
- **Potongan Madrasah:** Persentase potongan madrasah diset per periode tabungan.
- **Verifikasi Buku Fisik:** Proses pencocokan saldo sistem dengan catatan saldo fisik di buku tabungan.
- **Rincian Pecahan Uang:** Kalkulasi otomatis pecahan uang kas fisik (Rp 100.000 s/d Rp 100) per murid dan rekapitulasi global untuk memudahkan penarikan kas dari bank.

---

## 3. ARSITEKTUR DIREKTORI & MODULARISASI DOMAIN

Aplikasi menggunakan arsitektur **Domain-Driven Grouping** untuk memudahkan penemuan file:

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Akademik/          # Rombel, Jadwal, Presensi, Pelanggaran, Kalender
│   │   │   ├── Arsip/             # Arsip Dokumen, SK, Rapor, Ijazah
│   │   │   ├── Bantuan/           # Laporan Kendala Sistem
│   │   │   ├── KasRuangan/        # Kas Ruangan Murid
│   │   │   ├── Kepengurusan/      # Struktur Organisasi, Anggota, Pengurus
│   │   │   ├── Keuangan/          # Tagihan SPP, Pembayaran Murid
│   │   │   ├── MasterData/        # Murid, Ustadz, Wali Murid, Administrator, Ruangan, Mapel
│   │   │   ├── Pengaturan/        # User, Tahun Pelajaran, Pengumuman, Backup
│   │   │   ├── PengaturanMenu/    # RBAC (Role, Permission, Menu Navigasi)
│   │   │   ├── Spmb/              # Pendaftaran Santri Baru (Publik & Admin)
│   │   │   ├── Tabungan/          # Master Rekening, Setor, Tarik, Pecahan, Pembagian
│   │   │   └── Ujian/             # Jadwal Ujian, Persyaratan, Nilai Leger, Rapor
│   │   └── Requests/              # Form Request Validation (dikelompokkan per domain)
│   │       ├── Akademik/
│   │       ├── Keuangan/
│   │       ├── MasterData/
│   │       └── Pengaturan/
│   ├── Models/
│   │   ├── Tabungan/              # Tabungan, TransaksiTabungan, PeriodeTabungan, dsb.
│   │   ├── KasRuangan/            # KasRuangan Models
│   │   ├── Kepengurusan/          # Pengurus, Anggota, Jabatan
│   │   ├── KonfigurasiMenu/       # Menu, Permission
│   │   ├── Ujian/                 # Dispensasi, dsb.
│   │   └── [Murid, Ustadz, WaliMurid, TahunPelajaran, Ruangan, dsb.]
│   ├── Repositories/              # Query builder kompleks terisolasi
│   └── Services/                  # Business Logic layer (TabunganService, NilaiUjianService, dsb.)
├── database/
│   ├── migrations/                # Skema basis data terurut timestamp
│   └── seeders/                   # Seeder RBAC, Menu, Data Awal
├── resources/
│   └── views/
│       ├── components/            # Reusable Material 3 Blade UI Components
│       ├── layouts/               # Layout dasar (app, guest, cetak)
│       └── [domain_views]/        # View dikelompokkan per fitur (murid, user, tabungan, dsb.)
└── routes/
    ├── web.php                    # Seluruh rute Web & Admin (terkelompok rapi)
    └── api.php                    # Rute API / Mobile Integration
```

---

## 4. SISTEM RBAC, ROLE & MENU DINAMIS

Aplikasi menerapkan sistem hak akses berbasis peran (_Role-Based Access Control_):

### A. Jenis Role Utama:

1. **`administrator`** (Super Admin): Akses penuh ke seluruh menu dan pengaturan sistem. Bersifat global (tanpa batasan `tingkat_id`).
2. **`petugas-tabungan`**: Akses khusus untuk operasional keuangan tabungan madrasah (buka rekening, setor, tarik, rincian pecahan, pembagian). Bersifat global.
3. **`bendahara`**: Akses modul keuangan, tagihan SPP murid, dan rekap kas. Bersifat global.
4. **`staff`**: Tenaga administrasi yang dapat ditugaskan ke **Tingkat Tertentu** (`tingkat_id`) untuk membatasi cakupan murid/kelas yang dikelola.

### B. Menu Navigasi Dinamis

Menu yang muncul di sidebar tidak di-_hardcode_, melainkan tersimpan di tabel `menus` & `menu_permissions`.

- Pengaturan menu dapat dikonfigurasi melalui GUI di `/pengaturan-menu/menu`.
- Icon menggunakan Bootstrap Icons (`bi bi-*`).

---

## 5. PANDUAN LANGKAH DEMI LANGKAH MENAMBAH FITUR BARU

Saat Anda diminta atau ingin menambahkan fitur baru (misalnya: _Modul Beasiswa Murid_), ikuti 8 langkah standar berikut:

```
[1. Migration & Model] ➡️ [2. Form Request] ➡️ [3. Service Logic] ➡️ [4. Controller]
         ⬇️
[8. Testing & QA] ⬅️ [7. RBAC & Menu] ⬅️ [6. Blade Views UI] ⬅️ [5. Routes web.php]
```

### Langkah 1: Buat Migration & Model

Jalankan artisan command untuk membuat model beserta migration-nya:

```bash
php artisan make:model Beasiswa/BeasiswaMurid -m
```

- Tempatkan file migration di `database/migrations/`.
- Definisikan foreign keys dengan benar (`foreignId('murid_id')->constrained('murids')->cascadeOnDelete()`).
- Di dalam model, atur `$guarded = ['id']`, `$casts`, dan relasi `belongsTo` / `hasMany`.

### Langkah 2: Buat Form Request (Validasi Input)

Jangan menulis validasi langsung di controller. Buat Form Request terpisah di folder domain terkait:

```bash
php artisan make:request Keuangan/BeasiswaRequest
```

- Definisikan rules validasi, pesan error kustom dalam Bahasa Indonesia, dan otorisasi `authorize() => true`.

### Langkah 3: Buat / Perluas Service Layer (Jika Logika Kompleks)

Jika fitur melibatkan kalkulasi keuangan, mutasi multi-tabel, atau transaksi penting:

- Buat service di `app/Services/` (contoh: `app/Services/Keuangan/BeasiswaService.php`).
- Gunakan `DB::transaction(function () { ... })` agar data konsisten.

### Langkah 4: Buat Controller

Buat controller di sub-folder domain yang sesuai:

```bash
php artisan make:controller Keuangan/BeasiswaMuridController
```

- Gunakan _Dependency Injection_ untuk memanggil Service atau Model.
- Buat method RESTful standar: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`.
- Untuk interaksi cepat, sediakan respon JSON untuk request AJAX (`if ($request->ajax())`).

### Langkah 5: Daftarkan Rute di `routes/web.php`

Buka `routes/web.php` dan tambahkan rute di kelompok domain yang sesuai dengan proteksi auth & middleware:

```php
// Keuangan - Beasiswa Murid
Route::prefix('beasiswa-murid')->name('beasiswa-murid.')->group(function () {
    Route::get('/', [BeasiswaMuridController::class, 'index'])->name('index');
    Route::post('/', [BeasiswaMuridController::class, 'store'])->name('store');
    Route::put('/{id}', [BeasiswaMuridController::class, 'update'])->name('update');
    Route::delete('/{id}', [BeasiswaMuridController::class, 'destroy'])->name('destroy');
});
```

### Langkah 6: Buat Blade View (Material 3 Theme)

Buat folder view di `resources/views/` (contoh: `resources/views/beasiswa/`):

- Gunakan layout standar `<x-app-layout>`.
- Manfaatkan komponen UI yang sudah ada:
    - `<x-toggle-status>` : Switch status AJAX.
    - `<x-search-filter>` / `<x-filter-dropdown>` : Toolbar filter responsif.
    - `m3-glass-card`, `m3-btn-primary`, `m3-input-glass` : Styling CSS konsisten.

### Langkah 7: Daftarkan Permission & Menu RBAC

Agar menu baru muncul di sidebar dan dapat diakses sesuai role:

1. Tambahkan permission di database / seeder (contoh: `kelola-beasiswa`).
2. Masukkan menu baru ke tabel `menus` melalui `/pengaturan-menu/menu` atau seeder `DatabaseSeeder.php`.
3. Hubungkan menu tersebut ke role yang diizinkan (misal: `administrator` dan `bendahara`).

### Langkah 8: Pengujian & Verifikasi

- Jalankan browser dan uji alur CRUD.
- Pastikan filter tahun pelajaran / ruangan berfungsi.
- Periksa konsol browser (F12) untuk memastikan tidak ada error JavaScript / AJAX 500.

---

## 6. DAFTAR MODUL & FITUR UTAMA

| Domain            | Fitur Utama                                                                                       | Rute Utama                                                                              |
| :---------------- | :------------------------------------------------------------------------------------------------ | :-------------------------------------------------------------------------------------- |
| **Master Data**   | Murid, Ustadz, Wali Murid, Admin, Ruangan, Tingkat, Mapel, Cetak Kartu Pelajar                    | `/murid`, `/ustadz`, `/ruangan`, dll.                                                   |
| **Akademik**      | Rombel, Jadwal Pelajaran, Presensi Murid/Guru, Rekap Pelanggaran, Kalender                        | `/rombongan-belajar`, `/presensi-murid`, dll.                                           |
| **Ujian & Rapor** | Jadwal Ujian, Syarat Ujian, Input Leger Nilai, Bintang Pelajar, Cetak Rapor                       | `/nilai-ujian`, `/rapor`, `/bintang-pelajar`                                            |
| **Keuangan SPP**  | Tagihan Bulanan Murid, Pembayaran Kasir, Pembayaran Donatur, Kas Ruangan                          | `/tagihan-murid`, `/kas-ruangan`                                                        |
| **Tabungan**      | Master Rekening, Setor/Tarik, Cek Mutasi, Verifikasi Fisik, Rincian Uang Pecahan, Pembagian Akhir | `/tabungan/dashboard`, `/tabungan/rekening`, `/tabungan/pecahan`, `/tabungan/pembagian` |
| **Koperasi POS**  | Kasir POS Toko, Paket Bundling Kitab per Kelas, Potong Tabungan, Kartu Stok, Laporan Penjualan    | `/koperasi/dashboard`, `/koperasi/pos`, `/koperasi/paket`, `/koperasi/laporan`          |
| **SPMB**          | Formulir Pendaftaran Santri Baru Publik, Cek Status, Panel Verifikasi Admin                       | `/spmb`, `/spmb-admin`                                                                  |
| **Pengaturan**    | Manajemen User Online/Offline, Reset Password, RBAC Role & Permission, Menu Builder, Backup DB    | `/pengguna`, `/pengaturan-menu/menu`, `/pengaturan/tahun-pelajaran`                     |

---

## 7. KONVENSI KODE & STANDAR PRAKTIK TERBAIK (BEST PRACTICES)

1. **Selalu Gunakan `DB::transaction()` untuk Operasi Finansial:**
   Setiap transaksi tabungan, pembayaran tagihan, atau pembagian uang wajib dibungkus dalam database transaction dengan `lockForUpdate()` untuk mencegah _race condition_.
2. **Jangan Menghapus Data Sembarangan (Gunakan Soft Deletes jika Diperlukan):**
   Untuk data krusial seperti Murid atau Rekening Tabungan, pastikan cek relasi transaksi sebelum menghapus.
3. **Format Standar Respon AJAX JSON:**
    ```json
    {
        "success": true,
        "message": "Data berhasil disimpan.",
        "data": { ... }
    }
    ```
4. **Keamanan URL Bertanda Tangan (Signed Route):**
   Fitur cetak publik atau verifikasi barcode profil publik menggunakan middleware `signed` untuk mencegah manipulasi ID.

---

_Dokumentasi ini diperbarui secara berkala sesuai perkembangan arsitektur MDT Hidayatus Shibyan._
