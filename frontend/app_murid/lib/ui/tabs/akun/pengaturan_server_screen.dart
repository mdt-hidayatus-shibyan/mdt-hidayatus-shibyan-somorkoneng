import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../providers/auth_provider.dart';
import '../../widgets/glass_card.dart';

class PengaturanServerScreen extends StatefulWidget {
  const PengaturanServerScreen({super.key});

  @override
  State<PengaturanServerScreen> createState() => _PengaturanServerScreenState();
}

class _PengaturanServerScreenState extends State<PengaturanServerScreen> {
  late TextEditingController _urlController;

  @override
  void initState() {
    super.initState();
    final auth = context.read<AuthProvider>();
    _urlController = TextEditingController(text: auth.baseUrl);
  }

  @override
  void dispose() {
    _urlController.dispose();
    super.dispose();
  }

  void _saveUrl() {
    final text = _urlController.text.trim();
    if (text.isEmpty) return;

    HapticHelper.medium();
    context.read<AuthProvider>().setBaseUrl(text);

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Alamat server API berhasil diperbarui!'),
        backgroundColor: Color(0xFF10B981),
      ),
    );
    Navigator.of(context).pop();
  }

  void _resetToDefault() {
    HapticHelper.light();
    _urlController.text = AppConstants.defaultBaseUrl;
  }

  void _setToProduction() {
    HapticHelper.light();
    _urlController.text = AppConstants.productionBaseUrl;
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      backgroundColor: isDark ? AppColors.surfaceDark : AppColors.surfaceLight,
      appBar: AppBar(
        title: const Text('Pengaturan Server API'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 18),
          onPressed: () => Navigator.of(context).pop(),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        physics: const BouncingScrollPhysics(),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            GlassCard(
              padding: const EdgeInsets.all(20),
              borderRadius: 24,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Konfigurasi Endpoint API',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w900,
                      color: isDark ? Colors.white : Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    'Ubah alamat base URL backend Laravel jika Anda menggunakan jaringan lokal IP WiFi madrasah atau domain hosting.',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                      color: isDark ? Colors.white60 : Colors.black54,
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 18),

                  TextField(
                    controller: _urlController,
                    decoration: InputDecoration(
                      labelText: 'Base URL API',
                      labelStyle: TextStyle(
                        fontSize: 12,
                        color: isDark ? Colors.white60 : Colors.black54,
                      ),
                      hintText: 'http://192.168.1.xxx:8000/api',
                      prefixIcon: Icon(
                        Icons.link_rounded,
                        size: 20,
                        color: isDark
                            ? AppColors.primaryDark
                            : AppColors.primaryLight,
                      ),
                      filled: true,
                      fillColor: isDark
                          ? AppColors.surfaceContainerLowDark
                          : AppColors.surfaceLight,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: BorderSide(
                          color: isDark
                              ? AppColors.outlineDark
                              : AppColors.outlineLight,
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 16),

                  Row(
                    children: [
                      OutlinedButton(
                        onPressed: _resetToDefault,
                        style: OutlinedButton.styleFrom(
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: const Text(
                          'Emulator (10.0.2.2)',
                          style: TextStyle(fontSize: 11),
                        ),
                      ),
                      const SizedBox(width: 8),
                      OutlinedButton(
                        onPressed: _setToProduction,
                        style: OutlinedButton.styleFrom(
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: const Text(
                          'Domain Web',
                          style: TextStyle(fontSize: 11),
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 20),

                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: ElevatedButton(
                      onPressed: _saveUrl,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: isDark
                            ? AppColors.primaryDark
                            : AppColors.primaryLight,
                        foregroundColor: isDark ? Colors.black : Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                        elevation: 0,
                      ),
                      child: const Text(
                        'Simpan Konfigurasi',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w900,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
