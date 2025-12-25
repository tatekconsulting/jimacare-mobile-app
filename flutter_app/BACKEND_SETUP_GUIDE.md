# 🔧 Backend Setup Guide - Fixing 404 Error

## ❌ Current Problem

You're getting a **404 Not Found** error because:
- The API endpoint `https://jimacare.com/api/v1` doesn't exist yet
- OR your backend isn't deployed
- OR the URL is incorrect

---

## ✅ Solutions

### Option 1: Use Local Laravel Backend (Recommended for Development)

If you have Laravel running locally:

1. **Start your Laravel server:**
   ```bash
   cd path/to/your/laravel/project
   php artisan serve
   ```
   This runs on `http://localhost:8000`

2. **Update API URL in Flutter app:**

   Open `lib/config/api_config.dart` and change:
   ```dart
   // For Android Emulator
   static const String baseUrl = 'http://10.0.2.2:8000/api/v1';
   
   // OR for iOS Simulator
   // static const String baseUrl = 'http://localhost:8000/api/v1';
   
   // OR for Physical Device (use your computer's IP)
   // Find IP: ipconfig (Windows) or ifconfig (Mac/Linux)
   // static const String baseUrl = 'http://192.168.1.XXX:8000/api/v1';
   ```

3. **Hot reload the app** (press `r` in terminal)

### Option 2: Deploy Backend to Production

If you want to use `https://jimacare.com`:

1. **Deploy your Laravel backend** to your server
2. **Ensure API routes are set up:**
   ```php
   // routes/api.php
   Route::prefix('v1/mobile')->group(function () {
       Route::post('/login', [MobileAuthController::class, 'login']);
       Route::post('/register', [MobileAuthController::class, 'register']);
       // ... other routes
   });
   ```

3. **Test the endpoint:**
   ```bash
   curl -X POST https://jimacare.com/api/v1/mobile/login \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"email":"test@example.com","password":"password"}'
   ```

### Option 3: Use Different Domain/Server

If your backend is on a different URL:

1. **Update `lib/config/api_config.dart`:**
   ```dart
   static const String baseUrl = 'https://your-actual-domain.com/api/v1';
   ```

---

## 🚀 Quick Setup: Create Laravel Backend Routes

If you need to create the backend endpoints, here's what you need:

### 1. Create Mobile Auth Controller

```php
// app/Http/Controllers/Api/MobileAuthController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Login successful',
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Registration successful',
        ], 201);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
```

### 2. Add Routes

```php
// routes/api.php
Route::prefix('v1/mobile')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\MobileAuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\MobileAuthController::class, 'register']);
});

Route::prefix('v1/mobile')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', [\App\Http\Controllers\Api\MobileAuthController::class, 'user']);
    Route::post('/logout', [\App\Http\Controllers\Api\MobileAuthController::class, 'logout']);
});
```

### 3. Install Laravel Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 4. Configure CORS

```php
// config/cors.php
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
'allowed_methods' => ['*'],
```

---

## 📱 Testing Without Backend (Temporary)

If you want to test the app UI without a backend, I can add a mock/test mode. Let me know if you want this!

---

## ✅ Next Steps

1. **Choose an option above** (local backend recommended for development)
2. **Update `lib/config/api_config.dart`** with the correct URL
3. **Hot reload the app** (press `r`)
4. **Try logging in again**

---

## 🔍 How to Find Your Computer's IP (for Physical Device Testing)

**Windows:**
```powershell
ipconfig
# Look for "IPv4 Address" under your network adapter
```

**Mac/Linux:**
```bash
ifconfig
# Look for "inet" address
```

Then use: `http://YOUR_IP:8000/api/v1`

---

## 💡 Quick Fix Right Now

**For Android Emulator with local Laravel:**

1. Make sure Laravel is running: `php artisan serve`
2. Update `lib/config/api_config.dart`:
   ```dart
   static const String baseUrl = 'http://10.0.2.2:8000/api/v1';
   ```
3. Hot reload: Press `r` in terminal
4. Try login again

**Need help setting up the Laravel backend? Let me know!** 🚀

