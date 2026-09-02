import 'package:flutter/material.dart';
import '../../../core/theme/app_colors.dart';
import '../../widgets/segmented_tab_bar.dart';
import 'input_nilai_tab_view.dart';
import 'presensi_ujian_tab_view.dart';

class UjianTab extends StatefulWidget {
  const UjianTab({super.key});

  @override
  State<UjianTab> createState() => _UjianTabState();
}

class _UjianTabState extends State<UjianTab>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        setState(() {});
      }
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Ujian Madrasah',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
      ),
      body: Column(
        children: [
          // Segmented Navigation Pill
          SegmentedTabBar(
            selectedIndex: _tabController.index,
            onTabChanged: (idx) {
              _tabController.animateTo(idx);
              setState(() {});
            },
            items: const [
              SegmentedTabItem(
                activeIcon: Icons.fact_check_rounded,
                inactiveIcon: Icons.fact_check_outlined,
                label: 'Presensi Ujian',
                activeColor: AppColors.primaryLight,
              ),
              SegmentedTabItem(
                activeIcon: Icons.edit_document,
                inactiveIcon: Icons.edit_note_rounded,
                label: 'Input Nilai',
                activeColor: AppColors.violetAccent,
              ),
            ],
          ),

          // Tab Views
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: const [PresensiUjianTabView(), InputNilaiTabView()],
            ),
          ),
        ],
      ),
    );
  }
}
