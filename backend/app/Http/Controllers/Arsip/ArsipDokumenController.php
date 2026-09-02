<?php

namespace App\Http\Controllers\Arsip;

use App\Http\Controllers\Controller;
use App\Models\Arsip\ArsipDokumen;
use Illuminate\Http\Request;

class ArsipDokumenController extends Controller
{
    /**
     * Cetak dokumen universal berdasarkan data JSON yang sudah dibekukan
     */
    public function cetak($id)
    {
        // 1. Cari data arsip, jika tidak ada tampilkan 404
        $arsip = ArsipDokumen::findOrFail($id);

        // 2. Ekstrak data JSON yang sudah dibekukan
        // $data ini berisi identitas, nilai, dll yang tidak akan pernah berubah
        $data = $arsip->snapshot_data;

        // 3. Arahkan ke file Blade cetak masing-masing berdasarkan tipe dokumennya
        switch ($arsip->tipe_dokumen) {

            case 'rapor_murid':
                // Pastikan path view ini sesuai dengan file blade cetak rapor arsip Anda
                return view('cetak-baru.cetak_rapor_arsip', compact('data', 'arsip'));

            case 'sk_keputusan':
                // Pastikan path view ini sesuai dengan file blade cetak SK arsip Anda
                return view('cetak-baru.cetak_sk_arsip', compact('data', 'arsip'));

            case 'ijazah':
                // Ini akan memanggil view ijazah yang sudah kita percantik sebelumnya
                // Pastikan path view-nya sesuai ('cetak_ijazah_arsip.blade.php' diletakkan di mana)
                return view('cetak-baru.cetak_ijazah_arsip', compact('data', 'arsip'));

            default:
                // Jika tipe dokumen tidak dikenali (misal salah input atau data corrupt)
                abort(404, 'Tipe dokumen tidak dikenali atau format belum didukung untuk dicetak.');
        }
    }
}
