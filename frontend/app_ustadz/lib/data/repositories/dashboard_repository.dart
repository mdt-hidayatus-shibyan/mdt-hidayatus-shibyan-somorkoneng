import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../models/dashboard_model.dart';

class DashboardRepository {
  final ApiClient _client = ApiClient();

  Future<DashboardModel> getDashboardData() async {
    try {
      final response = await _client.dio.get(ApiConstants.dashboard);
      if (response.statusCode == 200 && response.data['success'] == true) {
        return DashboardModel.fromJson(response.data['data']);
      } else {
        throw Exception(response.data['message'] ?? 'Gagal memuat dashboard');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat data dashboard: ${e.message}');
    }
  }

  Future<List<KalendarPendidikanItem>> getKalendarPendidikan() async {
    try {
      final response = await _client.dio.get(ApiConstants.kalender);
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => KalendarPendidikanItem.fromJson(e)).toList();
      } else {
        throw Exception(response.data['message'] ?? 'Gagal memuat kalender');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat kalender pendidikan: ${e.message}');
    }
  }

  Future<List<PengumumanItem>> getAllPengumuman() async {
    try {
      final response = await _client.dio.get(ApiConstants.pengumuman);
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => PengumumanItem.fromJson(e)).toList();
      } else {
        throw Exception(response.data['message'] ?? 'Gagal memuat pengumuman');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat pengumuman: ${e.message}');
    }
  }
}
