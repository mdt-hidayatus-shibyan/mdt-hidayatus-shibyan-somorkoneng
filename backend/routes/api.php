<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AkademikController;
use App\Http\Controllers\Api\PresensiMuridController;
use App\Http\Controllers\Api\PresensiUstadzController;
use App\Http\Controllers\Api\PelanggaranMuridController;
use App\Http\Controllers\Api\NilaiUjianController;
use App\Http\Controllers\Api\PresensiUjianController;
use App\Http\Controllers\Api\KasRuanganController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\MuridController;
use App\Http\Controllers\Api\LaporanController;

/*
|--------------------------------------------------------------------------
| API Routes - MDT Hidayatus Shibyan Mobile App (Role: Ustadz)
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. PUBLIC ROUTES
// =========================================================================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// =========================================================================
// 2. PROTECTED ROUTES (auth:sanctum)
// =========================================================================
Route::middleware('auth:sanctum')->group(function () {
    // 2.1 Autentikasi & Akun
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile/update-password', [AuthController::class, 'updatePassword']);
    Route::post('/profile/update-account', [AuthController::class, 'updateAccount']);
    Route::get('/ustadz/biodata', [AuthController::class, 'getBiodata']);
    Route::post('/ustadz/update-biodata', [AuthController::class, 'updateBiodata']);
    Route::post('/ustadz/update-foto', [AuthController::class, 'updateFoto']);
    Route::post('/ustadz/update-tanda-tangan', [AuthController::class, 'updateTandaTangan']);

    // 2.2 Dashboard & Pengumuman
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/pengumuman', [DashboardController::class, 'pengumuman']);

    // 2.3 Akademik & Menu Cepat Ustadz
    Route::get('/kalendar-pendidikan', [AkademikController::class, 'getKalendar']);
    Route::get('/referensi-pelanggaran', [AkademikController::class, 'getReferensiPelanggaran']);
    Route::get('/mata-pelajaran', [AkademikController::class, 'getMataPelajaran']);
    Route::get('/jadwal-pelajaran', [AkademikController::class, 'getJadwalPelajaran']);

    // 2.4 Presensi Murid & Ustadz
    Route::get('/presensi-murid/sesi', [PresensiMuridController::class, 'getSesi']);
    Route::get('/presensi-murid/murid', [PresensiMuridController::class, 'getMurid']);
    Route::post('/presensi-murid/simpan', [PresensiMuridController::class, 'simpan']);
    Route::get('/presensi-ustadz/sesi', [PresensiUstadzController::class, 'getSesi']);
    Route::post('/presensi-ustadz/checkin', [PresensiUstadzController::class, 'checkin']);
    Route::get('/presensi-ustadz/daftar-ustadz', [PresensiUstadzController::class, 'getDaftarUstadz']);
    Route::get('/presensi-ustadz/riwayat', [PresensiUstadzController::class, 'getRiwayat']);

    // 2.5 Buku Kasus & Pelanggaran Murid
    Route::get('/pelanggaran/referensi', [PelanggaranMuridController::class, 'getReferensi']);
    Route::get('/pelanggaran/ruangan-list', [PelanggaranMuridController::class, 'getRuanganList']);
    Route::get('/pelanggaran/murid-by-ruangan/{ruangan_id}', [PelanggaranMuridController::class, 'getMuridByRuangan']);
    Route::get('/pelanggaran/harian', [PelanggaranMuridController::class, 'getHarian']);
    Route::get('/pelanggaran/riwayat', [PelanggaranMuridController::class, 'getRiwayat']);
    Route::post('/pelanggaran/simpan', [PelanggaranMuridController::class, 'simpan']);
    Route::post('/pelanggaran/simpan-massal', [PelanggaranMuridController::class, 'simpanMassal']);
    Route::delete('/pelanggaran/{id}', [PelanggaranMuridController::class, 'destroy']);

    // 2.6 Penilaian & Leger Nilai Ujian
    Route::get('/ujian/list', [NilaiUjianController::class, 'getUjianList']);
    Route::get('/ujian/mapel-jadwal', [NilaiUjianController::class, 'getMapelJadwal']);
    Route::get('/ujian/input-data', [NilaiUjianController::class, 'getInputData']);
    Route::post('/ujian/simpan-nilai', [NilaiUjianController::class, 'simpanNilai']);
    Route::get('/ujian/leger', [NilaiUjianController::class, 'getLeger']);

    // 2.7 Presensi Ujian Santri & Pengawas
    Route::get('/presensi-ujian/data', [PresensiUjianController::class, 'getData']);
    Route::post('/presensi-ujian/simpan', [PresensiUjianController::class, 'simpan']);

    // 2.7 Kas Ruangan (Wali Ruangan)
    Route::get('/kas-ruangan/ringkasan', [KasRuanganController::class, 'getRingkasan']);
    Route::get('/kas-ruangan/murid-list', [KasRuanganController::class, 'getMuridList']);
    Route::post('/kas-ruangan/simpan-bayar', [KasRuanganController::class, 'simpanBayar']);
    Route::post('/kas-ruangan/update-bayar/{id}', [KasRuanganController::class, 'updateBayar']);
    Route::delete('/kas-ruangan/hapus-bayar/{id}', [KasRuanganController::class, 'hapusBayar']);
    Route::get('/kas-ruangan/riwayat-murid/{murid_id}', [KasRuanganController::class, 'getRiwayatMurid']);
    Route::get('/kas-ruangan/pengaturan', [KasRuanganController::class, 'getPengaturan']);
    Route::post('/kas-ruangan/pengaturan', [KasRuanganController::class, 'updatePengaturan']);
    Route::get('/kas-ruangan/setoran/riwayat', [KasRuanganController::class, 'getRiwayatSetoran']);
    Route::get('/kas-ruangan/setoran/penerima-list', [KasRuanganController::class, 'getPenerimaList']);
    Route::post('/kas-ruangan/setoran/simpan', [KasRuanganController::class, 'simpanSetoran']);
    Route::post('/kas-ruangan/setoran/update/{id}', [KasRuanganController::class, 'updateSetoran']);
    Route::delete('/kas-ruangan/setoran/hapus/{id}', [KasRuanganController::class, 'hapusSetoran']);

    // 2.8 Tagihan & SPP (Wali Ruangan)
    Route::get('/tagihan/spp/ringkasan', [TagihanController::class, 'getSppRingkasan']);
    Route::get('/tagihan/spp/murid-list', [TagihanController::class, 'getSppMuridList']);
    Route::get('/tagihan/spp/kartu/{murid_id}', [TagihanController::class, 'getKartuSppMurid']);

    // Tagihan Non-SPP (Semester & Insidental)
    Route::get('/tagihan/non-spp/master-list', [TagihanController::class, 'getNonSppMasterList']);
    Route::get('/tagihan/non-spp/ringkasan', [TagihanController::class, 'getNonSppRingkasan']);
    Route::get('/tagihan/non-spp/murid-list', [TagihanController::class, 'getNonSppMuridList']);
    Route::post('/tagihan/non-spp/bayar', [TagihanController::class, 'prosesBayarNonSpp']);
    Route::post('/tagihan/non-spp/batal-bayar/{tagihan_id}', [TagihanController::class, 'batalBayarNonSpp']);

    // 2.9 Direktori Murid
    Route::get('/murid/ruangan', [MuridController::class, 'getMuridRuangan']);

    // 2.10 Pusat Laporan Terpadu
    Route::get('/laporan/presensi-murid', [LaporanController::class, 'getLaporanPresensiMurid']);
    Route::get('/laporan/presensi-ustadz', [LaporanController::class, 'getLaporanPresensiUstadz']);
    Route::get('/laporan/pelanggaran-murid', [LaporanController::class, 'getLaporanPelanggaranMurid']);
    Route::get('/laporan/ujian', [LaporanController::class, 'getLaporanUjian']);
    Route::get('/laporan/kenaikan-kelas', [LaporanController::class, 'getLaporanKenaikanKelas']);
});
