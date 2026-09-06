import 'package:flutter/material.dart';
import '../data/models/dokumen_model.dart';
import '../data/models/jadwal_model.dart';
import '../data/models/nilai_model.dart';
import '../data/models/pelanggaran_model.dart';
import '../data/repositories/wali_repository.dart';

class AkademikProvider extends ChangeNotifier {
  final WaliRepository _repo = WaliRepository();

  bool _isLoading = false;
  String? _errorMessage;
  RekapNilaiAnakModel? _rekapNilai;
  List<JadwalItemModel> _jadwalList = [];
  RekapPelanggaranAnakModel? _rekapPelanggaran;
  DokumenGroupModel? _dokumen;
  int? _loadedAnakId;

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  RekapNilaiAnakModel? get rekapNilai => _rekapNilai;
  List<UjianRaporItem> get daftarUjian => _rekapNilai?.daftarUjian ?? [];
  List<JadwalItemModel> get jadwalList => _jadwalList;
  RekapPelanggaranAnakModel? get rekapPelanggaran => _rekapPelanggaran;
  DokumenGroupModel? get dokumen => _dokumen;
  List<DokumenItemModel> get raporArsipList => _dokumen?.raporList ?? [];
  List<DokumenItemModel> get skList => _dokumen?.skList ?? [];
  List<DokumenItemModel> get ijazahList => _dokumen?.ijazahList ?? [];
  bool get isKelasAkhir => _dokumen?.isKelasAkhir ?? false;

  Future<void> fetchAkademik(int anakId, {bool force = false}) async {
    if (!force &&
        _loadedAnakId == anakId &&
        _rekapNilai != null &&
        _dokumen != null)
      return;

    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final results = await Future.wait([
        _repo.getNilaiAnak(anakId),
        _repo.getJadwalAnak(anakId),
        _repo.getPelanggaranAnak(anakId),
        _repo.getDokumenAnak(anakId),
      ]);

      _rekapNilai = results[0] as RekapNilaiAnakModel?;
      _jadwalList = results[1] as List<JadwalItemModel>;
      _rekapPelanggaran = results[2] as RekapPelanggaranAnakModel?;
      _dokumen = results[3] as DokumenGroupModel?;
      _loadedAnakId = anakId;
    } catch (e) {
      _errorMessage = 'Gagal memuat data akademik: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
