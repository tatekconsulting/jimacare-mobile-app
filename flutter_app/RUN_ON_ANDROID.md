# 📱 How to Run App on Android

## ✅ You Have an Android Emulator Available!

You have: **Medium Phone API 36.0**

---

## 🚀 Option 1: Start Android Emulator (Recommended)

### Step 1: Launch the Emulator

```powershell
flutter emulators --launch Medium_Phone_API_36.0
```

**Wait for the emulator to fully start** (may take 1-2 minutes)

### Step 2: Run the App

Once the emulator is running, use:

```powershell
flutter run
```

Or specify Android:

```powershell
flutter run -d Medium_Phone_API_36.0
```

---

## 🎯 Option 2: Use Mock Mode on Web (Quickest)

**If you want to test quickly without starting emulator:**

Mock mode is already enabled! Just run:

```powershell
flutter run -d chrome
```

Then try logging in with any email/password - it will work!

---

## 🔧 Option 3: Test on Windows Desktop

You can also test on Windows:

```powershell
flutter run -d windows
```

---

## 📋 Quick Commands

```powershell
# Start Android emulator
flutter emulators --launch Medium_Phone_API_36.0

# Run app (after emulator starts)
flutter run

# Or run on web with mock mode
flutter run -d chrome

# Or run on Windows
flutter run -d windows
```

---

## ⚠️ Note

**Mock mode is currently enabled** in `lib/config/api_config.dart`

This means:
- ✅ App works without backend connection
- ✅ No CORS issues
- ✅ Can test all UI features
- ✅ Use any email/password to login

**To use real backend later:**
- Set `useMockMode = false` in `api_config.dart`
- Make sure backend CORS is configured

---

## 🎯 Recommended: Start Android Emulator

**Best for testing mobile features:**

1. Run: `flutter emulators --launch Medium_Phone_API_36.0`
2. Wait for emulator to start
3. Run: `flutter run`
4. App will install and open on emulator!
