<?php

namespace App\Services;

use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Request;

class CustomVite extends Vite
{
    /**
     * Get the path to a given asset when running in HMR mode.
     * Secara dinamis menyesuaikan host URL Vite dengan host request (IP LAN/HP/Localhost)
     * agar asset CSS/JS dan HMR dapat dimuat dengan sempurna saat diakses dari perangkat mobile/HP.
     *
     * @param  string  $asset
     * @return string
     */
    protected function hotAsset($asset)
    {
        $rawUrl = rtrim(file_get_contents($this->hotFile()));
        $parsed = parse_url($rawUrl);
        $serverHost = $parsed['host'] ?? 'localhost';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : ':5173';
        $scheme = $parsed['scheme'] ?? 'http';

        // Jika hot host adalah 0.0.0.0, localhost, 127.0.0.1, atau IPv6, ganti dengan Host HTTP aktif
        $requestHost = Request::getHost();
        if ($requestHost && in_array($serverHost, ['0.0.0.0', '127.0.0.1', 'localhost', '::', '[::]', '::1', '[::1]'])) {
            $rawUrl = "{$scheme}://{$requestHost}{$port}";
        }

        return $rawUrl . '/' . $asset;
    }
}