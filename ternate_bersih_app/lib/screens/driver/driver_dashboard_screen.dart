import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import '../../services/api_service.dart';
import 'driver_history_screen.dart';
import 'driver_home_screen.dart';
import '../profile/profile_screen.dart';

class DriverDashboardScreen extends StatefulWidget {
  const DriverDashboardScreen({super.key});

  @override
  State<DriverDashboardScreen> createState() => _DriverDashboardScreenState();
}

class _DriverDashboardScreenState extends State<DriverDashboardScreen> {
  int _currentIndex = 0;

  Future<void> _handleLogout() async {
    bool confirm = await showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Keluar'),
        content: const Text('Apakah Anda yakin ingin keluar dari akun Petugas Armada?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Batal')),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Keluar', style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    ) ?? false;

    if (confirm && mounted) {
      await ApiService.logout();
      Navigator.pushReplacementNamed(context, '/login');
    }
  }

  Widget _buildBody() {
    if (_currentIndex == 0) {
      return DriverHomeScreen(
        onNavigateToTasks: () {
          setState(() {
            _currentIndex = 1;
          });
        },
      );
    }
    if (_currentIndex == 2) return const DriverHistoryScreen();
    if (_currentIndex == 3) return const ProfileScreen();
    
    // Index 1 (Peta & Tugas)
    return FutureBuilder<List<dynamic>>(
      future: ApiService.fetchDriverTasks(),
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (snapshot.hasError) {
            return const Center(child: Text('Gagal memuat daftar tugas.'));
          }

          final tasks = snapshot.data ?? [];

          if (tasks.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.check_circle_outline_rounded, size: 64, color: Colors.grey.shade300),
                  const SizedBox(height: 16),
                  const Text('Tidak ada tugas tersisa hari ini.\nKerja bagus!', 
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.grey),
                  ),
                ],
              ),
            );
          }

          // Kumpulkan marker untuk peta
          List<Marker> mapMarkers = tasks.map((task) {
            double lat = double.tryParse(task['lat']?.toString() ?? '0') ?? 0.0;
            double lng = double.tryParse(task['lng']?.toString() ?? '0') ?? 0.0;

            return Marker(
              point: LatLng(lat, lng),
              width: 40,
              height: 40,
              child: const Icon(Icons.location_on, color: Colors.red, size: 40),
            );
          }).toList();

          // Hitung pusat peta (rata-rata lokasi atau titik pertama)
          LatLng mapCenter = mapMarkers.isNotEmpty 
              ? mapMarkers.first.point 
              : const LatLng(0.7893, 127.3620); // Default Ternate

          return Column(
            children: [
              // Peta di bagian atas
              SizedBox(
                height: MediaQuery.of(context).size.height * 0.35,
                child: FlutterMap(
                  options: MapOptions(
                    initialCenter: mapCenter,
                    initialZoom: 14.0,
                  ),
                  children: [
                    TileLayer(
                      urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                      userAgentPackageName: 'com.example.ternate_bersih',
                    ),
                    MarkerLayer(markers: mapMarkers),
                  ],
                ),
              ),
              
              // Ringkasan Harian dan Daftar Tugas
              Expanded(
                child: RefreshIndicator(
                  color: const Color(0xFF0D9488),
                  onRefresh: () async {
                    setState(() {});
                  },
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Header Ringkasan Harian
                      Container(
                        padding: const EdgeInsets.fromLTRB(20, 20, 20, 10),
                        child: Row(
                          children: [
                            const CircleAvatar(
                              radius: 24,
                              backgroundColor: Color(0xFF0D9488),
                              child: Icon(Icons.local_shipping_rounded, color: Colors.white, size: 28),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text('Ringkasan Hari Ini', style: TextStyle(fontSize: 14, color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
                                  Text(
                                    'Ada ${tasks.length} tugas tersisa',
                                    style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color(0xFF1E293B)),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                      
                      // Daftar Tugas
                      Expanded(
                        child: ListView.builder(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          itemCount: tasks.length,
                    itemBuilder: (context, index) {
                      final task = tasks[index];
                      
                      String? photoUrl;
                      if (task['photo_path'] != null) {
                        photoUrl = ApiService.getImageUrl(task['photo_path']);
                      }

                      // Menentukan warna badge prioritas
                      String priority = task['priority'] ?? 'Sedang';
                      Color priorityColor = const Color(0xFFF59E0B); // Default Kuning/Sedang
                      Color priorityBg = const Color(0xFFFEF3C7);
                      if (priority.toLowerCase() == 'tinggi') {
                        priorityColor = const Color(0xFFDC2626); // Merah
                        priorityBg = const Color(0xFFFEE2E2);
                      } else if (priority.toLowerCase() == 'rendah') {
                        priorityColor = const Color(0xFF059669); // Hijau
                        priorityBg = const Color(0xFFD1FAE5);
                      }

                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF0F172A).withValues(alpha: 0.04),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                          border: Border.all(color: const Color(0xFFF1F5F9), width: 1.5),
                        ),
                        child: Material(
                          color: Colors.transparent,
                          child: InkWell(
                            borderRadius: BorderRadius.circular(20),
                            onTap: () {
                              Navigator.pushNamed(context, '/driver_task_detail', arguments: task).then((_) {
                                setState(() {});
                              });
                            },
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.center,
                                children: [
                                  // Thumbnail Foto Sampah atau Ikon Default
                                  Container(
                                    width: 70,
                                    height: 70,
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFE6F4F1),
                                      borderRadius: BorderRadius.circular(16),
                                      image: photoUrl != null
                                          ? DecorationImage(
                                              image: NetworkImage(photoUrl),
                                              fit: BoxFit.cover,
                                            )
                                          : null,
                                      boxShadow: [
                                        BoxShadow(
                                          color: Colors.black.withValues(alpha: 0.05),
                                          blurRadius: 4,
                                          offset: const Offset(0, 2),
                                        ),
                                      ],
                                    ),
                                    child: photoUrl == null
                                        ? const Icon(Icons.delete_sweep_rounded, color: Color(0xFF0D9488), size: 32)
                                        : null,
                                  ),
                                  const SizedBox(width: 16),
                                  
                                  // Detail Tugas
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Expanded(
                                              child: Text(
                                                task['report_number'] ?? 'Laporan',
                                                style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15, color: Color(0xFF1E293B)),
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                            const SizedBox(width: 8),
                                            // Badge Prioritas
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                              decoration: BoxDecoration(
                                                color: priorityBg,
                                                borderRadius: BorderRadius.circular(8),
                                              ),
                                              child: Row(
                                                children: [
                                                  Icon(
                                                    priority.toLowerCase() == 'tinggi' ? Icons.warning_rounded : Icons.info_outline_rounded,
                                                    size: 12,
                                                    color: priorityColor,
                                                  ),
                                                  const SizedBox(width: 4),
                                                  Text(
                                                    priority,
                                                    style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: priorityColor),
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 6),
                                        Row(
                                          children: [
                                            const Icon(Icons.location_on_rounded, size: 14, color: Color(0xFF94A3B8)),
                                            const SizedBox(width: 4),
                                            Expanded(
                                              child: Text(
                                                task['address'] ?? 'Alamat tidak diketahui',
                                                style: const TextStyle(color: Color(0xFF64748B), fontSize: 13, fontWeight: FontWeight.w500),
                                                maxLines: 2,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  
                                  // Tombol Aksi Kanan
                                  Container(
                                    padding: const EdgeInsets.all(8),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFF0D9488).withValues(alpha: 0.1),
                                      shape: BoxShape.circle,
                                    ),
                                    child: const Icon(Icons.arrow_forward_rounded, color: Color(0xFF0D9488), size: 20),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  },
);
}

  PreferredSizeWidget _buildAppBar() {
    return AppBar(
      backgroundColor: Colors.white,
      surfaceTintColor: Colors.white,
      elevation: 0,
      titleSpacing: 20,
      title: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFF0F766E), Color(0xFF0D9488)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
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
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Belum ada notifikasi baru.')),
              );
            },
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: _currentIndex == 0 
          ? _buildAppBar() 
          : (_currentIndex == 1 ? AppBar(
              backgroundColor: const Color(0xFF0D9488),
              title: const Text('Peta & Tugas Armada', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              actions: [
                IconButton(
                  icon: const Icon(Icons.logout_rounded, color: Colors.white),
                  onPressed: _handleLogout,
                ),
              ],
            ) : null),
      body: _buildBody(),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) {
          setState(() {
            _currentIndex = index;
          });
        },
        selectedItemColor: const Color(0xFF0D9488),
        unselectedItemColor: const Color(0xFF94A3B8),
        backgroundColor: Colors.white,
        elevation: 10,
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.home_rounded),
            label: 'Beranda',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.map_rounded),
            label: 'Tugas',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.history_rounded),
            label: 'Riwayat',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.person_rounded),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}
