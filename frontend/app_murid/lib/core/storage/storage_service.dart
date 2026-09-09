import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/app_constants.dart';

class StorageService {
  static SharedPreferences? _prefs;

  static Future<void> init() async {
    _prefs ??= await SharedPreferences.getInstance();
  }

  // Token
  static Future<void> setToken(String token) async {
    await _prefs?.setString(AppConstants.keyToken, token);
  }

  static String? getToken() {
    return _prefs?.getString(AppConstants.keyToken);
  }

  static Future<void> removeToken() async {
    await _prefs?.remove(AppConstants.keyToken);
  }

  // Wali User Data
  static Future<void> setWaliData(Map<String, dynamic> data) async {
    await _prefs?.setString(AppConstants.keyWaliData, jsonEncode(data));
  }

  static Map<String, dynamic>? getWaliData() {
    final str = _prefs?.getString(AppConstants.keyWaliData);
    if (str == null) return null;
    try {
      return jsonDecode(str) as Map<String, dynamic>;
    } catch (_) {
      return null;
    }
  }

  // Active Child ID
  static Future<void> setSelectedAnakId(int id) async {
    await _prefs?.setInt(AppConstants.keySelectedAnakId, id);
  }

  static int? getSelectedAnakId() {
    return _prefs?.getInt(AppConstants.keySelectedAnakId);
  }

  // Custom Base URL
  static Future<void> setCustomBaseUrl(String url) async {
    await _prefs?.setString(AppConstants.keyCustomBaseUrl, url);
  }

  static String getBaseUrl() {
    return _prefs?.getString(AppConstants.keyCustomBaseUrl) ??
        AppConstants.defaultBaseUrl;
  }

  // Theme Mode
  static Future<void> setThemeMode(String mode) async {
    await _prefs?.setString(AppConstants.keyThemeMode, mode);
  }

  static String? getThemeMode() {
    return _prefs?.getString(AppConstants.keyThemeMode);
  }

  // Clear All on Logout
  static Future<void> clearAll() async {
    await _prefs?.remove(AppConstants.keyToken);
    await _prefs?.remove(AppConstants.keyWaliData);
    await _prefs?.remove(AppConstants.keySelectedAnakId);
  }
}
