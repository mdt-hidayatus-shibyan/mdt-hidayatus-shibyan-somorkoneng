import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../../core/network/api_client.dart';
import '../models/kas_ruangan_model.dart';

class KasRepository {
  final ApiClient _client = ApiClient();

  Future<KasRingkasanModel> getRingkasan({int? ruanganId}) async {
    try {
      final response = await _client.dio.get(
        ApiConstants.kasRingkasan,
        queryParameters: ruanganId != null ? {'ruangan_id': ruanganId} : null,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return KasRingkasanModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat ringkasan kas',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat ringkasan kas: ${e.message}');
    }
  }

  Future<List<MuridKasItem>> getMuridList(int ruanganId) async {
    try {
      final response = await _client.dio.get(
        ApiConstants.kasMuridList,
        queryParameters: {'ruangan_id': ruanganId},
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => MuridKasItem.fromJson(e)).toList();
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat daftar kas santri',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat kas santri: ${e.message}');
    }
  }

  Future<bool> simpanBayarKas({
    required int ruanganId,
    required int muridId,
    required num jumlahBayar,
    required String tanggalBayar,
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.kasSimpanBayar,
        data: {
          'ruangan_id': ruanganId,
          'murid_id': muridId,
          'jumlah_bayar': jumlahBayar,
          'tanggal_bayar': tanggalBayar,
        },
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal mencatat kas santri',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal mencatat kas: ${e.message}');
    }
  }

  Future<bool> updateBayarKas({
    required int id,
    required num jumlahBayar,
    required String tanggalBayar,
  }) async {
    try {
      final response = await _client.dio.post(
        '${ApiConstants.kasUpdateBayar}/$id',
        data: {'jumlah_bayar': jumlahBayar, 'tanggal_bayar': tanggalBayar},
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memperbarui kas santri',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memperbarui kas: ${e.message}');
    }
  }

  Future<bool> hapusBayarKas(int id) async {
    try {
      final response = await _client.dio.delete(
        '${ApiConstants.kasHapusBayar}/$id',
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal membatalkan pembayaran kas',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal membatalkan kas: ${e.message}');
    }
  }

  Future<List<RiwayatBayarKasItem>> getRiwayatMurid(int muridId) async {
    try {
      final response = await _client.dio.get(
        '${ApiConstants.kasRiwayatMurid}/$muridId',
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => RiwayatBayarKasItem.fromJson(e)).toList();
      } else {
        throw Exception(response.data['message'] ?? 'Gagal memuat riwayat kas');
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat riwayat kas: ${e.message}');
    }
  }

  Future<PengaturanKasItem> getPengaturan() async {
    try {
      final response = await _client.dio.get(ApiConstants.kasPengaturan);
      if (response.statusCode == 200 && response.data['success'] == true) {
        return PengaturanKasItem.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat pengaturan kas ruangan',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat pengaturan kas: ${e.message}');
    }
  }

  Future<bool> updatePengaturan({
    int? ruanganId,
    required int nominalLaki,
    required int nominalPerempuan,
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.kasPengaturan,
        data: {
          if (ruanganId != null) 'ruangan_id': ruanganId,
          'nominal_laki': nominalLaki,
          'nominal_perempuan': nominalPerempuan,
        },
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal menyimpan pengaturan kas',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal menyimpan pengaturan kas: ${e.message}');
    }
  }

  Future<RiwayatSetoranModel> getRiwayatSetoran({int? ruanganId}) async {
    try {
      final response = await _client.dio.get(
        ApiConstants.kasSetoranRiwayat,
        queryParameters: ruanganId != null ? {'ruangan_id': ruanganId} : null,
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return RiwayatSetoranModel.fromJson(response.data['data']);
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat riwayat setoran',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat riwayat setoran: ${e.message}');
    }
  }

  Future<List<PenerimaKasItem>> getPenerimaList() async {
    try {
      final response = await _client.dio.get(ApiConstants.kasSetoranPenerima);
      if (response.statusCode == 200 && response.data['success'] == true) {
        final list = response.data['data'] as List;
        return list.map((e) => PenerimaKasItem.fromJson(e)).toList();
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memuat daftar penerima setoran',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memuat daftar penerima: ${e.message}');
    }
  }

  Future<bool> simpanSetoran({
    required int ruanganId,
    required num jumlahSetor,
    required String tanggalSetor,
    int? penerimaId,
    String? keterangan,
  }) async {
    try {
      final response = await _client.dio.post(
        ApiConstants.kasSetoranSimpan,
        data: {
          'ruangan_id': ruanganId,
          'jumlah_setor': jumlahSetor,
          'tanggal_setor': tanggalSetor,
          if (penerimaId != null) 'penerima_id': penerimaId,
          if (keterangan != null && keterangan.isNotEmpty)
            'keterangan': keterangan,
        },
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal mencatat setoran kas',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal mencatat setoran: ${e.message}');
    }
  }

  Future<bool> updateSetoran({
    required int id,
    required num jumlahSetor,
    required String tanggalSetor,
    int? penerimaId,
    String? keterangan,
  }) async {
    try {
      final response = await _client.dio.post(
        '${ApiConstants.kasSetoranUpdate}/$id',
        data: {
          'jumlah_setor': jumlahSetor,
          'tanggal_setor': tanggalSetor,
          if (penerimaId != null) 'penerima_id': penerimaId,
          if (keterangan != null) 'keterangan': keterangan,
        },
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal memperbarui setoran kas',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal memperbarui setoran: ${e.message}');
    }
  }

  Future<bool> hapusSetoran(int id) async {
    try {
      final response = await _client.dio.delete(
        '${ApiConstants.kasSetoranHapus}/$id',
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return true;
      } else {
        throw Exception(
          response.data['message'] ?? 'Gagal membatalkan setoran kas',
        );
      }
    } on DioException catch (e) {
      if (e.response?.data != null && e.response?.data['message'] != null) {
        throw Exception(e.response!.data['message']);
      }
      throw Exception('Gagal membatalkan setoran: ${e.message}');
    }
  }
}
