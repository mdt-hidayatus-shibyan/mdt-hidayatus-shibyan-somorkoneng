import 'package:intl/intl.dart';

class DateFormatter {
  DateFormatter._();

  static String formatTanggal(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '-';
    try {
      final dt = DateTime.parse(dateStr);
      return DateFormat('d MMMM yyyy', 'id_ID').format(dt);
    } catch (_) {
      return dateStr;
    }
  }

  static String formatShort(String? dateStr) {
    if (dateStr == null || dateStr.isEmpty) return '-';
    try {
      final dt = DateTime.parse(dateStr);
      return DateFormat('dd/MM/yyyy').format(dt);
    } catch (_) {
      return dateStr;
    }
  }
}
