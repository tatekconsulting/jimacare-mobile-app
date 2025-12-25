# 🚀 JimaCare Flutter App - Quick Start Guide

## ✅ What's Been Set Up

Your Flutter project has been configured with:

1. **Project Structure** - Organized folders for models, services, screens, widgets
2. **API Client** - HTTP client ready to connect to your Laravel backend
3. **Authentication Service** - Login, register, and token management
4. **Configuration** - API endpoints and settings
5. **Dependencies** - All necessary packages installed

## 🔧 Step 1: Configure Your API URL

**IMPORTANT:** Update the API base URL in `lib/config/api_config.dart`:

```dart
// Change this line:
static const String baseUrl = 'https://your-domain.com/api/v1';

// To your actual Laravel backend URL, for example:
static const String baseUrl = 'https://jimacare.com/api/v1';

// For local testing (Android emulator):
// static const String baseUrl = 'http://10.0.2.2:8000/api/v1';

// For local testing (iOS simulator):
// static const String baseUrl = 'http://localhost:8000/api/v1';
```

## 📱 Step 2: Test the App

1. **Run the app:**
   ```powershell
   flutter run -d chrome
   ```
   Or for Android:
   ```powershell
   flutter run -d android
   ```

2. **You'll see a login screen** - This is a basic implementation to test API connection

## 🔌 Step 3: Connect to Your Laravel Backend

### Backend Requirements:

1. **Ensure Laravel Sanctum is installed:**
   ```bash
   composer require laravel/sanctum
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```

2. **Update User Model** (`app/Models/User.php`):
   ```php
   use Laravel\Sanctum\HasApiTokens;
   
   class User extends Authenticatable
   {
       use HasApiTokens, HasFactory, Notifiable;
       // ...
   }
   ```

3. **Create Mobile Auth Endpoints** (if not already created):
   
   Add to `routes/api.php`:
   ```php
   // Mobile Authentication Routes
   Route::prefix('v1/mobile')->group(function () {
       Route::post('/login', [\App\Http\Controllers\Api\MobileAuthController::class, 'login']);
       Route::post('/register', [\App\Http\Controllers\Api\MobileAuthController::class, 'register']);
   });
   
   Route::prefix('v1/mobile')->middleware('auth:sanctum')->group(function () {
       Route::get('/user', [\App\Http\Controllers\Api\MobileAuthController::class, 'user']);
       Route::post('/logout', [\App\Http\Controllers\Api\MobileAuthController::class, 'logout']);
   });
   ```

4. **Configure CORS** (`config/cors.php`):
   ```php
   'allowed_origins' => ['*'], // Or specify your mobile app domain
   'allowed_headers' => ['*'],
   'allowed_methods' => ['*'],
   ```

## 🏗️ Step 4: Build Your App Features

### Current Structure:
```
lib/
├── config/
│   └── api_config.dart      ✅ API configuration
├── services/
│   ├── api_client.dart      ✅ HTTP client
│   └── auth_service.dart   ✅ Authentication
├── screens/
│   └── auth/
│       └── login_screen.dart ✅ Login screen (basic)
└── utils/
    └── constants.dart       ✅ App constants
```

### Next Steps to Build:

1. **Create More Screens:**
   - Register screen
   - Home/Dashboard screen
   - Job listings screen
   - Profile screen
   - etc.

2. **Create Models:**
   - User model
   - Job model
   - Booking model
   - etc.

3. **Create More Services:**
   - Job service
   - Booking service
   - Profile service
   - etc.

4. **Add Navigation:**
   - Set up routing (using go_router)
   - Create navigation structure

## 📋 Development Checklist

### Phase 1: Foundation ✅
- [x] Flutter project setup
- [x] API client configuration
- [x] Authentication service
- [x] Basic login screen
- [ ] Register screen
- [ ] Navigation setup
- [ ] Token storage

### Phase 2: Core Features
- [ ] Home/Dashboard screen
- [ ] Job listings screen
- [ ] Job details screen
- [ ] Profile screen
- [ ] API integration for jobs

### Phase 3: Advanced Features
- [ ] Push notifications
- [ ] Location services
- [ ] Video calls
- [ ] Messaging
- [ ] Document upload

## 🧪 Testing Your Setup

1. **Test API Connection:**
   - Update `api_config.dart` with your backend URL
   - Run the app
   - Try logging in with test credentials
   - Check if API calls are working

2. **Check Network Requests:**
   - Use Flutter DevTools to monitor network requests
   - Verify tokens are being stored
   - Check API responses

## 🐛 Troubleshooting

### "Connection refused" or "Failed to connect"
- **Check backend is running:** Make sure your Laravel server is running
- **Check URL:** Verify the API URL in `api_config.dart` is correct
- **Android Emulator:** Use `10.0.2.2` instead of `localhost`
- **CORS:** Ensure CORS is configured in Laravel

### "401 Unauthorized"
- **Check Sanctum:** Ensure Laravel Sanctum is installed and configured
- **Check token:** Verify token is being stored after login
- **Check headers:** Ensure Authorization header is being sent

### Build Errors
```powershell
flutter clean
flutter pub get
flutter run
```

## 📚 Key Files to Understand

1. **`lib/config/api_config.dart`** - API endpoints and configuration
2. **`lib/services/api_client.dart`** - HTTP client with token management
3. **`lib/services/auth_service.dart`** - Authentication methods
4. **`lib/screens/auth/login_screen.dart`** - Example screen implementation

## 🎯 Next Actions

1. ✅ **Update API URL** in `api_config.dart`
2. ✅ **Test login** with your backend
3. ✅ **Create register screen** (similar to login)
4. ✅ **Set up navigation** between screens
5. ✅ **Build home screen** with job listings
6. ✅ **Add more features** one by one

## 📖 Resources

- **Flutter Docs:** https://docs.flutter.dev/
- **Dio Package:** https://pub.dev/packages/dio
- **Provider:** https://pub.dev/packages/provider
- **Laravel Sanctum:** https://laravel.com/docs/sanctum

---

**You're ready to start building!** Update the API URL and test the connection. 🚀

