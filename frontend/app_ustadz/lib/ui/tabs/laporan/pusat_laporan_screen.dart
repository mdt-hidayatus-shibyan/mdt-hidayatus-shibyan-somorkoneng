import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/theme/app_colors.dart';
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

  // Filter Presensi Murid
  int? _selectedBulanPresensiId;
  String _searchPresensiQuery = '';

  // Filter Presensi Ustadz
  int? _selectedUstadzId;

  // Filter Pelanggaran
  String _kategoriPelanggaran = 'Semua';
  String _searchPelanggaranQuery = '';

  // Filter Ujian
  int? _selectedUjianId;
  String _searchUjianQuery = '';

  // Filter Kenaikan Kelas
  String _searchKenaikanQuery = '';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(
      length: 5,
      vsync: this,
      initialIndex: widget.initialTabIndex,
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
    );
    provider.fetchPresensiUstadz(ustadzId: _selectedUstadzId);
    provider.fetchPelanggaranMurid(
      ruanganId: _selectedRuanganId,
      kategori: _kategoriPelanggaran,
    );
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
      return const GlassCard(
        margin: EdgeInsets.all(16),
        padding: EdgeInsets.all(24),
        child: Center(child: Text('Gagal memuat laporan presensi murid.')),
      );
    }

    final filteredMurid = data.rekapMurid.where((m) {
      return m.nama.toLowerCase().contains(
            _searchPresensiQuery.toLowerCase(),
          ) ||
          m.nism.contains(_searchPresensiQuery);
    }).toList();

    return RefreshIndicator(
      onRefresh: () async => provider.fetchPresensiMurid(
        ruanganId: _selectedRuanganId,
        bulanHijriyahId: _selectedBulanPresensiId,
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
                    Row(
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
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Presensi ${data.namaRuangan}',
                              style: const TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                              ),
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
                      ],
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
          const SizedBox(height: 14),

          // 2. Filter Bulan & Search Bar
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
                    value: _selectedBulanPresensiId,
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
                      setState(() => _selectedBulanPresensiId = val);
                      provider.fetchPresensiMurid(
                        ruanganId: _selectedRuanganId,
                        bulanHijriyahId: val,
                      );
                    },
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // 3. Daftar Santri & Kehadiran
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
                            isPutra ? Icons.face_rounded : Icons.face_3_rounded,
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
      return const GlassCard(
        margin: EdgeInsets.all(16),
        padding: EdgeInsets.all(24),
        child: Center(child: Text('Gagal memuat laporan presensi ustadz.')),
      );
    }

    return RefreshIndicator(
      onRefresh: () async =>
          provider.fetchPresensiUstadz(ustadzId: _selectedUstadzId),
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 40),
        children: [
          // 1. Kartu Ustadz & Ringkasan
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
                                  'NIUP: ${data.ustadz.niup} • ${data.tahunPelajaran}',
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
          const SizedBox(height: 14),

          // 2. Switcher Ustadz jika ada daftar ustadz
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
                          if (newId != null) {
                            setState(() => _selectedUstadzId = newId);
                            provider.fetchPresensiUstadz(ustadzId: newId);
                          }
                        },
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 14),
          ],

          // 3. Riwayat Sesi Presensi Ustadz
          const Text(
            'Riwayat Presensi:',
            style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          if (data.riwayat.isEmpty)
            const GlassCard(
              padding: EdgeInsets.all(20),
              child: Center(child: Text('Belum ada riwayat sesi presensi.')),
            )
          else
            ...data.riwayat.map((r) {
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
                            'Jam Masuk: ${r.jamMasuk} • Ket: ${r.keterangan}',
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
  // TAB 3: LAPORAN PELANGGARAN SANTRI
  // =========================================================================
  Widget _buildPelanggaranTab(bool isDark, LaporanProvider provider) {
    final data = provider.pelanggaranMurid;
    if (provider.isLoadingPelanggaranMurid) {
      return const ShimmerLoadingList(count: 4, height: 110);
    }
    if (data == null) {
      return const GlassCard(
        margin: EdgeInsets.all(16),
        padding: EdgeInsets.all(24),
        child: Center(child: Text('Gagal memuat laporan pelanggaran.')),
      );
    }

    final filteredSantri = data.rekapSantri.where((s) {
      return s.nama.toLowerCase().contains(
            _searchPelanggaranQuery.toLowerCase(),
          ) ||
          s.nism.contains(_searchPelanggaranQuery);
    }).toList();

    return RefreshIndicator(
      onRefresh: () async => provider.fetchPelanggaranMurid(
        ruanganId: _selectedRuanganId,
        kategori: _kategoriPelanggaran,
      ),
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 40),
        children: [
          // 1. Ringkasan Pelanggaran Kelas
          GlassCard(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: AppColors.roseDanger.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: const Icon(
                            Icons.gavel_rounded,
                            color: AppColors.roseDanger,
                            size: 20,
                          ),
                        ),
                        const SizedBox(width: 10),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Pelanggaran ${data.namaRuangan}',
                              style: const TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              '${data.totalKasus} Kasus • ${data.totalPoin} Total Poin',
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
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: isDark
                            ? const Color(0xFF380C14)
                            : const Color(0xFFFFE4E6),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        '${data.totalPoin} Poin',
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: AppColors.roseDanger,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // 3 Kotak Kategori (Ringan, Sedang, Berat)
                Row(
                  children: [
                    _buildStatBox(
                      'Ringan',
                      '${data.kasusRingan}',
                      AppColors.amberAccent,
                      isDark
                          ? const Color(0xFF382305)
                          : const Color(0xFFFEF3C7),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Sedang',
                      '${data.kasusSedang}',
                      Colors.orange,
                      isDark
                          ? const Color(0xFF331E05)
                          : const Color(0xFFFFEDD5),
                    ),
                    const SizedBox(width: 8),
                    _buildStatBox(
                      'Berat',
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
          const SizedBox(height: 14),

          // 2. Filter Kategori Chips & Search
          TextField(
            decoration: const InputDecoration(
              hintText: 'Cari murid...',
              prefixIcon: Icon(Icons.search_rounded, size: 20),
              contentPadding: EdgeInsets.symmetric(vertical: 8, horizontal: 12),
              isDense: true,
            ),
            onChanged: (val) => setState(() => _searchPelanggaranQuery = val),
          ),
          const SizedBox(height: 10),

          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: ['Semua', 'Ringan', 'Sedang', 'Berat'].map((kat) {
                final isSelected = _kategoriPelanggaran == kat;
                return Padding(
                  padding: const EdgeInsets.only(right: 6),
                  child: FilterChip(
                    label: Text(kat, style: const TextStyle(fontSize: 11)),
                    selected: isSelected,
                    selectedColor: AppColors.roseDanger,
                    checkmarkColor: Colors.white,
                    onSelected: (val) {
                      setState(() => _kategoriPelanggaran = kat);
                      provider.fetchPelanggaranMurid(
                        ruanganId: _selectedRuanganId,
                        kategori: kat,
                      );
                    },
                  ),
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 12),

          // 3. Leaderboard Poin Pelanggaran Santri
          ...filteredSantri.map((s) {
            final isPutra = s.jenisKelamin == 'L';
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
                    backgroundImage: s.foto != null
                        ? NetworkImage(s.foto!)
                        : null,
                    child: s.foto == null
                        ? Icon(
                            isPutra ? Icons.face_rounded : Icons.face_3_rounded,
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
                          s.nama,
                          style: const TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.bold,
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                        Text(
                          'NISM: ${s.nism} • ${s.totalKasus} Kasus Terdaftar',
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
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        '${s.totalPoin} Poin',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w900,
                          color: s.totalPoin == 0
                              ? AppColors.hadirTextLight
                              : (s.totalPoin <= 10
                                    ? AppColors.amberAccent
                                    : AppColors.roseDanger),
                        ),
                      ),
                      const SizedBox(height: 2),
                      Text(
                        s.statusKedisiplinan,
                        style: TextStyle(
                          fontSize: 9.5,
                          fontWeight: FontWeight.bold,
                          color: s.totalPoin == 0
                              ? AppColors.hadirTextLight
                              : (s.totalPoin <= 10
                                    ? AppColors.amberAccent
                                    : AppColors.roseDanger),
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
  // TAB 4: LAPORAN UJIAN & LEGER
  // =========================================================================
  Widget _buildUjianTab(bool isDark, LaporanProvider provider) {
    final data = provider.ujian;
    if (provider.isLoadingUjian) {
      return const ShimmerLoadingList(count: 4, height: 110);
    }
    if (data == null) {
      return const GlassCard(
        margin: EdgeInsets.all(16),
        padding: EdgeInsets.all(24),
        child: Center(child: Text('Gagal memuat laporan ujian.')),
      );
    }

    final filteredMurid = data.rekapMurid.where((m) {
      return m.nama.toLowerCase().contains(_searchUjianQuery.toLowerCase()) ||
          m.nism.contains(_searchUjianQuery);
    }).toList();

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
                    Row(
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
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Hasil ${data.ujian.namaUjian}',
                              style: const TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              'Kelas: ${data.namaRuangan} • ${data.totalSantri} Murid',
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
                      '${data.jumlahTuntas}',
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

          // 3. Search Bar
          TextField(
            decoration: const InputDecoration(
              hintText: 'Cari murid...',
              prefixIcon: Icon(Icons.search_rounded, size: 20),
              contentPadding: EdgeInsets.symmetric(vertical: 8, horizontal: 12),
              isDense: true,
            ),
            onChanged: (val) => setState(() => _searchUjianQuery = val),
          ),
          const SizedBox(height: 12),

          // 4. Daftar Santri & Peringkat Bintang Pelajar
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
                  'NISM: ${m.nism} • Rata-rata: ${m.rataRata}',
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
      return const GlassCard(
        margin: EdgeInsets.all(16),
        padding: EdgeInsets.all(24),
        child: Center(child: Text('Gagal memuat laporan kenaikan kelas.')),
      );
    }

    final filteredMurid = data.dataKenaikan.where((m) {
      return m.nama.toLowerCase().contains(
            _searchKenaikanQuery.toLowerCase(),
          ) ||
          m.nism.contains(_searchKenaikanQuery);
    }).toList();

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
                    Row(
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
                        Column(
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
                            ),
                            Text(
                              'Bobot: IMDA 60% • Hadir 24% • Tertib 16%',
                              style: TextStyle(
                                fontSize: 10.5,
                                color: isDark
                                    ? const Color(0xFF8D9387)
                                    : const Color(0xFF73796E),
                              ),
                            ),
                          ],
                        ),
                      ],
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
                        '${data.totalSantri} Murid',
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
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 14),

          // 2. Search Bar
          TextField(
            decoration: const InputDecoration(
              hintText: 'Cari murid...',
              prefixIcon: Icon(Icons.search_rounded, size: 20),
              contentPadding: EdgeInsets.symmetric(vertical: 8, horizontal: 12),
              isDense: true,
            ),
            onChanged: (val) => setState(() => _searchKenaikanQuery = val),
          ),
          const SizedBox(height: 12),

          // 3. Daftar Status Kenaikan Santri
          ...filteredMurid.map((m) {
            final isNaik =
                m.keputusanFinal == 'Naik Kelas' || m.keputusanFinal == 'Lulus';

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
                        child: Text(
                          m.nama,
                          style: const TextStyle(
                            fontSize: 13.5,
                            fontWeight: FontWeight.bold,
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
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
                              setState(() => _selectedRuanganId = newId);
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
