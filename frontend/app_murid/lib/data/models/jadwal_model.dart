class JadwalItemModel {
  final int id;
  final String hari;
  final String? jamKe;
  final String waktu;
  final String mapel;
  final String ustadz;
  final bool isUjian;

  JadwalItemModel({
    required this.id,
    required this.hari,
    this.jamKe,
    required this.waktu,
    required this.mapel,
    required this.ustadz,
    this.isUjian = false,
  });

  factory JadwalItemModel.fromJson(Map<String, dynamic> json) {
    return JadwalItemModel(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      hari: json['hari']?.toString() ?? 'Sabtu',
      jamKe: json['jam_ke']?.toString(),
      waktu: json['waktu']?.toString() ?? '14:00 - 14:45 WIB',
      mapel: json['mapel']?.toString() ?? 'Mata Pelajaran',
      ustadz: json['ustadz']?.toString() ?? 'Ustadz Pengampu',
      isUjian: json['is_ujian'] == true,
    );
  }
}

class JadwalDetailAnakModel {
  final String ruangan;
  final String hariIni;
  final String tanggalHariIni;
  final String tanggalFormatted;
  final bool isLibur;
  final String? keteranganLibur;
  final bool isUjian;
  final String? namaUjian;
  final List<JadwalItemModel> jadwalHariIni;
  final List<JadwalItemModel> jadwalMingguan;

  JadwalDetailAnakModel({
    required this.ruangan,
    required this.hariIni,
    required this.tanggalHariIni,
    required this.tanggalFormatted,
    required this.isLibur,
    this.keteranganLibur,
    required this.isUjian,
    this.namaUjian,
    required this.jadwalHariIni,
    required this.jadwalMingguan,
  });

  factory JadwalDetailAnakModel.fromJson(Map<String, dynamic> json) {
    final listHariIni = json['jadwal_hari_ini'] as List<dynamic>? ?? [];
    final listMingguan =
        (json['jadwal_mingguan'] ?? json['jadwal']) as List<dynamic>? ?? [];

    return JadwalDetailAnakModel(
      ruangan: json['ruangan']?.toString() ?? '-',
      hariIni: json['hari_ini']?.toString() ?? 'Hari Ini',
      tanggalHariIni: json['tanggal_hari_ini']?.toString() ?? '',
      tanggalFormatted: json['tanggal_formatted']?.toString() ?? '',
      isLibur: json['is_libur'] == true,
      keteranganLibur: json['keterangan_libur']?.toString(),
      isUjian: json['is_ujian'] == true,
      namaUjian: json['nama_ujian']?.toString(),
      jadwalHariIni: listHariIni
          .map((e) => JadwalItemModel.fromJson(e))
          .toList(),
      jadwalMingguan: listMingguan
          .map((e) => JadwalItemModel.fromJson(e))
          .toList(),
    );
  }
}
