import 'package:flutter/material.dart';
import '../data/models/laporan_model.dart';
import '../data/repositories/laporan_repository.dart';

class LaporanProvider extends ChangeNotifier {
  final LaporanRepository _repository = LaporanRepository();

  // State 1: Laporan Presensi Murid
  LaporanPresensiMuridModel? _presensiMurid;
  bool _isLoadingPresensiMurid = false;
  String? _errorPresensiMurid;

  LaporanPresensiMuridModel? get presensiMurid => _presensiMurid;
  bool get isLoadingPresensiMurid => _isLoadingPresensiMurid;
  String? get errorPresensiMurid => _errorPresensiMurid;

  // State 2: Laporan Presensi Ustadz
  LaporanPresensiUstadzModel? _presensiUstadz;
  bool _isLoadingPresensiUstadz = false;
  String? _errorPresensiUstadz;

  LaporanPresensiUstadzModel? get presensiUstadz => _presensiUstadz;
  bool get isLoadingPresensiUstadz => _isLoadingPresensiUstadz;
  String? get errorPresensiUstadz => _errorPresensiUstadz;

  // State 3: Laporan Pelanggaran Murid
  LaporanPelanggaranMuridModel? _pelanggaranMurid;
  bool _isLoadingPelanggaranMurid = false;
  String? _errorPelanggaranMurid;

  LaporanPelanggaranMuridModel? get pelanggaranMurid => _pelanggaranMurid;
  bool get isLoadingPelanggaranMurid => _isLoadingPelanggaranMurid;
  String? get errorPelanggaranMurid => _errorPelanggaranMurid;

  // State 4: Laporan Ujian
  LaporanUjianModel? _ujian;
  bool _isLoadingUjian = false;
  String? _errorUjian;

  LaporanUjianModel? get ujian => _ujian;
  bool get isLoadingUjian => _isLoadingUjian;
  String? get errorUjian => _errorUjian;

  // State 5: Laporan Kenaikan Kelas
  LaporanKenaikanKelasModel? _kenaikanKelas;
  bool _isLoadingKenaikanKelas = false;
  String? _errorKenaikanKelas;

  LaporanKenaikanKelasModel? get kenaikanKelas => _kenaikanKelas;
  bool get isLoadingKenaikanKelas => _isLoadingKenaikanKelas;
  String? get errorKenaikanKelas => _errorKenaikanKelas;

  // =========================================================================
  // ACTIONS
  // =========================================================================

  Future<void> fetchPresensiMurid({
    int? ruanganId,
    int? bulanHijriyahId,
    String? semester,
    String? startDate,
    String? endDate,
  }) async {
    _isLoadingPresensiMurid = true;
    _errorPresensiMurid = null;
    notifyListeners();

    try {
      _presensiMurid = await _repository.getLaporanPresensiMurid(
        ruanganId: ruanganId,
        bulanHijriyahId: bulanHijriyahId,
        semester: semester,
        startDate: startDate,
        endDate: endDate,
      );
    } catch (e) {
      _errorPresensiMurid = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingPresensiMurid = false;
      notifyListeners();
    }
  }

  Future<void> fetchPresensiUstadz({
    int? ustadzId,
    int? bulanHijriyahId,
    String? startDate,
    String? endDate,
  }) async {
    _isLoadingPresensiUstadz = true;
    _errorPresensiUstadz = null;
    notifyListeners();

    try {
      _presensiUstadz = await _repository.getLaporanPresensiUstadz(
        ustadzId: ustadzId,
        bulanHijriyahId: bulanHijriyahId,
        startDate: startDate,
        endDate: endDate,
      );
    } catch (e) {
      _errorPresensiUstadz = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingPresensiUstadz = false;
      notifyListeners();
    }
  }

  Future<void> fetchPelanggaranMurid({
    int? ruanganId,
    String? kategori,
    String? startDate,
    String? endDate,
  }) async {
    _isLoadingPelanggaranMurid = true;
    _errorPelanggaranMurid = null;
    notifyListeners();

    try {
      _pelanggaranMurid = await _repository.getLaporanPelanggaranMurid(
        ruanganId: ruanganId,
        kategori: kategori,
        startDate: startDate,
        endDate: endDate,
      );
    } catch (e) {
      _errorPelanggaranMurid = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingPelanggaranMurid = false;
      notifyListeners();
    }
  }

  Future<void> fetchUjian({int? ruanganId, int? ujianId}) async {
    _isLoadingUjian = true;
    _errorUjian = null;
    notifyListeners();

    try {
      _ujian = await _repository.getLaporanUjian(
        ruanganId: ruanganId,
        ujianId: ujianId,
      );
    } catch (e) {
      _errorUjian = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingUjian = false;
      notifyListeners();
    }
  }

  Future<void> fetchKenaikanKelas({int? ruanganId}) async {
    _isLoadingKenaikanKelas = true;
    _errorKenaikanKelas = null;
    notifyListeners();

    try {
      _kenaikanKelas = await _repository.getLaporanKenaikanKelas(
        ruanganId: ruanganId,
      );
    } catch (e) {
      _errorKenaikanKelas = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingKenaikanKelas = false;
      notifyListeners();
    }
  }
}
