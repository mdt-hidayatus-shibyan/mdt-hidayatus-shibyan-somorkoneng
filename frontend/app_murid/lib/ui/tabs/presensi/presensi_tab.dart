import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../providers/dashboard_provider.dart';
import '../../../providers/presensi_provider.dart';
import '../../widgets/child_switcher_bar.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/modern_header.dart';

class PresensiTab extends StatefulWidget {
  const PresensiTab({super.key});

  @override
  State<PresensiTab> createState() => _PresensiTabState();
}

class _PresensiTabState extends State<PresensiTab> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final selectedAnak = context.read<DashboardProvider>().selectedAnak;
      if (selectedAnak != null) {
        context.read<PresensiProvider>().fetchPresensi(selectedAnak.id);
      }
    });
  }

  void _kirimIzinWhatsApp(String namaAnak, String ruangan) async {
    HapticHelper.medium();

    final text =
        "Assalamu'alaikum Wr. Wb. Ustadz / Ustadzah Wali Ruangan,\n\n"
        "Saya selaku Wali Murid dari:\n"
        "👤 *Nama Santri:* $namaAnak\n"
        "🏫 *Ruangan:* $ruangan\n\n"
        "Bermaksud untuk memohon izin bahwa ananda berhalangan hadir pada kegiatan belajar mengajar madrasah dikarenakan: (tuliskan alasan: Sakit / Kepentingan Keluarga).\n\n"
        "Atas perhatian dan izin dari Ustadz/Ustadzah, kami sampaikan terima kasih.\nWassalamu'alaikum Wr. Wb.";

    final encoded = Uri.encodeComponent(text);
    final url = Uri.parse('https://wa.me/?text=$encoded');

    try {
      if (await canLaunchUrl(url)) {
        await launchUrl(url, mode: LaunchMode.externalApplication);
      }
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final dashboard = context.watch<DashboardProvider>();
    final selectedAnak = dashboard.selectedAnak;
    final presensi = context.watch<PresensiProvider>();

    if (selectedAnak == null) {
      return Scaffold(
        body: const EmptyStateWidget(
          icon: Icons.child_care_rounded,
          title: 'Pilih Santri Terlebih Dahulu',
          subtitle: 'Silakan pilih profil anak pada menu Beranda.',
        ),
      );
    }

    final rekap = presensi.rekapPresensi;

    return Scaffold(
      backgroundColor: isDark ? AppColors.surfaceDark : AppColors.surfaceLight,
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
              child: ModernHeader(
                title: 'Presensi Santri',
                subtitle: 'Rekapitulasi Kehadiran & Izin Madrasah',
                icon: Icons.event_available_rounded,
              ),
            ),

            // Multi-Child Switcher
            const ChildSwitcherBar(),

            Expanded(
              child: presensi.isLoading
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
                        await presensi.fetchPresensi(
                          selectedAnak.id,
                          force: true,
                        );
                      },
                      color: isDark
                          ? AppColors.primaryDark
                          : AppColors.primaryLight,
                      child: ListView(
                        physics: const AlwaysScrollableScrollPhysics(
                          parent: BouncingScrollPhysics(),
                        ),
                        padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
                        children: [
                          // Card Statistik Persentase Kehadiran
                          GlassCard(
                            padding: const EdgeInsets.all(20),
                            borderRadius: 24,
                            child: Column(
                              children: [
                                Row(
                                  children: [
                                    // Circular Percentage Box
                                    Container(
                                      width: 64,
                                      height: 64,
                                      decoration: BoxDecoration(
                                        shape: BoxShape.circle,
                                        color: isDark
                                            ? AppColors.primaryDark.withValues(
                                                alpha: 0.15,
                                              )
                                            : AppColors.primaryLight.withValues(
                                                alpha: 0.1,
                                              ),
                                        border: Border.all(
                                          color: isDark
                                              ? AppColors.primaryDark
                                              : AppColors.primaryLight,
                                          width: 3,
                                        ),
                                      ),
                                      child: Center(
                                        child: Text(
                                          '${rekap?.persentaseHadir.toInt() ?? 0}%',
                                          style: TextStyle(
                                            fontSize: 16,
                                            fontWeight: FontWeight.w900,
                                            color: isDark
                                                ? AppColors.primaryDark
                                                : AppColors.primaryLight,
                                          ),
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 16),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            'Tingkat Kehadiran',
                                            style: TextStyle(
                                              fontSize: 15,
                                              fontWeight: FontWeight.w900,
                                              color: isDark
                                                  ? Colors.white
                                                  : Colors.black87,
                                            ),
                                          ),
                                          const SizedBox(height: 2),
                                          Text(
                                            'Total Sesi Terlaksana: ${rekap?.totalSesi ?? 0} Sesi',
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
                                  ],
                                ),
                                const SizedBox(height: 16),
                                const Divider(height: 1),
                                const SizedBox(height: 14),

                                // Grid Status (Hadir, Sakit, Izin, Alpha)
                                Row(
                                  children: [
                                    Expanded(
                                      child: _buildPresensiStatBox(
                                        label: 'Hadir',
                                        count: rekap?.hadir ?? 0,
                                        color: const Color(0xFF10B981),
                                        isDark: isDark,
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: _buildPresensiStatBox(
                                        label: 'Izin',
                                        count: rekap?.izin ?? 0,
                                        color: AppColors.skyBlueAccent,
                                        isDark: isDark,
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: _buildPresensiStatBox(
                                        label: 'Sakit',
                                        count: rekap?.sakit ?? 0,
                                        color: AppColors.amberAccent,
                                        isDark: isDark,
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: _buildPresensiStatBox(
                                        label: 'Alpha',
                                        count: rekap?.alpha ?? 0,
                                        color: AppColors.roseDanger,
                                        isDark: isDark,
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),

                          const SizedBox(height: 14),

                          // Tombol Cepat Ajukan Izin via WhatsApp
                          GlassCard(
                            padding: const EdgeInsets.all(16),
                            borderRadius: 20,
                            onTap: () => _kirimIzinWhatsApp(
                              selectedAnak.namaLengkap,
                              selectedAnak.ruangan ?? '-',
                            ),
                            child: Row(
                              children: [
                                Container(
                                  width: 44,
                                  height: 44,
                                  decoration: BoxDecoration(
                                    color: const Color(
                                      0xFF25D366,
                                    ).withValues(alpha: 0.15),
                                    borderRadius: BorderRadius.circular(14),
                                  ),
                                  child: const Icon(
                                    Icons.chat_rounded,
                                    color: Color(0xFF25D366),
                                    size: 22,
                                  ),
                                ),
                                const SizedBox(width: 14),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'Ajukan Izin / Sakit Santri',
                                        style: TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w900,
                                          color: isDark
                                              ? Colors.white
                                              : Colors.black87,
                                        ),
                                      ),
                                      const SizedBox(height: 2),
                                      Text(
                                        'Kirim format surat izin otomatis ke Wali Ruangan via WA',
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
                                const Icon(
                                  Icons.arrow_forward_ios_rounded,
                                  size: 14,
                                ),
                              ],
                            ),
                          ),

                          const SizedBox(height: 16),

                          Text(
                            'RIWAYAT PRESENSI PER SESI',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 0.8,
                              color: isDark ? Colors.white60 : Colors.black54,
                            ),
                          ),
                          const SizedBox(height: 8),

                          if (presensi.riwayat.isEmpty)
                            const EmptyStateWidget(
                              icon: Icons.event_busy_rounded,
                              title: 'Belum Ada Riwayat Presensi',
                              subtitle:
                                  'Catatan presensi santri akan muncul di sini.',
                            )
                          else
                            ...presensi.riwayat.map((p) {
                              return Padding(
                                padding: const EdgeInsets.only(bottom: 8),
                                child: GlassCard(
                                  padding: const EdgeInsets.all(14),
                                  borderRadius: 18,
                                  child: Row(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      Row(
                                        children: [
                                          Container(
                                            width: 36,
                                            height: 36,
                                            decoration: BoxDecoration(
                                              color: isDark
                                                  ? Colors.white.withValues(
                                                      alpha: 0.05,
                                                    )
                                                  : Colors.black.withValues(
                                                      alpha: 0.03,
                                                    ),
                                              borderRadius:
                                                  BorderRadius.circular(12),
                                            ),
                                            child: Icon(
                                              Icons.menu_book_rounded,
                                              size: 18,
                                              color: isDark
                                                  ? AppColors.primaryDark
                                                  : AppColors.primaryLight,
                                            ),
                                          ),
                                          const SizedBox(width: 12),
                                          Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                p.mapel,
                                                style: TextStyle(
                                                  fontSize: 13,
                                                  fontWeight: FontWeight.w800,
                                                  color: isDark
                                                      ? Colors.white
                                                      : Colors.black87,
                                                ),
                                              ),
                                              Text(
                                                '${p.hari}, ${p.tanggal}',
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
                                        ],
                                      ),
                                      _buildStatusBadge(p.status, isDark),
                                    ],
                                  ),
                                ),
                              );
                            }),
                        ],
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPresensiStatBox({
    required String label,
    required int count,
    required Color color,
    required bool isDark,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 6),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        children: [
          Text(
            '$count',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w900,
              color: color,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w700,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusBadge(String status, bool isDark) {
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
      text = const Color(0xFFD97706);
    } else if (status == 'Alpha') {
      bg = AppColors.roseDanger.withValues(alpha: 0.15);
      text = AppColors.roseDanger;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(10),
      ),
      child: Text(
        status,
        style: TextStyle(
          fontSize: 10,
          fontWeight: FontWeight.w900,
          color: text,
        ),
      ),
    );
  }
}
