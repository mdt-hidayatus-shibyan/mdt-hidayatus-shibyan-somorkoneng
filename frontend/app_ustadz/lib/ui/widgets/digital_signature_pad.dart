import 'dart:convert';
import 'dart:typed_data';
import 'dart:ui' as ui;
import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/utils/haptic_helper.dart';

class StrokeLine {
  final List<Offset> points;
  final Color color;
  final double strokeWidth;

  StrokeLine({
    required this.points,
    required this.color,
    this.strokeWidth = 3.0,
  });
}

class DigitalSignaturePainter extends CustomPainter {
  final List<StrokeLine> lines;
  final Color? backgroundColor;

  DigitalSignaturePainter({required this.lines, this.backgroundColor});

  @override
  void paint(Canvas canvas, Size size) {
    if (backgroundColor != null) {
      final bgPaint = Paint()..color = backgroundColor!;
      canvas.drawRect(Rect.fromLTWH(0, 0, size.width, size.height), bgPaint);
    }

    // Grid garis bantu tanda tangan halus di sepertiga bawah
    final linePaint = Paint()
      ..color = Colors.grey.withValues(alpha: 0.25)
      ..strokeWidth = 1.0
      ..style = PaintingStyle.stroke;

    final guidelineY = size.height * 0.75;
    canvas.drawLine(
      Offset(20, guidelineY),
      Offset(size.width - 20, guidelineY),
      linePaint,
    );

    for (final line in lines) {
      if (line.points.isEmpty) continue;

      final paint = Paint()
        ..color = line.color
        ..strokeCap = StrokeCap.round
        ..strokeJoin = StrokeJoin.round
        ..strokeWidth = line.strokeWidth
        ..style = PaintingStyle.stroke;

      if (line.points.length == 1) {
        canvas.drawCircle(line.points.first, line.strokeWidth / 2, paint);
        continue;
      }

      final path = Path();
      path.moveTo(line.points.first.dx, line.points.first.dy);

      for (int i = 1; i < line.points.length; i++) {
        final p0 = line.points[i - 1];
        final p1 = line.points[i];
        // Smooth quadratic bezier curve
        final mid = Offset((p0.dx + p1.dx) / 2, (p0.dy + p1.dy) / 2);
        path.quadraticBezierTo(p0.dx, p0.dy, mid.dx, mid.dy);
      }

      canvas.drawPath(path, paint);
    }
  }

  @override
  bool shouldRepaint(covariant DigitalSignaturePainter oldDelegate) => true;
}

class DigitalSignaturePad extends StatefulWidget {
  final ValueChanged<String>? onExportBase64;
  final ValueChanged<Uint8List>? onExportBytes;
  final double height;

  const DigitalSignaturePad({
    super.key,
    this.onExportBase64,
    this.onExportBytes,
    this.height = 200,
  });

  @override
  State<DigitalSignaturePad> createState() => DigitalSignaturePadState();
}

class DigitalSignaturePadState extends State<DigitalSignaturePad> {
  final List<StrokeLine> _lines = [];
  StrokeLine? _currentLine;
  final GlobalKey _canvasKey = GlobalKey();

  bool get isEmpty => _lines.isEmpty;

  void clear() {
    HapticHelper.light();
    setState(() {
      _lines.clear();
      _currentLine = null;
    });
  }

  void undo() {
    if (_lines.isNotEmpty) {
      HapticHelper.selection();
      setState(() {
        _lines.removeLast();
      });
    }
  }

  Future<Uint8List?> exportBytes({int width = 500, int height = 240}) async {
    if (_lines.isEmpty) return null;

    final recorder = ui.PictureRecorder();
    final canvas = Canvas(
      recorder,
      Rect.fromPoints(
        const Offset(0, 0),
        Offset(width.toDouble(), height.toDouble()),
      ),
    );

    // Scale drawing from widget render box to export dimensions
    final renderBox =
        _canvasKey.currentContext?.findRenderObject() as RenderBox?;
    final actualSize =
        renderBox?.size ?? Size(width.toDouble(), height.toDouble());

    final scaleX = width / actualSize.width;
    final scaleY = height / actualSize.height;

    canvas.scale(scaleX, scaleY);

    // Draw lines without background for transparent PNG
    final painter = DigitalSignaturePainter(lines: _lines);
    painter.paint(canvas, actualSize);

    final picture = recorder.endRecording();
    final img = await picture.toImage(width, height);
    final byteData = await img.toByteData(format: ui.ImageByteFormat.png);

    return byteData?.buffer.asUint8List();
  }

  Future<String?> exportBase64({int width = 500, int height = 240}) async {
    final bytes = await exportBytes(width: width, height: height);
    if (bytes == null) return null;
    return 'data:image/png;base64,${base64Encode(bytes)}';
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final penColor = isDark ? Colors.white : const Color(0xFF1B2E1B);

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        // CANVAS CONTAINER
        ClipRRect(
          borderRadius: BorderRadius.circular(18),
          child: Container(
            key: _canvasKey,
            height: widget.height,
            width: double.infinity,
            decoration: BoxDecoration(
              color: isDark ? const Color(0xFF162016) : const Color(0xFFF8FAF7),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(
                color: isDark
                    ? const Color(0xFF263326)
                    : const Color(0xFFD1D5DB),
                width: 1.5,
              ),
            ),
            child: Stack(
              children: [
                // WATERMARK PETUNJUK
                if (_lines.isEmpty)
                  Center(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          Icons.draw_rounded,
                          size: 32,
                          color: isDark
                              ? Colors.white.withValues(alpha: 0.15)
                              : Colors.black.withValues(alpha: 0.15),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'Goreskan tanda tangan Anda di sini',
                          style: TextStyle(
                            fontSize: 13,
                            color: isDark
                                ? Colors.white.withValues(alpha: 0.25)
                                : Colors.black.withValues(alpha: 0.3),
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),

                // GESTURE DETECTOR UNTUK MENGGAMBAR
                GestureDetector(
                  onPanStart: (details) {
                    final localPos = details.localPosition;
                    _currentLine = StrokeLine(
                      points: [localPos],
                      color: penColor,
                      strokeWidth: 3.0,
                    );
                    setState(() {
                      _lines.add(_currentLine!);
                    });
                  },
                  onPanUpdate: (details) {
                    final localPos = details.localPosition;
                    if (_currentLine != null) {
                      setState(() {
                        _currentLine!.points.add(localPos);
                      });
                    }
                  },
                  onPanEnd: (_) {
                    _currentLine = null;
                  },
                  child: CustomPaint(
                    painter: DigitalSignaturePainter(lines: _lines),
                    size: Size.infinite,
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 10),

        // CONTROL BUTTONS: CLEAR & UNDO
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            TextButton.icon(
              onPressed: _lines.isEmpty ? null : undo,
              icon: const Icon(Icons.undo_rounded, size: 16),
              label: const Text('Urungkan (Undo)'),
              style: TextButton.styleFrom(
                foregroundColor: isDark
                    ? const Color(0xFF8D9387)
                    : const Color(0xFF64748B),
              ),
            ),
            TextButton.icon(
              onPressed: _lines.isEmpty ? null : clear,
              icon: const Icon(Icons.refresh_rounded, size: 16),
              label: const Text('Bersihkan'),
              style: TextButton.styleFrom(
                foregroundColor: AppColors.roseDanger,
              ),
            ),
          ],
        ),
      ],
    );
  }
}
