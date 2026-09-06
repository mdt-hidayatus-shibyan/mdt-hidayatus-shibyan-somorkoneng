class AnakModel {
  final int id;
  final String nism;
  final String? nisn;
  final String? nik;
  final String namaLengkap;
  final String? namaPanggilan;
  final String? jenisKelamin;
  final String? foto;
  final String? ruangan;
  final String? statusHariIni;
  final String? tempatLahir;
  final String? tanggalLahir;
  final String? namaAyah;
  final String? namaIbu;
  final String? kampung;

  AnakModel({
    required this.id,
    required this.nism,
    this.nisn,
    this.nik,
    required this.namaLengkap,
    this.namaPanggilan,
    this.jenisKelamin,
    this.foto,
    this.ruangan,
    this.statusHariIni,
    this.tempatLahir,
    this.tanggalLahir,
    this.namaAyah,
    this.namaIbu,
    this.kampung,
  });

  factory AnakModel.fromJson(Map<String, dynamic> json) {
    return AnakModel(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      nism: json['nism']?.toString() ?? '-',
      nisn: json['nisn']?.toString(),
      nik: json['nik']?.toString(),
      namaLengkap: json['nama_lengkap']?.toString() ?? '-',
      namaPanggilan: json['nama_panggilan']?.toString(),
      jenisKelamin: json['jenis_kelamin']?.toString() ?? 'L',
      foto: json['foto']?.toString(),
      ruangan: json['ruangan']?.toString() ?? '-',
      statusHariIni: json['status_hari_ini']?.toString() ?? 'Belum Ada Sesi',
      tempatLahir: json['tempat_lahir']?.toString(),
      tanggalLahir: json['tanggal_lahir']?.toString(),
      namaAyah: json['nama_ayah']?.toString(),
      namaIbu: json['nama_ibu']?.toString(),
      kampung: json['kampung']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nism': nism,
      'nisn': nisn,
      'nik': nik,
      'nama_lengkap': namaLengkap,
      'nama_panggilan': namaPanggilan,
      'jenis_kelamin': jenisKelamin,
      'foto': foto,
      'ruangan': ruangan,
      'status_hari_ini': statusHariIni,
      'tempat_lahir': tempatLahir,
      'tanggal_lahir': tanggalLahir,
      'nama_ayah': namaAyah,
      'nama_ibu': namaIbu,
      'kampung': kampung,
    };
  }
}
