# 🤖 Android Deployment - Step by Step

Quick reference guide for deploying to Google Play Store.

## Step 1: Generate Signing Key

```bash
keytool -genkey -v -keystore C:\Users\lalyk\jimacare-keystore.jks -storetype JKS -keyalg RSA -keysize 2048 -validity 10000 -alias jimacare
```

**Save these securely:**
- Keystore password
- Key password
- Keystore file location

## Step 2: Create key.properties

Create file: `android/key.properties`

```properties
storePassword=YOUR_KEYSTORE_PASSWORD
keyPassword=YOUR_KEY_PASSWORD
keyAlias=jimacare
storeFile=C:\\Users\\lalyk\\jimacare-keystore.jks
```

## Step 3: Update build.gradle.kts

Edit `android/app/build.gradle.kts`:

**Add at the top (after imports):**
```kotlin
val keystoreProperties = Properties()
val keystorePropertiesFile = rootProject.file("key.properties")
if (keystorePropertiesFile.exists()) {
    keystoreProperties.load(FileInputStream(keystorePropertiesFile))
}
```

**Update android block:**
```kotlin
android {
    namespace = "com.jimacare.app"  // Change to your package name
    
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
            isMinifyEnabled = true
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
        }
    }
}
```

## Step 4: Update Package Name

In `android/app/build.gradle.kts`, change:
```kotlin
applicationId = "com.jimacare.app"  // Use your unique package name
```

## Step 5: Build App Bundle

```bash
flutter build appbundle --release
```

Output: `build/app/outputs/bundle/release/app-release.aab`

## Step 6: Upload to Play Store

1. Go to https://play.google.com/console
2. Create app (if first time)
3. Upload `.aab` file
4. Complete store listing
5. Submit for review

**That's it!** 🎉

