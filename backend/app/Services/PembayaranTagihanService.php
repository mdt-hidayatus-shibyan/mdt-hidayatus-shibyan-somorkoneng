<?php

namespace App\Services;

use App\Models\Murid;
use App\Models\PembayaranTagihan;
use App\Models\TagihanMurid;
use Illuminate\Support\Facades\DB;

class PembayaranTagihanService
{
    /**
     * Proses transaksi pembayaran tagihan wali murid
     */
    public function prosesPembayaranWali($muridId, array $tagihanIds)
    {
        return DB::transaction(function () use ($muridId, $tagihanIds) {
            $murid = Murid::with('waliMurid')->findOrFail($muridId);
            $namaWali = $murid->nama_ayah;

            $tagihans = TagihanMurid::with('pengaturanTagihan')->whereIn('id', $tagihanIds)->get();
            $totalNominal = $tagihans->sum('nominal_tagihan');

            $hari = now()->format('d');
            $bulan = now()->format('m');
            $tahun = now()->format('Y');

            $kodeTagihan = $tagihans->first()->pengaturanTagihan->kode_tagihan ?? 'TGH';
            $nism = $murid->nism ?? '0000';
            $randomCode = mt_rand(10000, 99999);

            $noKwitansi = 'TRX/' . $kodeTagihan . '/' . $nism . '/' . $tahun . '/' . $hari . $bulan . '/'  . $randomCode;

            $pembayaran = PembayaranTagihan::create([
                'no_transaksi'      => $noKwitansi,
                'tanggal_bayar'     => now(),
                'tipe_pembayar'     => 'Wali Murid',
                'nama_pembayar'     => $namaWali,
                'metode_pembayaran' => 'Tunai',
                'total_nominal'     => $totalNominal,
                'catatan'           => 'Pembayaran Syahriyah/SPP a.n. Murid: ' . $murid->nama_lengkap . ' (' . $murid->nism . ')'
            ]);

            TagihanMurid::whereIn('id', $tagihanIds)->update([
                'status_bayar'          => 'Lunas',
                'pembayaran_tagihan_id' => $pembayaran->id,
                'updated_at'            => now(),
            ]);

            return $pembayaran;
        });
    }

    /**
     * Proses pelunasan massal via Leger
     */
    public function prosesLegerPembayaran(array $tagihanIds)
    {
        return DB::transaction(function () use ($tagihanIds) {
            $tagihans = TagihanMurid::with(['murid.waliMurid', 'pengaturanTagihan'])
                ->whereIn('id', $tagihanIds)
                ->get()
                ->groupBy('murid_id');

            $bulan = now()->format('m');
            $tahun = now()->format('Y');
            $totalCount = count($tagihanIds);

            foreach ($tagihans as $muridId => $tagihanGroup) {
                $murid = $tagihanGroup->first()->murid;

                $kodeTagihan = $tagihanGroup->first()->pengaturanTagihan->kode_tagihan ?? 'TGH';
                $namaWali =  $murid->nama_ayah;
                $totalNominal = $tagihanGroup->sum('nominal_tagihan');

                $nism = $murid->nism ?? '0000';
                $randomCode = mt_rand(10000, 99999);
                $noKwitansi = 'TRX/' . $kodeTagihan . '/' . $nism . '/' . $tahun . '/' . $bulan . '/'  . $randomCode;

                $pembayaran = PembayaranTagihan::create([
                    'no_transaksi'      => $noKwitansi,
                    'tanggal_bayar'     => now(),
                    'tipe_pembayar'     => 'Wali Murid',
                    'nama_pembayar'     => $namaWali,
                    'metode_pembayaran' => 'Tunai',
                    'total_nominal'     => $totalNominal,
                    'catatan'           => 'Pembayaran Massal (Leger) a.n. Murid: ' . $murid->nama_lengkap,
                ]);

                TagihanMurid::whereIn('id', $tagihanGroup->pluck('id'))->update([
                    'status_bayar'          => 'Lunas',
                    'pembayaran_tagihan_id' => $pembayaran->id,
                    'updated_at'            => now(),
                ]);
            }

            return $totalCount;
        });
    }
}
