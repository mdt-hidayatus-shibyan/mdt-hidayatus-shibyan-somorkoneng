import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../providers/dashboard_provider.dart';
import '../../../providers/keuangan_provider.dart';
import '../../widgets/child_switcher_bar.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/modern_header.dart';
import '../../widgets/segmented_tab_bar.dart';

class KeuanganTab extends StatefulWidget {
  const KeuanganTab({super.key});

  @override
  State<KeuanganTab> createState() => _KeuanganTabState();
}

class _KeuanganTabState extends State<KeuanganTab>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {});
      }
    });

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final selectedAnak = context.read<DashboardProvider>().selectedAnak;
      if (selectedAnak != null) {
        context.read<KeuanganProvider>().fetchTagihan(selectedAnak.id);
      }
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final dashboard = context.watch<DashboardProvider>();
    final selectedAnak = dashboard.selectedAnak;
    final keuangan = context.watch<KeuanganProvider>();

    if (selectedAnak == null) {
      return Scaffold(
        body: const EmptyStateWidget(
          icon: Icons.child_care_rounded,
          title: 'Pilih Santri Terlebih Dahulu',
          subtitle: 'Silakan pilih profil anak pada menu Beranda.',
        ),
      );
    }

    return Scaffold(
      backgroundColor: isDark ? AppColors.surfaceDark : AppColors.surfaceLight,
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
              child: ModernHeader(
                title: 'Keuangan & SPP',
                subtitle: 'Monitoring SPP 11 Bulan & Tagihan Santri',
                icon: Icons.account_balance_wallet_rounded,
              ),
            ),

            // Multi-Child Switcher
            const ChildSwitcherBar(),

            // Segmented Navigation Pill
            SegmentedTabBar(
              controller: _tabController,
              tabs: const [
                SegmentedTabBarItem(
                  label: 'Kartu SPP (11 Bulan)',
                  icon: Icons.credit_card_rounded,
                ),
                SegmentedTabBarItem(
                  label: 'Tagihan Non-SPP',
                  icon: Icons.receipt_long_rounded,
                ),
              ],
            ),

            Expanded(
              child: keuangan.isLoading
                  ? Center(
                      child: CircularProgressIndicator(
                        color: isDark
                            ? AppColors.primaryDark
                            : AppColors.primaryLight,
                      ),
                    )
                  : RefreshIndicator(
                      onRefresh: () async {
                        HapticHelper.light();
                        await keuangan.fetchTagihan(
                          selectedAnak.id,
                          force: true,
                        );
                      },
                      color: isDark
                          ? AppColors.primaryDark
                          : AppColors.primaryLight,
                      child: TabBarView(
                        controller: _tabController,
                        physics: const BouncingScrollPhysics(),
                        children: [
                          _buildSppView(keuangan, isDark),
                          _buildNonSppView(keuangan, isDark),
                        ],
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSppView(KeuanganProvider keuangan, bool isDark) {
    final sppList = keuangan.sppList;
    final rekap = keuangan.rekapTagihan;

    if (sppList.isEmpty) {
      return const EmptyStateWidget(
        icon: Icons.credit_card_off_rounded,
        title: 'Belum Ada Tagihan SPP',
        subtitle: 'Data tagihan SPP bulanan santri belum diatur.',
      );
    }

    return ListView(
      physics: const AlwaysScrollableScrollPhysics(
        parent: BouncingScrollPhysics(),
      ),
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
      children: [
        // Ringkasan Keuangan Banner
        if (rekap != null) ...[
          GlassCard(
            padding: const EdgeInsets.all(16),
            borderRadius: 20,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildSummaryColumn(
                  'Total Target',
                  CurrencyFormatter.format(rekap.totalTagihan),
                  isDark ? Colors.white70 : Colors.black87,
                  isDark,
                ),
                Container(
                  height: 32,
                  width: 1,
                  color: isDark ? Colors.white12 : Colors.black12,
                ),
                _buildSummaryColumn(
                  'Total Terbayar',
                  CurrencyFormatter.format(rekap.totalLunas),
                  const Color(0xFF10B981),
                  isDark,
                ),
                Container(
                  height: 32,
                  width: 1,
                  color: isDark ? Colors.white12 : Colors.black12,
                ),
                _buildSummaryColumn(
                  'Tunggakan',
                  CurrencyFormatter.format(rekap.totalTunggakan),
                  rekap.totalTunggakan > 0
                      ? AppColors.roseDanger
                      : const Color(0xFF10B981),
                  isDark,
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),
        ],

        Text(
          'KARTU SPP SYAHRIYAH (11 BULAN HIJRIYAH)',
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w900,
            letterSpacing: 0.8,
            color: isDark ? Colors.white60 : Colors.black54,
          ),
        ),
        const SizedBox(height: 8),

        // List SPP 11 Bulan
        ...sppList.map((spp) {
          final isLunas = spp.isLunas;
          final isBebas = spp.isBebas;

          Color badgeBg = Colors.grey.withValues(alpha: 0.15);
          Color badgeColor = Colors.grey;

          if (isLunas) {
            badgeBg = const Color(0xFF10B981).withValues(alpha: 0.15);
            badgeColor = const Color(0xFF10B981);
          } else if (isBebas) {
            badgeBg = AppColors.skyBlueAccent.withValues(alpha: 0.15);
            badgeColor = AppColors.skyBlueAccent;
          } else {
            badgeBg = AppColors.amberAccent.withValues(alpha: 0.15);
            badgeColor = const Color(0xFFD97706);
          }

          return Padding(
            padding: const EdgeInsets.only(bottom: 8),
            child: GlassCard(
              padding: const EdgeInsets.all(16),
              borderRadius: 20,
              child: Row(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: isLunas
                          ? const Color(0xFF10B981).withValues(alpha: 0.12)
                          : (isDark
                                ? Colors.white.withValues(alpha: 0.05)
                                : Colors.black.withValues(alpha: 0.03)),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Icon(
                      isLunas
                          ? Icons.check_circle_rounded
                          : Icons.calendar_month_rounded,
                      color: isLunas
                          ? const Color(0xFF10B981)
                          : (isDark ? Colors.white60 : Colors.black54),
                      size: 22,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Bulan ${spp.bulan}',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: isDark ? Colors.white : Colors.black87,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          CurrencyFormatter.format(spp.nominal),
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w700,
                            color: isDark
                                ? AppColors.primaryDark
                                : AppColors.primaryLight,
                          ),
                        ),
                        if (spp.tanggalBayar != null) ...[
                          const SizedBox(height: 2),
                          Text(
                            'Dibayar: ${spp.tanggalBayar} • Kwitansi: ${spp.noTransaksi ?? "-"}',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.w500,
                              color: isDark ? Colors.white54 : Colors.black54,
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 5,
                    ),
                    decoration: BoxDecoration(
                      color: badgeBg,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      spp.statusBayar,
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w900,
                        color: badgeColor,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }

  Widget _buildNonSppView(KeuanganProvider keuangan, bool isDark) {
    final nonSppList = keuangan.nonSppList;

    if (nonSppList.isEmpty) {
      return const EmptyStateWidget(
        icon: Icons.receipt_long_rounded,
        title: 'Tidak Ada Tagihan Non-SPP',
        subtitle: 'Tidak ada tagihan berkala atau insidental untuk santri ini.',
      );
    }

    return ListView(
      physics: const AlwaysScrollableScrollPhysics(
        parent: BouncingScrollPhysics(),
      ),
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
      children: [
        Text(
          'TAGIHAN NON-SPP (UJIAN, KITAB, SERAGAM, DLL)',
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w900,
            letterSpacing: 0.8,
            color: isDark ? Colors.white60 : Colors.black54,
          ),
        ),
        const SizedBox(height: 8),

        ...nonSppList.map((tagihan) {
          final isLunas = tagihan.isLunas;

          return Padding(
            padding: const EdgeInsets.only(bottom: 8),
            child: GlassCard(
              padding: const EdgeInsets.all(16),
              borderRadius: 20,
              child: Row(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: isLunas
                          ? const Color(0xFF10B981).withValues(alpha: 0.12)
                          : (isDark
                                ? Colors.white.withValues(alpha: 0.05)
                                : Colors.black.withValues(alpha: 0.03)),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Icon(
                      isLunas
                          ? Icons.check_circle_rounded
                          : Icons.receipt_rounded,
                      color: isLunas
                          ? const Color(0xFF10B981)
                          : (isDark ? Colors.white60 : Colors.black54),
                      size: 22,
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          tagihan.namaTagihan,
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: isDark ? Colors.white : Colors.black87,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          CurrencyFormatter.format(tagihan.nominal),
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w700,
                            color: isDark
                                ? AppColors.primaryDark
                                : AppColors.primaryLight,
                          ),
                        ),
                        if (tagihan.tanggalBayar != null) ...[
                          const SizedBox(height: 2),
                          Text(
                            'Dibayar: ${tagihan.tanggalBayar} • No. Kwitansi: ${tagihan.noTransaksi ?? "-"}',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.w500,
                              color: isDark ? Colors.white54 : Colors.black54,
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 5,
                    ),
                    decoration: BoxDecoration(
                      color: isLunas
                          ? const Color(0xFF10B981).withValues(alpha: 0.15)
                          : AppColors.amberAccent.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      tagihan.statusBayar,
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w900,
                        color: isLunas
                            ? const Color(0xFF10B981)
                            : const Color(0xFFD97706),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        }),
      ],
    );
  }

  Widget _buildSummaryColumn(
    String title,
    String value,
    Color color,
    bool isDark,
  ) {
    return Column(
      children: [
        Text(
          title,
          style: TextStyle(
            fontSize: 10,
            fontWeight: FontWeight.w700,
            color: isDark ? Colors.white54 : Colors.black54,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w900,
            color: color,
          ),
        ),
      ],
    );
  }
}
