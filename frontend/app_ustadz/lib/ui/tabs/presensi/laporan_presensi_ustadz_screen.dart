import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../providers/presensi_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';

class LaporanPresensiUstadzScreen extends StatefulWidget {
  const LaporanPresensiUstadzScreen({super.key});

  @override
  State<LaporanPresensiUstadzScreen> createState() =>
      _LaporanPresensiUstadzScreenState();
}

class _LaporanPresensiUstadzScreenState
    extends State<LaporanPresensiUstadzScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<PresensiProvider>().fetchRiwayatUstadz();
    });
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final presensi = context.watch<PresensiProvider>();

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Laporan Presensi Mengajar',
          style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: () => presensi.fetchRiwayatUstadz(),
        color: isDark ? AppColors.primaryDark : AppColors.primaryLight,
        child: ListView(
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 40),
          children: [
            // 1. Akumulasi Kehadiran Bulan Ini
            GlassCard(
              padding: const EdgeInsets.all(18),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Akumulasi Mengajar Bulan Ini',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Icon(
                        Icons.analytics_rounded,
                        size: 20,
                        color: isDark
                            ? AppColors.primaryDark
                            : AppColors.primaryLight,
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      _buildCountCol(
                        'Hadir',
                        '${presensi.riwayatUstadz?.totalHadir ?? 0}',
                        AppColors.hadirTextLight,
                      ),
                      _buildCountCol(
                        'Izin',
                        '${presensi.riwayatUstadz?.totalIzin ?? 0}',
                        AppColors.izinTextLight,
                      ),
                      _buildCountCol(
                        'Sakit',
                        '${presensi.riwayatUstadz?.totalSakit ?? 0}',
                        AppColors.sakitTextLight,
                      ),
                      _buildCountCol(
                        'Alpha',
                        '${presensi.riwayatUstadz?.totalAlpha ?? 0}',
                        AppColors.alphaTextLight,
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 22),

            // 2. Daftar Riwayat Kehadiran & Guru Badal
            const Text(
              'Riwayat Kehadiran & Badal Terkini',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),

            if (presensi.isLoading)
              const ShimmerLoadingList(count: 4)
            else if (presensi.riwayatUstadz?.riwayat.isEmpty ?? true)
              const GlassCard(
                padding: EdgeInsets.all(24),
                child: Center(
                  child: Text(
                    'Belum ada catatan riwayat mengajar.',
                    style: TextStyle(fontSize: 13),
                  ),
                ),
              )
            else
              ...presensi.riwayatUstadz!.riwayat.map(
                (r) => GlassCard(
                  margin: const EdgeInsets.only(bottom: 10),
                  padding: const EdgeInsets.all(14),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: r.status == 'Hadir'
                              ? (isDark
                                    ? AppColors.hadirBgDark
                                    : AppColors.hadirBgLight)
                              : (isDark
                                    ? AppColors.izinBgDark
                                    : AppColors.izinBgLight),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          r.status,
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: r.status == 'Hadir'
                                ? (isDark
                                      ? AppColors.hadirTextDark
                                      : AppColors.hadirTextLight)
                                : (isDark
                                      ? AppColors.izinTextDark
                                      : AppColors.izinTextLight),
                          ),
                        ),
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              r.mapel,
                              style: const TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              '${r.ruangan} • ${r.tanggal}',
                              style: TextStyle(
                                fontSize: 11,
                                color: isDark
                                    ? const Color(0xFF8D9387)
                                    : const Color(0xFF73796E),
                              ),
                            ),
                            if (r.ustadzPengganti != null) ...[
                              const SizedBox(height: 2),
                              Text(
                                'Badal: ${r.ustadzPengganti}',
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w600,
                                  color: isDark
                                      ? AppColors.skyBlueAccent
                                      : const Color(0xFF0284C7),
                                ),
                              ),
                            ],
                            if (r.keterangan != null &&
                                r.keterangan!.isNotEmpty) ...[
                              const SizedBox(height: 2),
                              Text(
                                'Catatan: ${r.keterangan}',
                                style: TextStyle(
                                  fontSize: 11,
                                  fontStyle: FontStyle.italic,
                                  color: isDark
                                      ? const Color(0xFF8D9387)
                                      : const Color(0xFF73796E),
                                ),
                              ),
                            ],
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildCountCol(String label, String count, Color color) {
    return Column(
      children: [
        Text(
          count,
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.w900,
            color: color,
          ),
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
        ),
      ],
    );
  }
}
