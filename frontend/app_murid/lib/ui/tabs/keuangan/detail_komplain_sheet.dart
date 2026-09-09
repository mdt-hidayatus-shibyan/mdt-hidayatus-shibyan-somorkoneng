import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../data/models/tabungan_model.dart';
import '../../../providers/keuangan_provider.dart';
import '../../widgets/glass_card.dart';

class DetailKomplainSheet extends StatelessWidget {
  final int anakId;
  final String namaAnak;
  final TransaksiTabunganAnak transaksi;
  final TabunganKomplainModel komplain;

  const DetailKomplainSheet({
    super.key,
    required this.anakId,
    required this.namaAnak,
    required this.transaksi,
    required this.komplain,
  });

  Future<void> _batalkanKomplain(BuildContext context) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Batalkan Komplain?'),
        content: const Text(
          'Apakah Anda yakin ingin membatalkan pengajuan sanggahan setoran ini?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Kembali'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.roseDanger,
              foregroundColor: Colors.white,
            ),
            child: const Text('Ya, Batalkan'),
          ),
        ],
      ),
    );

    if (confirm != true || !context.mounted) return;

    HapticHelper.medium();
    final provider = context.read<KeuanganProvider>();
    final res = await provider.batalkanKomplain(
      anakId: anakId,
      komplainId: komplain.id,
    );

    if (!context.mounted) return;

    if (res['success'] == true) {
      HapticHelper.medium();
      Navigator.pop(context, true);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Komplain berhasil dibatalkan.'),
          backgroundColor: const Color(0xFF10B981),
        ),
      );
    } else {
      HapticHelper.error();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Gagal membatalkan komplain.'),
          backgroundColor: AppColors.roseDanger,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final isSubmitting = context.watch<KeuanganProvider>().isSubmittingKomplain;

    Color statusBg = Colors.grey.withValues(alpha: 0.15);
    Color statusColor = Colors.grey;
    IconData statusIcon = Icons.info_outline_rounded;
    String statusTitle = komplain.status;

    if (komplain.isPending) {
      statusBg = AppColors.amberAccent.withValues(alpha: 0.15);
      statusColor = const Color(0xFFD97706);
      statusIcon = Icons.hourglass_top_rounded;
      statusTitle = 'Menunggu Verifikasi Pengelola';
    } else if (komplain.isDisetujui) {
      statusBg = const Color(0xFF10B981).withValues(alpha: 0.15);
      statusColor = const Color(0xFF10B981);
      statusIcon = Icons.check_circle_rounded;
      statusTitle = 'Komplain Disetujui (Saldo Disesuaikan)';
    } else if (komplain.isDitolak) {
      statusBg = AppColors.roseDanger.withValues(alpha: 0.15);
      statusColor = AppColors.roseDanger;
      statusIcon = Icons.cancel_rounded;
      statusTitle = 'Komplain Ditolak';
    }

    return Container(
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF141F16) : Colors.white,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(28)),
      ),
      padding: EdgeInsets.only(
        left: 20,
        right: 20,
        top: 16,
        bottom: MediaQuery.of(context).viewInsets.bottom + 24,
      ),
      child: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Handle Bar
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: isDark ? Colors.white24 : Colors.black12,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Header Section
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: statusBg,
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: Icon(statusIcon, color: statusColor, size: 22),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        statusTitle,
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w900,
                          color: statusColor,
                        ),
                      ),
                      Text(
                        'Kode: ${komplain.kodeKomplain}',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          fontFamily: 'monospace',
                          color: isDark ? Colors.white60 : Colors.black54,
                        ),
                      ),
                    ],
                  ),
                ),
                IconButton(
                  onPressed: () => Navigator.pop(context),
                  icon: const Icon(Icons.close_rounded, size: 20),
                ),
              ],
            ),
            const SizedBox(height: 14),
            const Divider(height: 1),
            const SizedBox(height: 14),

            // Kartu Komparasi Nominal
            GlassCard(
              padding: const EdgeInsets.all(16),
              borderRadius: 20,
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Tercatat di Sistem',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: isDark ? Colors.white54 : Colors.black45,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            CurrencyFormatter.format(komplain.nominalTercatat),
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
                              decoration: TextDecoration.lineThrough,
                              color: Colors.grey,
                            ),
                          ),
                        ],
                      ),
                      const Icon(
                        Icons.arrow_forward_rounded,
                        color: Colors.grey,
                        size: 18,
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const Text(
                            'Klaim Wali Murid',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF10B981),
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            CurrencyFormatter.format(komplain.nominalKlaim),
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w900,
                              color: Color(0xFF10B981),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  const Divider(height: 1),
                  const SizedBox(height: 10),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Selisih Saldo:',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: isDark ? Colors.white70 : Colors.black87,
                        ),
                      ),
                      Text(
                        '${komplain.selisih >= 0 ? "+" : ""}${CurrencyFormatter.format(komplain.selisih)}',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w900,
                          color: komplain.selisih >= 0
                              ? const Color(0xFF10B981)
                              : AppColors.roseDanger,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 14),

            // Alasan Pengajuan
            Text(
              'ALASAN / PENJELASAN WALI MURID',
              style: TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w900,
                letterSpacing: 0.8,
                color: isDark ? Colors.white60 : Colors.black54,
              ),
            ),
            const SizedBox(height: 6),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: isDark
                    ? Colors.white.withValues(alpha: 0.04)
                    : Colors.black.withValues(alpha: 0.03),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Text(
                komplain.alasan,
                style: const TextStyle(fontSize: 12, height: 1.4),
              ),
            ),
            const SizedBox(height: 14),

            // Tanggapan Verifikasi Pengelola (jika sudah ada keputusan)
            if (komplain.catatanVerifikasi != null &&
                komplain.catatanVerifikasi!.isNotEmpty) ...[
              Text(
                'TANGGAPAN ADMIN / BENDAHARA',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  letterSpacing: 0.8,
                  color: statusColor,
                ),
              ),
              const SizedBox(height: 6),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: statusBg,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: statusColor.withValues(alpha: 0.3)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      komplain.catatanVerifikasi!,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        color: isDark ? Colors.white : Colors.black87,
                      ),
                    ),
                    if (komplain.diverifikasiPada != null) ...[
                      const SizedBox(height: 6),
                      Text(
                        'Diverifikasi pada: ${komplain.diverifikasiPada} • Oleh: ${komplain.petugasVerifikasi ?? "Admin"}',
                        style: TextStyle(
                          fontSize: 10,
                          color: isDark ? Colors.white54 : Colors.black54,
                        ),
                      ),
                    ],
                  ],
                ),
              ),
              const SizedBox(height: 14),
            ],

            // Tombol Batalkan Komplain (hanya jika masih pending)
            if (komplain.isPending) ...[
              const SizedBox(height: 8),
              SizedBox(
                width: double.infinity,
                height: 46,
                child: OutlinedButton.icon(
                  onPressed: isSubmitting
                      ? null
                      : () => _batalkanKomplain(context),
                  icon: const Icon(Icons.cancel_outlined, size: 16),
                  label: isSubmitting
                      ? const SizedBox(
                          width: 18,
                          height: 18,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Text(
                          'Batalkan Pengajuan Sanggahan',
                          style: TextStyle(fontWeight: FontWeight.bold),
                        ),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppColors.roseDanger,
                    side: const BorderSide(color: AppColors.roseDanger),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
