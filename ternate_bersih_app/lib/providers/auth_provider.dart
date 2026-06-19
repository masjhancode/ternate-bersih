import 'package:flutter/material.dart';

class AuthProvider with ChangeNotifier {
  bool _isAuthenticated = false;

  bool get isAuthenticated => _isAuthenticated;

  Future<void> checkAuthStatus() async {
    // TODO: Implementasi pembacaan token dari SharedPreferences
    await Future.delayed(const Duration(seconds: 2));
    _isAuthenticated = false; 
    notifyListeners();
  }
}
