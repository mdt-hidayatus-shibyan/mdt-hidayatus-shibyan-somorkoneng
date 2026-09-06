import 'anak_model.dart';
import 'wali_model.dart';

class DashboardDataModel {
  final WaliModel wali;
  final String tahunHijriyah;
  final String tahunMasehi;
  final int totalTagihan;
  final int totalLunas;
  final int totalTunggakan;
  final List<AnakModel> anakList;

  DashboardDataModel({
    required this.wali,
    required this.tahunHijriyah,
    required this.tahunMasehi,
    required this.totalTagihan,
    required this.totalLunas,
    required this.totalTunggakan,
    required this.anakList,
  });

  factory DashboardDataModel.fromJson(Map<String, dynamic> json) {
    final waliJson = json['wali'] as Map<String, dynamic>? ?? {};
    final tahunJson = json['tahun_pelajaran'] as Map<String, dynamic>? ?? {};
    final keuanganJson =
        json['ringkasan_keuangan'] as Map<String, dynamic>? ?? {};
    final rawAnak = json['anak'] as List<dynamic>? ?? [];

    return DashboardDataModel(
      wali: WaliModel.fromJson(waliJson),
      tahunHijriyah: tahunJson['nama_hijriyah']?.toString() ?? '-',
      tahunMasehi: tahunJson['nama_masehi']?.toString() ?? '-',
      totalTagihan: keuanganJson['total_tagihan'] is int
          ? keuanganJson['total_tagihan']
          : int.tryParse('${keuanganJson['total_tagihan']}') ?? 0,
      totalLunas: keuanganJson['total_lunas'] is int
          ? keuanganJson['total_lunas']
          : int.tryParse('${keuanganJson['total_lunas']}') ?? 0,
      totalTunggakan: keuanganJson['total_tunggakan'] is int
          ? keuanganJson['total_tunggakan']
          : int.tryParse('${keuanganJson['total_tunggakan']}') ?? 0,
      anakList: rawAnak
          .map((e) => AnakModel.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}
