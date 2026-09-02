import '../../core/network/api_client.dart';

class RuanganKasItem {
  final int id;
  final String namaRuangan;
  final String levelNama;

  RuanganKasItem({
    required this.id,
    required this.namaRuangan,
    this.levelNama = '-',
  });

  factory RuanganKasItem.fromJson(Map<String, dynamic> json) {
    return RuanganKasItem(
      id: json['id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
    );
  }
}

class KasRingkasanModel {
  final int ruanganId;
  final String namaRuangan;
  final String levelNama;
  final int totalMurid;
  final num totalTerkumpul;
  final num totalSudahDisetor;
  final num sisaDiTanganWali;
  final List<RuanganKasItem> ruanganList;

  KasRingkasanModel({
    required this.ruanganId,
    required this.namaRuangan,
    this.levelNama = '-',
    required this.totalMurid,
    required this.totalTerkumpul,
    required this.totalSudahDisetor,
    required this.sisaDiTanganWali,
    this.ruanganList = const [],
  });

  factory KasRingkasanModel.fromJson(Map<String, dynamic> json) {
    return KasRingkasanModel(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      totalMurid: json['total_murid'] ?? json['total_Murid'] ?? 0,
      totalTerkumpul: json['total_terkumpul'] ?? 0,
      totalSudahDisetor: json['total_sudah_disetor'] ?? 0,
      sisaDiTanganWali: json['sisa_di_tangan_wali'] ?? 0,
      ruanganList: (json['ruangan_list'] as List? ?? [])
          .map((e) => RuanganKasItem.fromJson(e))
          .toList(),
    );
  }
}

class MuridKasItem {
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final num targetKas;
  final num totalDibayar;
  final num sisaTunggakan;
  final String status; // Lunas, Belum Lunas

  MuridKasItem({
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    required this.targetKas,
    required this.totalDibayar,
    this.sisaTunggakan = 0,
    required this.status,
  });

  factory MuridKasItem.fromJson(Map<String, dynamic> json) {
    return MuridKasItem(
      muridId: json['murid_id'] ?? 0,
      nama: json['nama'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      targetKas: json['target_kas'] ?? 0,
      totalDibayar: json['total_dibayar'] ?? 0,
      sisaTunggakan: json['sisa_tunggakan'] ?? 0,
      status: json['status'] ?? 'Belum Lunas',
    );
  }
}

class RiwayatBayarKasItem {
  final int id;
  final String tanggalBayar;
  final String? hariTanggal;
  final num jumlahBayar;
  final bool isDisetor;
  final String keteranganStatus;

  RiwayatBayarKasItem({
    required this.id,
    required this.tanggalBayar,
    this.hariTanggal,
    required this.jumlahBayar,
    required this.isDisetor,
    required this.keteranganStatus,
  });

  factory RiwayatBayarKasItem.fromJson(Map<String, dynamic> json) {
    return RiwayatBayarKasItem(
      id: json['id'] ?? 0,
      tanggalBayar: json['tanggal_bayar'] ?? '',
      hariTanggal: json['hari_tanggal'],
      jumlahBayar: json['jumlah_bayar'] ?? 0,
      isDisetor: json['is_disetor'] ?? false,
      keteranganStatus: json['keterangan_status'] ?? '',
    );
  }
}

class PengaturanKasItem {
  final int ruanganId;
  final String namaRuangan;
  final int nominalLaki;
  final int nominalPerempuan;

  PengaturanKasItem({
    required this.ruanganId,
    required this.namaRuangan,
    required this.nominalLaki,
    required this.nominalPerempuan,
  });

  factory PengaturanKasItem.fromJson(Map<String, dynamic> json) {
    return PengaturanKasItem(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? 'Ruangan Binaan',
      nominalLaki: json['nominal_laki'] ?? 0,
      nominalPerempuan: json['nominal_perempuan'] ?? 0,
    );
  }
}

class PenerimaKasItem {
  final int id;
  final String name;
  final String username;
  final String role;

  PenerimaKasItem({
    required this.id,
    required this.name,
    required this.username,
    required this.role,
  });

  factory PenerimaKasItem.fromJson(Map<String, dynamic> json) {
    return PenerimaKasItem(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      username: json['username'] ?? '',
      role: json['role'] ?? 'Staff',
    );
  }
}

class SetoranKasItem {
  final int id;
  final int ruanganId;
  final String tanggalSetor;
  final String? hariTanggal;
  final num jumlahSetor;
  final String keterangan;
  final int? penerimaId;
  final String penerimaNama;
  final String disetorOlehNama;

  SetoranKasItem({
    required this.id,
    required this.ruanganId,
    required this.tanggalSetor,
    this.hariTanggal,
    required this.jumlahSetor,
    required this.keterangan,
    this.penerimaId,
    required this.penerimaNama,
    required this.disetorOlehNama,
  });

  factory SetoranKasItem.fromJson(Map<String, dynamic> json) {
    return SetoranKasItem(
      id: json['id'] ?? 0,
      ruanganId: json['ruangan_id'] ?? 0,
      tanggalSetor: json['tanggal_setor'] ?? '',
      hariTanggal: json['hari_tanggal'],
      jumlahSetor: json['jumlah_setor'] ?? 0,
      keterangan: json['keterangan'] ?? '-',
      penerimaId: json['penerima_id'],
      penerimaNama: json['penerima_nama'] ?? 'Bendahara Madrasah',
      disetorOlehNama: json['disetor_oleh_nama'] ?? 'Wali Ruangan',
    );
  }
}

class RiwayatSetoranModel {
  final int ruanganId;
  final String namaRuangan;
  final String levelNama;
  final num totalTerkumpul;
  final num totalDisetor;
  final num sisaDiTanganWali;
  final List<SetoranKasItem> list;

  RiwayatSetoranModel({
    required this.ruanganId,
    required this.namaRuangan,
    required this.levelNama,
    required this.totalTerkumpul,
    required this.totalDisetor,
    required this.sisaDiTanganWali,
    required this.list,
  });

  factory RiwayatSetoranModel.fromJson(Map<String, dynamic> json) {
    return RiwayatSetoranModel(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      totalTerkumpul: json['total_terkumpul'] ?? 0,
      totalDisetor: json['total_disetor'] ?? 0,
      sisaDiTanganWali: json['sisa_di_tangan_wali'] ?? 0,
      list: (json['list'] as List? ?? [])
          .map((e) => SetoranKasItem.fromJson(e))
          .toList(),
    );
  }
}
