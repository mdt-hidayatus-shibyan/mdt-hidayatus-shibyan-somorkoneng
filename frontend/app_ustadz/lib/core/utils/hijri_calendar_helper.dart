import '../../data/models/akademik_model.dart';

class HijriMonthMeta {
  final int index;
  final String id;
  final String en;
  final String ar;

  const HijriMonthMeta({
    required this.index,
    required this.id,
    required this.en,
    required this.ar,
  });
}

class HijriCalendarHelper {
  static const List<String> pasarans = [
    'Legi',
    'Pahing',
    'Pon',
    'Wage',
    'Kliwon',
  ];

  static const List<String> hariNames = [
    'Senin',
    'Selasa',
    'Rabu',
    'Kamis',
    'Jum\'at',
    'Sabtu',
    'Ahad',
  ];

  static const List<String> bulanMasehiNames = [
    '',
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
  ];

  static const List<String> bulanMasehiShort = [
    '',
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'Mei',
    'Jun',
    'Jul',
    'Agt',
    'Sep',
    'Okt',
    'Nov',
    'Des',
  ];

  static const List<String> arabicDigits = [
    '٠',
    '١',
    '٢',
    '٣',
    '٤',
    '٥',
    '٦',
    '٧',
    '٨',
    '٩',
  ];

  static const List<HijriMonthMeta> hijriMonthsMeta = [
    HijriMonthMeta(index: 1, id: 'Muharram', en: 'Muḥarram', ar: 'مُحَرَّم'),
    HijriMonthMeta(index: 2, id: 'Safar', en: 'Ṣafar', ar: 'صَفَر'),
    HijriMonthMeta(
      index: 3,
      id: 'Rabiul Awal',
      en: 'Rabīʿ al-awwal',
      ar: 'رَبِيع ٱلْأَوَّل',
    ),
    HijriMonthMeta(
      index: 4,
      id: 'Rabiul Akhir',
      en: 'Rabīʿ al-thānī',
      ar: 'رَبِيع ٱلثَّانِي',
    ),
    HijriMonthMeta(
      index: 5,
      id: 'Jumadil Awal',
      en: 'Jumādá al-ūlá',
      ar: 'جُمَادَىٰ ٱلْأُولَىٰ',
    ),
    HijriMonthMeta(
      index: 6,
      id: 'Jumadil Akhir',
      en: 'Jumādá al-ākhirah',
      ar: 'جُمَادَىٰ ٱلْآخِرَة',
    ),
    HijriMonthMeta(index: 7, id: 'Rajab', en: 'Rajab', ar: 'رَجَب'),
    HijriMonthMeta(index: 8, id: 'Syaban', en: 'Shaʿbān', ar: 'شَعْبَان'),
    HijriMonthMeta(index: 9, id: 'Ramadhan', en: 'Ramaḍān', ar: 'رَمَضَان'),
    HijriMonthMeta(index: 10, id: 'Syawal', en: 'Shawwāl', ar: 'شَوَّال'),
    HijriMonthMeta(
      index: 11,
      id: 'Zulqa\'dah',
      en: 'Dhū al-Qaʿdah',
      ar: 'ذُو ٱلْقَعْدَة',
    ),
    HijriMonthMeta(
      index: 12,
      id: 'Zulhijjah',
      en: 'Dhū al-Ḥijjah',
      ar: 'ذُو ٱلْحِجَّة',
    ),
  ];

  /// Normalize and match Hijri month name to standard metadata
  static HijriMonthMeta getMonthMetaByName(String name) {
    final lower = name.toLowerCase().replaceAll(RegExp(r'[^a-z0-9]'), '');
    if (lower.contains('muhar')) {
      return hijriMonthsMeta[0]; // 1. Muharram
    }
    if (lower.contains('safar') ||
        lower.contains('shafar') ||
        lower.contains('saphar')) {
      return hijriMonthsMeta[1]; // 2. Safar
    }
    if (lower.contains('rabiulawal') ||
        lower.contains('rabiulawwal') ||
        lower.contains('rabiulula') ||
        (lower.contains('rabi') &&
            (lower.contains('awal') ||
                lower.contains('awwal') ||
                lower.contains('ula')))) {
      return hijriMonthsMeta[2]; // 3. Rabiul Awal
    }
    if (lower.contains('rabiulakhir') ||
        lower.contains('rabiultsani') ||
        lower.contains('rabiustsani') ||
        (lower.contains('rabi') &&
            (lower.contains('akhir') ||
                lower.contains('tsani') ||
                lower.contains('sani')))) {
      return hijriMonthsMeta[3]; // 4. Rabiul Akhir
    }
    if (lower.contains('jumadalula') ||
        lower.contains('jumadilawal') ||
        lower.contains('jumadilula') ||
        (lower.contains('jumad') &&
            (lower.contains('ula') ||
                lower.contains('awal') ||
                lower.contains('1')))) {
      return hijriMonthsMeta[4]; // 5. Jumadil Awal
    }
    if (lower.contains('jumadalakhir') ||
        lower.contains('jumadilakhir') ||
        (lower.contains('jumad') &&
            (lower.contains('akhir') ||
                lower.contains('2') ||
                lower.contains('tsani')))) {
      return hijriMonthsMeta[5]; // 6. Jumadil Akhir
    }
    if (lower.contains('rajab') || lower.contains('rejeb')) {
      return hijriMonthsMeta[6]; // 7. Rajab
    }
    if (lower.contains('syaban') ||
        lower.contains('shaban') ||
        lower.contains('syabaan')) {
      return hijriMonthsMeta[7]; // 8. Syaban
    }
    if (lower.contains('ramadhan') ||
        lower.contains('ramadan') ||
        lower.contains('romadhon')) {
      return hijriMonthsMeta[8]; // 9. Ramadhan
    }
    if (lower.contains('syawal') || lower.contains('shawwal')) {
      return hijriMonthsMeta[9]; // 10. Syawal
    }
    if (lower.contains('qadah') ||
        lower.contains('qaidah') ||
        lower.contains('qodah') ||
        lower.contains('qoidah') ||
        lower.contains('kaedah')) {
      return hijriMonthsMeta[10]; // 11. Zulqa'dah
    }
    if (lower.contains('hijjah') ||
        lower.contains('hijah') ||
        lower.contains('hajjah')) {
      return hijriMonthsMeta[11]; // 12. Zulhijjah
    }

    return hijriMonthsMeta[0];
  }

