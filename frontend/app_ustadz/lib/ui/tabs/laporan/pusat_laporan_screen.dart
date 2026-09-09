import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
import '../../../core/utils/haptic_helper.dart';
import '../../../data/models/laporan_model.dart';
import '../../../providers/laporan_provider.dart';
import '../../widgets/glass_card.dart';
import '../../widgets/segmented_tab_bar.dart';
import '../../widgets/shimmer_loading.dart';

class PusatLaporanScreen extends StatefulWidget {
  final int? initialRuanganId;
  final int initialTabIndex;

  const PusatLaporanScreen({
    super.key,
    this.initialRuanganId,
    this.initialTabIndex = 0,
  });

  @override
  State<PusatLaporanScreen> createState() => _PusatLaporanScreenState();
}

class _PusatLaporanScreenState extends State<PusatLaporanScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  int? _selectedRuanganId;

  // Filter Tab 1: Presensi Murid
  int? _selectedBulanPresensiId;
  String _selectedSemesterPresensi = 'Semua';
  String _predikatPresensiFilter = 'Semua';
  String _sortPresensiMurid = 'Nama (A-Z)';
  String _searchPresensiQuery = '';

  // Filter Tab 2: Presensi Ustadz
  int? _selectedUstadzId;
  int? _selectedBulanUstadzId;
  String _statusPresensiUstadz = 'Semua';
  String _searchPresensiUstadzQuery = '';

  // Filter Tab 3: Pelanggaran Murid (Buku Kasus)
  String _statusDisiplinFilter = 'Semua';
  String _sortPelanggaran = 'Paling Banyak Poin';
  String _searchPelanggaranQuery = '';

  // Filter Tab 4: Ujian & Leger
  int? _selectedUjianId;
  String _statusKetuntasanUjian = 'Semua';
  String _sortUjian = 'Peringkat';
  String _searchUjianQuery = '';

  // Filter Tab 5: Kenaikan Kelas & Kelulusan
  String _statusKenaikanFilter = 'Semua';
  String _sortKenaikan = 'Nilai Tertinggi';
  String _searchKenaikanQuery = '';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(
      length: 5,
      vsync: this,
      initialIndex: widget.initialTabIndex.clamp(0, 4),
    );
    _tabController.addListener(() {
      if (mounted) setState(() {});
    });

    _selectedRuanganId = widget.initialRuanganId;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadCurrentTabData();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  void _loadCurrentTabData() {
    final provider = context.read<LaporanProvider>();
    provider.fetchPresensiMurid(
      ruanganId: _selectedRuanganId,
      bulanHijriyahId: _selectedBulanPresensiId,
      semester: _selectedSemesterPresensi == 'Semua'
          ? null
          : (_selectedSemesterPresensi == 'Semester 1' ? '1' : '2'),
    );
    provider.fetchPresensiUstadz(
      ruanganId: _selectedRuanganId,
      ustadzId: _selectedUstadzId,
      bulanHijriyahId: _selectedBulanUstadzId,
      status: _statusPresensiUstadz,
    );
    provider.fetchPelanggaranMurid(ruanganId: _selectedRuanganId);
    provider.fetchUjian(
      ruanganId: _selectedRuanganId,
      ujianId: _selectedUjianId,
    );
    provider.fetchKenaikanKelas(ruanganId: _selectedRuanganId);
  }

  // =========================================================================
  // TAB 1: LAPORAN PRESENSI MURID
  // =========================================================================
  Widget _buildPresensiMuridTab(bool isDark, LaporanProvider provider) {
    final data = provider.presensiMurid;
    if (provider.isLoadingPresensiMurid) {
      return const ShimmerLoadingList(count: 4, height: 110);
    }
    if (data == null) {
      return GlassCard(
        margin: const EdgeInsets.all(16),
        padding: const EdgeInsets.all(24),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.info_outline_rounded,
                size: 40,
                color: Colors.grey,
              ),
              const SizedBox(height: 12),
              Text(
                provider.errorPresensiMurid ??
                    'Gagal memuat laporan presensi murid.',
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              ElevatedButton.icon(
                onPressed: () => provider.fetchPresensiMurid(
                  ruanganId: _selectedRuanganId,
                  bulanHijriyahId: _selectedBulanPresensiId,
                ),
                icon: const Icon(Icons.refresh_rounded, size: 16),
                label: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }

    var filteredMurid = data.rekapMurid.where((m) {
      final matchesSearch =
          m.nama.toLowerCase().contains(_searchPresensiQuery.toLowerCase()) ||
          m.nism.contains(_searchPresensiQuery);

      final matchesPredikat =
          _predikatPresensiFilter == 'Semua' ||
          m.predikat.toLowerCase() == _predikatPresensiFilter.toLowerCase();

      return matchesSearch && matchesPredikat;
    }).toList();

    // Sorting
    if (_sortPresensiMurid == 'Nama (A-Z)') {
      filteredMurid.sort((a, b) => a.nama.compareTo(b.nama));
    } else if (_sortPresensiMurid == 'Kehadiran Terendah') {
      filteredMurid.sort(
        (a, b) => a.persentaseKehadiran.compareTo(b.persentaseKehadiran),
      );
    } else if (_sortPresensiMurid == 'Kehadiran Tertinggi') {
      filteredMurid.sort(
        (a, b) => b.persentaseKehadiran.compareTo(a.persentaseKehadiran),
      );
    } else if (_sortPresensiMurid == 'Paling Banyak Alpha') {
      filteredMurid.sort((a, b) => b.alphaCount.compareTo(a.alphaCount));
    }

    return RefreshIndicator(
      onRefresh: () async => provider.fetchPresensiMurid(
        ruanganId: _selectedRuanganId,
        bulanHijriyahId: _selectedBulanPresensiId,
        semester: _selectedSemesterPresensi == 'Semua'
            ? null
            : (_selectedSemesterPresensi == 'Semester 1' ? '1' : '2'),
      ),
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 40),
        children: [
          // 1. Ringkasan Presensi Kelas
          GlassCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: AppColors.primaryLight.withValues(
                                alpha: 0.12,
                              ),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: const Icon(
                              Icons.fact_check_rounded,
                              color: AppColors.primaryLight,
                              size: 20,
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Presensi ${data.namaRuangan}',
                                  style: const TextStyle(
                                    fontSize: 15,
                                    fontWeight: FontWeight.bold,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                                Text(
                                  'Tahun: ${data.tahunPelajaran} • ${data.totalHariEfektif} Hari Efektif',
                                  style: TextStyle(
                                    fontSize: 11,
                                    color: isDark
                                        ? const Color(0xFF8D9387)
                                        : const Color(0xFF73796E),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: isDark
                            ? AppColors.primaryContainerDark
                            : AppColors.primaryContainerLight,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        '${data.persentaseKehadiranKelas}% Hadir',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: isDark
                              ? AppColors.primaryDark
                              : AppColors.primaryLight,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // 4 Kotak H / I / S / A
                Row(
                  children: [
                    _buildStatBox(
                      'Hadir',
                      '${data.totalHadir}',
                      AppColors.hadirTextLight,
                      isDark ? AppColors.hadirBgDark : AppColors.hadirBgLight,
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Izin',
                      '${data.totalIzin}',
                      AppColors.skyBlueAccent,
                      isDark
                          ? const Color(0xFF0C243B)
                          : const Color(0xFFE0F2FE),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Sakit',
                      '${data.totalSakit}',
                      AppColors.amberAccent,
                      isDark
                          ? const Color(0xFF382305)
                          : const Color(0xFFFEF3C7),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Alpha',
                      '${data.totalAlpha}',
                      AppColors.roseDanger,
                      isDark
                          ? const Color(0xFF380C14)
                          : const Color(0xFFFFE4E6),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),

          // Banner Keterangan Presensi Ujian Murid
          GlassCard(
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: AppColors.violetAccent.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(
                    Icons.assignment_turned_in_rounded,
                    size: 18,
                    color: AppColors.violetAccent,
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Presensi Masa Ujian Murid',
                        style: TextStyle(
                          fontSize: 12.5,
                          fontWeight: FontWeight.bold,
                          color: isDark
                              ? Colors.white
                              : const Color(0xFF581C87),
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        'Kehadiran pada hari ujian madrasah dicatat otomatis melalui Modul Presensi Ujian.',
                        style: TextStyle(
                          fontSize: 10.5,
                          color: isDark
                              ? const Color(0xFF8D9387)
                              : const Color(0xFF73796E),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 14),

          // 2. Filter Bar (Bulan Hijriyah, Semester, Search)
          Row(
            children: [
              Expanded(
                child: TextField(
                  decoration: const InputDecoration(
                    hintText: 'Cari murid...',
                    prefixIcon: Icon(Icons.search_rounded, size: 20),
                    contentPadding: EdgeInsets.symmetric(
                      vertical: 8,
                      horizontal: 12,
                    ),
                    isDense: true,
                  ),
                  onChanged: (val) =>
                      setState(() => _searchPresensiQuery = val),
                ),
              ),
              const SizedBox(width: 6),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8),
                decoration: BoxDecoration(
                  color: isDark
                      ? const Color(0xFF1B241C)
                      : const Color(0xFFF1F5F9),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: isDark
                        ? const Color(0xFF334155)
                        : const Color(0xFFCBD5E1),
                  ),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: _selectedSemesterPresensi,
                    isDense: true,
                    items: const [
                      DropdownMenuItem(
                        value: 'Semua',
                        child: Text(
                          'Semua Sem',
                          style: TextStyle(fontSize: 11.5),
                        ),
                      ),
                      DropdownMenuItem(
                        value: 'Semester 1',
                        child: Text('Sem 1', style: TextStyle(fontSize: 11.5)),
                      ),
                      DropdownMenuItem(
                        value: 'Semester 2',
                        child: Text('Sem 2', style: TextStyle(fontSize: 11.5)),
                      ),
                    ],
                    onChanged: (val) {
                      if (val != null) {
                        HapticHelper.light();
                        setState(() => _selectedSemesterPresensi = val);
                        provider.fetchPresensiMurid(
                          ruanganId: _selectedRuanganId,
                          bulanHijriyahId: _selectedBulanPresensiId,
                          semester: val == 'Semua'
                              ? null
                              : (val == 'Semester 1' ? '1' : '2'),
                        );
                      }
                    },
                  ),
                ),
              ),
              const SizedBox(width: 6),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8),
                decoration: BoxDecoration(
                  color: isDark
                      ? const Color(0xFF1B241C)
                      : const Color(0xFFF1F5F9),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: isDark
                        ? const Color(0xFF334155)
                        : const Color(0xFFCBD5E1),
                  ),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<int?>(
                    value: _selectedBulanPresensiId,
                    isDense: true,
                    hint: const Text(
                      'Semua Bulan',
                      style: TextStyle(fontSize: 11.5),
                    ),
                    items: [
                      const DropdownMenuItem<int?>(
                        value: null,
                        child: Text(
                          'Semua Bulan',
                          style: TextStyle(fontSize: 11.5),
                        ),
                      ),
                      ...data.bulanHijriyahList.map((b) {
                        return DropdownMenuItem<int?>(
                          value: b.id,
                          child: Text(
                            b.namaBulan,
                            style: const TextStyle(fontSize: 11.5),
                          ),
                        );
                      }),
                    ],
                    onChanged: (val) {
                      HapticHelper.light();
                      setState(() => _selectedBulanPresensiId = val);
                      provider.fetchPresensiMurid(
                        ruanganId: _selectedRuanganId,
                        bulanHijriyahId: val,
                        semester: _selectedSemesterPresensi == 'Semua'
                            ? null
                            : (_selectedSemesterPresensi == 'Semester 1'
                                  ? '1'
                                  : '2'),
                      );
                    },
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),

          // Filter Chips: Predikat Kehadiran & Pengurutan
          Row(
            children: [
              Expanded(
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children:
                        [
                          'Semua',
                          'Sangat Baik',
                          'Baik',
                          'Cukup',
                          'Kurang',
                        ].map((pred) {
                          final isSelected = _predikatPresensiFilter == pred;
                          return Padding(
                            padding: const EdgeInsets.only(right: 6),
                            child: FilterChip(
                              label: Text(
                                pred,
                                style: const TextStyle(fontSize: 11),
                              ),
                              selected: isSelected,
                              selectedColor: AppColors.primaryLight,
                              checkmarkColor: Colors.white,
                              onSelected: (val) {
                                HapticHelper.selection();
                                setState(() => _predikatPresensiFilter = pred);
                              },
                            ),
                          );
                        }).toList(),
                  ),
                ),
              ),
              const SizedBox(width: 6),
              PopupMenuButton<String>(
                icon: const Icon(Icons.sort_rounded, size: 20),
                tooltip: 'Urutkan',
                onSelected: (val) {
                  HapticHelper.selection();
                  setState(() => _sortPresensiMurid = val);
                },
                itemBuilder: (_) =>
                    [
                      'Nama (A-Z)',
                      'Kehadiran Terendah',
                      'Kehadiran Tertinggi',
                      'Paling Banyak Alpha',
                    ].map((s) {
                      return PopupMenuItem(
                        value: s,
                        child: Row(
                          children: [
                            if (_sortPresensiMurid == s)
                              const Icon(
                                Icons.check,
                                size: 16,
                                color: AppColors.primaryLight,
                              )
                            else
                              const SizedBox(width: 16),
                            const SizedBox(width: 8),
                            Text(s, style: const TextStyle(fontSize: 12)),
                          ],
                        ),
                      );
                    }).toList(),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // 3. Daftar Murid & Kehadiran
          if (filteredMurid.isEmpty)
            const GlassCard(
              padding: EdgeInsets.all(20),
              child: Center(
                child: Text(
                  'Tidak ada data murid yang sesuai filter.',
                  style: TextStyle(fontSize: 12),
                ),
              ),
            )
          else
            ...filteredMurid.map((m) {
              final isPutra = m.jenisKelamin == 'L';
              return GlassCard(
                margin: const EdgeInsets.only(bottom: 8),
                padding: const EdgeInsets.all(12),
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 18,
                      backgroundColor: isPutra
                          ? Colors.blue.withValues(alpha: 0.15)
                          : Colors.pink.withValues(alpha: 0.15),
                      backgroundImage: m.foto != null
                          ? NetworkImage(m.foto!)
                          : null,
                      child: m.foto == null
                          ? Icon(
                              isPutra
                                  ? Icons.face_rounded
                                  : Icons.face_3_rounded,
                              size: 20,
                              color: isPutra ? Colors.blue : Colors.pink,
                            )
                          : null,
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            m.nama,
                            style: const TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.bold,
                            ),
                            overflow: TextOverflow.ellipsis,
                          ),
                          Text(
                            'NISM: ${m.nism} • Wali: ${m.wali}',
                            style: TextStyle(
                              fontSize: 10.5,
                              color: isDark
                                  ? const Color(0xFF8D9387)
                                  : const Color(0xFF73796E),
                            ),
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 4),
                          Row(
                            children: [
                              _buildMiniCounter(
                                'H: ${m.hadirCount}',
                                AppColors.hadirTextLight,
                              ),
                              const SizedBox(width: 6),
                              _buildMiniCounter(
                                'I: ${m.izinCount}',
                                AppColors.skyBlueAccent,
                              ),
                              const SizedBox(width: 6),
                              _buildMiniCounter(
                                'S: ${m.sakitCount}',
                                AppColors.amberAccent,
                              ),
                              const SizedBox(width: 6),
                              _buildMiniCounter(
                                'A: ${m.alphaCount}',
                                AppColors.roseDanger,
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(
                          '${m.persentaseKehadiran}%',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w900,
                            color: m.persentaseKehadiran >= 75
                                ? AppColors.hadirTextLight
                                : (m.persentaseKehadiran >= 60
                                      ? AppColors.amberAccent
                                      : AppColors.roseDanger),
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          m.predikat,
                          style: TextStyle(
                            fontSize: 9.5,
                            fontWeight: FontWeight.bold,
                            color:
                                m.predikat == 'Sangat Baik' ||
                                    m.predikat == 'Baik'
                                ? AppColors.hadirTextLight
                                : AppColors.roseDanger,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }

  // =========================================================================
  // TAB 2: LAPORAN PRESENSI USTADZ
  // =========================================================================
  Widget _buildPresensiUstadzTab(bool isDark, LaporanProvider provider) {
    final data = provider.presensiUstadz;
    if (provider.isLoadingPresensiUstadz) {
      return const ShimmerLoadingList(count: 4, height: 110);
    }
    if (data == null) {
      return GlassCard(
        margin: const EdgeInsets.all(16),
        padding: const EdgeInsets.all(24),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.info_outline_rounded,
                size: 40,
                color: Colors.grey,
              ),
              const SizedBox(height: 12),
              Text(
                provider.errorPresensiUstadz ??
                    'Gagal memuat laporan presensi ustadz.',
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              ElevatedButton.icon(
                onPressed: () => provider.fetchPresensiUstadz(
                  ruanganId: _selectedRuanganId,
                  ustadzId: _selectedUstadzId,
                  bulanHijriyahId: _selectedBulanUstadzId,
                  status: _statusPresensiUstadz,
                ),
                icon: const Icon(Icons.refresh_rounded, size: 16),
                label: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }

    final filteredRiwayat = data.riwayat.where((r) {
      final matchesSearch =
          _searchPresensiUstadzQuery.isEmpty ||
          r.tanggal.contains(_searchPresensiUstadzQuery) ||
          (r.hariTanggal?.toLowerCase().contains(
                _searchPresensiUstadzQuery.toLowerCase(),
              ) ??
              false) ||
          (r.mapel?.toLowerCase().contains(
                _searchPresensiUstadzQuery.toLowerCase(),
              ) ??
              false) ||
          r.keterangan.toLowerCase().contains(
            _searchPresensiUstadzQuery.toLowerCase(),
          );

      final matchesStatus =
          _statusPresensiUstadz == 'Semua' ||
          r.status.toLowerCase() == _statusPresensiUstadz.toLowerCase();

      return matchesSearch && matchesStatus;
    }).toList();

    return RefreshIndicator(
      onRefresh: () async => provider.fetchPresensiUstadz(
        ruanganId: _selectedRuanganId,
        ustadzId: _selectedUstadzId,
        bulanHijriyahId: _selectedBulanUstadzId,
        status: _statusPresensiUstadz,
      ),
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 40),
        children: [
          // 1. Kartu Ustadz & Ringkasan Kehadiran
          GlassCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Row(
                        children: [
                          CircleAvatar(
                            radius: 20,
                            backgroundColor: AppColors.primaryLight.withValues(
                              alpha: 0.15,
                            ),
                            backgroundImage: data.ustadz.foto != null
                                ? NetworkImage(data.ustadz.foto!)
                                : null,
                            child: data.ustadz.foto == null
                                ? const Icon(
                                    Icons.person_rounded,
                                    color: AppColors.primaryLight,
                                  )
                                : null,
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  data.ustadz.nama,
                                  style: const TextStyle(
                                    fontSize: 14.5,
                                    fontWeight: FontWeight.bold,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                                Text(
                                  'NIUP: ${data.ustadz.niup} • ${data.namaRuangan != null ? 'Kelas: ${data.namaRuangan} • ' : ''}${data.tahunPelajaran}',
                                  style: TextStyle(
                                    fontSize: 11,
                                    color: isDark
                                        ? const Color(0xFF8D9387)
                                        : const Color(0xFF73796E),
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: isDark
                            ? AppColors.primaryContainerDark
                            : AppColors.primaryContainerLight,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        '${data.persentaseKehadiran}% Hadir',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: isDark
                              ? AppColors.primaryDark
                              : AppColors.primaryLight,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // 5 Kotak Kehadiran Ustadz
                Row(
                  children: [
                    _buildStatBox(
                      'Hadir',
                      '${data.totalHadir}',
                      AppColors.hadirTextLight,
                      isDark ? AppColors.hadirBgDark : AppColors.hadirBgLight,
                    ),
                    const SizedBox(width: 6),
                    _buildStatBox(
                      'Tugas',
                      '${data.totalTugas}',
                      AppColors.violetAccent,
                      isDark
                          ? const Color(0xFF241538)
                          : const Color(0xFFF3E8FF),
                    ),
                    const SizedBox(width: 6),
                    _buildStatBox(
                      'Izin',
                      '${data.totalIzin}',
                      AppColors.skyBlueAccent,
                      isDark
                          ? const Color(0xFF0C243B)
                          : const Color(0xFFE0F2FE),
                    ),
                    const SizedBox(width: 6),
                    _buildStatBox(
                      'Sakit',
                      '${data.totalSakit}',
                      AppColors.amberAccent,
                      isDark
                          ? const Color(0xFF382305)
                          : const Color(0xFFFEF3C7),
                    ),
                    const SizedBox(width: 6),
                    _buildStatBox(
                      'Alpha',
                      '${data.totalAlpha}',
                      AppColors.roseDanger,
                      isDark
                          ? const Color(0xFF380C14)
                          : const Color(0xFFFFE4E6),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),

          // 2. Switcher Ustadz jika ada >1 Ustadz
          if (data.daftarUstadz.length > 1) ...[
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: isDark
                    ? const Color(0xFF1E293B)
                    : const Color(0xFFF1F5F9),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(
                children: [
                  const Icon(Icons.swap_horiz_rounded, size: 18),
                  const SizedBox(width: 8),
                  const Text(
                    'Pilih Asatidz: ',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: DropdownButtonHideUnderline(
                      child: DropdownButton<int>(
                        value: _selectedUstadzId ?? data.ustadz.id,
                        isDense: true,
                        items: data.daftarUstadz.map((u) {
                          return DropdownMenuItem<int>(
                            value: u.id,
                            child: Text(u.nama),
                          );
                        }).toList(),
                        onChanged: (newId) {
                          HapticHelper.light();
                          if (newId != null) {
                            setState(() => _selectedUstadzId = newId);
                            provider.fetchPresensiUstadz(
                              ruanganId: _selectedRuanganId,
                              ustadzId: newId,
                              bulanHijriyahId: _selectedBulanUstadzId,
                              status: _statusPresensiUstadz,
                            );
                          }
                        },
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
          ],

          // Filter Bulan Hijriyah & Search
          Row(
            children: [
              Expanded(
                child: TextField(
                  decoration: const InputDecoration(
                    hintText: 'Cari tanggal/mapel/keterangan...',
                    prefixIcon: Icon(Icons.search_rounded, size: 20),
                    contentPadding: EdgeInsets.symmetric(
                      vertical: 8,
                      horizontal: 12,
                    ),
                    isDense: true,
                  ),
                  onChanged: (val) =>
                      setState(() => _searchPresensiUstadzQuery = val),
                ),
              ),
              const SizedBox(width: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10),
                decoration: BoxDecoration(
                  color: isDark
                      ? const Color(0xFF1B241C)
                      : const Color(0xFFF1F5F9),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                    color: isDark
                        ? const Color(0xFF334155)
                        : const Color(0xFFCBD5E1),
                  ),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<int?>(
                    value: _selectedBulanUstadzId,
                    isDense: true,
                    hint: const Text(
                      'Semua Bulan',
                      style: TextStyle(fontSize: 12),
                    ),
                    items: [
                      const DropdownMenuItem<int?>(
                        value: null,
                        child: Text(
                          'Semua Bulan',
                          style: TextStyle(fontSize: 12),
                        ),
                      ),
                      ...data.bulanHijriyahList.map((b) {
                        return DropdownMenuItem<int?>(
                          value: b.id,
                          child: Text(
                            b.namaBulan,
                            style: const TextStyle(fontSize: 12),
                          ),
                        );
                      }),
                    ],
                    onChanged: (val) {
                      HapticHelper.light();
                      setState(() => _selectedBulanUstadzId = val);
                      provider.fetchPresensiUstadz(
                        ruanganId: _selectedRuanganId,
                        ustadzId: _selectedUstadzId,
                        bulanHijriyahId: val,
                        status: _statusPresensiUstadz,
                      );
                    },
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),

          // Filter Chips Status Kehadiran
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: ['Semua', 'Hadir', 'Tugas', 'Izin', 'Sakit', 'Alpha']
                  .map((st) {
                    final isSelected = _statusPresensiUstadz == st;
                    return Padding(
                      padding: const EdgeInsets.only(right: 6),
                      child: FilterChip(
                        label: Text(st, style: const TextStyle(fontSize: 11)),
                        selected: isSelected,
                        selectedColor: AppColors.skyBlueAccent,
                        checkmarkColor: Colors.white,
                        onSelected: (val) {
                          HapticHelper.selection();
                          setState(() => _statusPresensiUstadz = st);
                        },
                      ),
                    );
                  })
                  .toList(),
            ),
          ),
          const SizedBox(height: 12),

          // 3. Riwayat Sesi Presensi Ustadz
          const Text(
            'Riwayat Presensi:',
            style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          if (filteredRiwayat.isEmpty)
            const GlassCard(
              padding: EdgeInsets.all(20),
              child: Center(
                child: Text('Belum ada riwayat sesi presensi di kelas ini.'),
              ),
            )
          else
            ...filteredRiwayat.map((r) {
              final isHadir = r.status == 'Hadir';
              final isTugas = r.status == 'Tugas';
              final isIzin = r.status == 'Izin';
              final isSakit = r.status == 'Sakit';

              Color statusColor = AppColors.roseDanger;
              Color statusBg = isDark
                  ? const Color(0xFF380C14)
                  : const Color(0xFFFFE4E6);
              if (isHadir) {
                statusColor = AppColors.hadirTextLight;
                statusBg = isDark
                    ? AppColors.hadirBgDark
                    : AppColors.hadirBgLight;
              } else if (isTugas) {
                statusColor = AppColors.violetAccent;
                statusBg = isDark
                    ? const Color(0xFF241538)
                    : const Color(0xFFF3E8FF);
              } else if (isIzin) {
                statusColor = AppColors.skyBlueAccent;
                statusBg = isDark
                    ? const Color(0xFF0C243B)
                    : const Color(0xFFE0F2FE);
              } else if (isSakit) {
                statusColor = AppColors.amberAccent;
                statusBg = isDark
                    ? const Color(0xFF382305)
                    : const Color(0xFFFEF3C7);
              }

              return GlassCard(
                margin: const EdgeInsets.only(bottom: 8),
                padding: const EdgeInsets.all(12),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            r.hariTanggal ?? r.tanggal,
                            style: const TextStyle(
                              fontSize: 12.5,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            'Mapel: ${r.mapel ?? '-'} • Jam: ${r.jamMasuk} • Ket: ${r.keterangan}',
                            style: TextStyle(
                              fontSize: 11,
                              color: isDark
                                  ? const Color(0xFF8D9387)
                                  : const Color(0xFF73796E),
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 3,
                      ),
                      decoration: BoxDecoration(
                        color: statusBg,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        r.status,
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: statusColor,
                        ),
                      ),
                    ),
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }

  // =========================================================================
  // TAB 3: LAPORAN PELANGGARAN MURID (BUKU KASUS)
  // =========================================================================
  Widget _buildPelanggaranTab(bool isDark, LaporanProvider provider) {
    final data = provider.pelanggaranMurid;
    if (provider.isLoadingPelanggaranMurid) {
      return const ShimmerLoadingList(count: 4, height: 110);
    }
    if (data == null) {
      return GlassCard(
        margin: const EdgeInsets.all(16),
        padding: const EdgeInsets.all(24),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.info_outline_rounded,
                size: 40,
                color: Colors.grey,
              ),
              const SizedBox(height: 12),
              Text(
                provider.errorPelanggaranMurid ??
                    'Gagal memuat laporan pelanggaran murid.',
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              ElevatedButton.icon(
                onPressed: () => provider.fetchPelanggaranMurid(
                  ruanganId: _selectedRuanganId,
                ),
                icon: const Icon(Icons.refresh_rounded, size: 16),
                label: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }

    // Filter & Sort Rekap Murid
    var filteredMurid = data.rekapMurid.where((m) {
      final matchesSearch =
          m.nama.toLowerCase().contains(
            _searchPelanggaranQuery.toLowerCase(),
          ) ||
          m.nism.contains(_searchPelanggaranQuery) ||
          m.wali.toLowerCase().contains(_searchPelanggaranQuery.toLowerCase());

      final matchesStatus =
          _statusDisiplinFilter == 'Semua' ||
          m.statusKedisiplinan.toLowerCase() ==
              _statusDisiplinFilter.toLowerCase();

      return matchesSearch && matchesStatus;
    }).toList();

    if (_sortPelanggaran == 'Paling Banyak Poin') {
      filteredMurid.sort((a, b) => b.totalPoin.compareTo(a.totalPoin));
    } else if (_sortPelanggaran == 'Poin Terendah') {
      filteredMurid.sort((a, b) => a.totalPoin.compareTo(b.totalPoin));
    } else if (_sortPelanggaran == 'Nama (A-Z)') {
      filteredMurid.sort((a, b) => a.nama.compareTo(b.nama));
    }

    final totalKasus = data.totalKasus;
    final totalPoin = data.totalPoin;

    return RefreshIndicator(
      onRefresh: () async =>
          provider.fetchPelanggaranMurid(ruanganId: _selectedRuanganId),
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 40),
        children: [
          // 1. Header Ringkasan Statistik Pelanggaran
          GlassCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Buku Catatan Kasus',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(
                      data.tahunPelajaran,
                      style: TextStyle(
                        fontSize: 11,
                        color: isDark
                            ? const Color(0xFF8D9387)
                            : const Color(0xFF73796E),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    _buildStatBox(
                      'TOTAL KASUS',
                      '$totalKasus',
                      AppColors.roseDanger,
                      isDark
                          ? const Color(0xFF380C14)
                          : const Color(0xFFFFE4E6),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'TOTAL POIN',
                      '${totalPoin.toInt()}',
                      Colors.orange,
                      isDark
                          ? const Color(0xFF331E05)
                          : const Color(0xFFFFEDD5),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'RINGAN',
                      '${data.kasusRingan}',
                      AppColors.amberAccent,
                      isDark
                          ? const Color(0xFF382305)
                          : const Color(0xFFFEF3C7),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'SEDANG',
                      '${data.kasusSedang}',
                      Colors.deepOrange,
                      isDark
                          ? const Color(0xFF38150C)
                          : const Color(0xFFFFEBE6),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'BERAT',
                      '${data.kasusBerat}',
                      AppColors.roseDanger,
                      isDark
                          ? const Color(0xFF380C14)
                          : const Color(0xFFFFE4E6),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),

          // 2. Search & Sort Bar
          Row(
            children: [
              Expanded(
                child: TextField(
                  decoration: InputDecoration(
                    hintText: 'Cari nama, NISM, atau wali...',
                    hintStyle: const TextStyle(fontSize: 12),
                    prefixIcon: const Icon(Icons.search_rounded, size: 18),
                    isDense: true,
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(10),
                      borderSide: BorderSide.none,
                    ),
                    filled: true,
                    fillColor: isDark
                        ? const Color(0xFF1E261D)
                        : Colors.grey.shade100,
                  ),
                  style: const TextStyle(fontSize: 12),
                  onChanged: (val) =>
                      setState(() => _searchPelanggaranQuery = val),
                ),
              ),
              const SizedBox(width: 8),
              PopupMenuButton<String>(
                icon: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: isDark
                        ? const Color(0xFF1E261D)
                        : Colors.grey.shade100,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: const Icon(Icons.sort_rounded, size: 20),
                ),
                onSelected: (val) {
                  HapticHelper.selection();
                  setState(() => _sortPelanggaran = val);
                },
                itemBuilder: (ctx) => [
                  'Paling Banyak Poin',
                  'Poin Terendah',
                  'Nama (A-Z)',
                ].map((s) => PopupMenuItem(value: s, child: Text(s))).toList(),
              ),
            ],
          ),
          const SizedBox(height: 10),

          // 3. Status Disiplin Filter Chips
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children:
                  [
                    'Semua',
                    'Disiplin',
                    'Perhatian',
                    'Peringatan',
                    'Kritis',
                  ].map((st) {
                    final isSelected = _statusDisiplinFilter == st;
                    return Padding(
                      padding: const EdgeInsets.only(right: 6),
                      child: FilterChip(
                        label: Text(st, style: const TextStyle(fontSize: 11)),
                        selected: isSelected,
                        selectedColor: AppColors.roseDanger,
                        checkmarkColor: Colors.white,
                        onSelected: (val) {
                          HapticHelper.selection();
                          setState(() => _statusDisiplinFilter = st);
                        },
                      ),
                    );
                  }).toList(),
            ),
          ),
          const SizedBox(height: 12),

          // 4. Daftar Rekap Murid (Ketuk nama untuk melihat riwayat pelanggaran anak tersebut)
          if (filteredMurid.isEmpty)
            const GlassCard(
              padding: EdgeInsets.all(20),
              child: Center(
                child: Text(
                  'Tidak ada data murid yang sesuai filter.',
                  style: TextStyle(fontSize: 12),
                ),
              ),
            )
          else
            ...filteredMurid.map((m) {
              final isClean =
                  m.statusKedisiplinan == 'Disiplin' || m.totalPoin == 0;
              final isKritis = m.statusKedisiplinan == 'Kritis';
              final isPeringatan = m.statusKedisiplinan == 'Peringatan';
              final isPutra = m.jenisKelamin.toLowerCase() == 'l';

              Color statusColor = AppColors.hadirTextLight;
              Color statusBg = isDark
                  ? AppColors.hadirBgDark
                  : AppColors.hadirBgLight;
              if (isKritis) {
                statusColor = AppColors.roseDanger;
                statusBg = isDark
                    ? const Color(0xFF380C14)
                    : const Color(0xFFFFE4E6);
              } else if (isPeringatan) {
                statusColor = Colors.orange;
                statusBg = isDark
                    ? const Color(0xFF331E05)
                    : const Color(0xFFFFEDD5);
              } else if (!isClean) {
                statusColor = AppColors.amberAccent;
                statusBg = isDark
                    ? const Color(0xFF382305)
                    : const Color(0xFFFEF3C7);
              }

              return GlassCard(
                margin: const EdgeInsets.only(bottom: 8),
                padding: EdgeInsets.zero,
                child: InkWell(
                  borderRadius: BorderRadius.circular(12),
                  onTap: () {
                    HapticHelper.light();
                    _showRiwayatPelanggaranMuridSheet(
                      context,
                      isDark,
                      m,
                      data.riwayatLog,
                    );
                  },
                  child: Padding(
                    padding: const EdgeInsets.all(12),
                    child: Row(
                      children: [
                        CircleAvatar(
                          radius: 20,
                          backgroundColor: isPutra
                              ? Colors.blue.withValues(alpha: 0.15)
                              : Colors.pink.withValues(alpha: 0.15),
                          backgroundImage: m.foto != null
                              ? NetworkImage(m.foto!)
                              : null,
                          child: m.foto == null
                              ? Icon(
                                  isPutra
                                      ? Icons.face_rounded
                                      : Icons.face_3_rounded,
                                  size: 22,
                                  color: isPutra ? Colors.blue : Colors.pink,
                                )
                              : null,
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                m.nama,
                                style: const TextStyle(
                                  fontSize: 13.5,
                                  fontWeight: FontWeight.bold,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                              const SizedBox(height: 2),
                              Text(
                                'NISM: ${m.nism} • Wali: ${m.wali}',
                                style: TextStyle(
                                  fontSize: 11,
                                  color: isDark
                                      ? const Color(0xFF8D9387)
                                      : const Color(0xFF73796E),
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                              const SizedBox(height: 4),
                              Row(
                                children: [
                                  Text(
                                    '${m.totalKasus} Kasus Tercatat',
                                    style: TextStyle(
                                      fontSize: 10.5,
                                      fontWeight: FontWeight.w600,
                                      color: isDark
                                          ? const Color(0xFF8D9387)
                                          : const Color(0xFF64748B),
                                    ),
                                  ),
                                  const SizedBox(width: 6),
                                  Text(
                                    '• Ketuk untuk rincian',
                                    style: TextStyle(
                                      fontSize: 10,
                                      fontStyle: FontStyle.italic,
                                      color: isDark
                                          ? AppColors.primaryDark
                                          : AppColors.primaryLight,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(width: 8),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text(
                              '${m.totalPoin.toInt()} Poin',
                              style: TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w900,
                                color: isClean
                                    ? AppColors.hadirTextLight
                                    : (isKritis
                                          ? AppColors.roseDanger
                                          : (isPeringatan
                                                ? Colors.orange
                                                : AppColors.amberAccent)),
                              ),
                            ),
                            const SizedBox(height: 4),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 8,
                                vertical: 2.5,
                              ),
                              decoration: BoxDecoration(
                                color: statusBg,
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                m.statusKedisiplinan,
                                style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: statusColor,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(width: 4),
                        Icon(
                          Icons.chevron_right_rounded,
                          size: 20,
                          color: isDark ? Colors.white38 : Colors.black26,
                        ),
                      ],
                    ),
                  ),
                ),
              );
            }),
        ],
      ),
    );
  }

  void _showRiwayatPelanggaranMuridSheet(
    BuildContext context,
    bool isDark,
    RekapPelanggaranMuridItem murid,
    List<LogPelanggaranItem> allLogs,
  ) {
    final studentLogs = allLogs
        .where((l) => l.muridId == murid.muridId)
        .toList();
    final isClean =
        murid.statusKedisiplinan == 'Disiplin' || murid.totalPoin == 0;
    final isPutra = murid.jenisKelamin.toLowerCase() == 'l';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (ctx) {
        return Container(
          constraints: BoxConstraints(
            maxHeight: MediaQuery.of(context).size.height * 0.85,
          ),
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF141A14) : Colors.white,
            borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Handle Bar
              Center(
                child: Container(
                  margin: const EdgeInsets.only(top: 12, bottom: 8),
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey.withValues(alpha: 0.4),
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),

              // Murid Header Info
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 24,
                      backgroundColor: isPutra
                          ? Colors.blue.withValues(alpha: 0.15)
                          : Colors.pink.withValues(alpha: 0.15),
                      backgroundImage: murid.foto != null
                          ? NetworkImage(murid.foto!)
                          : null,
                      child: murid.foto == null
                          ? Icon(
                              isPutra
                                  ? Icons.face_rounded
                                  : Icons.face_3_rounded,
                              size: 26,
                              color: isPutra ? Colors.blue : Colors.pink,
                            )
                          : null,
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            murid.nama,
                            style: const TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Text(
                            'NISM: ${murid.nism} • Wali: ${murid.wali}',
                            style: TextStyle(
                              fontSize: 11.5,
                              color: isDark
                                  ? const Color(0xFF8D9387)
                                  : const Color(0xFF73796E),
                            ),
                          ),
                        ],
                      ),
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(
                          '${murid.totalPoin.toInt()} Poin',
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w900,
                            color: isClean
                                ? AppColors.hadirTextLight
                                : AppColors.roseDanger,
                          ),
                        ),
                        Text(
                          '${murid.totalKasus} Kasus',
                          style: TextStyle(
                            fontSize: 11,
                            color: isDark
                                ? const Color(0xFF8D9387)
                                : const Color(0xFF73796E),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const Divider(height: 1),

              // Title Section
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'Riwayat Pelanggaran & Kasus',
                      style: TextStyle(
                        fontSize: 13.5,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(
                      '${studentLogs.length} Catatan',
                      style: TextStyle(
                        fontSize: 11.5,
                        color: isDark
                            ? const Color(0xFF8D9387)
                            : const Color(0xFF73796E),
                      ),
                    ),
                  ],
                ),
              ),

              // Log List or Clean State
              Flexible(
                child: studentLogs.isEmpty
                    ? Container(
                        padding: const EdgeInsets.all(32),
                        alignment: Alignment.center,
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(16),
                              decoration: const BoxDecoration(
                                color: AppColors.hadirBgLight,
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.verified_user_rounded,
                                size: 48,
                                color: AppColors.hadirTextLight,
                              ),
                            ),
                            const SizedBox(height: 16),
                            const Text(
                              'Bersih dari Pelanggaran',
                              style: TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              'Alhamdulillah, tidak ada catatan riwayat pelanggaran atau kasus untuk murid ini.',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                fontSize: 12,
                                color: isDark
                                    ? const Color(0xFF8D9387)
                                    : const Color(0xFF73796E),
                              ),
                            ),
                          ],
                        ),
                      )
                    : ListView.builder(
                        shrinkWrap: true,
                        padding: const EdgeInsets.fromLTRB(16, 4, 16, 24),
                        itemCount: studentLogs.length,
                        itemBuilder: (ctx, idx) {
                          final log = studentLogs[idx];
                          final isRingan = log.kategori == 'Ringan';
                          final isSedang = log.kategori == 'Sedang';

                          Color katColor = AppColors.roseDanger;
                          Color katBg = isDark
                              ? const Color(0xFF380C14)
                              : const Color(0xFFFFE4E6);
                          if (isRingan) {
                            katColor = AppColors.amberAccent;
                            katBg = isDark
                                ? const Color(0xFF382305)
                                : const Color(0xFFFEF3C7);
                          } else if (isSedang) {
                            katColor = Colors.orange;
                            katBg = isDark
                                ? const Color(0xFF331E05)
                                : const Color(0xFFFFEDD5);
                          }

                          return GlassCard(
                            margin: const EdgeInsets.only(bottom: 8),
                            padding: const EdgeInsets.all(12),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      log.hariTanggal ?? log.tanggal,
                                      style: TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.bold,
                                        color: isDark
                                            ? const Color(0xFF8D9387)
                                            : const Color(0xFF64748B),
                                      ),
                                    ),
                                    Row(
                                      children: [
                                        Container(
                                          padding: const EdgeInsets.symmetric(
                                            horizontal: 7,
                                            vertical: 2,
                                          ),
                                          decoration: BoxDecoration(
                                            color: katBg,
                                            borderRadius: BorderRadius.circular(
                                              6,
                                            ),
                                          ),
                                          child: Text(
                                            log.kategori,
                                            style: TextStyle(
                                              fontSize: 9.5,
                                              fontWeight: FontWeight.bold,
                                              color: katColor,
                                            ),
                                          ),
                                        ),
                                        const SizedBox(width: 8),
                                        Text(
                                          '+${log.poin.toInt()} Poin',
                                          style: const TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.w900,
                                            color: AppColors.roseDanger,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 6),
                                Text(
                                  log.namaPelanggaran,
                                  style: const TextStyle(
                                    fontSize: 13,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                if (log.keterangan.isNotEmpty &&
                                    log.keterangan != '-') ...[
                                  const SizedBox(height: 3),
                                  Text(
                                    'Catatan: ${log.keterangan}',
                                    style: TextStyle(
                                      fontSize: 11,
                                      fontStyle: FontStyle.italic,
                                      color: isDark
                                          ? const Color(0xFF8D9387)
                                          : const Color(0xFF73796E),
                                    ),
                                  ),
                                ],
                                const SizedBox(height: 4),
                                Text(
                                  'Dicatat oleh: ${log.pencatat}',
                                  style: TextStyle(
                                    fontSize: 10,
                                    color: isDark
                                        ? const Color(0xFF8D9387)
                                        : const Color(0xFF94A3B8),
                                  ),
                                ),
                              ],
                            ),
                          );
                        },
                      ),
              ),
            ],
          ),
        );
      },
    );
  }

  // =========================================================================
  // TAB 4: LAPORAN UJIAN & LEGER
  // =========================================================================
  Widget _buildUjianTab(bool isDark, LaporanProvider provider) {
    final data = provider.ujian;
    if (provider.isLoadingUjian) {
      return const ShimmerLoadingList(count: 4, height: 110);
    }
    if (data == null) {
      return GlassCard(
        margin: const EdgeInsets.all(16),
        padding: const EdgeInsets.all(24),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.info_outline_rounded,
                size: 40,
                color: Colors.grey,
              ),
              const SizedBox(height: 12),
              Text(
                provider.errorUjian ?? 'Gagal memuat laporan ujian.',
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              ElevatedButton.icon(
                onPressed: () => provider.fetchUjian(
                  ruanganId: _selectedRuanganId,
                  ujianId: _selectedUjianId,
                ),
                icon: const Icon(Icons.refresh_rounded, size: 16),
                label: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }

    var filteredMurid = data.rekapMurid.where((m) {
      final matchesSearch =
          m.nama.toLowerCase().contains(_searchUjianQuery.toLowerCase()) ||
          m.nism.contains(_searchUjianQuery);

      final matchesKetuntasan =
          _statusKetuntasanUjian == 'Semua' ||
          m.statusTuntas.toLowerCase() == _statusKetuntasanUjian.toLowerCase();

      return matchesSearch && matchesKetuntasan;
    }).toList();

    // Sorting
    if (_sortUjian == 'Peringkat') {
      filteredMurid.sort((a, b) => a.ranking.compareTo(b.ranking));
    } else if (_sortUjian == 'Nama (A-Z)') {
      filteredMurid.sort((a, b) => a.nama.compareTo(b.nama));
    } else if (_sortUjian == 'Nilai Tertinggi') {
      filteredMurid.sort((a, b) => b.rataRata.compareTo(a.rataRata));
    } else if (_sortUjian == 'Nilai Terendah') {
      filteredMurid.sort((a, b) => a.rataRata.compareTo(b.rataRata));
    }

    return RefreshIndicator(
      onRefresh: () async => provider.fetchUjian(
        ruanganId: _selectedRuanganId,
        ujianId: _selectedUjianId,
      ),
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 40),
        children: [
          // 1. Selector Ujian
          if (data.daftarUjian.isNotEmpty) ...[
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: data.daftarUjian.map((u) {
                  final isSelected = data.ujian.id == u.id;
                  return Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: ChoiceChip(
                      label: Text(
                        '${u.namaUjian} (${u.tipeUjian})',
                        style: TextStyle(
                          fontSize: 11.5,
                          fontWeight: isSelected
                              ? FontWeight.bold
                              : FontWeight.normal,
                          color: isSelected
                              ? Colors.white
                              : (isDark
                                    ? const Color(0xFFC3C8BC)
                                    : const Color(0xFF43483E)),
                        ),
                      ),
                      selected: isSelected,
                      selectedColor: AppColors.primaryLight,
                      onSelected: (val) {
                        if (val) {
                          HapticHelper.selection();
                          setState(() => _selectedUjianId = u.id);
                          provider.fetchUjian(
                            ruanganId: _selectedRuanganId,
                            ujianId: u.id,
                          );
                        }
                      },
                    ),
                  );
                }).toList(),
              ),
            ),
            const SizedBox(height: 12),
          ],

          // 2. Ringkasan Nilai Kelas
          GlassCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: AppColors.primaryLight.withValues(
                                alpha: 0.12,
                              ),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: const Icon(
                              Icons.auto_stories_rounded,
                              color: AppColors.primaryLight,
                              size: 20,
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Hasil ${data.ujian.namaUjian}',
                                  style: const TextStyle(
                                    fontSize: 15,
                                    fontWeight: FontWeight.bold,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                                Text(
                                  'Kelas: ${data.namaRuangan} • ${data.totalMurid} Murid',
                                  style: TextStyle(
                                    fontSize: 11,
                                    color: isDark
                                        ? const Color(0xFF8D9387)
                                        : const Color(0xFF73796E),
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: isDark
                            ? AppColors.primaryContainerDark
                            : AppColors.primaryContainerLight,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        'Rata: ${data.rataRataKelas}',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: isDark
                              ? AppColors.primaryDark
                              : AppColors.primaryLight,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // 4 Kotak Nilai
                Row(
                  children: [
                    _buildStatBox(
                      'Tertinggi',
                      '${data.nilaiTertinggi}',
                      AppColors.hadirTextLight,
                      isDark ? AppColors.hadirBgDark : AppColors.hadirBgLight,
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Terendah',
                      '${data.nilaiTerendah}',
                      AppColors.roseDanger,
                      isDark
                          ? const Color(0xFF380C14)
                          : const Color(0xFFFFE4E6),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Tuntas',
                      '${data.jumlahTuntas} (${data.persentaseTuntas}%)',
                      AppColors.primaryLight,
                      isDark
                          ? const Color(0xFF1E293B)
                          : const Color(0xFFF1F5F9),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Belum Tuntas',
                      '${data.jumlahBelumTuntas}',
                      AppColors.amberAccent,
                      isDark
                          ? const Color(0xFF382305)
                          : const Color(0xFFFEF3C7),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 14),

          // 3. Filter Bar (Search & Sort)
          Row(
            children: [
              Expanded(
                child: TextField(
                  decoration: const InputDecoration(
                    hintText: 'Cari murid...',
                    prefixIcon: Icon(Icons.search_rounded, size: 20),
                    contentPadding: EdgeInsets.symmetric(
                      vertical: 8,
                      horizontal: 12,
                    ),
                    isDense: true,
                  ),
                  onChanged: (val) => setState(() => _searchUjianQuery = val),
                ),
              ),
              const SizedBox(width: 8),
              PopupMenuButton<String>(
                icon: const Icon(Icons.sort_rounded, size: 20),
                tooltip: 'Urutkan',
                onSelected: (val) {
                  HapticHelper.selection();
                  setState(() => _sortUjian = val);
                },
                itemBuilder: (_) =>
                    [
                      'Peringkat',
                      'Nama (A-Z)',
                      'Nilai Tertinggi',
                      'Nilai Terendah',
                    ].map((s) {
                      return PopupMenuItem(
                        value: s,
                        child: Row(
                          children: [
                            if (_sortUjian == s)
                              const Icon(
                                Icons.check,
                                size: 16,
                                color: AppColors.primaryLight,
                              )
                            else
                              const SizedBox(width: 16),
                            const SizedBox(width: 8),
                            Text(s, style: const TextStyle(fontSize: 12)),
                          ],
                        ),
                      );
                    }).toList(),
              ),
            ],
          ),
          const SizedBox(height: 10),

          // Filter Chips Status Ketuntasan
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: ['Semua', 'Tuntas', 'Belum Tuntas'].map((kt) {
                final isSelected = _statusKetuntasanUjian == kt;
                return Padding(
                  padding: const EdgeInsets.only(right: 6),
                  child: FilterChip(
                    label: Text(kt, style: const TextStyle(fontSize: 11)),
                    selected: isSelected,
                    selectedColor: AppColors.primaryLight,
                    checkmarkColor: Colors.white,
                    onSelected: (val) {
                      HapticHelper.selection();
                      setState(() => _statusKetuntasanUjian = kt);
                    },
                  ),
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 12),

          // 4. Daftar Murid & Peringkat Bintang Pelajar
          if (filteredMurid.isEmpty)
            const GlassCard(
              padding: EdgeInsets.all(20),
              child: Center(
                child: Text(
                  'Tidak ada data murid yang sesuai filter.',
                  style: TextStyle(fontSize: 12),
                ),
              ),
            )
          else
            ...filteredMurid.map((m) {
              final isPutra = m.jenisKelamin == 'L';
              final isTop3 = m.ranking <= 3;

              return GlassCard(
                margin: const EdgeInsets.only(bottom: 8),
                padding: const EdgeInsets.all(12),
                child: ExpansionTile(
                  tilePadding: EdgeInsets.zero,
                  childrenPadding: const EdgeInsets.only(top: 8),
                  leading: CircleAvatar(
                    radius: 18,
                    backgroundColor: isTop3
                        ? AppColors.amberAccent.withValues(alpha: 0.2)
                        : (isPutra
                              ? Colors.blue.withValues(alpha: 0.15)
                              : Colors.pink.withValues(alpha: 0.15)),
                    child: isTop3
                        ? Icon(
                            Icons.emoji_events_rounded,
                            size: 20,
                            color: m.ranking == 1
                                ? Colors.amber
                                : (m.ranking == 2 ? Colors.grey : Colors.brown),
                          )
                        : Text(
                            '${m.ranking}',
                            style: const TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                  ),
                  title: Text(
                    m.nama,
                    style: const TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  subtitle: Text(
                    'NISM: ${m.nism} • Rata-rata: ${m.rataRata} • Total: ${m.totalNilai}',
                    style: TextStyle(
                      fontSize: 11,
                      color: isDark
                          ? const Color(0xFF8D9387)
                          : const Color(0xFF73796E),
                    ),
                  ),
                  trailing: Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 3,
                    ),
                    decoration: BoxDecoration(
                      color: m.statusTuntas == 'Tuntas'
                          ? (isDark
                                ? AppColors.hadirBgDark
                                : AppColors.hadirBgLight)
                          : (isDark
                                ? const Color(0xFF380C14)
                                : const Color(0xFFFFE4E6)),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      'Peringkat ${m.ranking}',
                      style: TextStyle(
                        fontSize: 10.5,
                        fontWeight: FontWeight.bold,
                        color: m.statusTuntas == 'Tuntas'
                            ? AppColors.hadirTextLight
                            : AppColors.roseDanger,
                      ),
                    ),
                  ),
                  children: [
                    const Divider(height: 1),
                    const SizedBox(height: 6),
                    ...m.mapelNilai.map((mpl) {
                      return Padding(
                        padding: const EdgeInsets.symmetric(
                          vertical: 3,
                          horizontal: 4,
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              mpl.namaMapel,
                              style: const TextStyle(fontSize: 11.5),
                            ),
                            Text(
                              '${mpl.nilai}',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: mpl.nilai >= 60
                                    ? AppColors.hadirTextLight
                                    : AppColors.roseDanger,
                              ),
                            ),
                          ],
                        ),
                      );
                    }),
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }

  // =========================================================================
  // TAB 5: LAPORAN KENAIKAN KELAS & KELULUSAN
  // =========================================================================
  Widget _buildKenaikanKelasTab(bool isDark, LaporanProvider provider) {
    final data = provider.kenaikanKelas;
    if (provider.isLoadingKenaikanKelas) {
      return const ShimmerLoadingList(count: 4, height: 110);
    }
    if (data == null) {
      return GlassCard(
        margin: const EdgeInsets.all(16),
        padding: const EdgeInsets.all(24),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                Icons.info_outline_rounded,
                size: 40,
                color: Colors.grey,
              ),
              const SizedBox(height: 12),
              Text(
                provider.errorKenaikanKelas ??
                    'Gagal memuat laporan kenaikan kelas.',
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              ElevatedButton.icon(
                onPressed: () =>
                    provider.fetchKenaikanKelas(ruanganId: _selectedRuanganId),
                icon: const Icon(Icons.refresh_rounded, size: 16),
                label: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }

    var filteredMurid = data.dataKenaikan.where((m) {
      final matchesSearch =
          m.nama.toLowerCase().contains(_searchKenaikanQuery.toLowerCase()) ||
          m.nism.contains(_searchKenaikanQuery) ||
          m.levelTujuanNama.toLowerCase().contains(
            _searchKenaikanQuery.toLowerCase(),
          );

      final matchesStatus =
          _statusKenaikanFilter == 'Semua' ||
          m.keputusanFinal.toLowerCase() == _statusKenaikanFilter.toLowerCase();

      return matchesSearch && matchesStatus;
    }).toList();

    // Sorting
    if (_sortKenaikan == 'Nilai Tertinggi') {
      filteredMurid.sort(
        (a, b) => b.nilaiAkumulasi.compareTo(a.nilaiAkumulasi),
      );
    } else if (_sortKenaikan == 'Nilai Terendah') {
      filteredMurid.sort(
        (a, b) => a.nilaiAkumulasi.compareTo(b.nilaiAkumulasi),
      );
    } else if (_sortKenaikan == 'Nama (A-Z)') {
      filteredMurid.sort((a, b) => a.nama.compareTo(b.nama));
    }

    return RefreshIndicator(
      onRefresh: () async =>
          provider.fetchKenaikanKelas(ruanganId: _selectedRuanganId),
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 40),
        children: [
          // 1. Ringkasan Kenaikan Kelas
          GlassCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: AppColors.primaryLight.withValues(
                                alpha: 0.12,
                              ),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: const Icon(
                              Icons.school_rounded,
                              color: AppColors.primaryLight,
                              size: 20,
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  data.isKelasAkhir
                                      ? 'Kelulusan ${data.namaRuangan}'
                                      : 'Kenaikan ${data.namaRuangan}',
                                  style: const TextStyle(
                                    fontSize: 15,
                                    fontWeight: FontWeight.bold,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                                Text(
                                  'Bobot: IMDA 60% • Hadir 24% • Tertib 16%',
                                  style: TextStyle(
                                    fontSize: 10.5,
                                    color: isDark
                                        ? const Color(0xFF8D9387)
                                        : const Color(0xFF73796E),
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: isDark
                            ? AppColors.primaryContainerDark
                            : AppColors.primaryContainerLight,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        '${data.totalMurid} Murid',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: isDark
                              ? AppColors.primaryDark
                              : AppColors.primaryLight,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // 3 Kotak Naik / Lulus / Tinggal
                Row(
                  children: [
                    _buildStatBox(
                      data.isKelasAkhir ? 'Lulus' : 'Naik Kelas',
                      data.isKelasAkhir
                          ? '${data.totalLulus}'
                          : '${data.totalNaikKelas}',
                      AppColors.hadirTextLight,
                      isDark ? AppColors.hadirBgDark : AppColors.hadirBgLight,
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Tinggal Kelas',
                      '${data.totalTinggalKelas}',
                      AppColors.roseDanger,
                      isDark
                          ? const Color(0xFF380C14)
                          : const Color(0xFFFFE4E6),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Total Murid',
                      '${data.totalMurid}',
                      AppColors.skyBlueAccent,
                      isDark
                          ? const Color(0xFF0C243B)
                          : const Color(0xFFE0F2FE),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 14),

          // 2. Filter Bar (Search & Sort)
          Row(
            children: [
              Expanded(
                child: TextField(
                  decoration: const InputDecoration(
                    hintText: 'Cari murid, tujuan...',
                    prefixIcon: Icon(Icons.search_rounded, size: 20),
                    contentPadding: EdgeInsets.symmetric(
                      vertical: 8,
                      horizontal: 12,
                    ),
                    isDense: true,
                  ),
                  onChanged: (val) =>
                      setState(() => _searchKenaikanQuery = val),
                ),
              ),
              const SizedBox(width: 8),
              PopupMenuButton<String>(
                icon: const Icon(Icons.sort_rounded, size: 20),
                tooltip: 'Urutkan',
                onSelected: (val) {
                  HapticHelper.selection();
                  setState(() => _sortKenaikan = val);
                },
                itemBuilder: (_) =>
                    ['Nilai Tertinggi', 'Nilai Terendah', 'Nama (A-Z)'].map((
                      s,
                    ) {
                      return PopupMenuItem(
                        value: s,
                        child: Row(
                          children: [
                            if (_sortKenaikan == s)
                              const Icon(
                                Icons.check,
                                size: 16,
                                color: AppColors.primaryLight,
                              )
                            else
                              const SizedBox(width: 16),
                            const SizedBox(width: 8),
                            Text(s, style: const TextStyle(fontSize: 12)),
                          ],
                        ),
                      );
                    }).toList(),
              ),
            ],
          ),
          const SizedBox(height: 10),

          // Filter Chips Status Kenaikan
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children:
                  [
                    'Semua',
                    data.isKelasAkhir ? 'Lulus' : 'Naik Kelas',
                    'Tinggal Kelas',
                  ].map((st) {
                    final isSelected = _statusKenaikanFilter == st;
                    return Padding(
                      padding: const EdgeInsets.only(right: 6),
                      child: FilterChip(
                        label: Text(st, style: const TextStyle(fontSize: 11)),
                        selected: isSelected,
                        selectedColor: AppColors.violetAccent,
                        checkmarkColor: Colors.white,
                        onSelected: (val) {
                          HapticHelper.selection();
                          setState(() => _statusKenaikanFilter = st);
                        },
                      ),
                    );
                  }).toList(),
            ),
          ),
          const SizedBox(height: 12),

          // 3. Daftar Status Kenaikan Murid
          if (filteredMurid.isEmpty)
            const GlassCard(
              padding: EdgeInsets.all(20),
              child: Center(
                child: Text(
                  'Tidak ada data murid yang sesuai filter.',
                  style: TextStyle(fontSize: 12),
                ),
              ),
            )
          else
            ...filteredMurid.map((m) {
              final isNaik =
                  m.keputusanFinal == 'Naik Kelas' ||
                  m.keputusanFinal == 'Lulus';

              return GlassCard(
                margin: const EdgeInsets.only(bottom: 8),
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  m.nama,
                                  style: const TextStyle(
                                    fontSize: 13.5,
                                    fontWeight: FontWeight.bold,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                              if (m.sudahDikunci) ...[
                                const SizedBox(width: 6),
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 5,
                                    vertical: 1.5,
                                  ),
                                  decoration: BoxDecoration(
                                    color: isDark
                                        ? const Color(0xFF1E293B)
                                        : const Color(0xFFE2E8F0),
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                  child: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Icon(
                                        Icons.lock_outline_rounded,
                                        size: 11,
                                        color: isDark
                                            ? const Color(0xFF94A3B8)
                                            : const Color(0xFF475569),
                                      ),
                                      const SizedBox(width: 3),
                                      Text(
                                        'Terkunci',
                                        style: TextStyle(
                                          fontSize: 9,
                                          fontWeight: FontWeight.w600,
                                          color: isDark
                                              ? const Color(0xFF94A3B8)
                                              : const Color(0xFF475569),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 3,
                          ),
                          decoration: BoxDecoration(
                            color: isNaik
                                ? (isDark
                                      ? AppColors.hadirBgDark
                                      : AppColors.hadirBgLight)
                                : (isDark
                                      ? const Color(0xFF380C14)
                                      : const Color(0xFFFFE4E6)),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            m.keputusanFinal,
                            style: TextStyle(
                              fontSize: 10.5,
                              fontWeight: FontWeight.bold,
                              color: isNaik
                                  ? AppColors.hadirTextLight
                                  : AppColors.roseDanger,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'NISM: ${m.nism} • Tujuan: ${m.levelTujuanNama}',
                      style: TextStyle(
                        fontSize: 11,
                        color: isDark
                            ? const Color(0xFF8D9387)
                            : const Color(0xFF73796E),
                      ),
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Sem 1: ${m.skorSem1} | Sem 2: ${m.skorSem2}',
                          style: TextStyle(
                            fontSize: 11,
                            color: isDark
                                ? const Color(0xFF8D9387)
                                : const Color(0xFF64748B),
                          ),
                        ),
                        Text(
                          'Nilai Akhir: ${m.nilaiAkumulasi}',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: isNaik
                                ? AppColors.hadirTextLight
                                : AppColors.roseDanger,
                          ),
                        ),
                      ],
                    ),
                    if (m.catatan.isNotEmpty) ...[
                      const SizedBox(height: 4),
                      Text(
                        'Catatan: ${m.catatan}',
                        style: TextStyle(
                          fontSize: 10.5,
                          fontStyle: FontStyle.italic,
                          color: isDark
                              ? const Color(0xFF8D9387)
                              : const Color(0xFF73796E),
                        ),
                      ),
                    ],
                  ],
                ),
              );
            }),
        ],
      ),
    );
  }

  // =========================================================================
  // MAIN BUILD
  // =========================================================================
  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final provider = context.watch<LaporanProvider>();
    final roomList = provider.presensiMurid?.ruanganList ?? [];

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Pusat Laporan',
          style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold),
        ),
      ),
      body: Column(
        children: [
          // 0. Pemilih Ruangan (Jika >1 Ruangan)
          if (roomList.length > 1)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 4, 16, 0),
              child: Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 4,
                ),
                decoration: BoxDecoration(
                  color: isDark
                      ? const Color(0xFF1E293B)
                      : const Color(0xFFF1F5F9),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.meeting_room_outlined, size: 18),
                    const SizedBox(width: 8),
                    const Text(
                      'Pilih Kelas: ',
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: DropdownButtonHideUnderline(
                        child: DropdownButton<int>(
                          value: _selectedRuanganId ?? roomList.first.id,
                          isDense: true,
                          items: roomList.map((r) {
                            return DropdownMenuItem<int>(
                              value: r.id,
                              child: Text('${r.namaRuangan} (${r.levelNama})'),
                            );
                          }).toList(),
                          onChanged: (newId) {
                            if (newId != null) {
                              setState(() {
                                _selectedRuanganId = newId;
                                _selectedUjianId = null;
                                _selectedUstadzId = null;
                              });
                              _loadCurrentTabData();
                            }
                          },
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),

          // 1. Navigation Segmented Tab Bar
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
                label: 'Murid',
                activeColor: AppColors.primaryLight,
              ),
              SegmentedTabItem(
                activeIcon: Icons.badge_rounded,
                inactiveIcon: Icons.badge_outlined,
                label: 'Asatidz',
                activeColor: AppColors.skyBlueAccent,
              ),
              SegmentedTabItem(
                activeIcon: Icons.gavel_rounded,
                inactiveIcon: Icons.gavel_outlined,
                label: 'Kasus',
                activeColor: AppColors.roseDanger,
              ),
              SegmentedTabItem(
                activeIcon: Icons.auto_stories_rounded,
                inactiveIcon: Icons.auto_stories_outlined,
                label: 'Ujian',
                activeColor: AppColors.amberAccent,
              ),
              SegmentedTabItem(
                activeIcon: Icons.school_rounded,
                inactiveIcon: Icons.school_outlined,
                label: 'Kenaikan',
                activeColor: AppColors.violetAccent,
              ),
            ],
          ),

          // 2. Tab Bar Views
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildPresensiMuridTab(isDark, provider),
                _buildPresensiUstadzTab(isDark, provider),
                _buildPelanggaranTab(isDark, provider),
                _buildUjianTab(isDark, provider),
                _buildKenaikanKelasTab(isDark, provider),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatBox(
    String label,
    String value,
    Color textColor,
    Color bgColor,
  ) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 8),
        decoration: BoxDecoration(
          color: bgColor,
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(
          children: [
            Text(
              value,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w900,
                color: textColor,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                fontSize: 9,
                fontWeight: FontWeight.w600,
                color: textColor,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMiniCounter(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(4),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 9.5,
          fontWeight: FontWeight.bold,
          color: color,
        ),
      ),
    );
  }
}
