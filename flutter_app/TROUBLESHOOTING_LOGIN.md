# 🔧 Troubleshooting Login Issues

## Common Login Problems & Solutions

### 1. **"Cannot connect to server" Error**

**Problem:** The app can't reach your backend API.

**Solutions:**
- Check if your Laravel backend is running
- Verify the API URL in `lib/config/api_config.dart`:
  - Production: `https://jimacare.com/api/v1`
  - Local (Android Emulator): `http://10.0.2.2:8000/api/v1`
  - Local (iOS Simulator): `http://localhost:8000/api/v1`
- Check your internet connection
- Verify the backend server is accessible

### 2. **"404 Not Found" Error**

**Problem:** The login endpoint doesn't exist on your backend.

**Solutions:**
- Ensure your Laravel backend has the route: `POST /api/v1/mobile/login`
- Check your `routes/api.php` file
- Verify the route is registered correctly

### 3. **"401 Unauthorized" or "Invalid credentials"**

**Problem:** Wrong email/password or user doesn't exist.

**Solutions:**
- Verify your credentials are correct
- Check if the user exists in your database
- Try registering a new account first

### 4. **"CORS Error" or "Network Error"**

**Problem:** Backend is blocking requests from the mobile app.

**Solutions:**
- Configure CORS in Laravel (`config/cors.php`):
  ```php
  'allowed_origins' => ['*'],
  'allowed_headers' => ['*'],
  'allowed_methods' => ['*'],
  ```
- Clear Laravel cache: `php artisan config:clear`

### 5. **"Timeout" Error**

**Problem:** Server is taking too long to respond.

**Solutions:**
- Check if backend is overloaded
- Increase timeout in `api_config.dart` if needed
- Check server logs for errors

---

## 🔍 Debugging Steps

### Step 1: Check Console Logs

When you try to login, check the console output. You should see:
```
Attempting login to: https://jimacare.com/api/v1/mobile/login
Email: your@email.com
Response status: 200 (or error code)
Response data: {...}
```

### Step 2: Test API Manually

Test your backend API directly:

**Using curl:**
```bash
curl -X POST https://jimacare.com/api/v1/mobile/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

**Using Postman or browser:**
- URL: `https://jimacare.com/api/v1/mobile/login`
- Method: POST
- Headers: `Content-Type: application/json`, `Accept: application/json`
- Body: `{"email":"test@example.com","password":"password123"}`

### Step 3: Verify Backend Setup

Ensure your Laravel backend has:

1. **Route defined** (`routes/api.php`):
```php
Route::prefix('v1/mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login']);
});
```

2. **Controller method** that returns:
```php
return response()->json([
    'token' => $token,
    'user' => $user,
    'message' => 'Login successful'
], 200);
```

3. **CORS configured** (`config/cors.php`)

---

## 🧪 Quick Test

Try these steps:

1. **Check if backend is running:**
   - Open browser: `https://jimacare.com/api/v1/mobile/login`
   - Should show error (method not allowed for GET) but confirms server is up

2. **Test with test credentials:**
   - Try registering first
   - Then login with those credentials

3. **Check error message:**
   - The app now shows detailed error messages
   - Look at the red error dialog/snackbar

---

## 📝 What Error Are You Seeing?

Please check:
1. What error message appears when you try to login?
2. What do you see in the console/terminal?
3. Is your backend running and accessible?
4. Have you tested the API endpoint directly?

---

## 🔧 Quick Fixes

### For Local Development:

If testing locally, update `lib/config/api_config.dart`:

```dart
// For Android Emulator
static const String baseUrl = 'http://10.0.2.2:8000/api/v1';

// For iOS Simulator  
static const String baseUrl = 'http://localhost:8000/api/v1';
```

Then restart the app.

### For Production:

Ensure:
- Backend is deployed and accessible
- API routes are correct
- CORS is configured
- SSL certificate is valid (for HTTPS)

---

## 💡 Still Having Issues?

Share:
1. The exact error message
2. Console output
3. Whether backend is accessible
4. API endpoint test results

Then we can fix it together! 🚀

