<?php

namespace App\Http\Controllers\PengaturanMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengaturanMenu\MenuRequest;
use App\Models\KonfigurasiMenu\Menu;
use App\Models\KonfigurasiMenu\Permission;
use App\Repositories\MenuRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MenuController extends Controller
{
    public function __construct(private MenuRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Tampilkan daftar menu & susunan hierarki navigasi
     */
    public function index(Request $request)
    {
        $selectedCategory = $request->filled('category') ? strtoupper(trim($request->category)) : null;
        $searchQuery = $request->filled('q') ? trim($request->q) : null;

        $query = Menu::whereNull('main_menu_id')
            ->with(['subMenus' => function ($q) {
                $q->orderBy('orders', 'ASC')->with('permissions');
            }, 'permissions']);

        if ($selectedCategory) {
            if ($selectedCategory === 'UTAMA') {
                $query->where(function ($q) {
                    $q->whereNull('category')->orWhere('category', '')->orWhere('category', 'UTAMA');
                });
            } else {
                $query->where('category', $selectedCategory);
            }
        }

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('url', 'like', "%{$searchQuery}%")
                    ->orWhere('category', 'like', "%{$searchQuery}%")
                    ->orWhereHas('subMenus', function ($sq) use ($searchQuery) {
                        $sq->where('name', 'like', "%{$searchQuery}%")
                            ->orWhere('url', 'like', "%{$searchQuery}%");
                    });
            });
        }

        $menus = $query->orderBy('orders', 'ASC')->get();

        $categories = $this->getAvailableCategories();
        $totalMenus = Menu::whereNull('main_menu_id')->count();
        $totalSubMenus = Menu::whereNotNull('main_menu_id')->count();

        return view('pengaturan-menu.menu.index', compact(
            'menus',
            'categories',
            'selectedCategory',
            'searchQuery',
            'totalMenus',
            'totalSubMenus'
        ));
    }

    /**
     * Tampilkan modal form tambah menu
     */
    public function create(Request $request)
    {
        $mainMenus = Menu::whereNull('main_menu_id')->orderBy('orders', 'ASC')->get();
        $categories = $this->getAvailableCategories();
        $routes = $this->getSystemRoutes();
        $permissions = Permission::orderBy('name')->get();

        if ($request->ajax()) {
            return view('pengaturan-menu.menu.form-menu', compact('mainMenus', 'categories', 'routes', 'permissions'));
        }

        return redirect()->route('menu.index')->with('info', 'Silakan gunakan tombol tambah di halaman manajemen menu.');
    }

    /**
     * Simpan menu baru ke database
     */
    public function store(MenuRequest $request)
    {
        $category = $request->category;
        if (empty($category) && $request->filled('main_menu_id')) {
            $parent = Menu::find($request->main_menu_id);
            if ($parent) {
                $category = $parent->category;
            }
        }

        $orders = $request->orders;
        if ($orders === null || $orders === '') {
            $maxOrder = Menu::where('main_menu_id', $request->main_menu_id)->max('orders');
            $orders = ($maxOrder !== null) ? $maxOrder + 1 : 1;
        }

        $menu = Menu::create([
            'name'         => $request->name,
            'url'          => $request->url,
            'category'     => $category,
            'icon'         => $request->icon ?? 'bi-circle',
            'orders'       => $orders,
            'is_active'    => $request->is_active ?? 1,
            'main_menu_id' => $request->main_menu_id,
        ]);

        // Sync permissions
        $this->syncPermissions($menu, $request->permissions ?? []);

        $this->clearMenuCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu baru "' . $menu->name . '" berhasil ditambahkan!'
            ]);
        }

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Tampilkan modal form edit menu
     */
    public function edit(Request $request, $id)
    {
        $menu = Menu::with('permissions')->findOrFail($id);

        // Ambil menu utama selain dirinya sendiri agar tidak circular dependency
        $mainMenus = Menu::whereNull('main_menu_id')
            ->where('id', '!=', $id)
            ->orderBy('orders', 'ASC')
            ->get();

        $categories = $this->getAvailableCategories();
        $routes = $this->getSystemRoutes();
        $permissions = Permission::orderBy('name')->get();

        if ($request->ajax()) {
            return view('pengaturan-menu.menu.form-menu', compact('menu', 'mainMenus', 'categories', 'routes', 'permissions'));
        }

        return redirect()->route('menu.index');
    }

    /**
     * Update data menu
     */
    public function update(MenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $category = $request->category;
        if (empty($category) && $request->filled('main_menu_id')) {
            $parent = Menu::find($request->main_menu_id);
            if ($parent) {
                $category = $parent->category;
            }
        }

        $menu->update([
            'name'         => $request->name,
            'url'          => $request->url,
            'category'     => $category,
            'icon'         => $request->icon ?? 'bi-circle',
            'orders'       => $request->orders ?? 0,
            'is_active'    => $request->is_active ?? 1,
            'main_menu_id' => $request->main_menu_id,
        ]);

        // Sinkronkan kategori anak jika ini menu induk
        if (!$request->main_menu_id && $category) {
            Menu::where('main_menu_id', $menu->id)->update(['category' => $category]);
        }

        // Sinkronisasi permissions
        $this->syncPermissions($menu, $request->permissions ?? []);

        $this->clearMenuCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data menu "' . $menu->name . '" berhasil diperbarui!'
            ]);
        }

        return redirect()->route('menu.index')->with('success', 'Data menu berhasil diperbarui.');
    }

    /**
     * Toggle status aktif menu (AJAX)
     */
    public function toggleActive(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_active = $menu->is_active ? 0 : 1;
        $menu->save();

        $this->clearMenuCache();

        return response()->json([
            'success'   => true,
            'is_active' => (bool) $menu->is_active,
            'message'   => 'Status menu "' . $menu->name . '" berhasil diubah menjadi ' . ($menu->is_active ? 'Aktif' : 'Non-Aktif') . '.'
        ]);
    }

    /**
     * Hapus menu & sub-menunya
     */
    public function destroy(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        // Bersihkan permission dan sub-menu
        $subMenus = Menu::where('main_menu_id', $id)->get();
        foreach ($subMenus as $sub) {
            $sub->permissions()->detach();
            $sub->delete();
        }

        $menu->permissions()->detach();
        $menu->delete();

        $this->clearMenuCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu "' . $menu->name . '" beserta sub-menunya berhasil dihapus!'
            ]);
        }

        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * Update urutan & hierarki menu hasil drag-and-drop Nestable
     */
    public function updateOrder(Request $request)
    {
        $menuOrderJson = $request->get('menu_order');
        $menus = json_decode($menuOrderJson, true);

        if (!is_array($menus)) {
            return response()->json([
                'success' => false,
                'message' => 'Format susunan menu tidak valid.'
            ], 400);
        }

        $this->updateMenuHierarchy($menus, null);

        $this->clearMenuCache();

        return response()->json([
            'success' => true,
            'message' => 'Susunan navigasi & hierarki menu berhasil diperbarui!'
        ]);
    }

    /**
     * Update hierarki rekursif
     */
    private function updateMenuHierarchy(array $menus, ?int $parentId): void
    {
        $parentCategory = null;
        if ($parentId) {
            $parent = Menu::find($parentId);
            $parentCategory = $parent?->category;
        }

        foreach ($menus as $index => $item) {
            if (!isset($item['id'])) {
                continue;
            }

            $updateData = [
                'orders'       => $index + 1,
                'main_menu_id' => $parentId
            ];

            if ($parentCategory !== null) {
                $updateData['category'] = $parentCategory;
            }

            Menu::where('id', $item['id'])->update($updateData);

            if (!empty($item['children']) && is_array($item['children'])) {
                $this->updateMenuHierarchy($item['children'], (int) $item['id']);
            }
        }
    }

    /**
     * Sinkronisasi permissions menu
     */
    private function syncPermissions(Menu $menu, array $permissions): void
    {
        if (empty($permissions)) {
            $menu->permissions()->detach();
            return;
        }

        $permIds = [];
        $identifier = $menu->url === '#' ? Str::slug($menu->name) : $menu->url;

        foreach ($permissions as $action) {
            $permName = strtolower($action . ' ' . $identifier);
            $perm = Permission::firstOrCreate([
                'name'       => $permName,
                'guard_name' => 'web'
            ]);
            $permIds[] = $perm->id;
        }

        $menu->permissions()->sync($permIds);

        // Auto grant permission baru ke peran Administrator & Staff
        $adminRoles = Role::whereIn('name', ['administrator', 'staff', 'Admin', 'Bendahara'])->get();
        foreach ($adminRoles as $role) {
            foreach ($permIds as $pId) {
                $permission = Permission::find($pId);
                if ($permission && !$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }

    /**
     * Mengambil daftar route sistem yang valid untuk menu navigasi
     */
    protected function getSystemRoutes(): array
    {
        $routes = Route::getRoutes();
        $namedRoutes = [];

        // Daftar awalan route internal / non-menu yang diabaikan
        $ignoredPrefixes = [
            'sanctum.',
            'ignition.',
            '_debugbar.',
            'livewire.',
            'password.',
            'verification.',
            'login',
            'logout',
            'register',
            'profile.',
            'profil.publik',
            'storage.'
        ];

        foreach ($routes as $route) {
            $name = $route->getName();
            if (!$name) {
                continue;
            }

            if (!in_array('GET', $route->methods())) {
                continue;
            }

            $isIgnored = false;
            foreach ($ignoredPrefixes as $prefix) {
                if (str_starts_with($name, $prefix) || $name === $prefix) {
                    $isIgnored = true;
                    break;
                }
            }
            if ($isIgnored) {
                continue;
            }

            $namedRoutes[] = [
                'name'   => $name,
                'uri'    => $route->uri(),
                'action' => $route->getActionName(),
            ];
        }

        // Urutkan berdasarkan nama route
        usort($namedRoutes, fn($a, $b) => strcmp($a['name'], $b['name']));

        return $namedRoutes;
    }

    /**
     * Mengambil daftar kategori menu yang tersedia di sistem
     */
    protected function getAvailableCategories(): array
    {
        $defaults = [
            'UTAMA',
            'AKADEMIK',
            'KEUANGAN',
            'KESEKRETARISAN',
            'TAGIHAN & PEMBAYARAN',
            'ARSIP DOKUMEN',
            'LAYANAN & BANTUAN',
            'MASTER DATA',
            'PENGATURAN',
        ];

        $existing = Menu::select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->map(fn($cat) => strtoupper(trim($cat)))
            ->toArray();

        $all = array_unique(array_merge($defaults, $existing));
        sort($all);

        return array_values($all);
    }

    /**
     * Bersihkan cache menu
     */
    private function clearMenuCache(): void
    {
        Cache::forget('menus');
        Cache::forget('urlMenu');
        Cache::forget('menus_hierarchy_with_permissions');
        Cache::forget('app_settings');
    }
}
