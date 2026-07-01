import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:crypto/crypto.dart';
import 'package:frontend/main.dart';
import '../services/secure_storage_service.dart';

class PinScreen extends StatefulWidget {
  final bool isSetup; 
  const PinScreen({super.key, required this.isSetup});

  @override
  State<PinScreen> createState() => _PinScreenState();
}

class _PinScreenState extends State<PinScreen> {
  final _pinController = TextEditingController();
  final SecureStorageService _storage = SecureStorageService();
  String _errorMessage = '';

  String _hashPIN(String pin) {
    var bytes = utf8.encode(pin);
    var digest = sha256.convert(bytes);
    return digest.toString();
  }

  Future<void> _submitPin() async {
    final pin = _pinController.text;
    if (pin.length < 4) {
      setState(() => _errorMessage = 'PIN minimal 4 digit!');
      return;
    }

    final hashedPin = _hashPIN(pin);

    if (widget.isSetup) {
      await _storage.saveLocalPIN(hashedPin);
      if (!mounted) return;
      _goToDashboard();
    } else {
      final savedHashedPin = await _storage.getLocalPIN();
      if (hashedPin == savedHashedPin) {
        if (!mounted) return;
        _goToDashboard();
      } else {
        setState(() {
          _errorMessage = 'PIN Salah!';
          _pinController.clear();
        });
      }
    }
  }

  void _goToDashboard() {
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => const LicenseHomeScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF002B93), 
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(32.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.lock_person, size: 80, color: Colors.white),
              const SizedBox(height: 24),
              Text(
                widget.isSetup ? "Buat PIN Keamanan Baru" : "Masukkan PIN Anda",
                style: const TextStyle(fontSize: 22, color: Colors.white, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              const Text(
                "Digunakan untuk akses masuk harian (Offline)",
                style: TextStyle(color: Colors.white70),
              ),
              const SizedBox(height: 32),
              TextField(
                controller: _pinController,
                obscureText: true,
                keyboardType: TextInputType.number,
                maxLength: 6,
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 24, color: Colors.white, letterSpacing: 16),
                decoration: const InputDecoration(
                  counterText: "",
                  enabledBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.white54)),
                  focusedBorder: UnderlineInputBorder(borderSide: BorderSide(color: Colors.white, width: 2)),
                ),
              ),
              const SizedBox(height: 16),
              if (_errorMessage.isNotEmpty)
                Text(_errorMessage, style: const TextStyle(color: Color(0xFFCC5900), fontWeight: FontWeight.bold)),
              const SizedBox(height: 32),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFCC5900),
                  minimumSize: const Size(200, 50),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(25)),
                ),
                onPressed: _submitPin,
                child: const Text('Lanjutkan', style: TextStyle(color: Colors.white, fontSize: 16)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}