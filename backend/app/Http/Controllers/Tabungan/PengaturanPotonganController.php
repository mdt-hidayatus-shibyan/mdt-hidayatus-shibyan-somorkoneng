<?php

namespace App\Http\Controllers\Tabungan;

use App\Http\Controllers\Controller;
use App\Models\Tabungan\KategoriPenarikan;
use App\Models\Tabungan\PengaturanPotonganTabungan;
use App\Models\Tabungan\PeriodeTabungan;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanPotonganController extends Controller
{
    /**
     * Tampilan Master Periode Tabungan Berjangka & Kategori Penarikan
     */
    public function index(Request $request)
    {
        $periodes = PeriodeTabungan::with(['tahunPelajaran', 'potongans.userPengubah'])->orderBy('id', 'desc')->get();
        $tahunPelajarans = TahunPelajaran::orderBy('id', 'desc')->get();
        $kategoriPenarikans = KategoriPenarikan::withCount('transaksis')->ordered()->get();

        // Inisialisasi otomatis potongan default jika ada periode yang belum memiliki setting potongan
        foreach ($periodes as $p) {
            if ($p->potongans->isEmpty()) {
                PengaturanPotonganTabungan::inisialisasiPotonganPeriode($p->id, [], Auth::id());
                $p->load('potongans.userPengubah');
            }
        }

        return view('tabungan.pengaturan.index', compact('periodes', 'tahunPelajarans', 'kategoriPenarikans'));
    }

    /**
     * Modal Form Edit Potongan Penarikan per Periode (AJAX)
     */
    public function editPotonganModal(Request $request, $id)
    {
        $periode = PeriodeTabungan::with('potongans.userPengubah')->findOrFail($id);

        if ($periode->potongans->isEmpty()) {
            PengaturanPotonganTabungan::inisialisasiPotonganPeriode($periode->id, [], Auth::id());
            $periode->load('potongans.userPengubah');
        }

        if ($request->ajax()) {
            return view('tabungan.pengaturan.potongan_form', compact('periode'));
        }

        return redirect()->route('tabungan.pengaturan.index');
    }

    /**
     * Update Potongan Penarikan Modal per Periode (AJAX)
     */
    public function updatePotonganModal(Request $request, $id)
    {
        $request->validate([
            'potongan' => 'required|array',
            'potongan.*.persentase' => 'required|numeric|min:0|max:100',
            'potongan.*.dasar_musyawarah' => 'nullable|string|max:255',
        ]);

        $periode = PeriodeTabungan::findOrFail($id);

        foreach ($request->potongan as $jenis => $item) {
            PengaturanPotonganTabungan::updateOrCreate(
                [
                    'periode_tabungan_id' => $periode->id,
                    'jenis_nasabah' => $jenis
                ],
                [
                    'persentase_potongan' => (float) $item['persentase'],
                    'dasar_musyawarah' => $item['dasar_musyawarah'] ?? null,
                    'diubah_oleh' => Auth::id(),
                ]
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Konfigurasi potongan periode '{$periode->nama_periode}' berhasil diperbarui!"
            ], 200);
        }

        return back()->with('success', "Konfigurasi potongan periode '{$periode->nama_periode}' berhasil diperbarui!");
    }

    /**
     * Modal Form Tambah Periode Tabungan (AJAX)
     */
    public function createPeriode(Request $request)
    {
        $tahunPelajarans = TahunPelajaran::orderBy('id', 'desc')->get();

        if ($request->ajax()) {
            return view('tabungan.pengaturan.periode_form', compact('tahunPelajarans'));
        }

        return redirect()->route('tabungan.pengaturan.index');
    }

    /**
     * Modal Form Edit Periode Tabungan (AJAX)
     */
    public function editPeriode(Request $request, $id)
    {
        $periode = PeriodeTabungan::with('potongans')->findOrFail($id);
        $tahunPelajarans = TahunPelajaran::orderBy('id', 'desc')->get();

        // Pastikan potongan untuk periode ini sudah terinisialisasi
        if ($periode->potongans->isEmpty()) {
            PengaturanPotonganTabungan::inisialisasiPotonganPeriode($periode->id, [], Auth::id());
            $periode->load('potongans');
        }

        if ($request->ajax()) {
            return view('tabungan.pengaturan.periode_form', compact('periode', 'tahunPelajarans'));
        }

        return redirect()->route('tabungan.pengaturan.index');
    }

    /**
     * Tambah Periode Tabungan Berjangka Baru
     */
    public function storePeriode(Request $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajarans,id',
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_penutupan' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_pembagian' => 'required|date|after_or_equal:tanggal_penutupan',
            'catatan' => 'nullable|string',
            'potongan' => 'nullable|array',
        ]);

        if ($request->has('is_active') && $request->is_active) {
            PeriodeTabungan::where('is_active', true)->update(['is_active' => false]);
        }

        $periode = PeriodeTabungan::create([
            'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_penutupan' => $request->tanggal_penutupan,
            'tanggal_pembagian' => $request->tanggal_pembagian,
            'status' => 'Aktif',
            'is_active' => $request->has('is_active') ? true : false,
            'catatan' => $request->catatan,
        ]);

        // Inisialisasi potongan khusus untuk periode baru ini
        PengaturanPotonganTabungan::inisialisasiPotonganPeriode(
            $periode->id,
            $request->input('potongan', []),
            Auth::id()
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Periode tabungan '{$periode->nama_periode}' berhasil dibuat dengan konfigurasi potongan!"
            ], 200);
        }

        return back()->with('success', "Periode tabungan '{$request->nama_periode}' berhasil dibuat!");
    }

    /**
     * Update Data Periode Tabungan Berjangka
     */
    public function updatePeriode(Request $request, $id)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajarans,id',
            'nama_periode' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_penutupan' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_pembagian' => 'required|date|after_or_equal:tanggal_penutupan',
            'status' => 'required|in:Aktif,Ditutup,Selesai',
            'catatan' => 'nullable|string',
            'potongan' => 'nullable|array',
        ]);

        $periode = PeriodeTabungan::findOrFail($id);

        if ($request->has('is_active') && $request->is_active) {
            PeriodeTabungan::where('id', '!=', $id)->update(['is_active' => false]);
        }

        $periode->update([
            'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_penutupan' => $request->tanggal_penutupan,
            'tanggal_pembagian' => $request->tanggal_pembagian,
            'status' => $request->status,
            'is_active' => $request->has('is_active') ? true : false,
            'catatan' => $request->catatan,
        ]);

        // Update potongan jika disertakan dalam request
        if ($request->has('potongan')) {
            PengaturanPotonganTabungan::inisialisasiPotonganPeriode(
                $periode->id,
                $request->input('potongan', []),
                Auth::id()
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Periode tabungan '{$periode->nama_periode}' berhasil diperbarui!"
            ], 200);
        }

        return back()->with('success', "Periode tabungan '{$periode->nama_periode}' berhasil diperbarui!");
    }

    /**
     * Hapus Periode Tabungan Berjangka
     */
    public function destroyPeriode($id)
    {
        $periode = PeriodeTabungan::withCount('tabungans')->findOrFail($id);

        if ($periode->tabungans_count > 0) {
            $msg = "Periode '{$periode->nama_periode}' tidak dapat dihapus karena sudah memiliki {$periode->tabungans_count} rekening tabungan terkait. Anda dapat mengubah statusnya menjadi 'Ditutup' atau 'Selesai'.";
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $nama = $periode->nama_periode;
        $periode->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Periode tabungan '{$nama}' berhasil dihapus!"
            ], 200);
        }

        return back()->with('success', "Periode tabungan '{$nama}' berhasil dihapus!");
    }

    /**
     * Toggle / Set Status Periode Aktif via AJAX
     */
    public function toggleStatusPeriode(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $periode = PeriodeTabungan::findOrFail($id);

        if ($request->is_active == 1) {
            PeriodeTabungan::where('id', '!=', $id)->update(['is_active' => false]);
        }

        $periode->is_active = (bool) $request->is_active;
        $periode->save();

        return response()->json([
            'status' => 'success',
            'message' => "Status aktif periode '{$periode->nama_periode}' berhasil diperbarui!"
        ], 200);
    }

    /**
     * Set Periode Aktif
     */
    public function setPeriodeAktif($id)
    {
        PeriodeTabungan::where('is_active', true)->update(['is_active' => false]);
        $periode = PeriodeTabungan::findOrFail($id);
        $periode->update(['is_active' => true]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Periode '{$periode->nama_periode}' sekarang aktif!"
            ], 200);
        }

        return back()->with('success', "Periode '{$periode->nama_periode}' sekarang aktif!");
    }

    /**
     * Modal Form Tambah Kategori Penarikan (AJAX)
     */
    public function createKategori(Request $request)
    {
        if ($request->ajax()) {
            return view('tabungan.pengaturan.kategori_form');
        }

        return redirect()->route('tabungan.pengaturan.index');
    }

    /**
     * Modal Form Edit Kategori Penarikan (AJAX)
     */
    public function editKategori(Request $request, $id)
    {
        $kategori = KategoriPenarikan::findOrFail($id);

        if ($request->ajax()) {
            return view('tabungan.pengaturan.kategori_form', compact('kategori'));
        }

        return redirect()->route('tabungan.pengaturan.index');
    }

    /**
     * Simpan Kategori Penarikan Baru
     */
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'kode_kategori' => 'nullable|string|max:50|unique:kategori_penarikans,kode_kategori',
            'jenis_tujuan' => 'required|in:Tunai,Tagihan,Kas Ruangan,Lainnya',
            'keterangan' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer|min:0',
        ], [
            'nama_kategori.required' => 'Nama kategori penarikan wajib diisi.',
            'kode_kategori.unique' => 'Kode kategori penarikan sudah digunakan.',
            'jenis_tujuan.required' => 'Jenis tujuan penarikan wajib dipilih.',
        ]);

        $kategori = KategoriPenarikan::create([
            'nama_kategori' => $request->nama_kategori,
            'kode_kategori' => !empty($request->kode_kategori) ? strtoupper(trim($request->kode_kategori)) : null,
            'jenis_tujuan' => $request->jenis_tujuan,
            'keterangan' => $request->keterangan,
            'urutan' => (int) ($request->urutan ?? 0),
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Kategori penarikan '{$kategori->nama_kategori}' berhasil ditambahkan!"
            ], 200);
        }

        return back()->with('success', "Kategori penarikan '{$kategori->nama_kategori}' berhasil ditambahkan!");
    }

    /**
     * Update Data Kategori Penarikan
     */
    public function updateKategori(Request $request, $id)
    {
        $kategori = KategoriPenarikan::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:100',
            'kode_kategori' => 'nullable|string|max:50|unique:kategori_penarikans,kode_kategori,' . $id,
            'jenis_tujuan' => 'required|in:Tunai,Tagihan,Kas Ruangan,Lainnya',
            'keterangan' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer|min:0',
        ], [
            'nama_kategori.required' => 'Nama kategori penarikan wajib diisi.',
            'kode_kategori.unique' => 'Kode kategori penarikan sudah digunakan.',
            'jenis_tujuan.required' => 'Jenis tujuan penarikan wajib dipilih.',
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'kode_kategori' => !empty($request->kode_kategori) ? strtoupper(trim($request->kode_kategori)) : null,
            'jenis_tujuan' => $request->jenis_tujuan,
            'keterangan' => $request->keterangan,
            'urutan' => (int) ($request->urutan ?? 0),
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Kategori penarikan '{$kategori->nama_kategori}' berhasil diperbarui!"
            ], 200);
        }

        return back()->with('success', "Kategori penarikan '{$kategori->nama_kategori}' berhasil diperbarui!");
    }

    /**
     * Hapus Kategori Penarikan
     */
    public function destroyKategori($id)
    {
        $kategori = KategoriPenarikan::withCount('transaksis')->findOrFail($id);

        if ($kategori->transaksis_count > 0) {
            $msg = "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena telah digunakan pada {$kategori->transaksis_count} transaksi penarikan tabungan. Anda dapat menonaktifkan statusnya.";
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => "Kategori penarikan '{$nama}' berhasil dihapus!"
            ], 200);
        }

        return back()->with('success', "Kategori penarikan '{$nama}' berhasil dihapus!");
    }

    /**
     * Toggle Status Aktif/Nonaktif Kategori Penarikan via AJAX
     */
    public function toggleStatusKategori(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $kategori = KategoriPenarikan::findOrFail($id);
        $kategori->is_active = (bool) $request->is_active;
        $kategori->save();

        return response()->json([
            'status' => 'success',
            'message' => "Status kategori '{$kategori->nama_kategori}' berhasil diubah menjadi " . ($kategori->is_active ? 'Aktif' : 'Non-Aktif') . "!"
        ], 200);
    }
}
