import 'package:flutter/material.dart';

/// Material 3 Expressive & Google Pixel Color Palette
/// Anchor Seed: Madrasah Forest Green (#146C2E / #3BC05B)
class AppColors {
  AppColors._();

  // === BRAND PRIMARY ===
  static const Color primaryLight = Color(0xFF146C2E); // Vibrant Forest Green
  static const Color primaryDark = Color(0xFF3BC05B); // Bright Emerald for OLED
  static const Color onPrimaryLight = Color(0xFFFFFFFF);
  static const Color onPrimaryDark = Color(0xFF003911);

  // === PRIMARY CONTAINERS ===
  static const Color primaryContainerLight = Color(0xFFDCFCE7); // Emerald 100
  static const Color primaryContainerDark = Color(
    0xFF00531E,
  ); // Emerald 950 Deep
  static const Color onPrimaryContainerLight = Color(0xFF14532D); // Emerald 900
  static const Color onPrimaryContainerDark = Color(0xFFA6FBAA); // Emerald 200

  // === OLED TRUE BLACK SURFACES ===
  static const Color surfaceLight = Color(0xFFFAF9F6); // Warm Clean Canvas
  static const Color surfaceDark = Color(0xFF000000); // Super AMOLED True Black

  static const Color cardGlassLight = Color(
    0xD9FFFFFF,
  ); // rgba(255, 255, 255, 0.85)
  static const Color cardGlassDark = Color(0xA6181F18); // Dark Glass Surface

  static const Color surfaceContainerLowDark = Color(0xFF080D08);
  static const Color surfaceContainerDark = Color(0xFF101710);
  static const Color surfaceContainerHighDark = Color(0xFF182218);

  // === OUTLINE & BORDERS ===
  static const Color outlineLight = Color(0xFFE4E4E7); // Zinc 200
  static const Color outlineDark = Color(0xFF272F27); // Deep Greenish Zinc

  // === ACCENT PALETTE ===
  static const Color amberAccent = Color(
    0xFFF59E0B,
  ); // Poin Pelanggaran, Ranking
  static const Color skyBlueAccent = Color(0xFF0284C7); // Kalender, Pengumuman
  static const Color violetAccent = Color(
    0xFF7C3AED,
  ); // Ujian & Leger Wali Ruangan
  static const Color roseDanger = Color(0xFFDC2626); // Hapus, Alpha, Locked

  // === SEMANTIC STATUS PRESENSI (H, I, S, A, D) ===
  // Hadir
  static const Color hadirTextLight = Color(0xFF15803D);
  static const Color hadirBgLight = Color(0xFFDCFCE7);
  static const Color hadirTextDark = Color(0xFF4ADE80);
  static const Color hadirBgDark = Color(0xFF052E16);

  // Izin
  static const Color izinTextLight = Color(0xFF1D4ED8);
  static const Color izinBgLight = Color(0xFFDBEAFE);
  static const Color izinTextDark = Color(0xFF60A5FA);
  static const Color izinBgDark = Color(0xFF172554);

  // Sakit
  static const Color sakitTextLight = Color(0xFFB45309);
  static const Color sakitBgLight = Color(0xFFFEF3C7);
  static const Color sakitTextDark = Color(0xFFFBBF24);
  static const Color sakitBgDark = Color(0xFF451A03);

  // Alpha
  static const Color alphaTextLight = Color(0xFFB91C1C);
  static const Color alphaBgLight = Color(0xFFFEE2E2);
  static const Color alphaTextDark = Color(0xFFF87171);
  static const Color alphaBgDark = Color(0xFF450A0A);

  // Dispensasi
  static const Color dispensasiTextLight = Color(0xFF6D28D9);
  static const Color dispensasiBgLight = Color(0xFFF3E8FF);
  static const Color dispensasiTextDark = Color(0xFFC084FC);
  static const Color dispensasiBgDark = Color(0xFF3B0764);
}
