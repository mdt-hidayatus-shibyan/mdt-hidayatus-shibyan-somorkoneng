class ApiEndpoints {
  ApiEndpoints._();

  // Auth & Session
  static const String loginWali = '/wali/login';
  static const String logout = '/logout';
  static const String profile = '/profile';

  // Wali Dashboard & Monitoring
  static const String dashboard = '/wali/dashboard';
  static const String detailAnak = '/wali/anak'; // + /{id}
  static const String tagihanAnak = '/wali/tagihan'; // + /{id}
  static const String presensiAnak = '/wali/presensi'; // + /{id}
  static const String pelanggaranAnak = '/wali/pelanggaran'; // + /{id}
  static const String nilaiAnak = '/wali/nilai'; // + /{id}
  static const String jadwalAnak = '/wali/jadwal'; // + /{id}
  static const String dokumenAnak = '/wali/dokumen'; // + /{id}

  // Umum / Bantuan
  static const String pengumuman = '/pengumuman';
  static const String bantuanKontak = '/bantuan/kontak';
  static const String kalendar = '/kalendar-pendidikan';
}
