import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/utils/haptic_helper.dart';
import '../tabs/home/home_tab.dart';
import '../tabs/presensi/presensi_tab.dart';
import '../tabs/pelanggaran/pelanggaran_tab.dart';
import '../tabs/ujian/ujian_tab.dart';
import '../tabs/akun/akun_tab.dart';

class MainNavigationShell extends StatefulWidget {
  final int initialIndex;
  const MainNavigationShell({super.key, this.initialIndex = 0});

  @override
  State<MainNavigationShell> createState() => _MainNavigationShellState();
}

class _MainNavigationShellState extends State<MainNavigationShell> {
  late int _currentIndex;

  final List<Widget> _tabs = const [
    HomeTab(),
    PresensiTab(),
    PelanggaranTab(),
    UjianTab(),
    AkunTab(),
  ];

  @override
  void initState() {
    super.initState();
    _currentIndex = widget.initialIndex;
  }

  void _onTabSelected(int index) {
    if (_currentIndex != index) {
      HapticHelper.segmentTick();
      setState(() {
        _currentIndex = index;
      });
    }
  }

  void _onPresensiPressed() {
    HapticHelper.medium();
    _onTabSelected(1); // Navigasi ke Tab Presensi (Index 1)
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final isPresensiActive = _currentIndex == 1;

    return Scaffold(
      extendBody: true, // Content flows smoothly behind floating navigation
      body: IndexedStack(index: _currentIndex, children: _tabs),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(14, 0, 14, 12),
          child: Row(
            children: [
              // 1. MAIN PILL NAVIGATION BAR
              Expanded(
                child: Container(
                  height: 62,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 6,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    color: isDark
                        ? const Color(0xFF121712).withValues(alpha: 0.95)
                        : const Color(0xFFF1F3F5).withValues(alpha: 0.96),
                    borderRadius: BorderRadius.circular(36),
                    border: Border.all(
                      color: isDark
                          ? AppColors.outlineDark.withValues(alpha: 0.6)
                          : const Color(0xFFE2E8F0),
                      width: 1,
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(
                          alpha: isDark ? 0.45 : 0.08,
                        ),
                        blurRadius: 20,
                        offset: const Offset(0, 6),
                      ),
                    ],
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _buildNavItem(
                        index: 0,
                        activeIcon: Icons.home_rounded,
                        inactiveIcon: Icons.home_outlined,
                        label: 'Home',
                        isDark: isDark,
                      ),
                      _buildNavItem(
                        index: 2,
                        activeIcon: Icons.warning_amber_rounded,
                        inactiveIcon: Icons.warning_amber_outlined,
                        label: 'Disiplin',
                        isDark: isDark,
                      ),
                      _buildNavItem(
                        index: 3,
                        activeIcon: Icons.assignment_turned_in_rounded,
                        inactiveIcon: Icons.dashboard_outlined,
                        label: 'Ujian',
                        isDark: isDark,
                      ),
                      _buildNavItem(
                        index: 4,
                        activeIcon: Icons.person_rounded,
                        inactiveIcon: Icons.person_outline_rounded,
                        label: 'Akun',
                        isDark: isDark,
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(width: 10),

              // 2. SEPARATE CIRCULAR ACTION BUTTON (PRESENSI CEPAT)
              GestureDetector(
                onTap: _onPresensiPressed,
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 250),
                  width: 58,
                  height: 58,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: LinearGradient(
                      colors: isDark
                          ? const [AppColors.primaryDark, Color(0xFF22C55E)]
                          : const [AppColors.primaryLight, Color(0xFF16A34A)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    border: isPresensiActive
                        ? Border.all(
                            color: isDark
                                ? AppColors.onPrimaryDark
                                : AppColors.onPrimaryLight,
                            width: 2.5,
                          )
                        : null,
                    boxShadow: [
                      BoxShadow(
                        color:
                            (isDark
                                    ? AppColors.primaryDark
                                    : AppColors.primaryLight)
                                .withValues(
                                  alpha: isPresensiActive ? 0.55 : 0.35,
                                ),
                        blurRadius: isPresensiActive ? 18 : 14,
                        offset: const Offset(0, 6),
                      ),
                    ],
                  ),
                  child: Center(
                    child: Icon(
                      Icons.how_to_reg_rounded, // Icon Presensi / Kehadiran
                      color: isDark
                          ? AppColors.onPrimaryDark
                          : AppColors.onPrimaryLight,
                      size: 26,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem({
    required int index,
    required IconData activeIcon,
    required IconData inactiveIcon,
    required String label,
    required bool isDark,
  }) {
    final isSelected = _currentIndex == index;

    return GestureDetector(
      onTap: () => _onTabSelected(index),
      behavior: HitTestBehavior.opaque,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 260),
        curve: Curves.easeOutCubic,
        padding: EdgeInsets.symmetric(
          horizontal: isSelected ? 12 : 8,
          vertical: 6,
        ),
        decoration: BoxDecoration(
          color: isSelected
              ? (isDark ? AppColors.surfaceContainerHighDark : Colors.white)
              : Colors.transparent,
          borderRadius: BorderRadius.circular(28),
          boxShadow: isSelected
              ? [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: isDark ? 0.35 : 0.08),
                    blurRadius: 10,
                    offset: const Offset(0, 3),
                  ),
                ]
              : null,
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Icon Pill Badge when selected / clean outline icon when inactive
            if (isSelected) ...[
              Container(
                width: 28,
                height: 28,
                decoration: BoxDecoration(
                  color: isDark
                      ? AppColors.primaryDark
                      : AppColors.primaryLight,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Center(
                  child: Icon(
                    activeIcon,
                    size: 16,
                    color: isDark
                        ? AppColors.onPrimaryDark
                        : AppColors.onPrimaryLight,
                  ),
                ),
              ),
              const SizedBox(width: 6),
              Text(
                label,
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w900,
                  letterSpacing: -0.2,
                  color: isDark ? Colors.white : AppColors.primaryLight,
                ),
              ),
            ] else ...[
              Padding(
                padding: const EdgeInsets.all(4.0),
                child: Icon(
                  inactiveIcon,
                  size: 22,
                  color: isDark ? Colors.white70 : const Color(0xFF334155),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
