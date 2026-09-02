import 'package:flutter/material.dart';
import '../core/utils/haptic_helper.dart';
import '../data/models/kas_ruangan_model.dart';
import '../data/repositories/kas_repository.dart';

class KasProvider extends ChangeNotifier {
  final KasRepository _repo = KasRepository();

  KasRingkasanModel? _ringkasan;
  List<MuridKasItem> _muridList = [];
  List<RiwayatBayarKasItem> _riwayatSantri = [];
  bool _isLoading = false;
  String? _errorMessage;

  KasRingkasanModel? get ringkasan => _ringkasan;
  List<MuridKasItem> get muridList => _muridList;
  List<RiwayatBayarKasItem> get riwayatSantri => _riwayatSantri;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> fetchRingkasan({int? ruanganId}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _ringkasan = await _repo.getRingkasan(ruanganId: ruanganId);
      if (_ringkasan != null) {
        _muridList = await _repo.getMuridList(_ringkasan!.ruanganId);
      }
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> bayarKas({
    required int muridId,
    required num jumlahBayar,
    required String tanggalBayar,
  }) async {
    if (_ringkasan == null) return false;
    try {
      final success = await _repo.simpanBayarKas(
        ruanganId: _ringkasan!.ruanganId,
        muridId: muridId,
        jumlahBayar: jumlahBayar,
        tanggalBayar: tanggalBayar,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchRingkasan(ruanganId: _ringkasan!.ruanganId);
      }
      return success;
    } catch (e) {
      HapticHelper.warning();
      return false;
    }
  }

  Future<bool> updateBayarKas({
    required int id,
    required int muridId,
    required num jumlahBayar,
    required String tanggalBayar,
  }) async {
    try {
      final success = await _repo.updateBayarKas(
        id: id,
        jumlahBayar: jumlahBayar,
        tanggalBayar: tanggalBayar,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchRiwayatMurid(muridId);
        if (_ringkasan != null) {
          await fetchRingkasan(ruanganId: _ringkasan!.ruanganId);
        }
      }
      return success;
    } catch (e) {
      HapticHelper.warning();
      return false;
    }
  }

  Future<bool> hapusBayarKas({required int id, required int muridId}) async {
    try {
      final success = await _repo.hapusBayarKas(id);
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchRiwayatMurid(muridId);
        if (_ringkasan != null) {
          await fetchRingkasan(ruanganId: _ringkasan!.ruanganId);
        }
      }
      return success;
    } catch (e) {
      HapticHelper.warning();
      return false;
    }
  }

  PengaturanKasItem? _pengaturan;
  PengaturanKasItem? get pengaturan => _pengaturan;

  Future<void> fetchPengaturan() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _pengaturan = await _repo.getPengaturan();
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> updatePengaturan({
    int? ruanganId,
    required int nominalLaki,
    required int nominalPerempuan,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final success = await _repo.updatePengaturan(
        ruanganId: ruanganId,
        nominalLaki: nominalLaki,
        nominalPerempuan: nominalPerempuan,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchPengaturan();
        await fetchRingkasan();
      }
      _isLoading = false;
      notifyListeners();
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> fetchRiwayatMurid(int muridId) async {
    _isLoading = true;
    notifyListeners();
    try {
      _riwayatSantri = await _repo.getRiwayatMurid(muridId);
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  RiwayatSetoranModel? _riwayatSetoran;
  RiwayatSetoranModel? get riwayatSetoran => _riwayatSetoran;

  List<PenerimaKasItem> _penerimaList = [];
  List<PenerimaKasItem> get penerimaList => _penerimaList;

  Future<void> fetchRiwayatSetoran({int? ruanganId}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _riwayatSetoran = await _repo.getRiwayatSetoran(ruanganId: ruanganId);
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchPenerimaList() async {
    try {
      _penerimaList = await _repo.getPenerimaList();
      notifyListeners();
    } catch (_) {}
  }

  Future<bool> simpanSetoran({
    required int ruanganId,
    required num jumlahSetor,
    required String tanggalSetor,
    int? penerimaId,
    String? keterangan,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final success = await _repo.simpanSetoran(
        ruanganId: ruanganId,
        jumlahSetor: jumlahSetor,
        tanggalSetor: tanggalSetor,
        penerimaId: penerimaId,
        keterangan: keterangan,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchRingkasan(ruanganId: ruanganId);
        await fetchRiwayatSetoran(ruanganId: ruanganId);
      }
      _isLoading = false;
      notifyListeners();
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateSetoran({
    required int id,
    required int ruanganId,
    required num jumlahSetor,
    required String tanggalSetor,
    int? penerimaId,
    String? keterangan,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final success = await _repo.updateSetoran(
        id: id,
        jumlahSetor: jumlahSetor,
        tanggalSetor: tanggalSetor,
        penerimaId: penerimaId,
        keterangan: keterangan,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchRingkasan(ruanganId: ruanganId);
        await fetchRiwayatSetoran(ruanganId: ruanganId);
      }
      _isLoading = false;
      notifyListeners();
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> hapusSetoran({required int id, required int ruanganId}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final success = await _repo.hapusSetoran(id);
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchRingkasan(ruanganId: ruanganId);
        await fetchRiwayatSetoran(ruanganId: ruanganId);
      }
      _isLoading = false;
      notifyListeners();
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  void reset() {
    _ringkasan = null;
    _muridList = [];
    _riwayatSantri = [];
    _riwayatSetoran = null;
    _penerimaList = [];
    _pengaturan = null;
    _isLoading = false;
    _errorMessage = null;
    notifyListeners();
  }
}
