import '../../core/network/api_client.dart';

class BulanHijriyahItem {
  final int id;
  final String namaBulan;
  final String tahunHijriyah;
  final int urutan;

  BulanHijriyahItem({
    required this.id,
    required this.namaBulan,
    required this.tahunHijriyah,
    required this.urutan,
  });

  factory BulanHijriyahItem.fromJson(Map<String, dynamic> json) {
    return BulanHijriyahItem(
      id: json['id'] ?? 0,
      namaBulan: json['nama_bulan'] ?? '',
      tahunHijriyah: json['tahun_hijriyah'] ?? '',
      urutan: json['urutan'] ?? 0,
    );
  }
}

class SppRuanganItem {
  final int id;
  final String namaRuangan;
  final String levelNama;

  SppRuanganItem({
    required this.id,
    required this.namaRuangan,
    this.levelNama = '-',
  });

  factory SppRuanganItem.fromJson(Map<String, dynamic> json) {
    return SppRuanganItem(
      id: json['id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
    );
  }
}

class SppRingkasanModel {
  final int ruanganId;
  final String namaRuangan;
  final String levelNama;
  final num nominalSppBulanan;
  final int totalSantri;
  final int totalBulan;
  final num totalTargetSpp;
  final num totalLunasNominal;
  final num totalTunggakanNominal;
  final int totalSantriLunasSemua;
  final int totalSantriBelumLunas;
  final int totalSantriBebasDonatur;
  final List<SppRuanganItem> ruanganList;
  final List<BulanHijriyahItem> bulanList;

  SppRingkasanModel({
    required this.ruanganId,
    required this.namaRuangan,
    this.levelNama = '-',
    required this.nominalSppBulanan,
    required this.totalSantri,
    required this.totalBulan,
    required this.totalTargetSpp,
    required this.totalLunasNominal,
    required this.totalTunggakanNominal,
    required this.totalSantriLunasSemua,
    required this.totalSantriBelumLunas,
    required this.totalSantriBebasDonatur,
    this.ruanganList = const [],
    this.bulanList = const [],
  });

  factory SppRingkasanModel.fromJson(Map<String, dynamic> json) {
    return SppRingkasanModel(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      nominalSppBulanan: json['nominal_spp_bulanan'] ?? 25000,
      totalSantri: json['total_santri'] ?? 0,
      totalBulan: json['total_bulan'] ?? 11,
      totalTargetSpp: json['total_target_spp'] ?? 0,
      totalLunasNominal: json['total_lunas_nominal'] ?? 0,
      totalTunggakanNominal: json['total_tunggakan_nominal'] ?? 0,
      totalSantriLunasSemua: json['total_santri_lunas_semua'] ?? 0,
      totalSantriBelumLunas: json['total_santri_belum_lunas'] ?? 0,
      totalSantriBebasDonatur: json['total_santri_bebas_donatur'] ?? 0,
      ruanganList: (json['ruangan_list'] as List? ?? [])
          .map((e) => SppRuanganItem.fromJson(e))
          .toList(),
      bulanList: (json['bulan_list'] as List? ?? [])
          .map((e) => BulanHijriyahItem.fromJson(e))
          .toList(),
    );
  }
}

class BulanSppItem {
  final int? tagihanId;
  final int bulanHijriyahId;
  final String namaBulan;
  final String tahunHijriyah;
  final num nominal;
  final String statusBayar; // Lunas, Belum Lunas, Ditanggung Donatur, Bebas SPP
  final String? noKwitansi;
  final String? tanggalBayar;
  final String? hariTanggalBayar;

  BulanSppItem({
    this.tagihanId,
    required this.bulanHijriyahId,
    required this.namaBulan,
    this.tahunHijriyah = '',
    required this.nominal,
    required this.statusBayar,
    this.noKwitansi,
    this.tanggalBayar,
    this.hariTanggalBayar,
  });

