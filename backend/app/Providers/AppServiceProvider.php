<?php

namespace App\Providers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \Illuminate\Foundation\Vite::class,
            \App\Services\CustomVite::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\UstadzRepositoryInterface::class,
            \App\Repositories\Eloquent\UstadzRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. GATE SUPERADMIN (Yang tadi kita buat)
        Gate::before(function ($user, $ability) {
            return $user->hasRole('administrator') ? true : null;
        });

        Event::listen(function (Verified $event) {
            /** @var \App\Models\User $user */
            $user = $event->user;

            $user->update([
                'is_active' => true
            ]);
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email Anda - MDT. HIDAYATUS SHIBYAN') // Judul Email
                ->greeting('Assalamualaikum War. Wab., ' . $notifiable->name . '!') // Salam pembuka
                ->line('Terima kasih sudah bergabung. Silakan klik tombol di bawah ini untuk memverifikasi alamat email akun Anda.') // Teks paragraf 1
                ->action('Verifikasi Email Saya', $url) // Teks di dalam tombol
                ->line('Jika Anda tidak merasa mendaftar di Web Madrasah, abaikan saja email ini.'); // Teks paragraf 2
        });
    }
}
