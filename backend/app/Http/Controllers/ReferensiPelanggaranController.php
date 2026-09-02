<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRefPelanggaranRequest;
use App\Http\Requests\ReferensiPelanggaranRequest;
use App\Models\ReferensiPelanggaran;
use Illuminate\Http\Request;

class ReferensiPelanggaranController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $pelanggarans = ReferensiPelanggaran::when($search, function ($query, $search) {
            return $query->where('nama_pelanggaran', 'like', '%' . $search . '%')
                ->orWhere('kategori', 'like', '%' . $search . '%')
                ->orWhere('poin', 'like', '%' . $search . '%');
        })
            ->orderBy('id', 'asc')
            ->get();

        return view('referensi-pelanggaran.index', compact('pelanggarans'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('referensi-pelanggaran.form');
        }
        return redirect()->route('referensi-pelanggaran.index')->with('error', 'Silakan gunakan tombol tambah data.');
    }

    public function store(ReferensiPelanggaranRequest $request)
    {
        ReferensiPelanggaran::create($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Data Referensi Pelanggaran berhasil ditambahkan!'
        ], 200);
    }


    public function edit(Request $request, ReferensiPelanggaran $referensiPelanggaran)
    {
        if ($request->ajax()) {
            return view('referensi-pelanggaran.form', compact('referensiPelanggaran'));
        }

        return redirect()->route('referensi-pelanggaran.index')->with('error', 'Silakan gunakan tombol edit data.');
    }


    public function update(ReferensiPelanggaranRequest $request, ReferensiPelanggaran $referensiPelanggaran)
    {
        $referensiPelanggaran->update($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Data Referensi Pelanggaran berhasil diubah!'
        ], 200);
    }


    public function destroy($id)
    {
        if (request()->ajax()) {
            ReferensiPelanggaran::findOrFail($id)->delete();
            return response()->json(['message' => 'Data berhasil dihapus!']);
        }
        return redirect()->route('referensi-pelanggaran.index')->with('error', 'Silakan gunakan tombol hapus data.');
    }

    public function modalImport(Request $request)
    {
        if ($request->ajax()) {
            return view('referensi-pelanggaran.import');
        }
        return redirect()->route('referensi-pelanggaran.index')->with('error', 'Silakan gunakan tombol import data.');
    }


    public function template()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_import_pelanggaran.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // 4 Kolom sederhana
        $columns = [
            'id',
            'nama_pelanggaran',
            'kategori',
            'poin'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['', 'Terlambat Masuk Kelas', 'Ringan', '10']);
            fputcsv($file, ['', 'Keluar Area Madrasah Tanpa Izin', 'Sedang', '50']);
            fputcsv($file, ['', 'Membawa Senjata Tajam', 'Berat', '100']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(ImportRefPelanggaranRequest $request)
    {

        $file = $request->file('file_import');

        $barisPertama = fgets(fopen($file->path(), 'r'));
        $delimiter = strpos($barisPertama, ';') !== false ? ';' : ',';

        $handle = fopen($file->path(), 'r');
        $header = fgetcsv($handle, 1000, $delimiter);

        $berhasil = 0;
        $gagal_kolom = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $row = array_filter($row, function ($value) {
                    return $value !== null;
                });

                if (count($row) < 4) {
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

                $id               = $cleanNull($row[0]);
                $nama_pelanggaran = $cleanNull($row[1]);

                $inputKategori = ucfirst(strtolower(trim($row[2])));
                $kategori = in_array($inputKategori, ['Ringan', 'Sedang', 'Berat']) ? $inputKategori : 'Ringan';

                $poinString = str_replace(',', '.', trim($row[3]));
                $poin = (float) $poinString;

                if (empty($nama_pelanggaran)) {
                    $gagal_kolom++;
                    continue;
                }

                if (!empty($id)) {
                    $pelanggaran = ReferensiPelanggaran::find($id);
                    if (!$pelanggaran) {
                        $pelanggaran = new ReferensiPelanggaran();
                        $pelanggaran->id = $id;
                    }
                } else {
                    $pelanggaran = new ReferensiPelanggaran();
                }

                $pelanggaran->nama_pelanggaran = $nama_pelanggaran;
                $pelanggaran->kategori         = $kategori;
                $pelanggaran->poin             = $poin;
                $pelanggaran->save();

                $berhasil++;
            }
            \Illuminate\Support\Facades\DB::commit();
            fclose($handle);

            $pesan = "Import Selesai! Berhasil: $berhasil data pelanggaran.";
            if ($gagal_kolom > 0) $pesan .= " Gagal (kosong/format salah): $gagal_kolom baris.";

            return redirect()->route('referensi-pelanggaran.index')->with('success', $pesan);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            fclose($handle);
            return redirect()->route('referensi-pelanggaran.index')->withErrors(['file_import' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}
