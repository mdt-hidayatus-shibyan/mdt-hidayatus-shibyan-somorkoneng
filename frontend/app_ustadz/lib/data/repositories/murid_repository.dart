import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../models/murid_model.dart';

class MuridRuanganResponse {
  final String namaRuangan;
  final List<MuridModel> murids;

  MuridRuanganResponse({required this.namaRuangan, required this.murids});
}

class MuridRepository {
  final ApiClient _client = ApiClient();

  Future<MuridRuanganResponse> getMuridRuangan({int? ruanganId}) async {
    try {
      final Map<String, dynamic> query = {};
      if (ruanganId != null && ruanganId > 0) {
        query['ruangan_id'] = ruanganId;
      }

      final response = await _client.dio.get(
        ApiConstants.muridRuangan,
        queryParameters: query,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final namaRuangan = data['ruangan'] ?? 'Ruangan';
        final rawList = data['murids'] as List? ?? [];
        final list = rawList.map((e) => MuridModel.fromJson(e)).toList();
        return MuridRuanganResponse(namaRuangan: namaRuangan, murids: list);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat direktori santri',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat data santri ruangan: ${e.message}');
    }
  }
}
