import '../../core/network/api_client.dart';

class PoinFormatter {
  static String format(double val) {
    // Round to 2 decimal places to eliminate floating point IEEE artifacts
    final rounded = double.parse(val.toStringAsFixed(2));
    if (rounded % 1 == 0) {
      return rounded.toInt().toString();
    }
    String str = rounded.toStringAsFixed(2);
    while (str.endsWith('0')) {
      str = str.substring(0, str.length - 1);
    }
    if (str.endsWith('.')) {
      str = str.substring(0, str.length - 1);
    }
    return str;
  }
}

class ReferensiPelanggaranItem {
  final int id;
  final String namaPelanggaran;
  final String kategori;
  final double poin;

  ReferensiPelanggaranItem({
    required this.id,
    required this.namaPelanggaran,
    this.kategori = 'Ringan',
    required this.poin,
  });

  String get poinFormatted => PoinFormatter.format(poin);

  factory ReferensiPelanggaranItem.fromJson(Map<String, dynamic> json) {
    return ReferensiPelanggaranItem(
      id: json['id'] ?? 0,
      namaPelanggaran: json['nama_pelanggaran'] ?? '',
      kategori: json['kategori'] ?? 'Ringan',
      poin:
          (json['poin'] as num?)?.toDouble() ??
          double.tryParse(json['poin']?.toString() ?? '0') ??
          0.0,
    );
  }
}

class RuanganPelanggaranItem {
  final int id;
  final String namaRuangan;
  final String levelNama;
  final String waliUstadz;
  final int totalMurid;

  RuanganPelanggaranItem({
    required this.id,
    required this.namaRuangan,
    required this.levelNama,
    required this.waliUstadz,
    required this.totalMurid,
  });

  factory RuanganPelanggaranItem.fromJson(Map<String, dynamic> json) {
    return RuanganPelanggaranItem(
      id: json['id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      waliUstadz: json['wali_ustadz'] ?? '-',
      totalMurid: json['total_murid'] ?? 0,
    );
  }
}

class MuridPelanggaranItem {
  final int id;
  final String namaLengkap;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final double totalPoin;

  MuridPelanggaranItem({
    required this.id,
    required this.namaLengkap,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    required this.totalPoin,
  });

  String get totalPoinFormatted => PoinFormatter.format(totalPoin);

  factory MuridPelanggaranItem.fromJson(Map<String, dynamic> json) {
    return MuridPelanggaranItem(
      id: json['id'] ?? 0,
      namaLengkap: json['nama_lengkap'] ?? '',
      nism: json['nism'] ?? '-',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      totalPoin:
          (json['total_poin'] as num?)?.toDouble() ??
          double.tryParse(json['total_poin']?.toString() ?? '0') ??
          0.0,
    );
  }
}

class PelanggaranHarianData {
  final String tanggal;
  final int totalKasus;
  final double totalPoin;
  final int totalSantri;
  final List<PelanggaranHarianItem> list;

  PelanggaranHarianData({
    required this.tanggal,
    required this.totalKasus,
    required this.totalPoin,
    required this.totalSantri,
    required this.list,
  });

  String get totalPoinFormatted => PoinFormatter.format(totalPoin);

  factory PelanggaranHarianData.fromJson(Map<String, dynamic> json) {
    return PelanggaranHarianData(
      tanggal: json['tanggal'] ?? '',
      totalKasus: json['total_kasus'] ?? 0,
      totalPoin:
          (json['total_poin'] as num?)?.toDouble() ??
          double.tryParse(json['total_poin']?.toString() ?? '0') ??
          0.0,
      totalSantri: json['total_santri'] ?? 0,
      list: (json['list'] as List? ?? [])
          .map((item) => PelanggaranHarianItem.fromJson(item))
          .toList(),
    );
  }
}

class PelanggaranHarianItem {
  final int id;
  final String tanggal;
  final int muridId;
  final String muridNama;
  final String nism;
  final int ruanganId;
  final String ruanganNama;
  final int referensiId;
  final String pelanggaran;
  final String kategori;
  final double poin;
  final String? keterangan;
  final String diinputOleh;
  final String? waktu;

  PelanggaranHarianItem({
    required this.id,
    required this.tanggal,
    required this.muridId,
    required this.muridNama,
    required this.nism,
    required this.ruanganId,
    required this.ruanganNama,
    required this.referensiId,
    required this.pelanggaran,
    required this.kategori,
    required this.poin,
    this.keterangan,
    required this.diinputOleh,
    this.waktu,
  });

  String get poinFormatted => PoinFormatter.format(poin);

  factory PelanggaranHarianItem.fromJson(Map<String, dynamic> json) {
    return PelanggaranHarianItem(
      id: json['id'] ?? 0,
      tanggal: json['tanggal'] ?? '',
      muridId: json['murid_id'] ?? 0,
      muridNama: json['murid_nama'] ?? '',
      nism: json['nism'] ?? '',
      ruanganId: json['ruangan_id'] ?? 0,
      ruanganNama: json['ruangan_nama'] ?? '',
      referensiId: json['referensi_id'] ?? 0,
      pelanggaran: json['pelanggaran'] ?? '',
      kategori: json['kategori'] ?? 'Ringan',
      poin:
          (json['poin'] as num?)?.toDouble() ??
          double.tryParse(json['poin']?.toString() ?? '0') ??
          0.0,
      keterangan: json['keterangan'],
      diinputOleh: json['diinput_oleh'] ?? '',
      waktu: json['waktu'],
    );
  }
}

class RiwayatPelanggaranItem {
  final int id;
  final String tanggal;
  final int? muridId;
  final String muridNama;
  final String nism;
  final int? ruanganId;
  final String? ruanganNama;
  final int? referensiId;
  final String pelanggaran;
  final String kategori;
  final double poin;
  final String? keterangan;
  final String diinputOleh;

  RiwayatPelanggaranItem({
    required this.id,
    required this.tanggal,
    this.muridId,
    required this.muridNama,
    required this.nism,
    this.ruanganId,
    this.ruanganNama,
    this.referensiId,
    required this.pelanggaran,
    this.kategori = 'Ringan',
    required this.poin,
    this.keterangan,
    required this.diinputOleh,
  });

  String get poinFormatted => PoinFormatter.format(poin);

  factory RiwayatPelanggaranItem.fromJson(Map<String, dynamic> json) {
    return RiwayatPelanggaranItem(
      id: json['id'] ?? 0,
      tanggal: json['tanggal'] ?? '',
      muridId: json['murid_id'],
      muridNama: json['murid_nama'] ?? '',
      nism: json['nism'] ?? '',
      ruanganId: json['ruangan_id'],
      ruanganNama: json['ruangan_nama'],
      referensiId: json['referensi_id'],
      pelanggaran: json['pelanggaran'] ?? '',
      kategori: json['kategori'] ?? 'Ringan',
      poin:
          (json['poin'] as num?)?.toDouble() ??
          double.tryParse(json['poin']?.toString() ?? '0') ??
          0.0,
      keterangan: json['keterangan'],
      diinputOleh: json['diinput_oleh'] ?? '',
    );
  }
}
