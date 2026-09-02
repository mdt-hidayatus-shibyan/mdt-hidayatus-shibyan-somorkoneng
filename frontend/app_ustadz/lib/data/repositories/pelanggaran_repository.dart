import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../models/pelanggaran_model.dart';

class PelanggaranRepository {
  final ApiClient _client = ApiClient();

  Future<List<ReferensiPelanggaranItem>> getReferensi({
    int? id,
    String? query,
    String? kategori,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (id != null) queryParams['id'] = id;
      if (query != null && query.isNotEmpty) queryParams['query'] = query;
      if (kategori != null && kategori.isNotEmpty) queryParams['kategori'] = kategori;

      final response = await _client.dio.get(
        ApiConstants.pelanggaranReferensi,
        queryParameters: queryParams,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => ReferensiPelanggaranItem.fromJson(e)).toList();
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat referensi pelanggaran',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat referensi pelanggaran: ${e.message}');
    }
  }

  Future<List<RuanganPelanggaranItem>> getRuanganList() async {
    try {
      final response = await _client.dio.get(ApiConstants.pelanggaranRuanganList);
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => RuanganPelanggaranItem.fromJson(e)).toList();
      } else {
        throw Exception(response.data['message'] ?? 'Gagal memuat daftar ruangan');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat daftar ruangan: ${e.message}');
    }
  }

  Future<List<MuridPelanggaranItem>> getMuridByRuangan(int ruanganId) async {
    try {
      final response = await _client.dio.get(
        '${ApiConstants.pelanggaranMuridByRuangan}/$ruanganId',
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => MuridPelanggaranItem.fromJson(e)).toList();
      } else {
        throw Exception(response.data['message'] ?? 'Gagal memuat daftar santri');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat daftar santri: ${e.message}');
    }
  }

  Future<PelanggaranHarianData> getHarian({
    String? tanggal,
    int? ruanganId,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (tanggal != null) queryParams['tanggal'] = tanggal;
      if (ruanganId != null) queryParams['ruangan_id'] = ruanganId;

      final response = await _client.dio.get(
        ApiConstants.pelanggaranHarian,
        queryParameters: queryParams,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return PelanggaranHarianData.fromJson(response.data['data']);
      } else {
        throw Exception(response.data['message'] ?? 'Gagal memuat data harian');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat data harian: ${e.message}');
    }
  }

  Future<List<RiwayatPelanggaranItem>> getRiwayat({
    String? search,
    int? ruanganId,
    String? kategori,
    String? tanggalMulai,
    String? tanggalSelesai,
  }) async {
    try {
      final queryParams = <String, dynamic>{};
      if (search != null && search.isNotEmpty) queryParams['search'] = search;
      if (ruanganId != null) queryParams['ruangan_id'] = ruanganId;
      if (kategori != null && kategori.isNotEmpty) queryParams['kategori'] = kategori;
      if (tanggalMulai != null) queryParams['tanggal_mulai'] = tanggalMulai;
      if (tanggalSelesai != null) queryParams['tanggal_selesai'] = tanggalSelesai;

      final response = await _client.dio.get(
        ApiConstants.pelanggaranRiwayat,
        queryParameters: queryParams,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => RiwayatPelanggaranItem.fromJson(e)).toList();
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat riwayat pelanggaran',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat riwayat pelanggaran: ${e.message}');
    }
  }

  Future<bool> simpanPelanggaran({
    required String tanggal,
    required int ruanganId,
    required int muridId,
    required int referensiId,
    String? keterangan,
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.pelanggaranSimpan,
        data: {
          'tanggal': tanggal,
          'ruangan_id': ruanganId,
          'murid_id': muridId,
          'referensi_pelanggaran_id': referensiId,
          'keterangan': keterangan ?? '',
        },
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal mencatat pelanggaran',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal mencatat pelanggaran: ${e.message}');
    }
  }

  Future<bool> simpanPelanggaranMassal({
    required String tanggal,
    required int ruanganId,
    required List<int> muridIds,
    required int referensiId,
    String? keterangan,
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.pelanggaranSimpanMassal,
        data: {
          'tanggal': tanggal,
          'ruangan_id': ruanganId,
          'murid_ids': muridIds,
          'referensi_pelanggaran_id': referensiId,
          'keterangan': keterangan ?? '',
        },
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal mencatat pelanggaran massal',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal mencatat pelanggaran massal: ${e.message}');
    }
  }

  Future<bool> hapusPelanggaran(int id) async {
    try {
      final response = await _client.dio.delete('/pelanggaran/$id');
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(response.data['message'] ?? 'Gagal menghapus catatan');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal menghapus catatan: ${e.message}');
    }
  }
}
