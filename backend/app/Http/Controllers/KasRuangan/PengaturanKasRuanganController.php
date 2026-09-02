<?php

namespace App\Http\Controllers\KasRuangan;

use App\Http\Controllers\Controller;
use App\Models\KasRuangan\PengaturanKasRuangan;
use App\Models\Ruangan;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class PengaturanKasRuanganController extends Controller
{
    public function indexPengaturan(Request $request)
    {
        $daftarTahun = TahunPelajaran::orderBy('id', 'asc')->get();
        $tahunPelajaranId = $request->tahun_id ?? TahunPelajaran::where('is_active', true)->value('id') ?? $daftarTahun->first()->id;

        /// 1. Daftar Ruangan (Biasanya untuk opsi dropdown)
        $daftarRuangan = Ruangan::select('ruangans.*')
            ->join('levels', 'ruangans.level_id', '=', 'levels.id')
            ->berdasarkanHakAkses()
            ->where('ruangans.tahun_pelajaran_id', $tahunPelajaranId)
            ->orderBy('levels.urutan_level', 'asc')         // Prioritas 1: Urutkan dari level terkecil
            ->orderBy('ruangans.nama_ruangan', 'asc') // Prioritas 2: Urutkan abjad A-Z (jika levelnya sama)
            ->get();

        // 2. Tangkap ID ruangan yang dipilih (jika ada)
        $ruanganId = $request->ruangan_id;

        // 3. Query dasar ruangan beserta relasinya
        $query = Ruangan::with(['pengaturanKas', 'level'])
            ->select('ruangans.*') // Wajib! Menghindari bentrok antara ID 'ruangans' dan 'levels'
            ->join('levels', 'ruangans.level_id', '=', 'levels.id')
            ->berdasarkanHakAkses()
            ->where('ruangans.tahun_pelajaran_id', $tahunPelajaranId)
            ->orderBy('levels.urutan_level', 'asc')
            ->orderBy('ruangans.nama_ruangan', 'asc');

        // 4. Jika user memilih ruangan tertentu, saring datanya
        if ($ruanganId) {
            // Tambahkan prefix 'ruangans.' agar SQL tidak bingung ID mana yang dimaksud
            $query->where('ruangans.id', $ruanganId);
        }

        $ruangans = $query->get();
        return view('kas-ruangan.pengaturan', compact('ruangans', 'daftarTahun', 'tahunPelajaranId', 'daftarRuangan', 'ruanganId'));
    }

    public function autoSavePengaturan(Request $request)
    {
        // Validasi data yang dikirim dari Javascript
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'field'      => 'required|in:nominal_laki,nominal_perempuan',
            'value'      => 'required|numeric|min:0'
        ]);

        // Cari pengaturan kelas tersebut, jika belum ada, buatkan instansiasi baru
        $pengaturan = PengaturanKasRuangan::firstOrNew(['ruangan_id' => $request->ruangan_id]);

        // Update kolom (laki/perempuan) sesuai yang diketik user
        $pengaturan->{$request->field} = $request->value;
        $pengaturan->save();

        // Kembalikan respon sukses ke Javascript
        return response()->json([
            'success' => true,
            'message' => 'Tersimpan otomatis!'
        ]);
    }
}
