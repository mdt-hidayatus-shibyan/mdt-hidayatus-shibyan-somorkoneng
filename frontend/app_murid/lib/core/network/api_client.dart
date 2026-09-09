import 'package:dio/dio.dart';
import '../storage/storage_service.dart';
import 'api_interceptors.dart';

class ApiClient {
  static ApiClient? _instance;
  late Dio _dio;

  ApiClient._internal() {
    _dio = Dio(
      BaseOptions(
        baseUrl: StorageService.getBaseUrl(),
        connectTimeout: const Duration(seconds: 15),
        receiveTimeout: const Duration(seconds: 15),
      ),
    );
    _dio.interceptors.add(ApiInterceptors());
  }

  static ApiClient get instance {
    _instance ??= ApiClient._internal();
    return _instance!;
  }

  void updateBaseUrl(String newUrl) {
    _dio.options.baseUrl = newUrl;
  }

  Dio get dio => _dio;

  static String? resolveImageUrl(String? rawUrl) {
    if (rawUrl == null || rawUrl.trim().isEmpty) return null;
    final trimmed = rawUrl.trim();
    final activeBase = StorageService.getBaseUrl();
    final baseUri = Uri.tryParse(activeBase);
    if (baseUri == null || !baseUri.hasScheme || !baseUri.hasAuthority) {
      return trimmed;
    }

    final targetHostAuthority = '${baseUri.scheme}://${baseUri.authority}';

    final rawUri = Uri.tryParse(trimmed);
    if (rawUri != null &&
        rawUri.hasScheme &&
        (rawUri.host == 'localhost' || rawUri.host == '127.0.0.1')) {
      final pathAndQuery =
          '${rawUri.path}${rawUri.hasQuery ? '?${rawUri.query}' : ''}';
      return '$targetHostAuthority$pathAndQuery';
    }

    if (!trimmed.startsWith('http://') && !trimmed.startsWith('https://')) {
      final cleanPath = trimmed.startsWith('/') ? trimmed : '/$trimmed';
      return '$targetHostAuthority$cleanPath';
    }

    return trimmed;
  }
}
