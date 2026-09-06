import 'package:dio/dio.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/network/api_client.dart';
import '../models/anak_model.dart';
import '../models/dashboard_model.dart';
import '../models/dokumen_model.dart';
import '../models/jadwal_model.dart';
import '../models/nilai_model.dart';
import '../models/pelanggaran_model.dart';
import '../models/tagihan_model.dart';
import '../models/presensi_model.dart';

class WaliRepository {
  final Dio _dio = ApiClient.instance.dio;

  Future<DashboardDataModel?> getDashboard() async {
    try {
      final res = await _dio.get(ApiEndpoints.dashboard);
      if (res.statusCode == 200 && res.data['success'] == true) {
        return DashboardDataModel.fromJson(res.data['data']);
      }
    } catch (_) {}
    return null;
  }

  Future<AnakModel?> getDetailAnak(int anakId) async {
    try {
      final res = await _dio.get('${ApiEndpoints.detailAnak}/$anakId');
      if (res.statusCode == 200 && res.data['success'] == true) {
        return AnakModel.fromJson(res.data['data']);
      }
    } catch (_) {}
    return null;
  }

  Future<RekapTagihanAnakModel?> getTagihanAnak(int anakId) async {
    try {
      final res = await _dio.get('${ApiEndpoints.tagihanAnak}/$anakId');
      if (res.statusCode == 200 && res.data['success'] == true) {
        return RekapTagihanAnakModel.fromJson(res.data['data']);
      }
    } catch (_) {}
    return null;
  }

  Future<RekapPresensiAnakModel?> getPresensiAnak(int anakId) async {
    try {
      final res = await _dio.get('${ApiEndpoints.presensiAnak}/$anakId');
      if (res.statusCode == 200 && res.data['success'] == true) {
        return RekapPresensiAnakModel.fromJson(res.data['data']);
      }
    } catch (_) {}
    return null;
  }

  Future<RekapPelanggaranAnakModel?> getPelanggaranAnak(int anakId) async {
    try {
      final res = await _dio.get('${ApiEndpoints.pelanggaranAnak}/$anakId');
      if (res.statusCode == 200 && res.data['success'] == true) {
        return RekapPelanggaranAnakModel.fromJson(res.data['data']);
      }
    } catch (_) {}
    return null;
  }

  Future<RekapNilaiAnakModel?> getNilaiAnak(int anakId) async {
    try {
      final res = await _dio.get('${ApiEndpoints.nilaiAnak}/$anakId');
      if (res.statusCode == 200 && res.data['success'] == true) {
        return RekapNilaiAnakModel.fromJson(res.data['data']);
      }
    } catch (_) {}
    return null;
  }

  Future<List<JadwalItemModel>> getJadwalAnak(int anakId) async {
    try {
      final res = await _dio.get('${ApiEndpoints.jadwalAnak}/$anakId');
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = res.data['data']?['jadwal'] as List<dynamic>? ?? [];
        return list.map((e) => JadwalItemModel.fromJson(e)).toList();
      }
    } catch (_) {}
    return [];
  }

  Future<DokumenGroupModel?> getDokumenAnak(int anakId) async {
    try {
      final res = await _dio.get('${ApiEndpoints.dokumenAnak}/$anakId');
      if (res.statusCode == 200 && res.data['success'] == true) {
        return DokumenGroupModel.fromJson(res.data['data']);
      }
    } catch (_) {}
    return null;
  }

  Future<Map<String, dynamic>?> getBantuanKontak() async {
    try {
      final res = await _dio.get(ApiEndpoints.bantuanKontak);
      if (res.statusCode == 200 && res.data['success'] == true) {
        return res.data['data'] as Map<String, dynamic>;
      }
    } catch (_) {}
    return null;
  }

  Future<List<Map<String, dynamic>>> getPengumuman() async {
    try {
      final res = await _dio.get(ApiEndpoints.pengumuman);
      if (res.statusCode == 200 && res.data['success'] == true) {
        final list = res.data['data'] as List<dynamic>? ?? [];
        return list.map((e) => e as Map<String, dynamic>).toList();
      }
    } catch (_) {}
    return [];
  }
}
