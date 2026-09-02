import 'dart:ui';
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

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      extendBody: true, // Content flows behind floating glass bar
      body: IndexedStack(index: _currentIndex, children: _tabs),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(32),
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
              child: Container(
                height: 64,
                padding: const EdgeInsets.symmetric(horizontal: 8),
                decoration: BoxDecoration(
                  color: isDark
                      ? const Color(0xE6101710)
                      : const Color(0xE6FFFFFF),
                  borderRadius: BorderRadius.circular(32),
                  border: Border.all(
                    color: isDark
                        ? AppColors.outlineDark
                        : AppColors.outlineLight,
                    width: 1,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(
                        alpha: isDark ? 0.4 : 0.08,
                      ),
                      blurRadius: 20,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: [
                    _buildNavItem(
                      0,
                      Icons.home_rounded,
                      Icons.home_outlined,
                      'Home',
                      isDark,
                    ),
                    _buildNavItem(
                      1,
                      Icons.fact_check_rounded,
                      Icons.fact_check_outlined,
                      'Presensi',
                      isDark,
                    ),
                    _buildNavItem(
                      2,
                      Icons.warning_amber_rounded,
                      Icons.warning_amber_outlined,
                      'Disiplin',
                      isDark,
                    ),
                    _buildNavItem(
                      3,
                      Icons.assignment_turned_in_rounded,
                      Icons.assignment_turned_in_outlined,
                      'Ujian',
                      isDark,
                    ),
                    _buildNavItem(
                      4,
                      Icons.person_rounded,
                      Icons.person_outline_rounded,
                      'Akun',
                      isDark,
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(
    int index,
    IconData activeIcon,
    IconData inactiveIcon,
    String label,
    bool isDark,
  ) {
    final isSelected = _currentIndex == index;
    final primaryColor = isDark
        ? AppColors.primaryDark
        : AppColors.primaryLight;
    final activeContainer = isDark
        ? AppColors.primaryContainerDark
        : AppColors.primaryContainerLight;
    final inactiveColor = isDark
        ? const Color(0xFF8D9387)
        : const Color(0xFF73796E);

    return InkWell(
      onTap: () => _onTabSelected(index),
      borderRadius: BorderRadius.circular(24),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 220),
        curve: Curves.easeOutCubic,
        padding: EdgeInsets.symmetric(
          horizontal: isSelected ? 12 : 8,
          vertical: 6,
        ),
        decoration: BoxDecoration(
          color: isSelected ? activeContainer : Colors.transparent,
          borderRadius: BorderRadius.circular(20),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              isSelected ? activeIcon : inactiveIcon,
              size: 22,
              color: isSelected ? primaryColor : inactiveColor,
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
                color: isSelected ? primaryColor : inactiveColor,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
