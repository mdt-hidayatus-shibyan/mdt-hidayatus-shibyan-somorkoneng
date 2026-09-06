class JadwalItemModel {
  final int id;
  final String hari;
  final String? jamKe;
  final String waktu;
  final String mapel;
  final String ustadz;

  JadwalItemModel({
    required this.id,
    required this.hari,
    this.jamKe,
    required this.waktu,
    required this.mapel,
    required this.ustadz,
  });

  factory JadwalItemModel.fromJson(Map<String, dynamic> json) {
    return JadwalItemModel(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      hari: json['hari']?.toString() ?? 'Sabtu',
      jamKe: json['jam_ke']?.toString(),
      waktu: json['waktu']?.toString() ?? '14:00 - 14:45 WIB',
      mapel: json['mapel']?.toString() ?? 'Mata Pelajaran',
      ustadz: json['ustadz']?.toString() ?? 'Ustadz Pengampu',
    );
  }
}
