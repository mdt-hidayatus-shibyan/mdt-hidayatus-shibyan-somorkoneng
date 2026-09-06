class DokumenItemModel {
  final String id;
  final String namaDokumen;
  final String tipeDokumen; // 'rapor', 'sk', 'ijazah'
  final String? tipeUjian;
  final String nomorDokumen;
  final String tahunPelajaran;
  final String? ruangan;
  final double rataRata;
  final int totalMapel;
  final String? statusKeputusan;
  final String? lulusDariTingkat;
  final String tanggalDisahkan;
  final String downloadUrl;
  final String cetakUrl;

  DokumenItemModel({
    required this.id,
    required this.namaDokumen,
    required this.tipeDokumen,
    this.tipeUjian,
    required this.nomorDokumen,
    required this.tahunPelajaran,
    this.ruangan,
    this.rataRata = 0,
    this.totalMapel = 0,
    this.statusKeputusan,
    this.lulusDariTingkat,
    required this.tanggalDisahkan,
    required this.downloadUrl,
    required this.cetakUrl,
  });

  factory DokumenItemModel.fromJson(Map<String, dynamic> json, String tipe) {
    return DokumenItemModel(
      id: json['id']?.toString() ?? '',
      namaDokumen: json['nama_dokumen']?.toString() ?? 'Dokumen Resmi',
      tipeDokumen: tipe,
      tipeUjian: json['tipe_ujian']?.toString(),
      nomorDokumen: json['nomor_dokumen']?.toString() ?? '-',
      tahunPelajaran: json['tahun_pelajaran']?.toString() ?? '-',
      ruangan: json['ruangan']?.toString(),
      rataRata: (json['rata_rata'] as num?)?.toDouble() ?? 0,
      totalMapel: (json['total_mapel'] as num?)?.toInt() ?? 0,
      statusKeputusan: json['status_keputusan']?.toString(),
      lulusDariTingkat: json['lulus_dari_tingkat']?.toString(),
      tanggalDisahkan: json['tanggal_disahkan']?.toString() ?? '-',
      downloadUrl: json['download_url']?.toString() ?? '',
      cetakUrl: json['cetak_url']?.toString() ?? '',
    );
  }
}

class DokumenGroupModel {
  final bool isKelasAkhir;
  final String level;
  final String ruangan;
  final List<DokumenItemModel> raporList;
  final List<DokumenItemModel> skList;
  final List<DokumenItemModel> ijazahList;

  DokumenGroupModel({
    this.isKelasAkhir = false,
    this.level = '-',
    this.ruangan = '-',
    this.raporList = const [],
    this.skList = const [],
    this.ijazahList = const [],
  });

  factory DokumenGroupModel.fromJson(Map<String, dynamic> json) {
    final santri = json['santri'] as Map<String, dynamic>? ?? {};
    final isKelasAkhir = santri['is_kelas_akhir'] == true;
    final level = santri['level']?.toString() ?? '-';
    final ruangan = santri['ruangan']?.toString() ?? '-';

    final rawRapor = json['rapor'] as List<dynamic>? ?? [];
    final rawSk = json['sk'] as List<dynamic>? ?? [];
    final rawIjazah = json['ijazah'] as List<dynamic>? ?? [];

    return DokumenGroupModel(
      isKelasAkhir: isKelasAkhir,
      level: level,
      ruangan: ruangan,
      raporList: rawRapor
          .map(
            (r) =>
                DokumenItemModel.fromJson(r as Map<String, dynamic>, 'rapor'),
          )
          .toList(),
      skList: rawSk
          .map(
            (s) => DokumenItemModel.fromJson(s as Map<String, dynamic>, 'sk'),
          )
          .toList(),
      ijazahList: rawIjazah
          .map(
            (i) =>
                DokumenItemModel.fromJson(i as Map<String, dynamic>, 'ijazah'),
          )
          .toList(),
    );
  }
}
