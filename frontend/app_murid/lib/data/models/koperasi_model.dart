class KoperasiSummary {
  final int totalBelanja;
  final int totalTransaksi;
  final int totalItem;

  KoperasiSummary({
    required this.totalBelanja,
    required this.totalTransaksi,
    required this.totalItem,
  });

  factory KoperasiSummary.fromJson(Map<String, dynamic> json) {
    return KoperasiSummary(
      totalBelanja: (json['total_belanja'] as num?)?.toInt() ?? 0,
      totalTransaksi: (json['total_transaksi'] as num?)?.toInt() ?? 0,
      totalItem: (json['total_item'] as num?)?.toInt() ?? 0,
    );
  }
}

class ItemBelanjaKoperasi {
  final int id;
  final String tipeItem;
  final String kodeItem;
  final String namaItem;
  final String satuan;
  final int hargaSatuan;
  final int jumlah;
  final int diskonItem;
  final int subtotal;

  ItemBelanjaKoperasi({
    required this.id,
    required this.tipeItem,
    required this.kodeItem,
    required this.namaItem,
    required this.satuan,
    required this.hargaSatuan,
    required this.jumlah,
    required this.diskonItem,
    required this.subtotal,
  });

  factory ItemBelanjaKoperasi.fromJson(Map<String, dynamic> json) {
    return ItemBelanjaKoperasi(
      id: json['id'] ?? 0,
      tipeItem: json['tipe_item'] ?? 'Produk',
      kodeItem: json['kode_item'] ?? '',
      namaItem: json['nama_item'] ?? '',
      satuan: json['satuan'] ?? 'pcs',
      hargaSatuan: (json['harga_satuan'] as num?)?.toInt() ?? 0,
      jumlah: (json['jumlah'] as num?)?.toInt() ?? 1,
      diskonItem: (json['diskon_item'] as num?)?.toInt() ?? 0,
      subtotal: (json['subtotal'] as num?)?.toInt() ?? 0,
    );
  }
}

class TransaksiKoperasiItem {
  final int id;
  final String nomorNota;
  final String tanggal;
  final String tanggalFormatted;
  final int totalItem;
  final int subtotal;
  final int diskon;
  final int totalAkhir;
  final String metodePembayaran;
  final int nominalBayar;
  final int kembalian;
  final String status;
  final String catatan;
  final String kasir;
  final List<ItemBelanjaKoperasi> items;

  TransaksiKoperasiItem({
    required this.id,
    required this.nomorNota,
    required this.tanggal,
    required this.tanggalFormatted,
    required this.totalItem,
    required this.subtotal,
    required this.diskon,
    required this.totalAkhir,
    required this.metodePembayaran,
    required this.nominalBayar,
    required this.kembalian,
    required this.status,
    required this.catatan,
    required this.kasir,
    required this.items,
  });

  factory TransaksiKoperasiItem.fromJson(Map<String, dynamic> json) {
    return TransaksiKoperasiItem(
      id: json['id'] ?? 0,
      nomorNota: json['nomor_nota'] ?? '',
      tanggal: json['tanggal'] ?? '',
      tanggalFormatted: json['tanggal_formatted'] ?? '',
      totalItem: (json['total_item'] as num?)?.toInt() ?? 0,
      subtotal: (json['subtotal'] as num?)?.toInt() ?? 0,
      diskon: (json['diskon'] as num?)?.toInt() ?? 0,
      totalAkhir: (json['total_akhir'] as num?)?.toInt() ?? 0,
      metodePembayaran: json['metode_pembayaran'] ?? 'Tunai',
      nominalBayar: (json['nominal_bayar'] as num?)?.toInt() ?? 0,
      kembalian: (json['kembalian'] as num?)?.toInt() ?? 0,
      status: json['status'] ?? 'Selesai',
      catatan: json['catatan'] ?? '-',
      kasir: json['kasir'] ?? 'Kasir Koperasi',
      items:
          (json['items'] as List?)
              ?.map((e) => ItemBelanjaKoperasi.fromJson(e))
              .toList() ??
          [],
    );
  }

  bool get isPotongTabungan =>
      metodePembayaran.toLowerCase() == 'potong_tabungan' ||
      metodePembayaran.toLowerCase() == 'tabungan';
}

class KoperasiAnakData {
  final KoperasiSummary summary;
  final List<TransaksiKoperasiItem> riwayat;

  KoperasiAnakData({required this.summary, required this.riwayat});

  factory KoperasiAnakData.fromJson(Map<String, dynamic> json) {
    return KoperasiAnakData(
      summary: KoperasiSummary.fromJson(json['summary'] ?? {}),
      riwayat:
          (json['riwayat'] as List?)
              ?.map((e) => TransaksiKoperasiItem.fromJson(e))
              .toList() ??
          [],
    );
  }
}
