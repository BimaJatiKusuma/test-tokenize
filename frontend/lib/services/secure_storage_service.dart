import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  // Inisialisasi Flutter Secure Storage
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  // Key untuk penyimpanan biner aman
  static const String _keyLocalPin = 'local_pin';
  static const String _keyOnlineToken = 'online_token';
  static const String _keyOfflineToken = 'offline_token';

  // ==========================================
  // MANAJEMEN PIN LOKAL
  // ==========================================
  
  // Menyimpan PIN yang sudah di-hash
  Future<void> saveLocalPIN(String pin) async {
    await _storage.write(key: _keyLocalPin, value: pin);
  }

  // Mengambil PIN yang sudah di-hash
  Future<String?> getLocalPIN() async {
    return await _storage.read(key: _keyLocalPin);
  }

  // ==========================================
  // MANAJEMEN TOKEN (ONLINE & OFFLINE)
  // ==========================================

  // Menyimpan Online Token (Sanctum) dan Offline Token (HMAC) sekaligus
  Future<void> saveTokens({required String onlineToken, required String offlineToken}) async {
    await _storage.write(key: _keyOnlineToken, value: onlineToken);
    await _storage.write(key: _keyOfflineToken, value: offlineToken);
  }

  // Mengambil Online Token untuk otorisasi API ke Laravel Server
  Future<String?> getOnlineToken() async {
    return await _storage.read(key: _keyOnlineToken);
  }

  // Mengambil Offline Token untuk verifikasi offline lokal
  Future<String?> getOfflineToken() async {
    return await _storage.read(key: _keyOfflineToken);
  }

  // ==========================================
  // PENGHAPUSAN DATA (LOGOUT / RESET)
  // ==========================================
  
  // Menghapus semua data yang tersimpan di secure storage
  Future<void> clearAll() async {
    await _storage.deleteAll();
  }
}