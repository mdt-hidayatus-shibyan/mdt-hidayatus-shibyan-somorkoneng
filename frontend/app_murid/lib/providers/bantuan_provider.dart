import 'package:flutter/material.dart';
import '../data/repositories/wali_repository.dart';

class BantuanProvider extends ChangeNotifier {
  final WaliRepository _repo = WaliRepository();

  bool _isLoading = false;
  Map<String, dynamic>? _kontakData;

  bool get isLoading => _isLoading;
  Map<String, dynamic>? get kontakData => _kontakData;
  String get adminName => _kontakData?['nama_admin'] ?? 'Admin MDTHS';
  String get noWaClean => _kontakData?['no_wa_clean'] ?? '6281234567890';
  String get email =>
      _kontakData?['email'] ?? 'info@mdthidayatusshibyan.sch.id';
  String get alamat =>
      _kontakData?['alamat'] ?? 'Dsn. Somorkoneng, Kec. Kwanyar, Bangkalan';
  List<dynamic> get faqs => _kontakData?['faqs'] as List<dynamic>? ?? [];

  Future<void> fetchKontak() async {
    if (_kontakData != null) return;
    _isLoading = true;
    notifyListeners();

    try {
      _kontakData = await _repo.getBantuanKontak();
    } catch (_) {}

    _isLoading = false;
    notifyListeners();
  }
}
