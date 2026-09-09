class TabunganRekeningAnak {
  final int id;
  final String nomorRekening;
  final String namaRekening;
  final String namaNasabah;
  final String status;
  final String periode;
  final int saldo;
  final int totalSetor;
  final int totalTarik;
  final int totalPotongan;
  final double persentasePotongan;
  final int saldoBersihTotal;
  final int saldoDapatDitarik;

  TabunganRekeningAnak({
    required this.id,
    required this.nomorRekening,
    required this.namaRekening,
    required this.namaNasabah,
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

  factory TabunganRekeningAnak.fromJson(Map<String, dynamic> json) {
    return TabunganRekeningAnak(
      id: json['id'] ?? 0,
      nomorRekening: json['nomor_rekening'] ?? '',
      namaRekening: json['nama_rekening'] ?? 'Tabungan',
      namaNasabah: json['nama_nasabah'] ?? '',
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

class RekapMutasiBulanan {
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

  RekapMutasiBulanan({
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

  factory RekapMutasiBulanan.fromJson(Map<String, dynamic> json) {
    return RekapMutasiBulanan(
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

class TabunganKomplainModel {
  final int id;
  final String kodeKomplain;
  final int? transaksiId;
  final String? kodeTransaksi;
  final String? tanggalTransaksi;
  final int nominalTercatat;
  final int nominalKlaim;
  final int selisih;
  final String alasan;
  final String status;
  final String? catatanVerifikasi;
  final String? diverifikasiPada;
  final String? petugasVerifikasi;
  final String? createdAt;

  TabunganKomplainModel({
    required this.id,
    required this.kodeKomplain,
    this.transaksiId,
    this.kodeTransaksi,
    this.tanggalTransaksi,
    required this.nominalTercatat,
    required this.nominalKlaim,
    required this.selisih,
    required this.alasan,
    required this.status,
    this.catatanVerifikasi,
    this.diverifikasiPada,
    this.petugasVerifikasi,
    this.createdAt,
  });

  factory TabunganKomplainModel.fromJson(Map<String, dynamic> json) {
    return TabunganKomplainModel(
      id: json['id'] ?? 0,
      kodeKomplain: json['kode_komplain'] ?? '',
      transaksiId: json['transaksi_id'],
      kodeTransaksi: json['kode_transaksi'],
      tanggalTransaksi: json['tanggal_transaksi'],
      nominalTercatat: (json['nominal_tercatat'] as num?)?.toInt() ?? 0,
      nominalKlaim: (json['nominal_klaim'] as num?)?.toInt() ?? 0,
      selisih: (json['selisih'] as num?)?.toInt() ?? 0,
      alasan: json['alasan'] ?? '',
      status: json['status'] ?? 'Menunggu_Verifikasi',
      catatanVerifikasi: json['catatan_verifikasi'],
      diverifikasiPada: json['diverifikasi_pada'],
      petugasVerifikasi: json['petugas_verifikasi'],
      createdAt: json['created_at'],
    );
  }

  bool get isPending => status == 'Menunggu_Verifikasi';
  bool get isDisetujui => status == 'Disetujui';
  bool get isDitolak => status == 'Ditolak';
  bool get isDibatalkan => status == 'Dibatalkan';
}

class TransaksiTabunganAnak {
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
  final TabunganKomplainModel? komplain;

  TransaksiTabunganAnak({
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
    this.komplain,
  });

  factory TransaksiTabunganAnak.fromJson(Map<String, dynamic> json) {
    return TransaksiTabunganAnak(
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
      komplain: json['komplain'] != null
          ? TabunganKomplainModel.fromJson(json['komplain'])
          : null,
    );
  }

  bool get isSetor => jenisTransaksi.toLowerCase() == 'setor';
}

class TabunganAnakData {
  final bool hasTabungan;
  final List<TabunganRekeningAnak> daftarRekening;
  final TabunganRekeningAnak? rekening;
  final List<RekapMutasiBulanan> rekapBulanan;
  final List<TransaksiTabunganAnak> riwayat;
  final String? message;

  TabunganAnakData({
    required this.hasTabungan,
    this.daftarRekening = const [],
    this.rekening,
    this.rekapBulanan = const [],
    this.riwayat = const [],
    this.message,
  });

  factory TabunganAnakData.fromJson(Map<String, dynamic> json) {
    return TabunganAnakData(
      hasTabungan: json['has_tabungan'] ?? false,
      daftarRekening:
          (json['daftar_rekening'] as List?)
              ?.map((e) => TabunganRekeningAnak.fromJson(e))
              .toList() ??
          [],
      rekening: json['rekening'] != null
          ? TabunganRekeningAnak.fromJson(json['rekening'])
          : null,
      rekapBulanan:
          (json['rekap_bulanan'] as List?)
              ?.map((e) => RekapMutasiBulanan.fromJson(e))
              .toList() ??
          [],
      riwayat:
          (json['riwayat'] as List?)
              ?.map((e) => TransaksiTabunganAnak.fromJson(e))
              .toList() ??
          [],
      message: json['message'],
    );
  }
}
