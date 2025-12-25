# 🌐 Connecting Apps to jimacare.com

## ✅ YES! Both Apps Will Work with jimacare.com

Both apps have been configured to connect to **https://jimacare.com/api/v1**

---

## 🔧 What I've Updated

### ✅ Provider App (`flutter_app`)
- **API URL:** `https://jimacare.com/api/v1`
- **Mock Mode:** Disabled (using real backend)
- **File:** `lib/config/api_config.dart`

### ✅ Client App (`client_app`)
- **API URL:** `https://jimacare.com/api/v1`
- **Mock Mode:** Disabled (using real backend)
- **File:** `lib/config/api_config.dart`

---

## 📋 Backend Requirements (jimacare.com)

Your Laravel backend at `jimacare.com` needs these API endpoints:

### ✅ Authentication Endpoints
```
POST   /api/v1/mobile/login
POST   /api/v1/mobile/register
POST   /api/v1/mobile/logout
GET    /api/v1/mobile/user
POST   /api/v1/mobile/forgot-password
POST   /api/v1/mobile/verify-phone
POST   /api/v1/mobile/resend-otp
```

### ✅ Provider App Endpoints
```
GET    /api/v1/search/jobs          # Browse jobs
GET    /api/v1/search/jobs/:id       # Job details
POST   /api/v1/booking/:userId       # Create booking
GET    /api/v1/booking/:id/status    # Booking status
POST   /api/v1/booking/:id/accept    # Accept booking
POST   /api/v1/booking/:id/decline   # Decline booking
POST   /api/v1/availability/toggle   # Toggle availability
GET    /api/v1/availability/status   # Get availability
GET    /api/v1/availability/nearby   # Nearby jobs
POST   /api/v1/location/update       # Update location
GET    /api/v1/location/track/:id    # Track location
```

### ✅ Client App Endpoints
```
GET    /api/v1/search/carers         # Search carers
GET    /api/v1/search/carers/:id      # Carer details
POST   /api/v1/booking/:userId       # Book carer
GET    /api/v1/bookings              # My bookings
POST   /api/v1/chatbot/post-job       # Post job
GET    /api/v1/chatbot/job-types      # Get job types
```

### ✅ Common Endpoints
```
POST   /api/v1/video/call/:userId    # Initiate video call
POST   /api/v1/video/join/:room       # Join video call
POST   /api/v1/video/end/:room       # End video call
POST   /api/v1/push/subscribe         # Subscribe to push
```

---

## 🔐 Backend Configuration Checklist

### 1. CORS Configuration
Your Laravel backend must allow requests from mobile apps:

**File:** `config/cors.php`
```php
'allowed_origins' => ['*'], // Or specific domains
'allowed_headers' => ['*'],
'allowed_methods' => ['*'],
'supports_credentials' => true,
```

### 2. API Authentication (Laravel Sanctum)
Ensure Sanctum is configured for mobile apps:

**File:** `config/sanctum.php`
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', '')),
```

**For mobile apps, you may need:**
```php
'stateful' => [], // Mobile apps use token-based auth
```

### 3. API Routes
Ensure all routes are in `routes/api.php` with prefix:

```php
Route::prefix('v1')->group(function () {
    // Mobile routes
    Route::prefix('mobile')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        // ... other routes
    });
    
    // Search routes
    Route::prefix('search')->group(function () {
        Route::get('/jobs', [JobController::class, 'search']);
        Route::get('/carers', [CarerController::class, 'search']);
    });
    
    // ... other route groups
});
```

### 4. SSL Certificate
Ensure `jimacare.com` has a valid SSL certificate (HTTPS).

### 5. API Response Format
Apps expect responses in this format:

**Success:**
```json
{
  "success": true,
  "data": { ... },
  "message": "Success message"
}
```

**Error:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

---

## 🧪 Testing the Connection

### 1. Test from Provider App
```powershell
cd C:\Users\lalyk\Downloads\flutter_app
flutter run
```

Try logging in with real credentials from your backend.

### 2. Test from Client App
```powershell
cd C:\Users\lalyk\Downloads\client_app
flutter run
```

Try logging in with real credentials from your backend.

### 3. Check API Connection
The apps will show error messages if:
- Backend is not accessible
- SSL certificate is invalid
- API endpoints are missing
- Authentication fails

---

## 🔄 Switching Between Modes

### Use Production (jimacare.com)
```dart
// In lib/config/api_config.dart
static const bool useMockMode = false;
static const String baseUrl = 'https://jimacare.com/api/v1';
```

### Use Local Development
```dart
// In lib/config/api_config.dart
static const bool useMockMode = false;
static const String baseUrl = 'http://10.0.2.2:8000/api/v1'; // Android
// or
static const String baseUrl = 'http://localhost:8000/api/v1'; // iOS
```

### Use Mock Mode (No Backend)
```dart
// In lib/config/api_config.dart
static const bool useMockMode = true;
// baseUrl is ignored in mock mode
```

---

## ✅ Current Status

**Both apps are now configured to:**
- ✅ Connect to `https://jimacare.com/api/v1`
- ✅ Use real backend (mock mode disabled)
- ✅ Send authentication tokens
- ✅ Handle API responses
- ✅ Show error messages if connection fails

---

## 🚨 Troubleshooting

### If apps can't connect:

1. **Check Backend is Running**
   - Visit `https://jimacare.com/api/v1/mobile/login` in browser
   - Should see API response (not 404)

2. **Check CORS**
   - Backend must allow mobile app requests
   - Check browser console for CORS errors

3. **Check SSL Certificate**
   - Must be valid HTTPS
   - Apps won't connect to HTTP in production

4. **Check API Endpoints**
   - Ensure all endpoints exist
   - Check route names match exactly

5. **Check Authentication**
   - Ensure Sanctum is configured
   - Tokens should be returned in login response

---

## 📱 Ready to Test!

Both apps are ready to connect to `jimacare.com`! 

Run them and try logging in with real credentials from your backend! 🚀

