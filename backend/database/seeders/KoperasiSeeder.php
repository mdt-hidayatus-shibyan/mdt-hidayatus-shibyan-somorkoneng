<?php

namespace Database\Seeders;

use App\Models\KonfigurasiMenu\Menu;
use App\Models\KonfigurasiMenu\Permission as MenuPermissionModel;
use App\Models\Koperasi\KategoriProduk;
use App\Models\Koperasi\MutasiStokKoperasi;
use App\Models\Koperasi\PaketKoperasi;
use App\Models\Koperasi\PaketKoperasiItem;
use App\Models\Koperasi\ProdukKoperasi;
use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class KoperasiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DAFTARKAN PERMISSIONS RBAC
        $permissions = [
            'akses-koperasi' => 'Mengakses dashboard & navigasi Koperasi Madrasah',
            'kelola-produk-koperasi' => 'Menambah & mengedit master produk, kategori, dan paket bundling',
            'kasir-koperasi' => 'Melakukan transaksi penjualan kasir POS toko koperasi',
            'laporan-koperasi' => 'Melihat laporan omzet, laba rugi, dan kartu stok koperasi',
        ];

        $permModels = [];
        foreach ($permissions as $permName => $desc) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);

            // Catat ke tabel permission menu
            $menuPerm = MenuPermissionModel::firstOrCreate(
                ['name' => $permName],
                ['guard_name' => 'web', 'description' => $desc]
            );
            $permModels[$permName] = $menuPerm;
        }

        // 2. DAFTARKAN ROLE & ASSIGN PERMISSIONS
        $roleAdmin = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo(array_keys($permissions));

        $roleKoperasi = Role::firstOrCreate(['name' => 'petugas-koperasi', 'guard_name' => 'web']);
        $roleKoperasi->givePermissionTo(array_keys($permissions));

        // 3. SEED KATEGORI PRODUK
        $kategoriList = [
            [
                'nama_kategori' => 'Kitab & Buku Pelajaran',
                'slug' => 'kitab-dan-buku-pelajaran',
                'icon' => 'bi-book-half',
                'warna' => 'emerald',
                'keterangan' => 'Kitab kuning, buku pegon, dan buku materi ajar MDT',
            ],
            [
                'nama_kategori' => 'Seragam & Atribut Madrasah',
                'slug' => 'seragam-dan-atribut-madrasah',
                'icon' => 'bi-person-badge-fill',
                'warna' => 'indigo',
                'keterangan' => 'Seragam putra/putri, peci, kerudung, sabuk, dan badge',
            ],
            [
                'nama_kategori' => 'Alat Tulis & Perlengkapan',
                'slug' => 'alat-tulis-dan-perlengkapan',
                'icon' => 'bi-pencil-square',
                'warna' => 'amber',
                'keterangan' => 'Buku tulis, pulpen, pensil, penghapus, dan map',
            ],
            [
                'nama_kategori' => 'Makanan & Minuman Santri',
                'slug' => 'makanan-dan-minuman-santri',
                'icon' => 'bi-cup-straw',
                'warna' => 'rose',
                'keterangan' => 'Air mineral, snack, dan makanan ringan',
            ],
        ];

        $kategoriModels = [];
        foreach ($kategoriList as $k) {
            $kategoriModels[$k['slug']] = KategoriProduk::firstOrCreate(
                ['slug' => $k['slug']],
                $k
            );
        }

        // 4. SEED PRODUK CONTOH & STOK AWAL
        $firstUser = User::first();
        $adminId = $firstUser ? $firstUser->id : 1;

        $produkList = [
            // Kitab
            [
                'kategori_slug' => 'kitab-dan-buku-pelajaran',
                'kode_produk' => 'KTB-FQ-001',
                'nama_produk' => 'Kitab Fathul Qorib',
                'satuan' => 'buku',
                'harga_beli' => 25000.00,
                'harga_jual' => 30000.00,
                'stok' => 50,
                'stok_minimum' => 5,
            ],
            [
                'kategori_slug' => 'kitab-dan-buku-pelajaran',
                'kode_produk' => 'KTB-JRM-002',
                'nama_produk' => 'Kitab Al-Jurumiyyah',
                'satuan' => 'buku',
                'harga_beli' => 12000.00,
                'harga_jual' => 15000.00,
                'stok' => 60,
                'stok_minimum' => 5,
            ],
            [
                'kategori_slug' => 'kitab-dan-buku-pelajaran',
                'kode_produk' => 'KTB-SFN-003',
                'nama_produk' => 'Kitab Safinatun Najah',
                'satuan' => 'buku',
                'harga_beli' => 10000.00,
                'harga_jual' => 13000.00,
                'stok' => 70,
                'stok_minimum' => 5,
            ],
            [
                'kategori_slug' => 'kitab-dan-buku-pelajaran',
                'kode_produk' => 'KTB-PGN-004',
                'nama_produk' => 'Buku Tulis Pegon Khusus MDT',
                'satuan' => 'buku',
                'harga_beli' => 4500.00,
                'harga_jual' => 6000.00,
                'stok' => 150,
                'stok_minimum' => 20,
            ],
            // Seragam & Atribut
            [
                'kategori_slug' => 'seragam-dan-atribut-madrasah',
                'kode_produk' => 'SRG-PH-005',
                'nama_produk' => 'Stel Seragam Putih Hijau MDT',
                'satuan' => 'stel',
                'harga_beli' => 85000.00,
                'harga_jual' => 105000.00,
                'stok' => 40,
                'stok_minimum' => 5,
            ],
            [
                'kategori_slug' => 'seragam-dan-atribut-madrasah',
                'kode_produk' => 'ATR-PEC-006',
                'nama_produk' => 'Peci Hitam Polos Standar',
                'satuan' => 'pcs',
                'harga_beli' => 30000.00,
                'harga_jual' => 40000.00,
                'stok' => 35,
                'stok_minimum' => 5,
            ],
            // Alat Tulis
            [
                'kategori_slug' => 'alat-tulis-dan-perlengkapan',
                'kode_produk' => 'ATK-PLP-007',
                'nama_produk' => 'Pulpen Gel Hitam Standar',
                'satuan' => 'pcs',
                'harga_beli' => 2000.00,
                'harga_jual' => 3000.00,
                'stok' => 100,
                'stok_minimum' => 15,
            ],
        ];

        $produkModels = [];
        foreach ($produkList as $p) {
            $kat = $kategoriModels[$p['kategori_slug']];
            $prod = ProdukKoperasi::firstOrCreate(
                ['kode_produk' => $p['kode_produk']],
                [
                    'kategori_id' => $kat->id,
                    'kode_produk' => $p['kode_produk'],
                    'nama_produk' => $p['nama_produk'],
                    'satuan' => $p['satuan'],
                    'harga_beli' => $p['harga_beli'],
                    'harga_jual' => $p['harga_jual'],
                    'stok' => $p['stok'],
                    'stok_minimum' => $p['stok_minimum'],
                    'status' => 'Aktif',
                ]
            );
            $produkModels[$p['kode_produk']] = $prod;

            if (!MutasiStokKoperasi::where('produk_id', $prod->id)->exists()) {
                MutasiStokKoperasi::create([
                    'produk_id' => $prod->id,
                    'jenis_mutasi' => 'Stok_Awal',
                    'jumlah' => $p['stok'],
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $p['stok'],
                    'referensi' => 'SALDO-AWAL',
                    'keterangan' => "Stok awal master produk ({$p['stok']} {$p['satuan']})",
                    'petugas_id' => $adminId,
                ]);
            }
        }

        // 5. SEED PAKET BUNDLING (PAKET KITAB KELAS 5 IBTIDAIYAH)
        $level5 = Level::where('nama_level', 'like', '%5%')->orWhere('urutan_level', 5)->first();

        $paket1 = PaketKoperasi::firstOrCreate(
            ['kode_paket' => 'PKT-KLS5-IBT'],
            [
                'kode_paket' => 'PKT-KLS5-IBT',
                'nama_paket' => 'Paket Kitab Lengkap Kelas 5 Ibtidaiyah',
                'level_id' => $level5?->id,
                'tingkat_id' => $level5?->tingkat_id,
                'harga_paket' => 65000.00,
                'total_hpp_komponen' => 51500.00,
                'is_active' => true,
                'deskripsi' => 'Paket lengkap kitab & buku tulis pegon wajib untuk santri/murid Kelas 5 IBT di awal tahun ajaran.',
            ]
        );

        $paket1Items = [
            ['kode' => 'KTB-FQ-001', 'qty' => 1],
            ['kode' => 'KTB-JRM-002', 'qty' => 1],
            ['kode' => 'KTB-SFN-003', 'qty' => 1],
            ['kode' => 'KTB-PGN-004', 'qty' => 2],
        ];

        foreach ($paket1Items as $item) {
            if (isset($produkModels[$item['kode']])) {
                PaketKoperasiItem::firstOrCreate([
                    'paket_koperasi_id' => $paket1->id,
                    'produk_id' => $produkModels[$item['kode']]->id,
                ], [
                    'jumlah' => $item['qty'],
                ]);
            }
        }

        // Paket 2: Perlengkapan Murid Baru
        $level1 = Level::where('nama_level', 'like', '%1%')->orWhere('urutan_level', 1)->first();
        $paket2 = PaketKoperasi::firstOrCreate(
            ['kode_paket' => 'PKT-MB-001'],
            [
                'kode_paket' => 'PKT-MB-001',
                'nama_paket' => 'Paket Perlengkapan Murid Baru',
                'level_id' => $level1?->id,
                'tingkat_id' => $level1?->tingkat_id,
                'harga_paket' => 155000.00,
                'total_hpp_komponen' => 126000.00,
                'is_active' => true,
                'deskripsi' => 'Paket seragam, peci, buku pegon, dan alat tulis untuk murid baru.',
            ]
        );

        $paket2Items = [
            ['kode' => 'SRG-PH-005', 'qty' => 1],
            ['kode' => 'ATR-PEC-006', 'qty' => 1],
            ['kode' => 'KTB-PGN-004', 'qty' => 2],
            ['kode' => 'ATK-PLP-007', 'qty' => 1],
        ];

        foreach ($paket2Items as $item) {
            if (isset($produkModels[$item['kode']])) {
                PaketKoperasiItem::firstOrCreate([
                    'paket_koperasi_id' => $paket2->id,
                    'produk_id' => $produkModels[$item['kode']]->id,
                ], [
                    'jumlah' => $item['qty'],
                ]);
            }
        }

        // 6. DAFTARKAN MENU SIDEBAR
        $parentMenu = Menu::firstOrNew(['name' => 'Koperasi Madrasah', 'category' => 'KEUANGAN']);
        $parentMenu->name = 'Koperasi Madrasah';
        $parentMenu->url = '#';
        $parentMenu->category = 'KEUANGAN';
        $parentMenu->icon = 'bi-shop';
        $parentMenu->is_active = 1;
        $parentMenu->orders = 38;
        $parentMenu->save();

        if (isset($permModels['akses-koperasi'])) {
            $parentMenu->permissions()->syncWithoutDetaching([$permModels['akses-koperasi']->id]);
        }

        $submenus = [
            [
                'name'   => 'Dashboard Koperasi',
                'url'    => 'koperasi.dashboard',
                'icon'   => 'bi-speedometer2',
                'orders' => 1,
                'perm'   => 'akses-koperasi',
            ],
            [
                'name'   => 'Kasir POS (Toko)',
                'url'    => 'koperasi.pos.index',
                'icon'   => 'bi-calculator-fill',
                'orders' => 2,
                'perm'   => 'kasir-koperasi',
            ],
            [
                'name'   => 'Master Produk',
                'url'    => 'koperasi.produk.index',
                'icon'   => 'bi-box-seam-fill',
                'orders' => 3,
                'perm'   => 'kelola-produk-koperasi',
            ],
            [
                'name'   => 'Kategori Produk',
                'url'    => 'koperasi.kategori.index',
                'icon'   => 'bi-tags-fill',
                'orders' => 4,
                'perm'   => 'kelola-produk-koperasi',
            ],
            [
                'name'   => 'Paket Bundling',
                'url'    => 'koperasi.paket.index',
                'icon'   => 'bi-collection-fill',
                'orders' => 5,
                'perm'   => 'kelola-produk-koperasi',
            ],
            [
                'name'   => 'Mutasi & Stok',
                'url'    => 'koperasi.stok.index',
                'icon'   => 'bi-arrow-left-right',
                'orders' => 6,
                'perm'   => 'kelola-produk-koperasi',
            ],
            [
                'name'   => 'Pembelian (Kulakan)',
                'url'    => 'koperasi.pembelian.index',
                'icon'   => 'bi-bag-plus-fill',
                'orders' => 7,
                'perm'   => 'kelola-produk-koperasi',
            ],
            [
                'name'   => 'Riwayat Transaksi',
                'url'    => 'koperasi.transaksi.index',
                'icon'   => 'bi-receipt-cutoff',
                'orders' => 8,
                'perm'   => 'akses-koperasi',
            ],
            [
                'name'   => 'Laporan Penjualan',
                'url'    => 'koperasi.laporan.index',
                'icon'   => 'bi-file-earmark-bar-graph-fill',
                'orders' => 9,
                'perm'   => 'laporan-koperasi',
            ],
        ];

        foreach ($submenus as $sm) {
            $sub = Menu::firstOrNew(['name' => $sm['name'], 'main_menu_id' => $parentMenu->id]);
            $sub->name = $sm['name'];
            $sub->url = $sm['url'];
            $sub->category = 'KEUANGAN';
            $sub->icon = $sm['icon'];
            $sub->main_menu_id = $parentMenu->id;
            $sub->is_active = 1;
            $sub->orders = $sm['orders'];
            $sub->save();

            if (isset($permModels[$sm['perm']])) {
                $sub->permissions()->syncWithoutDetaching([$permModels[$sm['perm']]->id]);
            }
        }

        Cache::flush();
    }
}
