import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static String get hostUrl {
    // Karena kita sudah mengaktifkan jembatan kabel USB (adb reverse),
    // HP Android sekarang bisa langsung mengakses localhost Mac!
    return 'http://127.0.0.1:8000';
  }

  static String get baseUrl => '$hostUrl/api';

  static String getImageUrl(String path) {
    return '$hostUrl/storage/$path';
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  // Fungsi Login Asli ke API
  static Future<bool> login(String email, String password) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      String? fcmToken = prefs.getString('fcm_token');

      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {'Accept': 'application/json'},
        body: {
          'email': email, 
          'password': password,
          if (fcmToken != null) 'fcm_token': fcmToken,
        },
      );

      print('--- DEBUG LOGIN ---');
      print('Status Code: ${response.statusCode}');
      print('Response Body: ${response.body}');

      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'success') {
          // Simpan token ke HP
          await prefs.setString('auth_token', data['data']['access_token']);
          await prefs.setString('user_name', data['data']['user']['name'] ?? 'Warga');
          return true;
        }
      }
      return false;
    } catch (e) {
      print('--- DEBUG LOGIN ERROR ---');
      print('Exception: $e');
      return false;
    }
  }

  // Fungsi Register Asli ke API
  static Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String nik,
    required String phone,
    required String password,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/register'),
        headers: {'Accept': 'application/json'},
        body: {
          'name': name,
          'email': email,
          'nik': nik,
          'phone_number': phone,
          'password': password,
          'password_confirmation': password,
          'village_id': '1', 
          'address': 'Ternate',
        },
      );

      var data = json.decode(response.body);

      if (response.statusCode == 201 && data['status'] == 'success') {
        return {'success': true, 'message': 'Berhasil register'};
      }
      
      // Ambil pesan error detail dari Laravel
      String errorMsg = data['message'] ?? 'Registrasi gagal';
      if (data['errors'] != null) {
         // Ambil error validasi pertama yang muncul
         var firstErrorKey = data['errors'].keys.first;
         errorMsg = data['errors'][firstErrorKey][0];
      }
      return {'success': false, 'message': errorMsg};

    } catch (e) {
      return {'success': false, 'message': 'Gagal menyambung ke server. Pastikan Server jalan.'};
    }
  }

  // Fungsi khusus untuk mengirim Laporan menggunakan MultipartRequest (untuk unggah gambar)
  static Future<Map<String, dynamic>> submitReport({
    required File photo,
    required double lat,
    required double lng,
    required String address,
    required String description,
    required String categoryName, // Idealnya pakai category_id, ini disederhanakan
  }) async {
    final token = await getToken();
    
    // Fallback: Jika belum login beneran, pakai token dummy (Jika API belum dilindungi)
    // Tapi karena API kita pakai sanctum, kita butuh token. Jika belum, API mungkin menolak.
    // Untuk tes, jika belum ada token, kita bypass atau harus pastikan fungsi Login sudah dibuat.

    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/reports'));
    
    if (token != null) {
      request.headers['Authorization'] = 'Bearer $token';
    }
    request.headers['Accept'] = 'application/json';

    // Karena Laravel kita butuh category_id (angka), kita konversi nama kategori ke ID sembarang (1-5)
    // Pada skenario nyata, ID ini didapat dari API Master Data.
    String categoryId = '1'; 
    if (categoryName.contains('Air')) categoryId = '2';
    if (categoryName.contains('TPS')) categoryId = '3';

    request.fields['category_id'] = categoryId;
    request.fields['lat'] = lat.toString();
    request.fields['lng'] = lng.toString();
    request.fields['address'] = address;
    request.fields['description'] = description;

    // Menambahkan file foto
    var pic = await http.MultipartFile.fromPath('photo', photo.path);
    request.files.add(pic);

    var response = await request.send();
    var responseData = await response.stream.bytesToString();
    var data = json.decode(responseData);

    if (response.statusCode == 201) {
      return {'status': 'success', 'message': data['message']};
    } else {
      String errorMsg = data['message'] ?? 'Gagal mengirim laporan';
      if (data['errors'] != null) {
         var firstErrorKey = data['errors'].keys.first;
         errorMsg = data['errors'][firstErrorKey][0];
      }
      return {'status': 'error', 'message': errorMsg};
    }
  }

  // Fungsi untuk menghapus laporan (hanya jika masih Menunggu Verifikasi)
  static Future<Map<String, dynamic>> deleteReport(int reportId) async {
    final token = await getToken();
    if (token == null) return {'status': 'error', 'message': 'Not authenticated'};

    final response = await http.delete(
      Uri.parse('$baseUrl/reports/$reportId'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      var data = json.decode(response.body);
      return {'status': 'error', 'message': data['message'] ?? 'Gagal menghapus laporan'};
    }
  }

  // Fungsi untuk mengambil Riwayat Laporan milik warga yang sedang Login
  static Future<List<dynamic>> fetchReports() async {
    try {
      final token = await getToken();
      if (token == null) return [];

      final response = await http.get(
        Uri.parse('$baseUrl/reports'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'success') {
          return data['data']['data']; // Laravel pagination mengembalikan objek 'data' di dalam 'data'
        }
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  // Fungsi untuk mengambil detail satu laporan spesifik (digunakan untuk polling realtime)
  static Future<Map<String, dynamic>?> fetchReportDetail(int id) async {
    try {
      final token = await getToken();
      if (token == null) return null;

      final response = await http.get(
        Uri.parse('$baseUrl/reports/$id'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'success') {
          return data['data']; 
        }
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  // Fungsi untuk mengambil Data Profil
  static Future<Map<String, dynamic>?> getProfile() async {
    try {
      final token = await getToken();
      if (token == null) return null;

      final response = await http.get(
        Uri.parse('$baseUrl/profile'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        return data['data'];
      }
    } catch (e) { }
    return null;
  }

  // Fungsi untuk Logout
  static Future<bool> logout() async {
    try {
      final token = await getToken();
      if (token != null) {
        // Tembak API Logout di Laravel untuk menghancurkan sesi Sanctum
        await http.post(
          Uri.parse('$baseUrl/logout'),
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          },
        );
      }
    } catch (e) { 
      // Jika error internet, tetap paksa hapus token di HP
    }
    
    // Hapus token di HP
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    return true;
  }

  // --- DRIVER API ---

  // Ambil daftar tugas khusus untuk driver
  static Future<List<dynamic>> fetchDriverTasks() async {
    try {
      final token = await getToken();
      if (token == null) return [];

      final response = await http.get(
        Uri.parse('$baseUrl/driver/tasks'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'success') {
          return data['data'];
        }
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  // Ambil histori tugas yang sudah diselesaikan
  static Future<List<dynamic>> fetchDriverHistory() async {
    try {
      final token = await getToken();
      if (token == null) return [];

      final response = await http.get(
        Uri.parse('$baseUrl/driver/tasks/history'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'success') {
          return data['data'];
        }
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  // Selesaikan tugas (upload bukti foto)
  static Future<bool> completeDriverTask({
    required int reportId,
    required File proofPhoto,
    String? notes,
  }) async {
    try {
      final token = await getToken();
      if (token == null) return false;

      var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/driver/tasks/$reportId/complete'));
      
      request.headers['Authorization'] = 'Bearer $token';
      request.headers['Accept'] = 'application/json';

      if (notes != null && notes.isNotEmpty) {
        request.fields['notes'] = notes;
      }

      var multipartFile = await http.MultipartFile.fromPath(
        'proof_photo',
        proofPhoto.path,
      );
      request.files.add(multipartFile);

      var response = await request.send();
      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }

  // Mengambil daftar notifikasi
  static Future<List<dynamic>> fetchNotifications() async {
    try {
      final token = await getToken();
      if (token == null) return [];

      final response = await http.get(
        Uri.parse('$baseUrl/notifications'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        var data = json.decode(response.body);
        if (data['status'] == 'success') {
          return data['data'];
        }
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  // Tandai notifikasi sebagai sudah dibaca
  static Future<void> markNotificationAsRead(String id) async {
    try {
      final token = await getToken();
      if (token == null) return;

      await http.post(
        Uri.parse('$baseUrl/notifications/$id/read'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );
    } catch (e) {
      // Abaikan error
    }
  }
}
