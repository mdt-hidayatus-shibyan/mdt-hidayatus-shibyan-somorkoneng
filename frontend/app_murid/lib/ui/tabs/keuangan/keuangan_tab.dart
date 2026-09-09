import 'package:flutter/material.dart';
import '../tagihan/tagihan_tab.dart';

export '../tagihan/tagihan_tab.dart';
export '../tabungan/tabungan_screen.dart';
export '../koperasi/koperasi_screen.dart';

/// Legacy alias for [TagihanTab] for backward compatibility
class KeuanganTab extends StatelessWidget {
  final int initialTabIndex;

  const KeuanganTab({super.key, this.initialTabIndex = 0});

  @override
  Widget build(BuildContext context) {
    return TagihanTab(initialTabIndex: initialTabIndex);
  }
}
