import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/theme/app_colors.dart';
import '../../core/utils/haptic_helper.dart';
import '../../providers/akademik_provider.dart';
import '../../providers/dashboard_provider.dart';
import '../../providers/keuangan_provider.dart';
import '../../providers/presensi_provider.dart';
import '../tabs/akademik/akademik_tab.dart';
import '../tabs/akun/akun_tab.dart';
import '../tabs/home/home_tab.dart';
import '../tabs/presensi/presensi_tab.dart';
import '../tabs/tagihan/tagihan_tab.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _initData();
    });
  }

  void _initData() async {
    final dashboard = context.read<DashboardProvider>();
    await dashboard.fetchDashboard();

    if (dashboard.selectedAnak != null) {
      final id = dashboard.selectedAnak!.id;
      if (mounted) {
        context.read<KeuanganProvider>().fetchAllKeuangan(id);
        context.read<PresensiProvider>().fetchPresensi(id);
        context.read<AkademikProvider>().fetchAkademik(id);
      }
    }
  }

  void _onTabChanged(int index) {
    if (_currentIndex == index) return;
    HapticHelper.selection();
    setState(() {
      _currentIndex = index;
    });

    final selectedAnak = context.read<DashboardProvider>().selectedAnak;
    if (selectedAnak != null) {
      if (index == 1) {
        context.read<KeuanganProvider>().fetchAllKeuangan(selectedAnak.id);
      } else if (index == 2) {
        context.read<PresensiProvider>().fetchPresensi(selectedAnak.id);
      } else if (index == 3) {
        context.read<AkademikProvider>().fetchAkademik(selectedAnak.id);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    final List<Widget> screens = [
      HomeTab(onNavigateTab: _onTabChanged),
      const TagihanTab(),
      const PresensiTab(),
      const AkademikTab(),
      const AkunTab(),
    ];

    return Scaffold(
      backgroundColor: isDark ? AppColors.surfaceDark : AppColors.surfaceLight,
      body: Stack(
        children: [
          IndexedStack(index: _currentIndex, children: screens),

          // Floating Pill Bottom Navigation Bar
          Positioned(
            left: 16,
            right: 16,
            bottom: 16,
            child: _buildFloatingNavBar(isDark),
          ),
        ],
      ),
    );
  }

  Widget _buildFloatingNavBar(bool isDark) {
    final items = [
      _NavItem(icon: Icons.home_rounded, label: 'Beranda'),
      _NavItem(icon: Icons.receipt_long_rounded, label: 'Tagihan'),
      _NavItem(icon: Icons.event_available_rounded, label: 'Presensi'),
      _NavItem(icon: Icons.auto_stories_rounded, label: 'Akademik'),
      _NavItem(icon: Icons.person_rounded, label: 'Akun'),
    ];

    final bgColor = isDark ? const Color(0xE6101710) : const Color(0xF2FFFFFF);
    final borderColor = isDark ? AppColors.outlineDark : AppColors.outlineLight;

    return ClipRRect(
      borderRadius: BorderRadius.circular(28),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
        child: Container(
          height: 64,
          padding: const EdgeInsets.symmetric(horizontal: 8),
          decoration: BoxDecoration(
            color: bgColor,
            borderRadius: BorderRadius.circular(28),
            border: Border.all(color: borderColor, width: 1),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: isDark ? 0.4 : 0.08),
                blurRadius: 24,
                offset: const Offset(0, 8),
              ),
            ],
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: List.generate(items.length, (index) {
              final isSelected = _currentIndex == index;
              final item = items[index];

              return InkWell(
                onTap: () => _onTabChanged(index),
                borderRadius: BorderRadius.circular(20),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 200),
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 8,
                  ),
                  decoration: BoxDecoration(
                    color: isSelected
                        ? (isDark
                              ? AppColors.primaryDark.withValues(alpha: 0.2)
                              : AppColors.primaryLight.withValues(alpha: 0.12))
                        : Colors.transparent,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        item.icon,
                        size: 22,
                        color: isSelected
                            ? (isDark
                                  ? AppColors.primaryDark
                                  : AppColors.primaryLight)
                            : (isDark ? Colors.white54 : Colors.black45),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        item.label,
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: isSelected
                              ? FontWeight.w900
                              : FontWeight.w600,
                          color: isSelected
                              ? (isDark
                                    ? AppColors.primaryDark
                                    : AppColors.primaryLight)
                              : (isDark ? Colors.white54 : Colors.black45),
                        ),
                      ),
                    ],
                  ),
                ),
              );
            }),
          ),
        ),
      ),
    );
  }
}

class _NavItem {
  final IconData icon;
  final String label;

  _NavItem({required this.icon, required this.label});
}
