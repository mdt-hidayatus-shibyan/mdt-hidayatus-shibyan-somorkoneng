import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/akademik_provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/dashboard_provider.dart';
import '../../providers/kas_provider.dart';
import '../../providers/murid_provider.dart';
import '../../providers/nilai_provider.dart';
import '../../providers/pelanggaran_provider.dart';
import '../../providers/presensi_provider.dart';
import '../../providers/presensi_ujian_provider.dart';

class SessionHelper {
  SessionHelper._();

  static void resetAllProviders(BuildContext context) {
    context.read<AuthProvider>().reset();
    context.read<DashboardProvider>().reset();
    context.read<PresensiProvider>().reset();
    context.read<PresensiUjianProvider>().reset();
    context.read<PelanggaranProvider>().reset();
    context.read<NilaiProvider>().reset();
    context.read<KasProvider>().reset();
    context.read<MuridProvider>().reset();
    context.read<AkademikProvider>().reset();
  }
}
