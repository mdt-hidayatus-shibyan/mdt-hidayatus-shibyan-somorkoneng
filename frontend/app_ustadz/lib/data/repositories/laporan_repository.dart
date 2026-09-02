import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../models/laporan_model.dart';

class LaporanRepository {
  final ApiClient _client = ApiClient();

  // 1. Laporan Presensi Murid
  Future<LaporanPresensiMuridModel> getLaporanPresensiMurid({
    int? ruanganId,
    int? bulanHijriyahId,
    String? semester,
    String? startDate,
    String? endDate,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (ruanganId != null) queryParams['ruangan_id'] = ruanganId;
      if (bulanHijriyahId != null) {
        queryParams['bulan_hijriyah_id'] = bulanHijriyahId;
      }
      if (semester != null) queryParams['semester'] = semester;
      if (startDate != null) queryParams['start_date'] = startDate;
      if (endDate != null) queryParams['end_date'] = endDate;

      final response = await _client.dio.get(
        ApiConstants.laporanPresensiMurid,
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return LaporanPresensiMuridModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat laporan presensi santri.',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat laporan presensi santri: ${e.message}');
    }
  }

  // 2. Laporan Presensi Ustadz
  Future<LaporanPresensiUstadzModel> getLaporanPresensiUstadz({
    int? ustadzId,
    int? bulanHijriyahId,
    String? startDate,
    String? endDate,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (ustadzId != null) queryParams['ustadz_id'] = ustadzId;
      if (bulanHijriyahId != null) {
        queryParams['bulan_hijriyah_id'] = bulanHijriyahId;
      }
      if (startDate != null) queryParams['start_date'] = startDate;
      if (endDate != null) queryParams['end_date'] = endDate;

      final response = await _client.dio.get(
        ApiConstants.laporanPresensiUstadz,
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return LaporanPresensiUstadzModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat laporan presensi ustadz.',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat laporan presensi ustadz: ${e.message}');
    }
  }

  // 3. Laporan Pelanggaran Murid
  Future<LaporanPelanggaranMuridModel> getLaporanPelanggaranMurid({
    int? ruanganId,
    String? kategori,
    String? startDate,
    String? endDate,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (ruanganId != null) queryParams['ruangan_id'] = ruanganId;
      if (kategori != null && kategori != 'Semua') {
        queryParams['kategori'] = kategori;
      }
      if (startDate != null) queryParams['start_date'] = startDate;
      if (endDate != null) queryParams['end_date'] = endDate;

      final response = await _client.dio.get(
        ApiConstants.laporanPelanggaranMurid,
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return LaporanPelanggaranMuridModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ??
              'Gagal memuat laporan pelanggaran santri.',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat laporan pelanggaran santri: ${e.message}');
    }
  }

  // 4. Laporan Ujian & Leger
  Future<LaporanUjianModel> getLaporanUjian({
    int? ruanganId,
    int? ujianId,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (ruanganId != null) queryParams['ruangan_id'] = ruanganId;
      if (ujianId != null) queryParams['ujian_id'] = ujianId;

      final response = await _client.dio.get(
        ApiConstants.laporanUjian,
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return LaporanUjianModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat laporan ujian.',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat laporan ujian: ${e.message}');
    }
  }

  // 5. Laporan Kenaikan Kelas & Kelulusan
  Future<LaporanKenaikanKelasModel> getLaporanKenaikanKelas({
    int? ruanganId,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (ruanganId != null) queryParams['ruangan_id'] = ruanganId;

      final response = await _client.dio.get(
        ApiConstants.laporanKenaikanKelas,
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return LaporanKenaikanKelasModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat laporan kenaikan kelas.',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat laporan kenaikan kelas: ${e.message}');
    }
  }
}
