<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulanRequest;
use App\Http\Requests\SemesterRequest;
use App\Models\BulanHijriyah;
use App\Models\PengaturanAkademik;
use App\Models\Semester;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;


class PengaturanAkademikController extends Controller
{


    public function index(Request $request)
    {
        // 1. KODE YANG DIPERBAIKI: Panggil sebagai dua relasi terpisah yang sejajar
        $tahunPelajarans = TahunPelajaran::with(['semesters', 'bulanHijriyahs'])->orderBy('id', 'asc')->get();

        // 2. Tentukan Tahun Pelajaran yang akan ditampilkan konfigurasinya
        $selectedTpId = $request->query('tp_id');
        if (!$selectedTpId && $tahunPelajarans->isNotEmpty()) {
            $activeTp = $tahunPelajarans->where('is_active', 1)->first();
            $selectedTpId = $activeTp ? $activeTp->id : $tahunPelajarans->first()->id;
        }

        $konfig = null;
        if ($selectedTpId) {
            // 3. Cek apakah konfig untuk tahun terpilih sudah ada
            $konfig = PengaturanAkademik::where('tahun_pelajaran_id', $selectedTpId)->first();

            // KECERDASAN SISTEM: Jika belum ada, buat otomatis!
            if (!$konfig) {
                $konfigLama = PengaturanAkademik::where('tahun_pelajaran_id', '<', $selectedTpId)
                    ->orderBy('tahun_pelajaran_id', 'desc')
                    ->first();

                $konfig = PengaturanAkademik::create([
                    'tahun_pelajaran_id' => $selectedTpId,
                    'bobot_imda' => $konfigLama ? $konfigLama->bobot_imda : 60,
                    'bobot_akhlaq' => $konfigLama ? $konfigLama->bobot_akhlaq : 40,
                    'bobot_presensi' => $konfigLama ? $konfigLama->bobot_presensi : 24,
                    'bobot_pelanggaran' => $konfigLama ? $konfigLama->bobot_pelanggaran : 16,
                    'poin_alpha' => $konfigLama ? $konfigLama->poin_alpha : 1,
                    'poin_izin' => $konfigLama ? $konfigLama->poin_izin : 0.16,
                    'poin_hadir' => $konfigLama ? $konfigLama->poin_hadir : 0,
                    'poin_dispen' => $konfigLama ? $konfigLama->poin_dispen : 0
                ]);
            }
        }

        return view('pengaturan-akademik.index', compact('konfig', 'tahunPelajarans', 'selectedTpId'));
    }




