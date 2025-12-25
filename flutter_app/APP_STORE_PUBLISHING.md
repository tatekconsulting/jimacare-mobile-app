# 📱 App Store Publishing Guide - JimaCare

## 🎯 Overview

This guide will help you publish your JimaCare app to:
- ✅ **Google Play Store** (Android) - $25 one-time fee
- ✅ **Apple App Store** (iOS) - $99/year

---

## 🤖 Google Play Store (Android)

### Prerequisites
- ✅ Google Play Developer Account ($25 one-time)
- ✅ App signing key (we'll create this)
- ✅ App bundle (.aab file)

### Step 1: Generate Signing Key

**Create a keystore file (one-time, save securely!):**

```powershell
keytool -genkey -v -keystore C:\Users\lalyk\jimacare-keystore.jks -storetype JKS -keyalg RSA -keysize 2048 -validity 10000 -alias jimacare
```

**You'll be asked for:**
- Password (save this securely!)
- Your name, organization, city, etc.

**⚠️ IMPORTANT:** Save the keystore file and password! You'll need them for ALL future app updates.

### Step 2: Create key.properties File

Create file: `android/key.properties`

```properties
storePassword=YOUR_KEYSTORE_PASSWORD
keyPassword=YOUR_KEY_PASSWORD
keyAlias=jimacare
storeFile=C:\\Users\\lalyk\\jimacare-keystore.jks
```

**⚠️ Add this file to `.gitignore` to keep passwords secure!**

### Step 3: Build Release App Bundle

```powershell
flutter build appbundle --release
```

**Output:** `build/app/outputs/bundle/release/app-release.aab`

### Step 4: Upload to Google Play Console

1. **Go to:** https://play.google.com/console
2. **Create new app:**
   - App name: JimaCare
   - Default language: English
   - App or game: App
   - Free or paid: Choose your option
3. **Create release:**
   - Go to "Production" → "Create new release"
   - Upload `app-release.aab` file
   - Add release notes (e.g., "Initial release")
4. **Complete store listing:**
   - App description (4000 chars max)
   - Screenshots (at least 2, up to 8)
   - Feature graphic (1024x500 PNG)
   - App icon (512x512 PNG)
   - Privacy policy URL (required!)
   - Content rating questionnaire
5. **Submit for review** (takes 1-7 days)

---

## 🍎 Apple App Store (iOS)

### Prerequisites
- ✅ Mac computer (required)
- ✅ Apple Developer Account ($99/year)
- ✅ Xcode installed
- ✅ CocoaPods installed

### Step 1: Get Apple Developer Account

1. Go to: https://developer.apple.com/programs/
2. Sign up for Apple Developer Program
3. Pay $99/year fee

### Step 2: Install Xcode (on Mac)

1. Download Xcode from Mac App Store
2. Install Command Line Tools:
   ```bash
   xcode-select --install
   ```

### Step 3: Install CocoaPods

```bash
sudo gem install cocoapods
cd ios
pod install
cd ..
```

### Step 4: Configure iOS App

1. **Open in Xcode:**
   ```bash
   open ios/Runner.xcworkspace
   ```

2. **Configure signing:**
   - Select Runner project
   - Go to "Signing & Capabilities"
   - Select your Apple Developer team
   - Xcode will create provisioning profile automatically

3. **Update Bundle Identifier:**
   - In Xcode, go to "General" tab
   - Change to: `com.jimacare.app` (or your unique ID)

### Step 5: Build for App Store

**Option 1: Using Flutter (Recommended)**
```bash
flutter build ipa --release
```

**Option 2: Using Xcode**
1. Product → Archive
2. Wait for archive to complete
3. Click "Distribute App"
4. Select "App Store Connect"
5. Follow the wizard

### Step 6: Upload to App Store Connect

1. **Go to:** https://appstoreconnect.apple.com
2. **Create new app:**
   - App name: JimaCare
   - Primary language
   - Bundle ID: com.jimacare.app
   - SKU: unique identifier
3. **Complete App Store listing:**
   - Description
   - Keywords
   - Screenshots (required for each device size)
   - App icon (1024x1024 PNG)
   - Privacy policy URL
   - Support URL
4. **Submit for review** (takes 1-3 days typically)

---

## 📋 App Store Requirements Checklist

### Both Stores Need:
- ✅ App icon (high quality)
- ✅ Screenshots (multiple sizes)
- ✅ App description
- ✅ Privacy policy URL (required!)
- ✅ Support/contact information
- ✅ Content rating

### Google Play Specific:
- Feature graphic (1024x500)
- Short description (80 chars)
- Long description (4000 chars)

### Apple App Store Specific:
- Screenshots for each device size (iPhone, iPad)
- App preview videos (optional but recommended)
- Keywords (100 chars max)
- Support URL

---

## 🔒 Security & Privacy

### Before Publishing:

1. **Privacy Policy:**
   - Create a privacy policy page on your website
   - Must explain what data you collect
   - Required by both stores

2. **App Permissions:**
   - Only request permissions you actually use
   - Explain why you need each permission

3. **Data Security:**
   - Use HTTPS for all API calls (you're already doing this!)
   - Encrypt sensitive data
   - Follow GDPR/CCPA if applicable

---

## 🚀 Quick Commands Reference

### Android:
```powershell
# Build release APK (for testing)
flutter build apk --release

# Build App Bundle (for Play Store)
flutter build appbundle --release

# Check app size
flutter build appbundle --release --analyze-size
```

### iOS (on Mac):
```bash
# Build for device testing
flutter build ios --release

# Build IPA for App Store
flutter build ipa --release

# Open in Xcode
open ios/Runner.xcworkspace
```

---

## 📝 Version Management

**Update version in `pubspec.yaml`:**
```yaml
version: 1.0.0+1  # Format: version+buildNumber
```

- **Version** (1.0.0): User-visible version
- **Build Number** (+1): Internal build counter (increment for each release)

**For updates:**
- Bug fixes: 1.0.1+2
- New features: 1.1.0+3
- Major changes: 2.0.0+4

---

## 🐛 Common Issues

### Android:
**"Keystore file not found":**
- Check path in `key.properties`
- Use double backslashes: `C:\\Users\\...`

**"Signing config error":**
- Verify `key.properties` file exists
- Check passwords are correct

### iOS:
**"No signing certificate":**
- Make sure you're logged into Xcode with Apple Developer account
- Xcode → Preferences → Accounts → Add Apple ID

**"CocoaPods error":**
```bash
cd ios
pod deintegrate
pod install
cd ..
```

---

## ✅ Pre-Publishing Checklist

Before submitting to stores:

- [ ] Test app thoroughly on real devices
- [ ] Update app version number
- [ ] Create app icons (all sizes)
- [ ] Take screenshots
- [ ] Write app description
- [ ] Create privacy policy page
- [ ] Test login/registration flow
- [ ] Test all main features
- [ ] Check for crashes or errors
- [ ] Verify API endpoints work
- [ ] Test on different Android/iOS versions
- [ ] Prepare store listing content

---

## 🎉 After Publishing

### Google Play:
- Review takes 1-7 days
- App goes live automatically after approval
- You can track downloads in Play Console

### Apple App Store:
- Review takes 1-3 days typically
- You'll get email when approved
- You can schedule release date
- Track analytics in App Store Connect

---

## 📞 Need Help?

- **Google Play Help:** https://support.google.com/googleplay/android-developer
- **Apple Developer Support:** https://developer.apple.com/support/
- **Flutter Documentation:** https://docs.flutter.dev/deployment

---

**Good luck with your app launch! 🚀**

