import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  final _storage = const FlutterSecureStorage();

  Future<void> saveTokens({required String onlineToken, required String offlineToken}) async {
    await _storage.write(key: 'online_token', value: onlineToken);
    await _storage.write(key: 'offline_token', value: offlineToken);
  }

  Future<String?> getOfflineToken() async {
    return await _storage.read(key: 'offline_token');
  }

  Future<void> saveLocalPIN(String hashedPin) async {
    await _storage.write(key: 'local_pin', value: hashedPin);
  }

  Future<String?> getLocalPIN() async {
    return await _storage.read(key: 'local_pin');
  }
}