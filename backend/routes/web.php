<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\Arsip\ArsipDokumenController;
use App\Http\Controllers\Arsip\ArsipIjazahController;
use App\Http\Controllers\Arsip\ArsipRaporController;
use App\Http\Controllers\Arsip\ArsipSKController;
use App\Http\Controllers\PetugasCetakController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\KalendarPendidikanController;
use App\Http\Controllers\KampungController;
use App\Http\Controllers\KartuPelajarController;
use App\Http\Controllers\KasRuangan\KasRuanganController;
use App\Http\Controllers\KasRuangan\PengaturanKasRuanganController;
use App\Http\Controllers\KasRuangan\SetoranKasRuanganController;
use App\Http\Controllers\KategoriKegiatanController;
use App\Http\Controllers\Kepengurusan\AnggotaController;
use App\Http\Controllers\Kepengurusan\JabatanPengurusController;
use App\Http\Controllers\Kepengurusan\PengurusController;
use App\Http\Controllers\Kepengurusan\PeriodeKepengurusanController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PelanggaranMuridController;
use App\Http\Controllers\PembayaranTagihanController;
use App\Http\Controllers\PengaturanAkademikController;
use App\Http\Controllers\PengaturanMenu\MenuController;
use App\Http\Controllers\PengaturanMenu\PermissionController;
use App\Http\Controllers\PengaturanMenu\RoleController;
use App\Http\Controllers\PengaturanTagihanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PresensiMuridController;
use App\Http\Controllers\PresensiUstadzController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\ReferensiPelanggaranController;
use App\Http\Controllers\RombonganBelajarController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TagihanMuridController;
use App\Http\Controllers\TahunPelajaranController;
use App\Http\Controllers\TingkatController;
use App\Http\Controllers\Ujian\BintangPelajarController;
use App\Http\Controllers\Ujian\JadwalUjianController;
use App\Http\Controllers\Ujian\NilaiUjianController;
use App\Http\Controllers\Ujian\PembayaranUjianController;
use App\Http\Controllers\Ujian\PersyaratanUjianController;
use App\Http\Controllers\Ujian\PresensiUjianController;
use App\Http\Controllers\Ujian\RaporController;
use App\Http\Controllers\Ujian\RiwayatKenaikanController;
use App\Http\Controllers\Ujian\UjianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UstadzController;
use App\Http\Controllers\WaliMuridController;
use App\Http\Controllers\SpmbController;
use App\Http\Controllers\Admin\SpmbAdminController;
use Illuminate\Support\Facades\Route;

