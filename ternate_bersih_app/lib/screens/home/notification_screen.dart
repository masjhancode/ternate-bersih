import 'dart:async';
import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
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
        title: const Text('Notifikasi', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: Colors.white,
        elevation: 0,
        centerTitle: true,
      ),
      body: RefreshIndicator(
        color: const Color(0xFF0D9488),
        onRefresh: () async {
          setState(() {});
        },
        child: FutureBuilder<List<dynamic>>(
          future: ApiService.fetchNotifications(),
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator(color: Color(0xFF0D9488)));
            }

            if (snapshot.hasError) {
              return ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: const [
                  SizedBox(height: 100),
                  Center(child: Text('Gagal memuat notifikasi', style: TextStyle(color: Colors.red))),
                ],
              );
            }

            final notifications = snapshot.data ?? [];

            if (notifications.isEmpty) {
              return ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: [
                  SizedBox(height: MediaQuery.of(context).size.height * 0.3),
                  Center(
                    child: Column(
                      children: [
                        Icon(Icons.notifications_off_rounded, size: 64, color: Colors.grey.shade300),
                        const SizedBox(height: 16),
                        const Text('Belum ada notifikasi baru', style: TextStyle(color: Color(0xFF64748B), fontSize: 16)),
                      ],
                    ),
                  ),
                ],
              );
            }

            return ListView.builder(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(20),
              itemCount: notifications.length,
              itemBuilder: (context, index) {
                final notif = notifications[index];
                final data = notif['data'] ?? {};
                final isUnread = notif['read_at'] == null;
                
                String title = data['title'] ?? 'Notifikasi Baru';
                String message = data['message'] ?? 'Anda mendapatkan pembaruan informasi.';
                String time = notif['created_at'] ?? 'Baru saja';
                
                IconData icon = Icons.notifications_active_rounded;
                Color color = const Color(0xFF0D9488); // Default teal
                
                // Menyesuaikan ikon dan warna berdasarkan isi pesan (bisa juga dari type)
                if (message.toLowerCase().contains('selesai')) {
                  icon = Icons.check_circle_rounded;
                  color = const Color(0xFF059669);
                } else if (message.toLowerCase().contains('tolak')) {
                  icon = Icons.cancel_rounded;
                  color = const Color(0xFFDC2626);
                }

                return GestureDetector(
                  onTap: () {
                    if (isUnread) {
                      ApiService.markNotificationAsRead(notif['id']);
                      setState(() {
                        notif['read_at'] = DateTime.now().toIso8601String();
                      });
                    }
                    // Opsional: navigasi ke halaman detail jika ada
                  },
                  child: Padding(
                    padding: const EdgeInsets.only(bottom: 16),
                    child: _buildNotificationCard(
                      title: title,
                      message: message,
                      time: time,
                      icon: icon,
                      color: color,
                      isUnread: isUnread,
                    ),
                  ),
                );
              },
            );
          },
        ),
      ),
    );
  }

  Widget _buildNotificationCard({
    required String title,
    required String message,
    required String time,
    required IconData icon,
    required Color color,
    required bool isUnread,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isUnread ? Colors.white : const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: isUnread ? color.withValues(alpha: 0.3) : const Color(0xFFE2E8F0)),
        boxShadow: isUnread
            ? [
                BoxShadow(
                  color: color.withValues(alpha: 0.05),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                )
              ]
            : null,
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        title,
                        style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: Color(0xFF1E293B)),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    if (isUnread)
                      Container(
                        width: 8,
                        height: 8,
                        margin: const EdgeInsets.only(left: 8),
                        decoration: const BoxDecoration(color: Color(0xFFDC2626), shape: BoxShape.circle),
                      ),
                  ],
                ),
                const SizedBox(height: 6),
                Text(
                  message,
                  style: const TextStyle(fontSize: 13, color: Color(0xFF64748B), height: 1.4),
                ),
                const SizedBox(height: 12),
                Text(
                  time,
                  style: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8), fontWeight: FontWeight.w500),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
