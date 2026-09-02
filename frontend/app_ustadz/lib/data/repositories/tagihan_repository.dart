import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../models/tagihan_model.dart';

class TagihanRepository {
  final ApiClient _client = ApiClient();

  Future<SppRingkasanModel> getSppRingkasan({int? ruanganId}) async {
    try {
      final response = await _client.dio.get(
        ApiConstants.tagihanSppRingkasan,
        queryParameters: ruanganId != null ? {'ruangan_id': ruanganId} : null,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return SppRingkasanModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat ringkasan SPP',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat ringkasan SPP: ${e.message}');
    }
  }

  Future<List<MuridSppItem>> getSppMuridList({
    required int ruanganId,
    int? bulanHijriyahId,
    String? status,
    String? search,
  }) async {
    try {
      final queryParams = <String, dynamic>{'ruangan_id': ruanganId};
      if (bulanHijriyahId != null) {
        queryParams['bulan_hijriyah_id'] = bulanHijriyahId;
      }
      if (status != null && status != 'Semua') {
        queryParams['status'] = status;
      }
      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }

      final response = await _client.dio.get(
        ApiConstants.tagihanSppMuridList,
        queryParameters: queryParams,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => MuridSppItem.fromJson(e)).toList();
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat daftar SPP santri',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat SPP santri: ${e.message}');
    }
  }

  Future<KartuSppModel> getKartuSppMurid(int muridId, {int? ruanganId}) async {
    try {
      final response = await _client.dio.get(
        '${ApiConstants.tagihanSppKartu}/$muridId',
        queryParameters: ruanganId != null ? {'ruangan_id': ruanganId} : null,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return KartuSppModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat kartu SPP santri',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat kartu SPP: ${e.message}');
    }
  }

  // =========================================================================
  // TAGIHAN NON-SPP (SEMESTER & INSIDENTAL)
  // =========================================================================

  Future<List<MasterTagihanNonSppItem>> getNonSppMasterList() async {
    try {
      final response = await _client.dio.get(
        ApiConstants.tagihanNonSppMasterList,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => MasterTagihanNonSppItem.fromJson(e)).toList();
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat master tagihan non-SPP',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat master tagihan: ${e.message}');
    }
  }

  Future<NonSppRingkasanModel> getNonSppRingkasan({
    int? ruanganId,
    int? pengaturanTagihanId,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (ruanganId != null) queryParams['ruangan_id'] = ruanganId;
      if (pengaturanTagihanId != null) {
        queryParams['pengaturan_tagihan_id'] = pengaturanTagihanId;
      }

      final response = await _client.dio.get(
        ApiConstants.tagihanNonSppRingkasan,
        queryParameters: queryParams.isNotEmpty ? queryParams : null,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return NonSppRingkasanModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat ringkasan tagihan non-SPP',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat ringkasan tagihan: ${e.message}');
    }
  }

  Future<List<MuridNonSppItem>> getNonSppMuridList({
    required int ruanganId,
    required int pengaturanTagihanId,
    String? status,
    String? search,
  }) async {
    try {
      final queryParams = <String, dynamic>{
        'ruangan_id': ruanganId,
        'pengaturan_tagihan_id': pengaturanTagihanId,
      };
      if (status != null && status != 'Semua') {
        queryParams['status'] = status;
      }
      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }

      final response = await _client.dio.get(
        ApiConstants.tagihanNonSppMuridList,
        queryParameters: queryParams,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => MuridNonSppItem.fromJson(e)).toList();
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat daftar santri non-SPP',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat santri non-SPP: ${e.message}');
    }
  }

  Future<Map<String, dynamic>> prosesBayarNonSpp({
    required List<int> tagihanIds,
    String? tipePembayar,
    String? metodePembayaran,
    String? tanggalBayar,
    String? catatan,
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.tagihanNonSppBayar,
        data: {
          'tagihan_ids': tagihanIds,
          'tipe_pembayar': tipePembayar ?? 'Wali Murid',
          'metode_pembayaran': metodePembayaran ?? 'Tunai',
          'tanggal_bayar': tanggalBayar,
          'catatan': catatan,
        },
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return response.data['data'] ?? {};
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memproses pembayaran tagihan',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memproses pembayaran: ${e.message}');
    }
  }

  Future<void> batalBayarNonSpp(int tagihanId) async {
    try {
      final response = await _client.dio.post(
        '${ApiConstants.tagihanNonSppBatalBayar}/$tagihanId',
      );
      if (response.statusCode != 200 || response.data['success'] != true) {
        throw Exception(
          response.data['message'] ?? 'Gagal membatalkan pembayaran tagihan',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal membatalkan pembayaran: ${e.message}');
    }
  }
}
