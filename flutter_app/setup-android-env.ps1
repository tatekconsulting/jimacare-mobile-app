# Flutter Android Environment Setup Script
# This script helps set up Android environment variables for Flutter development

Write-Host "Flutter Android Environment Setup" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Detect Android SDK path
$androidSdkPath = "$env:LOCALAPPDATA\Android\sdk"
if (-not (Test-Path $androidSdkPath)) {
    Write-Host "Warning: Android SDK not found at: $androidSdkPath" -ForegroundColor Yellow
    Write-Host "Please install Android Studio or SDK first." -ForegroundColor Yellow
    Write-Host ""
    $customPath = Read-Host "Enter custom Android SDK path (or press Enter to use default)"
    if ($customPath) {
        $androidSdkPath = $customPath
    }
}

if (-not (Test-Path $androidSdkPath)) {
    Write-Host "Error: Android SDK path does not exist: $androidSdkPath" -ForegroundColor Red
    exit 1
}

Write-Host "Using Android SDK path: $androidSdkPath" -ForegroundColor Green
Write-Host ""

# Set ANDROID_HOME
Write-Host "Setting ANDROID_HOME environment variable..." -ForegroundColor Yellow
[Environment]::SetEnvironmentVariable("ANDROID_HOME", $androidSdkPath, "User")
$env:ANDROID_HOME = $androidSdkPath
Write-Host "[OK] ANDROID_HOME set to: $androidSdkPath" -ForegroundColor Green

# Get current PATH
$currentPath = [Environment]::GetEnvironmentVariable("Path", "User")
$pathsToAdd = @(
    "$androidSdkPath\platform-tools",
    "$androidSdkPath\tools",
    "$androidSdkPath\cmdline-tools\latest\bin"
)

# Add paths to PATH if they don't exist
Write-Host ""
Write-Host "Updating PATH environment variable..." -ForegroundColor Yellow
$pathUpdated = $false
foreach ($path in $pathsToAdd) {
    if (Test-Path $path) {
        if ($currentPath -notlike "*$path*") {
            $currentPath += ";$path"
            $pathUpdated = $true
            Write-Host "[OK] Added to PATH: $path" -ForegroundColor Green
        } else {
            Write-Host "  Already in PATH: $path" -ForegroundColor Gray
        }
    } else {
        Write-Host "  Path does not exist (will be added when created): $path" -ForegroundColor Yellow
        if ($currentPath -notlike "*$path*") {
            $currentPath += ";$path"
            $pathUpdated = $true
        }
    }
}

if ($pathUpdated) {
    [Environment]::SetEnvironmentVariable("Path", $currentPath, "User")
    $env:Path = [Environment]::GetEnvironmentVariable("Path", "Machine") + ";" + [Environment]::GetEnvironmentVariable("Path", "User")
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Setup Complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Restart your terminal/PowerShell window" -ForegroundColor White
Write-Host "2. Install Android cmdline-tools if not already installed:" -ForegroundColor White
Write-Host "   - Open Android Studio → SDK Manager → SDK Tools → Android SDK Command-line Tools" -ForegroundColor White
Write-Host "   OR download from: https://developer.android.com/studio#command-line-tools-only" -ForegroundColor White
Write-Host "3. Accept Android licenses:" -ForegroundColor White
Write-Host "   flutter doctor --android-licenses" -ForegroundColor Cyan
Write-Host "4. Verify setup:" -ForegroundColor White
Write-Host "   flutter doctor -v" -ForegroundColor Cyan
Write-Host ""

