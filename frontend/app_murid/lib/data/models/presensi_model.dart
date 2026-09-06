class RiwayatPresensiItem {
  final int id;
  final String tanggal;
  final String hari;
  final String mapel;
  final String status;

  RiwayatPresensiItem({
    required this.id,
    required this.tanggal,
    required this.hari,
    required this.mapel,
    required this.status,
  });

  factory RiwayatPresensiItem.fromJson(Map<String, dynamic> json) {
    return RiwayatPresensiItem(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      tanggal: json['tanggal']?.toString() ?? '-',
      hari: json['hari']?.toString() ?? '-',
      mapel: json['mapel']?.toString() ?? 'Pelajaran',
      status: json['status']?.toString() ?? 'Hadir',
    );
  }
}

class RekapPresensiAnakModel {
  final int totalSesi;
  final double persentaseHadir;
  final int hadir;
  final int sakit;
  final int izin;
  final int alpha;
  final int dispensasi;
  final List<RiwayatPresensiItem> riwayat;

  RekapPresensiAnakModel({
    required this.totalSesi,
    required this.persentaseHadir,
    required this.hadir,
    required this.sakit,
    required this.izin,
    required this.alpha,
    required this.dispensasi,
    required this.riwayat,
  });

  factory RekapPresensiAnakModel.fromJson(Map<String, dynamic> json) {
    final stats = json['statistik'] as Map<String, dynamic>? ?? {};
    final rincian = stats['rincian'] as Map<String, dynamic>? ?? {};
    final rawRiwayat = json['riwayat'] as List<dynamic>? ?? [];

    return RekapPresensiAnakModel(
      totalSesi: stats['total_sesi'] is int
          ? stats['total_sesi']
          : int.tryParse('${stats['total_sesi']}') ?? 0,
      persentaseHadir: stats['persentase_hadir'] != null
          ? double.tryParse('${stats['persentase_hadir']}') ?? 0.0
          : 0.0,
      hadir: rincian['hadir'] is int
          ? rincian['hadir']
          : int.tryParse('${rincian['hadir']}') ?? 0,
      sakit: rincian['sakit'] is int
          ? rincian['sakit']
          : int.tryParse('${rincian['sakit']}') ?? 0,
      izin: rincian['izin'] is int
          ? rincian['izin']
          : int.tryParse('${rincian['izin']}') ?? 0,
      alpha: rincian['alpha'] is int
          ? rincian['alpha']
          : int.tryParse('${rincian['alpha']}') ?? 0,
      dispensasi: rincian['dispensasi'] is int
          ? rincian['dispensasi']
          : int.tryParse('${rincian['dispensasi']}') ?? 0,
      riwayat: rawRiwayat
          .map((e) => RiwayatPresensiItem.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}
