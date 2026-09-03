import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:open_filex/open_filex.dart';
import 'package:path_provider/path_provider.dart';
import 'package:url_launcher/url_launcher.dart';

class FileDownloadHelper {
  FileDownloadHelper._();

  /// Mengunduh file PDF secara lokal dan membukanya langsung dengan viewer HP
  static Future<void> downloadAndOpen(
    BuildContext context, {
    required String url,
    required String fileName,
  }) async {
    // Tampilkan loading snackbar
    final messenger = ScaffoldMessenger.of(context);
    messenger.showSnackBar(
      SnackBar(
        content: Row(
          children: [
            const SizedBox(
              width: 18,
              height: 18,
              child: CircularProgressIndicator(
                strokeWidth: 2.2,
                color: Colors.white,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                'Mengunduh $fileName...',
                style: const TextStyle(fontSize: 13),
              ),
            ),
          ],
        ),
        duration: const Duration(seconds: 30),
      ),
    );

    try {
      final dio = Dio();
      final dir = await getApplicationDocumentsDirectory();

      // Sanitasi nama file agar valid di filesystem
      final safeName = fileName.replaceAll(RegExp(r'[^\w\s\.-]'), '_');
      final savePath = '${dir.path}/$safeName';

      // Unduh file
      await dio.download(url, savePath);

      messenger.hideCurrentSnackBar();

      final file = File(savePath);
      if (await file.exists()) {
        final result = await OpenFilex.open(savePath);

        if (result.type != ResultType.done) {
          // Jika gagal membuka via viewer lokal, fallback ke url_launcher browser
          final uri = Uri.parse(url);
          if (await canLaunchUrl(uri)) {
            await launchUrl(uri, mode: LaunchMode.externalApplication);
          }
        }

        messenger.showSnackBar(
          SnackBar(
            backgroundColor: const Color(0xFF059669),
            content: Row(
              children: [
                const Icon(
                  Icons.check_circle_rounded,
                  color: Colors.white,
                  size: 20,
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Text(
                    'Berhasil membuka: $safeName',
                    style: const TextStyle(fontSize: 13, color: Colors.white),
                  ),
                ),
              ],
            ),
            duration: const Duration(seconds: 3),
          ),
        );
      }
    } catch (e) {
      messenger.hideCurrentSnackBar();

      // Fallback langsung ke URL Launcher browser jika download lokal ada kendala
      try {
        final uri = Uri.parse(url);
        if (await canLaunchUrl(uri)) {
          await launchUrl(uri, mode: LaunchMode.externalApplication);
          messenger.showSnackBar(
            const SnackBar(
              content: Text('Membuka dokumen di peramban eksternal...'),
              duration: Duration(seconds: 2),
            ),
          );
          return;
        }
      } catch (_) {}

      messenger.showSnackBar(
        SnackBar(
          backgroundColor: const Color(0xFFE11D48),
          content: Text('Gagal mengunduh file: $e'),
          duration: const Duration(seconds: 4),
        ),
      );
    }
  }

  /// Membuka langsung via browser / PDF viewer eksternal
  static Future<void> openExternal(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }
}
