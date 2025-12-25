# Flutter Setup Guide - Fixing Doctor Issues

This guide will help you resolve the issues found by `flutter doctor -v`.

## Current Status

✅ **Working:**
- Flutter SDK (3.38.3)
- Windows Version
- Chrome (for web development)
- Connected devices (Windows, Chrome, Edge)

❌ **Issues to Fix:**
1. Android toolchain - missing cmdline-tools and licenses
2. Visual Studio - not installed (needed for Windows app development)

---

## Issue 1: Android Toolchain

### Problem
- `cmdline-tools` component is missing
- Android license status unknown

### Solution

#### Option A: Install via Android Studio (Recommended)
1. Download and install [Android Studio](https://developer.android.com/studio)
2. Open Android Studio
3. Go to **Tools → SDK Manager**
4. In the **SDK Tools** tab, check:
   - ✅ Android SDK Command-line Tools (latest)
   - ✅ Android SDK Platform-Tools
   - ✅ Android SDK Build-Tools
5. Click **Apply** and wait for installation
6. Set environment variable `ANDROID_HOME`:
   - Open **System Properties → Environment Variables**
   - Add new System Variable:
     - Name: `ANDROID_HOME`
     - Value: `C:\Users\lalyk\AppData\Local\Android\sdk` (your SDK path)
   - Add to `Path` variable: `%ANDROID_HOME%\platform-tools` and `%ANDROID_HOME%\tools`

#### Option B: Install Command-line Tools Only
1. Download [Android Command-line Tools](https://developer.android.com/studio#command-line-tools-only)
2. Extract to: `C:\Users\lalyk\AppData\Local\Android\sdk\cmdline-tools\latest`
3. Set `ANDROID_HOME` environment variable (same as Option A)
4. Open PowerShell as Administrator and run:
   ```powershell
   $env:ANDROID_HOME = "C:\Users\lalyk\AppData\Local\Android\sdk"
   $env:PATH += ";$env:ANDROID_HOME\cmdline-tools\latest\bin"
   $env:PATH += ";$env:ANDROID_HOME\platform-tools"
   ```

### Accept Android Licenses
After installing cmdline-tools, run:
```powershell
flutter doctor --android-licenses
```
Press `y` to accept all licenses.

### Verify
Run `flutter doctor -v` again. Android toolchain should show ✅.

---

## Issue 2: Visual Studio (for Windows App Development)

### Problem
Visual Studio is not installed, which is required to build Windows desktop apps.

### Solution

#### Option A: Install Visual Studio Community (Recommended - Free)
1. Download [Visual Studio Community](https://visualstudio.microsoft.com/downloads/)
2. During installation, select the **"Desktop development with C++"** workload
3. Make sure these components are included:
   - ✅ MSVC v143 - VS 2022 C++ x64/x86 build tools
   - ✅ Windows 10 SDK (latest version)
   - ✅ C++ CMake tools for Windows
   - ✅ C++ core features

#### Option B: Install Visual Studio Build Tools Only
1. Download [Visual Studio Build Tools](https://visualstudio.microsoft.com/downloads/#build-tools-for-visual-studio-2022)
2. Install with **"Desktop development with C++"** workload

### Verify
After installation, restart your terminal and run:
```powershell
flutter doctor -v
```
Visual Studio should show ✅.

---

## Quick Fix Script

You can use the following PowerShell script to help set up Android environment variables:

```powershell
# Set Android environment variables
$androidSdkPath = "C:\Users\lalyk\AppData\Local\Android\sdk"
[Environment]::SetEnvironmentVariable("ANDROID_HOME", $androidSdkPath, "User")
$env:ANDROID_HOME = $androidSdkPath

# Add to PATH
$currentPath = [Environment]::GetEnvironmentVariable("Path", "User")
$pathsToAdd = @(
    "$androidSdkPath\platform-tools",
    "$androidSdkPath\tools",
    "$androidSdkPath\cmdline-tools\latest\bin"
)

foreach ($path in $pathsToAdd) {
    if ($currentPath -notlike "*$path*") {
        $currentPath += ";$path"
    }
}

[Environment]::SetEnvironmentVariable("Path", $currentPath, "User")
Write-Host "Android environment variables set. Please restart your terminal."
```

---

## Notes

- **For Web Development**: You don't need Android or Visual Studio - you can develop for web right now!
- **For Android Development**: You need to fix the Android toolchain issues.
- **For Windows Desktop Apps**: You need to install Visual Studio with C++ workload.

After fixing these issues, run `flutter doctor -v` to verify everything is working.

---

## Testing Your Setup

Once fixed, test with:
```powershell
# For web
flutter run -d chrome

# For Windows (after Visual Studio is installed)
flutter run -d windows

# For Android (after Android setup is complete)
flutter run -d android
```

