import 'package:flutter/material.dart';
import 'package:lottie/lottie.dart';

class SuccessReportScreen extends StatefulWidget {
  const SuccessReportScreen({super.key});

  @override
  State<SuccessReportScreen> createState() => _SuccessReportScreenState();
}

class _SuccessReportScreenState extends State<SuccessReportScreen> {
  @override
  void initState() {
    super.initState();
    // Pindah otomatis setelah animasi selesai (sekitar 2.5 detik)
    Future.delayed(const Duration(milliseconds: 2500), () {
      if (mounted) {
        // Pop mengembalikan sinyal "true" ke DashboardScreen agar me-refresh daftar
        Navigator.pop(context, true);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Lottie.network(
              'https://assets10.lottiefiles.com/packages/lf20_pqnfmone.json',
              width: 250,
              height: 250,
              repeat: false,
              fit: BoxFit.contain,
            ),
            const SizedBox(height: 24),
            const Text(
              'Laporan Terkirim!',
              style: TextStyle(
                fontSize: 26,
                fontWeight: FontWeight.w900,
                color: Color(0xFF1E293B),
                letterSpacing: -0.5,
              ),
            ),
            const SizedBox(height: 12),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 48),
              child: Text(
                'Terima kasih! Laporan Anda berhasil dikirim dan akan segera ditindaklanjuti oleh petugas terkait.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 15,
                  color: Color(0xFF64748B),
                  height: 1.5,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
