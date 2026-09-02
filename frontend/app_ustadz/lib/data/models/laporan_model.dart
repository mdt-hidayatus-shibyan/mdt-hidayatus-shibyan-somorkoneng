import '../../core/network/api_client.dart';

class LaporanRuanganItem {
  final int id;
  final String namaRuangan;
  final String levelNama;

  LaporanRuanganItem({
    required this.id,
    required this.namaRuangan,
    this.levelNama = '-',
  });

  factory LaporanRuanganItem.fromJson(Map<String, dynamic> json) {
    return LaporanRuanganItem(
      id: json['id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
    );
  }
}

class BulanHijriyahLaporanItem {
  final int id;
  final String namaBulan;
  final String tahunHijriyah;
  final String semester;

  BulanHijriyahLaporanItem({
    required this.id,
    required this.namaBulan,
    this.tahunHijriyah = '',
    this.semester = '1',
  });

  factory BulanHijriyahLaporanItem.fromJson(Map<String, dynamic> json) {
    return BulanHijriyahLaporanItem(
      id: json['id'] ?? 0,
      namaBulan: json['nama_bulan'] ?? '',
      tahunHijriyah: json['tahun_hijriyah'] ?? '',
      semester: json['semester']?.toString() ?? '1',
    );
  }
}

// =========================================================================
// 1. MODEL LAPORAN PRESENSI MURID
// =========================================================================
class RekapPresensiMuridItem {
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final String wali;
  final int hadirCount;
  final int izinCount;
  final int sakitCount;
  final int alphaCount;
  final int totalPresensi;
  final double persentaseKehadiran;
  final String predikat;

  RekapPresensiMuridItem({
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    this.wali = '-',
    required this.hadirCount,
    required this.izinCount,
    required this.sakitCount,
    required this.alphaCount,
    required this.totalPresensi,
    required this.persentaseKehadiran,
    required this.predikat,
  });

  factory RekapPresensiMuridItem.fromJson(Map<String, dynamic> json) {
    return RekapPresensiMuridItem(
      muridId: json['murid_id'] ?? 0,
      nama: json['nama'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      wali: json['nama_wali'] ?? json['wali'] ?? '-',
      hadirCount: json['hadir_count'] ?? 0,
      izinCount: json['izin_count'] ?? 0,
      sakitCount: json['sakit_count'] ?? 0,
      alphaCount: json['alpha_count'] ?? 0,
      totalPresensi: json['total_presensi'] ?? 0,
      persentaseKehadiran: (json['persentase_kehadiran'] is num)
          ? (json['persentase_kehadiran'] as num).toDouble()
          : 0.0,
      predikat: json['predikat'] ?? 'Baik',
    );
  }
}

class LaporanPresensiMuridModel {
  final int ruanganId;
  final String namaRuangan;
  final String levelNama;
  final String tahunPelajaran;
  final int totalSantri;
  final int totalHariEfektif;
  final int totalHadir;
  final int totalIzin;
  final int totalSakit;
  final int totalAlpha;
  final double persentaseKehadiranKelas;
  final List<LaporanRuanganItem> ruanganList;
  final List<BulanHijriyahLaporanItem> bulanHijriyahList;
  final List<RekapPresensiMuridItem> rekapMurid;

  LaporanPresensiMuridModel({
    required this.ruanganId,
    required this.namaRuangan,
    this.levelNama = '-',
    required this.tahunPelajaran,
    required this.totalSantri,
    required this.totalHariEfektif,
    required this.totalHadir,
    required this.totalIzin,
    required this.totalSakit,
    required this.totalAlpha,
    required this.persentaseKehadiranKelas,
    this.ruanganList = const [],
    this.bulanHijriyahList = const [],
    this.rekapMurid = const [],
  });

  factory LaporanPresensiMuridModel.fromJson(Map<String, dynamic> json) {
    return LaporanPresensiMuridModel(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      tahunPelajaran: json['tahun_pelajaran'] ?? '-',
      totalSantri: json['total_santri'] ?? 0,
      totalHariEfektif: json['total_hari_efektif'] ?? 0,
      totalHadir: json['total_hadir'] ?? 0,
      totalIzin: json['total_izin'] ?? 0,
      totalSakit: json['total_sakit'] ?? 0,
      totalAlpha: json['total_alpha'] ?? 0,
      persentaseKehadiranKelas: (json['persentase_kehadiran_kelas'] is num)
          ? (json['persentase_kehadiran_kelas'] as num).toDouble()
          : 0.0,
      ruanganList: (json['ruangan_list'] as List? ?? [])
          .map((e) => LaporanRuanganItem.fromJson(e))
          .toList(),
      bulanHijriyahList: (json['bulan_hijriyah_list'] as List? ?? [])
          .map((e) => BulanHijriyahLaporanItem.fromJson(e))
          .toList(),
      rekapMurid: (json['rekap_murid'] as List? ?? [])
          .map((e) => RekapPresensiMuridItem.fromJson(e))
          .toList(),
    );
  }
}

// =========================================================================
// 2. MODEL LAPORAN PRESENSI USTADZ
// =========================================================================
class UstadzLaporanItem {
  final int id;
  final String nama;
  final String niup;
  final String? foto;

