import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../models/presensi_ujian_model.dart';

class PresensiUjianRepository {
  final ApiClient _client = ApiClient();

  Future<PresensiUjianDataResponse> getPresensiUjianData({
    int? ujianId,
    int? ruanganId,
    int? jadwalUjianId,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (ujianId != null) queryParams['ujian_id'] = ujianId;
      if (ruanganId != null) queryParams['ruangan_id'] = ruanganId;
      if (jadwalUjianId != null) queryParams['jadwal_ujian_id'] = jadwalUjianId;

      final response = await _client.dio.get(
        ApiConstants.presensiUjianData,
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return PresensiUjianDataResponse.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat data presensi ujian',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat data presensi ujian: ${e.message}');
    }
  }

  Future<bool> simpanPresensiUjian({
    required int ujianId,
    required int ruanganId,
    required int jadwalUjianId,
    required Map<String, dynamic> presensi,
    Map<String, dynamic>? pengawas,
  }) async {
    try {
      final payload = <String, dynamic>{
        'ujian_id': ujianId,
        'ruangan_id': ruanganId,
        'jadwal_ujian_id': jadwalUjianId,
        'presensi': presensi,
      };

      if (pengawas != null) {
        payload['pengawas'] = pengawas;
      }

      final response = await _client.dio.post(
        ApiConstants.presensiUjianSimpan,
        data: payload,
      );

      return response.statusCode == 200 && response.data['success'] == true;
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal menyimpan presensi ujian: ${e.message}');
    }
  }
}
