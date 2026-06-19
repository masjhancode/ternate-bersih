import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class HistoryScreen extends StatefulWidget {
  const HistoryScreen({super.key});

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen> {
  String _selectedFilter = 'Semua';
  final List<String> _filters = ['Semua', 'Menunggu', 'Diproses', 'Selesai', 'Ditolak'];
  Timer? _pollingTimer;

  @override
  void initState() {
    super.initState();
    // Memuat data secara background setiap 3 detik agar realtime
    _pollingTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
      if (mounted) {
        setState(() {});
      }
    });
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
      appBar: AppBar(
        title: const Text('Riwayat Laporan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: Column(
        children: [
          _buildFilterMenu(),
          Expanded(
            child: RefreshIndicator(
              color: const Color(0xFF0D9488),
              onRefresh: () async {
                setState(() {});
              },
              child: FutureBuilder<List<dynamic>>(
                future: ApiService.fetchReports(),
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return const Center(child: CircularProgressIndicator(color: Color(0xFF0D9488)));
                  }

                  if (snapshot.hasError) {
                    return ListView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      children: const [
                        SizedBox(height: 100),
                        Center(child: Text('Gagal memuat data riwayat', style: TextStyle(color: Colors.red))),
                      ],
                    );
                  }

                  final allReports = snapshot.data ?? [];
                  
                  // Logika Filter
                  final reports = allReports.where((report) {
                    if (_selectedFilter == 'Semua') return true;
                    
                    String status = report['status'] ?? '';
                    if (_selectedFilter == 'Menunggu') {
                      return status == 'Menunggu Verifikasi';
                    } else if (_selectedFilter == 'Diproses') {
                      return status == 'Dalam Penanganan' || status == 'Diverifikasi' || status == 'Ditugaskan' || status == 'Diproses';
                    } else if (_selectedFilter == 'Selesai') {
                      return status == 'Selesai';
                    } else if (_selectedFilter == 'Ditolak') {
                      return status == 'Ditolak';
                    }
                    return true;
                  }).toList();

                  if (reports.isEmpty) {
                    return ListView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      children: [
                        SizedBox(height: MediaQuery.of(context).size.height * 0.25),
                        Center(
                          child: Column(
                            children: [
                              Icon(Icons.history_rounded, size: 64, color: Colors.grey.shade300),
                              const SizedBox(height: 16),
                              Text('Belum ada riwayat $_selectedFilter', style: const TextStyle(color: Color(0xFF64748B), fontSize: 16)),
                            ],
                          ),
                        ),
                      ],
                    );
                  }

                  return ListView.builder(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(20),
              itemCount: reports.length,
              itemBuilder: (context, index) {
                final report = reports[index];
                String dateStr = report['created_at'] ?? '';
                if (dateStr.length > 16) dateStr = dateStr.substring(0, 16).replaceAll('T', ' ');

                return GestureDetector(
                  onTap: () {
                    Navigator.pushNamed(context, '/report_detail', arguments: report);
                  },
                  child: Container(
                    margin: const EdgeInsets.only(bottom: 16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF0F172A).withValues(alpha: 0.03),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
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
                            height: 160,
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
          },
        ),
      ),
      ),
        ],
      ),
    );
  }

  Widget _buildFilterMenu() {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
      physics: const BouncingScrollPhysics(),
      child: Row(
        children: _filters.map((filter) {
          final isSelected = _selectedFilter == filter;
          return Padding(
            padding: const EdgeInsets.only(right: 8),
            child: ChoiceChip(
              label: Text(
                filter,
                style: TextStyle(
                  color: isSelected ? Colors.white : const Color(0xFF64748B),
                  fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
                  fontSize: 13,
                ),
              ),
              showCheckmark: false,
              selected: isSelected,
              selectedColor: const Color(0xFF0D9488),
              backgroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
                side: BorderSide(
                  color: isSelected ? const Color(0xFF0D9488) : const Color(0xFFE2E8F0),
                ),
              ),
              onSelected: (selected) {
                if (selected && _selectedFilter != filter) {
                  setState(() => _selectedFilter = filter);
                }
              },
            ),
          );
        }).toList(),
      ),
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

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(8)),
      child: Text(status, style: TextStyle(color: textColor, fontSize: 10, fontWeight: FontWeight.bold)),
    );
  }
}
