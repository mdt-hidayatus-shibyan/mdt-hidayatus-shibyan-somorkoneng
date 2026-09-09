import 'package:flutter/material.dart';
import '../core/utils/date_formatter.dart';
import '../data/models/presensi_model.dart';
import '../data/repositories/wali_repository.dart';

class PresensiProvider extends ChangeNotifier {
  final WaliRepository _repo = WaliRepository();

  bool _isLoading = false;
  String? _errorMessage;
  RekapPresensiAnakModel? _rekapPresensi;
  int? _loadedAnakId;
  DateTime _selectedDate = DateTime.now();
  bool _isFilterSemua = false;

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  RekapPresensiAnakModel? get rekapPresensi => _rekapPresensi;
  List<RiwayatPresensiItem> get riwayat => _rekapPresensi?.riwayat ?? [];
  DateTime get selectedDate => _selectedDate;
  bool get isFilterSemua => _isFilterSemua;

  void setSelectedDate(DateTime date, int anakId) {
    _selectedDate = date;
    _isFilterSemua = false;
    fetchPresensi(anakId, force: true);
  }

  void setFilterSemua(int anakId) {
    _isFilterSemua = true;
    fetchPresensi(anakId, force: true);
  }

  Future<void> fetchPresensi(int anakId, {bool force = false}) async {
    if (!force &&
        _loadedAnakId == anakId &&
        _rekapPresensi != null &&
        !_isFilterSemua) {
      return;
    }

    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final dateStr = !_isFilterSemua
          ? DateFormatter.toYmd(_selectedDate)
          : null;
      final res = await _repo.getPresensiAnak(anakId, tanggal: dateStr);
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