  UstadzLaporanItem({
    required this.id,
    required this.nama,
    this.niup = '-',
    this.foto,
  });

  factory UstadzLaporanItem.fromJson(Map<String, dynamic> json) {
    return UstadzLaporanItem(
      id: json['id'] ?? 0,
      nama: json['nama'] ?? json['nama_lengkap'] ?? '',
      niup: json['niup'] ?? '-',
      foto: ApiClient.resolveImageUrl(json['foto']),
    );
  }
}

class RiwayatPresensiUstadzItem {
  final int id;
  final String tanggal;
  final String? hariTanggal;
  final String status;
  final String jamMasuk;
  final String? jamKeluar;
  final String keterangan;
  final String? foto;

  RiwayatPresensiUstadzItem({
    required this.id,
    required this.tanggal,
    this.hariTanggal,
    required this.status,
    required this.jamMasuk,
    this.jamKeluar,
    this.keterangan = '-',
    this.foto,
  });

  factory RiwayatPresensiUstadzItem.fromJson(Map<String, dynamic> json) {
    return RiwayatPresensiUstadzItem(
      id: json['id'] ?? 0,
      tanggal: json['tanggal'] ?? '',
      hariTanggal: json['hari_tanggal'],
      status: json['status'] ?? 'Hadir',
      jamMasuk: json['jam_masuk'] ?? '-',
      jamKeluar: json['jam_keluar'],
      keterangan: json['keterangan'] ?? '-',
      foto: ApiClient.resolveImageUrl(json['foto']),
    );
  }
}

class LaporanPresensiUstadzModel {
  final UstadzLaporanItem ustadz;
  final String tahunPelajaran;
  final int totalSesi;
  final int totalHadir;
  final int totalTugas;
  final int totalIzin;
  final int totalSakit;
  final int totalAlpha;
  final double persentaseKehadiran;
  final List<UstadzLaporanItem> daftarUstadz;
  final List<BulanHijriyahLaporanItem> bulanHijriyahList;
  final List<RiwayatPresensiUstadzItem> riwayat;

  LaporanPresensiUstadzModel({
    required this.ustadz,
    required this.tahunPelajaran,
    required this.totalSesi,
    required this.totalHadir,
    required this.totalTugas,
    required this.totalIzin,
    required this.totalSakit,
    required this.totalAlpha,
    required this.persentaseKehadiran,
    this.daftarUstadz = const [],
    this.bulanHijriyahList = const [],
    this.riwayat = const [],
  });

  factory LaporanPresensiUstadzModel.fromJson(Map<String, dynamic> json) {
    return LaporanPresensiUstadzModel(
      ustadz: UstadzLaporanItem.fromJson(json['ustadz'] ?? {}),
      tahunPelajaran: json['tahun_pelajaran'] ?? '-',
      totalSesi: json['total_sesi'] ?? 0,
      totalHadir: json['total_hadir'] ?? 0,
      totalTugas: json['total_tugas'] ?? 0,
      totalIzin: json['total_izin'] ?? 0,
      totalSakit: json['total_sakit'] ?? 0,
      totalAlpha: json['total_alpha'] ?? 0,
      persentaseKehadiran: (json['persentase_kehadiran'] is num)
          ? (json['persentase_kehadiran'] as num).toDouble()
          : 0.0,
      daftarUstadz: (json['daftar_ustadz'] as List? ?? [])
          .map((e) => UstadzLaporanItem.fromJson(e))
          .toList(),
      bulanHijriyahList: (json['bulan_hijriyah_list'] as List? ?? [])
          .map((e) => BulanHijriyahLaporanItem.fromJson(e))
          .toList(),
      riwayat: (json['riwayat'] as List? ?? [])
          .map((e) => RiwayatPresensiUstadzItem.fromJson(e))
          .toList(),
    );
  }
}

// =========================================================================
// 3. MODEL LAPORAN PELANGGARAN SANTRI
// =========================================================================
class RekapPelanggaranMuridItem {
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final String wali;
  final int totalKasus;
  final double totalPoin;
  final String statusKedisiplinan;

