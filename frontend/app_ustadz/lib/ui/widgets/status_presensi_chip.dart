import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';

class StatusPresensiChip extends StatelessWidget {
  final String status; // 'H', 'I', 'S', 'A', 'D'
  final bool isSelected;
  final VoidCallback onTap;

  const StatusPresensiChip({
    super.key,
    required this.status,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    Color activeBg;
    Color activeText;
    Color activeBorder;

    switch (status) {
      case 'H':
        activeBg = isDark ? AppColors.hadirBgDark : AppColors.hadirBgLight;
        activeText = isDark
            ? AppColors.hadirTextDark
            : AppColors.hadirTextLight;
        activeBorder = isDark
            ? AppColors.hadirTextDark
            : const Color(0xFF86EFAC);
        break;
      case 'I':
        activeBg = isDark ? AppColors.izinBgDark : AppColors.izinBgLight;
        activeText = isDark ? AppColors.izinTextDark : AppColors.izinTextLight;
        activeBorder = isDark
            ? AppColors.izinTextDark
            : const Color(0xFF93C5FD);
        break;
      case 'S':
        activeBg = isDark ? AppColors.sakitBgDark : AppColors.sakitBgLight;
        activeText = isDark
            ? AppColors.sakitTextDark
            : AppColors.sakitTextLight;
        activeBorder = isDark
            ? AppColors.sakitTextDark
            : const Color(0xFFFDE68A);
        break;
      case 'A':
        activeBg = isDark ? AppColors.alphaBgDark : AppColors.alphaBgLight;
        activeText = isDark
            ? AppColors.alphaTextDark
            : AppColors.alphaTextLight;
        activeBorder = isDark
            ? AppColors.alphaTextDark
            : const Color(0xFFFCA5A5);
        break;
      case 'D':
      default:
        activeBg = isDark
            ? AppColors.dispensasiBgDark
            : AppColors.dispensasiBgLight;
        activeText = isDark
            ? AppColors.dispensasiTextDark
            : AppColors.dispensasiTextLight;
        activeBorder = isDark
            ? AppColors.dispensasiTextDark
            : const Color(0xFFD8B4FE);
        break;
    }

    final inactiveBg = isDark ? const Color(0xFF080D08) : Colors.white;
    final inactiveText = isDark
        ? const Color(0xFF73796E)
        : const Color(0xFF9CA3AF);
    final inactiveBorder = isDark
        ? AppColors.outlineDark
        : AppColors.outlineLight;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 180),
        curve: Curves.easeOutCubic,
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          color: isSelected ? activeBg : inactiveBg,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: isSelected ? activeBorder : inactiveBorder,
            width: isSelected ? 1.8 : 1.0,
          ),
        ),
        alignment: Alignment.center,
        child: Text(
          status,
          style: TextStyle(
            fontSize: 13,
            fontWeight: isSelected ? FontWeight.w900 : FontWeight.w600,
            color: isSelected ? activeText : inactiveText,
          ),
        ),
      ),
    );
  }
}
