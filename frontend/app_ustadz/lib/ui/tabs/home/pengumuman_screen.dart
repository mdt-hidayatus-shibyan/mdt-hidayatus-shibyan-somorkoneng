import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../providers/dashboard_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/shimmer_loading.dart';

class PengumumanScreen extends StatefulWidget {
  const PengumumanScreen({super.key});

  @override
  State<PengumumanScreen> createState() => _PengumumanScreenState();
}

class _PengumumanScreenState extends State<PengumumanScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DashboardProvider>().fetchPengumuman();
    });
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final dashboard = context.watch<DashboardProvider>();

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Pengumuman Madrasah',
          style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: () => context.read<DashboardProvider>().fetchPengumuman(),
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            if (dashboard.isLoading)
              const ShimmerLoadingList(count: 3, height: 90)
            else if (dashboard.allPengumumanList.isEmpty)
              const GlassCard(
                padding: EdgeInsets.all(24),
                child: Center(child: Text('Tidak ada pengumuman saat ini.')),
              )
            else
              ...dashboard.allPengumumanList.map((p) {
                final isPenting = p.tipe.toLowerCase() == 'penting';

                return GlassCard(
                  margin: const EdgeInsets.only(bottom: 12),
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 3,
                            ),
                            decoration: BoxDecoration(
                              color: isPenting
                                  ? (isDark
                                        ? const Color(0xFF3B1212)
                                        : const Color(0xFFFEE2E2))
                                  : (isDark
                                        ? const Color(0xFF0F2313)
                                        : AppColors.primaryContainerLight),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              p.tipe,
                              style: TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                                color: isPenting
                                    ? AppColors.roseDanger
                                    : (isDark
                                          ? AppColors.primaryDark
                                          : AppColors.primaryLight),
                              ),
                            ),
                          ),
                          Text(
                            p.tanggalMulai,
                            style: TextStyle(
                              fontSize: 11,
                              color: isDark
                                  ? const Color(0xFF8D9387)
                                  : const Color(0xFF73796E),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 10),
                      Text(
                        p.judul,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        p.konten,
                        style: TextStyle(
                          fontSize: 13,
                          height: 1.4,
                          color: isDark
                              ? const Color(0xFFCCCCCC)
                              : const Color(0xFF4B5563),
                        ),
                      ),
                    ],
                  ),
                );
              }),
          ],
        ),
      ),
    );
  }
}