  RekapPelanggaranMuridItem({
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    this.wali = '-',
    required this.totalKasus,
    required this.totalPoin,
    required this.statusKedisiplinan,
  });

  factory RekapPelanggaranMuridItem.fromJson(Map<String, dynamic> json) {
    return RekapPelanggaranMuridItem(
      muridId: json['murid_id'] ?? 0,
      nama: json['nama'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      wali: json['nama_wali'] ?? json['wali'] ?? '-',
      totalKasus: json['total_kasus'] ?? 0,
      totalPoin: (json['total_poin'] is num)
          ? (json['total_poin'] as num).toDouble()
          : 0.0,
      statusKedisiplinan: json['status_kedisiplinan'] ?? 'Disiplin',
    );
  }
}

class LogPelanggaranItem {
  final int id;
  final int muridId;
  final String namaSantri;
  final String nism;
  final String tanggal;
  final String? hariTanggal;
  final String namaPelanggaran;
  final String kategori;
  final double poin;
  final String keterangan;
  final String pencatat;

  LogPelanggaranItem({
    required this.id,
    required this.muridId,
    required this.namaSantri,
    required this.nism,
    required this.tanggal,
    this.hariTanggal,
    required this.namaPelanggaran,
    required this.kategori,
    required this.poin,
    this.keterangan = '-',
    this.pencatat = 'Ustadz',
  });

  factory LogPelanggaranItem.fromJson(Map<String, dynamic> json) {
    return LogPelanggaranItem(
      id: json['id'] ?? 0,
      muridId: json['murid_id'] ?? 0,
      namaSantri: json['nama_santri'] ?? '',
      nism: json['nism'] ?? '',
      tanggal: json['tanggal'] ?? '',
      hariTanggal: json['hari_tanggal'],
      namaPelanggaran: json['nama_pelanggaran'] ?? '-',
      kategori: json['kategori'] ?? 'Ringan',
      poin: (json['poin'] is num) ? (json['poin'] as num).toDouble() : 0.0,
      keterangan: json['keterangan'] ?? '-',
      pencatat: json['pencatat'] ?? 'Ustadz',
    );
  }
}

class LaporanPelanggaranMuridModel {
  final int ruanganId;
  final String namaRuangan;
  final String levelNama;
  final String tahunPelajaran;
  final int totalSantri;
  final int totalKasus;
  final double totalPoin;
  final int kasusSelesai;
  final int kasusDiproses;
  final int kasusRingan;
  final int kasusSedang;
  final int kasusBerat;
  final List<LaporanRuanganItem> ruanganList;
  final List<RekapPelanggaranMuridItem> rekapSantri;
  final List<LogPelanggaranItem> riwayatLog;

  LaporanPelanggaranMuridModel({
    required this.ruanganId,
    required this.namaRuangan,
    this.levelNama = '-',
    required this.tahunPelajaran,
    required this.totalSantri,
    required this.totalKasus,
    required this.totalPoin,
    required this.kasusSelesai,
    required this.kasusDiproses,
    required this.kasusRingan,
    required this.kasusSedang,
    required this.kasusBerat,
    this.ruanganList = const [],
    this.rekapSantri = const [],
    this.riwayatLog = const [],
  });

  factory LaporanPelanggaranMuridModel.fromJson(Map<String, dynamic> json) {
    return LaporanPelanggaranMuridModel(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      tahunPelajaran: json['tahun_pelajaran'] ?? '-',
      totalSantri: json['total_santri'] ?? 0,
      totalKasus: json['total_kasus'] ?? 0,
      totalPoin: (json['total_poin'] is num)
          ? (json['total_poin'] as num).toDouble()
          : 0.0,
      kasusSelesai: json['kasus_selesai'] ?? 0,
      kasusDiproses: json['kasus_diproses'] ?? 0,
      kasusRingan: json['kasus_ringan'] ?? 0,
      kasusSedang: json['kasus_sedang'] ?? 0,
      kasusBerat: json['kasus_berat'] ?? 0,
      ruanganList: (json['ruangan_list'] as List? ?? [])
          .map((e) => LaporanRuanganItem.fromJson(e))
          .toList(),
      rekapSantri: (json['rekap_santri'] as List? ?? [])
          .map((e) => RekapPelanggaranMuridItem.fromJson(e))
          .toList(),
      riwayatLog: (json['riwayat_log'] as List? ?? [])
          .map((e) => LogPelanggaranItem.fromJson(e))
          .toList(),
    );
  }
}

// =========================================================================
// 4. MODEL LAPORAN UJIAN
// =========================================================================
class MapelNilaiItem {
  final int mapelId;
  final String namaMapel;
  final double nilai;

