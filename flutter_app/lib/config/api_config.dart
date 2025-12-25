/// API Configuration for JimaCare Flutter App
/// 
/// IMPORTANT: Update the baseUrl with your actual Laravel backend URL
class ApiConfig {
  // ============================================
  // CONFIGURE YOUR API URL HERE
  // ============================================
  
  // Option 1: Production URL (when backend is deployed)
  // static const String baseUrl = 'https://jimacare.com/api/v1';
  
  // Option 2: Local Laravel Backend (Android Emulator)
  // Use 10.0.2.2 to access localhost from Android emulator
  // static const String baseUrl = 'http://10.0.2.2:8000/api/v1';
  
  // Option 3: Local Laravel Backend (iOS Simulator)
  // static const String baseUrl = 'http://localhost:8000/api/v1';
  
  // Option 4: Local Laravel Backend (Physical Device - use your computer's IP)
  // Find your IP: ipconfig (Windows) or ifconfig (Mac/Linux)
  // Example: static const String baseUrl = 'http://192.168.1.100:8000/api/v1';
  
  // Option 5: Test/Development Server
  // static const String baseUrl = 'https://your-test-server.com/api/v1';
  
  // ============================================
  // API CONFIGURATION
  // ============================================
  
  // Set to true to use mock/test mode (no backend needed)
  // Set to false to use real backend at jimacare.com
  static const bool useMockMode = true; // Enabled for testing (avoids CORS issues on web)
  
  // CURRENT SETTING - Production URL (jimacare.com)
  static const String baseUrl = 'https://jimacare.com/api/v1';
  
  // For local testing, uncomment one of these and set useMockMode = false:
  // Option A: Android Emulator
  // static const String baseUrl = 'http://10.0.2.2:8000/api/v1';
  
  // Option B: iOS Simulator
  // static const String baseUrl = 'http://localhost:8000/api/v1';
  
  // Option C: Physical Device (replace XXX with your computer's IP)
  // Find IP: ipconfig (Windows) or ifconfig (Mac/Linux)
  // static const String baseUrl = 'http://192.168.1.XXX:8000/api/v1';
  
  // Timeouts
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  
  // API Endpoints
  static const String login = '/mobile/login';
  static const String register = '/mobile/register';
  static const String logout = '/mobile/logout';
  static const String user = '/mobile/user';
  static const String forgotPassword = '/mobile/forgot-password';
  static const String verifyPhone = '/mobile/verify-phone';
  static const String resendOtp = '/mobile/resend-otp';
  
  // Jobs
  static const String jobs = '/search/jobs';
  static const String carers = '/search/carers';
  
  // Availability
  static const String availabilityToggle = '/availability/toggle';
  static const String availabilityStatus = '/availability/status';
  static const String availabilityNearby = '/availability/nearby';
  
  // Bookings
  static String bookingCreate(String userId) => '/booking/$userId';
  static String bookingStatus(String bookingId) => '/booking/$bookingId/status';
  static String bookingAccept(String bookingId) => '/booking/$bookingId/accept';
  static String bookingDecline(String bookingId) => '/booking/$bookingId/decline';
  
  // Location
  static const String locationUpdate = '/location/update';
  static String locationTrack(String bookingId) => '/location/track/$bookingId';
  static String locationStart(String bookingId) => '/location/start/$bookingId';
  
  // Video Calls
  static String videoCallInitiate(String userId) => '/video/call/$userId';
  static String videoCallJoin(String room) => '/video/join/$room';
  static String videoCallEnd(String room) => '/video/end/$room';
  static String videoCallDecline(String room) => '/video/decline/$room';
  
  // Analytics
  static const String analyticsDashboard = '/analytics/dashboard';
  static const String analyticsEarnings = '/analytics/earnings';
  static const String analyticsPerformance = '/analytics/performance';
  
  // Push Notifications
  static const String pushSubscribe = '/push/subscribe';
  static const String pushUnsubscribe = '/push/unsubscribe';
  static const String pushStatus = '/push/status';
  static const String pushTest = '/push/test';
  
  // Chatbot (Public)
  static const String chatbotInit = '/chatbot/init';
  static const String chatbotMessage = '/chatbot/message';
  
  // Search Filters
  static const String searchFilters = '/search/filters';
}

