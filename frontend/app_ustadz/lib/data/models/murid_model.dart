import '../../core/network/api_client.dart';

class MuridModel {
  final int id;
  final String nism;
  final String? nisn;
  final String? nik;
  final String namaLengkap;
  final String? namaPanggilan;
  final String jenisKelamin;
  final String? tempatLahir;
  final String? tanggalLahir;
  final String? foto;
  final String? namaAyah;
  final String? statusAyah;
  final String? namaIbu;
  final String? statusIbu;
  final String status;
  final bool isYatim;

  MuridModel({
    required this.id,
    required this.nism,
    this.nisn,
    this.nik,
    required this.namaLengkap,
    this.namaPanggilan,
    required this.jenisKelamin,
    this.tempatLahir,
    this.tanggalLahir,
    this.foto,
    this.namaAyah,
    this.statusAyah,
    this.namaIbu,
    this.statusIbu,
    required this.status,
    required this.isYatim,
  });

  factory MuridModel.fromJson(Map<String, dynamic> json) {
    return MuridModel(
      id: json['id'] ?? 0,
      nism: json['nism'] ?? '',
      nisn: json['nisn'],
      nik: json['nik'],
      namaLengkap: json['nama_lengkap'] ?? json['nama'] ?? '',
      namaPanggilan: json['nama_panggilan'],
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      tempatLahir: json['tempat_lahir'],
      tanggalLahir: json['tanggal_lahir'],
      foto: ApiClient.resolveImageUrl(json['foto']),
      namaAyah: json['nama_ayah'],
      statusAyah: json['status_ayah'],
      namaIbu: json['nama_ibu'],
      statusIbu: json['status_ibu'],
      status: json['status'] ?? 'Aktif',
      isYatim: json['is_yatim'] ?? false,
    );
  }
}
