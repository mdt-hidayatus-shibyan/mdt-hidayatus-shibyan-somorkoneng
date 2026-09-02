<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{

    public function index()
    {

        $settings = Setting::pluck('value', 'key')->toArray();
        return view('pengaturan-aplikasi.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Ambil semua request kecuali token
        $data = $request->except('_token', '_method');

        // Daftar key yang berupa file gambar (Logo Aplikasi, Logo Kop, Favicon)
        $fileKeys = ['app_logo', 'kop_logo'];

        foreach ($fileKeys as $fileKey) {
            if ($request->hasFile($fileKey)) {
                // Upload file baru ke folder storage/app/public/settings
                $file = $request->file($fileKey);
                $path = $file->store('settings', 'public');

                // Simpan path-nya ke array data ('storage/settings/namafile.png')
                $data[$fileKey] = 'storage/' . $path;
            } else {
                // Jika tidak ada file baru yang diunggah, hapus dari array 
                // agar tidak menimpa data logo lama di database dengan nilai null
                unset($data[$fileKey]);
            }
        }

        // Looping untuk menyimpan ke tabel settings
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Hapus Cache
        Cache::forget('app_settings');

        return back()->with('success', 'Pengaturan aplikasi dan logo berhasil diperbarui!');
    }
}
