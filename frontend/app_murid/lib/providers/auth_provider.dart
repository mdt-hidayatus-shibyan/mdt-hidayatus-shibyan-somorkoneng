import 'package:flutter/material.dart';
import '../core/network/api_client.dart';
import '../core/storage/storage_service.dart';
import '../data/models/wali_model.dart';
import '../data/repositories/auth_repository.dart';

class AuthProvider extends ChangeNotifier {
  final AuthRepository _authRepo = AuthRepository();

  bool _isLoading = false;
  String? _errorMessage;
  WaliModel? _currentWali;
  String _baseUrl = StorageService.getBaseUrl();
  ThemeMode _themeMode = ThemeMode.light; // Default Light Mode

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  WaliModel? get currentWali => _currentWali;
  bool get isAuthenticated => StorageService.getToken() != null;
  String get baseUrl => _baseUrl;
  ThemeMode get themeMode => _themeMode;

  AuthProvider() {
    _loadSavedSession();
  }

  void _loadSavedSession() {
    final waliJson = StorageService.getWaliData();
    if (waliJson != null) {
      _currentWali = WaliModel.fromJson(waliJson);
    }
    _baseUrl = StorageService.getBaseUrl();

    final savedTheme = StorageService.getThemeMode();
    if (savedTheme == 'dark') {
      _themeMode = ThemeMode.dark;
    } else if (savedTheme == 'system') {
      _themeMode = ThemeMode.system;
    } else {
      _themeMode = ThemeMode.light;
    }
  }

  /// Step 1: Validasi keberadaan No. KK / NISM
  Future<Map<String, dynamic>> checkIdentifier(String identifier) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await _authRepo.checkIdentifier(identifier);

    _isLoading = false;
    if (result['success'] != true) {
      _errorMessage = result['message'] as String?;
    }
    notifyListeners();
    return result;
  }

  /// Step 2: Login dengan PIN
  Future<bool> login(String identifier, String pin) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await _authRepo.loginWali(identifier, pin);

    _isLoading = false;
    if (result['success'] == true) {
      _currentWali = result['wali'] as WaliModel;
      _errorMessage = null;
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'] as String?;
      notifyListeners();
      return false;
    }
  }

  /// Ganti PIN
  Future<Map<String, dynamic>> updatePin(String pinLama, String pinBaru) async {
    _isLoading = true;
    notifyListeners();

    final result = await _authRepo.updatePin(pinLama, pinBaru);

    _isLoading = false;
    if (result['success'] == true && _currentWali != null) {
      _currentWali = _currentWali!.copyWith(
        isFirstLogin: false,
        isPinChanged: true,
      );
      await StorageService.setWaliData(_currentWali!.toJson());
    }
    notifyListeners();
    return result;
  }

  Future<void> logout() async {
    await _authRepo.logout();
    _currentWali = null;
    notifyListeners();
  }

  void setBaseUrl(String newUrl) {
    _baseUrl = newUrl;
    StorageService.setCustomBaseUrl(newUrl);
    ApiClient.instance.updateBaseUrl(newUrl);
    notifyListeners();
  }

  void setThemeMode(ThemeMode mode) {
    _themeMode = mode;
    String modeStr = 'light';
    if (mode == ThemeMode.dark) modeStr = 'dark';
    if (mode == ThemeMode.system) modeStr = 'system';
    StorageService.setThemeMode(modeStr);
    notifyListeners();
  }
}
