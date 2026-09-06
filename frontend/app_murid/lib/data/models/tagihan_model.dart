class ItemSppModel {
  final int id;
  final String bulan;
  final int nominal;
  final String statusBayar;
  final String? tanggalBayar;
  final String? noTransaksi;

  ItemSppModel({
    required this.id,
    required this.bulan,
    required this.nominal,
    required this.statusBayar,
    this.tanggalBayar,
    this.noTransaksi,
  });

  bool get isLunas => statusBayar == 'Lunas';
  bool get isBebas => statusBayar == 'Bebas';

  factory ItemSppModel.fromJson(Map<String, dynamic> json) {
    return ItemSppModel(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      bulan: json['bulan']?.toString() ?? '-',
      nominal: json['nominal'] is int
          ? json['nominal']
          : int.tryParse('${json['nominal']}') ?? 0,
      statusBayar: json['status_bayar']?.toString() ?? 'Belum Lunas',
      tanggalBayar: json['tanggal_bayar']?.toString(),
      noTransaksi: json['no_transaksi']?.toString(),
    );
  }
}

class ItemNonSppModel {
  final int id;
  final String namaTagihan;
  final String tipe;
  final int nominal;
  final String statusBayar;
  final String? tanggalBayar;
  final String? noTransaksi;

  ItemNonSppModel({
    required this.id,
    required this.namaTagihan,
    required this.tipe,
    required this.nominal,
    required this.statusBayar,
    this.tanggalBayar,
    this.noTransaksi,
  });

  bool get isLunas => statusBayar == 'Lunas';

  factory ItemNonSppModel.fromJson(Map<String, dynamic> json) {
    return ItemNonSppModel(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      namaTagihan: json['nama_tagihan']?.toString() ?? 'Tagihan',
      tipe: json['tipe']?.toString() ?? 'insidental',
      nominal: json['nominal'] is int
          ? json['nominal']
          : int.tryParse('${json['nominal']}') ?? 0,
      statusBayar: json['status_bayar']?.toString() ?? 'Belum Lunas',
      tanggalBayar: json['tanggal_bayar']?.toString(),
      noTransaksi: json['no_transaksi']?.toString(),
    );
  }
}

class RekapTagihanAnakModel {
  final int totalTagihan;
  final int totalLunas;
  final int totalTunggakan;
  final List<ItemSppModel> sppList;
  final List<ItemNonSppModel> nonSppList;

  RekapTagihanAnakModel({
    required this.totalTagihan,
    required this.totalLunas,
    required this.totalTunggakan,
    required this.sppList,
    required this.nonSppList,
  });

  factory RekapTagihanAnakModel.fromJson(Map<String, dynamic> json) {
    final summary = json['summary'] as Map<String, dynamic>? ?? {};
    final rawSpp = json['spp'] as List<dynamic>? ?? [];
    final rawNonSpp = json['non_spp'] as List<dynamic>? ?? [];

    return RekapTagihanAnakModel(
      totalTagihan: summary['total_tagihan'] is int
          ? summary['total_tagihan']
          : int.tryParse('${summary['total_tagihan']}') ?? 0,
      totalLunas: summary['total_lunas'] is int
          ? summary['total_lunas']
          : int.tryParse('${summary['total_lunas']}') ?? 0,
      totalTunggakan: summary['total_tunggakan'] is int
          ? summary['total_tunggakan']
          : int.tryParse('${summary['total_tunggakan']}') ?? 0,
      sppList: rawSpp
          .map((e) => ItemSppModel.fromJson(e as Map<String, dynamic>))
          .toList(),
      nonSppList: rawNonSpp
          .map((e) => ItemNonSppModel.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}
