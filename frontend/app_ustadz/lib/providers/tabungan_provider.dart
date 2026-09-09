import 'package:flutter/material.dart';
import '../data/models/tabungan_model.dart';
import '../data/repositories/tabungan_repository.dart';

class TabunganProvider with ChangeNotifier {
  final TabunganRepository _repository = TabunganRepository();

  bool _isLoading = false;
  bool _isSubmitting = false;
  String? _errorMessage;

  TabunganUstadzResponse? _tabunganUstadz;
  TabunganRuanganResponse? _tabunganRuangan;
  DetailTabunganResponse? _detailTabungan;
  TabunganRekeningModel? _searchResult;

  int? _selectedTabunganId;
  int? _selectedRuanganId;
  String _searchQuery = '';
  String? _selectedBulanFilter;

  // Getters
  bool get isLoading => _isLoading;
  bool get isSubmitting => _isSubmitting;
  String? get errorMessage => _errorMessage;

  TabunganUstadzResponse? get tabunganUstadz => _tabunganUstadz;
  TabunganRuanganResponse? get tabunganRuangan => _tabunganRuangan;
  DetailTabunganResponse? get detailTabungan => _detailTabungan;
  TabunganRekeningModel? get searchResult => _searchResult;

  int? get selectedTabunganId => _selectedTabunganId;
  int? get selectedRuanganId => _selectedRuanganId;
  String get searchQuery => _searchQuery;
  String? get selectedBulanFilter => _selectedBulanFilter;

  /// Memuat data tabungan pribadi ustadz
  Future<void> fetchTabunganUstadz({int? tabunganId}) async {
    _isLoading = true;
    _errorMessage = null;
    if (tabunganId != null) {
      _selectedTabunganId = tabunganId;
    }
    notifyListeners();

    try {
      _tabunganUstadz = await _repository.getTabunganUstadz(
        tabunganId: _selectedTabunganId,
      );
      if (_tabunganUstadz?.rekening != null) {
        _selectedTabunganId = _tabunganUstadz!.rekening!.id;
      }
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  /// Memuat data tabungan santri di ruangan binaan
  Future<void> fetchTabunganRuangan({int? ruanganId}) async {
    _isLoading = true;
    _errorMessage = null;
    if (ruanganId != null) {
      _selectedRuanganId = ruanganId;
    }
    notifyListeners();

    try {
      _tabunganRuangan = await _repository.getTabunganRuangan(
        ruanganId: _selectedRuanganId,
      );
      if (_selectedRuanganId == null && _tabunganRuangan != null) {
        _selectedRuanganId = _tabunganRuangan!.ruangan.id;
      }
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  /// Memuat detail mutasi rekening tertentu
  Future<void> fetchDetailTabungan(int tabunganId, {String? bulan}) async {
    _isLoading = true;
    _errorMessage = null;
    _selectedBulanFilter = bulan;
    notifyListeners();

    try {
      _detailTabungan = await _repository.getDetailTabungan(
        tabunganId,
        bulan: bulan,
      );
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  /// Cari rekening via barcode atau kata kunci
  Future<void> cariRekening(String query) async {
    final clean = query.trim();
    if (clean.isEmpty) {
      _searchResult = null;
      _searchQuery = '';
      notifyListeners();
      return;
    }

    _isLoading = true;
    _errorMessage = null;
    _searchQuery = clean;
    notifyListeners();

    try {
      _searchResult = await _repository.cariRekening(clean);
    } catch (e) {
      _searchResult = null;
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void clearSearch() {
    _searchResult = null;
    _searchQuery = '';
    _errorMessage = null;
    notifyListeners();
  }

  /// Eksekusi setor tunai
  Future<bool> setorTunai({
    required int tabunganId,
    required num nominal,
    String? tanggal,
    String? keterangan,
    int? ruanganId,
  }) async {
    _isSubmitting = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await _repository.setorTunai(
        tabunganId: tabunganId,
        nominal: nominal,
        tanggal: tanggal,
        keterangan: keterangan,
        ruanganId: ruanganId ?? _selectedRuanganId,
      );

      // Refresh data yang relevan
      await fetchTabunganRuangan(ruanganId: _selectedRuanganId);
      if (_detailTabungan != null &&
          _detailTabungan!.rekening.id == tabunganId) {
        await fetchDetailTabungan(tabunganId, bulan: _selectedBulanFilter);
      }

      _isSubmitting = false;
      notifyListeners();
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isSubmitting = false;
      notifyListeners();
      return false;
    }
  }
}
