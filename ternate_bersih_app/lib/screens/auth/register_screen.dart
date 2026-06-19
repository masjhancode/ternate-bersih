import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _nikController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  
  bool _isPasswordVisible = false;
  bool _isLoading = false;

  void _handleRegister() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);
      
      final response = await ApiService.register(
        name: _nameController.text.trim(),
        email: _emailController.text.trim(),
        nik: _nikController.text.trim(),
        phone: _phoneController.text.trim(),
        password: _passwordController.text,
      );
      
      if (mounted) {
        setState(() => _isLoading = false);
        
        if (response['success'] == true) {
          // Tampilkan pesan menggunakan ScaffoldMessenger root agar tidak tertutup
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Registrasi berhasil! Silakan login dengan akun Anda.'),
              backgroundColor: Color(0xFF059669),
              behavior: SnackBarBehavior.floating,
              duration: Duration(seconds: 3),
            )
          );
          
          // Memaksa navigasi kembali ke halaman Login dan membersihkan input
          if (Navigator.canPop(context)) {
            Navigator.pop(context);
          } else {
            Navigator.pushReplacementNamed(context, '/login');
          }
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Gagal: ${response['message']}'),
              backgroundColor: Colors.red,
            )
          );
        }
      }
    }
  }

  // Widget helper untuk merapikan text field
  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    TextInputType keyboardType = TextInputType.text,
    bool isPassword = false,
    String? Function(String?)? validator,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20.0),
      child: TextFormField(
        controller: controller,
        keyboardType: keyboardType,
        obscureText: isPassword && !_isPasswordVisible,
        style: const TextStyle(fontSize: 15, color: Color(0xFF1E293B)),
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(icon, color: const Color(0xFF94A3B8)),
          suffixIcon: isPassword
              ? IconButton(
                  icon: Icon(
                    _isPasswordVisible ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                    color: const Color(0xFF94A3B8),
                  ),
                  onPressed: () => setState(() => _isPasswordVisible = !_isPasswordVisible),
                )
              : null,
          labelStyle: const TextStyle(color: Color(0xFF64748B)),
          floatingLabelStyle: const TextStyle(color: Color(0xFF0D9488), fontWeight: FontWeight.bold),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: const BorderSide(color: Color(0xFF0D9488), width: 2)),
          filled: true,
          fillColor: const Color(0xFFF8FAFC),
        ),
        validator: validator ?? (value) => value!.isEmpty ? '$label wajib diisi' : null,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text('Buat Akun Baru', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24.0),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const Text(
                  'Lengkapi Profil Anda',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF1E293B),
                    letterSpacing: -0.5,
                  ),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Data Anda dilindungi dan hanya digunakan untuk keperluan verifikasi laporan sampah.',
                  style: TextStyle(
                    fontSize: 14,
                    color: Color(0xFF64748B),
                    height: 1.5,
                  ),
                ),
                const SizedBox(height: 32),

                _buildTextField(
                  controller: _nameController,
                  label: 'Nama Lengkap (Sesuai KTP)',
                  icon: Icons.person_outline,
                ),
                _buildTextField(
                  controller: _nikController,
                  label: 'Nomor Induk Kependudukan (NIK)',
                  icon: Icons.badge_outlined,
                  keyboardType: TextInputType.number,
                  validator: (val) => val!.length != 16 ? 'NIK harus terdiri dari 16 digit angka' : null,
                ),
                _buildTextField(
                  controller: _emailController,
                  label: 'Alamat Email',
                  icon: Icons.email_outlined,
                  keyboardType: TextInputType.emailAddress,
                  validator: (val) => val!.isEmpty || !val.contains('@') ? 'Masukkan email yang valid' : null,
                ),
                _buildTextField(
                  controller: _phoneController,
                  label: 'Nomor WhatsApp / HP',
                  icon: Icons.phone_android_outlined,
                  keyboardType: TextInputType.phone,
                ),
                _buildTextField(
                  controller: _passwordController,
                  label: 'Kata Sandi',
                  icon: Icons.lock_outline,
                  isPassword: true,
                  validator: (val) => val!.length < 8 ? 'Kata sandi minimal 8 karakter' : null,
                ),

                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _isLoading ? null : _handleRegister,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF0D9488),
                    foregroundColor: Colors.white,
                    disabledBackgroundColor: const Color(0xFF94A3B8),
                    padding: const EdgeInsets.symmetric(vertical: 18),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    elevation: 0,
                  ),
                  child: _isLoading 
                      ? const SizedBox(
                          height: 20, 
                          width: 20, 
                          child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5)
                        )
                      : const Text('Daftar Sekarang', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ),
      ),
    );
  }

  @override
  void dispose() {
    _nameController.dispose();
    _nikController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    super.dispose();
  }
}