  /// Format number to Arabic numerals (e.g. 18 -> ١٨)
  static String toArabicDigits(int number) {
    final str = number.toString();
    final buffer = StringBuffer();
    for (int i = 0; i < str.length; i++) {
      final code = str.codeUnitAt(i) - 48;
      if (code >= 0 && code <= 9) {
        buffer.write(arabicDigits[code]);
      } else {
        buffer.write(str[i]);
      }
    }
    return buffer.toString();
  }

  /// Get Pasaran Jawa name (Legi, Pahing, Pon, Wage, Kliwon)
  static String getPasaran(DateTime date) {
    final d0 = DateTime.utc(1970, 1, 1);
    final target = DateTime.utc(date.year, date.month, date.day);
    final diffDays = target.difference(d0).inDays;
    final idx = (diffDays + 3) % 5;
    return pasarans[(idx >= 0) ? idx : (idx + 5) % 5];
  }

  /// Resolve Hijri Date from active database BulanHijriyah list
  static HijriDateResult getHijriDate(
    DateTime date,
    List<BulanHijriyahItem> listBulan,
  ) {
    final targetUtc = DateTime.utc(date.year, date.month, date.day);

    for (final b in listBulan) {
      if (b.tanggalMulai.isNotEmpty && b.tanggalSelesai.isNotEmpty) {
        final start = DateTime.tryParse(b.tanggalMulai);
        final end = DateTime.tryParse(b.tanggalSelesai);
        if (start != null && end != null) {
          final sUtc = DateTime.utc(start.year, start.month, start.day);
          final eUtc = DateTime.utc(end.year, end.month, end.day);

          if (!targetUtc.isBefore(sUtc) && !targetUtc.isAfter(eUtc)) {
            final day = targetUtc.difference(sUtc).inDays + 1;
            final meta = getMonthMetaByName(b.namaBulan);
            final hYear = int.tryParse(b.tahunHijriyah) ?? 1448;
            return HijriDateResult(
              day: day,
              monthName: meta.id,
              monthArabic: meta.ar,
              monthIndex: meta.index,
              year: hYear,
              arabicDay: toArabicDigits(day),
              arabicYear: toArabicDigits(hYear),
            );
          }
        }
      }
    }

    // Fallback algorithmic calculation with MABIMS +1 offset
    return _algorithmicHijri(date);
  }

  static HijriDateResult _algorithmicHijri(DateTime date) {
    int y = date.year;
    int m = date.month;
    int d = date.day;

    if (m <= 2) {
      y -= 1;
      m += 12;
    }

    final a = (y / 100).floor();
    final b = 2 - a + (a / 4).floor();
    final jd =
        (365.25 * (y + 4716)).floor() +
        (30.6001 * (m + 1)).floor() +
        d +
        b -
        1524.5;

    final z = jd + 0.5;
    final hijriDays = z - 1948439.5;
    final hYear = ((30 * hijriDays + 10646) / 10631).floor();
    final monthDays = hijriDays - ((354.367068 * (hYear - 1)).floor());
    final hMonth = ((monthDays + 0.5) / 29.5).floor() + 1;
    // Standard MABIMS Indonesia is +1 day relative to base unadjusted tabular
    final hDay = (monthDays - ((hMonth - 1) * 29.5)).floor() + 2;

    final safeMonth = (hMonth >= 1 && hMonth <= 12) ? hMonth : 1;
    final safeDay = (hDay >= 1 && hDay <= 30) ? hDay : (hDay > 30 ? 30 : 1);
    final safeYear = hYear > 0 ? hYear : 1448;

    final meta = hijriMonthsMeta[safeMonth - 1];

    return HijriDateResult(
      day: safeDay,
      monthName: meta.id,
      monthArabic: meta.ar,
      monthIndex: meta.index,
      year: safeYear,
      arabicDay: toArabicDigits(safeDay),
      arabicYear: toArabicDigits(safeYear),
    );
  }
}

class HijriDateResult {
  final int day;
  final String monthName;
  final String monthArabic;
  final int monthIndex;
  final int year;
  final String arabicDay;
  final String arabicYear;

  HijriDateResult({
    required this.day,
    required this.monthName,
    required this.monthArabic,
    required this.monthIndex,
    required this.year,
    required this.arabicDay,
    required this.arabicYear,
  });

  String get fullText => '$day $monthName $year H';
  String get fullArabicText => '$arabicDay $monthArabic $arabicYear هـ';
}
