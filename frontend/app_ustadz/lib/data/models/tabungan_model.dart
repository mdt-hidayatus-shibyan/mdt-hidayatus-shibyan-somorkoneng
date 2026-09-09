class TabunganRekeningModel {
  final int id;
  final String nomorRekening;
  final String namaRekening;
  final String namaNasabah;
  final String jenisNasabah;
  final String identitasNasabah;
  final String status;
  final String periode;
  final int saldo;
  final int totalSetor;
  final int totalTarik;
  final int totalPotongan;
  final double persentasePotongan;
  final int saldoBersihTotal;
  final int saldoDapatDitarik;

  TabunganRekeningModel({
    required this.id,
    required this.nomorRekening,
    required this.namaRekening,
    required this.namaNasabah,
    required this.jenisNasabah,
    this.identitasNasabah = '',
    required this.status,
    required this.periode,
    required this.saldo,
    required this.totalSetor,
    required this.totalTarik,
    required this.totalPotongan,
    required this.persentasePotongan,
    required this.saldoBersihTotal,
    required this.saldoDapatDitarik,
  });

  factory TabunganRekeningModel.fromJson(Map<String, dynamic> json) {
    return TabunganRekeningModel(
      id: json['id'] ?? 0,
      nomorRekening: json['nomor_rekening'] ?? '',
      namaRekening: json['nama_rekening'] ?? 'Tabungan',
      namaNasabah: json['nama_nasabah'] ?? '',
      jenisNasabah: json['jenis_nasabah'] ?? '',
      identitasNasabah: json['identitas_nasabah'] ?? '',
      status: json['status'] ?? 'Aktif',
      periode: json['periode'] ?? 'Tabungan Bebas',
      saldo: (json['saldo'] as num?)?.toInt() ?? 0,
      totalSetor: (json['total_setor'] as num?)?.toInt() ?? 0,
      totalTarik: (json['total_tarik'] as num?)?.toInt() ?? 0,
      totalPotongan: (json['total_potongan'] as num?)?.toInt() ?? 0,
      persentasePotongan:
          (json['persentase_potongan'] as num?)?.toDouble() ?? 0.0,
      saldoBersihTotal: (json['saldo_bersih_total'] as num?)?.toInt() ?? 0,
      saldoDapatDitarik: (json['saldo_dapat_ditarik'] as num?)?.toInt() ?? 0,
    );
  }
}

class TabunganUstadzResponse {
  final bool hasTabungan;
  final List<TabunganRekeningModel> daftarRekening;
  final TabunganRekeningModel? rekening;
  final List<RekapMutasiBulanItem> rekapBulanan;
  final List<TransaksiTabunganItem> transaksiTerbaru;
  final String? message;

  TabunganUstadzResponse({
    required this.hasTabungan,
    this.daftarRekening = const [],
    this.rekening,
    this.rekapBulanan = const [],
    this.transaksiTerbaru = const [],
    this.message,
  });

  factory TabunganUstadzResponse.fromJson(Map<String, dynamic> json) {
    return TabunganUstadzResponse(
      hasTabungan: json['has_tabungan'] ?? false,
      daftarRekening:
          (json['daftar_rekening'] as List?)
              ?.map((e) => TabunganRekeningModel.fromJson(e))
              .toList() ??
          [],
      rekening: json['rekening'] != null
          ? TabunganRekeningModel.fromJson(json['rekening'])
          : null,
      rekapBulanan:
          (json['rekap_bulanan'] as List?)
              ?.map((e) => RekapMutasiBulanItem.fromJson(e))
              .toList() ??
          [],
      transaksiTerbaru:
          (json['transaksi_terbaru'] as List?)
              ?.map((e) => TransaksiTabunganItem.fromJson(e))
              .toList() ??
          [],
      message: json['message'],
    );
  }
}

class RuanganItemModel {
  final int id;
  final String namaRuangan;
  final String levelNama;

  RuanganItemModel({
    required this.id,
    required this.namaRuangan,
    required this.levelNama,
  });

  factory RuanganItemModel.fromJson(Map<String, dynamic> json) {
    return RuanganItemModel(
      id: json['id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '',
    );
  }
}

class TabunganRuanganResponse {
  final bool hasTabungan;
  final RuanganItemModel ruangan;
  final List<RuanganItemModel> ruanganList;
  final TabunganRekeningModel? rekening;
  final List<RekapMutasiBulanItem> rekapBulanan;
  final List<TransaksiTabunganItem> transaksiTerbaru;
  final String? message;

  TabunganRuanganResponse({
    required this.hasTabungan,
    required this.ruangan,
    this.ruanganList = const [],
    this.rekening,
    this.rekapBulanan = const [],
    this.transaksiTerbaru = const [],
    this.message,
  });

