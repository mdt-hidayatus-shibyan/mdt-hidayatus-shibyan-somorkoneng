import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../data/models/tabungan_model.dart';
import '../../../providers/keuangan_provider.dart';
import '../../widgets/glass_card.dart';

class FormKomplainSetoranSheet extends StatefulWidget {
  final int anakId;
  final String namaAnak;
  final TransaksiTabunganAnak transaksi;

  const FormKomplainSetoranSheet({
    super.key,
    required this.anakId,
    required this.namaAnak,
    required this.transaksi,
  });

  @override
  State<FormKomplainSetoranSheet> createState() =>
      _FormKomplainSetoranSheetState();
}

class _FormKomplainSetoranSheetState extends State<FormKomplainSetoranSheet> {
  final _formKey = GlobalKey<FormState>();
  final _nominalController = TextEditingController();
  final _alasanController = TextEditingController();

  int _nominalKlaim = 0;

  @override
  void initState() {
    super.initState();
    // Default saran nominal klaim jika kosong
    _nominalController.addListener(() {
      final clean = _nominalController.text.replaceAll(RegExp(r'[^0-9]'), '');
      setState(() {
        _nominalKlaim = int.tryParse(clean) ?? 0;
      });
    });
  }

  @override
  void dispose() {
    _nominalController.dispose();
    _alasanController.dispose();
    super.dispose();
  }

  void _setNominal(int value) {
    HapticHelper.selection();
    _nominalController.text = value.toString();
  }

