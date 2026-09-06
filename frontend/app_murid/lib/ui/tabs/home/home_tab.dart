import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../providers/akademik_provider.dart';
import '../../../providers/dashboard_provider.dart';
import '../../../providers/keuangan_provider.dart';
import '../../../providers/presensi_provider.dart';
import '../../widgets/child_switcher_bar.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../akun/biodata_anak_screen.dart';
import '../akun/hubungi_admin_screen.dart';

class HomeTab extends StatelessWidget {
  final Function(int tabIndex)? onNavigateTab;

  const HomeTab({super.key, this.onNavigateTab});

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final dashboard = context.watch<DashboardProvider>();
    final data = dashboard.dashboardData;
    final selectedAnak = dashboard.selectedAnak;

    if (dashboard.isLoading && data == null) {
      return Center(
        child: CircularProgressIndicator(
          color: isDark ? AppColors.primaryDark : AppColors.primaryLight,
        ),
      );
    }

    if (data == null) {
      return Scaffold(
        body: EmptyStateWidget(
          icon: Icons.error_outline_rounded,
          title: 'Data Belum Tersedia',
          subtitle: dashboard.errorMessage ?? 'Gagal memuat data dashboard.',
          action: ElevatedButton.icon(
            onPressed: () => dashboard.fetchDashboard(),
            icon: const Icon(Icons.refresh_rounded),
            label: const Text('Muat Ulang'),
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: isDark ? AppColors.surfaceDark : AppColors.surfaceLight,
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            HapticHelper.light();
            await dashboard.fetchDashboard();
            if (!context.mounted) return;
            if (dashboard.selectedAnak != null) {
              final id = dashboard.selectedAnak!.id;
              context.read<KeuanganProvider>().fetchTagihan(id, force: true);
              context.read<PresensiProvider>().fetchPresensi(id, force: true);
              context.read<AkademikProvider>().fetchAkademik(id, force: true);
            }
          },
          color: isDark ? AppColors.primaryDark : AppColors.primaryLight,
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(
              parent: BouncingScrollPhysics(),
            ),
            padding: const EdgeInsets.only(bottom: 100),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Top Greeting & Header
                Padding(
                  padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            "Assalamu'alaikum,",
                            style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: isDark ? Colors.white60 : Colors.black54,
                            ),
                          ),
                          Text(
                            data.wali.namaKepalaKeluarga,
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w900,
                              letterSpacing: -0.3,
                              color: isDark ? Colors.white : Colors.black87,
                            ),
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: isDark
                              ? AppColors.primaryDark.withValues(alpha: 0.15)
                              : AppColors.primaryLight.withValues(alpha: 0.1),
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isDark
                                ? AppColors.primaryDark.withValues(alpha: 0.3)
                                : AppColors.primaryLight.withValues(alpha: 0.2),
                          ),
                        ),
                        child: Row(
                          children: [
                            Icon(
                              Icons.calendar_month_rounded,
                              size: 13,
                              color: isDark
                                  ? AppColors.primaryDark
                                  : AppColors.primaryLight,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              data.tahunHijriyah,
                              style: TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.w800,
                                color: isDark
                                    ? AppColors.primaryDark
                                    : AppColors.primaryLight,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),

                // Multi-Child Switcher
                const ChildSwitcherBar(),

                if (selectedAnak != null) ...[
                  // Kartu Profil Santri Terpilih
                  Padding(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 6,
                    ),
                    child: GlassCard(
                      padding: const EdgeInsets.all(18),
                      borderRadius: 24,
                      child: Column(
                        children: [
                          Row(
                            children: [
                              // Avatar / Foto Santri
                              Container(
                                width: 52,
                                height: 52,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: isDark
                                      ? AppColors.primaryDark.withValues(
                                          alpha: 0.2,
                                        )
                                      : AppColors.primaryLight.withValues(
                                          alpha: 0.1,
                                        ),
                                  border: Border.all(
                                    color: isDark
                                        ? AppColors.primaryDark
                                        : AppColors.primaryLight,
                                    width: 2,
                                  ),
                                ),
                                child: Center(
                                  child: Text(
                                    selectedAnak.namaLengkap.isNotEmpty
                                        ? selectedAnak.namaLengkap
                                              .substring(0, 1)
                                              .toUpperCase()
                                        : 'S',
                                    style: TextStyle(
                                      fontSize: 22,
                                      fontWeight: FontWeight.w900,
                                      color: isDark
                                          ? AppColors.primaryDark
                                          : AppColors.primaryLight,
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 14),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      selectedAnak.namaLengkap,
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.w900,
                                        letterSpacing: -0.3,
                                        color: isDark
                                            ? Colors.white
                                            : Colors.black87,
                                      ),
                                    ),
                                    const SizedBox(height: 2),
                                    Text(
                                      'Ruangan: ${selectedAnak.ruangan ?? "-"}',
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.w700,
                                        color: isDark
                                            ? AppColors.primaryDark
                                            : AppColors.primaryLight,
                                      ),
                                    ),
                                    Text(
                                      'NISM: ${selectedAnak.nism} • ${selectedAnak.kampung ?? "-"}',
                                      style: TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.w500,
                                        color: isDark
                                            ? Colors.white54
                                            : Colors.black54,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              // Tombol Detail Profil
                              IconButton(
                                onPressed: () {
                                  HapticHelper.light();
                                  Navigator.of(context).push(
                                    MaterialPageRoute(
                                      builder: (_) =>
                                          BiodataAnakScreen(anak: selectedAnak),
                                    ),
                                  );
                                },
                                icon: const Icon(
                                  Icons.arrow_forward_ios_rounded,
                                  size: 16,
                                ),
                                tooltip: 'Lihat Biodata',
                              ),
                            ],
                          ),
                          const SizedBox(height: 14),
                          const Divider(height: 1),
                          const SizedBox(height: 12),

                          // Status Kehadiran Hari Ini
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  Icon(
                                    Icons.access_time_filled_rounded,
                                    size: 15,
                                    color: isDark
                                        ? Colors.white60
                                        : Colors.black54,
                                  ),
                                  const SizedBox(width: 6),
                                  Text(
                                    'Status Hari Ini:',
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w600,
                                      color: isDark
                                          ? Colors.white70
                                          : Colors.black87,
                                    ),
                                  ),
                                ],
                              ),
                              _buildStatusBadge(
                                selectedAnak.statusHariIni,
                                isDark,
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),

                  // Ringkasan Keuangan Santri Card
                  Padding(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 6,
                    ),
                    child: GlassCard(
                      padding: const EdgeInsets.all(18),
                      borderRadius: 24,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  Icon(
                                    Icons.account_balance_wallet_rounded,
                                    size: 18,
                                    color: isDark
                                        ? AppColors.amberAccent
                                        : const Color(0xFFD97706),
                                  ),
                                  const SizedBox(width: 8),
                                  Text(
                                    'Ringkasan Keuangan',
                                    style: TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w900,
                                      color: isDark
                                          ? Colors.white
                                          : Colors.black87,
                                    ),
                                  ),
                                ],
                              ),
                              InkWell(
                                onTap: () => onNavigateTab?.call(1),
                                child: Text(
                                  'Lihat Semua >',
                                  style: TextStyle(
                                    fontSize: 11,
                                    fontWeight: FontWeight.w800,
                                    color: isDark
                                        ? AppColors.primaryDark
                                        : AppColors.primaryLight,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 14),
                          Row(
                            children: [
                              Expanded(
                                child: _buildFinanceMiniCard(
                                  title: 'Total Tagihan',
                                  amount: CurrencyFormatter.format(
                                    data.totalTagihan,
                                  ),
                                  color: isDark
                                      ? Colors.white70
                                      : Colors.black87,
                                  isDark: isDark,
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: _buildFinanceMiniCard(
                                  title: 'Lunas',
                                  amount: CurrencyFormatter.format(
                                    data.totalLunas,
                                  ),
                                  color: const Color(0xFF10B981),
                                  isDark: isDark,
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: _buildFinanceMiniCard(
                                  title: 'Tunggakan',
                                  amount: CurrencyFormatter.format(
                                    data.totalTunggakan,
                                  ),
                                  color: data.totalTunggakan > 0
                                      ? AppColors.roseDanger
                                      : const Color(0xFF10B981),
                                  isDark: isDark,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),

                  // Menu Pintasan / Quick Actions
                  Padding(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 10,
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'AKSES CEPAT',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 0.8,
                            color: isDark ? Colors.white60 : Colors.black54,
                          ),
                        ),
                        const SizedBox(height: 10),
                        Row(
                          children: [
                            Expanded(
                              child: _buildQuickActionBtn(
                                label: 'Kartu SPP',
                                icon: Icons.credit_card_rounded,
                                color: const Color(0xFF10B981),
                                onTap: () => onNavigateTab?.call(1),
                                isDark: isDark,
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: _buildQuickActionBtn(
                                label: 'Presensi',
                                icon: Icons.event_available_rounded,
                                color: AppColors.skyBlueAccent,
                                onTap: () => onNavigateTab?.call(2),
                                isDark: isDark,
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: _buildQuickActionBtn(
                                label: 'Rapor Nilai',
                                icon: Icons.auto_stories_rounded,
                                color: AppColors.violetAccent,
                                onTap: () => onNavigateTab?.call(3),
                                isDark: isDark,
                              ),
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: _buildQuickActionBtn(
                                label: 'Bantuan',
                                icon: Icons.headset_mic_rounded,
                                color: AppColors.amberAccent,
                                onTap: () {
                                  HapticHelper.light();
                                  Navigator.of(context).push(
                                    MaterialPageRoute(
                                      builder: (_) =>
                                          const HubungiAdminScreen(),
                                    ),
                                  );
                                },
                                isDark: isDark,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],

                // Pengumuman Madrasah
                if (dashboard.pengumumanList.isNotEmpty) ...[
                  Padding(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 10,
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'PENGUMUMAN MADRASAH',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 0.8,
                            color: isDark ? Colors.white60 : Colors.black54,
                          ),
                        ),
                        const SizedBox(height: 10),
                        ...dashboard.pengumumanList.map((p) {
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 8),
                            child: GlassCard(
                              padding: const EdgeInsets.all(16),
                              borderRadius: 20,
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 8,
                                          vertical: 3,
                                        ),
                                        decoration: BoxDecoration(
                                          color: AppColors.skyBlueAccent
                                              .withValues(alpha: 0.15),
                                          borderRadius: BorderRadius.circular(
                                            10,
                                          ),
                                        ),
                                        child: const Text(
                                          'Pengumuman',
                                          style: TextStyle(
                                            fontSize: 9,
                                            fontWeight: FontWeight.w900,
                                            color: AppColors.skyBlueAccent,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    p['judul']?.toString() ??
                                        'Pengumuman Resmi',
                                    style: TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w800,
                                      color: isDark
                                          ? Colors.white
                                          : Colors.black87,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    p['konten']?.toString() ?? '-',
                                    maxLines: 3,
                                    overflow: TextOverflow.ellipsis,
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w500,
                                      color: isDark
                                          ? Colors.white60
                                          : Colors.black54,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        }),
                      ],
                    ),
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildStatusBadge(String? status, bool isDark) {
    Color bg = Colors.grey.withValues(alpha: 0.15);
    Color text = Colors.grey;

    if (status == 'Hadir') {
      bg = const Color(0xFF10B981).withValues(alpha: 0.15);
      text = const Color(0xFF10B981);
    } else if (status == 'Izin') {
      bg = AppColors.skyBlueAccent.withValues(alpha: 0.15);
      text = AppColors.skyBlueAccent;
    } else if (status == 'Sakit') {
      bg = AppColors.amberAccent.withValues(alpha: 0.15);
      text = AppColors.amberAccent;
    } else if (status == 'Alpha') {
      bg = AppColors.roseDanger.withValues(alpha: 0.15);
      text = AppColors.roseDanger;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        status ?? 'Belum Ada Sesi',
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w800,
          color: text,
        ),
      ),
    );
  }

  Widget _buildFinanceMiniCard({
    required String title,
    required String amount,
    required Color color,
    required bool isDark,
  }) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: isDark
            ? Colors.white.withValues(alpha: 0.04)
            : Colors.black.withValues(alpha: 0.03),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
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
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              amount,
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w900,
                color: color,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActionBtn({
    required String label,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
    required bool isDark,
  }) {
    return InkWell(
      onTap: () {
        HapticHelper.light();
        onTap();
      },
      borderRadius: BorderRadius.circular(20),
      child: GlassCard(
        padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 8),
        borderRadius: 20,
        child: Column(
          children: [
            Container(
              width: 40,
              height: 40,
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(height: 8),
            Text(
              label,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w800,
                color: isDark ? Colors.white : Colors.black87,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
