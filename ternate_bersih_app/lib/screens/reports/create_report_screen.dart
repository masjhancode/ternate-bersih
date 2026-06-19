import 'dart:io';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http/http.dart' as http;
import 'package:lottie/lottie.dart';
import '../../services/api_service.dart';

class CreateReportScreen extends StatefulWidget {
  const CreateReportScreen({super.key});

  @override
  State<CreateReportScreen> createState() => _CreateReportScreenState();
}

class _CreateReportScreenState extends State<CreateReportScreen> {
  File? _imageFile;
  Position? _currentPosition;
  bool _isLoadingLocation = false;
  bool _isSubmitting = false;

  final _addressController = TextEditingController();
  final _descriptionController = TextEditingController();
  String? _selectedCategory;

  // Data Kategori Dummy (Nanti diganti dengan data dari API)
  final List<String> _categories = [
    'Sampah Menumpuk di Jalan',
    'Sampah di Saluran Air/Sungai',
    'Fasilitas TPS Penuh',
    'Sampah B3/Berbahaya',
    'Lainnya',
  ];

  @override
  void dispose() {
    _addressController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  Future<void> _pickImage(ImageSource source) async {
    final picker = ImagePicker();
    try {
      final pickedFile = await picker.pickImage(
        source: source,
        imageQuality: 80,
      );

      if (pickedFile != null) {
        setState(() => _imageFile = File(pickedFile.path));
        _getCurrentLocation();
      }
    } catch (e) {
      _showErrorSnackBar('Gagal membuka ${source == ImageSource.camera ? 'kamera' : 'galeri'}. (Jika di Simulator, gunakan Galeri)');
    }
  }

  void _showImageSourceDialog() {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) => SafeArea(
        child: Wrap(
          children: [
            const Padding(
              padding: EdgeInsets.fromLTRB(20, 20, 20, 10),
              child: Text('Pilih Sumber Foto Bukti', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
            ),
            ListTile(
              leading: const Icon(Icons.camera_alt_rounded, color: Color(0xFF0D9488)),
              title: const Text('Ambil dari Kamera (Untuk HP Asli)'),
              onTap: () {
                Navigator.pop(context);
                _pickImage(ImageSource.camera);
              },
            ),
            ListTile(
              leading: const Icon(Icons.photo_library_rounded, color: Color(0xFF0D9488)),
              title: const Text('Pilih dari Galeri Foto (Untuk Simulator)'),
              onTap: () {
                Navigator.pop(context);
                _pickImage(ImageSource.gallery);
              },
            ),
            const SizedBox(height: 10),
          ],
        ),
      ),
    );
  }

  Future<void> _getCurrentLocation() async {
    setState(() => _isLoadingLocation = true);

    bool serviceEnabled;
    LocationPermission permission;

    serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      _showErrorSnackBar('Akses GPS/Lokasi pada HP Anda belum diaktifkan.');
      setState(() => _isLoadingLocation = false);
      return;
    }

    permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        _showErrorSnackBar('Izin akses lokasi ditolak oleh Anda.');
        setState(() => _isLoadingLocation = false);
        return;
      }
    }

    if (permission == LocationPermission.deniedForever) {
      _showErrorSnackBar(
        'Izin akses lokasi ditolak secara permanen. Silakan buka pengaturan HP.',
      );
      setState(() => _isLoadingLocation = false);
      return;
    }

    try {
      Position position = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.best,
          timeLimit: Duration(seconds: 10),
        ),
      );
      
      String addressStr = await _reverseGeocode(position.latitude, position.longitude);

      setState(() {
        _currentPosition = position;
        _isLoadingLocation = false;
        _addressController.text = "$addressStr\n(Koordinat: ${position.latitude}, ${position.longitude})";
      });
    } catch (e) {
      // Jika timeout atau gagal, coba ambil lokasi terakhir
      Position? lastPosition = await Geolocator.getLastKnownPosition();
      
      if (lastPosition != null) {
        String addressStr = await _reverseGeocode(lastPosition.latitude, lastPosition.longitude);

        setState(() {
          _currentPosition = lastPosition;
          _isLoadingLocation = false;
          _addressController.text = "$addressStr\n(Koordinat: ${lastPosition.latitude}, ${lastPosition.longitude})";
        });
      } else {
        _showErrorSnackBar('Gagal mendapatkan lokasi. Pastikan Emulator/Simulator memiliki lokasi aktif.');
        setState(() => _isLoadingLocation = false);
      }
    }
  }

  /// Mengubah koordinat GPS menjadi alamat lengkap menggunakan OpenStreetMap Nominatim API
  Future<String> _reverseGeocode(double lat, double lng) async {
    String fallback = "Koordinat: $lat, $lng";
    try {
      final url = Uri.parse(
        'https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lng&zoom=18&addressdetails=1&accept-language=id',
      );
      final response = await http.get(url, headers: {
        'User-Agent': 'TernateBersihApp/1.0',
      }).timeout(const Duration(seconds: 5));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        final addr = data['address'];
        
        if (addr != null) {
          List<String> parts = [];

          // Nama jalan / kompleks / perumahan
          String? road = addr['road'] ?? addr['residential'] ?? addr['hamlet'];
          if (road != null && road.isNotEmpty) parts.add(road);

          // Kelurahan
          String? village = addr['village'] ?? addr['suburb'] ?? addr['neighbourhood'];
          if (village != null && village.isNotEmpty) parts.add('Kel. $village');

          // Kecamatan
          String? district = addr['district'] ?? addr['city_district'];
          if (district != null && district.isNotEmpty) parts.add('Kec. $district');

          // Kota
          String? city = addr['city'] ?? addr['town'] ?? addr['county'];
          if (city != null && city.isNotEmpty) parts.add(city);

          // Kode Pos
          String? postcode = addr['postcode'];
          if (postcode != null && postcode.isNotEmpty) parts.add(postcode);

          if (parts.isNotEmpty) {
            return parts.join(', ');
          }
        }

        // Jika addressdetails kosong, gunakan display_name penuh
        if (data['display_name'] != null) {
          return data['display_name'];
        }
      }
    } catch (e) {
      // Abaikan jika Nominatim gagal, tetap gunakan koordinat
    }
    return fallback;
  }

  void _showErrorSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  void _submitReport() async {
    if (_imageFile == null) {
      _showErrorSnackBar('Harap ambil foto tumpukan sampah terlebih dahulu.');
      return;
    }
    if (_currentPosition == null) {
      _showErrorSnackBar('Menunggu titik kordinat lokasi...');
      return;
    }
    if (_selectedCategory == null) {
      _showErrorSnackBar('Silakan pilih kategori sampah.');
      return;
    }
    if (_addressController.text.isEmpty) {
      _showErrorSnackBar('Silakan lengkapi detail alamat/patokan.');
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final response = await ApiService.submitReport(
        photo: _imageFile!,
        lat: _currentPosition!.latitude,
        lng: _currentPosition!.longitude,
        address: _addressController.text,
        description: _descriptionController.text,
        categoryName: _selectedCategory!,
      );

      if (mounted) {
        setState(() => _isSubmitting = false);
        
        if (response['status'] == 'success') {
          Navigator.pushReplacementNamed(context, '/success_report');
        } else {
          _showErrorSnackBar(response['message'] ?? 'Gagal mengirim laporan. Coba lagi.');
        }
      }
    } catch (e) {
      setState(() => _isSubmitting = false);
      _showErrorSnackBar('Terjadi kesalahan koneksi jaringan ke Server.');
    }
  }



  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text(
          'Lapor Tumpukan Sampah',
          style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
        ),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // 1. Bagian Foto Bukti
            GestureDetector(
              onTap: _showImageSourceDialog,
              child: Container(
                height: 250,
                width: double.infinity,
                color: const Color(0xFFE2E8F0),
                child: _imageFile != null
                    ? Image.file(_imageFile!, fit: BoxFit.cover)
                    : Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: const BoxDecoration(
                              color: Colors.white,
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(
                              Icons.camera_alt_rounded,
                              size: 40,
                              color: Color(0xFF0D9488),
                            ),
                          ),
                          const SizedBox(height: 12),
                          const Text(
                            'Ketuk untuk Membuka Kamera',
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF64748B),
                            ),
                          ),
                          const Text(
                            'Ambil foto bukti tumpukan sampah secara langsung',
                            style: TextStyle(
                              fontSize: 12,
                              color: Color(0xFF94A3B8),
                            ),
                          ),
                        ],
                      ),
              ),
            ),

            Padding(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // 2. Status Lokasi GPS
                  const Text(
                    'Titik Koordinat Lokasi',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF1E293B),
                    ),
                  ),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: const Color(0xFFE2E8F0)),
                    ),
                    child: Row(
                      children: [
                        if (_isLoadingLocation)
                          const SizedBox(
                            width: 24,
                            height: 24,
                            child: CircularProgressIndicator(strokeWidth: 2),
                          )
                        else
                          Icon(
                            Icons.location_on_rounded,
                            color: _currentPosition != null
                                ? const Color(0xFF059669)
                                : const Color(0xFFEF4444),
                          ),

                        const SizedBox(width: 16),
                         Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                _isLoadingLocation
                                    ? 'Mencari satelit GPS...'
                                    : (_currentPosition != null
                                          ? 'Lokasi Berhasil Dikunci'
                                          : 'Lokasi Belum Ditemukan'),
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  color: _currentPosition != null
                                      ? const Color(0xFF059669)
                                      : const Color(0xFF64748B),
                                ),
                              ),
                              if (_currentPosition != null)
                                Text(
                                  'Lat: ${_currentPosition!.latitude.toStringAsFixed(6)}, Lng: ${_currentPosition!.longitude.toStringAsFixed(6)} (±${_currentPosition!.accuracy.toStringAsFixed(0)}m)',
                                  style: const TextStyle(
                                    fontSize: 11,
                                    color: Color(0xFF94A3B8),
                                  ),
                                ),
                            ],
                          ),
                        ),
                        if (_currentPosition != null && !_isLoadingLocation)
                          TextButton.icon(
                            onPressed: _getCurrentLocation,
                            icon: const Icon(Icons.refresh_rounded, size: 16),
                            label: const Text('Kunci Ulang', style: TextStyle(fontSize: 12)),
                            style: TextButton.styleFrom(
                              foregroundColor: const Color(0xFF0D9488),
                              padding: const EdgeInsets.symmetric(horizontal: 8),
                            ),
                          ),
                        if (_currentPosition == null && !_isLoadingLocation)
                          TextButton(
                            onPressed: _getCurrentLocation,
                            child: const Text(
                              'Cari Ulang',
                              style: TextStyle(color: Color(0xFF0D9488)),
                            ),
                          ),
                      ],
                    ),
                  ),
                  if (_currentPosition != null)
                    Padding(
                      padding: const EdgeInsets.only(top: 8),
                      child: Row(
                        children: [
                          Icon(Icons.info_outline_rounded, size: 14, color: Colors.amber.shade700),
                          const SizedBox(width: 6),
                          Expanded(
                            child: Text(
                              'Alamat otomatis mungkin kurang akurat. Silakan perbaiki secara manual di kolom alamat di bawah.',
                              style: TextStyle(fontSize: 11, color: Colors.amber.shade700, fontStyle: FontStyle.italic),
                            ),
                          ),
                        ],
                      ),
                    ),
                  const SizedBox(height: 24),

                  // 3. Kategori Sampah
                  const Text(
                    'Kategori Masalah',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF1E293B),
                    ),
                  ),
                  const SizedBox(height: 8),
                  DropdownButtonFormField<String>(
                    initialValue: _selectedCategory,
                    hint: const Text('Pilih jenis pelaporan'),
                    decoration: InputDecoration(
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                      ),
                    ),
                    items: _categories
                        .map(
                          (cat) => DropdownMenuItem(
                            value: cat,
                            child: Text(
                              cat,
                              style: const TextStyle(fontSize: 14),
                            ),
                          ),
                        )
                        .toList(),
                    onChanged: (val) => setState(() => _selectedCategory = val),
                  ),
                  const SizedBox(height: 24),

                  // 4. Alamat Lengkap / Patokan
                  Row(
                    children: [
                      const Text(
                        'Alamat Lengkap & Patokan',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF1E293B),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: const Color(0xFFD1FAE5),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: const Text('Bisa Diedit', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Color(0xFF059669))),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _addressController,
                    maxLines: 3,
                    decoration: InputDecoration(
                      hintText:
                          'Contoh: Jl. Jati Perumnas, Kel. Jati, dekat tiang listrik kuning...',
                      hintStyle: const TextStyle(
                        fontSize: 14,
                        color: Color(0xFF94A3B8),
                      ),
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                      ),
                    ),
                  ),
                  const SizedBox(height: 24),

                  // 5. Keterangan Tambahan
                  const Text(
                    'Keterangan Tambahan (Opsional)',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF1E293B),
                    ),
                  ),
                  const SizedBox(height: 8),
                  TextField(
                    controller: _descriptionController,
                    maxLines: 3,
                    decoration: InputDecoration(
                      hintText: 'Tambahkan detail lain jika diperlukan...',
                      hintStyle: const TextStyle(
                        fontSize: 14,
                        color: Color(0xFF94A3B8),
                      ),
                      filled: true,
                      fillColor: Colors.white,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(16),
                        borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                      ),
                    ),
                  ),
                  const SizedBox(height: 32),

                  // 6. Tombol Kirim
                  ElevatedButton(
                    onPressed: _isSubmitting ? null : _submitReport,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF0D9488),
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: const Color(0xFF94A3B8),
                      padding: const EdgeInsets.symmetric(vertical: 18),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      elevation: 0,
                      minimumSize: const Size(double.infinity, 50),
                    ),
                    child: _isSubmitting
                        ? const SizedBox(
                            height: 20,
                            width: 20,
                            child: CircularProgressIndicator(
                              color: Colors.white,
                              strokeWidth: 2.5,
                            ),
                          )
                        : const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.send_rounded, size: 20),
                              SizedBox(width: 8),
                              Text(
                                'Kirim Laporan Sekarang',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
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
}
