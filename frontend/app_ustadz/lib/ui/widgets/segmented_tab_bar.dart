import 'dart:ui';
import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/utils/haptic_helper.dart';

class SegmentedTabItem {
  final IconData activeIcon;
  final IconData inactiveIcon;
  final String label;
  final Color? activeColor;
  final Color? activeContainer;

  const SegmentedTabItem({
    required this.activeIcon,
    required this.inactiveIcon,
    required this.label,
    this.activeColor,
    this.activeContainer,
  });
}

class SegmentedTabBar extends StatelessWidget {
  final List<SegmentedTabItem> items;
  final int selectedIndex;
  final ValueChanged<int> onTabChanged;
  final EdgeInsetsGeometry margin;
  final Color? accentColor;

  const SegmentedTabBar({
    super.key,
    required this.items,
    required this.selectedIndex,
    required this.onTabChanged,
    this.margin = const EdgeInsets.fromLTRB(16, 12, 16, 8),
    this.accentColor,
  });

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final defaultAccent =
        accentColor ??
        (isDark ? AppColors.primaryDark : AppColors.primaryLight);

    return Padding(
      padding: margin,
      child: ClipRRect(
        borderRadius: BorderRadius.circular(24),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 16, sigmaY: 16),
          child: Container(
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: isDark ? const Color(0xE6101710) : const Color(0xF2FFFFFF),
              borderRadius: BorderRadius.circular(24),
              border: Border.all(
                color: isDark
                    ? const Color(0xFF263326)
                    : const Color(0xFFE2E8F0),
                width: 1,
              ),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: isDark ? 0.35 : 0.06),
                  blurRadius: 16,
                  offset: const Offset(0, 3),
                ),
              ],
            ),
            child: Row(
              children: List.generate(items.length, (index) {
                final item = items[index];
                final isSelected = selectedIndex == index;
                final itemAccent = item.activeColor ?? defaultAccent;
                final itemContainer =
                    item.activeContainer ??
                    (isDark
                        ? itemAccent.withValues(alpha: 0.18)
                        : itemAccent.withValues(alpha: 0.14));
                final inactiveColor = isDark
                    ? const Color(0xFF8D9387)
                    : const Color(0xFF73796E);

                return Expanded(
                  child: InkWell(
                    onTap: () {
                      if (selectedIndex != index) {
                        HapticHelper.segmentTick();
                        onTabChanged(index);
                      }
                    },
                    borderRadius: BorderRadius.circular(18),
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 240),
                      curve: Curves.easeOutCubic,
                      padding: const EdgeInsets.symmetric(
                        vertical: 8,
                        horizontal: 6,
                      ),
                      decoration: BoxDecoration(
                        color: isSelected ? itemContainer : Colors.transparent,
                        borderRadius: BorderRadius.circular(18),
                        border: isSelected
                            ? Border.all(
                                color: itemAccent.withValues(
                                  alpha: isDark ? 0.35 : 0.25,
                                ),
                                width: 0.8,
                              )
                            : null,
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            isSelected ? item.activeIcon : item.inactiveIcon,
                            size: 17,
                            color: isSelected ? itemAccent : inactiveColor,
                          ),
                          const SizedBox(width: 6),
                          Flexible(
                            child: Text(
                              item.label,
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: isSelected
                                    ? FontWeight.w800
                                    : FontWeight.w600,
                                color: isSelected ? itemAccent : inactiveColor,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              }),
            ),
          ),
        ),
      ),
    );
  }
}