  MapelNilaiItem({
    required this.mapelId,
    required this.namaMapel,
    required this.nilai,
  });

  factory MapelNilaiItem.fromJson(Map<String, dynamic> json) {
    return MapelNilaiItem(
      mapelId: json['mapel_id'] ?? 0,
      namaMapel: json['nama_mapel'] ?? '',
      nilai: (json['nilai'] is num) ? (json['nilai'] as num).toDouble() : 0.0,
    );
  }
}

class RekapUjianMuridItem {
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final String wali;
  final double totalNilai;
  final double rataRata;
  final int ranking;
  final String statusTuntas;
  final List<MapelNilaiItem> mapelNilai;

  RekapUjianMuridItem({
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    this.wali = '-',
    required this.totalNilai,
    required this.rataRata,
    this.ranking = 1,
    required this.statusTuntas,
    this.mapelNilai = const [],
  });

  factory RekapUjianMuridItem.fromJson(Map<String, dynamic> json) {
    return RekapUjianMuridItem(
      muridId: json['murid_id'] ?? 0,
      nama: json['nama'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      wali: json['nama_wali'] ?? json['wali'] ?? '-',
      totalNilai: (json['total_nilai'] is num)
          ? (json['total_nilai'] as num).toDouble()
          : 0.0,
      rataRata: (json['rata_rata'] is num)
          ? (json['rata_rata'] as num).toDouble()
          : 0.0,
      ranking: json['ranking'] ?? 1,
      statusTuntas: json['status_tuntas'] ?? 'Tuntas',
      mapelNilai: (json['mapel_nilai'] as List? ?? [])
          .map((e) => MapelNilaiItem.fromJson(e))
          .toList(),
    );
  }
}

class UjianOptionItem {
  final int id;
  final String namaUjian;
  final String tipeUjian;

  UjianOptionItem({
    required this.id,
    required this.namaUjian,
    required this.tipeUjian,
  });

  factory UjianOptionItem.fromJson(Map<String, dynamic> json) {
    return UjianOptionItem(
      id: json['id'] ?? 0,
      namaUjian: json['nama_ujian'] ?? '',
      tipeUjian: json['tipe_ujian'] ?? '',
    );
  }
}

class LaporanUjianModel {
  final int ruanganId;
  final String namaRuangan;
  final String levelNama;
  final UjianOptionItem ujian;
  final String tahunPelajaran;
  final int totalSantri;
  final double rataRataKelas;
  final double nilaiTertinggi;
  final double nilaiTerendah;
  final double persentaseTuntas;
  final int jumlahTuntas;
  final int jumlahBelumTuntas;
  final List<UjianOptionItem> daftarUjian;
  final List<LaporanRuanganItem> ruanganList;
  final List<RekapUjianMuridItem> rekapMurid;

  LaporanUjianModel({
    required this.ruanganId,
    required this.namaRuangan,
    this.levelNama = '-',
    required this.ujian,
    required this.tahunPelajaran,
    required this.totalSantri,
    required this.rataRataKelas,
    required this.nilaiTertinggi,
    required this.nilaiTerendah,
    required this.persentaseTuntas,
    required this.jumlahTuntas,
    required this.jumlahBelumTuntas,
    this.daftarUjian = const [],
    this.ruanganList = const [],
    this.rekapMurid = const [],
  });

  factory LaporanUjianModel.fromJson(Map<String, dynamic> json) {
    return LaporanUjianModel(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      ujian: UjianOptionItem.fromJson(json['ujian'] ?? {}),
      tahunPelajaran: json['tahun_pelajaran'] ?? '-',
      totalSantri: json['total_santri'] ?? 0,
      rataRataKelas: (json['rata_rata_kelas'] is num)
          ? (json['rata_rata_kelas'] as num).toDouble()
          : 0.0,
      nilaiTertinggi: (json['nilai_tertinggi'] is num)
          ? (json['nilai_tertinggi'] as num).toDouble()
          : 0.0,
      nilaiTerendah: (json['nilai_terendah'] is num)
          ? (json['nilai_terendah'] as num).toDouble()
          : 0.0,
      persentaseTuntas: (json['persentase_tuntas'] is num)
          ? (json['persentase_tuntas'] as num).toDouble()
          : 0.0,
      jumlahTuntas: json['jumlah_tuntas'] ?? 0,
      jumlahBelumTuntas: json['jumlah_belum_tuntas'] ?? 0,
      daftarUjian: (json['daftar_ujian'] as List? ?? [])
          .map((e) => UjianOptionItem.fromJson(e))
          .toList(),
      ruanganList: (json['ruangan_list'] as List? ?? [])
          .map((e) => LaporanRuanganItem.fromJson(e))
          .toList(),
      rekapMurid: (json['rekap_murid'] as List? ?? [])
          .map((e) => RekapUjianMuridItem.fromJson(e))
          .toList(),
    );
  }
}

// =========================================================================
// 5. MODEL LAPORAN KENAIKAN KELAS & KELULUSAN
// =========================================================================
class MuridKenaikanItem {
  final int muridId;
  final String nama;
  final String nism;
  final String jenisKelamin;
  final String? foto;
  final String wali;
  final double skorSem1;
  final double skorSem2;
  final double nilaiAkumulasi;
  final String rekomendasi;
  final String keputusanFinal;
  final String levelTujuanNama;
  final String catatan;
  final bool sudahDikunci;

