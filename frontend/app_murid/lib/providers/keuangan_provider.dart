import 'package:flutter/material.dart';
import '../data/models/tagihan_model.dart';
import '../data/repositories/wali_repository.dart';

class KeuanganProvider extends ChangeNotifier {
  final WaliRepository _repo = WaliRepository();

  bool _isLoading = false;
  String? _errorMessage;
  RekapTagihanAnakModel? _rekapTagihan;
  int? _loadedAnakId;

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  RekapTagihanAnakModel? get rekapTagihan => _rekapTagihan;
  List<ItemSppModel> get sppList => _rekapTagihan?.sppList ?? [];
  List<ItemNonSppModel> get nonSppList => _rekapTagihan?.nonSppList ?? [];

  Future<void> fetchTagihan(int anakId, {bool force = false}) async {
    if (!force && _loadedAnakId == anakId && _rekapTagihan != null) return;

    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _repo.getTagihanAnak(anakId);
      _rekapTagihan = res;
      _loadedAnakId = anakId;
    } catch (e) {
      _errorMessage = 'Gagal memuat tagihan: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