  int get _selisih => _nominalKlaim - widget.transaksi.nominal;

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) {
      HapticHelper.error();
      return;
    }

    if (_nominalKlaim <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Nominal klaim harus lebih besar dari Rp 0.'),
          backgroundColor: AppColors.roseDanger,
        ),
      );
      HapticHelper.error();
      return;
    }

    if (_nominalKlaim == widget.transaksi.nominal) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Nominal klaim sama dengan nominal yang sudah tercatat (${CurrencyFormatter.format(widget.transaksi.nominal)}).',
          ),
          backgroundColor: AppColors.roseDanger,
        ),
      );
      HapticHelper.error();
      return;
    }

    HapticHelper.medium();

    final provider = context.read<KeuanganProvider>();
    final res = await provider.ajukanKomplain(
      anakId: widget.anakId,
      transaksiId: widget.transaksi.id,
      nominalKlaim: _nominalKlaim,
      alasan: _alasanController.text.trim(),
    );

    if (!mounted) return;

    if (res['success'] == true) {
      HapticHelper.medium();
      Navigator.pop(context, true);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Komplain berhasil diajukan.'),
          backgroundColor: const Color(0xFF10B981),
        ),
      );
    } else {
      HapticHelper.error();
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Gagal mengajukan komplain.'),
          backgroundColor: AppColors.roseDanger,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final isSubmitting = context.watch<KeuanganProvider>().isSubmittingKomplain;

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
      child: Form(
        key: _formKey,
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

              // Title & Header
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: AppColors.amberAccent.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: const Icon(
                      Icons.shield_outlined,
                      color: Color(0xFFD97706),
                      size: 22,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Sanggahan Setor Tunai',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                        Text(
                          'Murid: ${widget.namaAnak}',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
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

              // Kartu Rincian Transaksi Asli
              GlassCard(
                padding: const EdgeInsets.all(14),
                borderRadius: 18,
                child: Column(
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'KODE TRANSAKSI',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.w800,
                            color: isDark ? Colors.white54 : Colors.black45,
                          ),
                        ),
                        Text(
                          widget.transaksi.kodeTransaksi,
                          style: const TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w900,
                            fontFamily: 'monospace',
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Tanggal Setor:',
                          style: TextStyle(
                            fontSize: 11,
                            color: isDark ? Colors.white60 : Colors.black54,
                          ),
                        ),
                        Text(
                          '${widget.transaksi.tanggal} • ${widget.transaksi.petugas}',
                          style: const TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Nominal Tercatat di Sistem:',
                          style: TextStyle(
                            fontSize: 11,
                            color: isDark ? Colors.white60 : Colors.black54,
                          ),
                        ),
                        Text(
                          CurrencyFormatter.format(widget.transaksi.nominal),
                          style: const TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w900,
                            color: AppColors.roseDanger,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              // Input Nominal Klaim
              Text(
                'NOMINAL SEBENARNYA YANG DISETOR',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  letterSpacing: 0.8,
                  color: isDark ? Colors.white60 : Colors.black54,
                ),
              ),
              const SizedBox(height: 6),
              TextFormField(
                controller: _nominalController,
                keyboardType: TextInputType.number,
                autofocus: true,
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w900,
                ),
                decoration: InputDecoration(
                  prefixText: 'Rp ',
                  prefixStyle: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w900,
                    color: isDark
                        ? AppColors.primaryDark
                        : AppColors.primaryLight,
                  ),
                  hintText: '0',
                  filled: true,
                  fillColor: isDark
                      ? Colors.white.withValues(alpha: 0.05)
                      : Colors.black.withValues(alpha: 0.04),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: BorderSide.none,
                  ),
                ),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) {
                    return 'Nominal setoran wajib diisi';
                  }
                  final num = int.tryParse(
                    val.replaceAll(RegExp(r'[^0-9]'), ''),
                  );
                  if (num == null || num <= 0) {
                    return 'Masukkan nominal yang valid';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 8),

              // Preset Nominal Chips
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: [
                    _buildQuickChip(5000),
                    _buildQuickChip(10000),
                    _buildQuickChip(15000),
                    _buildQuickChip(20000),
                    _buildQuickChip(50000),
                    _buildQuickChip(100000),
                  ],
                ),
              ),
              const SizedBox(height: 12),

              // Kalkulasi Selisih Box
              if (_nominalKlaim > 0) ...[
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 14,
                    vertical: 10,
                  ),
                  decoration: BoxDecoration(
                    color: _selisih >= 0
                        ? const Color(0xFF10B981).withValues(alpha: 0.12)
                        : AppColors.roseDanger.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(
                      color: _selisih >= 0
                          ? const Color(0xFF10B981).withValues(alpha: 0.3)
                          : AppColors.roseDanger.withValues(alpha: 0.3),
                    ),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Icon(
                            _selisih >= 0
                                ? Icons.add_circle_outline_rounded
                                : Icons.remove_circle_outline_rounded,
                            size: 16,
                            color: _selisih >= 0
                                ? const Color(0xFF10B981)
                                : AppColors.roseDanger,
                          ),
                          const SizedBox(width: 6),
                          const Text(
                            'Selisih Koreksi Saldo:',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                      Text(
                        '${_selisih >= 0 ? "+" : ""}${CurrencyFormatter.format(_selisih)}',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w900,
                          color: _selisih >= 0
                              ? const Color(0xFF10B981)
                              : AppColors.roseDanger,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 14),
              ],

              // Input Alasan / Kronologi
              Text(
                'PENJELASAN / KRONOLOGI SANGGAHAN',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  letterSpacing: 0.8,
                  color: isDark ? Colors.white60 : Colors.black54,
                ),
              ),
              const SizedBox(height: 6),
              TextFormField(
                controller: _alasanController,
                maxLines: 3,
                style: const TextStyle(fontSize: 13),
                decoration: InputDecoration(
                  hintText:
                      'Misal: Anak saya menyetor uang pecahan Rp 5.000 ke ustadz saat masuk ruangan, namun di aplikasi tercatat Rp 2.000.',
                  hintStyle: TextStyle(
                    fontSize: 11,
                    color: isDark ? Colors.white38 : Colors.black38,
                  ),
                  filled: true,
                  fillColor: isDark
                      ? Colors.white.withValues(alpha: 0.05)
                      : Colors.black.withValues(alpha: 0.04),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(16),
                    borderSide: BorderSide.none,
                  ),
                ),
                validator: (val) {
                  if (val == null || val.trim().length < 5) {
                    return 'Alasan sanggahan minimal 5 karakter';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),

              // Catatan Informasi
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(
                    Icons.info_outline_rounded,
                    size: 15,
                    color: Color(0xFFD97706),
                  ),
                  const SizedBox(width: 6),
                  Expanded(
                    child: Text(
                      'Pengajuan komplain akan diverifikasi langsung oleh Admin / Bendahara Madrasah. Saldo tabungan murid akan disesuaikan setelah disetujui.',
                      style: TextStyle(
                        fontSize: 10,
                        color: isDark ? Colors.white54 : Colors.black54,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 18),

              // Submit Button
              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton(
                  onPressed: isSubmitting ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: isDark
                        ? AppColors.primaryDark
                        : AppColors.primaryLight,
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(18),
                    ),
                    elevation: 0,
                  ),
                  child: isSubmitting
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(
                            strokeWidth: 2.5,
                            color: Colors.white,
                          ),
                        )
                      : const Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.send_rounded, size: 18),
                            SizedBox(width: 8),
                            Text(
                              'Kirim Pengajuan Sanggahan',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ],
                        ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildQuickChip(int amount) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final isSelected = _nominalKlaim == amount;

    return Padding(
      padding: const EdgeInsets.only(right: 6),
      child: InkWell(
        onTap: () => _setNominal(amount),
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
          decoration: BoxDecoration(
            color: isSelected
                ? (isDark ? AppColors.primaryDark : AppColors.primaryLight)
                : (isDark
                      ? Colors.white.withValues(alpha: 0.06)
                      : Colors.black.withValues(alpha: 0.04)),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Text(
            CurrencyFormatter.format(amount),
            style: TextStyle(
              fontSize: 11,
              fontWeight: isSelected ? FontWeight.w900 : FontWeight.w600,
              color: isSelected
                  ? Colors.white
                  : (isDark ? Colors.white70 : Colors.black87),
            ),
          ),
        ),
      ),
    );
  }
}
