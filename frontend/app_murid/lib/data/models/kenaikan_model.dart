class KenaikanAnakModel {
  final int muridId;
  final String namaMurid;
  final String nism;
  final bool isKelasAkhir;
  final bool isDisahkan;
  final String tahunPelajaran;
  final String ruanganAsal;
  final String levelAsal;
  final String? levelTujuan;
  final String? statusKeputusan; // 'Naik Kelas', 'Tinggal Kelas', 'Lulus'
  final String? noSk;
  final double nilaiAkumulasi;
  final String? catatanWaliKelas;
  final String? tanggalDisahkan;
  final String? tanggalDisahkanFormatted;
  final String? diputuskanOleh;
  final String? pesan;

  KenaikanAnakModel({
    required this.muridId,
    required this.namaMurid,
    required this.nism,
    required this.isKelasAkhir,
    required this.isDisahkan,
    required this.tahunPelajaran,
    required this.ruanganAsal,
    required this.levelAsal,
    this.levelTujuan,
    this.statusKeputusan,
    this.noSk,
    this.nilaiAkumulasi = 0.0,
    this.catatanWaliKelas,
    this.tanggalDisahkan,
    this.tanggalDisahkanFormatted,
    this.diputuskanOleh,
    this.pesan,
  });

  factory KenaikanAnakModel.fromJson(Map<String, dynamic> json) {
    final murid = json['murid'] as Map<String, dynamic>? ?? {};

    return KenaikanAnakModel(
      muridId: murid['id'] is int
          ? murid['id']
          : int.tryParse('${murid['id']}') ?? 0,
      namaMurid: murid['nama_lengkap']?.toString() ?? '',
      nism: murid['nism']?.toString() ?? '',
      isKelasAkhir: murid['is_kelas_akhir'] == true,
      isDisahkan: json['is_disahkan'] == true,
      tahunPelajaran: json['tahun_pelajaran']?.toString() ?? '-',
      ruanganAsal: json['ruangan_asal']?.toString() ?? '-',
      levelAsal: json['level_asal']?.toString() ?? '-',
      levelTujuan: json['level_tujuan']?.toString(),
      statusKeputusan: json['status_keputusan']?.toString(),
      noSk: json['no_sk']?.toString(),
      nilaiAkumulasi: json['nilai_akumulasi'] != null
          ? double.tryParse('${json['nilai_akumulasi']}') ?? 0.0
          : 0.0,
      catatanWaliKelas: json['catatan_wali_kelas']?.toString(),
      tanggalDisahkan: json['tanggal_disahkan']?.toString(),
      tanggalDisahkanFormatted: json['tanggal_disahkan_formatted']?.toString(),
      diputuskanOleh: json['diputuskan_oleh']?.toString(),
      pesan: json['pesan']?.toString(),
    );
  }

  bool get isNaikKelas => statusKeputusan == 'Naik Kelas';
  bool get isLulus => statusKeputusan == 'Lulus';
  bool get isTinggalKelas => statusKeputusan == 'Tinggal Kelas';
}
