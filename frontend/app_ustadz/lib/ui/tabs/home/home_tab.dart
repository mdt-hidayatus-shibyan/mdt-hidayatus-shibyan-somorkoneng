import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/file_download_helper.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../data/models/dashboard_model.dart';
import '../../../providers/auth_provider.dart';
import '../../../providers/dashboard_provider.dart';
import '../../widgets/app_avatar.dart';
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
                    AppAvatar(
                      name: user?.name ?? 'Ustadz',
                      imageUrl: user?.photo,
                      radius: 24,
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
                    if (user?.isWaliRuangan ?? false)
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
                const SizedBox(height: 18),

                // 2. Jadwal Mengajar Hari Ini
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Icon(
                          Icons.event_available_rounded,
                          size: 19,
                          color: isDark
                              ? AppColors.primaryDark
                              : AppColors.primaryLight,
                        ),
                        const SizedBox(width: 6),
                        const Text(
                          'Jadwal Mengajar Hari Ini',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 3,
                      ),
                      decoration: BoxDecoration(
                        color:
                            (dashboard.dashboardData?.isLiburHariIni ?? false)
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
                            ? '🏖️ Libur KBM'
                            : '${dashboard.dashboardData?.jadwalHariIniList.length ?? 0} Sesi KBM',
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color:
                              (dashboard.dashboardData?.isLiburHariIni ?? false)
                              ? AppColors.amberAccent
                              : (isDark
                                    ? AppColors.primaryDark
                                    : AppColors.primaryLight),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),

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
                    padding: EdgeInsets.all(18),
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
                const SizedBox(height: 22),

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
                if (user?.isWaliRuangan ?? false) ...[
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
                        'Kas Ruangan',
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
                        'Anggota Murid',
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
                        'Laporan',
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
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Row(
                      children: [
                        Icon(
                          Icons.campaign_rounded,
                          size: 19,
                          color: AppColors.roseDanger,
                        ),
                        SizedBox(width: 6),
                        Text(
                          'Pengumuman Terkini',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
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
                const SizedBox(height: 6),
                if (dashboard.isLoading)
                  const ShimmerLoadingList(count: 2, height: 85)
                else if (dashboard.dashboardData?.pengumumanList.isEmpty ??
                    true)
                  GlassCard(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 18,
                    ),
                    child: Center(
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.info_outline_rounded,
                            size: 18,
                            color: isDark
                                ? const Color(0xFF8D9387)
                                : const Color(0xFF73796E),
                          ),
                          const SizedBox(width: 8),
                          Text(
                            'Belum ada pengumuman baru saat ini.',
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
                  )
                else
                  ...dashboard.dashboardData!.pengumumanList.map(
                    (p) => _buildPengumumanCard(context, p, isDark),
                  ),
                const SizedBox(height: 20),
              ],
            ),
          ),
        ),
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

  Widget _buildPengumumanCard(
    BuildContext context,
    PengumumanItem p,
    bool isDark,
  ) {
    final isPenting = p.tipe.toLowerCase() == 'penting';
    final isKegiatan = p.tipe.toLowerCase() == 'kegiatan';
    final isLibur = p.tipe.toLowerCase() == 'libur';

    final Color badgeBg;
    final Color badgeText;
    final IconData badgeIcon;

    if (isPenting) {
      badgeBg = isDark ? const Color(0xFF3B1212) : const Color(0xFFFEE2E2);
      badgeText = AppColors.roseDanger;
      badgeIcon = Icons.error_outline_rounded;
    } else if (isKegiatan) {
      badgeBg = isDark ? const Color(0xFF0F2313) : const Color(0xFFD1FAE5);
      badgeText = isDark ? AppColors.primaryDark : const Color(0xFF059669);
      badgeIcon = Icons.event_available_rounded;
    } else if (isLibur) {
      badgeBg = isDark ? const Color(0xFF382305) : const Color(0xFFFEF3C7);
      badgeText = AppColors.amberAccent;
      badgeIcon = Icons.beach_access_rounded;
    } else {
      badgeBg = isDark ? const Color(0xFF0C243B) : const Color(0xFFE0F2FE);
      badgeText = isDark ? AppColors.skyBlueAccent : const Color(0xFF0284C7);
      badgeIcon = Icons.info_outline_rounded;
    }

    final hasPdf = p.lampiranPdfUrl != null && p.lampiranPdfUrl!.isNotEmpty;

    return GlassCard(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      onTap: () {
        HapticHelper.light();
        _showPengumumanDetail(context, p, isDark);
      },
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 3,
                    ),
                    decoration: BoxDecoration(
                      color: badgeBg,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(badgeIcon, size: 12, color: badgeText),
                        const SizedBox(width: 4),
                        Text(
                          p.tipe,
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: badgeText,
                          ),
                        ),
                      ],
                    ),
                  ),
                  if (hasPdf) ...[
                    const SizedBox(width: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 7,
                        vertical: 3,
                      ),
                      decoration: BoxDecoration(
                        color: isDark
                            ? const Color(0xFF3B1212)
                            : const Color(0xFFFEE2E2),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(
                          color: AppColors.roseDanger.withValues(alpha: 0.3),
                        ),
                      ),
                      child: const Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(
                            Icons.picture_as_pdf_rounded,
                            size: 11,
                            color: AppColors.roseDanger,
                          ),
                          SizedBox(width: 3),
                          Text(
                            'PDF',
                            style: TextStyle(
                              fontSize: 9,
                              fontWeight: FontWeight.w900,
                              color: AppColors.roseDanger,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
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
          const SizedBox(height: 8),
          Text(
            p.judul,
            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 4),
          Text(
            p.konten,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
            style: TextStyle(
              fontSize: 12,
              height: 1.4,
              color: isDark ? const Color(0xFFCCCCCC) : const Color(0xFF4B5563),
            ),
          ),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              Text(
                'Baca Selengkapnya',
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  color: isDark
                      ? AppColors.primaryDark
                      : AppColors.primaryLight,
                ),
              ),
              const SizedBox(width: 2),
              Icon(
                Icons.chevron_right_rounded,
                size: 16,
                color: isDark ? AppColors.primaryDark : AppColors.primaryLight,
              ),
            ],
          ),
        ],
      ),
    );
  }

  void _showPengumumanDetail(
    BuildContext context,
    PengumumanItem p,
    bool isDark,
  ) {
    final hasPdf = p.lampiranPdfUrl != null && p.lampiranPdfUrl!.isNotEmpty;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return Container(
          constraints: BoxConstraints(
            maxHeight: MediaQuery.of(context).size.height * 0.85,
          ),
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF141914) : Colors.white,
            borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
            border: Border.all(
              color: isDark ? AppColors.outlineDark : AppColors.outlineLight,
            ),
          ),
          padding: const EdgeInsets.fromLTRB(20, 12, 20, 24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: isDark ? Colors.white24 : Colors.black12,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 9,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: isDark
                          ? AppColors.primaryContainerDark
                          : AppColors.primaryContainerLight,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      p.tipe,
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: isDark
                            ? AppColors.primaryDark
                            : AppColors.primaryLight,
                      ),
                    ),
                  ),
                  Text(
                    p.tanggalMulai,
                    style: TextStyle(
                      fontSize: 12,
                      color: isDark
                          ? const Color(0xFF8D9387)
                          : const Color(0xFF73796E),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Text(
                p.judul,
                style: const TextStyle(
                  fontSize: 17,
                  fontWeight: FontWeight.bold,
                  height: 1.3,
                ),
              ),
              const SizedBox(height: 12),
              Divider(
                color: isDark ? AppColors.outlineDark : AppColors.outlineLight,
              ),
              const SizedBox(height: 10),
              Flexible(
                child: SingleChildScrollView(
                  child: Text(
                    p.konten,
                    style: TextStyle(
                      fontSize: 14,
                      height: 1.6,
                      color: isDark
                          ? const Color(0xFFE4E4E7)
                          : const Color(0xFF27272A),
                    ),
                  ),
                ),
              ),
              if (hasPdf) ...[
                const SizedBox(height: 18),
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFE11D48),
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                      elevation: 0,
                    ),
                    onPressed: () {
                      HapticHelper.medium();
                      FileDownloadHelper.downloadAndOpen(
                        context,
                        url: p.lampiranPdfUrl!,
                        fileName: p.namaFilePdf ?? 'Pengumuman_${p.id}.pdf',
                      );
                    },
                    icon: const Icon(Icons.download_rounded, size: 20),
                    label: Text(
                      'Unduh & Buka PDF (${p.namaFilePdf ?? "Lampiran.pdf"})',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ],
            ],
          ),
        );
      },
    );
  }
}
