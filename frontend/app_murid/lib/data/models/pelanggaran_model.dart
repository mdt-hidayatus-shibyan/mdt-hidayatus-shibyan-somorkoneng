class PelanggaranItemModel {
  final int id;
  final String tanggal;
  final String kasus;
  final String kategori;
  final int poin;
  final String keterangan;

  PelanggaranItemModel({
    required this.id,
    required this.tanggal,
    required this.kasus,
    required this.kategori,
    required this.poin,
    required this.keterangan,
  });

  factory PelanggaranItemModel.fromJson(Map<String, dynamic> json) {
    return PelanggaranItemModel(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      tanggal: json['tanggal']?.toString() ?? '-',
      kasus: json['kasus']?.toString() ?? 'Pelanggaran',
      kategori: json['kategori']?.toString() ?? 'Ringan',
      poin: json['poin'] is int
          ? json['poin']
          : int.tryParse('${json['poin']}') ?? 0,
      keterangan: json['keterangan']?.toString() ?? '-',
    );
  }
}

class RekapPelanggaranAnakModel {
  final int totalPoin;
  final int totalKasus;
  final List<PelanggaranItemModel> riwayat;

  RekapPelanggaranAnakModel({
    required this.totalPoin,
    required this.totalKasus,
    required this.riwayat,
  });

  factory RekapPelanggaranAnakModel.fromJson(Map<String, dynamic> json) {
    final rawRiwayat = json['riwayat'] as List<dynamic>? ?? [];
    return RekapPelanggaranAnakModel(
      totalPoin: json['total_poin'] is int
          ? json['total_poin']
          : int.tryParse('${json['total_poin']}') ?? 0,
      totalKasus: json['total_kasus'] is int
          ? json['total_kasus']
          : int.tryParse('${json['total_kasus']}') ?? 0,
      riwayat: rawRiwayat
          .map((e) => PelanggaranItemModel.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}
