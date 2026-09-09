import 'package:flutter/material.dart';
import '../core/storage/storage_service.dart';
import '../data/models/anak_model.dart';
import '../data/models/dashboard_model.dart';
import '../data/models/jadwal_model.dart';
import '../data/repositories/wali_repository.dart';

class DashboardProvider extends ChangeNotifier {
  final WaliRepository _repo = WaliRepository();

  bool _isLoading = false;
  String? _errorMessage;
  DashboardDataModel? _dashboardData;
  AnakModel? _selectedAnak;
  List<Map<String, dynamic>> _pengumumanList = [];

  JadwalDetailAnakModel? _jadwalAnak;
  bool _isLoadingJadwal = false;

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  DashboardDataModel? get dashboardData => _dashboardData;
  AnakModel? get selectedAnak => _selectedAnak;
  List<AnakModel> get anakList => _dashboardData?.anakList ?? [];
  List<Map<String, dynamic>> get pengumumanList => _pengumumanList;
  JadwalDetailAnakModel? get jadwalAnak => _jadwalAnak;
  bool get isLoadingJadwal => _isLoadingJadwal;

  Future<void> fetchDashboard() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final results = await Future.wait([
        _repo.getDashboard(),
        _repo.getPengumuman(),
      ]);

      final data = results[0] as DashboardDataModel?;
      _pengumumanList = results[1] as List<Map<String, dynamic>>;

      if (data != null) {
        _dashboardData = data;
        final list = data.anakList;

        if (list.isNotEmpty) {
          final savedId = StorageService.getSelectedAnakId();
          final match = list.where((a) => a.id == savedId).firstOrNull;
          _selectedAnak = match ?? list.first;
          await StorageService.setSelectedAnakId(_selectedAnak!.id);
          await fetchJadwalForAnak(_selectedAnak!.id);
        } else {
          _selectedAnak = null;
          _jadwalAnak = null;
        }
      }
    } catch (e) {
      _errorMessage = 'Gagal memuat data dashboard: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchJadwalForAnak(int anakId) async {
    _isLoadingJadwal = true;
    notifyListeners();

    try {
      _jadwalAnak = await _repo.getJadwalAnak(anakId);
    } catch (_) {
      _jadwalAnak = null;
    } finally {
      _isLoadingJadwal = false;
      notifyListeners();
    }
  }

  void switchAnak(AnakModel anak) {
    _selectedAnak = anak;
    StorageService.setSelectedAnakId(anak.id);
    fetchJadwalForAnak(anak.id);
    notifyListeners();
  }
}
