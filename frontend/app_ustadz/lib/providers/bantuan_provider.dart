import 'package:flutter/material.dart';
import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';

class BantuanProvider extends ChangeNotifier {
  final ApiClient _client = ApiClient();

  Map<String, dynamic>? _kontak;
  List<dynamic> _riwayat = [];
  bool _isLoadingKontak = false;
  bool _isLoadingRiwayat = false;
  bool _isSubmitting = false;
  String? _errorMessage;

  Map<String, dynamic>? get kontak => _kontak;
  List<dynamic> get riwayat => _riwayat;
  bool get isLoadingKontak => _isLoadingKontak;
  bool get isLoadingRiwayat => _isLoadingRiwayat;
  bool get isSubmitting => _isSubmitting;
  String? get errorMessage => _errorMessage;

  /// Ambil data kontak resmi admin & FAQ
  Future<void> fetchKontak() async {
    _isLoadingKontak = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _client.dio.get(ApiConstants.bantuanKontak);
      if (response.data != null && response.data['success'] == true) {
        _kontak = response.data['data'] as Map<String, dynamic>?;
      }
    } catch (e) {
      _errorMessage = 'Gagal memuat informasi kontak admin.';
    } finally {
      _isLoadingKontak = false;
      notifyListeners();
    }
  }

  /// Ambil riwayat laporan ustadz
  Future<void> fetchRiwayat() async {
    _isLoadingRiwayat = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _client.dio.get(ApiConstants.bantuanRiwayat);
      if (response.data != null && response.data['success'] == true) {
        final rawData = response.data['data'];
        if (rawData is Map && rawData['data'] is List) {
          _riwayat = rawData['data'] as List<dynamic>;
        } else if (rawData is List) {
          _riwayat = rawData;
        }
      }
    } catch (e) {
      _errorMessage = 'Gagal memuat riwayat laporan.';
    } finally {
      _isLoadingRiwayat = false;
      notifyListeners();
    }
  }

  /// Kirim laporan / kendala / saran baru
  Future<Map<String, dynamic>?> submitLaporan({
    required String kategori,
    required String judul,
    required String deskripsi,
    String? tipePerangkat,
    String? versiAplikasi,
  }) async {
    _isSubmitting = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _client.dio.post(
        ApiConstants.bantuanLaporan,
        data: {
          'kategori': kategori,
          'judul': judul,
          'deskripsi': deskripsi,
          'tipe_perangkat': tipePerangkat ?? 'Android App',
          'versi_aplikasi': versiAplikasi ?? '1.0.0',
        },
      );

      if (response.data != null && response.data['success'] == true) {
        await fetchRiwayat();
        return response.data['data'] as Map<String, dynamic>?;
      } else {
        _errorMessage = response.data?['message'] ?? 'Gagal menyimpan laporan.';
        return null;
      }
    } catch (e) {
      _errorMessage = 'Terjadi kesalahan saat mengirim laporan: $e';
      return null;
    } finally {
      _isSubmitting = false;
      notifyListeners();
    }
  }
}
