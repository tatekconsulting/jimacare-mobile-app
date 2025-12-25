# Install Android Command-line Tools Script
# This script downloads and installs Android SDK Command-line Tools

Write-Host "Android Command-line Tools Installer" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

$androidSdkPath = "$env:LOCALAPPDATA\Android\sdk"
$cmdlineToolsPath = "$androidSdkPath\cmdline-tools\latest"

# Check if already installed
if (Test-Path "$cmdlineToolsPath\bin\sdkmanager.bat") {
    Write-Host "Command-line tools already installed at: $cmdlineToolsPath" -ForegroundColor Green
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
Write-Host "Choose installation method:" -ForegroundColor Yellow
Write-Host "1. Download and install automatically (recommended)" -ForegroundColor White
Write-Host "2. Manual installation instructions" -ForegroundColor White
Write-Host ""
$choice = Read-Host "Enter choice (1 or 2)"

if ($choice -eq "1") {
    Write-Host ""
    Write-Host "Downloading Android Command-line Tools..." -ForegroundColor Yellow
    Write-Host "This may take a few minutes..." -ForegroundColor Gray
    Write-Host ""
    
    # Download URL for Windows command-line tools
    $downloadUrl = "https://dl.google.com/android/repository/commandlinetools-win-11076708_latest.zip"
    $zipFile = "$env:TEMP\android-cmdline-tools.zip"
    
    try {
        # Download the file
        Write-Host "Downloading from: $downloadUrl" -ForegroundColor Gray
        Invoke-WebRequest -Uri $downloadUrl -OutFile $zipFile -UseBasicParsing
        
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
        
        # Move to correct location (cmdline-tools contains a 'cmdline-tools' folder)
        $extractedFolder = Get-ChildItem $tempExtractPath | Select-Object -First 1
        if ($extractedFolder.Name -eq "cmdline-tools") {
            # If it's already named cmdline-tools, move its contents
            $sourcePath = $extractedFolder.FullName
        } else {
            $sourcePath = $tempExtractPath
        }
        
        # Move to latest folder
        if (Test-Path $cmdlineToolsPath) {
            Remove-Item $cmdlineToolsPath -Recurse -Force
        }
        New-Item -ItemType Directory -Path $cmdlineToolsPath -Force | Out-Null
        
        # Copy all files from extracted folder to latest
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
            Write-Host "Next steps:" -ForegroundColor Yellow
            Write-Host "1. Restart your terminal/PowerShell window" -ForegroundColor White
            Write-Host "2. Run: flutter doctor --android-licenses" -ForegroundColor Cyan
            Write-Host "3. Press 'y' to accept all licenses" -ForegroundColor White
            Write-Host "4. Verify: flutter doctor -v" -ForegroundColor Cyan
        } else {
            Write-Host "Warning: Installation may have failed. Please try manual installation." -ForegroundColor Yellow
        }
        
    } catch {
        Write-Host ""
        Write-Host "Error during download/installation: $_" -ForegroundColor Red
        Write-Host ""
        Write-Host "Please try manual installation (option 2)" -ForegroundColor Yellow
    }
    
} else {
    Write-Host ""
    Write-Host "Manual Installation Instructions:" -ForegroundColor Yellow
    Write-Host "=================================" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "1. Open Android Studio" -ForegroundColor White
    Write-Host "2. Go to: Tools → SDK Manager" -ForegroundColor White
    Write-Host "3. Click on the 'SDK Tools' tab" -ForegroundColor White
    Write-Host "4. Check 'Android SDK Command-line Tools (latest)'" -ForegroundColor White
    Write-Host "5. Click 'Apply' and wait for installation" -ForegroundColor White
    Write-Host ""
    Write-Host "OR" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "1. Download from: https://developer.android.com/studio#command-line-tools-only" -ForegroundColor White
    Write-Host "2. Extract the ZIP file" -ForegroundColor White
    Write-Host "3. Copy the 'cmdline-tools' folder to: $androidSdkPath" -ForegroundColor White
    Write-Host "4. Rename it to 'latest' so the path is: $cmdlineToolsPath" -ForegroundColor White
    Write-Host ""
    Write-Host "After installation, restart your terminal and run:" -ForegroundColor Yellow
    Write-Host "  flutter doctor --android-licenses" -ForegroundColor Cyan
}

Write-Host ""


