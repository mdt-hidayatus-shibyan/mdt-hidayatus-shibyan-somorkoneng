import 'package:flutter/material.dart';
import '../data/models/presensi_model.dart';
import '../data/repositories/wali_repository.dart';

class PresensiProvider extends ChangeNotifier {
  final WaliRepository _repo = WaliRepository();

  bool _isLoading = false;
  String? _errorMessage;
  RekapPresensiAnakModel? _rekapPresensi;
  int? _loadedAnakId;

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  RekapPresensiAnakModel? get rekapPresensi => _rekapPresensi;
  List<RiwayatPresensiItem> get riwayat => _rekapPresensi?.riwayat ?? [];

  Future<void> fetchPresensi(int anakId, {bool force = false}) async {
    if (!force && _loadedAnakId == anakId && _rekapPresensi != null) return;

    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _repo.getPresensiAnak(anakId);
      _rekapPresensi = res;
      _loadedAnakId = anakId;
    } catch (e) {
      _errorMessage = 'Gagal memuat presensi: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
