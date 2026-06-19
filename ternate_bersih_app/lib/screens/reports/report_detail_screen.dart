import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class ReportDetailScreen extends StatefulWidget {
  const ReportDetailScreen({super.key});

  @override
  State<ReportDetailScreen> createState() => _ReportDetailScreenState();
}

class _ReportDetailScreenState extends State<ReportDetailScreen> {
  late Map<String, dynamic> report;
  Timer? _pollingTimer;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    // Ambil data awal dari parameter navigasi hanya sekali
    if (_pollingTimer == null) {
      report = ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
      
      // Mulai polling setiap 3 detik untuk mendapatkan update status terbaru
      _pollingTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
        _refreshReportData();
      });
    }
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    super.dispose();
  }

  Future<void> _refreshReportData() async {
    final updatedReport = await ApiService.fetchReportDetail(report['id']);
    if (updatedReport != null && mounted) {
      // Hanya update UI jika ada perubahan status atau prioritas
      if (updatedReport['status'] != report['status'] || updatedReport['priority'] != report['priority']) {
        setState(() {
          report = updatedReport;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    // Memformat path foto menjadi URL absolut ke Laravel Storage
    String? photoUrl;
    if (report['photo_path'] != null) {
      photoUrl = ApiService.getImageUrl(report['photo_path']);
    }

    String dateStr = report['created_at'] ?? '';
    if (dateStr.length > 16) dateStr = dateStr.substring(0, 16).replaceAll('T', ' ');

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Detail Laporan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        actions: [
          _buildStatusBadge(report['status'] ?? 'Menunggu Verifikasi'),
          const SizedBox(width: 16),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // 1. Gambar Bukti Laporan
            Container(
              height: 250,
              width: double.infinity,
              color: const Color(0xFFE2E8F0),
              child: photoUrl != null
                  ? Image.network(
                      photoUrl,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => const Center(
                        child: Icon(Icons.broken_image_rounded, size: 50, color: Color(0xFF94A3B8)),
                      ),
                    )
                  : const Center(
                      child: Icon(Icons.image_not_supported_rounded, size: 50, color: Color(0xFF94A3B8)),
                    ),
            ),

            // 2. Konten Detail Laporan
            Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Nomor Resi & Tanggal
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Expanded(
                        child: Text(
                          report['report_number'] ?? '-',
                          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: Color(0xFF0D9488)),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Text(dateStr, style: const TextStyle(fontSize: 12, color: Color(0xFF64748B), height: 1.5)),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // Informasi Kategori
                  _buildInfoRow(Icons.category_rounded, 'Kategori Masalah', report['category'] != null ? report['category']['name'] : 'Umum'),
                  
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 16),
                    child: Divider(color: Color(0xFFE2E8F0), height: 1),
                  ),

                  // Informasi Lokasi
                  _buildInfoRow(Icons.location_on_rounded, 'Lokasi Tumpukan', report['address'] ?? '-'),

                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 16),
                    child: Divider(color: Color(0xFFE2E8F0), height: 1),
                  ),

                  // Keterangan Pelapor
                  _buildInfoRow(Icons.description_rounded, 'Keterangan Anda', report['description'] ?? 'Tidak ada keterangan tambahan'),
                  
                  const SizedBox(height: 32),

                  // 3. Timeline Pelacakan (Tracking Progress)
                  const Text('Pelacakan Proses', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF1E293B))),
                  const SizedBox(height: 16),
                  
                  _buildTimelineItem(
                    isFirst: true,
                    isLast: false,
                    isCompleted: true,
                    title: 'Laporan Diterima',
                    description: 'Laporan Anda telah berhasil masuk ke dalam sistem kami.',
                    time: dateStr,
                  ),
                  _buildTimelineItem(
                    isFirst: false,
                    isLast: false,
                    isCompleted: _isStatusPassed(report['status'], 'Diverifikasi'),
                    title: 'Verifikasi Admin',
                    description: 'Admin DLH sedang memvalidasi laporan Anda.',
                    time: '',
                  ),
                  _buildTimelineItem(
                    isFirst: false,
                    isLast: false,
                    isCompleted: _isStatusPassed(report['status'], 'Ditugaskan'),
                    title: 'Armada Menuju Lokasi',
                    description: 'Petugas kebersihan telah dikerahkan ke titik lokasi.',
                    time: '',
                  ),
                  _buildTimelineItem(
                    isFirst: false,
                    isLast: true,
                    isCompleted: _isStatusPassed(report['status'], 'Selesai'),
                    title: 'Selesai Dibersihkan',
                    description: 'Tumpukan sampah telah berhasil diangkut.',
                    time: '',
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: report['status'] == 'Menunggu Verifikasi' 
          ? Container(
              padding: const EdgeInsets.all(16),
              decoration: const BoxDecoration(
                color: Colors.white,
                border: Border(top: BorderSide(color: Color(0xFFE2E8F0))),
              ),
              child: SafeArea(
                child: ElevatedButton.icon(
                  onPressed: () => _confirmDelete(context),
                  icon: const Icon(Icons.delete_outline, color: Colors.white),
                  label: const Text('Hapus Laporan', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFDC2626), // Merah
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    elevation: 0,
                  ),
                ),
              ),
            )
          : null,
    );
  }

  void _confirmDelete(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Hapus Laporan?'),
        content: const Text('Apakah Anda yakin ingin membatalkan dan menghapus laporan pengaduan ini? Tindakan ini tidak dapat dibatalkan.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Batal', style: TextStyle(color: Colors.grey)),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(ctx);
              _deleteReport();
            },
            child: const Text('Ya, Hapus', style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Future<void> _deleteReport() async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => const Center(child: CircularProgressIndicator()),
    );

    final result = await ApiService.deleteReport(report['id']);
    
    if (!mounted) return;
    Navigator.pop(context); // Tutup loading

    if (result['status'] == 'success') {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Laporan berhasil dihapus'), backgroundColor: Colors.green),
      );
      Navigator.pop(context, true); // Kembali ke dashboard dan beri sinyal refresh
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message']), backgroundColor: Colors.red),
      );
    }
  }

  Widget _buildInfoRow(IconData icon, String title, String content) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(color: const Color(0xFFF0FDF4), borderRadius: BorderRadius.circular(12)),
          child: Icon(icon, color: const Color(0xFF059669), size: 20),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: const TextStyle(fontSize: 12, color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
              const SizedBox(height: 4),
              Text(content, style: const TextStyle(fontSize: 14, color: Color(0xFF1E293B), fontWeight: FontWeight.w500, height: 1.4)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildTimelineItem({
    required bool isFirst,
    required bool isLast,
    required bool isCompleted,
    required String title,
    required String description,
    required String time,
  }) {
    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Garis Vertikal & Bulatan
          SizedBox(
            width: 30,
            child: Column(
              children: [
                Container(
                  width: 2,
                  height: 16,
                  color: isFirst ? Colors.transparent : (isCompleted ? const Color(0xFF0D9488) : const Color(0xFFE2E8F0)),
                ),
                Container(
                  width: 14,
                  height: 14,
                  decoration: BoxDecoration(
                    color: isCompleted ? const Color(0xFF0D9488) : Colors.white,
                    border: Border.all(color: isCompleted ? const Color(0xFF0D9488) : const Color(0xFFCBD5E1), width: 2),
                    shape: BoxShape.circle,
                  ),
                ),
                Expanded(
                  child: Container(
                    width: 2,
                    color: isLast ? Colors.transparent : (isCompleted ? const Color(0xFF0D9488) : const Color(0xFFE2E8F0)),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 12),
          // Konten Timeline
          Expanded(
            child: Padding(
              padding: const EdgeInsets.only(bottom: 24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(title, style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: isCompleted ? const Color(0xFF1E293B) : const Color(0xFF94A3B8))),
                      if (time.isNotEmpty) Text(time, style: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8))),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(description, style: TextStyle(fontSize: 12, color: isCompleted ? const Color(0xFF64748B) : const Color(0xFFCBD5E1), height: 1.4)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  bool _isStatusPassed(String? currentStatus, String targetStep) {
    if (currentStatus == null) return false;
    if (currentStatus == 'Ditolak') return false; // Alur berbeda jika ditolak
    
    // Hirarki status: Menunggu Verifikasi -> Diverifikasi -> Ditugaskan -> Selesai
    List<String> steps = ['Menunggu Verifikasi', 'Diverifikasi', 'Ditugaskan', 'Selesai'];
    
    int currentIndex = steps.indexOf(currentStatus);
    int targetIndex = steps.indexOf(targetStep);
    
    // Jika tidak ditemukan, defaultnya false
    if (currentIndex == -1 || targetIndex == -1) return false;
    
    return currentIndex >= targetIndex;
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
      case 'Ditolak':
        bgColor = const Color(0xFFFEE2E2);
        textColor = const Color(0xFFDC2626);
        break;
      case 'Menunggu Verifikasi':
      default:
        bgColor = const Color(0xFFFEF3C7);
        textColor = const Color(0xFFD97706);
        break;
    }

    return Center(
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
        decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(8)),
        child: Text(status, style: TextStyle(color: textColor, fontSize: 10, fontWeight: FontWeight.bold)),
      ),
    );
  }
}
