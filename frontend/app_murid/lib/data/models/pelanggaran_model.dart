import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';

class PoinFormatter {
  static String format(double val) {
    final rounded = double.parse(val.toStringAsFixed(2));
    if (rounded % 1 == 0) {
      return rounded.toInt().toString();
    }
    String str = rounded.toStringAsFixed(2);
    while (str.endsWith('0')) {
      str = str.substring(0, str.length - 1);
    }
    if (str.endsWith('.')) {
      str = str.substring(0, str.length - 1);
    }
    return str;
  }
}

class PelanggaranItemModel {
  final int id;
  final String tanggal;
  final String hari;
  final String kasus;
  final String kategori;
  final double poin;
  final String keterangan;
  final String? ruanganNama;
  final String diinputOleh;

  PelanggaranItemModel({
    required this.id,
    required this.tanggal,
    this.hari = '',
    required this.kasus,
    required this.kategori,
    required this.poin,
    required this.keterangan,
    this.ruanganNama,
    this.diinputOleh = 'Ustadz / Pengajar',
  });

  String get poinFormatted => PoinFormatter.format(poin);

  bool get isRingan => kategori.toLowerCase() == 'ringan';
  bool get isSedang => kategori.toLowerCase() == 'sedang';
  bool get isBerat => kategori.toLowerCase() == 'berat';

  Color get badgeColor {
    if (isBerat) return AppColors.roseDanger;
    if (isSedang) return AppColors.amberAccent;
    return AppColors.skyBlueAccent;
  }

  factory PelanggaranItemModel.fromJson(Map<String, dynamic> json) {
    return PelanggaranItemModel(
      id: json['id'] is int ? json['id'] : int.tryParse('${json['id']}') ?? 0,
      tanggal: json['tanggal']?.toString() ?? '-',
      hari: json['hari']?.toString() ?? '',
      kasus: json['kasus']?.toString() ?? 'Pelanggaran Tata Tertib',
      kategori: json['kategori']?.toString() ?? 'Ringan',
      poin:
          (json['poin'] as num?)?.toDouble() ??
          double.tryParse('${json['poin']}') ??
          0.0,
      keterangan: json['keterangan']?.toString() ?? '-',
      ruanganNama: json['ruangan_nama']?.toString(),
      diinputOleh: json['diinput_oleh']?.toString() ?? 'Ustadz / Pengajar',
    );
  }
}

class PelanggaranRincianModel {
  final int ringan;
  final int sedang;
  final int berat;

  PelanggaranRincianModel({
    required this.ringan,
    required this.sedang,
    required this.berat,
  });

  factory PelanggaranRincianModel.fromJson(Map<String, dynamic>? json) {
    if (json == null) {
      return PelanggaranRincianModel(ringan: 0, sedang: 0, berat: 0);
    }
    return PelanggaranRincianModel(
      ringan: (json['ringan'] as num?)?.toInt() ?? 0,
      sedang: (json['sedang'] as num?)?.toInt() ?? 0,
      berat: (json['berat'] as num?)?.toInt() ?? 0,
    );
  }
}

class RekapPelanggaranAnakModel {
  final double totalPoin;
  final int totalKasus;
  final PelanggaranRincianModel rincian;
  final List<PelanggaranItemModel> riwayat;

  RekapPelanggaranAnakModel({
    required this.totalPoin,
    required this.totalKasus,
    required this.rincian,
    required this.riwayat,
  });

  String get totalPoinFormatted => PoinFormatter.format(totalPoin);

  String get statusKedisiplinan {
    if (totalPoin <= 0 && totalKasus == 0) {
      return 'Sangat Baik & Bersih';
    } else if (totalPoin <= 15) {
      return 'Perlu Perhatian';
    } else if (totalPoin <= 30) {
      return 'Peringatan Pembinaan';
    } else {
      return 'Peringatan Keras';
    }
  }

  Color get statusColor {
    if (totalPoin <= 0 && totalKasus == 0) {
      return const Color(0xFF10B981);
    } else if (totalPoin <= 15) {
      return AppColors.amberAccent;
    } else {
      return AppColors.roseDanger;
    }
  }

  factory RekapPelanggaranAnakModel.fromJson(Map<String, dynamic> json) {
    final rawRiwayat = json['riwayat'] as List<dynamic>? ?? [];
    return RekapPelanggaranAnakModel(
      totalPoin:
          (json['total_poin'] as num?)?.toDouble() ??
          double.tryParse('${json['total_poin']}') ??
          0.0,
      totalKasus: json['total_kasus'] is int
          ? json['total_kasus']
          : int.tryParse('${json['total_kasus']}') ?? 0,
      rincian: PelanggaranRincianModel.fromJson(
        json['rincian'] as Map<String, dynamic>?,
      ),
      riwayat: rawRiwayat
          .map((e) => PelanggaranItemModel.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }
}
