import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/date_helper.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../providers/auth_provider.dart';
import '../../../providers/dashboard_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/schedule_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../akademik/jadwal_pelajaran_screen.dart';
import '../akademik/mata_pelajaran_screen.dart';
import '../kas/kas_ruangan_screen.dart';
import '../laporan/pusat_laporan_screen.dart';
import '../murid/direktori_murid_screen.dart';
import '../pelanggaran/referensi_pelanggaran_screen.dart';
import '../presensi/form_presensi_screen.dart';
import '../tagihan/tagihan_screen.dart';
import 'kalendar_screen.dart';
import 'pengumuman_screen.dart';

class HomeTab extends StatefulWidget {
  const HomeTab({super.key});

  @override
  State<HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends State<HomeTab> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DashboardProvider>().fetchDashboard();
    });
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final user = context.watch<AuthProvider>().user;
    final dashboard = context.watch<DashboardProvider>();

    return Scaffold(
      body: SafeArea(
        bottom: false,
        child: RefreshIndicator(
          onRefresh: () => context.read<DashboardProvider>().fetchDashboard(),
          color: isDark ? AppColors.primaryDark : AppColors.primaryLight,
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.fromLTRB(18, 12, 18, 100),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // 1. Header Bar: Profile, Salam, & Badges
                Row(
                  children: [
                    CircleAvatar(
                      radius: 24,
                      backgroundColor: isDark
                          ? const Color(0xFF0F2313)
                          : AppColors.primaryContainerLight,
                      child: Text(
                        (user?.name.isNotEmpty ?? false) ? user!.name[0] : 'U',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: isDark
                              ? AppColors.primaryDark
                              : AppColors.primaryLight,
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Assalamu\'alaikum,',
                            style: TextStyle(
                              fontSize: 12,
                              color: isDark
                                  ? const Color(0xFF8D9387)
                                  : const Color(0xFF73796E),
                            ),
                          ),
                          Text(
                            user?.name ?? 'Ust. Ahmad Fauzi, S.Pd.I',
                            style: const TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.bold,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),

                // Dual Badges: Tahun Ajaran & Status Wali
                Wrap(
                  spacing: 8,
                  runSpacing: 6,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: isDark ? const Color(0xFF101710) : Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: isDark
                              ? AppColors.outlineDark
                              : AppColors.outlineLight,
                        ),
                      ),
                      child: Text(
                        '🕌 ${user?.tahunPelajaran ?? "1447/1448 H • Ganjil"}',
                        style: const TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                    if (user?.isWaliRuangan ?? true)
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 4,
                        ),
                        decoration: BoxDecoration(
                          color: isDark
                              ? const Color(0xFF241538)
                              : const Color(0xFFF3E8FF),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: isDark
                                ? AppColors.violetAccent.withValues(alpha: 0.4)
                                : const Color(0xFFD8B4FE),
                          ),
                        ),
                        child: Text(
                          '⭐ Wali Ruangan: ${user?.ruanganWali ?? "2 B TPQ"}',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: isDark
                                ? AppColors.violetAccent
                                : const Color(0xFF6D28D9),
                          ),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 20),

                // 2. Quick Summary Banner Card
                GlassCard(
                  padding: const EdgeInsets.all(18),
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            DateHelper.formatIndonesian(DateTime.now()),
                            style: const TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 2,
                            ),
                            decoration: BoxDecoration(
                              color:
                                  (dashboard.dashboardData?.isLiburHariIni ??
                                      false)
                                  ? (isDark
                                        ? const Color(0xFF382305)
                                        : const Color(0xFFFEF3C7))
                                  : (isDark
                                        ? AppColors.primaryContainerDark
                                        : AppColors.primaryContainerLight),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              (dashboard.dashboardData?.isLiburHariIni ?? false)
                                  ? '🏖️ Hari Libur KBM'
                                  : 'Hari Aktif KBM',
                              style: TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                                color:
                                    (dashboard.dashboardData?.isLiburHariIni ??
                                        false)
                                    ? AppColors.amberAccent
                                    : (isDark
                                          ? AppColors.primaryDark
                                          : AppColors.primaryLight),
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          _buildStatItem(
                            'Jadwal Hari Ini',
                            '${dashboard.dashboardData?.jadwalHariIni ?? 2}',
                            isDark,
                          ),
                          Container(
                            height: 30,
                            width: 1,
                            color: isDark
                                ? AppColors.outlineDark
                                : AppColors.outlineLight,
                          ),
                          _buildStatItem(
                            'Sudah Absen',
                            '${dashboard.dashboardData?.presensiSelesaiHariIni ?? 1}',
                            isDark,
                          ),
                          Container(
                            height: 30,
                            width: 1,
                            color: isDark
                                ? AppColors.outlineDark
                                : AppColors.outlineLight,
                          ),
                          _buildStatItem(
                            'Murid Binaan',
                            '${dashboard.dashboardData?.totalMuridWali ?? 28}',
                            isDark,
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),

                // 3. Menu Cepat 4-Grid (Ustadz Umum & Wali Ruangan)
                const Text(
                  'Menu Cepat',
                  style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 10),
                Row(
                  children: [
                    _buildQuickAction(
                      Icons.event_note_rounded,
                      'Kalender',
                      isDark
                          ? const Color(0xFF0C243B)
                          : const Color(0xFFE0F2FE),
                      isDark
                          ? AppColors.skyBlueAccent
                          : const Color(0xFF0284C7),
                      () {
                        HapticHelper.light();
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const KalendarScreen(),
                          ),
                        );
                      },
                    ),
                    const SizedBox(width: 8),
                    _buildQuickAction(
                      Icons.gavel_rounded,
                      'Ref. Sanksi',
                      isDark
                          ? const Color(0xFF380C14)
                          : const Color(0xFFFFE4E6),
                      isDark ? const Color(0xFFF87171) : AppColors.roseDanger,
                      () {
                        HapticHelper.light();
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const ReferensiPelanggaranScreen(),
                          ),
                        );
                      },
                    ),
                    const SizedBox(width: 8),
                    _buildQuickAction(
                      Icons.menu_book_rounded,
                      'Mapel',
                      isDark
                          ? const Color(0xFF241538)
                          : const Color(0xFFF3E8FF),
                      AppColors.violetAccent,
                      () {
                        HapticHelper.light();
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const MataPelajaranScreen(),
                          ),
                        );
                      },
                    ),
                    const SizedBox(width: 8),
                    _buildQuickAction(
                      Icons.schedule_rounded,
                      'Jadwal',
                      isDark
                          ? const Color(0xFF0F2313)
                          : AppColors.primaryContainerLight,
                      isDark ? AppColors.primaryDark : AppColors.primaryLight,
                      () {
                        HapticHelper.light();
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => const JadwalPelajaranScreen(),
                          ),
                        );
                      },
                    ),
                  ],
                ),
                const SizedBox(height: 20),

                // Menu Tambahan Khusus Wali Ruangan
                if (user?.isWaliRuangan ?? true) ...[
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Wali Ruangan (${user?.ruanganWali ?? "Kelas Binaan"})',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 2,
                        ),
                        decoration: BoxDecoration(
                          color: isDark
                              ? const Color(0xFF241538)
                              : const Color(0xFFF3E8FF),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          'Akses Khusus',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: isDark
                                ? AppColors.violetAccent
                                : const Color(0xFF6D28D9),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      _buildQuickAction(
                        Icons.account_balance_wallet_rounded,
                        'Kas Kelas',
                        isDark
                            ? const Color(0xFF382305)
                            : const Color(0xFFFEF3C7),
                        isDark
                            ? AppColors.amberAccent
                            : const Color(0xFFD97706),
                        () {
                          HapticHelper.light();
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => const KasRuanganScreen(),
                            ),
                          );
                        },
                      ),
                      const SizedBox(width: 8),
                      _buildQuickAction(
                        Icons.receipt_long_rounded,
                        'Tagihan',
                        isDark
                            ? const Color(0xFF0C243B)
                            : const Color(0xFFE0F2FE),
                        isDark
                            ? AppColors.skyBlueAccent
                            : const Color(0xFF0284C7),
                        () {
                          HapticHelper.light();
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => TagihanScreen(
                                initialRuanganId: user?.ruanganWaliId,
                              ),
                            ),
                          );
                        },
                      ),
                      const SizedBox(width: 8),
                      _buildQuickAction(
                        Icons.people_alt_rounded,
                        'Murid Binaan',
                        isDark
                            ? const Color(0xFF182218)
                            : const Color(0xFFE8F5E9),
                        isDark ? AppColors.primaryDark : AppColors.primaryLight,
                        () {
                          HapticHelper.light();
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => DirektoriMuridScreen(
                                ruanganId: user?.ruanganWaliId,
                              ),
                            ),
                          );
                        },
                      ),
                      const SizedBox(width: 8),
                      _buildQuickAction(
                        Icons.assessment_rounded,
                        'Pusat Laporan',
                        isDark
                            ? const Color(0xFF0F2313)
                            : AppColors.primaryContainerLight,
                        isDark ? AppColors.primaryDark : AppColors.primaryLight,
                        () {
                          HapticHelper.light();
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => PusatLaporanScreen(
                                initialRuanganId: user?.ruanganWaliId,
                              ),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      _buildQuickAction(
                        Icons.school_rounded,
                        'Kenaikan Kelas',
                        isDark
                            ? const Color(0xFF241538)
                            : const Color(0xFFF3E8FF),
                        AppColors.violetAccent,
                        () {
                          HapticHelper.light();
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => PusatLaporanScreen(
                                initialRuanganId: user?.ruanganWaliId,
                                initialTabIndex: 4,
                              ),
                            ),
                          );
                        },
                      ),
                      const SizedBox(width: 8),
                      const Expanded(child: SizedBox()),
                      const SizedBox(width: 8),
                      const Expanded(child: SizedBox()),
                      const SizedBox(width: 8),
                      const Expanded(child: SizedBox()),
                    ],
                  ),
                  const SizedBox(height: 24),
                ],

                // 4. Pengumuman Madrasah
                if (dashboard.dashboardData?.pengumumanList.isNotEmpty ??
                    false) ...[
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Pengumuman Terkini',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      TextButton(
                        onPressed: () {
                          HapticHelper.light();
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => const PengumumanScreen(),
                            ),
                          );
                        },
                        child: const Text(
                          'Lihat Semua',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  ...dashboard.dashboardData!.pengumumanList.map(
                    (p) => GlassCard(
                      margin: const EdgeInsets.only(bottom: 10),
                      padding: const EdgeInsets.all(14),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: isDark
                                  ? const Color(0xFF3B1212)
                                  : const Color(0xFFFEE2E2),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: const Icon(
                              Icons.campaign_rounded,
                              size: 20,
                              color: AppColors.roseDanger,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  p.judul,
                                  style: const TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  p.konten,
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: isDark
                                        ? const Color(0xFF8D9387)
                                        : const Color(0xFF73796E),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                ],

                // 5. Jadwal Mengajar Hari Ini
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Jadwal Mengajar Hari Ini',
                      style: TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(
                      '${dashboard.dashboardData?.jadwalHariIniList.length ?? 0} Sesi',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: isDark
                            ? AppColors.primaryDark
                            : AppColors.primaryLight,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),

                if (dashboard.isLoading)
                  const ShimmerLoadingList(count: 2)
                else if (dashboard.dashboardData?.isLiburHariIni ?? false)
                  GlassCard(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 20,
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: isDark
                                ? const Color(0xFF382305)
                                : const Color(0xFFFEF3C7),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.beach_access_rounded,
                            size: 24,
                            color: AppColors.amberAccent,
                          ),
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Hari Ini Libur KBM',
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                  color: isDark
                                      ? Colors.white
                                      : const Color(0xFF92400E),
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                dashboard
                                        .dashboardData
                                        ?.keteranganLiburHariIni ??
                                    'Kegiatan Belajar Mengajar Diliburkan',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: isDark
                                      ? const Color(0xFF8D9387)
                                      : const Color(0xFF73796E),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  )
                else if (dashboard.dashboardData?.jadwalHariIniList.isEmpty ??
                    true)
                  const GlassCard(
                    padding: EdgeInsets.all(20),
                    child: Center(
                      child: Text(
                        'Tidak ada jadwal mengajar pada hari ini.',
                        style: TextStyle(fontSize: 13),
                      ),
                    ),
                  )
                else
                  ...dashboard.dashboardData!.jadwalHariIniList.map(
                    (j) => ScheduleCard(
                      item: j,
                      onAbsenTap: () {
                        HapticHelper.light();
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => FormPresensiScreen(
                              jadwalId: j.id,
                              mapel: j.mapel,
                              ruangan: j.kelas,
                              jam: j.jam,
                            ),
                          ),
                        );
                      },
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildStatItem(String label, String value, bool isDark) {
    return Expanded(
      child: Column(
        children: [
          Text(
            value,
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w900,
              color: isDark ? AppColors.primaryDark : AppColors.primaryLight,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w500,
              color: isDark ? const Color(0xFF8D9387) : const Color(0xFF73796E),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickAction(
    IconData icon,
    String label,
    Color bg,
    Color iconColor,
    VoidCallback onTap,
  ) {
    return Expanded(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(18),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 14),
          decoration: BoxDecoration(
            color: bg,
            borderRadius: BorderRadius.circular(18),
          ),
          child: Column(
            children: [
              Icon(icon, size: 24, color: iconColor),
              const SizedBox(height: 6),
              Text(
                label,
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  color: iconColor,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
