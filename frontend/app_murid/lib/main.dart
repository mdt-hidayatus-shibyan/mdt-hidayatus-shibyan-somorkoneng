import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'core/storage/storage_service.dart';
import 'core/theme/app_theme.dart';
import 'providers/akademik_provider.dart';
import 'providers/auth_provider.dart';
import 'providers/bantuan_provider.dart';
import 'providers/dashboard_provider.dart';
import 'providers/keuangan_provider.dart';
import 'providers/presensi_provider.dart';
import 'ui/auth/splash_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Edge to edge immersive system UI
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
      systemNavigationBarColor: Colors.transparent,
      systemNavigationBarIconBrightness: Brightness.dark,
    ),
  );

  await StorageService.init();

  runApp(const WaliApp());
}

class WaliApp extends StatelessWidget {
  const WaliApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => DashboardProvider()),
        ChangeNotifierProvider(create: (_) => KeuanganProvider()),
        ChangeNotifierProvider(create: (_) => PresensiProvider()),
        ChangeNotifierProvider(create: (_) => AkademikProvider()),
        ChangeNotifierProvider(create: (_) => BantuanProvider()),
      ],
      child: Consumer<AuthProvider>(
        builder: (context, auth, _) {
          return MaterialApp(
            title: 'Wali Murid - MDTHS',
            debugShowCheckedModeBanner: false,
            theme: AppTheme.lightTheme,
            darkTheme: AppTheme.darkTheme,
            themeMode: auth.themeMode,
            home: const SplashScreen(),
          );
        },
      ),
    );
  }
}
