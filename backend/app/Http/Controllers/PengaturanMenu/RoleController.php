<?php

namespace App\Http\Controllers\PengaturanMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengaturanMenu\RoleRequest;
use App\Models\KonfigurasiMenu\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * TAMPILKAN HALAMAN MATRIKS RBAC
     */
    public function index(Request $request)
    {
        // 1. Ambil semua data peran (Role)
        $roles = Role::orderBy('name')->get();

        // 2. Tentukan peran yang sedang aktif di-klik dari sidebar
        $activeRole = null;
        $rolePermissions = [];

        if ($request->has('role_id')) {
            $activeRole = Role::findById($request->role_id);
            $rolePermissions = $activeRole->permissions->pluck('name')->toArray();
        } elseif ($roles->isNotEmpty()) {
            $activeRole = $roles->first();
            $rolePermissions = $activeRole->permissions->pluck('name')->toArray();
        }

        // 3. GANTI NAMA VARIABEL MENJADI $matrixMenus
        $matrixMenus = Menu::with(['permissions', 'subMenus.permissions'])
            ->whereNull('main_menu_id')
            ->orderBy('orders', 'ASC')
            ->get();

        // PASTIKAN COMPACT JUGA MENGGUNAKAN 'matrixMenus'
        return view('pengaturan-menu.roles.index', compact('roles', 'activeRole', 'matrixMenus', 'rolePermissions'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('pengaturan-menu.roles.form-role');
        }
        return redirect()->route('roles.index');
    }

    /**
     * SIMPAN PERAN (ROLE) BARU
     */
    public function store(RoleRequest $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:roles,name']
        ], [
            'name.required' => 'Nama role tidak boleh kosong.',
            'name.unique'   => 'Nama role ini sudah digunakan, silakan pilih nama lain.'
        ]);

        Role::create(['name' => $request->name]);

        // 🟢 Kembalikan response JSON jika request dari AJAX form
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role pengguna baru berhasil ditambahkan!'
            ]);
        }

        return back()->with('success', 'Role pengguna baru berhasil ditambahkan.');
    }

    public function edit(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($request->ajax()) {
            return view('pengaturan-menu.roles.form-role', compact('role'));
        }
        return redirect()->route('roles.index');
    }

    /**
     * UPDATE NAMA ROLE
     */
    public function update(RoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        $role->update(['name' => $request->name]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Nama role berhasil diperbarui!'
            ]);
        }

        return back()->with('success', 'Nama role berhasil diperbarui.');
    }

    /**
     * HAPUS ROLE
     */
    public function destroy(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Hapus (detach) relasi permission ke role sebelum role dihapus
        $role->permissions()->detach();

        $role->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus permanen!'
            ]);
        }

        return back()->with('success', 'Role berhasil dihapus.');
    }

    /**
     * AUTO-SAVE HAK AKSES (AJAX TOGGLE)
     */
    public function givePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Ambil array nama permission yang dikirim dari form (jika kosong, jadikan array kosong)
        $permissions = $request->permissions ?? [];

        // Gunakan fitur sakti Spatie untuk menyinkronkan data sekaligus
        // (Otomatis menghapus yang tidak dicentang & menyimpan yang dicentang)
        $role->syncPermissions($permissions);

        // Berikan balasan JSON untuk ditangkap oleh Javascript fetch()
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Konfigurasi hak akses berhasil disimpan!'
            ]);
        }

        // Fallback jika tidak menggunakan AJAX
        return back()->with('success', 'Konfigurasi hak akses berhasil disimpan!');
    }
}
