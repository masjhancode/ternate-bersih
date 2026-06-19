import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../services/api_service.dart';

class DriverTaskDetailScreen extends StatefulWidget {
  const DriverTaskDetailScreen({super.key});

  @override
  State<DriverTaskDetailScreen> createState() => _DriverTaskDetailScreenState();
}

class _DriverTaskDetailScreenState extends State<DriverTaskDetailScreen> {
  bool _isLoading = false;
  File? _proofPhoto;

  Future<void> _pickImage(ImageSource source) async {
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(source: source, imageQuality: 70);

    if (pickedFile != null) {
      setState(() {
        _proofPhoto = File(pickedFile.path);
      });
    }
  }

  void _showImageSourceBottomSheet() {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) {
        return SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 20),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text(
                  'Ambil Bukti Foto',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color(0xFF1E293B)),
                ),
                const SizedBox(height: 16),
                ListTile(
                  leading: const CircleAvatar(
                    backgroundColor: Color(0xFFE0F2FE),
                    child: Icon(Icons.camera_alt_rounded, color: Color(0xFF0284C7)),
                  ),
                  title: const Text('Gunakan Kamera', style: TextStyle(fontWeight: FontWeight.w600)),
                  onTap: () {
                    Navigator.pop(context);
                    _pickImage(ImageSource.camera);
                  },
                ),
                ListTile(
                  leading: const CircleAvatar(
                    backgroundColor: Color(0xFFF3E8FF),
                    child: Icon(Icons.photo_library_rounded, color: Color(0xFF9333EA)),
                  ),
                  title: const Text('Pilih dari Galeri', style: TextStyle(fontWeight: FontWeight.w600)),
                  onTap: () {
                    Navigator.pop(context);
                    _pickImage(ImageSource.gallery);
                  },
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Future<void> _completeTask(int reportId) async {
    if (_proofPhoto == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Harap ambil foto bukti terlebih dahulu.')));
      return;
    }

    setState(() => _isLoading = true);

    bool success = await ApiService.completeDriverTask(
      reportId: reportId,
      proofPhoto: _proofPhoto!,
      notes: 'Area telah dibersihkan oleh armada.',
    );

    if (mounted) {
      setState(() => _isLoading = false);
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Tugas berhasil diselesaikan!'), backgroundColor: Colors.green));
        Navigator.pop(context, true); // Kembali dan beritahu list untuk refresh
      } else {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Gagal menyelesaikan tugas. Coba lagi.'), backgroundColor: Colors.red));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final Map<String, dynamic> task = ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
    
    String? photoUrl;
    if (task['photo_path'] != null) {
      photoUrl = ApiService.getImageUrl(task['photo_path']);
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Detail Tugas', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Gambar Bukti Laporan
            Container(
              height: 250,
              width: double.infinity,
              color: const Color(0xFFE2E8F0),
              child: photoUrl != null
                  ? Image.network(
                      photoUrl,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => const Center(child: Icon(Icons.broken_image_rounded, size: 50, color: Color(0xFF94A3B8))),
                    )
                  : const Center(child: Icon(Icons.image_not_supported_rounded, size: 50, color: Color(0xFF94A3B8))),
            ),

            Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          task['report_number'] ?? '-', 
                          style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Color(0xFF0D9488)),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(color: const Color(0xFFFEF3C7), borderRadius: BorderRadius.circular(8)),
                        child: const Text('Tugas Aktif', style: TextStyle(color: Color(0xFFD97706), fontSize: 10, fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),
                  _buildInfoRow(Icons.category_rounded, 'Kategori', task['category'] != null ? task['category']['name'] : 'Umum'),
                  const SizedBox(height: 16),
                  _buildInfoRow(Icons.location_on_rounded, 'Lokasi', task['address'] ?? '-'),
                  const SizedBox(height: 16),
                  _buildInfoRow(Icons.description_rounded, 'Catatan Warga', task['description'] ?? 'Tidak ada catatan'),
                  const SizedBox(height: 32),

                  // Buka Maps
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      onPressed: () async {
                        final lat = task['lat'];
                        final lng = task['lng'];
                        if (lat != null && lng != null) {
                          final url = Uri.parse('https://maps.google.com/?q=$lat,$lng');
                          try {
                            await launchUrl(url, mode: LaunchMode.externalApplication);
                          } catch (e) {
                            if (mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(content: Text('Tidak dapat membuka Google Maps atau Browser.')),
                              );
                            }
                          }
                        } else {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Koordinat lokasi tidak tersedia.')),
                          );
                        }
                      },
                      icon: const Icon(Icons.map_rounded),
                      label: const Text('Buka Rute di Google Maps'),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                  ),
                  
                  const SizedBox(height: 32),
                  const Divider(),
                  const SizedBox(height: 16),

                  const Text('Bukti Penyelesaian', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 12),
                  
                  // Kotak Foto Bukti
                  GestureDetector(
                    onTap: _showImageSourceBottomSheet,
                    child: Container(
                      height: 180,
                      width: double.infinity,
                      decoration: BoxDecoration(
                        color: _proofPhoto == null ? const Color(0xFFF1F5F9) : null,
                        border: Border.all(color: const Color(0xFFCBD5E1), style: BorderStyle.solid),
                        borderRadius: BorderRadius.circular(12),
                        image: _proofPhoto != null ? DecorationImage(image: FileImage(_proofPhoto!), fit: BoxFit.cover) : null,
                      ),
                      child: _proofPhoto == null
                          ? const Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.camera_alt_rounded, size: 40, color: Color(0xFF94A3B8)),
                                SizedBox(height: 8),
                                Text('Ambil Foto Area Bersih', style: TextStyle(color: Color(0xFF64748B), fontWeight: FontWeight.bold)),
                              ],
                            )
                          : null,
                    ),
                  ),

                  const SizedBox(height: 24),

                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: _isLoading ? null : () => _completeTask(task['id']),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF0D9488),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: _isLoading 
                          ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                          : const Text('Tandai Selesai', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String title, String content) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, color: const Color(0xFF0D9488), size: 20),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: const TextStyle(fontSize: 12, color: Colors.grey)),
              const SizedBox(height: 2),
              Text(content, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500)),
            ],
          ),
        ),
      ],
    );
  }
}
