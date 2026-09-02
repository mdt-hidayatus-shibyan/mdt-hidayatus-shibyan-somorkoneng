import 'package:flutter/material.dart';
import '../ujian/input_nilai_tab_view.dart';

class PenilaianTab extends StatelessWidget {
  const PenilaianTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Input Nilai Ujian',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
      ),
      body: const InputNilaiTabView(),
    );
  }
}