  MuridKenaikanItem({
    required this.muridId,
    required this.nama,
    required this.nism,
    required this.jenisKelamin,
    this.foto,
    this.wali = '-',
    required this.skorSem1,
    required this.skorSem2,
    required this.nilaiAkumulasi,
    required this.rekomendasi,
    required this.keputusanFinal,
    this.levelTujuanNama = '-',
    this.catatan = '',
    this.sudahDikunci = false,
  });

  factory MuridKenaikanItem.fromJson(Map<String, dynamic> json) {
    return MuridKenaikanItem(
      muridId: json['murid_id'] ?? 0,
      nama: json['nama'] ?? '',
      nism: json['nism'] ?? '',
      jenisKelamin: json['jenis_kelamin'] ?? 'L',
      foto: ApiClient.resolveImageUrl(json['foto']),
      wali: json['nama_wali'] ?? json['wali'] ?? '-',
      skorSem1: (json['skor_sem1'] is num)
          ? (json['skor_sem1'] as num).toDouble()
          : 0.0,
      skorSem2: (json['skor_sem2'] is num)
          ? (json['skor_sem2'] as num).toDouble()
          : 0.0,
      nilaiAkumulasi: (json['nilai_akumulasi'] is num)
          ? (json['nilai_akumulasi'] as num).toDouble()
          : 0.0,
      rekomendasi: json['rekomendasi'] ?? 'Tinggal Kelas',
      keputusanFinal: json['keputusan_final'] ?? 'Tinggal Kelas',
      levelTujuanNama: json['level_tujuan_nama'] ?? '-',
      catatan: json['catatan'] ?? '',
      sudahDikunci: json['sudah_dikunci'] ?? false,
    );
  }
}

class LaporanKenaikanKelasModel {
  final int ruanganId;
  final String namaRuangan;
  final String levelNama;
  final bool isKelasAkhir;
  final String tahunPelajaran;
  final int totalSantri;
  final int totalNaikKelas;
  final int totalLulus;
  final int totalTinggalKelas;
  final List<LaporanRuanganItem> ruanganList;
  final List<MuridKenaikanItem> dataKenaikan;

  LaporanKenaikanKelasModel({
    required this.ruanganId,
    required this.namaRuangan,
    this.levelNama = '-',
    this.isKelasAkhir = false,
    required this.tahunPelajaran,
    required this.totalSantri,
    required this.totalNaikKelas,
    required this.totalLulus,
    required this.totalTinggalKelas,
    this.ruanganList = const [],
    this.dataKenaikan = const [],
  });

  factory LaporanKenaikanKelasModel.fromJson(Map<String, dynamic> json) {
    return LaporanKenaikanKelasModel(
      ruanganId: json['ruangan_id'] ?? 0,
      namaRuangan: json['nama_ruangan'] ?? '',
      levelNama: json['level_nama'] ?? '-',
      isKelasAkhir: json['is_kelas_akhir'] ?? false,
      tahunPelajaran: json['tahun_pelajaran'] ?? '-',
      totalSantri: json['total_santri'] ?? 0,
      totalNaikKelas: json['total_naik_kelas'] ?? 0,
      totalLulus: json['total_lulus'] ?? 0,
      totalTinggalKelas: json['total_tinggal_kelas'] ?? 0,
      ruanganList: (json['ruangan_list'] as List? ?? [])
          .map((e) => LaporanRuanganItem.fromJson(e))
          .toList(),
      dataKenaikan: (json['data_kenaikan'] as List? ?? [])
          .map((e) => MuridKenaikanItem.fromJson(e))
          .toList(),
    );
  }
}
