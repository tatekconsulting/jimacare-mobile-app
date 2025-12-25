# JimaCare Flutter App Setup Guide

This guide will help you convert your JimaCare Laravel website into Android and iOS mobile apps using Flutter.

## 📋 Overview

Your Laravel backend already has API endpoints set up. We'll create a Flutter app that connects to these APIs to provide a native mobile experience.

## 🏗️ Project Structure

```
lib/
├── main.dart                 # App entry point
├── config/
│   └── api_config.dart      # API base URL and configuration
├── models/                  # Data models
│   ├── user.dart
│   ├── job.dart
│   ├── booking.dart
│   └── ...
├── services/                # API services
│   ├── api_client.dart     # HTTP client setup
│   ├── auth_service.dart   # Authentication
│   ├── job_service.dart    # Jobs API
│   ├── booking_service.dart
│   └── ...
├── screens/                 # App screens
│   ├── auth/
│   │   ├── login_screen.dart
│   │   ├── register_screen.dart
│   │   └── verify_phone_screen.dart
│   ├── home/
│   │   └── home_screen.dart
│   ├── jobs/
│   │   ├── job_list_screen.dart
│   │   ├── job_detail_screen.dart
│   │   └── job_application_screen.dart
│   ├── profile/
│   │   └── profile_screen.dart
│   └── ...
├── widgets/                # Reusable widgets
│   ├── custom_button.dart
│   ├── custom_text_field.dart
│   └── ...
└── utils/                  # Utilities
    ├── constants.dart
    └── helpers.dart
```

## 🔧 Step 1: Update Dependencies

The `pubspec.yaml` has been updated with all necessary packages. Run:

```powershell
flutter pub get
```

## 🔌 Step 2: Configure API Connection

### 2.1 Create API Configuration

Create `lib/config/api_config.dart` to store your API base URL:

```dart
class ApiConfig {
  // Update this with your Laravel backend URL
  static const String baseUrl = 'https://your-domain.com/api/v1';
  
  // For local development (if using emulator)
  // static const String baseUrl = 'http://10.0.2.2:8000/api/v1'; // Android emulator
  // static const String baseUrl = 'http://localhost:8000/api/v1'; // iOS simulator
  
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
}
```

**Important:** 
- Replace `your-domain.com` with your actual Laravel backend URL
- For Android emulator testing, use `10.0.2.2` instead of `localhost`
- For iOS simulator, use `localhost` or your computer's IP address

## 🔐 Step 3: Set Up Authentication

Your Laravel backend uses Laravel Sanctum for API authentication. The Flutter app will:

1. Login/Register users
2. Store authentication tokens
3. Include tokens in API requests
4. Handle token refresh

## 📱 Step 4: Main Features to Implement

Based on your Laravel API endpoints, implement these features:

### Core Features:
1. **Authentication**
   - Login/Register
   - Phone verification (OTP)
   - Password reset

2. **Jobs/Contracts**
   - Browse jobs
   - Search and filter
   - Job details
   - Apply to jobs
   - Post jobs (for clients)

3. **Profile Management**
   - View/edit profile
   - Upload documents
   - Manage skills/experience

4. **Messaging**
   - Chat with users
   - Real-time messaging

5. **Bookings**
   - Create bookings
   - Accept/decline bookings
   - Track bookings

6. **Notifications**
   - Push notifications
   - In-app notifications

7. **Location Services**
   - Location tracking
   - Nearby carers/jobs

8. **Video Calls**
   - Initiate video calls
   - Join video calls (Twilio)

## 🚀 Step 5: Development Workflow

### Phase 1: Foundation (Week 1-2)
- [x] Set up Flutter project structure
- [ ] Create API client
- [ ] Implement authentication
- [ ] Create basic navigation

### Phase 2: Core Features (Week 3-4)
- [ ] Job listings screen
- [ ] Job details screen
- [ ] Profile screen
- [ ] Basic messaging

### Phase 3: Advanced Features (Week 5-6)
- [ ] Push notifications
- [ ] Location services
- [ ] Video calls
- [ ] Document upload

### Phase 4: Polish & Testing (Week 7-8)
- [ ] UI/UX improvements
- [ ] Bug fixes
- [ ] Performance optimization
- [ ] Testing on real devices

## 📦 Building for Production

### Android (APK/AAB)
```powershell
# Build APK
flutter build apk --release

# Build App Bundle (for Play Store)
flutter build appbundle --release
```

### iOS
```powershell
# Build for iOS (requires Mac and Xcode)
flutter build ios --release
```

## 🔗 Connecting to Your Laravel Backend

### Backend Requirements:

1. **Enable CORS** for mobile app domain
   - Update `config/cors.php` in Laravel
   - Allow your mobile app to make requests

2. **API Authentication**
   - Ensure Laravel Sanctum is configured
   - Mobile apps will use token-based authentication

3. **API Endpoints**
   - Your existing API endpoints in `routes/api.php` are ready
   - May need to add mobile-specific endpoints if needed

## 🧪 Testing

### Test API Connection:
1. Start your Laravel backend
2. Update `api_config.dart` with correct URL
3. Test a simple API call (e.g., login)
4. Check network requests in Flutter DevTools

### Test on Devices:
- **Android**: Use emulator or connect physical device
- **iOS**: Use simulator (Mac required) or physical device

## 📚 Next Steps

1. **Update API Configuration**: Set your backend URL in `lib/config/api_config.dart`
2. **Start with Authentication**: Implement login/register screens
3. **Test API Connection**: Verify you can connect to your Laravel backend
4. **Build Core Features**: Start with job listings and profile
5. **Iterate**: Add features one by one

## 🆘 Troubleshooting

### Can't connect to API?
- Check if Laravel backend is running
- Verify API URL in `api_config.dart`
- Check CORS settings in Laravel
- For Android emulator, use `10.0.2.2` instead of `localhost`

### Authentication not working?
- Verify Laravel Sanctum is installed and configured
- Check token storage in Flutter (SharedPreferences)
- Ensure token is included in API request headers

### Build errors?
- Run `flutter clean`
- Run `flutter pub get`
- Check `flutter doctor -v` for issues

## 📖 Resources

- **Flutter Docs**: https://docs.flutter.dev/
- **Laravel Sanctum**: https://laravel.com/docs/sanctum
- **Dio Package**: https://pub.dev/packages/dio
- **Provider State Management**: https://pub.dev/packages/provider

---

**Ready to start?** The project structure and dependencies are set up. Next, configure your API URL and start building screens!