  factory BulanSppItem.fromJson(Map<String, dynamic> json) {
    return BulanSppItem(
      tagihanId: json['tagihan_id'],
      bulanHijriyahId: json['bulan_hijriyah_id'] ?? 0,
      namaBulan: json['nama_bulan'] ?? '',
      tahunHijriyah: json['tahun_hijriyah'] ?? '',
      nominal: json['nominal'] ?? 0,
      statusBayar: json['status_bayar'] ?? 'Belum Lunas',
      noKwitansi: json['no_kwitansi'],
      tanggalBayar: json['tanggal_bayar'],
      hariTanggalBayar: json['hari_tanggal_bayar'],
    );
  }
}

class MuridSppItem {
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final String wali;
  final int totalBulan;
  final int bulanLunasCount;
  final int bulanBelumLunasCount;
  final int bulanBebasCount;
  final num totalTarget;
  final num totalDibayar;
  final num sisaTunggakan;
  final String statusKeseluruhan; // Lunas, Belum Lunas, Ditanggung Donatur
  final List<BulanSppItem> bulanItems;

  MuridSppItem({
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    this.wali = '-',
    required this.totalBulan,
    required this.bulanLunasCount,
    required this.bulanBelumLunasCount,
    required this.bulanBebasCount,
    required this.totalTarget,
    required this.totalDibayar,
    required this.sisaTunggakan,
    required this.statusKeseluruhan,
    this.bulanItems = const [],
  });

  factory MuridSppItem.fromJson(Map<String, dynamic> json) {
    return MuridSppItem(
      muridId: json['murid_id'] ?? 0,
      nama: json['nama'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      wali: json['nama_wali'] ?? json['wali'] ?? '-',
      totalBulan: json['total_bulan'] ?? 11,
      bulanLunasCount: json['bulan_lunas_count'] ?? 0,
      bulanBelumLunasCount: json['bulan_belum_lunas_count'] ?? 0,
      bulanBebasCount: json['bulan_bebas_count'] ?? 0,
      totalTarget: json['total_target'] ?? 0,
      totalDibayar: json['total_dibayar'] ?? 0,
      sisaTunggakan: json['sisa_tunggakan'] ?? 0,
      statusKeseluruhan: json['status_keseluruhan'] ?? 'Belum Lunas',
      bulanItems: (json['bulan_items'] as List? ?? [])
          .map((e) => BulanSppItem.fromJson(e))
          .toList(),
    );
  }
}

class KartuSppModel {
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final String namaRuangan;
  final String levelNama;
  final String namaWali;
  final String alamat;
  final int totalBulan;
  final int bulanLunasCount;
  final int bulanBelumLunasCount;
  final int bulanBebasCount;
  final num totalTarget;
  final num totalDibayar;
  final num sisaTunggakan;
  final String statusKeseluruhan;
  final List<BulanSppItem> bulanItems;

  KartuSppModel({
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    required this.namaRuangan,
    this.levelNama = '-',
    required this.namaWali,
    required this.alamat,
    required this.totalBulan,
    required this.bulanLunasCount,
    required this.bulanBelumLunasCount,
    required this.bulanBebasCount,
    required this.totalTarget,
    required this.totalDibayar,
    required this.sisaTunggakan,
    required this.statusKeseluruhan,
    this.bulanItems = const [],
  });

  factory KartuSppModel.fromJson(Map<String, dynamic> json) {
    return KartuSppModel(
      muridId: json['murid_id'] ?? 0,
      nama: json['nama'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      namaRuangan: json['nama_ruangan'] ?? '-',
      levelNama: json['level_nama'] ?? '-',
      namaWali: json['nama_wali'] ?? json['wali'] ?? '-',
      alamat: json['alamat'] ?? '-',
      totalBulan: json['total_bulan'] ?? 11,
      bulanLunasCount: json['bulan_lunas_count'] ?? 0,
      bulanBelumLunasCount: json['bulan_belum_lunas_count'] ?? 0,
      bulanBebasCount: json['bulan_bebas_count'] ?? 0,
      totalTarget: json['total_target'] ?? 0,
      totalDibayar: json['total_dibayar'] ?? 0,
      sisaTunggakan: json['sisa_tunggakan'] ?? 0,
      statusKeseluruhan: json['status_keseluruhan'] ?? 'Belum Lunas',
      bulanItems: (json['bulan_items'] as List? ?? [])
          .map((e) => BulanSppItem.fromJson(e))
          .toList(),
    );
  }
}

class MasterTagihanNonSppItem {
  final int id;
  final String kodeTagihan;
  final String namaTagihan;
  final String tipe;
  final num nominal;

  MasterTagihanNonSppItem({
    required this.id,
    required this.kodeTagihan,
    required this.namaTagihan,
    required this.tipe,
    required this.nominal,
  });