  factory TabunganRuanganResponse.fromJson(Map<String, dynamic> json) {
    return TabunganRuanganResponse(
      hasTabungan: json['has_tabungan'] ?? false,
      ruangan: RuanganItemModel.fromJson(json['ruangan'] ?? {}),
      ruanganList:
          (json['ruangan_list'] as List?)
              ?.map((e) => RuanganItemModel.fromJson(e))
              .toList() ??
          [],
      rekening: json['rekening'] != null
          ? TabunganRekeningModel.fromJson(json['rekening'])
          : null,
      rekapBulanan:
          (json['rekap_bulanan'] as List?)
              ?.map((e) => RekapMutasiBulanItem.fromJson(e))
              .toList() ??
          [],
      transaksiTerbaru:
          (json['transaksi_terbaru'] as List?)
              ?.map((e) => TransaksiTabunganItem.fromJson(e))
              .toList() ??
          [],
      message: json['message'],
    );
  }
}

class TransaksiTabunganItem {
  final int id;
  final String kodeTransaksi;
  final String tanggal;
  final String jenisTransaksi;
  final int nominal;
  final int saldoAwal;
  final int saldoAkhir;
  final String kategori;
  final String keterangan;
  final String petugas;
  final String metode;

  TransaksiTabunganItem({
    required this.id,
    required this.kodeTransaksi,
    required this.tanggal,
    required this.jenisTransaksi,
    required this.nominal,
    required this.saldoAwal,
    required this.saldoAkhir,
    required this.kategori,
    required this.keterangan,
    required this.petugas,
    required this.metode,
  });

  factory TransaksiTabunganItem.fromJson(Map<String, dynamic> json) {
    return TransaksiTabunganItem(
      id: json['id'] ?? 0,
      kodeTransaksi: json['kode_transaksi'] ?? '',
      tanggal: json['tanggal'] ?? '',
      jenisTransaksi: json['jenis_transaksi'] ?? 'Setor',
      nominal: (json['nominal'] as num?)?.toInt() ?? 0,
      saldoAwal: (json['saldo_awal'] as num?)?.toInt() ?? 0,
      saldoAkhir: (json['saldo_akhir'] as num?)?.toInt() ?? 0,
      kategori: json['kategori'] ?? '-',
      keterangan: json['keterangan'] ?? '-',
      petugas: json['petugas'] ?? 'Sistem',
      metode: json['metode'] ?? 'Tunai',
    );
  }

  bool get isSetor => jenisTransaksi.toLowerCase() == 'setor';
}

class RekapMutasiBulanItem {
  final String key;
  final String label;
  final int tahun;
  final int bulan;
  final int saldoAwal;
  final int totalSetor;
  final int frekuensiSetor;
  final int totalTarik;
  final int frekuensiTarik;
  final int netMutasi;
  final int saldoAkhir;

  RekapMutasiBulanItem({
    required this.key,
    required this.label,
    required this.tahun,
    required this.bulan,
    required this.saldoAwal,
    required this.totalSetor,
    required this.frekuensiSetor,
    required this.totalTarik,
    required this.frekuensiTarik,
    required this.netMutasi,
    required this.saldoAkhir,
  });

  factory RekapMutasiBulanItem.fromJson(Map<String, dynamic> json) {
    return RekapMutasiBulanItem(
      key: json['key'] ?? '',
      label: json['label'] ?? '',
      tahun: json['tahun'] ?? 0,
      bulan: json['bulan'] ?? 0,
      saldoAwal: (json['saldo_awal'] as num?)?.toInt() ?? 0,
      totalSetor: (json['total_setor'] as num?)?.toInt() ?? 0,
      frekuensiSetor: (json['frekuensi_setor'] as num?)?.toInt() ?? 0,
      totalTarik: (json['total_tarik'] as num?)?.toInt() ?? 0,
      frekuensiTarik: (json['frekuensi_tarik'] as num?)?.toInt() ?? 0,
      netMutasi: (json['net_mutasi'] as num?)?.toInt() ?? 0,
      saldoAkhir: (json['saldo_akhir'] as num?)?.toInt() ?? 0,
    );
  }
}

class DetailTabunganResponse {
  final TabunganRekeningModel rekening;
  final List<RekapMutasiBulanItem> rekapBulanan;
  final List<TransaksiTabunganItem> riwayat;

  DetailTabunganResponse({
    required this.rekening,
    required this.rekapBulanan,
    required this.riwayat,
  });

  factory DetailTabunganResponse.fromJson(Map<String, dynamic> json) {
    return DetailTabunganResponse(
      rekening: TabunganRekeningModel.fromJson(json['rekening'] ?? {}),
      rekapBulanan:
          (json['rekap_bulanan'] as List?)
              ?.map((e) => RekapMutasiBulanItem.fromJson(e))
              .toList() ??
          [],
      riwayat:
          (json['riwayat'] as List?)
              ?.map((e) => TransaksiTabunganItem.fromJson(e))
              .toList() ??
          [],
    );
  }
}
