class SesiPresensiResponse {
  final bool isLibur;
  final String? keteranganLibur;
  final List<SesiPresensiItem> sesiList;

  SesiPresensiResponse({
    required this.isLibur,
    this.keteranganLibur,
    required this.sesiList,
  });

  factory SesiPresensiResponse.fromJson(Map<String, dynamic> json) {
    final rawList = json['data'] as List? ?? [];
    return SesiPresensiResponse(
      isLibur: json['is_libur'] ?? false,
      keteranganLibur: json['keterangan_libur'],
      sesiList: rawList.map((e) => SesiPresensiItem.fromJson(e)).toList(),
    );
  }
}

class SesiPresensiItem {
  final int id;
  final String jam;
  final String pelajaran;
  final String kelas;
  final String guru;
  final bool isMilikWali;
  final bool sudahAbsen;

  SesiPresensiItem({
    required this.id,
    required this.jam,
    required this.pelajaran,
    required this.kelas,
    required this.guru,
    required this.isMilikWali,
    required this.sudahAbsen,
  });

  factory SesiPresensiItem.fromJson(Map<String, dynamic> json) {
    return SesiPresensiItem(
      id: json['id'] ?? 0,
      jam: json['jam'] ?? '',
      pelajaran: json['pelajaran'] ?? json['mapel'] ?? '',
      kelas: json['kelas'] ?? json['ruangan'] ?? '',
      guru: json['guru'] ?? '',
      isMilikWali: json['is_milik_wali'] ?? false,
      sudahAbsen: json['sudah_absen'] ?? false,
    );
  }
}

class MuridPresensiItem {
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  String status; // Hadir, Sakit, Izin, Alpha, Dispensasi

  MuridPresensiItem({
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.status = 'Hadir',
  });

  factory MuridPresensiItem.fromJson(Map<String, dynamic> json) {
    return MuridPresensiItem(
      muridId: json['murid_id'] ?? json['id'] ?? 0,
      nama: json['nama'] ?? json['nama_lengkap'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      status: json['status'] ?? 'Hadir',
    );
  }

  Map<String, dynamic> toJson() {
    return {'murid_id': muridId, 'status': status};
  }
}

class RiwayatPresensiUstadz {
  final int totalHadir;
  final int totalIzin;
  final int totalSakit;
  final int totalAlpha;
  final List<RiwayatUstadzItem> riwayat;

  RiwayatPresensiUstadz({
    required this.totalHadir,
    required this.totalIzin,
    required this.totalSakit,
    required this.totalAlpha,
    required this.riwayat,
  });

  factory RiwayatPresensiUstadz.fromJson(Map<String, dynamic> json) {
    final rawList = json['riwayat'] as List? ?? [];
    return RiwayatPresensiUstadz(
      totalHadir: json['total_hadir'] ?? 0,
      totalIzin: json['total_izin'] ?? 0,
      totalSakit: json['total_sakit'] ?? 0,
      totalAlpha: json['total_alpha'] ?? 0,
      riwayat: rawList.map((e) => RiwayatUstadzItem.fromJson(e)).toList(),
    );
  }
}

class RiwayatUstadzItem {
  final int id;
  final String tanggal;
  final String mapel;
  final String ruangan;
  final String status;
  final String? ustadzPengganti;
  final String? keterangan;

  RiwayatUstadzItem({
    required this.id,
    required this.tanggal,
    required this.mapel,
    required this.ruangan,
    required this.status,
    this.ustadzPengganti,
    this.keterangan,
  });

  factory RiwayatUstadzItem.fromJson(Map<String, dynamic> json) {
    return RiwayatUstadzItem(
      id: json['id'] ?? 0,
      tanggal: json['tanggal'] ?? '',
      mapel: json['mapel'] ?? '',
      ruangan: json['ruangan'] ?? '',
      status: json['status'] ?? 'Hadir',
      ustadzPengganti: json['ustadz_pengganti'],
      keterangan: json['keterangan'],
    );
  }
}

class SesiPresensiUstadzResponse {
  final bool isLibur;
  final String? keteranganLibur;
  final List<SesiPresensiUstadzItem> sesiList;

  SesiPresensiUstadzResponse({
    required this.isLibur,
    this.keteranganLibur,
    required this.sesiList,
  });

  factory SesiPresensiUstadzResponse.fromJson(Map<String, dynamic> json) {
    final rawList = json['data'] as List? ?? [];
    return SesiPresensiUstadzResponse(
      isLibur: json['is_libur'] ?? false,
      keteranganLibur: json['keterangan_libur'],
      sesiList: rawList.map((e) => SesiPresensiUstadzItem.fromJson(e)).toList(),
    );
  }
}

class SesiPresensiUstadzItem {
  final int jadwalId;
  final String jamKe;
  final String jam;
  final String mapel;
  final String ruangan;
  final String guruPengajar;
  final bool isMilikWali;
  final bool sudahCheckin;
  final String status;
  final int? ustadzPenggantiId;
  final String? ustadzPenggantiNama;
  final String? keterangan;
  final String? waktuCheckin;

  SesiPresensiUstadzItem({
    required this.jadwalId,
    required this.jamKe,
    required this.jam,
    required this.mapel,
    required this.ruangan,
    required this.guruPengajar,
    this.isMilikWali = false,
    required this.sudahCheckin,
    required this.status,
    this.ustadzPenggantiId,
    this.ustadzPenggantiNama,
    this.keterangan,
    this.waktuCheckin,
  });

  factory SesiPresensiUstadzItem.fromJson(Map<String, dynamic> json) {
    return SesiPresensiUstadzItem(
      jadwalId: json['jadwal_id'] ?? 0,
      jamKe: json['jam_ke'] ?? '',
      jam: json['jam'] ?? '',
      mapel: json['mapel'] ?? '',
      ruangan: json['ruangan'] ?? '',
      guruPengajar: json['guru_pengajar'] ?? '-',
      isMilikWali: json['is_milik_wali'] ?? false,
      sudahCheckin: json['sudah_checkin'] ?? false,
      status: json['status'] ?? 'Belum Absen',
      ustadzPenggantiId: json['ustadz_pengganti_id'],
      ustadzPenggantiNama: json['ustadz_pengganti_nama'],
      keterangan: json['keterangan'],
      waktuCheckin: json['waktu_checkin'],
    );
  }
}

class UstadzBadalItem {
  final int id;
  final String namaLengkap;
  final String? jenisKelamin;
  final String? noHp;

  UstadzBadalItem({
    required this.id,
    required this.namaLengkap,
    this.jenisKelamin,
    this.noHp,
  });

  factory UstadzBadalItem.fromJson(Map<String, dynamic> json) {
    return UstadzBadalItem(
      id: json['id'] ?? 0,
      namaLengkap: json['nama_lengkap'] ?? '',
      jenisKelamin: json['jenis_kelamin'],
      noHp: json['no_hp'],
    );
  }
}
