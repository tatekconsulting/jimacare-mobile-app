# Fixing "Not Enough Space on Disk" Error

## Problem
The build is trying to download Android SDK Build-Tools 35, but there's not enough disk space.

## Solutions

### Option 1: Free Up Disk Space (Recommended)

1. **Check available space:**
   ```powershell
   Get-PSDrive C | Select-Object Free,@{Name="FreeGB";Expression={[math]::Round($_.Free/1GB,2)}}
   ```

2. **Free up space:**
   - Delete temporary files: `%TEMP%` folder
   - Empty Recycle Bin
   - Uninstall unused programs
   - Use Disk Cleanup tool
   - Delete old Android SDK versions you don't need

3. **You need at least 2-3 GB free** for the build tools download

### Option 2: Use Older Build Tools (Quick Fix)

I've already configured your build to use Build-Tools 34.0.0. If that's not installed either, you can:

1. **Install via Android Studio:**
   - Open Android Studio
   - Tools → SDK Manager
   - SDK Tools tab
   - Check "Android SDK Build-Tools 34.0.0" (or 33.0.0)
   - Click Apply

2. **Or manually specify version in `android/app/build.gradle.kts`:**
   ```kotlin
   android {
       buildToolsVersion = "34.0.0"  // or "33.0.0"
   }
   ```

### Option 3: Disable Minification (Temporary)

I've already disabled minification in your build config. This reduces build requirements but increases app size slightly.

### Option 4: Build APK Instead of Bundle

APK builds are smaller and faster:

```bash
flutter build apk --release
```

You can still upload APK to Play Store (though AAB is preferred).

## Recommended Action

1. **Free up at least 3 GB of disk space**
2. **Then try building again:**
   ```bash
   flutter build appbundle --release
   ```

## Check What's Taking Space

```powershell
# Check Android SDK size
Get-ChildItem "C:\Users\lalyk\AppData\Local\Android\Sdk" -Recurse | Measure-Object -Property Length -Sum | Select-Object @{Name="SizeGB";Expression={[math]::Round($_.Sum/1GB,2)}}

# Check temp files
Get-ChildItem $env:TEMP -Recurse -ErrorAction SilentlyContinue | Measure-Object -Property Length -Sum | Select-Object @{Name="SizeGB";Expression={[math]::Round($_.Sum/1GB,2)}}
```

## After Freeing Space

Once you have enough space, the build should work. The Android SDK will download Build-Tools 35 automatically.

