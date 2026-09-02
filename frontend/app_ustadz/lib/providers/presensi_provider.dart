import 'package:flutter/material.dart';
import '../core/utils/date_helper.dart';
import '../core/utils/haptic_helper.dart';
import '../data/models/presensi_model.dart';
import '../data/repositories/presensi_repository.dart';

class PresensiProvider extends ChangeNotifier {
  final PresensiRepository _repo = PresensiRepository();

  // === 1. STATE PRESENSI MURID ===
  DateTime _selectedDate = DateTime.now();
  List<SesiPresensiItem> _sesiList = [];
  List<MuridPresensiItem> _muridList = [];
  bool _isLibur = false;
  String? _keteranganLibur;
  bool _isLoading = false;
  bool _isSaving = false;
  String? _errorMessage;

  DateTime get selectedDate => _selectedDate;
  List<SesiPresensiItem> get sesiList => _sesiList;
  List<MuridPresensiItem> get muridList => _muridList;
  bool get isLibur => _isLibur;
  String? get keteranganLibur => _keteranganLibur;
  bool get isLoading => _isLoading;
  bool get isSaving => _isSaving;
  String? get errorMessage => _errorMessage;

  // Live Summary Counters for Sticky Bottom Bar
  int get totalMurid => _muridList.length;
  int get countHadir => _muridList.where((m) => m.status == 'Hadir').length;
  int get countSakit => _muridList.where((m) => m.status == 'Sakit').length;
  int get countIzin => _muridList.where((m) => m.status == 'Izin').length;
  int get countAlpha => _muridList.where((m) => m.status == 'Alpha').length;
  int get countDispensasi =>
      _muridList.where((m) => m.status == 'Dispensasi').length;

  void setSelectedDate(DateTime date) {
    _selectedDate = date;
    fetchSesi();
  }

  Future<void> fetchSesi() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final dateStr = DateHelper.toYmd(_selectedDate);
      final response = await _repo.getSesiHarian(dateStr);
      _sesiList = response.sesiList;
      _isLibur = response.isLibur;
      _keteranganLibur = response.keteranganLibur;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchMurid(int jadwalId) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final dateStr = DateHelper.toYmd(_selectedDate);
      _muridList = await _repo.getMuridPerJadwal(jadwalId, dateStr);
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void updateMuridStatus(int muridId, String newStatus) {
    final index = _muridList.indexWhere((m) => m.muridId == muridId);
    if (index != -1) {
      _muridList[index].status = newStatus;
      HapticHelper.segmentTick();
      notifyListeners();
    }
  }

  // 1-Tap Quick Action: Set Semua Hadir
  void setSemuaHadir() {
    for (var murid in _muridList) {
      murid.status = 'Hadir';
    }
    HapticHelper.medium();
    notifyListeners();
  }

  Future<bool> simpanPresensi(int jadwalId) async {
    _isSaving = true;
    notifyListeners();

    try {
      final dateStr = DateHelper.toYmd(_selectedDate);
      final success = await _repo.simpanPresensiMassal(
        jadwalId,
        dateStr,
        _muridList,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchSesi(); // Refresh session badge
      }
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      HapticHelper.warning();
      return false;
    } finally {
      _isSaving = false;
      notifyListeners();
    }
  }

  // === 2. STATE PRESENSI USTADZ (CHECK-IN PER JADWAL) ===
  DateTime _selectedDateUstadz = DateTime.now();
  List<SesiPresensiUstadzItem> _sesiUstadzList = [];
  List<UstadzBadalItem> _daftarBadalList = [];
  RiwayatPresensiUstadz? _riwayatUstadz;
  bool _isLiburUstadz = false;
  String? _keteranganLiburUstadz;
  bool _isLoadingUstadz = false;
  bool _isCheckingInUstadz = false;

  DateTime get selectedDateUstadz => _selectedDateUstadz;
  List<SesiPresensiUstadzItem> get sesiUstadzList => _sesiUstadzList;
  List<UstadzBadalItem> get daftarBadalList => _daftarBadalList;
  RiwayatPresensiUstadz? get riwayatUstadz => _riwayatUstadz;
  bool get isLiburUstadz => _isLiburUstadz;
  String? get keteranganLiburUstadz => _keteranganLiburUstadz;
  bool get isLoadingUstadz => _isLoadingUstadz;
  bool get isCheckingInUstadz => _isCheckingInUstadz;

  void setSelectedDateUstadz(DateTime date) {
    _selectedDateUstadz = date;
    fetchSesiUstadz();
  }

  Future<void> fetchSesiUstadz() async {
    _isLoadingUstadz = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final dateStr = DateHelper.toYmd(_selectedDateUstadz);
      final response = await _repo.getSesiUstadzHarian(dateStr);
      _sesiUstadzList = response.sesiList;
      _isLiburUstadz = response.isLibur;
      _keteranganLiburUstadz = response.keteranganLibur;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
    } finally {
      _isLoadingUstadz = false;
      notifyListeners();
    }
  }

  Future<void> fetchDaftarBadal() async {
    try {
      _daftarBadalList = await _repo.getDaftarUstadzBadal();
      notifyListeners();
    } catch (_) {}
  }

  Future<bool> checkinUstadz({
    required int jadwalId,
    required String status,
    int? ustadzPenggantiId,
    String? keterangan,
  }) async {
    _isCheckingInUstadz = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final dateStr = DateHelper.toYmd(_selectedDateUstadz);
      final success = await _repo.checkinUstadz(
        jadwalId: jadwalId,
        tanggal: dateStr,
        status: status,
        ustadzPenggantiId: ustadzPenggantiId,
        keterangan: keterangan,
      );
      if (success) {
        HapticHelper.confirmSuccess();
        await fetchSesiUstadz();
        await fetchRiwayatUstadz();
      }
      return success;
    } catch (e) {
      _errorMessage = e.toString().replaceAll('Exception: ', '');
      HapticHelper.warning();
      return false;
    } finally {
      _isCheckingInUstadz = false;
      notifyListeners();
    }
  }

  Future<void> fetchRiwayatUstadz() async {
    try {
      _riwayatUstadz = await _repo.getRiwayatUstadz();
      notifyListeners();
    } catch (_) {}
  }

  void reset() {
    _selectedDate = DateTime.now();
    _sesiList = [];
    _muridList = [];
    _isLibur = false;
    _keteranganLibur = null;
    _isLoading = false;
    _isSaving = false;
    _errorMessage = null;

    _selectedDateUstadz = DateTime.now();
    _sesiUstadzList = [];
    _daftarBadalList = [];
    _riwayatUstadz = null;
    _isLiburUstadz = false;
    _keteranganLiburUstadz = null;
    _isLoadingUstadz = false;
    _isCheckingInUstadz = false;
    notifyListeners();
  }
}
