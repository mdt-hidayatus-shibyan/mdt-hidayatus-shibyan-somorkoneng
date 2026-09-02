import 'package:flutter/material.dart';
import '../data/models/dashboard_model.dart';
import '../data/repositories/dashboard_repository.dart';

class DashboardProvider extends ChangeNotifier {
  final DashboardRepository _repo = DashboardRepository();

  DashboardModel? _dashboardData;
  List<KalendarPendidikanItem> _kalendarList = [];
  List<PengumumanItem> _allPengumumanList = [];
  bool _isLoading = false;
  String? _errorMessage;

  DashboardModel? get dashboardData => _dashboardData;
  List<KalendarPendidikanItem> get kalendarList => _kalendarList;
  List<PengumumanItem> get allPengumumanList => _allPengumumanList;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> fetchDashboard() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _dashboardData = await _repo.getDashboardData();
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchKalendar() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _kalendarList = await _repo.getKalendarPendidikan();
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchPengumuman() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _allPengumumanList = await _repo.getAllPengumuman();
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void reset() {
    _dashboardData = null;
    _kalendarList = [];
    _allPengumumanList = [];
    _isLoading = false;
    _errorMessage = null;
    notifyListeners();
  }
}
