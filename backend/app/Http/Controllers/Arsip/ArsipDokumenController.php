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

    /**
     * Download dokumen PDF langsung
     */
    public function download($id)
    {
        $arsip = ArsipDokumen::findOrFail($id);
        $data = $arsip->snapshot_data;

        $viewName = match ($arsip->tipe_dokumen) {
            'rapor_murid' => 'cetak-baru.cetak_rapor_arsip',
            'sk_keputusan' => 'cetak-baru.cetak_sk_arsip',
            'ijazah' => 'cetak-baru.cetak_ijazah_arsip',
            default => null,
        };

        if (!$viewName) {
            abort(404, 'Tipe dokumen tidak didukung untuk unduhan PDF.');
        }

        $paper = $arsip->tipe_dokumen === 'ijazah' ? 'landscape' : 'portrait';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, compact('data', 'arsip'))
            ->setPaper('a4', $paper);

        $namaSantri = \Illuminate\Support\Str::slug($data['nama_murid'] ?? $data['nama_santri'] ?? 'santri');
        $namaDok = match ($arsip->tipe_dokumen) {
            'rapor_murid' => 'Rapor-' . \Illuminate\Support\Str::slug($data['nama_ujian'] ?? 'ujian'),
            'sk_keputusan' => 'SK-Kelulusan',
            'ijazah' => 'Ijazah-Madrasah',
            default => 'Dokumen',
        };

        $filename = "{$namaDok}-{$namaSantri}.pdf";
        return $pdf->download($filename);
    }
}
