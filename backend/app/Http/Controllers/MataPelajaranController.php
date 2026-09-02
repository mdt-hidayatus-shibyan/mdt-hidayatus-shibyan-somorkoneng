<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportMataPelajaranRequest;
use App\Http\Requests\MataPelajaranRequest;
use App\Models\Level;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $levels = Level::withCount('mataPelajarans')->berdasarkanHakAkses()->orderBy('id', 'asc')->get();
        return view('mata-pelajaran.index', compact('levels'));
    }

    public function levelShow($level_id)
    {
        $level = Level::with(['mataPelajarans' => function ($q) {
            $q->orderBy('nama_mapel', 'asc');
        }])->findOrFail($level_id);

        return view('mata-pelajaran.level', compact('level'));
    }

    public function create(Request $request, $level_id)
    {
        $levelId = $level_id;
        if ($request->ajax()) {
            $mataPelajaran = new MataPelajaran();

            return view('mata-pelajaran.form', compact('mataPelajaran', 'levelId'));
        }
        return redirect()->route('mata-pelajaran.level', $levelId)->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function edit(Request $request, $level_id, $mapel_id)
    {
        $levelId = $level_id;
        if ($request->ajax()) {
            $mataPelajaran = MataPelajaran::findOrFail($mapel_id);
            $levelId = $mataPelajaran->level_id;

            return view('mata-pelajaran.form', compact('mataPelajaran', 'levelId'));
        }
        return redirect()->route('mata-pelajaran.level', $levelId)->with('error', 'Silakan gunakan tombol edit');
    }

    public function store(MataPelajaranRequest $request)
    {
        $validated = $request->validated();

        MataPelajaran::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Mata pelajaran berhasil ditambahkan!'
        ]);
    }

    public function update(MataPelajaranRequest $request, $id)
    {
        $mataPelajaran = MataPelajaran::findOrFail($id);

        $validated = $request->validated();

        $mataPelajaran->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Mata pelajaran berhasil diperbarui!'
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'is_active' => 'required|boolean'
            ]);

            $mapel = MataPelajaran::findOrFail($id);

            $mapel->is_active = $request->boolean('is_active');
            $mapel->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Status Mata Pelajaran berhasil diperbarui!',
                'reload'  => false
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        if (request()->ajax()) {
            MataPelajaran::findOrFail($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus!', 'reload'  => false]);
        }
        return redirect()->route('mata-pelajaran.index')->with('error', 'Silakan gunakan tombol hapus hapus.');
    }



    public function modalImport()
    {
        return view('mata-pelajaran.import');
    }


    public function template()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_import_mata_pelajaran.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 8 Kolom lengkap
        $columns = [
            'id',
            'level_id',
            'kode_mapel',
            'nama_mapel',
            'kelompok',
            'referensi',
            'pengarang',
            'penerbit',
            'is_active'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Contoh isian baris pertama
            fputcsv($file, [
                '',
                '1', // ID Kelas/Level (Wajib Angka)
                'KODE-001',
                'Fiqih Ibadah',
                'Wajib',
                'Safinatun Najah',
                'Syeikh....',
                'Hidas Pustaka',
                '1' // 1 untuk Aktif, 0 untuk Non-Aktif
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(ImportMataPelajaranRequest $request)
    {


        $file = $request->file('file_import');

        $barisPertama = fgets(fopen($file->path(), 'r'));
        $delimiter = strpos($barisPertama, ';') !== false ? ';' : ',';

        $handle = fopen($file->path(), 'r');
        $header = fgetcsv($handle, 1000, $delimiter);

        $berhasil = 0;
        $gagal_kolom = 0;
        $gagal_level = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $row = array_filter($row, function ($value) {
                    return $value !== null;
                });

                if (count($row) < 8) {
                    $gagal_kolom++;
                    continue;
                }

                $cleanNull = function ($value) {
                    $trimmed = trim($value);
                    if ($trimmed === '' || strtolower($trimmed) === 'null' || $trimmed === '-') {
                        return null;
                    }
                    return $trimmed;
                };

                $id             = $cleanNull($row[0]);
                $level_id       = trim($row[1]);
                $kode_mapel = $cleanNull($row[2]);
                $nama_mapel = $cleanNull($row[3]);

                $inputKelompok = ucfirst(strtolower(trim($row[4])));
                $kelompok = in_array($inputKelompok, ['Wajib', 'Ekstra']) ? $inputKelompok : 'Wajib';

                $referensi = $cleanNull($row[5]);
                $pengarang   = $cleanNull($row[6]);
                $penerbit   = $cleanNull($row[7]);

                $is_active = trim($row[8]) === '0' ? 0 : 1; // Default aktif jika tidak diisi 0

                // Wajib ada nama dan level
                if (empty($kode_mapel) || empty($nama_mapel) || empty($level_id)) {
                    $gagal_kolom++;
                    continue;
                }

                // Cek apakah level_id valid di tabel levels
                $levelEksis = \App\Models\Level::where('id', $level_id)->exists();
                if (!$levelEksis) {
                    $gagal_level++;
                    continue;
                }

                if (!empty($id)) {
                    $mapel = MataPelajaran::find($id);
                    if (!$mapel) {
                        $mapel = new MataPelajaran();
                        $mapel->id = $id;
                    }
                } else {
                    $mapel = new MataPelajaran();
                }

                $mapel->level_id       = $level_id;
                $mapel->kode_mapel = $kode_mapel;
                $mapel->nama_mapel = $nama_mapel;
                $mapel->kelompok       = $kelompok;
                $mapel->referensi      = $referensi;
                $mapel->pengarang        = $pengarang;
                $mapel->penerbit        = $penerbit;
                $mapel->is_active      = $is_active;

                $mapel->save();

                $berhasil++;
            }
            \Illuminate\Support\Facades\DB::commit();
            fclose($handle);

            $pesan = "Import Selesai! Berhasil: $berhasil data pelajaran.";
            if ($gagal_kolom > 0) $pesan .= " Gagal (kosong/format salah): $gagal_kolom baris.";
            if ($gagal_level > 0) $pesan .= " Gagal (ID Kelas tidak ditemukan): $gagal_level baris.";

            return redirect()->route('mata-pelajaran.index')->with('success', $pesan);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            fclose($handle);
            return redirect()->route('mata-pelajaran.index')->withErrors(['file_import' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}
