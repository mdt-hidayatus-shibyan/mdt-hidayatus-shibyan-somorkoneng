import 'package:flutter/material.dart';
import '../core/storage/storage_service.dart';
import '../data/models/anak_model.dart';
import '../data/models/dashboard_model.dart';
import '../data/repositories/wali_repository.dart';

class DashboardProvider extends ChangeNotifier {
  final WaliRepository _repo = WaliRepository();

  bool _isLoading = false;
  String? _errorMessage;
  DashboardDataModel? _dashboardData;
  AnakModel? _selectedAnak;
  List<Map<String, dynamic>> _pengumumanList = [];

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  DashboardDataModel? get dashboardData => _dashboardData;
  AnakModel? get selectedAnak => _selectedAnak;
  List<AnakModel> get anakList => _dashboardData?.anakList ?? [];
  List<Map<String, dynamic>> get pengumumanList => _pengumumanList;

  Future<void> fetchDashboard() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final data = await _repo.getDashboard();
      _pengumumanList = await _repo.getPengumuman();

      if (data != null) {
        _dashboardData = data;
        final list = data.anakList;

        if (list.isNotEmpty) {
          final savedId = StorageService.getSelectedAnakId();
          final match = list.where((a) => a.id == savedId).firstOrNull;
          _selectedAnak = match ?? list.first;
          await StorageService.setSelectedAnakId(_selectedAnak!.id);
        } else {
          _selectedAnak = null;
        }
      }
    } catch (e) {
      _errorMessage = 'Gagal memuat data dashboard: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void switchAnak(AnakModel anak) {
    _selectedAnak = anak;
    StorageService.setSelectedAnakId(anak.id);
    notifyListeners();
  }
}
