<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\KonfigurasiMenu\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class PetugasTabunganSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat atau cari Role petugas-tabungan
        $role = Role::firstOrCreate(['name' => 'petugas-tabungan', 'guard_name' => 'web']);

        // 2. Kumpulkan seluruh permission yang berhubungan dengan Tabungan & Dashboard
        $tabunganRoutes = [
            'dashboard',
            'tabungan',
            'tabungan.dashboard',
            'tabungan.rekening.index',
            'tabungan.rekening.create',
            'tabungan.rekening.detail',
            'tabungan.rekening.cetak',
            'tabungan.rekening.ganti-buku',
            'tabungan.setor.index',
            'tabungan.setor.edit',
            'tabungan.tarik.index',
            'tabungan.tarik.edit',
            'tabungan.cek-mutasi.index',
            'tabungan.cek-mutasi.cetak',
            'tabungan.cek-mutasi.cetak-a6',
            'tabungan.rincian.index',
            'tabungan.rincian.cetak',
            'tabungan.pecahan.index',
            'tabungan.pecahan.cetak',
            'tabungan.pecahan.cetak-slip',
            'tabungan.pecahan.cetak-slip-massal',
            'tabungan.pembagian.index',
            'tabungan.pembagian.cetak',
            'tabungan.pengaturan.index',
            'tabungan.pengaturan.kategori.create',
            'tabungan.pengaturan.kategori.edit',
            'tabungan.pengaturan.periode.create',
            'tabungan.pengaturan.periode.edit',
            'tabungan.pengaturan.periode.potongan.edit',
            'tabungan.ajax.cari-murid',
            'tabungan.ajax.cari-ustadz',
            'tabungan.ajax.cari-rekening',
            'tabungan.barcode.generator',
            'tabungan.barcode.export',
        ];

        $actions = ['read', 'create', 'update', 'delete'];
        $permissionNames = [];

        foreach ($tabunganRoutes as $route) {
            foreach ($actions as $act) {
                $permName = "{$act} {$route}";
                $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                $permissionNames[] = $perm->name;
            }
        }

        // Assign permissions ke role petugas-tabungan
        $role->syncPermissions($permissionNames);

        // 3. Buat atau update User akun petugas-tabungan
        $user = User::firstOrNew(['username' => 'petugas_tabungan']);
        $user->name = 'PETUGAS TABUNGAN';
        $user->username = 'petugas_tabungan';
        $user->email = 'petugas.tabungan@mdthidayatusshibyan.sch.id';
        $user->password = Hash::make('tabungan123');
        $user->is_active = 1;
        $user->is_login = 0;
        $user->is_logout = 0;
        $user->save();

        // Assign role petugas-tabungan ke user
        $user->syncRoles([$role->name]);

        // 4. Buat atau update Biodata di tabel administrators
        $admin = Administrator::firstOrNew(['user_id' => $user->id]);
        $admin->user_id = $user->id;
        $admin->tingkat_id = null;
        $admin->nik = '3526110101980002';
        $admin->nama_lengkap = 'PETUGAS TABUNGAN MDT';
        $admin->jenis_kelamin = 'L';
        $admin->tempat_lahir = 'BANGKALAN';
        $admin->tanggal_lahir = '1998-01-01';
        $admin->alamat = 'Dsn. Somorkoneng, Kec. Kwanyar, Kab. Bangkalan';
        $admin->no_hp = '6285234567890';
        $admin->is_active = 1;
        $admin->save();

        // Bersihkan cache navigasi
        Cache::forget('menus');
        Cache::forget('urlMenu');
        Cache::forget('menus_hierarchy_with_permissions');
        Cache::flush();

        echo "=== HASIL PEMBUATAN AKUN & ROLE PETUGAS TABUNGAN ===\n";
        echo "User ID  : {$user->id}\n";
        echo "Name     : {$user->name}\n";
        echo "Username : {$user->username}\n";
        echo "Email    : {$user->email}\n";
        echo "Password : tabungan123\n";
        echo "Role     : {$role->name}\n";
        echo "Permissions Count : " . count($permissionNames) . " permissions\n";
        echo "Admin Biodata ID  : {$admin->id}\n";
        echo "Nama Lengkap      : {$admin->nama_lengkap}\n";
        echo "NIK               : {$admin->nik}\n";
        echo "No HP             : {$admin->no_hp}\n";
        echo "Alamat            : {$admin->alamat}\n";
    }
}
