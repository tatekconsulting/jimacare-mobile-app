import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';

/// Mock Authentication Service for testing without backend
class AuthServiceMock {
  /// Mock login - accepts any email/password for testing
  Future<Map<String, dynamic>> login(String email, String password) async {
    // Simulate network delay
    await Future.delayed(const Duration(seconds: 1));

    // Mock successful login
    final mockToken = 'mock_token_${DateTime.now().millisecondsSinceEpoch}';
    await _saveToken(mockToken);

    final mockUser = {
      'id': 1,
      'name': email.split('@').first,
      'email': email,
      'phone': '+1234567890',
      'role': 'user',
      'created_at': DateTime.now().toIso8601String(),
      'updated_at': DateTime.now().toIso8601String(),
    };

    return {
      'success': true,
      'data': {
        'token': mockToken,
        'user': mockUser,
      },
      'message': 'Login successful (Mock Mode)',
    };
  }

  /// Mock register
  Future<Map<String, dynamic>> register(Map<String, dynamic> userData) async {
    await Future.delayed(const Duration(seconds: 1));

    final mockToken = 'mock_token_${DateTime.now().millisecondsSinceEpoch}';
    await _saveToken(mockToken);

    return {
      'success': true,
      'data': {
        'token': mockToken,
        'user': {
          'id': 2,
          'name': userData['name'],
          'email': userData['email'],
          'phone': userData['phone'],
          'role': 'user',
          'created_at': DateTime.now().toIso8601String(),
          'updated_at': DateTime.now().toIso8601String(),
        },
      },
      'message': 'Registration successful (Mock Mode)',
    };
  }

  /// Mock get current user
  Future<Map<String, dynamic>> getCurrentUser() async {
    await Future.delayed(const Duration(milliseconds: 500));

    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');

    if (token == null) {
      return {
        'success': false,
        'message': 'Not authenticated',
      };
    }

    return {
      'success': true,
      'data': {
        'id': 1,
        'name': 'Test User',
        'email': 'test@example.com',
        'phone': '+1234567890',
        'role': 'user',
        'created_at': DateTime.now().toIso8601String(),
        'updated_at': DateTime.now().toIso8601String(),
      },
    };
  }

  /// Mock logout
  Future<void> logout() async {
    await Future.delayed(const Duration(milliseconds: 300));
    await _clearToken();
  }

  /// Check if logged in
  Future<bool> isLoggedIn() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    return token != null && token.isNotEmpty;
  }

  /// Get stored token
  Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  /// Save token
  Future<void> _saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  /// Clear token
  Future<void> _clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }
}

