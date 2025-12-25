# 🔧 Login Troubleshooting Guide

## Current Issue: Login Failed

If you're seeing "LOGIN FAILED" when trying to log in, here's how to fix it:

---

## 🔍 Step 1: Check Browser Console

**Open browser console (F12) and check for errors:**

1. Press **F12** in your browser
2. Go to **Console** tab
3. Look for red error messages
4. Check **Network** tab to see the API request

**Common errors you might see:**
- `CORS policy` - CORS issue
- `404 Not Found` - API endpoint doesn't exist
- `NetworkError` - Can't connect to server
- `401 Unauthorized` - Wrong credentials

---

## 🌐 Issue: Running on Web (Chrome)

Since you're running on `localhost:57878`, you're testing on **web**. This can cause:

### Problem 1: CORS (Cross-Origin Resource Sharing)

**Symptoms:**
- Error in console: "CORS policy" or "Access-Control-Allow-Origin"
- Login fails immediately
- Network tab shows CORS error

**Solution:**

Your Laravel backend at `jimacare.com` needs to allow requests from `localhost`.

**Update `config/cors.php` in your Laravel backend:**
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

**Or for development, allow all origins:**
```php
'allowed_origins' => ['*'],
```

---

### Problem 2: API Endpoint Not Found (404)

**Symptoms:**
- Error: "404 Not Found"
- Network tab shows 404 status

**Check:**
1. Visit `https://jimacare.com/api/v1/mobile/login` in browser
2. Should see API response (not 404 page)

**If 404:**
- The route doesn't exist in your Laravel backend
- Check `routes/api.php` for the route

---

### Problem 3: SSL Certificate Issue

**Symptoms:**
- Error: "Certificate error" or "SSL error"
- Can't connect to HTTPS

**Solution:**
- Ensure `jimacare.com` has valid SSL certificate
- For testing, you might need to use HTTP (not recommended for production)

---

## ✅ Quick Fixes

### Option 1: Use Mock Mode (For Testing UI)

**Temporarily enable mock mode to test the app UI:**

1. Open `lib/config/api_config.dart`
2. Change:
   ```dart
   static const bool useMockMode = true; // Enable mock mode
   ```
3. Hot restart the app
4. Try logging in with any email/password

**This will let you test the app without backend connection.**

---

### Option 2: Test on Android/iOS Instead

**Web has CORS restrictions. Mobile apps don't:**

```powershell
# Run on Android
flutter run -d android

# Or iOS
flutter run -d ios
```

---

### Option 3: Use Local Backend

**If you have Laravel running locally:**

1. Start Laravel: `php artisan serve`
2. Update `lib/config/api_config.dart`:
   ```dart
   static const bool useMockMode = false;
   static const String baseUrl = 'http://localhost:8000/api/v1';
   ```
3. For web, this should work without CORS issues

---

## 🔍 Debug Steps

### 1. Check What Error You're Getting

The login screen should show a dialog with the error message. Check:
- What does the error message say?
- Is it a CORS error?
- Is it a 404 error?
- Is it a connection error?

### 2. Check Browser Console

1. Press **F12**
2. Go to **Console** tab
3. Look for error messages
4. Check **Network** tab
5. Find the login request
6. See what status code it returns

### 3. Test API Directly

**Open in browser:**
```
https://jimacare.com/api/v1/mobile/login
```

**Should see:**
- API response (JSON)
- Or error message
- Not a 404 page

### 4. Check API Configuration

**File:** `lib/config/api_config.dart`

**Current setting:**
```dart
static const bool useMockMode = false;
static const String baseUrl = 'https://jimacare.com/api/v1';
```

**Make sure:**
- `useMockMode` is `false` (to use real backend)
- `baseUrl` is correct
- No typos in the URL

---

## 🎯 Recommended Solution

### For Development/Testing:

**Use Mock Mode:**
```dart
// In lib/config/api_config.dart
static const bool useMockMode = true;
```

This lets you test the app UI without backend issues.

### For Production:

**Fix CORS on backend:**
1. Update Laravel `config/cors.php`
2. Allow requests from your app domains
3. Test the connection

**Or test on mobile:**
- Android/iOS don't have CORS restrictions
- Run: `flutter run -d android`

---

## 📱 Next Steps

1. **Check browser console** (F12) for actual error
2. **Try mock mode** to test UI
3. **Fix CORS** on backend if needed
4. **Test on Android/iOS** instead of web

Let me know what error you see in the browser console, and I can help fix it! 🔧

