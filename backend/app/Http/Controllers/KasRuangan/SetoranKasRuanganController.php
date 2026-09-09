<?php

namespace App\Http\Controllers\KasRuangan;

use App\Http\Controllers\Controller;
use App\Models\KasRuangan\PembayaranKasRuangan;
use App\Models\KasRuangan\SetoranKasRuangan;
use App\Models\Ruangan;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use App\Models\TahunPelajaran;
use App\Services\Tabungan\TabunganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetoranKasRuanganController extends Controller
{
    protected $tabunganService;

    public function __construct(TabunganService $tabunganService)
    {
        $this->tabunganService = $tabunganService;
    }

    public function indexSetoran(Request $request)
    {
        $daftarTahun = TahunPelajaran::orderBy('id', 'asc')->get();
        $tahunPelajaranId = $request->tahun_id ?? TahunPelajaran::where('is_active', true)->value('id') ?? $daftarTahun->first()?->id;

        // 1. Ambil daftar ruangan untuk Dropdown Filter
        $daftarRuangan = Ruangan::where('tahun_pelajaran_id', $tahunPelajaranId)
            ->berdasarkanHakAkses()
            ->orderBy('id', 'asc')
            ->get();

        // 2. Tangkap ID ruangan jika difilter
        $ruanganId = $request->ruangan_id;

        // 3. Query utama brankas & tabungan
        $query = Ruangan::with(['level', 'setoranKas.penerima', 'setoranKas.penyetor', 'setoranKas.verifikator'])
            ->berdasarkanHakAkses()
            ->where('tahun_pelajaran_id', $tahunPelajaranId)
            ->withSum('pembayaranKas as total_terkumpul', 'jumlah_bayar')
            ->withSum(['setoranKas as total_disetor' => fn($q) => $q->where('status', 'Diterima')], 'jumlah_setor')
            ->withSum(['setoranKas as total_pending' => fn($q) => $q->where('status', 'Menunggu Verifikasi')], 'jumlah_setor');

        // 4. Terapkan filter ruangan
        if ($ruanganId) {
            $query->where('id', $ruanganId);
        }

        $ruangans = $query->orderBy('id', 'asc')->get();

        // Ambil data tabungan untuk masing-masing ruangan
        $ruanganIds = $ruangans->pluck('id');
        $tabungans = Tabungan::where('jenis_nasabah', 'Kas Ruangan')
            ->whereIn('ruangan_id', $ruanganIds)
            ->get()
            ->keyBy('ruangan_id');

        $totalMenungguVerifikasi = SetoranKasRuangan::whereHas('ruangan', fn($q) => $q->where('tahun_pelajaran_id', $tahunPelajaranId))
            ->where('status', 'Menunggu Verifikasi')
            ->count();

        return view('kas-ruangan.setoran.index', compact(
            'ruangans',
            'daftarTahun',
            'tahunPelajaranId',
            'daftarRuangan',
            'ruanganId',
            'tabungans',
            'totalMenungguVerifikasi'
        ));
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
            $ruangan = Ruangan::findOrFail($request->ruangan_id);

            // Cari atau buat rekening Tabungan Kas Ruangan
            $tabungan = Tabungan::firstOrCreate(
                ['jenis_nasabah' => 'Kas Ruangan', 'ruangan_id' => $ruangan->id],
                [
                    'nomor_rekening' => Tabungan::generateNomorRekening('Kas Ruangan', $ruangan),
                    'nama_rekening' => 'Tabungan Kas Ruangan ' . $ruangan->nama_ruangan,
                    'saldo' => 0.00,
                    'total_setor' => 0.00,
                    'total_tarik' => 0.00,
                    'status' => 'Aktif',
                    'dibuat_oleh' => Auth::id(),
                ]
            );

            // Karena admin/petugas yang input langsung di web, langsung disetujui & masuk Tabungan
            $trx = $this->tabunganService->setorTunai(
                tabunganId: $tabungan->id,
                nominal: (float) $request->jumlah_setor,
                petugasId: Auth::id(),
                tanggal: $request->tanggal_setor,
                keterangan: 'Setoran Kas Ruangan ' . $ruangan->nama_ruangan . ($request->keterangan ? ' - ' . $request->keterangan : ''),
                metode: 'Tunai',
                ruanganId: $ruangan->id,
            );

            $setoran = SetoranKasRuangan::create([
                'ruangan_id'            => $request->ruangan_id,
                'disetor_oleh'          => Auth::id(),
                'penerima_id'           => Auth::id(),
                'tanggal_setor'         => $request->tanggal_setor,
                'jumlah_setor'          => $request->jumlah_setor,
                'keterangan'            => $request->keterangan,
                'status'                => 'Diterima',
                'diverifikasi_oleh'     => Auth::id(),
                'diverifikasi_pada'     => now(),
                'transaksi_tabungan_id' => $trx->id,
            ]);

            $this->kunciCicilan($request->ruangan_id, $request->jumlah_setor);

            DB::commit();
            return redirect()->back()->with('success', 'Alhamdulillah, Setoran Kas Ruangan berhasil dicatat dan masuk ke Tabungan Madrasah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function riwayatSetoran($ruangan_id)
    {
        $ruangan = Ruangan::with(['level', 'setoranKas.penerima', 'setoranKas.penyetor', 'setoranKas.verifikator'])->findOrFail($ruangan_id);

        // Ambil data setoran untuk ruangan ini
        $setorans = $ruangan->setoranKas()
            ->with(['penyetor.ustadz', 'penerima.ustadz', 'verifikator.ustadz', 'transaksiTabungan'])
            ->orderBy('tanggal_setor', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Hitung sisa uang fisik yang masih di wali untuk validasi limit
        $terkumpul = $ruangan->pembayaranKas()->sum('jumlah_bayar');
        $disetor   = $setorans->where('status', 'Diterima')->sum('jumlah_setor');
        $pending   = $setorans->where('status', 'Menunggu Verifikasi')->sum('jumlah_setor');

        $tabunganKas = Tabungan::where('jenis_nasabah', 'Kas Ruangan')
            ->where('ruangan_id', $ruangan->id)
            ->first();

        $ditarik   = $tabunganKas ? (float) $tabunganKas->total_tarik : 0;
        $diWali    = max(0, ($terkumpul + $ditarik) - $disetor - $pending);

        // Ambil riwayat penarikan dari rekening Tabungan Kas Ruangan
        $penarikans = collect();
        if ($tabunganKas) {
            $penarikans = TransaksiTabungan::with(['petugas.ustadz', 'kategoriPenarikan'])
                ->where('tabungan_id', $tabunganKas->id)
                ->where('jenis_transaksi', 'Tarik')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('kas-ruangan.setoran.riwayat', compact('ruangan', 'setorans', 'diWali', 'disetor', 'pending', 'terkumpul', 'tabunganKas', 'penarikans'));
    }

    /**
     * Verifikasi pengajuan setoran kas oleh Admin / Petugas Tabungan
     */
    public function verifikasiSetoran(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Diterima,Ditolak',
            'catatan_verifikasi' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $setoran = SetoranKasRuangan::with('ruangan')->findOrFail($id);

            if ($setoran->status === 'Diterima') {
                return redirect()->back()->with('error', 'Setoran ini sudah berstatus Diterima sebelumnya.');
            }

            if ($request->status === 'Diterima') {
                $ruangan = $setoran->ruangan;

                // Cari atau buat rekening Tabungan Kas Ruangan
                $tabungan = Tabungan::firstOrCreate(
                    ['jenis_nasabah' => 'Kas Ruangan', 'ruangan_id' => $ruangan->id],
                    [
                        'nomor_rekening' => Tabungan::generateNomorRekening('Kas Ruangan', $ruangan),
                        'nama_rekening' => 'Tabungan Kas Ruangan ' . $ruangan->nama_ruangan,
                        'saldo' => 0.00,
                        'total_setor' => 0.00,
                        'total_tarik' => 0.00,
                        'status' => 'Aktif',
                        'dibuat_oleh' => Auth::id(),
                    ]
                );

                // Masukkan ke Tabungan Madrasah
                $trx = $this->tabunganService->setorTunai(
                    tabunganId: $tabungan->id,
                    nominal: (float) $setoran->jumlah_setor,
                    petugasId: Auth::id(),
                    tanggal: $setoran->tanggal_setor ? (string)$setoran->tanggal_setor : date('Y-m-d'),
                    keterangan: 'Setoran Kas Ruangan ' . $ruangan->nama_ruangan . ($setoran->keterangan ? ' - ' . $setoran->keterangan : ''),
                    metode: 'Tunai',
                    ruanganId: $ruangan->id,
                );

                // Kunci cicilan kas murid
                $this->kunciCicilan($setoran->ruangan_id, $setoran->jumlah_setor);

                $setoran->update([
                    'status' => 'Diterima',
                    'catatan_verifikasi' => $request->catatan_verifikasi,
                    'diverifikasi_oleh' => Auth::id(),
                    'diverifikasi_pada' => now(),
                    'transaksi_tabungan_id' => $trx->id,
                ]);

                DB::commit();
                return redirect()->back()->with('success', 'Alhamdulillah, Setoran Kas Ruangan sebesar Rp ' . number_format($setoran->jumlah_setor, 0, ',', '.') . ' telah diverifikasi dan masuk ke Tabungan Madrasah!');
            } else {
                // Status Ditolak
                $setoran->update([
                    'status' => 'Ditolak',
                    'catatan_verifikasi' => $request->catatan_verifikasi ?? 'Setoran ditolak oleh petugas.',
                    'diverifikasi_oleh' => Auth::id(),
                    'diverifikasi_pada' => now(),
                ]);

                DB::commit();
                return redirect()->back()->with('info', 'Setoran Kas Ruangan telah ditolak dengan catatan: ' . ($request->catatan_verifikasi ?: '-'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses verifikasi: ' . $e->getMessage());
        }
    }

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

            if ($setoran->status === 'Diterima') {
                // Step A: Buka kunci lama terlebih dahulu
                $this->bukaKunciCicilan($ruangan_id, $setoran->jumlah_setor);

                // Step B: Cek apakah uang di Wali Kelas cukup untuk target setoran yang baru
                $uangDiWali = PembayaranKasRuangan::where('ruangan_id', $ruangan_id)->where('is_disetor', false)->sum('jumlah_bayar');
                if ($request->jumlah_setor > ($uangDiWali + $setoran->jumlah_setor)) {
                    throw new \Exception('Gagal! Jumlah setoran baru melebihi total uang kas yang ada di Wali Kelas.');
                }

                // Step C: Kunci ulang cicilan murid berdasarkan nominal baru
                $this->kunciCicilan($ruangan_id, $request->jumlah_setor);

                // Step D: Sesuaikan transaksi tabungan
                if ($setoran->transaksi_tabungan_id) {
                    $trx = TransaksiTabungan::find($setoran->transaksi_tabungan_id);
                    if ($trx) {
                        $tabungan = Tabungan::find($trx->tabungan_id);
                        if ($tabungan) {
                            $selisih = $request->jumlah_setor - $setoran->jumlah_setor;
                            $tabungan->update([
                                'saldo' => $tabungan->saldo + $selisih,
                                'total_setor' => $tabungan->total_setor + $selisih,
                            ]);
                            $trx->update([
                                'nominal_kotor' => $request->jumlah_setor,
                                'nominal_bersih' => $request->jumlah_setor,
                                'saldo_akhir' => $trx->saldo_awal + $request->jumlah_setor,
                                'tanggal' => $request->tanggal_setor,
                            ]);
                        }
                    }
                }
            }

            // Update berkas setoran
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

    public function destroySetoran($id)
    {
        DB::beginTransaction();

        try {
            $setoran = SetoranKasRuangan::findOrFail($id);

            if ($setoran->status === 'Diterima') {
                // Buka kembali semua cicilan murid yang pernah dikunci
                $this->bukaKunciCicilan($setoran->ruangan_id, $setoran->jumlah_setor);

                // Kurangi saldo tabungan jika ada transaksi tabungan
                if ($setoran->transaksi_tabungan_id) {
                    $trx = TransaksiTabungan::find($setoran->transaksi_tabungan_id);
                    if ($trx) {
                        $tabungan = Tabungan::find($trx->tabungan_id);
                        if ($tabungan) {
                            $tabungan->update([
                                'saldo' => max(0, $tabungan->saldo - $setoran->jumlah_setor),
                                'total_setor' => max(0, $tabungan->total_setor - $setoran->jumlah_setor),
                            ]);
                        }
                        $trx->delete();
                    }
                }
            }

            // Hapus berkas setoran
            $setoran->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Setoran berhasil dihapus.');
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
