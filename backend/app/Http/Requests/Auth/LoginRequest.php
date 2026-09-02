<?php

namespace App\Http\Requests\Auth;

// use App\Models\Ruangan;
// use App\Models\TahunPelajaran;
// use App\Models\Ustadz;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Ambil inputan dari user
        $loginInput = $this->input('login');

        // 2. DETEKSI OTOMATIS: Jika formatnya valid email, maka cari di kolom 'email'. 
        // Jika tidak, cari di kolom 'username'
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $this->input('password'),
            'is_active' => 1 // Pastikan akun aktif
        ];

        // 3. Proses pengecekan ke database
        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('Kredensial yang diberikan salah atau akun Anda tidak aktif.'),
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasAnyRole(['administrator', 'staff'])) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Akses ditolak! Akun Anda hanya dapat digunakan melalui Aplikasi Mobile MDT.',
            ]);
        }

        // if ($user->hasRole('ustadz')) {
        //     $ruanganWali = null; // Ganti nama variabel biar tidak bingung

        //     // Ambil data Ustadz berdasarkan user_id
        //     $ustadz = Ustadz::where('user_id', $user->id)->first();

        //     // Ambil Tahun Pelajaran yang sedang aktif
        //     $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        //     // Jika data Ustadz dan Tahun Aktif ditemukan, ambil data ruangannya!
        //     if ($ustadz && $tahunAktif) {
        //         // GUNAKAN first() bukan exists()
        //         $ruanganWali = Ruangan::where('tahun_pelajaran_id', $tahunAktif->id)
        //             ->where('ustadz_id', $ustadz->id)
        //             ->first();
        //     }

        //     // Simpan hasil pengecekan ke dalam Session
        //     if ($ruanganWali) { // Jika objek ruangan ketemu
        //         session([
        //             'akses_sebagai'  => 'Wali Ruangan',
        //             'wali_ruangan'   => $ruanganWali->nama_ruangan, // Sekarang ini AMAN dan BISA dipanggil
        //             'is_waliruangan' => true
        //         ]);
        //     } else {
        //         session([
        //             'akses_sebagai'  => 'Ustadz',
        //             'is_waliruangan' => false
        //         ]);
        //         // (Opsional) Jika ustadz bukan wali kelas, hapus session wali_ruangan yang mungkin tersisa
        //         session()->forget('wali_ruangan');
        //     }
        // }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')) . '|' . $this->ip());
    }
}
