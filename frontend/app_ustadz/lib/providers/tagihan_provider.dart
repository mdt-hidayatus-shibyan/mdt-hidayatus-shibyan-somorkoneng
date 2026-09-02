import 'package:flutter/foundation.dart';
import '../data/models/tagihan_model.dart';
import '../data/repositories/tagihan_repository.dart';

class TagihanProvider extends ChangeNotifier {
  final TagihanRepository _repo = TagihanRepository();

  SppRingkasanModel? _ringkasan;
  List<MuridSppItem> _muridList = [];
  KartuSppModel? _kartuMurid;

  // Non-SPP State
  NonSppRingkasanModel? _nonSppRingkasan;
  List<MuridNonSppItem> _nonSppMuridList = [];
  bool _isLoadingNonSpp = false;

  bool _isLoading = false;
  bool _isLoadingKartu = false;
  String? _errorMessage;

  SppRingkasanModel? get ringkasan => _ringkasan;
  List<MuridSppItem> get muridList => _muridList;
  KartuSppModel? get kartuMurid => _kartuMurid;

  NonSppRingkasanModel? get nonSppRingkasan => _nonSppRingkasan;
  List<MuridNonSppItem> get nonSppMuridList => _nonSppMuridList;
  bool get isLoadingNonSpp => _isLoadingNonSpp;

  bool get isLoading => _isLoading;
  bool get isLoadingKartu => _isLoadingKartu;
  String? get errorMessage => _errorMessage;

  Future<void> fetchSppRingkasan({int? ruanganId}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _ringkasan = await _repo.getSppRingkasan(ruanganId: ruanganId);
      if (_ringkasan != null) {
        _muridList = await _repo.getSppMuridList(
          ruanganId: _ringkasan!.ruanganId,
        );
      }
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchSppMuridList({
    int? ruanganId,
    int? bulanHijriyahId,
    String? status,
    String? search,
  }) async {
    final targetRuanganId = ruanganId ?? _ringkasan?.ruanganId;
    if (targetRuanganId == null) return;

    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _muridList = await _repo.getSppMuridList(
        ruanganId: targetRuanganId,
        bulanHijriyahId: bulanHijriyahId,
        status: status,
        search: search,
      );
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchKartuSppMurid(int muridId, {int? ruanganId}) async {
    _isLoadingKartu = true;
    _kartuMurid = null;
    notifyListeners();

    try {
      _kartuMurid = await _repo.getKartuSppMurid(
        muridId,
        ruanganId: ruanganId ?? _ringkasan?.ruanganId,
      );
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingKartu = false;
      notifyListeners();
    }
  }

  // =========================================================================
  // NON-SPP METHODS
  // =========================================================================

  Future<void> fetchNonSppRingkasan({
    int? ruanganId,
    int? pengaturanTagihanId,
  }) async {
    _isLoadingNonSpp = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _nonSppRingkasan = await _repo.getNonSppRingkasan(
        ruanganId: ruanganId,
        pengaturanTagihanId: pengaturanTagihanId,
      );
      if (_nonSppRingkasan != null &&
          _nonSppRingkasan!.pengaturanTagihanId != null) {
        _nonSppMuridList = await _repo.getNonSppMuridList(
          ruanganId: _nonSppRingkasan!.ruanganId,
          pengaturanTagihanId: _nonSppRingkasan!.pengaturanTagihanId!,
        );
      }
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingNonSpp = false;
      notifyListeners();
    }
  }

  Future<void> fetchNonSppMuridList({
    int? ruanganId,
    int? pengaturanTagihanId,
    String? status,
    String? search,
  }) async {
    final targetRuanganId = ruanganId ?? _nonSppRingkasan?.ruanganId;
    final targetMasterId =
        pengaturanTagihanId ?? _nonSppRingkasan?.pengaturanTagihanId;

    if (targetRuanganId == null || targetMasterId == null) return;

    _isLoadingNonSpp = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _nonSppMuridList = await _repo.getNonSppMuridList(
        ruanganId: targetRuanganId,
        pengaturanTagihanId: targetMasterId,
        status: status,
        search: search,
      );
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingNonSpp = false;
      notifyListeners();
    }
  }

  Future<bool> bayarNonSpp({
    required List<int> tagihanIds,
    String? tipePembayar,
    String? metodePembayaran,
    String? tanggalBayar,
    String? catatan,
  }) async {
    _isLoadingNonSpp = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await _repo.prosesBayarNonSpp(
        tagihanIds: tagihanIds,
        tipePembayar: tipePembayar,
        metodePembayaran: metodePembayaran,
        tanggalBayar: tanggalBayar,
        catatan: catatan,
      );
      // Refresh ringkasan & list
      await fetchNonSppRingkasan(
        ruanganId: _nonSppRingkasan?.ruanganId,
        pengaturanTagihanId: _nonSppRingkasan?.pengaturanTagihanId,
      );
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoadingNonSpp = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> batalBayarNonSpp(int tagihanId) async {
    _isLoadingNonSpp = true;
    _errorMessage = null;
    notifyListeners();

    try {
      await _repo.batalBayarNonSpp(tagihanId);
      // Refresh ringkasan & list
      await fetchNonSppRingkasan(
        ruanganId: _nonSppRingkasan?.ruanganId,
        pengaturanTagihanId: _nonSppRingkasan?.pengaturanTagihanId,
      );
      return true;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoadingNonSpp = false;
      notifyListeners();
      return false;
    }
  }

  void reset() {
    _ringkasan = null;
    _muridList = [];
    _kartuMurid = null;
    _nonSppRingkasan = null;
    _nonSppMuridList = [];
    _isLoading = false;
    _isLoadingKartu = false;
    _isLoadingNonSpp = false;
    _errorMessage = null;
    notifyListeners();
  }
}
