<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanKendala;
use Illuminate\Http\Request;

class BantuanController extends Controller
{
    /**
     * Dapatkan informasi kontak resmi admin & bantuan madrasah
     */
    public function getKontak(Request $request)
    {
        $adminName = getSetting('admin_contact_name', 'Admin & Pengelola Sistem MDTHS');
        $adminPhone = getSetting('app_phone', '081234567890');
        $adminEmail = getSetting('app_email', 'info@mdthidayatusshibyan.sch.id');
        $madrasahAddress = getSetting('app_address', 'Dsn. Somorkoneng, Ds. Somorkoneng, Kec. Kwanyar, Kab. Bangkalan');

        // Bersihkan format nomor WA untuk wa.me link
        $waFormatted = preg_replace('/[^0-9]/', '', $adminPhone);
        if (str_starts_with($waFormatted, '0')) {
            $waFormatted = '62' . substr($waFormatted, 1);
        }

        $faqs = [
            [
                'tanya' => 'Bagaimana jika lupa password atau tidak bisa login?',
                'jawab' => 'Gunakan fitur Lupa Password di halaman awal aplikasi dengan memasukkan email akun ustadz atau hubungi Administrator madrasah.'
            ],
            [
                'tanya' => 'Bagaimana cara input nilai ujian santri?',
                'jawab' => 'Buka tab Menu Utama > Penilaian > pilih Ujian & Mapel yang diampu > input nilai santri dan klik Simpan Nilai.'
            ],
            [
                'tanya' => 'Apakah ustadz bisa mencatat pelanggaran santri dari kelas lain?',
                'jawab' => 'Ya, ustadz dapat mencatat pelanggaran santri manapun melalui menu Catat Pelanggaran Santri.'
            ],
            [
                'tanya' => 'Bagaimana jika nama santri tidak muncul di daftar presensi?',
                'jawab' => 'Pastikan santri tersebut sudah ditempatkan di ruangan yang bersangkutan pada tahun ajaran aktif melalui admin madrasah.'
            ]
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'nama_admin'      => $adminName,
                'no_wa'           => $adminPhone,
                'no_wa_clean'     => $waFormatted,
                'email'           => $adminEmail,
                'alamat'          => $madrasahAddress,
                'jam_operasional' => 'Sabtu - Kamis (07.00 - 17.00 WIB)',
                'faqs'            => $faqs,
            ]
        ], 200);
    }

    /**
     * Simpan Laporan, Kendala, atau Rekomendasi Fitur dari Ustadz
     */
    public function simpanLaporan(Request $request)
    {
        $request->validate([
            'kategori'       => 'required|string|max:100',
            'judul'          => 'required|string|max:200',
            'deskripsi'      => 'required|string',
            'tipe_perangkat' => 'nullable|string|max:100',
            'versi_aplikasi' => 'nullable|string|max:50',
        ]);

        $user = $request->user();
        $ustadz = $user->ustadz;

        $laporan = LaporanKendala::create([
            'user_id'        => $user->id,
            'ustadz_id'      => $ustadz?->id,
            'kategori'       => $request->kategori,
            'judul'          => $request->judul,
            'deskripsi'      => $request->deskripsi,
            'tipe_perangkat' => $request->tipe_perangkat ?? 'Android / Mobile App',
            'versi_aplikasi' => $request->versi_aplikasi ?? '1.0.0',
            'status'         => 'Menunggu',
        ]);

        // Buat format pesan WhatsApp siap kirim
        $adminPhone = getSetting('app_phone', '081234567890');
        $waClean = preg_replace('/[^0-9]/', '', $adminPhone);
        if (str_starts_with($waClean, '0')) {
            $waClean = '62' . substr($waClean, 1);
        }

        $namaUstadz = $ustadz->nama_lengkap ?? ($user->name ?? 'Ustadz MDTHS');
        $nigm = $ustadz->nigm ?? ($user->nigm ?? '-');

        $textWa = "Assalamu'alaikum Admin MDTHS,\n\n"
            . "Saya ingin menyampaikan {$request->kategori}:\n"
            . "📌 *Tiket #LP-{$laporan->id}*\n"
            . "👤 *Pengirim:* {$namaUstadz} (NIGM: {$nigm})\n"
            . "📁 *Kategori:* {$request->kategori}\n"
            . "📝 *Judul:* {$request->judul}\n\n"
            . "💬 *Detail/Keterangan:*\n{$request->deskripsi}\n\n"
            . "📱 *Aplikasi:* Ustadz MDTHS v" . ($request->versi_aplikasi ?? '1.0.0') . "\n"
            . "🗓️ *Tanggal:* " . now()->translatedFormat('d F Y, H:i') . " WIB\n\n"
            . "Mohon untuk ditindaklanjuti. Terima kasih.";

        $waUrl = "https://wa.me/{$waClean}?text=" . urlencode($textWa);

        return response()->json([
            'success' => true,
            'message' => 'Laporan / Rekomendasi Anda berhasil dikirim ke sistem!',
            'data'    => [
                'laporan'   => $laporan,
                'wa_url'    => $waUrl,
                'wa_text'   => $textWa,
            ]
        ], 201);
    }

    /**
     * Dapatkan riwayat laporan kendala / rekomendasi yang diajukan oleh ustadz
     */
    public function getRiwayat(Request $request)
    {
        $user = $request->user();

        $riwayat = LaporanKendala::with(['responder'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $riwayat
        ], 200);
    }
}
