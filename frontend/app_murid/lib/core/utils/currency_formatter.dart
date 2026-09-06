import 'package:intl/intl.dart';

class CurrencyFormatter {
  CurrencyFormatter._();

  static final NumberFormat _formatter = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  static String format(dynamic number) {
    if (number == null) return 'Rp 0';
    if (number is String) {
      number = num.tryParse(number) ?? 0;
    }
    return _formatter.format(number);
  }
}