    // Fungsi Update Konfigurasi Nilai (Support Multitahun)
    public function updateKonfig(Request $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajarans,id',
            'bobot_imda' => 'required|numeric',
            'bobot_akhlaq' => 'required|numeric',
        ]);

        // Simpan / Timpa berdasarkan tahun_pelajaran_id
        PengaturanAkademik::updateOrCreate(
            ['tahun_pelajaran_id' => $request->tahun_pelajaran_id],
            [
                'bobot_imda' => $request->bobot_imda,
                'bobot_akhlaq' => $request->bobot_akhlaq,
                'bobot_presensi' => $request->bobot_presensi,
                'bobot_pelanggaran' => $request->bobot_pelanggaran,
                'poin_alpha' => $request->poin_alpha,
                'poin_izin' => $request->poin_izin,
                'poin_hadir' => $request->poin_hadir,
                'poin_dispen' => $request->poin_dispen,
            ]
        );

        return back()->with('success', 'Konfigurasi Akademik untuk tahun pelajaran tersebut berhasil diperbarui!');
    }




    public function createSemester(Request $request, $tahun_id = null)
    {
        $tp = $tahun_id
            ? TahunPelajaran::find($tahun_id)
            : TahunPelajaran::where('is_active', true)->first();
        if ($request->ajax()) {
            return view('pengaturan-akademik.form-semester', compact('tp'));
        }
        return redirect()->route('pengaturan-akademik.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }
    public function storeSemester(SemesterRequest $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajarans,id',
            'nama_semester' => 'required|in:Semester 1 (Ganjil),Semester 2 (Genap)',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        Semester::create([
            'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
            'nama_semester'      => $request->nama_semester,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'is_active'          => false, // Default nonaktif saat baru dibuat (opsional)
        ]);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Semester berhasil ditambahkan ke Tahun Pelajaran.'
            ]);
        }
        return back()->with('success', 'Semester berhasil ditambahkan.');
    }
    public function editSemester(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);
        $tp = $semester->tahunPelajaran;

        // Jika Anda belum membuat relasi di model, Anda bisa menggunakan cara manual ini:
        // $tp = \App\Models\TahunPelajaran::find($semester->tahun_pelajaran_id);
        if ($request->ajax()) {
            return view('pengaturan-akademik.form-semester', compact('semester', 'tp'));
        }

        return redirect()->route('pengaturan-akademik.index')->with('error', 'Silakan gunakan tombol edit pada tabel.');
    }
    public function updateSemester(SemesterRequest $request, $id)
    {
        $semester = Semester::findOrFail($id);
        $semester->update([
            'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
            'nama_semester'      => $request->nama_semester,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Semester berhasil diperbarui.'
            ]);
        }
        return back()->with('success', 'Data Semester berhasil diperbarui.');
    }
    public function activateSemester(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $semester = Semester::findOrFail($id);

        if ($request->is_active == 1) {
            Semester::where('id', '!=', $id)->update(['is_active' => 0]);
        }

        $semester->is_active = $request->is_active;
        $semester->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status Semester berhasil diperbarui!',
            'reload' => $request->is_active == 1 ? true : false
        ], 200);
    }

    public function destroySemester(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        // Balasan JSON untuk mesin .delete-ajax
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Semester beserta seluruh data di dalamnya berhasil dihapus!'
            ]);
        }

        return back()->with('success', 'Semester beserta seluruh data di dalamnya berhasil dihapus!');
    }


    public function createBulan(Request $request, $tahun_id)
    {
        // Cari Tahun Pelajaran yang akan diikat, bukan Semester
        $tahunPelajaran = TahunPelajaran::findOrFail($tahun_id);

        if ($request->ajax()) {
            return view('pengaturan-akademik.form-bulan', compact('tahunPelajaran'));
        }
        return redirect()->route('pengaturan-akademik.index')->with('error', 'Gunakan tombol pada tabel.');
    }

    public function storeBulan(BulanRequest $request)
    {
        BulanHijriyah::create([
            'tahun_pelajaran_id'     => $request->tahun_pelajaran_id,
            'nama_bulan'             => $request->nama_bulan,
            'tahun_hijriyah'         => $request->tahun_hijriyah,
            'urutan'                 => $request->urutan,
            'tanggal_mulai_masehi'   => $request->tanggal_mulai_masehi,
            'tanggal_selesai_masehi' => $request->tanggal_selesai_masehi,
            'is_active'              => false,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bulan Hijriyah berhasil ditambahkan!'
            ]);
        }

        return back()->with('success', 'Bulan berhasil ditambahkan.');
    }

    public function editBulan(Request $request, $id)
    {
        // UBAH RELASI: dari 'semester' menjadi 'tahunPelajaran'
        $bulan = BulanHijriyah::with('tahunPelajaran')->findOrFail($id);

        if ($request->ajax()) {
            return view('pengaturan-akademik.form-bulan', compact('bulan'));
        }
        return redirect()->route('pengaturan-akademik.index')->with('error', 'Gunakan tombol edit pada tabel.');
    }

    public function updateBulan(BulanRequest $request, $id)
    {
        $bulan = BulanHijriyah::findOrFail($id);

        // Asalkan form update dan BulanRequest sudah sesuai, update() ini aman.
        $bulan->update($request->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Bulan Hijriyah berhasil diperbarui!'
            ]);
        }

        return back()->with('success', 'Data Bulan berhasil diperbarui.');
    }

    public function activateBulan(Request $request, $id)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $bh = BulanHijriyah::findOrFail($id);

        if ($request->is_active == 1) {
            // PERBAIKAN LOGIKA PENTING: 
            // Hanya nonaktifkan bulan lain yang berada di TAHUN PELAJARAN YANG SAMA.
            // Jika kode lama Anda yang dipakai, bulan aktif di tahun 2024 akan ikut mati saat mengaktifkan bulan di 2026!
            BulanHijriyah::where('tahun_pelajaran_id', $bh->tahun_pelajaran_id)
                ->where('id', '!=', $id)
                ->update(['is_active' => 0]);
        }

        $bh->is_active = $request->is_active;
        $bh->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status Bulan Hijriyah berhasil diperbarui!',
            'reload' => $request->is_active == 1 ? true : false
        ], 200);
    }

    public function destroyBulan(Request $request, $id)
    {
        // Logika destroy Anda sudah sempurna, tidak perlu diubah.
        $bulan = BulanHijriyah::findOrFail($id);

        if ($bulan->is_active) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bulan yang sedang AKTIF tidak dapat dihapus. Nonaktifkan terlebih dahulu.'
                ], 422);
            }
            return back()->with('error', 'Gagal! Bulan yang sedang berstatus AKTIF tidak dapat dihapus. Nonaktifkan terlebih dahulu.');
        }

        $bulan->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bulan Hijriyah berhasil dihapus dari kalender!'
            ]);
        }
        return back()->with('success', 'Bulan Hijriyah berhasil dihapus dari kalender!');
    }
}
