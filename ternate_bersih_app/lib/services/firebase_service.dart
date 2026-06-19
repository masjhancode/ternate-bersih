import 'dart:io' show Platform;
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'package:flutter/material.dart';

// Fungsi handler ketika notifikasi masuk di background
@pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
}

class FirebaseService {
  static Future<void> initialize(GlobalKey<NavigatorState> navigatorKey) async {
    // Firebase hanya diinisialisasi di Android (yang sudah ada google-services.json)
    if (kIsWeb || !Platform.isAndroid) {
      print('--- Firebase DILEWATI (Platform bukan Android) ---');
      return;
    }

    try {
      // Inisialisasi Firebase Core
      await Firebase.initializeApp();

      final firebaseMessaging = FirebaseMessaging.instance;
      final localNotifications = FlutterLocalNotificationsPlugin();

      // Meminta Izin Notifikasi dari OS (terutama Android 13+)
      await firebaseMessaging.requestPermission(
        alert: true,
        badge: true,
        sound: true,
      );

      // Setup Local Notifications untuk foreground
      const AndroidInitializationSettings initSettingsAndroid =
          AndroidInitializationSettings('@mipmap/ic_launcher');
      const InitializationSettings initSettings =
          InitializationSettings(android: initSettingsAndroid);
          
      // Tambahkan handler klik notifikasi lokal
      await localNotifications.initialize(
        settings: initSettings,
        onDidReceiveNotificationResponse: (NotificationResponse response) {
          if (response.payload != null && navigatorKey.currentContext != null) {
            _showNotificationDialog(navigatorKey.currentContext!, response.payload!);
          }
        },
      );

      // Mendaftarkan Background Handler
      FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);

      // Menangani klik notifikasi saat aplikasi berjalan di background
      FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
        if (message.notification?.body != null && navigatorKey.currentContext != null) {
          _showNotificationDialog(navigatorKey.currentContext!, message.notification!.body!);
        }
      });

      // Menangani Notifikasi Saat Aplikasi Terbuka (Foreground)
      FirebaseMessaging.onMessage.listen((RemoteMessage message) {
        RemoteNotification? notification = message.notification;
        AndroidNotification? android = message.notification?.android;

        if (notification != null && android != null) {
          localNotifications.show(
            id: notification.hashCode,
            title: notification.title,
            body: notification.body,
            notificationDetails: const NotificationDetails(
              android: AndroidNotificationDetails(
                'ternate_bersih_channel',
                'Ternate Bersih Notifikasi',
                channelDescription: 'Pemberitahuan perubahan status laporan',
                importance: Importance.max,
                priority: Priority.high,
                icon: '@mipmap/ic_launcher',
              ),
            ),
            payload: notification.body, // Simpan pesan untuk dialog klik
          );
        }
      });

      // Ambil Token FCM (Unik per HP)
      String? token = await firebaseMessaging.getToken();
      if (token != null) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('fcm_token', token);
        print("--- FCM TOKEN BERHASIL DIDAPATKAN ---");
        print(token);
      }

      // Refresh Token jika berubah
      firebaseMessaging.onTokenRefresh.listen((newToken) async {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('fcm_token', newToken);
      });
    } catch (e) {
      print('--- Firebase gagal diinisialisasi: $e ---');
    }
  }

  static void _showNotificationDialog(BuildContext context, String message) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Row(
            children: [
              Icon(Icons.notifications_active_rounded, color: Color(0xFF0D9488)),
              SizedBox(width: 8),
              Text('Informasi Laporan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            ],
          ),
          content: Text(
            message,
            style: const TextStyle(fontSize: 14, color: Color(0xFF334155), height: 1.4),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('TUTUP', style: TextStyle(color: Color(0xFF0D9488), fontWeight: FontWeight.bold)),
            ),
          ],
        );
      },
    );
  }
}
