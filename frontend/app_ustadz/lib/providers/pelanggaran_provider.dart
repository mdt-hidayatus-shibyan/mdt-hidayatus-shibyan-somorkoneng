import 'package:flutter/material.dart';
import '../core/utils/haptic_helper.dart';
import '../data/models/pelanggaran_model.dart';
import '../data/repositories/pelanggaran_repository.dart';

class PelanggaranProvider extends ChangeNotifier {
  final PelanggaranRepository _repo = PelanggaranRepository();

  List<ReferensiPelanggaranItem> _referensiList = [];
  List<RuanganPelanggaranItem> _ruanganList = [];
  List<MuridPelanggaranItem> _muridList = [];
  PelanggaranHarianData? _harianData;
  List<RiwayatPelanggaranItem> _riwayatList = [];

  bool _isLoading = false;
  bool _isLoadingMurid = false;
  String? _errorMessage;

  List<ReferensiPelanggaranItem> get referensiList => _referensiList;
  List<RuanganPelanggaranItem> get ruanganList => _ruanganList;
  List<MuridPelanggaranItem> get muridList => _muridList;
  PelanggaranHarianData? get harianData => _harianData;
  List<RiwayatPelanggaranItem> get riwayatList => _riwayatList;

  bool get isLoading => _isLoading;
  bool get isLoadingMurid => _isLoadingMurid;
  String? get errorMessage => _errorMessage;

  /// Find reference violation by ID number (Poin No ID)
  ReferensiPelanggaranItem? findReferensiById(int id) {
    try {
      return _referensiList.firstWhere((r) => r.id == id);
    } catch (_) {
      return null;
    }
  }

  /// Initial load for master reference, rooms, daily and history data
  Future<void> fetchAll() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final results = await Future.wait([
        _repo.getReferensi(),
        _repo.getRuanganList(),
        _repo.getHarian(),
        _repo.getRiwayat(),
      ]);

      _referensiList = results[0] as List<ReferensiPelanggaranItem>;
      _ruanganList = results[1] as List<RuanganPelanggaranItem>;
      _harianData = results[2] as PelanggaranHarianData;
      _riwayatList = results[3] as List<RiwayatPelanggaranItem>;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchHarian({String? tanggal, int? ruanganId}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _harianData = await _repo.getHarian(
        tanggal: tanggal,
        ruanganId: ruanganId,
      );
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchRiwayat({
    String? search,
    int? ruanganId,
    String? kategori,
    String? tanggalMulai,
    String? tanggalSelesai,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _riwayatList = await _repo.getRiwayat(
        search: search,
        ruanganId: ruanganId,
        kategori: kategori,
        tanggalMulai: tanggalMulai,
        tanggalSelesai: tanggalSelesai,
      );
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchMuridByRuangan(int ruanganId) async {
    _isLoadingMurid = true;
    _muridList = [];
    notifyListeners();

    try {
      _muridList = await _repo.getMuridByRuangan(ruanganId);
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingMurid = false;
      notifyListeners();
    }
  }

  Future<bool> simpanPelanggaran({
    required String tanggal,
    required int ruanganId,
    required int muridId,
    required int referensiId,
    String? keterangan,
  }) async {
    try {
      final success = await _repo.simpanPelanggaran(
        tanggal: tanggal,
        ruanganId: ruanganId,
        muridId: muridId,
        referensiId: referensiId,
        keterangan: keterangan,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await Future.wait([fetchHarian(), fetchRiwayat()]);
      }
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      HapticHelper.warning();
      return false;
    }
  }

  Future<bool> simpanPelanggaranMassal({
    required String tanggal,
    required int ruanganId,
    required List<int> muridIds,
    required int referensiId,
    String? keterangan,
  }) async {
    try {
      final success = await _repo.simpanPelanggaranMassal(
        tanggal: tanggal,
        ruanganId: ruanganId,
        muridIds: muridIds,
        referensiId: referensiId,
        keterangan: keterangan,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await Future.wait([fetchHarian(), fetchRiwayat()]);
      }
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      HapticHelper.warning();
      return false;
    }
  }

  Future<bool> hapusPelanggaran(int id) async {
    try {
      final success = await _repo.hapusPelanggaran(id);
      if (success) {
        HapticHelper.confirmSuccess();
        await Future.wait([fetchHarian(), fetchRiwayat()]);
      }
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      HapticHelper.warning();
      return false;
    }
  }

  void reset() {
    _referensiList = [];
    _ruanganList = [];
    _muridList = [];
    _harianData = null;
    _riwayatList = [];
    _isLoading = false;
    _isLoadingMurid = false;
    _errorMessage = null;
    notifyListeners();
  }
}
