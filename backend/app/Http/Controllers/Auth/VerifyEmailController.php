<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // 1. Cari user berdasarkan parameter ID di URL
        $user = User::findOrFail($request->route('id'));

        // 2. Cek apakah link valid (hashing)
        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Link verifikasi tidak valid.');
        }

        // 3. Jika sudah verifikasi, arahkan ke login
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('info', 'Email sudah diverifikasi sebelumnya.');
        }

        // 4. Verifikasi akun
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            // 🌟 Update status is_active jadi true di tabel user
            $user->update(['is_active' => true]);
        }

        // 5. Lempar ke halaman Login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi! Sekarang Anda sudah bisa login.');
    }
}
