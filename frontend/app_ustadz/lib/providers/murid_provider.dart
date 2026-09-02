import 'package:flutter/material.dart';
import '../data/models/murid_model.dart';
import '../data/repositories/murid_repository.dart';

class MuridProvider extends ChangeNotifier {
  final MuridRepository _repo = MuridRepository();

  List<MuridModel> _muridList = [];
  String _namaRuangan = 'Ruangan Binaan';
  bool _isLoading = false;
  String? _errorMessage;

  List<MuridModel> get muridList => _muridList;
  String get namaRuangan => _namaRuangan;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> fetchMuridRuangan({int? ruanganId}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final res = await _repo.getMuridRuangan(ruanganId: ruanganId);
      _namaRuangan = res.namaRuangan;
      _muridList = res.murids;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void reset() {
    _muridList = [];
    _namaRuangan = 'Ruangan Binaan';
    _isLoading = false;
    _errorMessage = null;
    notifyListeners();
  }
}
