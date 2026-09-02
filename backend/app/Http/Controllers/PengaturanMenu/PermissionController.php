<?php

namespace App\Http\Controllers\PengaturanMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengaturanMenu\PermissionRequest;
use Illuminate\Http\Request;
use App\Models\KonfigurasiMenu\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $permissions = $query->orderBy('id', 'DESC')->paginate(10);

        return view('pengaturan-menu.permission.index', compact('permissions'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('pengaturan-menu.permission.form-permission');
        }
        return redirect()->route('permissions.index');
    }

    public function store(PermissionRequest $request)
    {
        // Secara default guard_name adalah 'web'
        Permission::create(['name' => strtolower($request->name)]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hak akses baru berhasil ditambahkan!'
            ]);
        }
        return back()->with('success', 'Hak akses berhasil ditambahkan.');
    }

    public function edit(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        if ($request->ajax()) {
            return view('pengaturan-menu.permission.form-permission', compact('permission'));
        }
        return redirect()->route('permissions.index');
    }

    public function update(PermissionRequest $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $permission->update(['name' => strtolower($request->name)]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hak akses berhasil diperbarui!'
            ]);
        }
        return back()->with('success', 'Hak akses berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hak akses berhasil dihapus!'
            ]);
        }
        return back()->with('success', 'Hak akses berhasil dihapus.');
    }
}
