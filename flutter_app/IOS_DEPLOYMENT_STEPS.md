# 🍎 iOS Deployment - Step by Step

Quick reference guide for deploying to Apple App Store.

## Prerequisites

- ✅ Mac computer (required)
- ✅ Apple Developer Account ($99/year)
- ✅ Xcode installed
- ✅ CocoaPods installed

## Step 1: Install CocoaPods

```bash
sudo gem install cocoapods
```

## Step 2: Install Pods

```bash
cd ios
pod install
cd ..
```

## Step 3: Open in Xcode

```bash
open ios/Runner.xcworkspace
```

## Step 4: Configure Signing

1. In Xcode, select **Runner** project
2. Select **Runner** target
3. Go to **Signing & Capabilities** tab
4. Select your **Team** (Apple Developer account)
5. Xcode will automatically create provisioning profile

## Step 5: Update Bundle Identifier

1. In Xcode, go to **General** tab
2. Change **Bundle Identifier** to: `com.jimacare.app` (or your unique ID)

## Step 6: Build for Release

**Option 1: Using Flutter (Recommended)**
```bash
flutter build ipa --release
```

**Option 2: Using Xcode**
1. Product → Archive
2. Wait for archive to complete
3. Click "Distribute App"
4. Select "App Store Connect"
5. Follow wizard

## Step 7: Upload to App Store Connect

1. Go to https://appstoreconnect.apple.com
2. Create new app (if first time)
3. Fill in app information
4. Upload build from Xcode or use Transporter app
5. Complete App Store listing
6. Submit for review

## Common Issues

**"No signing certificate":**
- Make sure you're logged into Xcode with your Apple Developer account
- Xcode → Preferences → Accounts → Add your Apple ID

**"CocoaPods error":**
```bash
cd ios
pod deintegrate
pod install
cd ..
```

**That's it!** 🎉

