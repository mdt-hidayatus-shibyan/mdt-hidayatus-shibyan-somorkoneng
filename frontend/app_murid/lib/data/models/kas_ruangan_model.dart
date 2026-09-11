class KasRuanganAnakData {
  final bool hasKas;
  final String? pesan;
  final KasMuridModel? murid;
  final KasRuanganInfoModel? ruangan;
  final List<RiwayatPembayaranKasModel> riwayatPembayaran;

  const KasRuanganAnakData({
    required this.hasKas,
    this.pesan,
    this.murid,
    this.ruangan,
    this.riwayatPembayaran = const [],
  });

  factory KasRuanganAnakData.fromJson(Map<String, dynamic> json) {
    return KasRuanganAnakData(
      hasKas: json['has_kas'] == true,
      pesan: json['pesan'] as String?,
      murid: json['murid'] != null
          ? KasMuridModel.fromJson(json['murid'] as Map<String, dynamic>)
          : null,
      ruangan: json['ruangan'] != null
          ? KasRuanganInfoModel.fromJson(
              json['ruangan'] as Map<String, dynamic>,
            )
          : null,
      riwayatPembayaran: (json['riwayat_pembayaran'] as List<dynamic>? ?? [])
          .map(
            (e) =>
                RiwayatPembayaranKasModel.fromJson(e as Map<String, dynamic>),
          )
          .toList(),
    );
  }
}

class KasMuridModel {
  final int id;
  final String namaLengkap;
  final String nism;
  final String jenisKelamin;
  final int targetKas;
  final int totalKasTerkumpul;
  final int kurangKas;
  final String status;
  final int totalTransaksi;

  const KasMuridModel({
    required this.id,
    required this.namaLengkap,
    required this.nism,
    required this.jenisKelamin,
    required this.targetKas,
    required this.totalKasTerkumpul,
    required this.kurangKas,
    required this.status,
    required this.totalTransaksi,
  });

  factory KasMuridModel.fromJson(Map<String, dynamic> json) {
    return KasMuridModel(
      id: json['id'] as int? ?? 0,
      namaLengkap: json['nama_lengkap'] as String? ?? '',
      nism: json['nism'] as String? ?? '-',
      jenisKelamin: json['jenis_kelamin'] as String? ?? 'L',
      targetKas: (json['target_kas'] as num?)?.toInt() ?? 0,
      totalKasTerkumpul:
          (json['total_kas_terkumpul'] as num?)?.toInt() ??
          (json['total_dibayar'] as num?)?.toInt() ??
          0,
      kurangKas: (json['kurang_kas'] as num?)?.toInt() ?? 0,
      status: json['status'] as String? ?? 'Belum Bayar',
      totalTransaksi: (json['total_transaksi'] as num?)?.toInt() ?? 0,
    );
  }

  bool get isLunas => status == 'Lunas';
  bool get isBebasKas => status == 'Bebas Kas';
}

class KasRuanganInfoModel {
  final int id;
  final String namaRuangan;
  final String level;
  final String waliRuangan;

  const KasRuanganInfoModel({
    required this.id,
    required this.namaRuangan,
    required this.level,
    required this.waliRuangan,
  });

  factory KasRuanganInfoModel.fromJson(Map<String, dynamic> json) {
    return KasRuanganInfoModel(
      id: json['id'] as int? ?? 0,
      namaRuangan: json['nama_ruangan'] as String? ?? '-',
      level: json['level'] as String? ?? '-',
      waliRuangan: json['wali_ruangan'] as String? ?? '-',
    );
  }
}

class RiwayatPembayaranKasModel {
  final int id;
  final String tanggalBayar;
  final String tanggalBayarFormatted;
  final int jumlahBayar;
  final bool isDisetor;
  final String statusSetor;

  const RiwayatPembayaranKasModel({
    required this.id,
    required this.tanggalBayar,
    required this.tanggalBayarFormatted,
    required this.jumlahBayar,
    required this.isDisetor,
    required this.statusSetor,
  });

  factory RiwayatPembayaranKasModel.fromJson(Map<String, dynamic> json) {
    return RiwayatPembayaranKasModel(
      id: json['id'] as int? ?? 0,
      tanggalBayar: json['tanggal_bayar'] as String? ?? '-',
      tanggalBayarFormatted: json['tanggal_bayar_formatted'] as String? ?? '-',
      jumlahBayar: (json['jumlah_bayar'] as num?)?.toInt() ?? 0,
      isDisetor: json['is_disetor'] == true,
      statusSetor:
          json['status_setor'] as String? ?? 'Tersimpan di Wali Ruangan',
    );
  }
}
