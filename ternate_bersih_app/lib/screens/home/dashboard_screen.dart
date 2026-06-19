import 'dart:async';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../services/api_service.dart';

import '../profile/profile_screen.dart';
import 'history_screen.dart';
import 'notification_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _currentIndex = 0;
  String _userName = 'Warga';
  Timer? _pollingTimer;
  Map<int, String> _previousStatuses = {};
  bool _isFirstLoad = true;

  @override
  void initState() {
    super.initState();
    _loadUserProfile();
    _startPolling();
  }

  Future<void> _loadUserProfile() async {
    // 1. Ambil secara instan dari cache lokal (agar tidak kosong saat menunggu internet)
    final prefs = await SharedPreferences.getInstance();
    final cachedName = prefs.getString('user_name');
    if (cachedName != null && mounted) {
      setState(() {
        _userName = cachedName;
      });
    }

    // 2. Ambil dari API sebagai pencocokan latar belakang
    final profile = await ApiService.getProfile();
    if (profile != null && mounted) {
      final freshName = profile['name'] ?? '';
      if (freshName.isNotEmpty) {
        prefs.setString('user_name', freshName); // Update cache
        setState(() {
          _userName = freshName;
        });
      }
    }
  }

  void _startPolling() {
    // Mengecek update status setiap 3 detik (Simulasi Realtime)
    _pollingTimer = Timer.periodic(const Duration(seconds: 3), (timer) async {
      await _checkForUpdates();
      if (mounted) {
        setState(() {}); // Memaksa FutureBuilder (jika ada) dan stat ringkasan update
      }
    });
  }

  Future<void> _checkForUpdates() async {
    try {
      final reports = await ApiService.fetchReports();
      bool hasChanges = false;
      String popupMessage = '';

      for (var report in reports) {
        int id = report['id'];
        String newStatus = report['status'] ?? '';

        if (!_isFirstLoad && _previousStatuses.containsKey(id)) {
          if (_previousStatuses[id] != newStatus) {
            hasChanges = true;
            popupMessage = 'Laporan Anda (REP-$id) sekarang berstatus: $newStatus';
            // Perbarui state jika ada perubahan agar UI ikut berubah
            if (mounted) setState(() {});
          }
        }
        _previousStatuses[id] = newStatus;
      }

      _isFirstLoad = false;

      if (hasChanges && mounted) {
        _showNotificationPopup(popupMessage);
      }
    } catch (e) {
      // Abaikan error jaringan saat polling
    }
  }

  void _showNotificationPopup(String message) {
    showDialog(
      context: context,
      builder: (context) {
        return Dialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          elevation: 0,
          backgroundColor: Colors.transparent,
          child: Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFF0D9488).withValues(alpha: 0.1),
                  blurRadius: 20,
                  offset: const Offset(0, 10),
                ),
              ],
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: const Color(0xFF0D9488).withValues(alpha: 0.1),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.notifications_active_rounded,
                    color: Color(0xFF0D9488),
                    size: 40,
                  ),
                ),
                const SizedBox(height: 20),
                const Text(
                  'Pembaruan Laporan!',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF1E293B),
                  ),
                ),
                const SizedBox(height: 12),
                Text(
                  message,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    fontSize: 14,
                    color: Color(0xFF64748B),
                    height: 1.5,
                  ),
                ),
                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton(
                    onPressed: () => Navigator.pop(context),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0D9488),
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: const Text(
                      'Tutup',
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: _currentIndex == 0 ? _buildAppBar() : null, // AppBar hanya di beranda
      body: _buildBody(),
      floatingActionButton: Container(
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF0D9488).withOpacity(0.4),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: FloatingActionButton(
          onPressed: () {
            Navigator.pushNamed(context, '/create_report').then((result) {
              if (result == true) setState(() {});
            });
          },
          backgroundColor: const Color(0xFF0D9488),
          elevation: 0,
          shape: const CircleBorder(),
          child: const Icon(Icons.camera_alt_rounded, color: Colors.white, size: 28),
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
      bottomNavigationBar: BottomAppBar(
        color: Colors.white,
        surfaceTintColor: Colors.white,
        shape: const CircularNotchedRectangle(),
        notchMargin: 8.0,
        elevation: 10,
        shadowColor: Colors.black.withOpacity(0.5),
        child: SizedBox(
          height: 60,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildNavItem(icon: Icons.home_filled, label: 'Beranda', index: 0),
              _buildNavItem(icon: Icons.history_rounded, label: 'Riwayat', index: 1),
              const SizedBox(width: 48), // Ruang kosong untuk FAB di tengah
              _buildNavItem(icon: Icons.notifications_outlined, label: 'Notifikasi', index: 2),
              _buildNavItem(icon: Icons.person_outline, label: 'Profil', index: 3),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem({required IconData icon, required String label, required int index, int badgeCount = 0}) {
    final isSelected = _currentIndex == index;
    final color = isSelected ? const Color(0xFF0D9488) : const Color(0xFF94A3B8);

    return InkWell(
      onTap: () => setState(() => _currentIndex = index),
      customBorder: const CircleBorder(),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Stack(
            clipBehavior: Clip.none,
            children: [
              Icon(icon, color: color, size: 24),
              if (badgeCount > 0)
                Positioned(
                  right: -6,
                  top: -4,
                  child: Container(
                    padding: const EdgeInsets.all(4),
                    decoration: const BoxDecoration(
                      color: Colors.red,
                      shape: BoxShape.circle,
                    ),
                    constraints: const BoxConstraints(
                      minWidth: 16,
                      minHeight: 16,
                    ),
                    child: Text(
                      badgeCount > 9 ? '9+' : badgeCount.toString(),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 9,
                        fontWeight: FontWeight.bold,
                        height: 1,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: TextStyle(
              color: color,
              fontSize: 10,
              fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBody() {
    if (_currentIndex == 1) return const HistoryScreen();
    if (_currentIndex == 2) return const NotificationScreen();
    if (_currentIndex == 3) return const ProfileScreen();

    // Tampilan Beranda Default (dengan Pull-to-Refresh)
    return RefreshIndicator(
      color: const Color(0xFF0D9488),
      onRefresh: () async {
        setState(() {});
      },
      child: FutureBuilder<List<dynamic>>(
        future: ApiService.fetchReports(),
        builder: (context, snapshot) {
          final allReports = snapshot.data ?? [];
          final bool isLoading = snapshot.connectionState == ConnectionState.waiting;

          return SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                _buildGreeting(),
                _buildSummaryGrid(allReports, isLoading),
                _buildMainActionCard(context),
                _buildRecentReportsHeader(),
                _buildRecentReportsList(allReports, isLoading),
                const SizedBox(height: 24),
              ],
            ),
          );
        },
      ),
    );
  }

  String _getGreetingTime() {
    final hour = DateTime.now().hour;
    if (hour < 11) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
  }

  Widget _buildGreeting() {
    final greeting = _getGreetingTime();
    final firstName = _userName.isNotEmpty ? _userName.split(' ').first : '...';

    return Container(
      margin: const EdgeInsets.fromLTRB(20, 20, 20, 8),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        gradient: const LinearGradient(
          colors: [Color(0xFF0F766E), Color(0xFF0D9488)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0D9488).withOpacity(0.3),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Badge SIPAS
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        greeting,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(width: 4),
                      const Text('👋', style: TextStyle(fontSize: 10)),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                // Nama User
                Text(
                  'Halo, $_userName',
                  style: const TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.w900,
                    color: Colors.white,
                    letterSpacing: -0.5,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                // Pesan Mari Jaga Kota
                Text(
                  'Mari Jaga Kota Tetap Bersih!',
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.9),
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Satu laporan Anda sangat berarti.',
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.75),
                    fontSize: 12,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),
          // Ikon Dekorasi
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.15),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.delete_outline_rounded,
              color: Colors.white,
              size: 40,
            ),
          ),
        ],
      ),
    );
  }

  PreferredSizeWidget _buildAppBar() {
    return AppBar(
      backgroundColor: Colors.white,
      elevation: 0,
      surfaceTintColor: Colors.white,
      toolbarHeight: 64,
      title: Row(
        children: [
          // Logo SIPAS
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFF0F766E), Color(0xFF0D9488)],
              ),
              borderRadius: BorderRadius.circular(12),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFF0D9488).withValues(alpha: 0.25),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: const Icon(
              Icons.recycling_rounded,
              size: 24,
              color: Colors.white,
            ),
          ),
          const SizedBox(width: 12),
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'SIPAS',
                style: TextStyle(
                  fontSize: 17,
                  color: Color(0xFF0F766E),
                  fontWeight: FontWeight.w900,
                  letterSpacing: 1.5,
                ),
              ),
              Text(
                'Ternate Bersih',
                style: TextStyle(
                  fontSize: 12,
                  color: Color(0xFF64748B),
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ],
      ),
      actions: [
        // Ikon Notifikasi (Bell)
        Container(
          margin: const EdgeInsets.only(right: 12),
          decoration: BoxDecoration(
            color: const Color(0xFFF1F5F9),
            borderRadius: BorderRadius.circular(12),
          ),
          child: IconButton(
            icon: Stack(
              clipBehavior: Clip.none,
              children: [
                const Icon(
                  Icons.notifications_outlined,
                  color: Color(0xFF334155),
                  size: 24,
                ),
                Positioned(
                  right: -2,
                  top: -2,
                  child: Container(
                    padding: const EdgeInsets.all(4),
                    decoration: const BoxDecoration(
                      color: Colors.red,
                      shape: BoxShape.circle,
                    ),
                    constraints: const BoxConstraints(
                      minWidth: 16,
                      minHeight: 16,
                    ),
                    child: const Text(
                      '1',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 9,
                        fontWeight: FontWeight.bold,
                        height: 1,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ),
                ),
              ],
            ),
            onPressed: () {
              // Pindah ke tab Notifikasi
              setState(() => _currentIndex = 2);
            },
          ),
        ),
      ],
    );
  }



  Widget _buildMainActionCard(BuildContext context) {
    return GestureDetector(
      onTap: () {
        Navigator.pushNamed(context, '/create_report').then((result) {
          // Jika user berhasil kirim laporan, refresh dashboard otomatis
          if (result == true) {
            setState(() {}); // Memaksa FutureBuilder memuat ulang data
          }
        });
      },
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
        padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 24),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: const Color(0xFFE2E8F0)),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF0F172A).withValues(alpha: 0.04),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFFEE2E2),
                borderRadius: BorderRadius.circular(16),
              ),
              child: const Icon(
                Icons.camera_alt_rounded,
                color: Color(0xFFDC2626),
                size: 28,
              ),
            ),
            const SizedBox(width: 16),
            const Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Lapor Tumpukan Sampah',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF1E293B),
                    ),
                  ),
                  SizedBox(height: 4),
                  Text(
                    'Foto, tandai lokasi, dan laporkan!',
                    style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right_rounded, color: Color(0xFF94A3B8)),
          ],
        ),
      ),
    );
  }

  Widget _buildRecentReportsHeader() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(24, 24, 24, 16),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          const Text(
            'Laporan Terakhir Anda',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: Color(0xFF1E293B),
            ),
          ),
          TextButton(
            onPressed: () {},
            style: TextButton.styleFrom(
              padding: EdgeInsets.zero,
              minimumSize: const Size(50, 20),
              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
              foregroundColor: const Color(0xFF0D9488),
            ),
            child: const Text(
              'Lihat Semua',
              style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryGrid(List<dynamic> allReports, bool isLoading) {
    int total = allReports.length;
    int selesai = allReports.where((r) => r['status'] == 'Selesai').length;
    int diproses = allReports.where((r) => r['status'] == 'Diproses' || r['status'] == 'Terverifikasi').length;
    int ditolak = allReports.where((r) => r['status'] == 'Ditolak').length;

    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 8, 20, 24),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Ringkasan Laporan',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF1E293B),
                ),
              ),
              TextButton(
                onPressed: () => setState(() => _currentIndex = 1),
                style: TextButton.styleFrom(
                  foregroundColor: const Color(0xFF0D9488),
                  padding: EdgeInsets.zero,
                  minimumSize: const Size(50, 30),
                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                ),
                child: const Text('Lihat Semua', style: TextStyle(fontWeight: FontWeight.w600)),
              ),
            ],
          ),
          const SizedBox(height: 16),
          if (isLoading)
            const Center(child: Padding(padding: EdgeInsets.all(20), child: CircularProgressIndicator()))
          else
            Row(
              children: [
                Expanded(child: _buildSummaryCard('Total', total.toString(), Icons.analytics_outlined, Colors.blue)),
                const SizedBox(width: 12),
                Expanded(child: _buildSummaryCard('Selesai', selesai.toString(), Icons.check_circle_outline, Colors.green)),
              ],
            ),
          if (!isLoading) const SizedBox(height: 12),
          if (!isLoading)
            Row(
              children: [
                Expanded(child: _buildSummaryCard('Diproses', diproses.toString(), Icons.sync_rounded, Colors.orange)),
                const SizedBox(width: 12),
                Expanded(child: _buildSummaryCard('Ditolak', ditolak.toString(), Icons.cancel_outlined, Colors.red)),
              ],
            ),
        ],
      ),
    );
  }

  Widget _buildSummaryCard(String title, String count, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: color.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  count,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                    color: Color(0xFF1E293B),
                  ),
                ),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                    color: Color(0xFF64748B),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRecentReportsList(List<dynamic> allReports, bool isLoading) {
    if (isLoading) {
      return const Center(child: Padding(padding: EdgeInsets.all(20), child: CircularProgressIndicator()));
    }

    // Menyaring hanya laporan yang AKTIF (Bukan Selesai dan Bukan Ditolak)
    final reports = allReports.where((r) => r['status'] != 'Selesai' && r['status'] != 'Ditolak').toList();

    if (reports.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(32.0),
          child: Column(
            children: [
              Icon(Icons.inbox_rounded, size: 48, color: Colors.grey.shade300),
              const SizedBox(height: 12),
              const Text('Tidak ada laporan aktif', style: TextStyle(color: Colors.grey)),
            ],
          ),
        ),
      );
    }

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.symmetric(horizontal: 20),
      itemCount: reports.length,
      itemBuilder: (context, index) {
        final report = reports[index];
        
        // Format waktu sederhana dari ISO string API
        String dateStr = report['created_at'] ?? '';
        if (dateStr.length > 16) dateStr = dateStr.substring(0, 16).replaceAll('T', ' ');

        return GestureDetector(
          onTap: () {
            Navigator.pushNamed(
              context,
              '/report_detail',
              arguments: report, // Mengirim data laporan ke halaman detail
            ).then((result) {
              // Jika kembali dengan membawa sinyal `true`, refresh dashboard
              if (result == true) {
                    setState(() {});
                  }
                });
              },
              child: Container(
                margin: const EdgeInsets.only(bottom: 12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFF1F5F9)),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF0F172A).withValues(alpha: 0.02),
                      blurRadius: 8,
                      offset: const Offset(0, 2),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Foto sampah di atas (full width)
                    ClipRRect(
                      borderRadius: const BorderRadius.only(
                        topLeft: Radius.circular(20),
                        topRight: Radius.circular(20),
                      ),
                      child: SizedBox(
                        height: 160, // Ketinggian banner foto
                        child: report['photo_path'] != null
                            ? Image.network(
                                ApiService.getImageUrl(report['photo_path']),
                                fit: BoxFit.cover,
                                errorBuilder: (ctx, err, st) => Container(
                                  color: const Color(0xFFF1F5F9),
                                  child: const Icon(Icons.broken_image_rounded, color: Color(0xFF94A3B8), size: 48),
                                ),
                              )
                            : Container(
                                color: const Color(0xFFF1F5F9),
                                child: const Icon(Icons.image_not_supported_rounded, color: Color(0xFF94A3B8), size: 48),
                              ),
                      ),
                    ),
                    // Konten detail laporan di bawah foto
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Expanded(
                                child: Text(
                                  report['report_number'] ?? 'REP-UNKNOWN',
                                  style: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.bold,
                                    color: Color(0xFF0D9488),
                                  ),
                                ),
                              ),
                              Text(
                                dateStr,
                                style: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8)),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                margin: const EdgeInsets.only(top: 2),
                                padding: const EdgeInsets.all(6),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF8FAFC),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: const Icon(Icons.location_on_rounded, size: 14, color: Color(0xFF64748B)),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  report['address'] ?? 'Tanpa Alamat',
                                  style: const TextStyle(fontSize: 13, color: Color(0xFF334155), fontWeight: FontWeight.w500, height: 1.4),
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ),
                          const Padding(
                            padding: EdgeInsets.symmetric(vertical: 16),
                            child: Divider(color: Color(0xFFF1F5F9), height: 1),
                          ),
                          Row(
                            children: [
                              _buildStatusBadge(report['status'] ?? 'Menunggu Verifikasi'),
                              const Spacer(),
                              const Text(
                                'Detail',
                                style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF0D9488)),
                              ),
                              const SizedBox(width: 4),
                              const Icon(Icons.arrow_forward_ios_rounded, size: 12, color: Color(0xFF0D9488)),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
          );
      },
    );
  }

  Widget _buildStatusBadge(String status) {
    Color bgColor;
    Color textColor;

    switch (status) {
      case 'Dalam Penanganan':
      case 'Diverifikasi':
      case 'Ditugaskan':
        bgColor = const Color(0xFFE0E7FF);
        textColor = const Color(0xFF4338CA);
        break;
      case 'Selesai':
        bgColor = const Color(0xFFD1FAE5);
        textColor = const Color(0xFF059669);
        break;
      case 'Menunggu Verifikasi':
      default:
        bgColor = const Color(0xFFFEF3C7);
        textColor = const Color(0xFFD97706);
        break;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        status,
        style: TextStyle(
          color: textColor,
          fontSize: 10,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}
