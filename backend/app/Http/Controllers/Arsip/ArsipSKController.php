<?php

namespace App\Http\Controllers\Arsip;

use App\Http\Controllers\Controller;
use App\Models\Arsip\ArsipDokumen;
use App\Models\TahunPelajaran;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class ArsipSKController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data untuk Filter Dropdown
        $daftarTahun = TahunPelajaran::orderBy('id', 'asc')->get();
        $tahunPelajaranId = $request->tahun_id ?? TahunPelajaran::where('is_active', true)->value('id') ?? $daftarTahun->first()->id;
        $daftarRuangan = Ruangan::where('tahun_pelajaran_id', $tahunPelajaranId)->berdasarkanHakAkses()->orderBy('id', 'asc')->get();

        // 2. Query Utama khusus SK Keputusan
        // Pastikan 'sk_keputusan' sesuai dengan tipe_dokumen yang Anda simpan di database
        $query = ArsipDokumen::with('pencetak')
            ->where('tipe_dokumen', 'sk_keputusan')
            ->orderBy('created_at', 'desc');

        // 3. Terapkan Filter
        if ($request->filled('tahun_pelajaran')) {
            $query->where('snapshot_data->tahun_pelajaran', $request->tahun_pelajaran);
        }

        if ($request->filled('ruangan')) {
            $query->where('snapshot_data->nama_ruangan', $request->ruangan);
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

        return view('arsip-dokumen.sk.index', compact('arsips', 'daftarTahun', 'daftarRuangan'));
    }
}
