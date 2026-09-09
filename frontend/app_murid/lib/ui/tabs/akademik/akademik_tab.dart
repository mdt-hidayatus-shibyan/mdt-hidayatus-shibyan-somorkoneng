import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../data/models/dokumen_model.dart';
import '../../../providers/akademik_provider.dart';
import '../../../providers/dashboard_provider.dart';
import '../../widgets/child_switcher_bar.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/modern_header.dart';
import '../../widgets/segmented_tab_bar.dart';

class AkademikTab extends StatefulWidget {
  const AkademikTab({super.key});

  @override
  State<AkademikTab> createState() => _AkademikTabState();
}

class _AkademikTabState extends State<AkademikTab>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {});
      }
    });

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final selectedAnak = context.read<DashboardProvider>().selectedAnak;
      if (selectedAnak != null) {
        context.read<AkademikProvider>().fetchAkademik(selectedAnak.id);
      }
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _openUrl(String urlString, BuildContext context) async {
    if (urlString.isEmpty) return;
    HapticHelper.light();
    final uri = Uri.parse(urlString);
    try {
      if (!await launchUrl(uri, mode: LaunchMode.externalApplication)) {
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Tidak dapat membuka tautan dokumen.'),
              backgroundColor: AppColors.roseDanger,
            ),
          );
        }
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Gagal membuka dokumen: $e'),
            backgroundColor: AppColors.roseDanger,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final dashboard = context.watch<DashboardProvider>();
    final selectedAnak = dashboard.selectedAnak;
    final akademik = context.watch<AkademikProvider>();

    if (selectedAnak == null) {
      return Scaffold(
        body: const EmptyStateWidget(
          icon: Icons.family_restroom_rounded,
          title: 'Pilih Murid Terlebih Dahulu',
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
                title: 'Akademik & Rapor',
                subtitle: 'Rapor Ujian, Info Kenaikan, SK & Ijazah Murid',
                icon: Icons.auto_stories_rounded,
              ),
            ),

            // Multi-Child Switcher
            const ChildSwitcherBar(),

            // Segmented Navigation Pill (3 Tabs)
            SegmentedTabBar(
              controller: _tabController,
              tabs: const [
                SegmentedTabBarItem(
                  label: 'Rapor Ujian',
                  icon: Icons.menu_book_rounded,
                ),
                SegmentedTabBarItem(
                  label: 'Kenaikan Kelas',
                  icon: Icons.school_rounded,
                ),
                SegmentedTabBarItem(
                  label: 'SK & Ijazah',
                  icon: Icons.workspace_premium_rounded,
                ),
              ],
            ),

            Expanded(
              child: akademik.isLoading
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
                        await akademik.fetchAkademik(
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
                          _buildRaporTab(akademik, isDark),
                          _buildKenaikanKelasTab(akademik, isDark),
                          _buildDokumenIjazahTab(akademik, isDark),
                        ],
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  // =========================================================================
  // TAB 1: RAPOR UJIAN (ARSIP RESMI + RINCIAN NILAI)
  // =========================================================================
  Widget _buildRaporTab(AkademikProvider akademik, bool isDark) {
    final raporArsip = akademik.raporArsipList;
    final ujianList = akademik.daftarUjian;

    if (raporArsip.isEmpty && ujianList.isEmpty) {
      return const EmptyStateWidget(
        icon: Icons.menu_book_rounded,
        title: 'Belum Ada Rapor Terbit',
        subtitle:
            'Rapor ujian IMDA 1 & IMDA 2 / IMNI akan tampil setelah disahkan.',
      );
    }

    return ListView(
      physics: const AlwaysScrollableScrollPhysics(
        parent: BouncingScrollPhysics(),
      ),
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
      children: [
        // 1. DOKUMEN ARSIP RAPOR (SIAP UNDUH & CETAK)
        if (raporArsip.isNotEmpty) ...[
          Text(
            'ARSIP RAPOR RESMI (UNDUH & CETAK PDF)',
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w900,
              letterSpacing: 0.8,
              color: isDark ? Colors.white60 : Colors.black54,
            ),
          ),
          const SizedBox(height: 8),

          ...raporArsip.map((r) => _buildArsipRaporCard(r, isDark)),
          const SizedBox(height: 16),
        ],

        // 2. RINCIAN LEGER NILAI PER MAPEL
        if (ujianList.isNotEmpty) ...[
          Text(
            'RINCIAN NILAI UJIAN MURID',
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w900,
              letterSpacing: 0.8,
              color: isDark ? Colors.white60 : Colors.black54,
            ),
          ),
          const SizedBox(height: 8),

          ...ujianList.map((ujian) => _buildNilaiCard(ujian, isDark)),
        ],
      ],
    );
  }

  Widget _buildArsipRaporCard(DokumenItemModel r, bool isDark) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: GlassCard(
        padding: const EdgeInsets.all(16),
        borderRadius: 22,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    color: isDark
                        ? AppColors.primaryDark.withValues(alpha: 0.15)
                        : AppColors.primaryLight.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Center(
                    child: Icon(
                      Icons.picture_as_pdf_rounded,
                      color: isDark
                          ? AppColors.primaryDark
                          : AppColors.primaryLight,
                      size: 24,
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        r.namaDokumen,
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.3,
                          color: isDark ? Colors.white : Colors.black87,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'No: ${r.nomorDokumen} • ${r.tahunPelajaran}',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: isDark ? Colors.white54 : Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ),
                if (r.rataRata > 0)
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: const Color(0xFF10B981).withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      'Rata: ${r.rataRata}',
                      style: const TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w900,
                        color: Color(0xFF10B981),
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 12),
            const Divider(height: 1),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => _openUrl(r.cetakUrl, context),
                    icon: const Icon(Icons.visibility_rounded, size: 16),
                    label: const Text(
                      'Lihat / Print',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () => _openUrl(r.downloadUrl, context),
                    icon: const Icon(Icons.download_rounded, size: 16),
                    label: const Text(
                      'Unduh PDF',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: isDark
                          ? AppColors.primaryDark
                          : AppColors.primaryLight,
                      foregroundColor: isDark ? Colors.black : Colors.white,
                      elevation: 0,
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNilaiCard(dynamic ujian, bool isDark) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: GlassCard(
        padding: const EdgeInsets.all(18),
        borderRadius: 24,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        ujian.namaUjian,
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w900,
                          color: isDark ? Colors.white : Colors.black87,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        '${ujian.semester} • Ruangan: ${ujian.ruangan}',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: isDark ? Colors.white54 : Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 5,
                  ),
                  decoration: BoxDecoration(
                    color: AppColors.violetAccent.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    ujian.tipeUjian,
                    style: const TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w900,
                      color: AppColors.violetAccent,
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 14),
            const Divider(height: 1),
            const SizedBox(height: 12),

            // Ringkasan Nilai
            Row(
              children: [
                Expanded(
                  child: _buildMiniStat(
                    'Total Nilai',
                    '${ujian.totalNilai}',
                    isDark ? Colors.white70 : Colors.black87,
                    isDark,
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _buildMiniStat(
                    'Rata-rata',
                    '${ujian.rataRata}',
                    const Color(0xFF10B981),
                    isDark,
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: _buildMiniStat(
                    'Total Mapel',
                    '${ujian.totalMapel} Mapel',
                    isDark ? AppColors.primaryDark : AppColors.primaryLight,
                    isDark,
                  ),
                ),
              ],
            ),

            const SizedBox(height: 14),

            Text(
              'DAFTAR NILAI MATA PELAJARAN',
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w900,
                letterSpacing: 0.8,
                color: isDark ? Colors.white60 : Colors.black54,
              ),
            ),
            const SizedBox(height: 8),

            // Table Nilai Mapel
            ...ujian.daftarNilai.map((n) {
              return Container(
                margin: const EdgeInsets.only(bottom: 6),
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 10,
                ),
                decoration: BoxDecoration(
                  color: isDark
                      ? Colors.white.withValues(alpha: 0.03)
                      : Colors.black.withValues(alpha: 0.02),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            n.mapel,
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.w800,
                              color: isDark ? Colors.white : Colors.black87,
                            ),
                          ),
                          Text(
                            'KKM: ${n.kkm}',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.w500,
                              color: isDark ? Colors.white54 : Colors.black54,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Row(
                      children: [
                        Text(
                          '${n.nilaiAngka.toInt()}',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w900,
                            color: n.isLulus
                                ? (isDark ? Colors.white : Colors.black87)
                                : AppColors.roseDanger,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Container(
                          width: 28,
                          height: 28,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: n.isLulus
                                ? const Color(
                                    0xFF10B981,
                                  ).withValues(alpha: 0.15)
                                : AppColors.roseDanger.withValues(alpha: 0.15),
                          ),
                          child: Center(
                            child: Text(
                              n.nilaiHuruf,
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w900,
                                color: n.isLulus
                                    ? const Color(0xFF10B981)
                                    : AppColors.roseDanger,
                              ),
                            ),
                          ),
                        ),
                      ],
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

  // =========================================================================
  // TAB 2: INFORMASI KENAIKAN KELAS & KELULUSAN (HASIL PLENO SK RESMI)
  // =========================================================================
  Widget _buildKenaikanKelasTab(AkademikProvider akademik, bool isDark) {
    final kenaikan = akademik.kenaikan;

    if (kenaikan == null) {
      return const EmptyStateWidget(
        icon: Icons.school_rounded,
        title: 'Data Belum Tersedia',
        subtitle: 'Informasi kenaikan kelas murid belum dapat dimuat.',
      );
    }

    return ListView(
      physics: const AlwaysScrollableScrollPhysics(
        parent: BouncingScrollPhysics(),
      ),
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
      children: [
        if (kenaikan.isDisahkan) ...[
          // 1. HERO KEPUTUSAN RESMI
          Builder(
            builder: (_) {
              Color cardColor = const Color(0xFF10B981);
              IconData cardIcon = Icons.verified_rounded;
              String titleText = 'ALHAMDULILLAH, NAIK KELAS';
              String subtitleText =
                  'Dinyatakan naik ke tingkat ${kenaikan.levelTujuan ?? "-"}';

              if (kenaikan.isLulus) {
                cardColor = AppColors.violetAccent;
                cardIcon = Icons.school_rounded;
                titleText = 'ALHAMDULILLAH, LULUS';
                subtitleText =
                    'Dinyatakan lulus dari tingkat ${kenaikan.levelAsal}';
              } else if (kenaikan.isTinggalKelas) {
                cardColor = AppColors.amberAccent;
                cardIcon = Icons.menu_book_rounded;
                titleText = 'STATUS: TINGGAL KELAS';
                subtitleText =
                    'Tetap melanjutkan di tingkat ${kenaikan.levelAsal}';
              }

              return GlassCard(
                padding: const EdgeInsets.all(20),
                borderRadius: 24,
                child: Column(
                  children: [
                    Container(
                      width: 64,
                      height: 64,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: cardColor.withValues(alpha: 0.18),
                        border: Border.all(color: cardColor, width: 2),
                      ),
                      child: Icon(cardIcon, color: cardColor, size: 32),
                    ),
                    const SizedBox(height: 14),
                    Text(
                      titleText,
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 17,
                        fontWeight: FontWeight.w900,
                        letterSpacing: -0.3,
                        color: cardColor,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      subtitleText,
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: isDark ? Colors.white70 : Colors.black87,
                      ),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        color: cardColor.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(
                            Icons.assignment_turned_in_rounded,
                            size: 14,
                            color: cardColor,
                          ),
                          const SizedBox(width: 6),
                          Text(
                            'SK: ${kenaikan.noSk ?? "-"}',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w800,
                              color: cardColor,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),

          const SizedBox(height: 14),

          // 2. RINCIAN KEPUTUSAN & AKUMULASI NILAI
          Text(
            'DETAIL KEPUTUSAN & HASIL BELAJAR',
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w900,
              letterSpacing: 0.8,
              color: isDark ? Colors.white60 : Colors.black54,
            ),
          ),
          const SizedBox(height: 8),

          GlassCard(
            padding: const EdgeInsets.all(18),
            borderRadius: 22,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Tahun Pelajaran',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        color: isDark ? Colors.white54 : Colors.black54,
                      ),
                    ),
                    Text(
                      kenaikan.tahunPelajaran,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w800,
                        color: isDark ? Colors.white : Colors.black87,
                      ),
                    ),
                  ],
                ),
                const Divider(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Ruangan Asal',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        color: isDark ? Colors.white54 : Colors.black54,
                      ),
                    ),
                    Text(
                      kenaikan.ruanganAsal,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w800,
                        color: isDark ? Colors.white : Colors.black87,
                      ),
                    ),
                  ],
                ),
                const Divider(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      kenaikan.isLulus ? 'Tingkat Kelulusan' : 'Tingkat Tujuan',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        color: isDark ? Colors.white54 : Colors.black54,
                      ),
                    ),
                    Text(
                      kenaikan.levelTujuan ?? kenaikan.levelAsal,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w900,
                        color: isDark
                            ? AppColors.primaryDark
                            : AppColors.primaryLight,
                      ),
                    ),
                  ],
                ),
                const Divider(height: 20),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Nilai Akumulasi Akhir',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        color: isDark ? Colors.white54 : Colors.black54,
                      ),
                    ),
                    Text(
                      kenaikan.nilaiAkumulasi.toStringAsFixed(2),
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w900,
                        color: Color(0xFF10B981),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          const SizedBox(height: 14),

          // 3. CATATAN & PESAN WALI RUANGAN / DEWAN ASATIDZ
          if (kenaikan.catatanWaliKelas != null &&
              kenaikan.catatanWaliKelas!.isNotEmpty) ...[
            Text(
              'CATATAN WALI RUANGAN & DEWAN ASATIDZ',
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w900,
                letterSpacing: 0.8,
                color: isDark ? Colors.white60 : Colors.black54,
              ),
            ),
            const SizedBox(height: 8),

            GlassCard(
              padding: const EdgeInsets.all(16),
              borderRadius: 20,
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(
                    Icons.format_quote_rounded,
                    size: 24,
                    color: isDark
                        ? AppColors.primaryDark
                        : AppColors.primaryLight,
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      kenaikan.catatanWaliKelas!,
                      style: TextStyle(
                        fontSize: 12,
                        height: 1.5,
                        fontWeight: FontWeight.w500,
                        color: isDark ? Colors.white70 : Colors.black87,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 14),
          ],

          // 4. LEGALITAS PENGESAHAN
          GlassCard(
            padding: const EdgeInsets.all(16),
            borderRadius: 20,
            child: Row(
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color:
                        (isDark
                                ? AppColors.primaryDark
                                : AppColors.primaryLight)
                            .withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    Icons.verified_user_rounded,
                    size: 20,
                    color: isDark
                        ? AppColors.primaryDark
                        : AppColors.primaryLight,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Disahkan pada ${kenaikan.tanggalDisahkanFormatted ?? kenaikan.tanggalDisahkan ?? "-"}',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w800,
                          color: isDark ? Colors.white : Colors.black87,
                        ),
                      ),
                      Text(
                        'Oleh: ${kenaikan.diputuskanOleh ?? "Kepala MDT Hidayatus Shibyan"}',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: isDark ? Colors.white54 : Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ] else ...[
          // STATUS BELUM DISAHKAN
          GlassCard(
            padding: const EdgeInsets.all(24),
            borderRadius: 24,
            child: Column(
              children: [
                Container(
                  width: 64,
                  height: 64,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: AppColors.skyBlueAccent.withValues(alpha: 0.15),
                    border: Border.all(
                      color: AppColors.skyBlueAccent.withValues(alpha: 0.4),
                      width: 2,
                    ),
                  ),
                  child: const Icon(
                    Icons.pending_actions_rounded,
                    color: AppColors.skyBlueAccent,
                    size: 32,
                  ),
                ),
                const SizedBox(height: 16),
                Text(
                  'Keputusan Belum Disahkan',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -0.3,
                    color: isDark ? Colors.white : Colors.black87,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  kenaikan.pesan ??
                      'Keputusan hasil belajar dan kenaikan kelas untuk tahun pelajaran aktif belum disahkan dalam sidang pleno madrasah. Informasi resmi akan tampil otomatis setelah penetapan dewan asatidz.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 12,
                    height: 1.5,
                    fontWeight: FontWeight.w500,
                    color: isDark ? Colors.white60 : Colors.black54,
                  ),
                ),
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 14,
                    vertical: 8,
                  ),
                  decoration: BoxDecoration(
                    color: isDark
                        ? Colors.white.withValues(alpha: 0.05)
                        : Colors.black.withValues(alpha: 0.03),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.meeting_room_rounded,
                        size: 14,
                        color: isDark
                            ? AppColors.primaryDark
                            : AppColors.primaryLight,
                      ),
                      const SizedBox(width: 6),
                      Text(
                        'Ruangan Aktif: ${kenaikan.ruanganAsal}',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w800,
                          color: isDark ? Colors.white70 : Colors.black87,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ],
    );
  }

  // =========================================================================
  // TAB 3: DOKUMEN SK & IJAZAH (KHUSUS KELAS AKHIR & KELULUSAN)
  // =========================================================================
  Widget _buildDokumenIjazahTab(AkademikProvider akademik, bool isDark) {
    final skList = akademik.skList;
    final ijazahList = akademik.ijazahList;
    final isKelasAkhir = akademik.isKelasAkhir;

    return ListView(
      physics: const AlwaysScrollableScrollPhysics(
        parent: BouncingScrollPhysics(),
      ),
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
      children: [
        // 1. SURAT KETERANGAN KELULUSAN (SK / SKTB)
        Text(
          'SURAT KETERANGAN (SK / SKTB)',
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w900,
            letterSpacing: 0.8,
            color: isDark ? Colors.white60 : Colors.black54,
          ),
        ),
        const SizedBox(height: 8),

        if (skList.isEmpty) ...[
          GlassCard(
            padding: const EdgeInsets.all(18),
            borderRadius: 20,
            child: Row(
              children: [
                Icon(
                  Icons.info_outline_rounded,
                  color: isDark ? AppColors.amberDark : AppColors.amberLight,
                  size: 24,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    'Surat Keterangan Kelulusan/Kenaikan belum diterbitkan oleh pihak madrasah.',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: isDark ? Colors.white70 : Colors.black87,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ] else ...[
          ...skList.map((sk) => _buildSkCard(sk, isDark)),
        ],

        const SizedBox(height: 20),

        // 2. IJAZAH MADRASAH
        Text(
          'IJAZAH RESMI MADRASAH',
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w900,
            letterSpacing: 0.8,
            color: isDark ? Colors.white60 : Colors.black54,
          ),
        ),
        const SizedBox(height: 8),

        if (ijazahList.isEmpty) ...[
          GlassCard(
            padding: const EdgeInsets.all(18),
            borderRadius: 20,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: isDark
                            ? AppColors.amberDark.withValues(alpha: 0.15)
                            : AppColors.amberLight.withValues(alpha: 0.1),
                      ),
                      child: Center(
                        child: Icon(
                          Icons.school_rounded,
                          color: isDark
                              ? AppColors.amberDark
                              : AppColors.amberLight,
                          size: 24,
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            isKelasAkhir
                                ? 'Ijazah Sedang Diproses'
                                : 'Belum Mencapai Jenjang Akhir',
                            style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
                              color: isDark ? Colors.white : Colors.black87,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            isKelasAkhir
                                ? 'Ijazah murid jenjang akhir akan otomatis muncul setelah ujian akhir disahkan.'
                                : 'Ijazah resmi diterbitkan ketika murid telah menuntaskan jenjang akhir (3 TPQ, 6 IBT, atau 3 TSA).',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w500,
                              color: isDark ? Colors.white60 : Colors.black54,
                              height: 1.35,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ] else ...[
          ...ijazahList.map((ijz) => _buildIjazahCard(ijz, isDark)),
        ],
      ],
    );
  }

  Widget _buildSkCard(DokumenItemModel sk, bool isDark) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: GlassCard(
        padding: const EdgeInsets.all(16),
        borderRadius: 22,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    color: const Color(0xFF10B981).withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: const Center(
                    child: Icon(
                      Icons.verified_rounded,
                      color: Color(0xFF10B981),
                      size: 24,
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        sk.namaDokumen,
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w900,
                          color: isDark ? Colors.white : Colors.black87,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'No: ${sk.nomorDokumen} • ${sk.tahunPelajaran}',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: isDark ? Colors.white54 : Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFF10B981).withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    sk.statusKeputusan ?? 'LULUS',
                    style: const TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF10B981),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            const Divider(height: 1),
            const SizedBox(height: 10),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => _openUrl(sk.cetakUrl, context),
                    icon: const Icon(Icons.visibility_rounded, size: 16),
                    label: const Text(
                      'Lihat / Print',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () => _openUrl(sk.downloadUrl, context),
                    icon: const Icon(Icons.download_rounded, size: 16),
                    label: const Text(
                      'Unduh PDF',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: isDark
                          ? AppColors.primaryDark
                          : AppColors.primaryLight,
                      foregroundColor: isDark ? Colors.black : Colors.white,
                      elevation: 0,
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildIjazahCard(DokumenItemModel ijz, bool isDark) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(24),
          gradient: LinearGradient(
            colors: isDark
                ? [const Color(0xFF1A3322), const Color(0xFF0F1E14)]
                : [const Color(0xFFE8F5E9), const Color(0xFFC8E6C9)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          border: Border.all(
            color: isDark
                ? AppColors.primaryDark.withValues(alpha: 0.3)
                : AppColors.primaryLight.withValues(alpha: 0.3),
            width: 1.5,
          ),
        ),
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: isDark ? Colors.white10 : Colors.white,
                  ),
                  child: Center(
                    child: Icon(
                      Icons.workspace_premium_rounded,
                      color: isDark
                          ? AppColors.amberDark
                          : AppColors.amberLight,
                      size: 28,
                    ),
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        ijz.namaDokumen,
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w900,
                          letterSpacing: -0.3,
                          color: isDark ? Colors.white : Colors.black87,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'No. Ijazah: ${ijz.nomorDokumen}',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          color: isDark ? Colors.white70 : Colors.black87,
                        ),
                      ),
                      Text(
                        'Tahun: ${ijz.tahunPelajaran} • Disahkan: ${ijz.tanggalDisahkan}',
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.w500,
                          color: isDark ? Colors.white54 : Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 14),
            const Divider(height: 1),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => _openUrl(ijz.cetakUrl, context),
                    icon: const Icon(Icons.print_rounded, size: 16),
                    label: const Text(
                      'Lihat / Print',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () => _openUrl(ijz.downloadUrl, context),
                    icon: const Icon(Icons.download_rounded, size: 16),
                    label: const Text(
                      'Unduh Ijazah PDF',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: isDark
                          ? AppColors.primaryDark
                          : AppColors.primaryLight,
                      foregroundColor: isDark ? Colors.black : Colors.white,
                      elevation: 0,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMiniStat(String title, String value, Color color, bool isDark) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: isDark
            ? Colors.white.withValues(alpha: 0.04)
            : Colors.black.withValues(alpha: 0.03),
        borderRadius: BorderRadius.circular(14),
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
              value,
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w900,
                color: color,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
