class WaliModel {
  final int id;
  final String noRegistrasi;
  final String noKk;
  final String namaKepalaKeluarga;
  final String? noHp;
  final String? alamat;
  final String? kampung;
  final int totalAnak;
  final bool isFirstLogin;
  final bool isPinChanged;

  WaliModel({
    required this.id,
    required this.noRegistrasi,
    required this.noKk,
    required this.namaKepalaKeluarga,
    this.noHp,
    this.alamat,
    this.kampung,
    this.totalAnak = 0,
    this.isFirstLogin = false,
    this.isPinChanged = false,
  });

  factory WaliModel.fromJson(Map<String, dynamic> json) {
    final isPinChanged =
        json['is_pin_changed'] == true || json['is_pin_changed'] == 1;
    final isFirstLogin = json['is_first_login'] != null
        ? (json['is_first_login'] == true || json['is_first_login'] == 1)
        : !isPinChanged;

    return WaliModel(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      noRegistrasi: json['no_registrasi']?.toString() ?? '-',
      noKk: json['no_kk']?.toString() ?? '-',
      namaKepalaKeluarga: json['nama_kepala_keluarga']?.toString() ?? '-',
      noHp: json['no_hp']?.toString(),
      alamat: json['alamat']?.toString(),
      kampung: json['kampung']?.toString(),
      totalAnak: json['total_anak'] is int
          ? json['total_anak']
          : int.tryParse('${json['total_anak']}') ?? 0,
      isFirstLogin: isFirstLogin,
      isPinChanged: isPinChanged,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'no_registrasi': noRegistrasi,
      'no_kk': noKk,
      'nama_kepala_keluarga': namaKepalaKeluarga,
      'no_hp': noHp,
      'alamat': alamat,
      'kampung': kampung,
      'total_anak': totalAnak,
      'is_first_login': isFirstLogin,
      'is_pin_changed': isPinChanged,
    };
  }

  WaliModel copyWith({
    int? id,
    String? noRegistrasi,
    String? noKk,
    String? namaKepalaKeluarga,
    String? noHp,
    String? alamat,
    String? kampung,
    int? totalAnak,
    bool? isFirstLogin,
    bool? isPinChanged,
  }) {
    return WaliModel(
      id: id ?? this.id,
      noRegistrasi: noRegistrasi ?? this.noRegistrasi,
      noKk: noKk ?? this.noKk,
      namaKepalaKeluarga: namaKepalaKeluarga ?? this.namaKepalaKeluarga,
      noHp: noHp ?? this.noHp,
      alamat: alamat ?? this.alamat,
      kampung: kampung ?? this.kampung,
      totalAnak: totalAnak ?? this.totalAnak,
      isFirstLogin: isFirstLogin ?? this.isFirstLogin,
      isPinChanged: isPinChanged ?? this.isPinChanged,
    );
  }
}