  factory MasterTagihanNonSppItem.fromJson(Map<String, dynamic> json) {
    return MasterTagihanNonSppItem(
      id: json['id'] ?? 0,
      kodeTagihan: json['kode_tagihan'] ?? '',
      namaTagihan: json['nama_tagihan'] ?? '',
      tipe: json['tipe'] ?? '',
      nominal: json['nominal'] ?? 0,
    );
  }
}

class NonSppRingkasanModel {
  final int ruanganId;
  final String namaRuangan;
  final String levelNama;
  final int? pengaturanTagihanId;
  final String namaTagihan;
  final String kodeTagihan;
  final String tipeTagihan;
  final num nominal;
  final int totalSantri;
  final num totalTargetNominal;
  final num totalLunasNominal;
  final num totalTunggakanNominal;
  final int totalSantriLunas;
  final int totalSantriBelumLunas;
  final int totalSantriBebasDonatur;
  final List<SppRuanganItem> ruanganList;
  final List<MasterTagihanNonSppItem> masterTagihanList;

  NonSppRingkasanModel({
    required this.ruanganId,
    required this.namaRuangan,
    this.levelNama = '-',
    this.pengaturanTagihanId,
    required this.namaTagihan,
    required this.kodeTagihan,
    required this.tipeTagihan,
    required this.nominal,
    required this.totalSantri,
    required this.totalTargetNominal,
    required this.totalLunasNominal,
    required this.totalTunggakanNominal,
    required this.totalSantriLunas,
    required this.totalSantriBelumLunas,
    required this.totalSantriBebasDonatur,
    this.ruanganList = const [],
    this.masterTagihanList = const [],
  });

  factory NonSppRingkasanModel.fromJson(Map<String, dynamic> json) {
    return NonSppRingkasanModel(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      pengaturanTagihanId: json['pengaturan_tagihan_id'],
      namaTagihan: json['nama_tagihan'] ?? '-',
      kodeTagihan: json['kode_tagihan'] ?? '-',
      tipeTagihan: json['tipe_tagihan'] ?? '-',
      nominal: json['nominal'] ?? 0,
      totalSantri: json['total_santri'] ?? 0,
      totalTargetNominal: json['total_target_nominal'] ?? 0,
      totalLunasNominal: json['total_lunas_nominal'] ?? 0,
      totalTunggakanNominal: json['total_tunggakan_nominal'] ?? 0,
      totalSantriLunas: json['total_santri_lunas'] ?? 0,
      totalSantriBelumLunas: json['total_santri_belum_lunas'] ?? 0,
      totalSantriBebasDonatur: json['total_santri_bebas_donatur'] ?? 0,
      ruanganList: (json['ruangan_list'] as List? ?? [])
          .map((e) => SppRuanganItem.fromJson(e))
          .toList(),
      masterTagihanList: (json['master_tagihan_list'] as List? ?? [])
          .map((e) => MasterTagihanNonSppItem.fromJson(e))
          .toList(),
    );
  }
}

class MuridNonSppItem {
  final int? tagihanId;
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final String namaWali;
  final num nominal;
  final String statusBayar;
  final String? noKwitansi;
  final String? tanggalBayar;
  final String? hariTanggalBayar;
  final String? metodePembayaran;
  final String? tipePembayar;
  final String? catatan;

  MuridNonSppItem({
    this.tagihanId,
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    this.namaWali = '-',
    required this.nominal,
    required this.statusBayar,
    this.noKwitansi,
    this.tanggalBayar,
    this.hariTanggalBayar,
    this.metodePembayaran,
    this.tipePembayar,
    this.catatan,
  });

  factory MuridNonSppItem.fromJson(Map<String, dynamic> json) {
    return MuridNonSppItem(
      tagihanId: json['tagihan_id'],
      muridId: json['murid_id'] ?? 0,
      nama: json['nama'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      namaWali: json['nama_wali'] ?? json['wali'] ?? '-',
      nominal: json['nominal'] ?? 0,
      statusBayar: json['status_bayar'] ?? 'Belum Lunas',
      noKwitansi: json['no_kwitansi'],
      tanggalBayar: json['tanggal_bayar'],
      hariTanggalBayar: json['hari_tanggal_bayar'],
      metodePembayaran: json['metode_pembayaran'],
      tipePembayar: json['tipe_pembayar'],
      catatan: json['catatan'],
    );
  }
}
