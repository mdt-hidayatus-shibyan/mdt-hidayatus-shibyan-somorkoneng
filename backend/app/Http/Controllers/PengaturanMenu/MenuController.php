<?php

namespace App\Http\Controllers\PengaturanMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengaturanMenu\MenuRequest;
use App\Models\KonfigurasiMenu\Menu;
use App\Models\KonfigurasiMenu\Permission;
use App\Repositories\MenuRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class MenuController extends Controller
{
    public function __construct(private MenuRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->filled('q')) {
            // Mode Pencarian
            $menus = Menu::whereNull('main_menu_id') // <-- Asumsi kolom Anda bernama 'main_menu_id'
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->q . '%')
                        ->orWhere('url', 'like', '%' . $request->q . '%');
                })
                ->with('subMenus') // <-- Ambil sekalian sub-menunya (Mencegah N+1 query problem)
                ->orderBy('orders', 'ASC') // <-- Biasanya urutan dari kecil ke besar (ASC)
                ->get(); // <-- Ubah paginate jadi get()
        } else {
            // Mode Tampilan Biasa (Tampilkan hanya Menu Utama)
            $menus = Menu::whereNull('main_menu_id') // <-- Ambil yang main_menu_id nya kosong (Menu Utama)
                ->with('subMenus')
                ->orderBy('orders', 'ASC')
                ->get(); // <-- Ubah paginate jadi get()
        }

        return view('pengaturan-menu.menu.index', compact('menus'));
    }


    public function create(Request $request)
    {
        // Ambil hanya menu utama (yang tidak punya parent) untuk opsi "Jadikan Sub-Menu"
        $mainMenus = Menu::whereNull('main_menu_id')->orderBy('orders', 'ASC')->get();

        // Ambil semua daftar permission dari Spatie (atau tabel permission Anda)
        $permissions = Permission::all();

        if ($request->ajax()) {
            return view('pengaturan-menu.menu.form-menu', compact('mainMenus', 'permissions'));
        }

        return redirect()->route('pengaturan-menu.index')->with('error', 'Gunakan tombol tambah pada halaman.');
    }


    public function store(MenuRequest $request)
    {

        $menu = Menu::create([
            'name'         => $request->name,
            'url'          => $request->url,
            'category'     => $request->category,
            'icon'         => $request->icon,
            'orders'       => $request->orders ?? 0,
            'is_active'    => $request->is_active ?? 1,
            'main_menu_id' => $request->main_menu_id,
        ]);

        if ($request->has('permissions')) {
            $permIds = [];

            // Jika Menu Utama tidak punya URL (pakai '#'), gunakan slug dari namanya agar tidak error ('create #')
            // Jika menu punya URL (misal 'siswa'), gunakan URL tersebut.
            $identifier = $menu->url === '#' ? \Illuminate\Support\Str::slug($menu->name) : $menu->url;

            foreach ($request->permissions as $action) {
                // Hasilnya akan menjadi: "create siswa", "read siswa" (Sesuai dengan trait HasPermission)
                $permName = strtolower($action . ' ' . $identifier);

                $perm = Permission::firstOrCreate(['name' => $permName]);
                $permIds[] = $perm->id;
            }
            $menu->permissions()->sync($permIds);
        }

        Cache::forget('menus');
        Cache::forget('urlMenu');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu baru berhasil ditambahkan!'
            ]);
        }

        return back()->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * TAMPILKAN FORM EDIT (AJAX MODAL)
     */
    public function edit(Request $request, $id)
    {
        $menu = Menu::with('permissions')->findOrFail($id);

        // Ambil menu utama, kecualikan dirinya sendiri agar tidak terjadi infinity loop parent
        $mainMenus = Menu::whereNull('main_menu_id')->where('id', '!=', $id)->orderBy('orders', 'ASC')->get();
        $permissions = Permission::all();

        if ($request->ajax()) {
            return view('pengaturan-menu.menu.form-menu', compact('menu', 'mainMenus', 'permissions'));
        }

        return redirect()->route('menu.index');
    }

    /**
     * UPDATE DATA MENU
     */
    public function update(MenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $menu->update([
            'name'         => $request->name,
            'url'          => $request->url,
            'category'     => $request->category,
            'icon'         => $request->icon,
            'orders'       => $request->orders ?? 0,
            'is_active'    => $request->is_active ?? 1,
            'main_menu_id' => $request->main_menu_id,
        ]);

        // Menyinkronkan pivot tabel menu_permission (Otomatis hapus yang tidak dicentang, tambah yang dicentang)
        if ($request->has('permissions')) {
            $permIds = [];

            // Jika Menu Utama tidak punya URL (pakai '#'), gunakan slug dari namanya agar tidak error ('create #')
            // Jika menu punya URL (misal 'siswa'), gunakan URL tersebut.
            $identifier = $menu->url === '#' ? \Illuminate\Support\Str::slug($menu->name) : $menu->url;

            foreach ($request->permissions as $action) {
                // Hasilnya akan menjadi: "create siswa", "read siswa" (Sesuai dengan trait HasPermission)
                $permName = strtolower($action . ' ' . $identifier);

                $perm = Permission::firstOrCreate(['name' => $permName]);
                $permIds[] = $perm->id;
            }
            $menu->permissions()->sync($permIds);
        } else {
            $menu->permissions()->detach(); // Kosongkan jika tidak ada yang dicentang
        }

        Cache::forget('menus');
        Cache::forget('urlMenu');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data menu berhasil diperbarui!'
            ]);
        }

        return back()->with('success', 'Data menu berhasil diperbarui.');
    }

    /**
     * HAPUS MENU
     */
    public function destroy(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        // Hapus relasi pivot terlebih dahulu sebelum menghapus menu (jika tidak cascade di DB)
        $menu->permissions()->detach();

        // Opsional: Hapus sub-menu nya juga atau jadikan parent-nya null
        // Menu::where('main_menu_id', $id)->update(['main_menu_id' => null]);

        $menu->delete();

        Cache::forget('menus');
        Cache::forget('urlMenu');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus!'
            ]);
        }

        return back()->with('success', 'Menu berhasil dihapus.');
    }



    public function updateOrder(Request $request)
    {
        $menus = json_decode($request->get('menu_order'), true);

        // Fungsi rekursif untuk mengupdate urutan dan parent
        $this->updateMenuHierarchy($menus, null);

        // Jangan lupa bersihkan cache navigasi!
        \Illuminate\Support\Facades\Cache::forget('menus');
        \Illuminate\Support\Facades\Cache::forget('urlMenu');

        return response()->json([
            'success' => true,
            'message' => 'Susunan navigasi berhasil diperbarui!'
        ]);
    }

    private function updateMenuHierarchy($menus, $parentId)
    {
        foreach ($menus as $index => $item) {
            // Update menu ini (Urutan ke- index + 1, Parent ID-nya siapa)
            Menu::where('id', $item['id'])->update([
                'orders'       => $index + 1,
                'main_menu_id' => $parentId
            ]);

            // Jika dia punya sub-menu hasil drag & drop, jalankan fungsi ini lagi
            if (isset($item['children'])) {
                $this->updateMenuHierarchy($item['children'], $item['id']);
            }
        }
    }
}
