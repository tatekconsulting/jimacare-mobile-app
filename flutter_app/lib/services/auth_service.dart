import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';
import 'api_client.dart';
import 'auth_service_mock.dart';

/// Authentication Service
/// Uses mock mode if ApiConfig.useMockMode is true
class AuthService {
  final ApiClient _apiClient = ApiClient();
  final AuthServiceMock _mockService = AuthServiceMock();
  
  bool get _useMock => ApiConfig.useMockMode;
  
  /// Login user
  Future<Map<String, dynamic>> login(String email, String password) async {
    // Use mock service if mock mode is enabled
    if (_useMock) {
      return await _mockService.login(email, password);
    }
    
    try {
      print('Attempting login to: ${ApiConfig.baseUrl}${ApiConfig.login}');
      print('Email: $email');
      
      final response = await _apiClient.post(
        ApiConfig.login,
        data: {
          'email': email,
          'password': password,
        },
      );
      
      print('Response status: ${response.statusCode}');
      print('Response data: ${response.data}');
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;
        
        // Handle different response formats
        String? token;
        if (data is Map) {
          token = data['token'] ?? data['access_token'] ?? data['auth_token'];
        }
        
        // Store token if provided
        if (token != null) {
          await _saveToken(token);
          print('Token saved successfully');
        } else {
          print('Warning: No token found in response');
        }
        
        return {
          'success': true,
          'data': data,
          'message': data is Map && data.containsKey('message') 
              ? data['message'] 
              : 'Login successful',
        };
      }
      
      // Handle error responses
      String errorMessage = 'Login failed';
      if (response.data is Map) {
        final errorData = response.data as Map;
        errorMessage = errorData['message'] ?? 
                      errorData['error'] ?? 
                      errorData['errors']?.toString() ?? 
                      'Login failed. Status: ${response.statusCode}';
      }
      
      return {
        'success': false,
        'message': errorMessage,
        'statusCode': response.statusCode,
      };
    } catch (e) {
      print('Login exception: $e');
      print('Exception type: ${e.runtimeType}');
      
      String errorMessage = 'Connection error';
      String detailedError = e.toString();
      
      // Check for CORS errors (common on web)
      if (e.toString().contains('CORS') || 
          e.toString().contains('Access-Control-Allow-Origin') ||
          e.toString().contains('XMLHttpRequest')) {
        errorMessage = 'CORS Error: Cannot connect to ${ApiConfig.baseUrl}\n\n'
            'This is a browser security issue. Solutions:\n\n'
            '1. Enable CORS on your Laravel backend:\n'
            '   - Check config/cors.php\n'
            '   - Allow requests from your domain\n\n'
            '2. For testing, use mock mode:\n'
            '   - Set useMockMode = true in api_config.dart\n\n'
            '3. Test on Android/iOS instead of web\n\n'
            '4. Check browser console (F12) for details';
        detailedError = 'CORS Error: ${e.toString()}';
      } else if (e.toString().contains('SocketException') || 
          e.toString().contains('Failed host lookup') ||
          e.toString().contains('Connection refused') ||
          e.toString().contains('NetworkError')) {
        errorMessage = 'Cannot connect to server.\n\n'
            'Trying to reach: ${ApiConfig.baseUrl}${ApiConfig.login}\n\n'
            'Please ensure:\n'
            '1. Your Laravel backend is running\n'
            '2. The API URL in lib/config/api_config.dart is correct\n'
            '3. For web testing, check CORS settings\n'
            '4. For Android Emulator, use: http://10.0.2.2:8000/api/v1';
      } else if (e.toString().contains('TimeoutException') ||
          e.toString().contains('timeout')) {
        errorMessage = 'Request timed out.\n\n'
            'The server at ${ApiConfig.baseUrl} is not responding.\n\n'
            'Please try again or check your connection.';
      } else if (e.toString().contains('401') || e.toString().contains('Unauthorized')) {
        errorMessage = 'Invalid email or password.\n\n'
            'Please check your credentials and try again.';
      } else if (e.toString().contains('404') || e.toString().contains('Not found')) {
        errorMessage = 'API endpoint not found (404).\n\n'
            'The endpoint ${ApiConfig.baseUrl}${ApiConfig.login} does not exist.\n\n'
            'Solutions:\n'
            '1. Check if the route exists in your Laravel backend\n'
            '2. Verify the API URL in lib/config/api_config.dart\n'
            '3. Ensure your backend is running';
      } else if (e.toString().contains('500') || e.toString().contains('Internal Server Error')) {
        errorMessage = 'Server error (500).\n\n'
            'There was an error on the server.\n'
            'Please try again later or contact support.';
      } else {
        errorMessage = 'Login failed.\n\n'
            'Error: ${e.toString()}\n\n'
            'Check browser console (F12) for more details.';
      }
      
      return {
        'success': false,
        'message': errorMessage,
        'error': detailedError,
        'errorType': e.runtimeType.toString(),
      };
    }
  }
  
  /// Register user
  Future<Map<String, dynamic>> register(Map<String, dynamic> userData) async {
    // Use mock service if mock mode is enabled
    if (_useMock) {
      return await _mockService.register(userData);
    }
    
    try {
      final response = await _apiClient.post(
        ApiConfig.register,
        data: userData,
      );
      
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;
        
        // Store token if provided
        if (data['token'] != null) {
          await _saveToken(data['token']);
        }
        
        return {
          'success': true,
          'data': data,
          'message': data['message'] ?? 'Registration successful',
        };
      }
      
      return {
        'success': false,
        'message': 'Registration failed',
      };
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  /// Get current user
  Future<Map<String, dynamic>> getCurrentUser() async {
    // Use mock service if mock mode is enabled
    if (_useMock) {
      return await _mockService.getCurrentUser();
    }
    
    try {
      final response = await _apiClient.get(ApiConfig.user);
      
      if (response.statusCode == 200) {
        return {
          'success': true,
          'data': response.data,
        };
      }
      
      return {
        'success': false,
        'message': 'Failed to get user data',
      };
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  /// Logout user
  Future<void> logout() async {
    // Use mock service if mock mode is enabled
    if (_useMock) {
      await _mockService.logout();
      return;
    }
    
    try {
      await _apiClient.post(ApiConfig.logout);
    } catch (e) {
      // Ignore errors on logout
    } finally {
      await _clearToken();
    }
  }
  
  /// Verify phone with OTP
  Future<Map<String, dynamic>> verifyPhone(String phone, String otp) async {
    try {
      final response = await _apiClient.post(
        ApiConfig.verifyPhone,
        data: {
          'phone': phone,
          'otp': otp,
        },
      );
      
      if (response.statusCode == 200) {
        return {
          'success': true,
          'data': response.data,
          'message': response.data['message'] ?? 'Phone verified',
        };
      }
      
      return {
        'success': false,
        'message': 'Verification failed',
      };
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  /// Resend OTP
  Future<Map<String, dynamic>> resendOtp(String phone) async {
    try {
      final response = await _apiClient.post(
        ApiConfig.resendOtp,
        data: {'phone': phone},
      );
      
      if (response.statusCode == 200) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'OTP sent',
        };
      }
      
      return {
        'success': false,
        'message': 'Failed to send OTP',
      };
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  /// Forgot password
  Future<Map<String, dynamic>> forgotPassword(String email) async {
    try {
      final response = await _apiClient.post(
        ApiConfig.forgotPassword,
        data: {'email': email},
      );
      
      if (response.statusCode == 200) {
        return {
          'success': true,
          'message': response.data['message'] ?? 'Password reset link sent',
        };
      }
      
      return {
        'success': false,
        'message': 'Failed to send reset link',
      };
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  /// Check if user is logged in
  Future<bool> isLoggedIn() async {
    if (_useMock) {
      return await _mockService.isLoggedIn();
    }
    
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    return token != null && token.isNotEmpty;
  }
  
  /// Get stored token
  Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }
  
  /// Save authentication token
  Future<void> _saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }
  
  /// Clear authentication token
  Future<void> _clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }
}

