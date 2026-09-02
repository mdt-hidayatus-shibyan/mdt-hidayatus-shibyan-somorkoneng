<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // =========================================================
        // UPDATE STATUS ONLINE
        // =========================================================
        $user = $request->user();
        if ($user) {
            $user->update([
                'is_login'  => true,
                'is_logout' => false,
            ]);

            // Default fallback (Administrator dan role lainnya)
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Fallback jika objek $user gagal dimuat
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            $user->update([
                'is_login'  => false,
                'is_logout' => true,
            ]);
        }
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
