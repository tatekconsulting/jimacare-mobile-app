# 📱 Flutter App Deployment Guide - Android & iOS

This guide will help you build and deploy your JimaCare Flutter app to Google Play Store (Android) and Apple App Store (iOS).

## 📋 Prerequisites

### For Android:
- ✅ Android toolchain already set up (you've done this!)
- Google Play Developer Account ($25 one-time fee)
- App signing key

### For iOS:
- Mac computer (required for iOS builds)
- Apple Developer Account ($99/year)
- Xcode installed
- CocoaPods installed

---

## 🤖 Android Deployment

### Step 1: Prepare Your App

1. **Update app information in `pubspec.yaml`:**
   ```yaml
   name: jimacare_app
   description: "JimaCare mobile application for Android and iOS"
   version: 1.0.0+1  # Format: version+buildNumber
   ```

2. **Update app name and package:**
   - Edit `android/app/build.gradle.kts`
   - Change `applicationId` to your unique package name (e.g., `com.jimacare.app`)

3. **Update app icons:**
   - Replace icons in `android/app/src/main/res/mipmap-*/`
   - Use Android Asset Studio: https://romannurik.github.io/AndroidAssetStudio/

### Step 2: Generate Signing Key

**Create a keystore file (one-time setup):**

```bash
# On Windows (PowerShell or Command Prompt)
keytool -genkey -v -keystore C:\Users\lalyk\jimacare-keystore.jks -storetype JKS -keyalg RSA -keysize 2048 -validity 10000 -alias jimacare

# You'll be prompted for:
# - Password (remember this!)
# - Your name, organization, etc.
```

**Important:** Save the keystore file and password securely! You'll need them for all future updates.

### Step 3: Configure Signing

1. **Create `android/key.properties` file:**
   ```properties
   storePassword=<your-keystore-password>
   keyPassword=<your-key-password>
   keyAlias=jimacare
   storeFile=C:\\Users\\lalyk\\jimacare-keystore.jks
   ```

2. **Update `android/app/build.gradle.kts`:**
   Add signing configuration (see detailed instructions below)

### Step 4: Build Release APK or App Bundle

**Option A: Build APK (for direct installation or testing)**
```bash
flutter build apk --release
```
Output: `build/app/outputs/flutter-apk/app-release.apk`

**Option B: Build App Bundle (for Play Store - Recommended)**
```bash
flutter build appbundle --release
```
Output: `build/app/outputs/bundle/release/app-release.aab`

### Step 5: Upload to Google Play Console

1. **Go to:** https://play.google.com/console
2. **Create a new app** (if first time)
3. **Fill in app details:**
   - App name: JimaCare
   - Default language
   - App or game
   - Free or paid
4. **Create a release:**
   - Go to "Production" → "Create new release"
   - Upload the `.aab` file
   - Add release notes
5. **Complete store listing:**
   - App description
   - Screenshots
   - Feature graphic
   - Privacy policy URL
6. **Submit for review**

---

## 🍎 iOS Deployment

### Step 1: Prerequisites (Mac Required)

1. **Install Xcode:**
   - Download from Mac App Store
   - Install Command Line Tools: `xcode-select --install`

2. **Install CocoaPods:**
   ```bash
   sudo gem install cocoapods
   ```

3. **Get Apple Developer Account:**
   - Sign up at: https://developer.apple.com/programs/
   - Cost: $99/year

### Step 2: Configure iOS App

1. **Update app information:**
   - Edit `ios/Runner/Info.plist`
   - Update bundle identifier in Xcode

2. **Update app icons:**
   - Replace icons in `ios/Runner/Assets.xcassets/AppIcon.appiconset/`
   - Use sizes: 20, 29, 40, 60, 76, 83.5, 1024 points

3. **Configure signing in Xcode:**
   - Open `ios/Runner.xcworkspace` in Xcode
   - Select Runner → Signing & Capabilities
   - Select your team
   - Xcode will create provisioning profile

### Step 3: Build for iOS

**For Testing (on device):**
```bash
flutter build ios --release
```

**For App Store:**
```bash
flutter build ipa --release
```
Output: `build/ios/ipa/jimacare_app.ipa`

### Step 4: Upload to App Store Connect

1. **Open Xcode:**
   ```bash
   open ios/Runner.xcworkspace
   ```

2. **Archive the app:**
   - Product → Archive
   - Wait for archive to complete

3. **Upload to App Store:**
   - Click "Distribute App"
   - Select "App Store Connect"
   - Follow the wizard

4. **Complete App Store listing:**
   - Go to: https://appstoreconnect.apple.com
   - Add app information
   - Upload screenshots
   - Set pricing
   - Submit for review

---

## 🔧 Detailed Configuration

### Android Signing Configuration

Edit `android/app/build.gradle.kts`:

```kotlin
// Add at the top
val keystoreProperties = Properties()
val keystorePropertiesFile = rootProject.file("key.properties")
if (keystorePropertiesFile.exists()) {
    keystoreProperties.load(FileInputStream(keystorePropertiesFile))
}

android {
    // ... existing code ...
    
    signingConfigs {
        create("release") {
            keyAlias = keystoreProperties["keyAlias"] as String
            keyPassword = keystoreProperties["keyPassword"] as String
            storeFile = file(keystoreProperties["storeFile"] as String)
            storePassword = keystoreProperties["storePassword"] as String
        }
    }
    
    buildTypes {
        getByName("release") {
            signingConfig = signingConfigs.getByName("release")
            // ... other release config ...
        }
    }
}
```

### iOS Bundle Identifier

1. Open `ios/Runner.xcodeproj` in Xcode
2. Select Runner target
3. Go to "General" tab
4. Change Bundle Identifier to: `com.jimacare.app` (or your unique identifier)

---

## 📝 App Store Requirements

### Android (Google Play):
- ✅ App icon (512x512 PNG)
- ✅ Feature graphic (1024x500)
- ✅ Screenshots (at least 2, up to 8)
- ✅ App description (4000 chars max)
- ✅ Privacy policy URL (required)
- ✅ Content rating questionnaire

### iOS (App Store):
- ✅ App icon (1024x1024 PNG, no transparency)
- ✅ Screenshots (required for all device sizes)
- ✅ App description
- ✅ Privacy policy URL (required)
- ✅ App preview video (optional)
- ✅ Support URL

---

## 🚀 Quick Deployment Checklist

### Android:
- [ ] Generate signing key
- [ ] Create `key.properties` file
- [ ] Update `build.gradle.kts` with signing config
- [ ] Update app package name
- [ ] Add app icons
- [ ] Build app bundle: `flutter build appbundle --release`
- [ ] Create Google Play Developer account
- [ ] Upload to Play Console
- [ ] Complete store listing
- [ ] Submit for review

### iOS:
- [ ] Get Apple Developer account
- [ ] Install Xcode and CocoaPods
- [ ] Update bundle identifier
- [ ] Add app icons
- [ ] Configure signing in Xcode
- [ ] Build IPA: `flutter build ipa --release`
- [ ] Archive in Xcode
- [ ] Upload to App Store Connect
- [ ] Complete App Store listing
- [ ] Submit for review

---

## 🔍 Testing Before Release

### Test on Real Devices:
```bash
# Android
flutter run --release -d <device-id>

# iOS
flutter run --release -d <device-id>
```

### Check for Issues:
```bash
# Analyze code
flutter analyze

# Run tests
flutter test

# Check app size
flutter build apk --release --analyze-size
```

---

## 📦 Version Management

### Update Version:

**In `pubspec.yaml`:**
```yaml
version: 1.0.1+2  # version+buildNumber
```

- **Version:** User-visible version (1.0.1)
- **Build Number:** Internal build number (2)

**For each release:**
- Increment version for major/minor updates
- Always increment build number

---

## 🆘 Troubleshooting

### Android Build Errors:
- **"Gradle sync failed":** Run `flutter clean` then `flutter pub get`
- **"Signing config error":** Check `key.properties` file path and passwords
- **"Package name conflict":** Change `applicationId` in `build.gradle.kts`

### iOS Build Errors:
- **"Code signing error":** Check Apple Developer account and certificates in Xcode
- **"CocoaPods error":** Run `cd ios && pod install`
- **"Provisioning profile error":** Refresh profiles in Xcode

---

## 📚 Resources

- **Flutter Deployment:** https://docs.flutter.dev/deployment
- **Google Play Console:** https://play.google.com/console
- **App Store Connect:** https://appstoreconnect.apple.com
- **Android Asset Studio:** https://romannurik.github.io/AndroidAssetStudio/
- **App Icon Generator:** https://www.appicon.co/

---

## 💡 Tips

1. **Start with Android** - Easier and faster to deploy
2. **Test thoroughly** - Use real devices before submitting
3. **Screenshots matter** - Good screenshots improve downloads
4. **Update regularly** - Keep your app updated with bug fixes
5. **Monitor reviews** - Respond to user feedback

---

**Ready to deploy?** Start with Android, then move to iOS. Good luck! 🚀

