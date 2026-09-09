import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../models/tabungan_model.dart';

class TabunganRepository {
  final ApiClient _client = ApiClient();

  /// Mengambil data rekening tabungan pribadi Ustadz login
  Future<TabunganUstadzResponse> getTabunganUstadz({int? tabunganId}) async {
    try {
      final response = await _client.dio.get(
        ApiConstants.tabunganUstadzRekening,
        queryParameters:
            tabunganId != null ? {'tabungan_id': tabunganId} : null,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return TabunganUstadzResponse.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat tabungan ustadz',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat tabungan ustadz: ${e.message}');
    }
  }

  /// Mengambil data ringkasan tabungan ruangan kelas & daftar santri
  Future<TabunganRuanganResponse> getTabunganRuangan({int? ruanganId}) async {
    try {
      final response = await _client.dio.get(
        ApiConstants.tabunganRuangan,
        queryParameters: ruanganId != null ? {'ruangan_id': ruanganId} : null,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return TabunganRuanganResponse.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat tabungan kelas',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat tabungan kelas: ${e.message}');
    }
  }

  /// Mengambil detail buku tabungan & mutasi transaksi
  Future<DetailTabunganResponse> getDetailTabungan(
    int tabunganId, {
    String? bulan,
  }) async {
    try {
      final response = await _client.dio.get(
        '${ApiConstants.tabunganDetail}/$tabunganId',
        queryParameters: bulan != null ? {'bulan': bulan} : null,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return DetailTabunganResponse.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat detail tabungan',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat detail tabungan: ${e.message}');
    }
  }

  /// Eksekusi setor tunai tabungan santri
  Future<Map<String, dynamic>> setorTunai({
    required int tabunganId,
    required num nominal,
    String? tanggal,
    String? keterangan,
    int? ruanganId,
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.tabunganSetor,
        data: {
          'tabungan_id': tabunganId,
          'nominal': nominal,
          if (tanggal != null) 'tanggal': tanggal,
          if (keterangan != null) 'keterangan': keterangan,
          if (ruanganId != null) 'ruangan_id': ruanganId,
        },
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return response.data;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memproses setoran tabungan',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memproses setoran: ${e.message}');
    }
  }

  /// Pencarian rekening tabungan via Barcode / NISM / Nama
  Future<TabunganRekeningModel> cariRekening(String query) async {
    try {
      final response = await _client.dio.get(
        ApiConstants.tabunganCari,
        queryParameters: {'q': query},
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return TabunganRekeningModel.fromJson(response.data['data']);
      } else {
        throw Exception(response.data['message'] ?? 'Rekening tidak ditemukan');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Pencarian rekening gagal: ${e.message}');
    }
  }
}
