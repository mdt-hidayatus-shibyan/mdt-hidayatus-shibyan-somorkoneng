<?php

namespace App\Http\Controllers\Arsip;

use App\Http\Controllers\Controller;
use App\Models\Arsip\ArsipDokumen;
use App\Models\TahunPelajaran;
use App\Models\Ruangan;
use App\Models\Ujian\Ujian;
use Illuminate\Http\Request;

class ArsipRaporController extends Controller
{
    public function index(Request $request)
    {
        // 1. AMBIL DATA UNTUK KEBUTUHAN DROPDOWN FILTER
        $daftarTahun = TahunPelajaran::orderBy('id', 'asc')->get();
        $tahunPelajaranId = $request->tahun_id ?? TahunPelajaran::where('is_active', true)->value('id') ?? $daftarTahun->first()->id;
        $daftarRuangan = Ruangan::where('tahun_pelajaran_id', $tahunPelajaranId)->berdasarkanHakAkses()->orderBy('id', 'asc')->get();

        $isKelasAkhir = false;
        if ($request->filled('ruangan')) {
            $ruanganTerpilih = Ruangan::with('level')->where('nama_ruangan', $request->ruangan)->first();
            $levelNama = $ruanganTerpilih->level->nama_level ?? '';
            $isKelasAkhir = in_array($levelNama, ['3 TPQ', '6 IBT', '3 TSA']);
        }

        // 2. QUERY DAFTAR UJIAN BERDASARKAN KELAS
        $queryUjian = Ujian::orderBy('id', 'asc');

        if ($request->filled('ruangan')) {
            if ($isKelasAkhir) {
                $queryUjian->whereIn('tipe_ujian', ['IMDA 1', 'IMNI']);
            } else {
                $queryUjian->whereIn('tipe_ujian', ['IMDA 1', 'IMDA 2']);
            }
        }

        $daftarUjian = $queryUjian->get();

        // 4. QUERY UTAMA PENCARIAN ARSIP DOKUMEN
        $query = ArsipDokumen::with('pencetak')
            ->where('tipe_dokumen', 'rapor_murid')
            ->orderBy('created_at', 'desc');

        if ($request->filled('tahun_pelajaran')) {
            $query->where('snapshot_data->tahun_pelajaran', $request->tahun_pelajaran);
        }

        if ($request->filled('ruangan')) {
            $query->where('snapshot_data->nama_ruangan', $request->ruangan);
        }

        if ($request->filled('ujian')) {
            $query->where('snapshot_data->nama_ujian', $request->ujian);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('snapshot_data->nama_murid', 'LIKE', "%{$search}%")
                    ->orWhere('snapshot_data->nomor_dokumen', 'LIKE', "%{$search}%")
                    ->orWhere('snapshot_data->nism', 'LIKE', "%{$search}%");
            });
        }

        $arsips = $query->paginate(15)->withQueryString();

        // 5. KIRIM SEMUA DATA KE VIEW
        return view('arsip-dokumen.rapor.index', compact(
            'arsips',
            'daftarTahun',
            'daftarRuangan',
            'daftarUjian'
        ));
    }
}
