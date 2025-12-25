# Install Android Command-line Tools - Automatic
# This script automatically downloads and installs Android SDK Command-line Tools

Write-Host "Android Command-line Tools Installer" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

$androidSdkPath = "$env:LOCALAPPDATA\Android\sdk"
$cmdlineToolsPath = "$androidSdkPath\cmdline-tools\latest"

# Check if already installed
if (Test-Path "$cmdlineToolsPath\bin\sdkmanager.bat") {
    Write-Host "[OK] Command-line tools already installed at: $cmdlineToolsPath" -ForegroundColor Green
    Write-Host ""
    Write-Host "You can now run: flutter doctor --android-licenses" -ForegroundColor Yellow
    exit 0
}

Write-Host "Android SDK path: $androidSdkPath" -ForegroundColor Yellow
Write-Host ""

# Create directories if they don't exist
if (-not (Test-Path $androidSdkPath)) {
    Write-Host "Creating Android SDK directory..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $androidSdkPath -Force | Out-Null
}

if (-not (Test-Path "$androidSdkPath\cmdline-tools")) {
    Write-Host "Creating cmdline-tools directory..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path "$androidSdkPath\cmdline-tools" -Force | Out-Null
}

Write-Host ""
Write-Host "Downloading Android Command-line Tools..." -ForegroundColor Yellow
Write-Host "This may take a few minutes..." -ForegroundColor Gray
Write-Host ""

# Download URL for Windows command-line tools (latest version)
$downloadUrl = "https://dl.google.com/android/repository/commandlinetools-win-11076708_latest.zip"
$zipFile = "$env:TEMP\android-cmdline-tools.zip"

try {
    # Download the file
    Write-Host "Downloading from Google..." -ForegroundColor Gray
    $ProgressPreference = 'SilentlyContinue'  # Suppress progress bar for cleaner output
    Invoke-WebRequest -Uri $downloadUrl -OutFile $zipFile -UseBasicParsing
    $ProgressPreference = 'Continue'
    
    Write-Host "[OK] Download complete" -ForegroundColor Green
    Write-Host ""
    
    # Extract to temp location first
    $tempExtractPath = "$env:TEMP\android-cmdline-tools-temp"
    if (Test-Path $tempExtractPath) {
        Remove-Item $tempExtractPath -Recurse -Force
    }
    New-Item -ItemType Directory -Path $tempExtractPath -Force | Out-Null
    
    Write-Host "Extracting files..." -ForegroundColor Yellow
    Expand-Archive -Path $zipFile -DestinationPath $tempExtractPath -Force
    
    # The extracted folder structure varies, so we need to find the cmdline-tools folder
    $extractedItems = Get-ChildItem $tempExtractPath
    $sourcePath = $null
    
    # Look for cmdline-tools folder
    foreach ($item in $extractedItems) {
        if ($item.Name -eq "cmdline-tools") {
            $sourcePath = $item.FullName
            break
        }
    }
    
    # If not found, use the first folder (it might be the cmdline-tools content)
    if (-not $sourcePath) {
        $sourcePath = $extractedItems[0].FullName
    }
    
    # Move to latest folder
    if (Test-Path $cmdlineToolsPath) {
        Write-Host "Removing existing installation..." -ForegroundColor Yellow
        Remove-Item $cmdlineToolsPath -Recurse -Force
    }
    New-Item -ItemType Directory -Path $cmdlineToolsPath -Force | Out-Null
    
    # Copy all files from extracted folder to latest
    Write-Host "Installing to: $cmdlineToolsPath" -ForegroundColor Yellow
    Copy-Item -Path "$sourcePath\*" -Destination $cmdlineToolsPath -Recurse -Force
    
    # Cleanup
    Remove-Item $tempExtractPath -Recurse -Force
    Remove-Item $zipFile -Force
    
    Write-Host "[OK] Installation complete!" -ForegroundColor Green
    Write-Host ""
    
    # Verify installation
    if (Test-Path "$cmdlineToolsPath\bin\sdkmanager.bat") {
        Write-Host "Verification: Command-line tools installed successfully" -ForegroundColor Green
        Write-Host ""
        Write-Host "========================================" -ForegroundColor Cyan
        Write-Host "Next steps:" -ForegroundColor Yellow
        Write-Host "1. Restart your terminal/PowerShell window" -ForegroundColor White
        Write-Host "2. Run: flutter doctor --android-licenses" -ForegroundColor Cyan
        Write-Host "3. Press 'y' to accept all licenses" -ForegroundColor White
        Write-Host "4. Verify: flutter doctor -v" -ForegroundColor Cyan
        Write-Host ""
    } else {
        Write-Host "Warning: Installation may have failed. sdkmanager.bat not found." -ForegroundColor Yellow
        Write-Host "Please try installing via Android Studio SDK Manager instead." -ForegroundColor Yellow
    }
    
} catch {
    Write-Host ""
    Write-Host "Error during download/installation: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Alternative: Install via Android Studio" -ForegroundColor Yellow
    Write-Host "1. Open Android Studio" -ForegroundColor White
    Write-Host "2. Go to: Tools → SDK Manager" -ForegroundColor White
    Write-Host "3. Click 'SDK Tools' tab" -ForegroundColor White
    Write-Host "4. Check 'Android SDK Command-line Tools (latest)'" -ForegroundColor White
    Write-Host "5. Click 'Apply'" -ForegroundColor White
    Write-Host ""
}