// ==========================================
// SPMB ONLINE (PUBLIK / AUTH LAYOUT)
// ==========================================
Route::prefix('spmb')->name('spmb.')->group(function () {
    Route::get('/', [SpmbController::class, 'index'])->name('index');
    Route::get('/form', [SpmbController::class, 'form'])->name('form');
    Route::post('/check-kk', [SpmbController::class, 'checkKk'])->name('check-kk');
    Route::get('/daftar-wali', [SpmbController::class, 'formDaftarWali'])->name('daftar-wali');
    Route::post('/daftar-wali', [SpmbController::class, 'storeWali'])->name('store-wali');
    Route::get('/daftar-santri/{wali_murid_id}', [SpmbController::class, 'formDaftarSantri'])->name('daftar-santri');
    Route::post('/daftar-santri', [SpmbController::class, 'storeSantri'])->name('store-santri');
    Route::post('/', [SpmbController::class, 'store'])->name('store');
    Route::get('/search-kk', [SpmbController::class, 'searchKk'])->name('search-kk');
    Route::get('/bukti/{nomor_pendaftaran}', [SpmbController::class, 'bukti'])->name('bukti');
    Route::get('/bukti/{nomor_pendaftaran}/cetak', [SpmbController::class, 'cetakBukti'])->name('cetak-bukti');
    Route::get('/status', [SpmbController::class, 'cekStatus'])->name('cek-status');
});

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/verifikasi-profil/{tipe}/{id}', PublicProfileController::class)
    ->name('profil.publik')
    ->middleware('signed');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/petugas-cetak', [PetugasCetakController::class, 'index'])->name('petugas-cetak.index');

    // Route untuk fitur Notifikasi

    Route::post('/notifikasi/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifikasi.markAllRead');
    Route::get('/notifikasi/{notification}', NotificationController::class)->name('notifikasi.show');
    // ==========================================
    // 1. PROFIL PENGGUNA
    // ==========================================
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
        Route::patch('/administrator', 'updateAdministrator')->name('administrator.update');
    });

    // ==========================================
    // 2. MASTER PENGGUNA (USERS)
    // ==========================================

    Route::resource('jabatan-pengurus', JabatanPengurusController::class)->names('jabatan-pengurus');
    Route::resource('periode-pengurus', PeriodeKepengurusanController::class)->names('periode-pengurus');
    Route::controller(AnggotaController::class)
        ->prefix('anggota')
        ->name('anggota.')
        ->group(function () {
            Route::get('/', 'index')->name('index');                // Route: anggota.index
            Route::get('/create', 'create')->name('create');        // Route: anggota.create
            Route::post('/', 'store')->name('store');               // Route: anggota.store
            Route::get('/{anggota}/edit', 'edit')->name('edit');    // Route: anggota.edit
            Route::put('/{anggota}', 'update')->name('update');     // Route: anggota.update
            Route::delete('/{anggota}', 'destroy')->name('destroy'); // Route: anggota.destroy
        });
    Route::resource('pengurus', PengurusController::class)->names('pengurus');

    // -- Administrator --
    Route::prefix('administrator')->name('administrator.')->group(function () {
        Route::post('/{id}/toggle-status', [AdministratorController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{administrator}/resend-verification', [AdministratorController::class, 'resendVerification'])->name('resend-verification');
        Route::get('/{id}/signature', [AdministratorController::class, 'signature'])->name('signature');
        Route::post('/{id}/signature', [AdministratorController::class, 'updateSignature'])->name('signature.update');
    });
    Route::resource('administrator', AdministratorController::class);

    // -- Ustadz --
    Route::prefix('ustadz')->name('ustadz.')->group(function () {
        Route::post('/{id}/toggle-status', [UstadzController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/import', [UstadzController::class, 'modalImport'])->name('import');
        Route::post('/import', [UstadzController::class, 'import'])->name('import.store');
        Route::get('/template-import', [UstadzController::class, 'template'])->name('template');
        Route::post('/{ustadz}/resend-verification', [UstadzController::class, 'resendVerification'])->name('resend-verification');
        Route::get('/{id}/signature', [UstadzController::class, 'signature'])->name('signature');
        Route::post('/{id}/signature', [UstadzController::class, 'updateSignature'])->name('signature.update');
    });
    Route::resource('ustadz', UstadzController::class);

    // -- Wali Murid --
    Route::prefix('wali-murid')->name('wali-murid.')->group(function () {
        Route::get('/cetak', [WaliMuridController::class, 'cetak'])->name('cetak');
        Route::get('/export-excel', [WaliMuridController::class, 'exportExcel'])->name('export-excel');
        Route::get('/import', [WaliMuridController::class, 'modalImport'])->name('import');
        Route::post('/import', [WaliMuridController::class, 'import'])->name('import.store');
        Route::get('/template-import', [WaliMuridController::class, 'template'])->name('template');
        Route::get('/search-kk', [WaliMuridController::class, 'searchKk'])->name('searchKk');
        Route::post('/{id}/link-anak', [WaliMuridController::class, 'linkAnak'])->name('link-anak');
        Route::post('/{id}/unlink-anak/{murid_id}', [WaliMuridController::class, 'unlinkAnak'])->name('unlink-anak');
    });
    Route::resource('wali-murid', WaliMuridController::class);

    // -- SPMB Admin --
    Route::prefix('spmb-admin')->name('spmb-admin.')->controller(SpmbAdminController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/export-excel', 'exportExcel')->name('export-excel');
        Route::get('/scan/{nomor}', 'scan')->name('scan');
        Route::get('/{id}/detail-json', 'getDetailJson')->name('detail-json');
        Route::post('/{id}/verifikasi', 'verifikasi')->name('verifikasi');
        Route::post('/{id}/tolak', 'tolak')->name('tolak');
        Route::get('/{id}/cetak-diterima', 'cetakBuktiPenerimaan')->name('cetak-diterima');
    });

    // -- Murid --
    Route::prefix('murid')->name('murid.')->group(function () {
        Route::get('/yatim', [MuridController::class, 'filterYatim'])->name('yatim');
        Route::get('/yatim/download', [MuridController::class, 'downloadYatim'])->name('downloadYatim');
        Route::get('/import', [MuridController::class, 'modalImport'])->name('import');
        Route::post('/import', [MuridController::class, 'import'])->name('import.store');
        Route::get('/template-import', [MuridController::class, 'template'])->name('template');
        Route::patch('/{id}/status', [MuridController::class, 'updateStatus'])->name('updateStatus');
        Route::patch('/{id}/update-foto', [MuridController::class, 'updateFoto'])->name('updateFoto');
    });
    Route::resource('murid', MuridController::class);

    Route::get('/kartu-pelajar', [KartuPelajarController::class, 'index'])->name('kartu-pelajar.index');
    Route::post('/kartu-pelajar/cetak', [KartuPelajarController::class, 'cetak'])->name('kartu-pelajar.cetak');

    // -- Umum --
    Route::resource('user', UserController::class);

    // ==========================================
    // 3. MASTER DATA (REFERENSI)
    // ==========================================
    Route::resource('kategori-kegiatan', KategoriKegiatanController::class)->except(['index', 'show']);
    Route::resource('pengumuman', PengumumanController::class);
    Route::resource('kampung', KampungController::class);

    Route::post('/tingkat/{id}/toggle-status', [TingkatController::class, 'toggleStatus'])->name('tingkat.toggle-status');
    Route::resource('tingkat', TingkatController::class);

    Route::post('/tahun-pelajaran/{id}/toggle-status', [TahunPelajaranController::class, 'toggleStatus'])->name('tahun-pelajaran.toggle-status');
    Route::resource('tahun-pelajaran', TahunPelajaranController::class);

    Route::post('/level/{id}/toggle-status', [LevelController::class, 'toggleStatus'])->name('level.toggle-status');
    Route::resource('level', LevelController::class)->except(['show']);

    Route::prefix('mata-pelajaran')->name('mata-pelajaran.')->group(function () {
        Route::get('/level/{level_id}', [MataPelajaranController::class, 'levelShow'])->name('level');
        Route::get('/level/{level_id}/create', [MataPelajaranController::class, 'create'])->name('level.create');
        Route::get('/level/{level_id}/edit/{mapel_id}', [MataPelajaranController::class, 'edit'])->name('level.edit');
        Route::post('level/{id}/toggle-status', [MataPelajaranController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/import', [MataPelajaranController::class, 'modalImport'])->name('import');
        Route::get('/template/download', [MataPelajaranController::class, 'template'])->name('template');
        Route::post('/import', [MataPelajaranController::class, 'import'])->name('import.store');
    });
    Route::resource('mata-pelajaran', MataPelajaranController::class)->except(['create', 'show', 'edit']);


    Route::prefix('jadwal-pelajaran')->name('jadwal-pelajaran.')->group(function () {
        Route::get('/ruangan/{ruangan_id}', [JadwalPelajaranController::class, 'ruanganShow'])->name('ruangan');
        Route::post('/ruangan/{id}/mass-store', [JadwalPelajaranController::class, 'massStore'])
            ->name('mass-store');
        Route::get('/induk', [JadwalPelajaranController::class, 'jadwalInduk'])->name('induk');
        Route::patch('/ruangan/{ruangan_id}/toggle-publikasi', [JadwalPelajaranController::class, 'togglePublikasi'])->name('toggle-publikasi');
        Route::get('/cetak-leger', [JadwalPelajaranController::class, 'cetakLeger'])->name('cetak-leger');
    });
    Route::resource('jadwal-pelajaran', JadwalPelajaranController::class)->except(['create', 'edit', 'show']);

    Route::prefix('referensi-pelanggaran')->name('referensi-pelanggaran.')->group(function () {
        Route::get('/template/download', [ReferensiPelanggaranController::class, 'template'])->name('template');
        Route::get('/import', [ReferensiPelanggaranController::class, 'modalImport'])->name('import');
        Route::get('/template/download', [ReferensiPelanggaranController::class, 'template'])->name('template');
        Route::post('/import', [ReferensiPelanggaranController::class, 'import'])->name('import.store');
    });
    Route::resource('referensi-pelanggaran', ReferensiPelanggaranController::class);






    // ==========================================
    // 4. AKADEMIK & RUANGAN
    // ==========================================
    // -- Ruangan --
    Route::prefix('ruangan')->name('ruangan.')->group(function () {
        Route::post('/{id}/toggle-status', [RuanganController::class, 'toggleStatus'])->name('toggle-status');
    });
    Route::resource('ruangan', RuanganController::class)->except(['show']);


    Route::prefix('rombongan-belajar')->name('rombongan-belajar.')->group(function () {
        Route::get('/{id}/anggota', [RombonganBelajarController::class, 'anggota'])->name('anggota');
        Route::post('/{id}/anggota/attach', [RombonganBelajarController::class, 'attachAnggota'])->name('attach-anggota');
        Route::post('/{id}/anggota/detach/{murid_id}', [RombonganBelajarController::class, 'detachAnggota'])->name('detach-anggota');
        Route::get('/{id}/plotting-kenaikan', [RombonganBelajarController::class, 'plottingKenaikan'])->name('plotting-kenaikan');
        Route::post('/{id}/plotting-kenaikan', [RombonganBelajarController::class, 'storePlotting'])->name('store-plotting');
        Route::post('/{id}/pindah-anggota', [RombonganBelajarController::class, 'pindahAnggota'])->name('pindah-anggota');
        Route::get('/{id}/uploadFoto/{murid_id}', [RombonganBelajarController::class, 'modalUpload'])->name('uploadFoto');

        // Print & Export
        Route::get('/{id}/print-pembayaran-anggota', [RombonganBelajarController::class, 'printPembayaranAnggota'])->name('print-pembayaran-anggota');
        Route::get('/{id}/print-penilaian-anggota', [RombonganBelajarController::class, 'printPenilaianAnggota'])->name('print-penilaian-anggota');
        Route::get('/{id}/print-anggota', [RombonganBelajarController::class, 'printAnggota'])->name('print-anggota');
        Route::get('/{id}/export-anggota', [RombonganBelajarController::class, 'exportAnggota'])->name('export-anggota');
    });
    Route::resource('rombongan-belajar', RombonganBelajarController::class)->except(['show', 'create', 'edit', 'update', 'destroy']);

    // -- Pengaturan Akademik --
    Route::prefix('pengaturan-akademik')->name('pengaturan-akademik.')->controller(PengaturanAkademikController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/konfig', 'updateKonfig')->name('update-konfig');

        Route::get('/semester/{tahun_id?}', 'createSemester')->name('create-semester');
        Route::post('/semester', 'storeSemester')->name('store-semester');
        Route::get('/semester/{id}/edit', 'editSemester')->name('edit-semester');
        Route::put('/semester/{id}/update', 'updateSemester')->name('update-semester');
        Route::post('/semester/{id}/aktif', 'activateSemester')->name('activate-semester');
        Route::delete('/semester/{id}/destroy', 'destroySemester')->name('destroy-semester');

        Route::get('/bulan/{tahun_id}', 'createBulan')->name('create-bulan');
        Route::post('/bulan', 'storeBulan')->name('store-bulan');
        Route::get('/bulan/{id}/edit', 'editBulan')->name('edit-bulan');
        Route::put('/bulan/{id}/update', 'updateBulan')->name('update-bulan');
        Route::post('/bulan/{id}/aktif', 'activateBulan')->name('activate-bulan');
        Route::delete('/bulan/{id}/destroy', 'destroyBulan')->name('destroy-bulan');
    });

    // -- Kalender Pendidikan --
    Route::prefix('kalendar-pendidikan')->name('kalendar-pendidikan.')->controller(KalendarPendidikanController::class)->group(function () {
        Route::get('/matriks', 'matriksKalender')->name('matriks');
        Route::get('/matriks/set-bulan', 'setBulanMatriks')->name('matriks.set-bulan');
        Route::get('/matriks/create-agenda', 'createAgendaMatriks')->name('matriks.create-agenda');
        Route::post('/storebymatriks', 'storeBulanByMatriks')->name('matriks.store-bulanbymatriks');

        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });


    Route::prefix('presensi-murid')->name('presensi-murid.')->group(function () {
        Route::get('/', [PresensiMuridController::class, 'index'])->name('index');
        Route::post('/store', [PresensiMuridController::class, 'storeHarian'])->name('storeHarian');
        Route::get('/bulanan', [PresensiMuridController::class, 'bulanan'])->name('bulanan');
        Route::post('/bulanan/store', [PresensiMuridController::class, 'storeBulanan'])->name('store');
        Route::get('/rekap', [PresensiMuridController::class, 'rekap'])->name('rekap');
        Route::get('/cetak-rekap', [PresensiMuridController::class, 'cetakRekap'])->name('cetak-rekap');
    });

    Route::prefix('presensi-ustadz')->name('presensi-ustadz.')->group(function () {
        Route::get('/', [PresensiUstadzController::class, 'index'])->name('index');
        Route::post('/harian/store', [PresensiUstadzController::class, 'storeHarian'])->name('storeHarian');
        Route::delete('/harian/{id}/destroy', [PresensiUstadzController::class, 'destroyHarian'])->name('destroyHarian');
        Route::get('/bulanan', [PresensiUstadzController::class, 'bulanan'])->name('bulanan');
        Route::post('/bulanan/store', [PresensiUstadzController::class, 'storeBulanan'])->name('storeBulanan');
        Route::get('/rekap-semua', [PresensiUstadzController::class, 'rekapSemua'])->name('rekapSemua');
        Route::get('/cetak-rekap', [PresensiUstadzController::class, 'cetakRekap'])->name('cetak-rekap');
        Route::get('/rekap-semua/export', [PresensiUstadzController::class, 'exportExcel'])->name('exportExcel');
    });

    Route::prefix('pelanggaran-murid')->name('pelanggaran-murid.')->group(function () {
        Route::get('/', [PelanggaranMuridController::class, 'index'])->name('index');
        Route::post('/harian/store', [PelanggaranMuridController::class, 'storeHarian'])->name('storeHarian');
        Route::delete('/harian/{id}/destroy', [PelanggaranMuridController::class, 'destroyHarian'])->name('destroyHarian');
        Route::get('/massal', [PelanggaranMuridController::class, 'massal'])->name('massal');
        Route::post('/massal/store', [PelanggaranMuridController::class, 'storeMassal'])->name('storeMassal');
        Route::get('/admin-mode', [PelanggaranMuridController::class, 'adminMode'])->name('adminMode');
        Route::post('/admin-mode/sync', [PelanggaranMuridController::class, 'syncAdminMode'])->name('syncAdminMode');
        Route::get('/rekap', [PelanggaranMuridController::class, 'rekap'])->name('rekap');
        Route::get('/rekap/export', [PelanggaranMuridController::class, 'exportExcel'])->name('exportExcel');
    });


    Route::resource('ujian', UjianController::class)->names('ujian');

    Route::get('/jadwal-ujian/cetak-leger', [JadwalUjianController::class, 'cetakLeger'])->name('jadwal-ujian.cetak-leger');
    Route::get('/jadwal-ujian/create', [JadwalUjianController::class, 'create'])->name('jadwal-ujian.create');
    Route::post('/jadwal-ujian/store', [JadwalUjianController::class, 'store'])->name('jadwal-ujian.store');
    Route::resource('jadwal-ujian', JadwalUjianController::class)->names('jadwal-ujian')->except(['create', 'store']);

    Route::get('persyaratan-ujian', [PersyaratanUjianController::class, 'index'])->name('persyaratan-ujian.index');
    Route::post('persyaratan-ujian/dispensasi', [PersyaratanUjianController::class, 'beriDispensasi'])->name('persyaratan-ujian.dispensasi');

    // -- Presensi Ujian --
    Route::prefix('presensi-ujian')->name('presensi-ujian.')->group(function () {
        Route::get('/', [PresensiUjianController::class, 'index'])->name('index');
        Route::get('/input', [PresensiUjianController::class, 'inputPresensi'])->name('input');
        Route::post('/store', [PresensiUjianController::class, 'store'])->name('store');
        Route::get('/rekap', [PresensiUjianController::class, 'rekap'])->name('rekap');
        Route::get('/cetak-menu', [PresensiUjianController::class, 'cetakMenu'])->name('cetak-menu');
        Route::get('/cetak-dhpu', [PresensiUjianController::class, 'cetakDhpu'])->name('cetak-dhpu');
        Route::get('/cetak-berita-acara', [PresensiUjianController::class, 'cetakBeritaAcara'])->name('cetak-berita-acara');
        Route::get('/cetak-rekap', [PresensiUjianController::class, 'cetakRekap'])->name('cetak-rekap');
    });

    Route::get('/nilai-ujian', [NilaiUjianController::class, 'index'])->name('nilai-ujian.index');
    Route::get('nilai-ujian/input-nilai', [NilaiUjianController::class, 'inputNilai'])->name('nilai-ujian.input-nilai');
    Route::post('nilai-ujian/store', [NilaiUjianController::class, 'simpanNilai'])->name('nilai-ujian.store');
    Route::get('/input-leger', [NilaiUjianController::class, 'inputLeger'])->name('nilai-ujian.input-leger');
    Route::post('/input-leger/store', [NilaiUjianController::class, 'storeLeger'])->name('nilai-ujian.input-leger.store');
    Route::get('nilai-ujian/laporan-leger', [NilaiUjianController::class, 'laporanLeger'])->name('nilai-ujian.laporan-leger');

    Route::get('/bintang-pelajar', [BintangPelajarController::class, 'bintangPelajar'])->name('bintang-pelajar.index');
    Route::get('/bintang-madrasah', [BintangPelajarController::class, 'bintangMadrasah'])->name('bintang-madrasah.index');

    Route::prefix('kenaikan-kelas')->name('kenaikan-kelas.')->group(function () {
        Route::get('/', [RiwayatKenaikanController::class, 'index'])->name('index');
        Route::post('/simpan', [RiwayatKenaikanController::class, 'simpan'])->name('simpan');
        Route::get('/cetak-sk/{tahun_id}/{ruangan_id}/{murid_id}', [RiwayatKenaikanController::class, 'cetak_sk'])->name('cetak_sk');
        Route::get('/cetak-sk/{tahun_id}/{ruangan_id}/{murid_id}/pdf', [RiwayatKenaikanController::class, 'downloadSKPdf'])->name('cetak_sk_pdf');
        Route::get('/cetak-ijazah/{tahun_id}/{ruangan_id}/{murid_id}', [RiwayatKenaikanController::class, 'cetak_ijazah'])->name('cetak_ijazah');
    });

    Route::get('/rapor', [RaporController::class, 'index'])->name('rapor.index');
    Route::get('/rapor/cetak/{ujian_id}/{murid_id}', [RaporController::class, 'cetakRapor'])->name('rapor.cetak');
    Route::post('/rapor/{murid_id}/ujian/{ujian_id}/arsipkan', [RaporController::class, 'arsipkanRapor'])->name('rapor.arsipkan');
    Route::post('/rapor/arsipkan-bulk', [RaporController::class, 'arsipkanBulk'])->name('rapor.arsipkan_bulk');




    Route::get('/arsip-rapor', [ArsipRaporController::class, 'index'])->name('arsip-rapor.index');
    Route::get('/arsip-sk', [ArsipSKController::class, 'index'])->name('arsip-sk.index');
    Route::get('/arsip-ijazah', [ArsipIjazahController::class, 'index'])->name('arsip-ijazah.index');

    Route::get('/arsip-dokumen/{id}/cetak', [ArsipDokumenController::class, 'cetak'])
        ->name('arsip.cetak');







    Route::prefix('pembayaran-ujian')->name('pembayaran-ujian.')->controller(PembayaranUjianController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/proses', 'proses')->name('proses');
        Route::post('/batal/{id}', 'batalTransaksi')->name('batal');
        Route::get('/cetak-rekap-spp/{murid_id}/{tahun_id}', 'cetakRekapSpp')->name('cetak-rekap-spp');
        Route::get('/cetak/{id}', 'cetakKwitansi')->name('cetak');

        Route::get('/laporan', 'laporan')->name('laporan');
    });







    // ==========================================
    // 5. KEUANGAN & TAGIHAN
    // ==========================================
    Route::get('/tagihan-murid/cetak-kartu-spp/{murid_id}/{tahun_id}', [TagihanMuridController::class, 'cetakKartuSpp'])->name('tagihan-murid.cetak-kartu-spp');
    Route::get('/tagihan-murid/cetak-kartu-spp-massal/{ruangan_id}/{tahun_id}', [App\Http\Controllers\TagihanMuridController::class, 'cetakKartuSppMassal'])
        ->name('tagihan-murid.cetak-kartu-spp-massal');
    Route::post('/tagihan-murid/proses', [TagihanMuridController::class, 'prosesTagihanPilihan'])->name('tagihan-murid.proses');
    Route::resource('tagihan-murid', TagihanMuridController::class);

    Route::prefix('pembayaran-tagihan')->name('pembayaran-tagihan.')->controller(PembayaranTagihanController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/proses', 'proses')->name('proses');
        Route::post('/batal/{id}', 'batalTransaksi')->name('batal');

        Route::get('/cetak-rekap-spp/{murid_id}/{tahun_id}', 'cetakRekapSpp')->name('cetak-rekap-spp');
        Route::get('/cetak/{id}', 'cetakKwitansi')->name('cetak');

        Route::get('/leger', 'indexLeger')->name('leger');
        Route::post('/leger/proses', 'prosesLeger')->name('leger.proses');

        Route::get('/donatur', 'indexDonatur')->name('donatur');
        Route::post('/donatur/proses', 'prosesDonatur')->name('donatur.proses');
        Route::get('/cetak-donatur/{id}', 'cetakDonatur')->name('cetak-donatur');

        Route::get('/laporan', 'laporan')->name('laporan');
    });

    Route::resource('pengaturan-tagihan', PengaturanTagihanController::class);





    // Rute Pengaturan
    Route::get('/pengaturan-kas-ruangan', [PengaturanKasRuanganController::class, 'indexPengaturan'])->name('pengaturan-kas-ruangan.index');
    Route::post('/pengaturan-kas-ruangan/auto-save', [PengaturanKasRuanganController::class, 'autoSavePengaturan'])->name('pengaturan-kas-ruangan.auto-save');

    Route::get('/kas-ruangan', [KasRuanganController::class, 'indexKasRuangan'])->name('kas-ruangan.index');
    Route::get('/kas-ruangan/{ruangan_id}', [KasRuanganController::class, 'showKasRuangan'])->name('kas-ruangan.show');
    Route::post('/kas-ruangan/bayar', [KasRuanganController::class, 'simpanPembayaran'])->name('kas-ruangan.bayar');
    Route::get('/kas-ruangan/{ruangan}/murid/{murid}/riwayat', [KasRuanganController::class, 'riwayat'])->name('kas-ruangan.riwayat');
    Route::put('/kas-ruangan/bayar/{id}', [KasRuanganController::class, 'updatePembayaran'])->name('kas-ruangan.bayar.update');
    Route::delete('/kas-ruangan/bayar/{id}', [KasRuanganController::class, 'destroyPembayaran'])->name('kas-ruangan.bayar.destroy');


    Route::get('/setoran-kas-ruangan', [SetoranKasRuanganController::class, 'indexSetoran'])->name('setoran-kas-ruangan.index');
    Route::get('/setoran-kas-ruangan/{ruangan}/riwayat', [SetoranKasRuanganController::class, 'riwayatSetoran'])->name('setoran-kas-ruangan.riwayat');
    Route::post('/setoran-kas-ruangan', [SetoranKasRuanganController::class, 'simpanSetoran'])->name('setoran-kas-ruangan.simpan');
    Route::put('/setoran-kas-ruangan/{id}', [SetoranKasRuanganController::class, 'updateSetoran'])->name('setoran-kas-ruangan.update');
    Route::delete('/setoran-kas-ruangan/{id}', [SetoranKasRuanganController::class, 'destroySetoran'])->name('setoran-kas-ruangan.destroy');


    Route::post('/pengguna/{id}/force-logout', [UserController::class, 'forceLogout'])->name('pengguna.force-logout');
    Route::get('/pengguna/{id}/whatsapp', [UserController::class, 'hubungiWhatsApp'])->name('pengguna.whatsapp');
    Route::resource('pengguna', UserController::class);
    // ==========================================
    // 6. PENGATURAN SISTEM & HAK AKSES
    // ==========================================
    // -- Manajemen Menu (Drag & Drop M3) --
    Route::post('/menu/update-order', [MenuController::class, 'updateOrder'])->name('menu.update-order');
    Route::resource('menu', MenuController::class);

    // -- Role & Permission (RBAC) --
    Route::post('roles/{id}/give-permissions', [RoleController::class, 'givePermissions'])->name('roles.give-permissions');
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class)->except(['show']);

    // -- Aplikasi & Backup --
    Route::get('/pengaturan-aplikasi', [SettingController::class, 'index'])->name('pengaturan-aplikasi.index');
    Route::post('/pengaturan-aplikasi', [SettingController::class, 'update'])->name('pengaturan-aplikasi.update');

    Route::prefix('backup')->name('backup.')->controller(BackupController::class)->group(function () {
        Route::get('/', 'index')->name('database');
        Route::post('/process', 'process')->name('process');
        Route::post('/restore', 'restore')->name('restore');
    });
});



Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');

require __DIR__ . '/auth.php';
