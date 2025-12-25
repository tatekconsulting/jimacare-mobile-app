# Enable Developer Mode on Windows

## Why You Need This

Flutter requires symlink support to build apps on Windows. Developer Mode enables this feature.

## Steps to Enable Developer Mode

1. **Settings should have opened automatically** - if not, run:
   ```powershell
   start ms-settings:developers
   ```

2. **In the Settings window:**
   - Look for "Developer Mode" section
   - Toggle "Developer Mode" to **ON**
   - You may see a warning - click "Yes" to confirm

3. **Alternative method (if Settings doesn't work):**
   - Press `Win + R`
   - Type: `gpedit.msc` (if you have Group Policy Editor)
   - OR use Registry Editor (advanced)

## After Enabling

1. **Close and reopen your terminal/PowerShell**

2. **Try building again:**
   ```bash
   flutter build apk --release
   ```

## Quick Registry Method (Advanced)

If Settings doesn't work, you can enable it via Registry:

1. Press `Win + R`
2. Type `regedit` and press Enter
3. Navigate to: `HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\AppModelUnlock`
4. Create a DWORD (32-bit) value named `AllowDevelopmentWithoutDevLicense`
5. Set its value to `1`
6. Restart your computer

## Note

After enabling Developer Mode, you may need to restart your terminal or computer for changes to take effect.

