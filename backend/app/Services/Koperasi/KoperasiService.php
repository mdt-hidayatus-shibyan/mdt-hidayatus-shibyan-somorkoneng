<?php

namespace App\Services\Koperasi;

use App\Models\Koperasi\MutasiStokKoperasi;
use App\Models\Koperasi\PaketKoperasi;
use App\Models\Koperasi\PembelianDetailKoperasi;
use App\Models\Koperasi\PembelianKoperasi;
use App\Models\Koperasi\PenjualanDetailKoperasi;
use App\Models\Koperasi\PenjualanKoperasi;
use App\Models\Koperasi\ProdukKoperasi;
use App\Models\Tabungan\Tabungan;
use App\Models\Tabungan\TransaksiTabungan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KoperasiService
{
    /**
     * Memproses Transaksi Checkout Penjualan Kasir POS (Produk & Paket Bundling)
     */
    public function checkoutPenjualan(array $data, int $petugasId): PenjualanKoperasi
    {
        return DB::transaction(function () use ($data, $petugasId) {
            $items = $data['items'] ?? [];
            if (empty($items)) {
                throw new \InvalidArgumentException('Keranjang belanja kasir masih kosong.');
            }

            $nomorNota = PenjualanKoperasi::generateNomorNota();
            $tglTransaksi = $data['tanggal'] ?? now();
            $jenisPelanggan = $data['jenis_pelanggan'] ?? 'Umum';
            $muridId = ($jenisPelanggan === 'Murid') ? ($data['murid_id'] ?? null) : null;
            $ustadzId = ($jenisPelanggan === 'Ustadz') ? ($data['ustadz_id'] ?? null) : null;
            $namaUmum = ($jenisPelanggan === 'Umum') ? ($data['nama_pelanggan_umum'] ?? 'Pelanggan Umum') : null;

            $totalItem = 0;
            $totalHpp = 0.00;
            $subtotal = 0.00;
            $diskon = (float) ($data['diskon'] ?? 0);

            $processedDetails = [];

            // 1. Validasi & Pengurangan Stok per Item
            foreach ($items as $item) {
                $tipeItem = $item['tipe'] ?? 'Produk'; // 'Produk' atau 'Paket_Bundling'
                $qty = (int) ($item['jumlah'] ?? 1);
                if ($qty <= 0) continue;

                $totalItem += $qty;

                if ($tipeItem === 'Paket_Bundling') {
                    $paketId = (int) $item['paket_id'];
                    $paket = PaketKoperasi::with('items.produk')->where('id', $paketId)->firstOrFail();

                    if (!$paket->is_active) {
                        throw new \Exception("Paket Bundling '{$paket->nama_paket}' sedang dinonaktifkan.");
                    }

                    // Cek ketersediaan stok seluruh komponen dalam paket
                    $rincianKomponen = [];
                    $totalHppPaket = 0.00;

                    foreach ($paket->items as $pktItem) {
                        $prod = ProdukKoperasi::where('id', $pktItem->produk_id)->lockForUpdate()->firstOrFail();
                        $kebutuhan = $pktItem->jumlah * $qty;

                        if ($prod->stok < $kebutuhan) {
                            throw new \Exception("Stok tidak mencukupi untuk item '{$prod->nama_produk}' dalam paket '{$paket->nama_paket}'. Stok saat ini: {$prod->stok}, dibutuhkan: {$kebutuhan}.");
                        }

                        $stokSebelum = $prod->stok;
                        $stokSesudah = $stokSebelum - $kebutuhan;

                        // Kurangi stok produk
                        $prod->update(['stok' => $stokSesudah]);

                        // Catat mutasi stok komponen paket
                        MutasiStokKoperasi::create([
                            'produk_id' => $prod->id,
                            'jenis_mutasi' => 'Penjualan_Paket',
                            'jumlah' => -$kebutuhan,
                            'stok_sebelum' => $stokSebelum,
                            'stok_sesudah' => $stokSesudah,
                            'referensi' => $nomorNota,
                            'keterangan' => "Penjualan Paket: {$paket->nama_paket} ({$qty} paket x {$pktItem->jumlah} {$prod->satuan})",
                            'petugas_id' => $petugasId,
                        ]);

                        $hppItem = (float) $prod->harga_beli * $pktItem->jumlah;
                        $totalHppPaket += $hppItem;

                        $rincianKomponen[] = [
                            'produk_id' => $prod->id,
                            'kode_produk' => $prod->kode_produk,
                            'nama_produk' => $prod->nama_produk,
                            'jumlah_per_paket' => $pktItem->jumlah,
                            'total_qty_keluar' => $kebutuhan,
                            'satuan' => $prod->satuan,
                            'harga_beli' => (float) $prod->harga_beli,
                        ];
                    }

                    $hargaJualPaket = (float) ($item['harga_jual'] ?? $paket->harga_paket);
                    $subtotalPaket = $hargaJualPaket * $qty;
                    $diskonItem = (float) ($item['diskon_item'] ?? 0);
                    $subtotalItemAkhir = max(0, $subtotalPaket - $diskonItem);
                    $totalHppItem = $totalHppPaket * $qty;
                    $keuntunganItem = $subtotalItemAkhir - $totalHppItem;

                    $subtotal += $subtotalPaket;
                    $totalHpp += $totalHppItem;

                    $processedDetails[] = [
                        'tipe_item' => 'Paket_Bundling',
                        'produk_id' => null,
                        'paket_koperasi_id' => $paket->id,
                        'kode_item' => $paket->kode_paket,
                        'nama_item' => $paket->nama_paket,
                        'satuan' => 'paket',
                        'harga_beli' => $totalHppPaket,
                        'harga_jual' => $hargaJualPaket,
                        'jumlah' => $qty,
                        'diskon_item' => $diskonItem,
                        'subtotal' => $subtotalItemAkhir,
                        'keuntungan' => $keuntunganItem,
                        'rincian_paket_json' => $rincianKomponen,
                    ];
                } else {
                    // Produk Individu
                    $produkId = (int) $item['produk_id'];
                    $prod = ProdukKoperasi::where('id', $produkId)->lockForUpdate()->firstOrFail();

                    if ($prod->status !== 'Aktif') {
                        throw new \Exception("Produk '{$prod->nama_produk}' sedang tidak aktif.");
                    }

                    if ($prod->stok < $qty) {
                        throw new \Exception("Stok produk '{$prod->nama_produk}' tidak mencukupi. Stok saat ini: {$prod->stok}, diminta: {$qty}.");
                    }

                    $stokSebelum = $prod->stok;
                    $stokSesudah = $stokSebelum - $qty;

                    // Kurangi stok produk
                    $prod->update(['stok' => $stokSesudah]);

                    // Catat mutasi stok
                    MutasiStokKoperasi::create([
                        'produk_id' => $prod->id,
                        'jenis_mutasi' => 'Penjualan',
                        'jumlah' => -$qty,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stokSesudah,
                        'referensi' => $nomorNota,
                        'keterangan' => "Penjualan Kasir POS ({$qty} {$prod->satuan})",
                        'petugas_id' => $petugasId,
                    ]);

                    $hargaBeli = (float) $prod->harga_beli;
                    $hargaJual = (float) ($item['harga_jual'] ?? $prod->harga_jual);
                    $subtotalItem = $hargaJual * $qty;
                    $diskonItem = (float) ($item['diskon_item'] ?? 0);
                    $subtotalItemAkhir = max(0, $subtotalItem - $diskonItem);
                    $totalHppItem = $hargaBeli * $qty;
                    $keuntunganItem = $subtotalItemAkhir - $totalHppItem;

                    $subtotal += $subtotalItem;
                    $totalHpp += $totalHppItem;

                    $processedDetails[] = [
                        'tipe_item' => 'Produk',
                        'produk_id' => $prod->id,
                        'paket_koperasi_id' => null,
                        'kode_item' => $prod->kode_produk,
                        'nama_item' => $prod->nama_produk,
                        'satuan' => $prod->satuan,
                        'harga_beli' => $hargaBeli,
                        'harga_jual' => $hargaJual,
                        'jumlah' => $qty,
                        'diskon_item' => $diskonItem,
                        'subtotal' => $subtotalItemAkhir,
                        'keuntungan' => $keuntunganItem,
                        'rincian_paket_json' => null,
                    ];
                }
            }

            $totalAkhir = max(0, $subtotal - $diskon);
            $metodeBayar = $data['metode_pembayaran'] ?? 'Tunai';
            $nominalBayar = (float) ($data['nominal_bayar'] ?? $totalAkhir);
            $kembalian = 0.00;
            $tabunganId = null;

            // 2. Pemrosesan Metode Pembayaran
            if ($metodeBayar === 'Potong_Tabungan') {
                // Cari rekening tabungan murid atau ustadz
                if ($jenisPelanggan === 'Murid') {
                    $tabungan = Tabungan::where('murid_id', $muridId)->where('status', 'Aktif')->first();
                } elseif ($jenisPelanggan === 'Ustadz') {
                    $tabungan = Tabungan::where('ustadz_id', $ustadzId)->where('status', 'Aktif')->first();
                } else {
                    $tabungan = null;
                }

                if (!$tabungan) {
                    throw new \Exception("Rekening tabungan aktif untuk nasabah {$jenisPelanggan} ini tidak ditemukan.");
                }

                if ((float) $tabungan->saldo < $totalAkhir) {
                    $saldoFormat = number_format($tabungan->saldo, 0, ',', '.');
                    $totalFormat = number_format($totalAkhir, 0, ',', '.');
                    throw new \Exception("Saldo tabungan tidak mencukupi. Saldo saat ini: Rp {$saldoFormat}, Total belanja: Rp {$totalFormat}.");
                }

                $tabunganId = $tabungan->id;
                $saldoAwal = (float) $tabungan->saldo;
                $saldoAkhir = $saldoAwal - $totalAkhir;

                // Catat transaksi penarikan di sistem tabungan madrasah
                $kodeTrxTab = 'TRX-KOP-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                TransaksiTabungan::create([
                    'kode_transaksi' => $kodeTrxTab,
                    'tabungan_id' => $tabungan->id,
                    'jenis_transaksi' => 'Tarik',
                    'nominal_kotor' => $totalAkhir,
                    'persentase_potongan' => 0.00,
                    'nominal_potongan' => 0.00,
                    'nominal_bersih' => $totalAkhir,
                    'saldo_awal' => $saldoAwal,
                    'saldo_akhir' => $saldoAkhir,
                    'tanggal' => date('Y-m-d'),
                    'ruangan_id' => $tabungan->ruangan_id,
                    'petugas_id' => $petugasId,
                    'metode' => 'Auto_Debet',
                    'keterangan' => "Belanja Koperasi Madrasah (Nota: {$nomorNota})",
                ]);

                // Update saldo tabungan
                $tabungan->update([
                    'saldo' => $saldoAkhir,
                    'total_tarik' => (float) $tabungan->total_tarik + $totalAkhir,
                ]);

                $nominalBayar = $totalAkhir;
                $kembalian = 0.00;
            } elseif ($metodeBayar === 'Tunai') {
                if ($nominalBayar < $totalAkhir) {
                    throw new \Exception("Nominal uang yang diterima (Rp " . number_format($nominalBayar, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($totalAkhir, 0, ',', '.') . ").");
                }
                $kembalian = max(0, $nominalBayar - $totalAkhir);
            } elseif ($metodeBayar === 'Hutang') {
                // Bayar Nanti / Hutang
                $nominalBayar = (float) ($data['nominal_bayar'] ?? 0.00);
                $kembalian = 0.00;
            } else {
                // QRIS / Transfer
                $nominalBayar = $totalAkhir;
                $kembalian = 0.00;
            }

            // 3. Simpan Header Penjualan
            $statusPembayaran = ($metodeBayar === 'Hutang') ? 'Belum_Lunas' : 'Lunas';
            $tanggalPelunasan = ($statusPembayaran === 'Lunas') ? $tglTransaksi : null;
            $metodePelunasan = ($statusPembayaran === 'Lunas') ? $metodeBayar : null;
            $petugasPelunasanId = ($statusPembayaran === 'Lunas') ? $petugasId : null;

            $penjualan = PenjualanKoperasi::create([
                'nomor_nota' => $nomorNota,
                'tanggal' => $tglTransaksi,
                'petugas_id' => $petugasId,
                'jenis_pelanggan' => $jenisPelanggan,
                'murid_id' => $muridId,
                'ustadz_id' => $ustadzId,
                'nama_pelanggan_umum' => $namaUmum,
                'total_item' => $totalItem,
                'total_hpp' => $totalHpp,
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'total_akhir' => $totalAkhir,
                'metode_pembayaran' => $metodeBayar,
                'nominal_bayar' => $nominalBayar,
                'kembalian' => $kembalian,
                'tabungan_id' => $tabunganId,
                'status' => 'Selesai',
                'status_pembayaran' => $statusPembayaran,
                'tanggal_pelunasan' => $tanggalPelunasan,
                'metode_pelunasan' => $metodePelunasan,
                'petugas_pelunasan_id' => $petugasPelunasanId,
                'catatan' => $data['catatan'] ?? null,
            ]);

            // 4. Simpan Detail Item
            foreach ($processedDetails as $detail) {
                $detail['penjualan_id'] = $penjualan->id;
                PenjualanDetailKoperasi::create($detail);
            }

            return $penjualan->load(['details.produk', 'details.paket', 'petugas', 'petugasPelunasan', 'murid', 'ustadz', 'tabungan']);
        });
    }

    /**
     * Memproses Pelunasan Transaksi Hutang / Piutang
     */
    public function lunasiHutang(int $penjualanId, array $data, int $petugasId): PenjualanKoperasi
    {
        return DB::transaction(function () use ($penjualanId, $data, $petugasId) {
            $penjualan = PenjualanKoperasi::where('id', $penjualanId)->lockForUpdate()->firstOrFail();

            if ($penjualan->status === 'Dibatalkan') {
                throw new \Exception("Transaksi nota '{$penjualan->nomor_nota}' telah dibatalkan, tidak dapat dilunasi.");
            }

            if ($penjualan->status_pembayaran === 'Lunas') {
                throw new \Exception("Transaksi nota '{$penjualan->nomor_nota}' sudah lunas sebelumnya.");
            }

            $metodePelunasan = $data['metode_pelunasan'] ?? 'Tunai';
            $nominalBayar = (float) ($data['nominal_bayar'] ?? $penjualan->total_akhir);
            $kembalian = 0.00;
            $tabunganId = $penjualan->tabungan_id;

            if ($metodePelunasan === 'Potong_Tabungan') {
                if ($penjualan->jenis_pelanggan === 'Murid') {
                    $tabungan = Tabungan::where('murid_id', $penjualan->murid_id)->where('status', 'Aktif')->first();
                } elseif ($penjualan->jenis_pelanggan === 'Ustadz') {
                    $tabungan = Tabungan::where('ustadz_id', $penjualan->ustadz_id)->where('status', 'Aktif')->first();
                } else {
                    $tabungan = null;
                }

                if (!$tabungan) {
                    throw new \Exception("Rekening tabungan aktif untuk nasabah ini tidak ditemukan.");
                }

                if ((float) $tabungan->saldo < $penjualan->total_akhir) {
                    $saldoFormat = number_format($tabungan->saldo, 0, ',', '.');
                    $totalFormat = number_format($penjualan->total_akhir, 0, ',', '.');
                    throw new \Exception("Saldo tabungan tidak mencukupi untuk pelunasan. Saldo: Rp {$saldoFormat}, Tagihan: Rp {$totalFormat}.");
                }

                $tabunganId = $tabungan->id;
                $saldoAwal = (float) $tabungan->saldo;
                $saldoAkhir = $saldoAwal - (float) $penjualan->total_akhir;

                // Catat transaksi penarikan di sistem tabungan
                $kodeTrxTab = 'TRX-KOP-LUNAS-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                TransaksiTabungan::create([
                    'kode_transaksi' => $kodeTrxTab,
                    'tabungan_id' => $tabungan->id,
                    'jenis_transaksi' => 'Tarik',
                    'nominal_kotor' => $penjualan->total_akhir,
                    'persentase_potongan' => 0.00,
                    'nominal_potongan' => 0.00,
                    'nominal_bersih' => $penjualan->total_akhir,
                    'saldo_awal' => $saldoAwal,
                    'saldo_akhir' => $saldoAkhir,
                    'tanggal' => date('Y-m-d'),
                    'ruangan_id' => $tabungan->ruangan_id,
                    'petugas_id' => $petugasId,
                    'metode' => 'Auto_Debet',
                    'keterangan' => "Pelunasan Hutang Koperasi (Nota: {$penjualan->nomor_nota})",
                ]);

                $tabungan->update([
                    'saldo' => $saldoAkhir,
                    'total_tarik' => (float) $tabungan->total_tarik + $penjualan->total_akhir,
                ]);

                $nominalBayar = $penjualan->total_akhir;
                $kembalian = 0.00;
            } elseif ($metodePelunasan === 'Tunai') {
                if ($nominalBayar < $penjualan->total_akhir) {
                    throw new \Exception("Nominal uang yang diterima (Rp " . number_format($nominalBayar, 0, ',', '.') . ") kurang dari tagihan hutang (Rp " . number_format($penjualan->total_akhir, 0, ',', '.') . ").");
                }
                $kembalian = max(0, $nominalBayar - $penjualan->total_akhir);
            } else {
                // QRIS / Transfer
                $nominalBayar = $penjualan->total_akhir;
                $kembalian = 0.00;
            }

            $penjualan->update([
                'status_pembayaran' => 'Lunas',
                'nominal_bayar' => $nominalBayar,
                'kembalian' => $kembalian,
                'tanggal_pelunasan' => now(),
                'metode_pelunasan' => $metodePelunasan,
                'petugas_pelunasan_id' => $petugasId,
                'tabungan_id' => $tabunganId,
                'catatan_pelunasan' => $data['catatan_pelunasan'] ?? null,
            ]);

            return $penjualan->fresh(['petugas', 'petugasPelunasan', 'murid', 'ustadz', 'tabungan', 'details.produk', 'details.paket']);
        });
    }

    /**
     * Tambah Stok Masuk / Restock Produk
     */
    public function tambahStokMasuk(int $produkId, int $jumlah, ?float $hargaBeliBaru, ?string $referensi, ?string $keterangan, int $petugasId): MutasiStokKoperasi
    {
        return DB::transaction(function () use ($produkId, $jumlah, $hargaBeliBaru, $referensi, $keterangan, $petugasId) {
            $prod = ProdukKoperasi::where('id', $produkId)->lockForUpdate()->firstOrFail();
            $stokSebelum = $prod->stok;
            $stokSesudah = $stokSebelum + $jumlah;

            $updateData = ['stok' => $stokSesudah];
            if ($hargaBeliBaru !== null && $hargaBeliBaru > 0) {
                $updateData['harga_beli'] = $hargaBeliBaru;
            }

            $prod->update($updateData);

            return MutasiStokKoperasi::create([
                'produk_id' => $prod->id,
                'jenis_mutasi' => 'Stok_Masuk',
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'referensi' => $referensi ?: 'RESTOCK-' . date('Ymd'),
                'keterangan' => $keterangan ?: "Penambahan stok masuk sebanyak {$jumlah} {$prod->satuan}",
                'petugas_id' => $petugasId,
            ]);
        });
    }

    /**
     * Penyesuaian Stok Fisik (Stock Opname)
     */
    public function penyesuaianStockOpname(int $produkId, int $stokFisik, string $alasan, int $petugasId): MutasiStokKoperasi
    {
        return DB::transaction(function () use ($produkId, $stokFisik, $alasan, $petugasId) {
            $prod = ProdukKoperasi::where('id', $produkId)->lockForUpdate()->firstOrFail();
            $stokSebelum = $prod->stok;
            $selisih = $stokFisik - $stokSebelum;

            $prod->update(['stok' => $stokFisik]);

            return MutasiStokKoperasi::create([
                'produk_id' => $prod->id,
                'jenis_mutasi' => 'Penyesuaian_Opname',
                'jumlah' => $selisih,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokFisik,
                'referensi' => 'OPNAME-' . date('Ymd'),
                'keterangan' => "Stock Opname: {$alasan} (Selisih: " . ($selisih >= 0 ? "+{$selisih}" : "{$selisih}") . " {$prod->satuan})",
                'petugas_id' => $petugasId,
            ]);
        });
    }

    /**
     * Membatalkan / Void Transaksi Penjualan dan Mengembalikan Stok
     */
    public function batalTransaksi(int $penjualanId, string $alasan, int $petugasId): PenjualanKoperasi
    {
        return DB::transaction(function () use ($penjualanId, $alasan, $petugasId) {
            $penjualan = PenjualanKoperasi::with('details')->where('id', $penjualanId)->lockForUpdate()->firstOrFail();

            if ($penjualan->status === 'Dibatalkan') {
                throw new \Exception("Transaksi '{$penjualan->nomor_nota}' sudah dibatalkan sebelumnya.");
            }

            // 1. Kembalikan stok item
            foreach ($penjualan->details as $detail) {
                if ($detail->tipe_item === 'Paket_Bundling' && !empty($detail->rincian_paket_json)) {
                    foreach ($detail->rincian_paket_json as $komp) {
                        $prod = ProdukKoperasi::where('id', $komp['produk_id'])->lockForUpdate()->first();
                        if ($prod) {
                            $stokSebelum = $prod->stok;
                            $qtyKembali = (int) ($komp['total_qty_keluar'] ?? 0);
                            $stokSesudah = $stokSebelum + $qtyKembali;

                            $prod->update(['stok' => $stokSesudah]);

                            MutasiStokKoperasi::create([
                                'produk_id' => $prod->id,
                                'jenis_mutasi' => 'Pembatalan_Transaksi',
                                'jumlah' => $qtyKembali,
                                'stok_sebelum' => $stokSebelum,
                                'stok_sesudah' => $stokSesudah,
                                'referensi' => $penjualan->nomor_nota,
                                'keterangan' => "Batal Transaksi Nota {$penjualan->nomor_nota}: {$alasan}",
                                'petugas_id' => $petugasId,
                            ]);
                        }
                    }
                } elseif ($detail->produk_id) {
                    $prod = ProdukKoperasi::where('id', $detail->produk_id)->lockForUpdate()->first();
                    if ($prod) {
                        $stokSebelum = $prod->stok;
                        $qtyKembali = $detail->jumlah;
                        $stokSesudah = $stokSebelum + $qtyKembali;

                        $prod->update(['stok' => $stokSesudah]);

                        MutasiStokKoperasi::create([
                            'produk_id' => $prod->id,
                            'jenis_mutasi' => 'Pembatalan_Transaksi',
                            'jumlah' => $qtyKembali,
                            'stok_sebelum' => $stokSebelum,
                            'stok_sesudah' => $stokSesudah,
                            'referensi' => $penjualan->nomor_nota,
                            'keterangan' => "Batal Transaksi Nota {$penjualan->nomor_nota}: {$alasan}",
                            'petugas_id' => $petugasId,
                        ]);
                    }
                }
            }

            // 2. Refund Saldo jika metode bayar Potong_Tabungan
            if ($penjualan->metode_pembayaran === 'Potong_Tabungan' && $penjualan->tabungan_id) {
                $tabungan = Tabungan::where('id', $penjualan->tabungan_id)->lockForUpdate()->first();
                if ($tabungan) {
                    $saldoAwal = (float) $tabungan->saldo;
                    $nominalRefund = (float) $penjualan->total_akhir;
                    $saldoAkhir = $saldoAwal + $nominalRefund;

                    $kodeTrxTab = 'TRX-REFUND-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                    TransaksiTabungan::create([
                        'kode_transaksi' => $kodeTrxTab,
                        'tabungan_id' => $tabungan->id,
                        'jenis_transaksi' => 'Setor',
                        'nominal_kotor' => $nominalRefund,
                        'persentase_potongan' => 0.00,
                        'nominal_potongan' => 0.00,
                        'nominal_bersih' => $nominalRefund,
                        'saldo_awal' => $saldoAwal,
                        'saldo_akhir' => $saldoAkhir,
                        'tanggal' => date('Y-m-d'),
                        'ruangan_id' => $tabungan->ruangan_id,
                        'petugas_id' => $petugasId,
                        'metode' => 'Auto_Debet',
                        'keterangan' => "Pengembalian Dana Pembatalan Nota {$penjualan->nomor_nota}",
                    ]);

                    $tabungan->update([
                        'saldo' => $saldoAkhir,
                        'total_tarik' => max(0, (float) $tabungan->total_tarik - $nominalRefund),
                    ]);
                }
            }

            // 3. Update Status Penjualan
            $penjualan->update([
                'status' => 'Dibatalkan',
                'catatan' => ($penjualan->catatan ? $penjualan->catatan . " | " : "") . "Dibatalkan oleh Petugas: {$alasan}",
            ]);

            return $penjualan;
        });
    }

    /**
     * Ringkasan Dashboard Koperasi
     */
    public function getRingkasanDashboard(): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        // Hari Ini
        $penjualanHariIni = PenjualanKoperasi::whereDate('tanggal', $today)->where('status', 'Selesai');
        $omzetHariIni = (float) (clone $penjualanHariIni)->sum('total_akhir');
        $hppHariIni = (float) (clone $penjualanHariIni)->sum('total_hpp');
        $labaHariIni = max(0, $omzetHariIni - $hppHariIni);
        $trxHariIni = (clone $penjualanHariIni)->count();

        // Bulan Ini
        $penjualanBulanIni = PenjualanKoperasi::where('tanggal', 'like', "{$thisMonth}%")->where('status', 'Selesai');
        $omzetBulanIni = (float) (clone $penjualanBulanIni)->sum('total_akhir');
        $hppBulanIni = (float) (clone $penjualanBulanIni)->sum('total_hpp');
        $labaBulanIni = max(0, $omzetBulanIni - $hppBulanIni);
        $trxBulanIni = (clone $penjualanBulanIni)->count();

        // Master Data Stats
        $totalProduk = ProdukKoperasi::count();
        $totalProdukAktif = ProdukKoperasi::where('status', 'Aktif')->count();
        $totalPaket = PaketKoperasi::where('is_active', true)->count();
        $stokMenipisCount = ProdukKoperasi::whereRaw('stok <= stok_minimum')->count();

        return compact(
            'omzetHariIni',
            'labaHariIni',
            'trxHariIni',
            'omzetBulanIni',
            'labaBulanIni',
            'trxBulanIni',
            'totalProduk',
            'totalProdukAktif',
            'totalPaket',
            'stokMenipisCount'
        );
    }

    /**
     * Memproses Transaksi Pembelian / Kulakan Barang dari Supplier
     */
    public function simpanPembelian(array $data, int $petugasId, $fotoFaktur = null): PembelianKoperasi
    {
        return DB::transaction(function () use ($data, $petugasId, $fotoFaktur) {
            $items = $data['items'] ?? [];
            if (empty($items)) {
                throw new \InvalidArgumentException('Daftar barang kulakan masih kosong.');
            }

            $nomorFaktur = PembelianKoperasi::generateNomorFaktur();
            $nomorFakturSupplier = !empty($data['nomor_faktur_supplier']) ? trim($data['nomor_faktur_supplier']) : null;
            $supplier = trim($data['supplier'] ?? 'Supplier Umum');
            $tglTransaksi = $data['tanggal'] ?? now();

            $totalItem = 0;
            $totalNominal = 0.00;
            $processedDetails = [];

            // 1. Process each item: update stock, update buy price & sell price
            $updateHargaProduk = isset($data['update_harga_produk']) ? (bool) $data['update_harga_produk'] : true;

            foreach ($items as $item) {
                $produkId = (int) ($item['produk_id'] ?? 0);
                $qty = (int) ($item['jumlah'] ?? 1);
                if ($qty <= 0) continue;

                $hargaBeli = (float) ($item['harga_beli'] ?? $item['harga_beli_satuan'] ?? 0);
                $hargaJual = (float) ($item['harga_jual'] ?? $item['harga_jual_satuan'] ?? 0);
                $subtotal = $hargaBeli * $qty;

                $prod = ProdukKoperasi::where('id', $produkId)->lockForUpdate()->firstOrFail();

                $stokSebelum = $prod->stok;
                $stokSesudah = $stokSebelum + $qty;

                // Update product stock and pricing
                $updateData = ['stok' => $stokSesudah];
                if ($updateHargaProduk) {
                    if ($hargaBeli > 0) {
                        $updateData['harga_beli'] = $hargaBeli;
                    }
                    if ($hargaJual > 0) {
                        $updateData['harga_jual'] = $hargaJual;
                    }
                }
                $prod->update($updateData);

                // Record stock mutation
                MutasiStokKoperasi::create([
                    'produk_id' => $prod->id,
                    'jenis_mutasi' => 'Stok_Masuk',
                    'jumlah' => $qty,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'referensi' => $nomorFaktur,
                    'keterangan' => "Kulakan dari Supplier: {$supplier}" . ($nomorFakturSupplier ? " (Faktur: {$nomorFakturSupplier})" : ""),
                    'petugas_id' => $petugasId,
                ]);

                $totalItem += $qty;
                $totalNominal += $subtotal;

                $processedDetails[] = [
                    'produk_id' => $prod->id,
                    'kode_produk' => $prod->kode_produk,
                    'nama_produk' => $prod->nama_produk,
                    'satuan' => $prod->satuan,
                    'jumlah' => $qty,
                    'harga_beli_satuan' => $hargaBeli,
                    'harga_jual_satuan' => $hargaJual > 0 ? $hargaJual : (float) $prod->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            $ongkir = (float) ($data['ongkir'] ?? 0.00);
            $diskon = (float) ($data['diskon'] ?? 0.00);
            $grandTotal = max(0, $totalNominal + $ongkir - $diskon);

            // 2. Handle Payment Method & Debt Calculation
            $metodeBayar = $data['metode_pembayaran'] ?? 'Tunai_Kas'; // Tunai_Kas, Transfer_Bank, Hutang_Tempo
            $nominalBayar = (float) ($data['nominal_bayar'] ?? 0.00);
            $kembalian = 0.00;
            $sisaHutang = 0.00;
            $statusPembayaran = 'Lunas';
            $tanggalPelunasan = null;
            $metodePelunasan = null;
            $petugasPelunasanId = null;
            $tanggalJatuhTempo = !empty($data['tanggal_jatuh_tempo']) ? $data['tanggal_jatuh_tempo'] : null;

            if (in_array($metodeBayar, ['Hutang_Tempo', 'Hutang_Supplier', 'Hutang'])) {
                $statusPembayaran = 'Belum_Lunas';
                $sisaHutang = max(0, $grandTotal - $nominalBayar);
                if ($sisaHutang <= 0) {
                    $statusPembayaran = 'Lunas';
                    $tanggalPelunasan = $tglTransaksi;
                    $metodePelunasan = $metodeBayar;
                    $petugasPelunasanId = $petugasId;
                }
            } else {
                // Tunai_Kas atau Transfer_Bank
                if ($nominalBayar <= 0) {
                    $nominalBayar = $grandTotal;
                }
                $kembalian = max(0, $nominalBayar - $grandTotal);
                $sisaHutang = max(0, $grandTotal - $nominalBayar);
                if ($sisaHutang > 0) {
                    $statusPembayaran = 'Belum_Lunas';
                } else {
                    $statusPembayaran = 'Lunas';
                    $tanggalPelunasan = $tglTransaksi;
                    $metodePelunasan = $metodeBayar;
                    $petugasPelunasanId = $petugasId;
                }
            }

            // 3. Handle Foto Faktur Upload
            $fotoPath = null;
            if ($fotoFaktur) {
                $fotoPath = $fotoFaktur->store('koperasi/faktur_kulakan', 'public');
            }

            // 4. Create Pembelian Header
            $pembelian = PembelianKoperasi::create([
                'nomor_faktur' => $nomorFaktur,
                'nomor_faktur_supplier' => $nomorFakturSupplier,
                'supplier' => $supplier,
                'tanggal' => $tglTransaksi,
                'petugas_id' => $petugasId,
                'total_item' => $totalItem,
                'total_nominal' => $totalNominal,
                'ongkir' => $ongkir,
                'diskon' => $diskon,
                'metode_pembayaran' => $metodeBayar,
                'nominal_bayar' => $nominalBayar,
                'kembalian' => $kembalian,
                'sisa_hutang' => $sisaHutang,
                'status_pembayaran' => $statusPembayaran,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'tanggal_pelunasan' => $tanggalPelunasan,
                'metode_pelunasan' => $metodePelunasan,
                'petugas_pelunasan_id' => $petugasPelunasanId,
                'status' => 'Selesai',
                'foto_faktur' => $fotoPath,
                'catatan' => $data['catatan'] ?? null,
            ]);

            // 5. Create Detail Items
            foreach ($processedDetails as $detail) {
                $detail['pembelian_id'] = $pembelian->id;
                PembelianDetailKoperasi::create($detail);
            }

            return $pembelian->load(['details.produk', 'petugas', 'petugasPelunasan']);
        });
    }

    /**
     * Memproses Pelunasan Hutang ke Supplier
     */
    public function lunasiHutangSupplier(int $pembelianId, array $data, int $petugasId): PembelianKoperasi
    {
        return DB::transaction(function () use ($pembelianId, $data, $petugasId) {
            $pembelian = PembelianKoperasi::where('id', $pembelianId)->lockForUpdate()->firstOrFail();

            if ($pembelian->status === 'Dibatalkan') {
                throw new \Exception("Faktur pembelian '{$pembelian->nomor_faktur}' telah dibatalkan, tidak dapat dilunasi.");
            }

            if ($pembelian->status_pembayaran === 'Lunas') {
                throw new \Exception("Faktur pembelian '{$pembelian->nomor_faktur}' sudah lunas sebelumnya.");
            }

            $metodePelunasan = $data['metode_pelunasan'] ?? 'Tunai_Kas';
            $nominalBayar = (float) ($data['nominal_bayar'] ?? $pembelian->sisa_hutang);
            $kembalian = max(0, $nominalBayar - $pembelian->sisa_hutang);

            $pembelian->update([
                'status_pembayaran' => 'Lunas',
                'nominal_bayar' => (float) $pembelian->nominal_bayar + $nominalBayar,
                'kembalian' => $kembalian,
                'sisa_hutang' => 0.00,
                'tanggal_pelunasan' => now(),
                'metode_pelunasan' => $metodePelunasan,
                'petugas_pelunasan_id' => $petugasId,
                'catatan' => ($pembelian->catatan ? $pembelian->catatan . " | " : "") . ($data['catatan_pelunasan'] ?? 'Dilunasi ke Supplier'),
            ]);

            return $pembelian->fresh(['petugas', 'petugasPelunasan', 'details.produk']);
        });
    }

    /**
     * Membatalkan / Void Transaksi Pembelian dan Mengurangi Stok Kembali
     */
    public function batalPembelian(int $pembelianId, string $alasan, int $petugasId): PembelianKoperasi
    {
        return DB::transaction(function () use ($pembelianId, $alasan, $petugasId) {
            $pembelian = PembelianKoperasi::with('details')->where('id', $pembelianId)->lockForUpdate()->firstOrFail();

            if ($pembelian->status === 'Dibatalkan') {
                throw new \Exception("Transaksi pembelian '{$pembelian->nomor_faktur}' sudah dibatalkan sebelumnya.");
            }

            // Kurangi kembali stok yang sebelumnya masuk
            foreach ($pembelian->details as $detail) {
                $prod = ProdukKoperasi::where('id', $detail->produk_id)->lockForUpdate()->first();
                if ($prod) {
                    $stokSebelum = $prod->stok;
                    $stokSesudah = max(0, $stokSebelum - $detail->jumlah);

                    $prod->update(['stok' => $stokSesudah]);

                    MutasiStokKoperasi::create([
                        'produk_id' => $prod->id,
                        'jenis_mutasi' => 'Pembatalan_Transaksi',
                        'jumlah' => -$detail->jumlah,
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stokSesudah,
                        'referensi' => $pembelian->nomor_faktur,
                        'keterangan' => "Batal Kulakan Faktur {$pembelian->nomor_faktur}: {$alasan}",
                        'petugas_id' => $petugasId,
                    ]);
                }
            }

            $pembelian->update([
                'status' => 'Dibatalkan',
                'catatan' => ($pembelian->catatan ? $pembelian->catatan . " | " : "") . "Dibatalkan: {$alasan}",
            ]);

            return $pembelian;
        });
    }
}
