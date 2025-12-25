# ⚡ Quick Fix for Login Error

## 🔴 Current Issue
You're seeing "Login Failed" when trying to log in on web (Chrome).

## ✅ Quick Solution: Enable Mock Mode

**This lets you test the app without backend connection issues.**

### Steps:

1. **Open:** `lib/config/api_config.dart`

2. **Change this line:**
   ```dart
   static const bool useMockMode = false;  // Change to true
   ```
   
   To:
   ```dart
   static const bool useMockMode = true;  // Enable mock mode
   ```

3. **Hot restart the app:**
   - Press `R` in terminal, or
   - Stop and run `flutter run -d chrome` again

4. **Try logging in:**
   - Use any email (e.g., `test@example.com`)
   - Use any password (e.g., `password123`)
   - Should work immediately!

---

## 🔍 Why This Happens

**You're running on web (Chrome), which has CORS restrictions.**

When the app tries to connect to `https://jimacare.com/api/v1`, the browser blocks it because:
- The app is on `localhost:57878`
- The API is on `jimacare.com`
- Different origins = CORS restriction

---

## 🛠️ Permanent Fix (For Real Backend)

### Option 1: Fix CORS on Backend

**Update your Laravel backend `config/cors.php`:**

```php
'allowed_origins' => [
    'http://localhost:*',
    'http://127.0.0.1:*',
    'https://jimacare.com',
],
'allowed_headers' => ['*'],
'allowed_methods' => ['*'],
'supports_credentials' => true,
```

### Option 2: Test on Android/iOS Instead

**Web has CORS, mobile doesn't:**

```powershell
# Run on Android
flutter run -d android

# Or iOS
flutter run -d ios
```

---

## 📋 What to Check

1. **Browser Console (F12):**
   - Press F12
   - Go to Console tab
   - Look for red errors
   - Check Network tab for the login request

2. **Common Errors:**
   - `CORS policy` → CORS issue
   - `404 Not Found` → API endpoint missing
   - `NetworkError` → Can't connect
   - `401 Unauthorized` → Wrong credentials

---

## 🎯 Recommended: Use Mock Mode for Now

**Enable mock mode to test the app UI, then fix CORS later for production.**

The app works perfectly in mock mode - you can test all features!

