<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\KonfigurasiMenu\Menu;
use App\Models\KonfigurasiMenu\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset Spatie Permission Cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Daftar Role Standar Profesional
        $rolesData = [
            'administrator'     => 'Administrator Utama (Super Admin)',
            'staff'             => 'Staff Administrasi / TU',
            'petugas-tabungan'  => 'Petugas Tabungan Madrasah',
            'bendahara'         => 'Bendahara / Keuangan Madrasah',
            'ustadz'            => 'Dewan Guru / Tenaga Pendidik',
            'petugas-cetak'     => 'Petugas Cetak Rapor & Dokumen',
            'wali-murid'        => 'Wali Murid',
        ];

        $roles = [];
        foreach ($rolesData as $roleName => $roleDesc) {
            $roles[$roleName] = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 3. Bersihkan tabel relasi menu_permission & menu sebelum re-seed
        Schema::disableForeignKeyConstraints();
        DB::table('menu_permission')->truncate();
        Menu::truncate();
        Schema::enableForeignKeyConstraints();

        // 4. Struktur Definisi Menu Navigasi Profesional
        $menuTree = [
            // ==========================================
            // KATEGORI 1: UTAMA
            // ==========================================
            [
                'name'        => 'Dashboard',
                'url'         => 'dashboard',
                'category'    => 'UTAMA',
                'icon'        => 'bi-grid-1x2-fill',
                'orders'      => 1,
                'permissions' => ['read'],
            ],
            [
                'name'        => 'Kalendar Pendidikan',
                'url'         => 'kalendar-pendidikan.index',
                'category'    => 'UTAMA',
                'icon'        => 'bi-calendar3',
                'orders'      => 2,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Pengumuman Madrasah',
                'url'         => 'pengumuman.index',
                'category'    => 'UTAMA',
                'icon'        => 'bi-megaphone-fill',
                'orders'      => 3,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Penerimaan Murid (SPMB)',
                'url'         => 'spmb-admin.index',
                'category'    => 'UTAMA',
                'icon'        => 'bi-person-plus-fill',
                'orders'      => 4,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Kartu Pelajar',
                'url'         => 'kartu-pelajar.index',
                'category'    => 'UTAMA',
                'icon'        => 'bi-person-vcard-fill',
                'orders'      => 5,
                'permissions' => ['read', 'create'],
            ],

            // ==========================================
            // KATEGORI 2: AKADEMIK
            // ==========================================
            [
                'name'        => 'Jadwal Pelajaran',
                'url'         => 'jadwal-pelajaran.index',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-calendar-week-fill',
                'orders'      => 10,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Rombongan Belajar (Rombel)',
                'url'         => 'rombongan-belajar.index',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-people-fill',
                'orders'      => 11,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Presensi Murid',
                'url'         => 'presensi-murid.index',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-person-check-fill',
                'orders'      => 12,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Presensi Ustadz',
                'url'         => 'presensi-ustadz.index',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-person-badge-fill',
                'orders'      => 13,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Pelanggaran Murid',
                'url'         => 'pelanggaran-murid.index',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-shield-exclamation',
                'orders'      => 14,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Ujian & Evaluasi',
                'url'         => '#ujian',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-mortarboard-fill',
                'orders'      => 15,
                'permissions' => ['read'],
                'sub_menus'   => [
                    [
                        'name'        => 'Master Ujian',
                        'url'         => 'ujian.index',
                        'icon'        => 'bi-journal-check',
                        'orders'      => 1,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Persyaratan Ujian',
                        'url'         => 'persyaratan-ujian.index',
                        'icon'        => 'bi-card-checklist',
                        'orders'      => 2,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Presensi Ujian',
                        'url'         => 'presensi-ujian.index',
                        'icon'        => 'bi-person-check',
                        'orders'      => 3,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Nilai Ujian',
                        'url'         => 'nilai-ujian.index',
                        'icon'        => 'bi-award-fill',
                        'orders'      => 4,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Pembayaran Ujian',
                        'url'         => 'pembayaran-ujian.index',
                        'icon'        => 'bi-cash-stack',
                        'orders'      => 5,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Kenaikan Kelas',
                        'url'         => 'kenaikan-kelas.index',
                        'icon'        => 'bi-arrow-up-right-circle-fill',
                        'orders'      => 6,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                ],
            ],
            [
                'name'        => 'Bintang Pelajar',
                'url'         => 'bintang-pelajar.index',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-star-fill',
                'orders'      => 16,
                'permissions' => ['read'],
            ],
            [
                'name'        => 'Cetak Rapor',
                'url'         => 'rapor.index',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-file-earmark-spreadsheet-fill',
                'orders'      => 17,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Petugas Cetak Rapor',
                'url'         => 'petugas-cetak.index',
                'category'    => 'AKADEMIK',
                'icon'        => 'bi-printer-fill',
                'orders'      => 18,
                'permissions' => ['read'],
            ],

            // ==========================================
            // KATEGORI 3: KEUANGAN & TABUNGAN
            // ==========================================
            [
                'name'        => 'Tagihan Syahriyah',
                'url'         => 'tagihan-murid.index',
                'category'    => 'KEUANGAN & TABUNGAN',
                'icon'        => 'bi-receipt-cutoff',
                'orders'      => 20,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Pembayaran Syahriyah',
                'url'         => 'pembayaran-tagihan.index',
                'category'    => 'KEUANGAN & TABUNGAN',
                'icon'        => 'bi-cash-coin',
                'orders'      => 21,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Kas Ruangan',
                'url'         => '#kas-ruangan',
                'category'    => 'KEUANGAN & TABUNGAN',
                'icon'        => 'bi-cash-stack',
                'orders'      => 22,
                'permissions' => ['read'],
                'sub_menus'   => [
                    [
                        'name'        => 'Setoran Kas Ruangan',
                        'url'         => 'setoran-kas-ruangan.index',
                        'icon'        => 'bi-wallet-fill',
                        'orders'      => 1,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Pengaturan Kas Ruangan',
                        'url'         => 'pengaturan-kas-ruangan.index',
                        'icon'        => 'bi-sliders',
                        'orders'      => 2,
                        'permissions' => ['read', 'create', 'update'],
                    ],
                ],
            ],
            [
                'name'        => 'Tabungan Madrasah',
                'url'         => '#tabungan',
                'category'    => 'KEUANGAN & TABUNGAN',
                'icon'        => 'bi-piggy-bank-fill',
                'orders'      => 23,
                'permissions' => ['read'],
                'sub_menus'   => [
                    [
                        'name'        => 'Dashboard Tabungan',
                        'url'         => 'tabungan.dashboard',
                        'icon'        => 'bi-speedometer2',
                        'orders'      => 1,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Master Rekening',
                        'url'         => 'tabungan.rekening.index',
                        'icon'        => 'bi-journal-bookmark-fill',
                        'orders'      => 2,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Setor Tunai',
                        'url'         => 'tabungan.setor.index',
                        'icon'        => 'bi-arrow-down-circle-fill',
                        'orders'      => 3,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Tarik Tunai',
                        'url'         => 'tabungan.tarik.index',
                        'icon'        => 'bi-arrow-up-circle-fill',
                        'orders'      => 4,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Cek Mutasi & Buku',
                        'url'         => 'tabungan.cek-mutasi.index',
                        'icon'        => 'bi-file-earmark-check-fill',
                        'orders'      => 5,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Kalkulator Pecahan Uang',
                        'url'         => 'tabungan.pecahan.index',
                        'icon'        => 'bi-calculator-fill',
                        'orders'      => 6,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Rincian & Rekap Kas',
                        'url'         => 'tabungan.rincian.index',
                        'icon'        => 'bi-bar-chart-line-fill',
                        'orders'      => 7,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Pembagian Akhir Tabungan',
                        'url'         => 'tabungan.pembagian.index',
                        'icon'        => 'bi-gift-fill',
                        'orders'      => 8,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Pengaturan Tabungan',
                        'url'         => 'tabungan.pengaturan.index',
                        'icon'        => 'bi-gear-fill',
                        'orders'      => 9,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                ],
            ],

            // ==========================================
            // KATEGORI 4: ARSIP & KESEKRETARIATAN
            // ==========================================
            [
                'name'        => 'Kepengurusan Madrasah',
                'url'         => '#kepengurusan',
                'category'    => 'ARSIP & KESEKRETARIATAN',
                'icon'        => 'bi-diagram-3-fill',
                'orders'      => 30,
                'permissions' => ['read'],
                'sub_menus'   => [
                    [
                        'name'        => 'Struktur Pengurus',
                        'url'         => 'pengurus.index',
                        'icon'        => 'bi-people-fill',
                        'orders'      => 1,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Jabatan Pengurus',
                        'url'         => 'jabatan-pengurus.index',
                        'icon'        => 'bi-award',
                        'orders'      => 2,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Periode Kepengurusan',
                        'url'         => 'periode-pengurus.index',
                        'icon'        => 'bi-calendar-range',
                        'orders'      => 3,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Anggota Pengurus',
                        'url'         => 'anggota.index',
                        'icon'        => 'bi-person-lines-fill',
                        'orders'      => 4,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                ],
            ],
            [
                'name'        => 'Arsip Dokumen',
                'url'         => '#arsip-dokumen',
                'category'    => 'ARSIP & KESEKRETARIATAN',
                'icon'        => 'bi-archive-fill',
                'orders'      => 31,
                'permissions' => ['read'],
                'sub_menus'   => [
                    [
                        'name'        => 'Arsip Rapor',
                        'url'         => 'arsip-rapor.index',
                        'icon'        => 'bi-file-earmark-text-fill',
                        'orders'      => 1,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Arsip SK',
                        'url'         => 'arsip-sk.index',
                        'icon'        => 'bi-file-earmark-ruled-fill',
                        'orders'      => 2,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Arsip Ijazah',
                        'url'         => 'arsip-ijazah.index',
                        'icon'        => 'bi-mortarboard-fill',
                        'orders'      => 3,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                ],
            ],

            // ==========================================
            // KATEGORI 5: LAYANAN & BANTUAN
            // ==========================================
            [
                'name'        => 'Laporan & Kendala Ustadz',
                'url'         => 'laporan-kendala-admin.index',
                'category'    => 'LAYANAN & BANTUAN',
                'icon'        => 'bi-headset',
                'orders'      => 40,
                'permissions' => ['read', 'update', 'delete'],
            ],

            // ==========================================
            // KATEGORI 6: MASTER DATA
            // ==========================================
            [
                'name'        => 'Data Induk',
                'url'         => '#master-data',
                'category'    => 'MASTER DATA',
                'icon'        => 'bi-database-fill',
                'orders'      => 50,
                'permissions' => ['read'],
                'sub_menus'   => [
                    [
                        'name'        => 'Administrator & Staff',
                        'url'         => 'administrator.index',
                        'icon'        => 'bi-person-badge-fill',
                        'orders'      => 1,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Ustadz / Dewan Guru',
                        'url'         => 'ustadz.index',
                        'icon'        => 'bi-person-workspace',
                        'orders'      => 2,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Wali Murid',
                        'url'         => 'wali-murid.index',
                        'icon'        => 'bi-people-fill',
                        'orders'      => 3,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Murid',
                        'url'         => 'murid.index',
                        'icon'        => 'bi-person-video3',
                        'orders'      => 4,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Tingkat Madrasah',
                        'url'         => 'tingkat.index',
                        'icon'        => 'bi-layer-backward',
                        'orders'      => 5,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Level / Kelas',
                        'url'         => 'level.index',
                        'icon'        => 'bi-grid-fill',
                        'orders'      => 6,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Ruangan Kelas',
                        'url'         => 'ruangan.index',
                        'icon'        => 'bi-door-open-fill',
                        'orders'      => 7,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Mata Pelajaran',
                        'url'         => 'mata-pelajaran.index',
                        'icon'        => 'bi-book-half',
                        'orders'      => 8,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Referensi Pelanggaran',
                        'url'         => 'referensi-pelanggaran.index',
                        'icon'        => 'bi-exclamation-octagon-fill',
                        'orders'      => 9,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Data Kampung / Dusun',
                        'url'         => 'kampung.index',
                        'icon'        => 'bi-geo-alt-fill',
                        'orders'      => 10,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                ],
            ],

            // ==========================================
            // KATEGORI 7: PENGATURAN
            // ==========================================
            [
                'name'        => 'Manajemen Akun Pengguna',
                'url'         => 'pengguna.index',
                'category'    => 'PENGATURAN',
                'icon'        => 'bi-person-gear',
                'orders'      => 60,
                'permissions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'name'        => 'Konfigurasi RBAC & Menu',
                'url'         => '#pengaturan-menu',
                'category'    => 'PENGATURAN',
                'icon'        => 'bi-shield-lock-fill',
                'orders'      => 61,
                'permissions' => ['read'],
                'sub_menus'   => [
                    [
                        'name'        => 'Manajemen Menu Navigasi',
                        'url'         => 'menu.index',
                        'icon'        => 'bi-menu-app-fill',
                        'orders'      => 1,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Peran & Matriks Hak Akses (RBAC)',
                        'url'         => 'roles.index',
                        'icon'        => 'bi-key-fill',
                        'orders'      => 2,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Daftar Izin (Permissions)',
                        'url'         => 'permissions.index',
                        'icon'        => 'bi-lock-fill',
                        'orders'      => 3,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                ],
            ],
            [
                'name'        => 'Pengaturan Sistem',
                'url'         => '#pengaturan-sistem',
                'category'    => 'PENGATURAN',
                'icon'        => 'bi-gear-fill',
                'orders'      => 62,
                'permissions' => ['read'],
                'sub_menus'   => [
                    [
                        'name'        => 'Tahun Pelajaran',
                        'url'         => 'tahun-pelajaran.index',
                        'icon'        => 'bi-calendar2-range-fill',
                        'orders'      => 1,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Pengaturan Akademik',
                        'url'         => 'pengaturan-akademik.index',
                        'icon'        => 'bi-mortarboard-fill',
                        'orders'      => 2,
                        'permissions' => ['read', 'create', 'update'],
                    ],
                    [
                        'name'        => 'Pengaturan Tagihan',
                        'url'         => 'pengaturan-tagihan.index',
                        'icon'        => 'bi-cash-coin',
                        'orders'      => 3,
                        'permissions' => ['read', 'create', 'update', 'delete'],
                    ],
                    [
                        'name'        => 'Pengaturan Aplikasi',
                        'url'         => 'pengaturan-aplikasi.index',
                        'icon'        => 'bi-sliders2',
                        'orders'      => 4,
                        'permissions' => ['read', 'update'],
                    ],
                    [
                        'name'        => 'Backup & Restore Database',
                        'url'         => 'backup.database',
                        'icon'        => 'bi-cloud-arrow-down-fill',
                        'orders'      => 5,
                        'permissions' => ['read', 'create', 'update'],
                    ],
                ],
            ],
        ];

        // 5. Eksekusi Pembuatan Menu & Permissions
        $allPermissions = [];

        foreach ($menuTree as $mItem) {
            $mainMenu = Menu::create([
                'name'         => $mItem['name'],
                'url'          => $mItem['url'],
                'category'     => $mItem['category'],
                'icon'         => $mItem['icon'] ?? 'bi-circle',
                'orders'       => $mItem['orders'] ?? 1,
                'is_active'    => 1,
                'main_menu_id' => null,
            ]);

            // Buat permissions untuk main menu
            $permIds = [];
            if (!empty($mItem['permissions'])) {
                foreach ($mItem['permissions'] as $action) {
                    $identifier = $mItem['url'] === '#' || str_starts_with($mItem['url'], '#')
                        ? strtolower(str_replace('#', '', $mItem['url']))
                        : $mItem['url'];
                    $permName = strtolower(trim($action . ' ' . $identifier));
                    $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                    $permIds[] = $perm->id;
                    $allPermissions[] = $permName;
                }
                $mainMenu->permissions()->sync($permIds);
            }

            // Buat sub menus jika ada
            if (!empty($mItem['sub_menus'])) {
                foreach ($mItem['sub_menus'] as $subItem) {
                    $subMenu = Menu::create([
                        'name'         => $subItem['name'],
                        'url'          => $subItem['url'],
                        'category'     => $mItem['category'],
                        'icon'         => $subItem['icon'] ?? 'bi-circle',
                        'orders'       => $subItem['orders'] ?? 1,
                        'is_active'    => 1,
                        'main_menu_id' => $mainMenu->id,
                    ]);

                    $subPermIds = [];
                    if (!empty($subItem['permissions'])) {
                        foreach ($subItem['permissions'] as $action) {
                            $identifier = $subItem['url'] === '#' || str_starts_with($subItem['url'], '#')
                                ? strtolower(str_replace('#', '', $subItem['url']))
                                : $subItem['url'];
                            $permName = strtolower(trim($action . ' ' . $identifier));
                            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                            $subPermIds[] = $perm->id;
                            $allPermissions[] = $permName;
                        }
                        $subMenu->permissions()->sync($subPermIds);
                    }
                }
            }
        }

        // 6. Sinkronisasi Default Permissions ke Setiap Role

        // A. ADMINISTRATOR (Super Admin): Semua Permission
        $roles['administrator']->syncPermissions(Permission::all());

        // B. STAFF (Admin Tingkat / TU): Master Data, Akademik, SPMB, Tagihan, Arsip, Layanan
        $staffPerms = Permission::where(function ($q) {
            $q->where('name', 'like', '%dashboard%')
                ->orWhere('name', 'like', '%kalendar%')
                ->orWhere('name', 'like', '%pengumuman%')
                ->orWhere('name', 'like', '%spmb%')
                ->orWhere('name', 'like', '%kartu-pelajar%')
                ->orWhere('name', 'like', '%jadwal-pelajaran%')
                ->orWhere('name', 'like', '%rombongan-belajar%')
                ->orWhere('name', 'like', '%presensi-murid%')
                ->orWhere('name', 'like', '%presensi-ustadz%')
                ->orWhere('name', 'like', '%pelanggaran-murid%')
                ->orWhere('name', 'like', '%ujian%')
                ->orWhere('name', 'like', '%bintang-pelajar%')
                ->orWhere('name', 'like', '%rapor%')
                ->orWhere('name', 'like', '%tagihan-murid%')
                ->orWhere('name', 'like', '%pembayaran-tagihan%')
                ->orWhere('name', 'like', '%pengurus%')
                ->orWhere('name', 'like', '%arsip%')
                ->orWhere('name', 'like', '%laporan-kendala%')
                ->orWhere('name', 'like', '%murid%')
                ->orWhere('name', 'like', '%ustadz%')
                ->orWhere('name', 'like', '%wali-murid%')
                ->orWhere('name', 'like', '%tingkat%')
                ->orWhere('name', 'like', '%level%')
                ->orWhere('name', 'like', '%ruangan%')
                ->orWhere('name', 'like', '%mata-pelajaran%')
                ->orWhere('name', 'like', '%referensi-pelanggaran%')
                ->orWhere('name', 'like', '%kampung%')
                ->orWhere('name', 'like', '%tahun-pelajaran%')
                ->orWhere('name', 'like', '%pengaturan-akademik%');
        })->get();
        $roles['staff']->syncPermissions($staffPerms);

        // C. PETUGAS TABUNGAN: Seluruh Modul Tabungan Madrasah
        $tabunganPerms = Permission::where(function ($q) {
            $q->where('name', 'like', '%tabungan%')
                ->orWhere('name', 'like', '%dashboard%');
        })->get();
        $roles['petugas-tabungan']->syncPermissions($tabunganPerms);

        // D. BENDAHARA: Tagihan Syahriyah, Kas Ruangan, Tabungan, Pembayaran
        $bendaharaPerms = Permission::where(function ($q) {
            $q->where('name', 'like', '%dashboard%')
                ->orWhere('name', 'like', '%tagihan%')
                ->orWhere('name', 'like', '%pembayaran%')
                ->orWhere('name', 'like', '%kas-ruangan%')
                ->orWhere('name', 'like', '%tabungan%');
        })->get();
        $roles['bendahara']->syncPermissions($bendaharaPerms);

        // E. USTADZ: Dashboard, Jadwal, Presensi Murid, Nilai Ujian, Pelanggaran, Kendala
        $ustadzPerms = Permission::where(function ($q) {
            $q->where('name', 'like', '%dashboard%')
                ->orWhere('name', 'like', '%jadwal-pelajaran%')
                ->orWhere('name', 'like', '%presensi-murid%')
                ->orWhere('name', 'like', '%nilai-ujian%')
                ->orWhere('name', 'like', '%pelanggaran-murid%')
                ->orWhere('name', 'like', '%laporan-kendala%');
        })->get();
        $roles['ustadz']->syncPermissions($ustadzPerms);

        // F. PETUGAS CETAK: Cetak Rapor, Kartu Pelajar, Berita Acara, DHPU, SPMB Bukti
        $cetakPerms = Permission::where(function ($q) {
            $q->where('name', 'like', '%rapor%')
                ->orWhere('name', 'like', '%petugas-cetak%')
                ->orWhere('name', 'like', '%kartu-pelajar%')
                ->orWhere('name', 'like', '%presensi-ujian%')
                ->orWhere('name', 'like', '%spmb%');
        })->get();
        $roles['petugas-cetak']->syncPermissions($cetakPerms);

        // G. WALI MURID: View-only dashboard, tabungan, tagihan
        $waliPerms = Permission::where(function ($q) {
            $q->where('name', 'read dashboard')
                ->orWhere('name', 'read tabungan.dashboard')
                ->orWhere('name', 'read tagihan-murid.index');
        })->get();
        $roles['wali-murid']->syncPermissions($waliPerms);

        // 7. Buat / Sinkronkan User Utama Administrator & Petugas Tabungan jika belum ada
        $adminUser = User::firstOrCreate(
            ['username' => 'mikyal_adly'],
            [
                'name'              => 'Mikyal Adly',
                'email'             => 'mikyaladly7596@gmail.com',
                'password'          => Hash::make('mdths123'),
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles(['administrator']);

        Administrator::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'nik'           => '3526110705960003',
                'nama_lengkap'  => $adminUser->name,
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'BANGKALAN',
                'tanggal_lahir' => '1996-05-07',
                'alamat'        => 'DSN. MORKONENG 003/004 DESA SOMORKONENG KEC. KWANYAR',
                'no_hp'         => '6285104044033',
                'is_active'     => true,
            ]
        );

        $petugasUser = User::firstOrCreate(
            ['username' => 'petugas_tabungan'],
            [
                'name'              => 'PETUGAS TABUNGAN MDT',
                'email'             => 'tabungan@mdt-somorkoneng.sch.id',
                'password'          => Hash::make('tabungan123'),
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        $petugasUser->syncRoles(['petugas-tabungan']);

        Administrator::firstOrCreate(
            ['user_id' => $petugasUser->id],
            [
                'nik'           => '3526110101900004',
                'nama_lengkap'  => $petugasUser->name,
                'jenis_kelamin' => 'L',
                'tempat_lahir'  => 'BANGKALAN',
                'tanggal_lahir' => '1990-01-01',
                'alamat'        => 'PONDOK PESANTREN HIDAYATUS SHIBYAN SOMORKONENG',
                'no_hp'         => '6281234567890',
                'is_active'     => true,
            ]
        );

        // 8. Bersihkan Cache Menu
        \Illuminate\Support\Facades\Cache::forget('menus');
        \Illuminate\Support\Facades\Cache::forget('urlMenu');
        \Illuminate\Support\Facades\Cache::forget('menus_hierarchy_with_permissions');
    }
}
