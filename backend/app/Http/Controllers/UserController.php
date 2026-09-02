<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class UserController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $currentUser = auth()->user();

        // Ambil User beserta Role (Spatie) dan Tingkatnya
        $users = User::with(['roles', 'tingkat'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            // LOGIKA PENYEMBUNYIAN: Sembunyikan superadmin jika yang login bukan superadmin
            ->when(!$currentUser->hasRole('administrator'), function ($query) {
                return $query->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'administrator');
                });
            })
            // 1. Urutkan berdasarkan nama secara alfabetis terlebih dahulu
            ->orderBy('name', 'asc')
            ->get()
            // 2. TARIK USER YANG ONLINE KE PALING ATAS
            ->sortByDesc(function ($user) {
                return $user->isOnline(); // Yang bernilai true (Online) akan naik ke urutan #1
            })
            ->values(); // Reset struktur indeks array agar perulangan di Blade tetap rapi

        return view('user.index', compact('users'));
    }

    /**
     * FITUR 1: FORCE LOGOUT
     * Memutus sesi pengguna secara paksa dari sistem
     */
    public function forceLogout($id)
    {
        $user = User::findOrFail($id);

        // Keamanan tambahan: Cegah admin menendang dirinya sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat me-logout akun Anda sendiri dari sini.');
        }

        // 1. Hapus paksa semua sesi milik user ini di database session
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // 2. Hancurkan "Remember Token" agar mereka tidak bisa Auto-Login kembali
        // 3. Mundurkan last_seen_at 5 menit agar statusnya langsung berubah menjadi "Offline"
        $user->update([
            'remember_token' => null,
            'is_login'  => false,
            'is_logout' => true,
            'last_seen_at'   => \Carbon\Carbon::now()->subMinutes(5)
        ]);

        return back()->with('success', "Sesi pengguna {$user->name} berhasil diputus! Mereka telah ter-logout secara permanen.");
    }

    /**
     * FITUR 2: HUBUNGI WHATSAPP
     * Mencari nomor HP berdasarkan peran/profil, lalu me-redirect ke WA
     */
    public function hubungiWhatsApp($id)
    {
        $user = User::findOrFail($id);
        $nomorHp = null;

        // Ambil nomor HP dari tabel relasi profil (sesuaikan dengan nama relasi di Model User Anda)
        if ($user->hasRole('administrator') && $user->administrator) {
            $nomorHp = $user->administrator->no_hp;
        } elseif ($user->hasRole('ustadz') && $user->ustadz) {
            $nomorHp = $user->ustadz->no_hp;
        }

        // Jika nomor HP tidak diisi di profil
        if (empty($nomorHp)) {
            return back()->with('error', "Nomor WhatsApp belum didaftarkan untuk profil {$user->name}.");
        }

        // Normalisasi Nomor: Hapus spasi/strip, dan ubah awalan '0' menjadi '62'
        $nomorHp = preg_replace('/[^0-9]/', '', $nomorHp);
        if (str_starts_with($nomorHp, '0')) {
            $nomorHp = '62' . substr($nomorHp, 1);
        }

        // (Opsional) Pesan pembuka otomatis
        $pesan = urlencode("Assalamu'alaikum Ust/Ustz {$user->name}, kami dari Admin Madrasah ingin menginformasikan sesuatu.");

        // Redirect keluar dari aplikasi menuju API WhatsApp
        return redirect()->away("https://wa.me/{$nomorHp}?text={$pesan}");
    }
}
