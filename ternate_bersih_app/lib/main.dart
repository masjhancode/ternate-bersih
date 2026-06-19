import 'package:flutter/material.dart';
import 'package:flutter/cupertino.dart';
import 'dart:io' show Platform;
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:provider/provider.dart';

import 'providers/auth_provider.dart';
import 'screens/auth/login_screen.dart';
import 'screens/auth/register_screen.dart';
import 'screens/home/dashboard_screen.dart';
import 'screens/reports/create_report_screen.dart';
import 'screens/reports/report_detail_screen.dart';
import 'screens/splash_screen.dart';
import 'screens/driver/driver_dashboard_screen.dart';
import 'screens/driver/driver_task_detail_screen.dart';
import 'screens/reports/success_report_screen.dart';

import 'services/firebase_service.dart';

final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await FirebaseService.initialize(navigatorKey);

  runApp(
    MultiProvider(
      providers: [ChangeNotifierProvider(create: (_) => AuthProvider())],
      child: const TernateBersihApp(),
    ),
  );
}

class TernateBersihApp extends StatelessWidget {
  const TernateBersihApp({super.key});

  @override
  Widget build(BuildContext context) {
    bool isIOS = !kIsWeb && Platform.isIOS;

    return MaterialApp(
      navigatorKey: navigatorKey,
      title: 'Ternate Bersih',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF0D9488),
        ), // Teal/Emerald 600
        useMaterial3: true,
        // Adaptasi Native iOS (Cupertino)
        platform: isIOS ? TargetPlatform.iOS : TargetPlatform.android,
        cupertinoOverrideTheme: const CupertinoThemeData(
          primaryColor: Color(0xFF0D9488),
          scaffoldBackgroundColor: CupertinoColors.systemGroupedBackground,
          barBackgroundColor: CupertinoColors.white,
        ),
        appBarTheme: const AppBarTheme(
          centerTitle: true,
          elevation: 0,
          backgroundColor: Colors.white,
          foregroundColor: Colors.black87,
        ),
        pageTransitionsTheme: const PageTransitionsTheme(
          builders: {
            TargetPlatform.android: ZoomPageTransitionsBuilder(),
            TargetPlatform.iOS: CupertinoPageTransitionsBuilder(),
          },
        ),
      ),
      initialRoute: '/',
      routes: {
        '/': (context) => const SplashScreen(),
        '/login': (context) => const LoginScreen(),
        '/register': (context) => const RegisterScreen(),
        '/dashboard': (context) => const DashboardScreen(),
        '/create_report': (context) => const CreateReportScreen(),
        '/success_report': (context) => const SuccessReportScreen(),
        '/report_detail': (context) => const ReportDetailScreen(),
        '/driver_dashboard': (context) => const DriverDashboardScreen(),
        '/driver_task_detail': (context) => const DriverTaskDetailScreen(),
      },
    );
  }
}
