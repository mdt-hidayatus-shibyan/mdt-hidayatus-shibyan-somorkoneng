<?php

namespace App\Http\Controllers\KasRuangan;

use App\Http\Controllers\Controller;
use App\Models\KasRuangan\PembayaranKasRuangan;
use App\Models\KasRuangan\SetoranKasRuangan;
use App\Models\Ruangan;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetoranKasRuanganController extends Controller
{
    public function indexSetoran(Request $request)
    {
        $daftarTahun = TahunPelajaran::orderBy('id', 'asc')->get();
        $tahunPelajaranId = $request->tahun_id ?? TahunPelajaran::where('is_active', true)->value('id') ?? $daftarTahun->first()->id;

        // 1. Ambil daftar ruangan untuk Dropdown Filter
        $daftarRuangan = Ruangan::where('tahun_pelajaran_id', $tahunPelajaranId)
            ->berdasarkanHakAkses()
            ->orderBy('id', 'asc')
            ->get();

        // 2. Tangkap ID ruangan jika difilter
        $ruanganId = $request->ruangan_id;

        // 3. Query utama brankas
        $query = Ruangan::with(['level', 'setoranKas.penerima'])
            ->berdasarkanHakAkses()
            ->where('tahun_pelajaran_id', $tahunPelajaranId)
            ->withSum('pembayaranKas as total_terkumpul', 'jumlah_bayar')
            ->withSum('setoranKas as total_disetor', 'jumlah_setor');

        // 4. Terapkan filter ruangan
        if ($ruanganId) {
            $query->where('id', $ruanganId);
        }

        $ruangans = $query->orderBy('id', 'asc')->get();

        return view('kas-ruangan.setoran.index', compact('ruangans', 'daftarTahun', 'tahunPelajaranId', 'daftarRuangan', 'ruanganId'));
    }

    public function simpanSetoran(Request $request)
    {
        $request->validate([
            'ruangan_id'    => 'required|exists:ruangans,id',
            'jumlah_setor'  => 'required|numeric|min:1',
            'tanggal_setor' => 'required|date',
            'keterangan'    => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            SetoranKasRuangan::create([
                'ruangan_id'    => $request->ruangan_id,
                'disetor_oleh'   => Auth::id(),
                'penerima_id'   => Auth::id(),
                'tanggal_setor' => $request->tanggal_setor,
                'jumlah_setor'  => $request->jumlah_setor,
                'keterangan'    => $request->keterangan,
            ]);

            $this->kunciCicilan($request->ruangan_id, $request->jumlah_setor);

            DB::commit();
            return redirect()->back()->with('success', 'Alhamdulillah, Uang Setoran berhasil dicatat dan masuk ke Brankas Madrasah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function riwayatSetoran($ruangan_id)
    {
        $ruangan = Ruangan::with('setoranKas.penerima')->findOrFail($ruangan_id);

        // Ambil data setoran untuk ruangan ini
        $setorans = $ruangan->setoranKas()
            ->orderBy('tanggal_setor', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Hitung sisa uang fisik yang masih di wali untuk validasi limit edit
        $terkumpul = $ruangan->pembayaranKas()->sum('jumlah_bayar');
        $disetor    = $setorans->sum('jumlah_setor');
        $diWali     = $terkumpul - $disetor;

        return view('kas-ruangan.setoran.riwayat', compact('ruangan', 'setorans', 'diWali'));
    }

    // 2. TAMBAHKAN FUNGSI UPDATE SETORAN (EDIT NOMINAL & KUNCI ULANG)
    public function updateSetoran(Request $request, $id)
    {
        $request->validate([
            'jumlah_setor'  => 'required|numeric|min:1',
            'tanggal_setor' => 'required|date',
            'keterangan'    => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            $setoran = SetoranKasRuangan::findOrFail($id);
            $ruangan_id = $setoran->ruangan_id;

            // Step A: Buka kunci lama terlebih dahulu (Kembalikan status uang ke Wali Kelas)
            $this->bukaKunciCicilan($ruangan_id, $setoran->jumlah_setor);

            // Step B: Cek apakah uang di Wali Kelas cukup untuk target setoran yang baru
            $uangDiWali = PembayaranKasRuangan::where('ruangan_id', $ruangan_id)->where('is_disetor', false)->sum('jumlah_bayar');
            if ($request->jumlah_setor > $uangDiWali) {
                throw new \Exception('Gagal! Jumlah setoran baru melebihi total uang kas yang ada di Wali Kelas.');
            }

            // Step C: Kunci ulang cicilan santri berdasarkan nominal baru
            $this->kunciCicilan($ruangan_id, $request->jumlah_setor);

            // Step D: Update data dokumen setoran
            $setoran->update([
                'tanggal_setor' => $request->tanggal_setor,
                'jumlah_setor'  => $request->jumlah_setor,
                'keterangan'    => $request->keterangan,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data setoran berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // 3. TAMBAHKAN FUNGSI DESTROY SETORAN (HAPUS & TOTAL REFUND KUNCI)
    public function destroySetoran($id)
    {
        DB::beginTransaction();

        try {
            $setoran = SetoranKasRuangan::findOrFail($id);

            // Buka kembali semua cicilan santri yang pernah dikunci oleh nominal setoran ini
            $this->bukaKunciCicilan($setoran->ruangan_id, $setoran->jumlah_setor);

            // Hapus berkas setoran
            $setoran->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Setoran berhasil dihapus. Status uang telah dikembalikan ke Wali Kelas!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus setoran: ' . $e->getMessage());
        }
    }

    private function kunciCicilan($ruangan_id, $nominal)
    {
        $sisa = $nominal;
        $cicilans = PembayaranKasRuangan::where('ruangan_id', $ruangan_id)->where('is_disetor', false)
            ->orderBy('tanggal_bayar', 'asc')->orderBy('id', 'asc')->get();

        foreach ($cicilans as $c) {
            if ($sisa >= $c->jumlah_bayar) {
                $c->update(['is_disetor' => true]);
                $sisa -= $c->jumlah_bayar;
            } else {
                break;
            }
        }
    }

    private function bukaKunciCicilan($ruangan_id, $nominal)
    {
        $sisaRefund = $nominal;
        // Ambil data yang dikunci, urutkan dari yang PALING BARU (Reverse FIFO)
        $cicilansTerkuci = PembayaranKasRuangan::where('ruangan_id', $ruangan_id)->where('is_disetor', true)
            ->orderBy('tanggal_bayar', 'desc')->orderBy('id', 'desc')->get();

        foreach ($cicilansTerkuci as $c) {
            if ($sisaRefund >= $c->jumlah_bayar) {
                $c->update(['is_disetor' => false]);
                $sisaRefund -= $c->jumlah_bayar;
            } else {
                break;
            }
        }
    }
}
