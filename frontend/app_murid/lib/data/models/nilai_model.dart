class NilaiMapelItem {
  final int id;
  final String mapel;
  final int kkm;
  final double nilaiAngka;
  final String nilaiHuruf;
  final bool isLulus;
  final String catatan;

  NilaiMapelItem({
    required this.id,
    required this.mapel,
    required this.kkm,
    required this.nilaiAngka,
    required this.nilaiHuruf,
    required this.isLulus,
    required this.catatan,
  });

  factory NilaiMapelItem.fromJson(Map<String, dynamic> json) {
    return NilaiMapelItem(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      mapel: json['mapel']?.toString() ?? 'Mata Pelajaran',
      kkm: json['kkm'] is int
          ? json['kkm']
          : int.tryParse('${json['kkm']}') ?? 65,
      nilaiAngka: json['nilai_angka'] != null
          ? double.tryParse('${json['nilai_angka']}') ?? 0.0
          : 0.0,
      nilaiHuruf: json['nilai_huruf']?.toString() ?? 'C',
      isLulus: json['is_lulus'] == true || json['is_lulus'] == 1,
      catatan: json['catatan']?.toString() ?? '-',
    );
  }
}

class UjianRaporItem {
  final int? ujianId;
  final String namaUjian;
  final String tipeUjian;
  final String semester;
  final String ruangan;
  final int totalMapel;
  final double totalNilai;
  final double rataRata;
  final List<NilaiMapelItem> daftarNilai;

  UjianRaporItem({
    this.ujianId,
    required this.namaUjian,
    required this.tipeUjian,
    required this.semester,
    required this.ruangan,
    required this.totalMapel,
    required this.totalNilai,
    required this.rataRata,
    required this.daftarNilai,
  });

  factory UjianRaporItem.fromJson(Map<String, dynamic> json) {
    final rawNilai = json['daftar_nilai'] as List<dynamic>? ?? [];

    return UjianRaporItem(
      ujianId: json['ujian_id'] is int
          ? json['ujian_id']
          : int.tryParse('${json['ujian_id']}'),
      namaUjian: json['nama_ujian']?.toString() ?? 'Ujian Madrasah',
      tipeUjian: json['tipe_ujian']?.toString() ?? 'IMDA',
      semester: json['semester']?.toString() ?? 'Semester Aktif',
      ruangan: json['ruangan']?.toString() ?? '-',
      totalMapel: json['total_mapel'] is int
          ? json['total_mapel']
          : int.tryParse('${json['total_mapel']}') ?? 0,
      totalNilai: json['total_nilai'] != null
          ? double.tryParse('${json['total_nilai']}') ?? 0.0
          : 0.0,
      rataRata: json['rata_rata'] != null
          ? double.tryParse('${json['rata_rata']}') ?? 0.0
          : 0.0,
      daftarNilai: rawNilai
          .map((e) => NilaiMapelItem.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}

class RekapNilaiAnakModel {
  final List<UjianRaporItem> daftarUjian;

  RekapNilaiAnakModel({required this.daftarUjian});

  factory RekapNilaiAnakModel.fromJson(Map<String, dynamic> json) {
    final rawUjian = json['daftar_ujian'] as List<dynamic>? ?? [];
    return RekapNilaiAnakModel(
      daftarUjian: rawUjian
          .map((e) => UjianRaporItem.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}
