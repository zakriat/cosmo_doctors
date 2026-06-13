# Fix FFI Error - Quick Guide

## Error You're Getting

```
FFI API is restricted by "ffi.enable" configuration directive
```

## The Problem

Your `php.ini` file has:
1. A syntax error on line 1661 (stray `"true"`)
2. FFI is not properly enabled

## Quick Fix (Manual)

### Step 1: Open php.ini

**Location:** `C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.ini`

Open with Notepad or any text editor (as Administrator)

### Step 2: Find Line 1661

Press `Ctrl+G` and go to line 1661, you'll see:

```ini
; "preload" - enabled in CLI scripts and preloaded files (default)
; "false"   - always disabled
 "true"    - always enabled        ← DELETE THIS LINE
;ffi.enable=preload
```

### Step 3: Fix It

**Delete** the line with `"true"` and **uncomment** the ffi.enable line:

**BEFORE:**
```ini
; "preload" - enabled in CLI scripts and preloaded files (default)
; "false"   - always disabled
 "true"    - always enabled
;ffi.enable=preload
```

**AFTER:**
```ini
; "preload" - enabled in CLI scripts and preloaded files (default)
; "false"   - always disabled
ffi.enable=preload
```

### Step 4: Save the File

Save and close the editor.

### Step 5: Restart Laragon

1. Open Laragon
2. Click **"Stop All"**
3. Wait 5 seconds
4. Click **"Start All"**

### Step 6: Verify

Open terminal and run:

```bash
php -r "var_dump(extension_loaded('ffi'));"
```

**Expected output:**
```
bool(true)
```

If you see `bool(true)`, FFI is now enabled! ✅

## Alternative: Use the Fix Script

If you want to automate this:

```bash
php fix_ffi_config.php
```

Follow the prompts, then restart Laragon.

## After Fixing

Test the speech-to-text feature:

```bash
php test_whisper_installation.php
```

Should show:
```
✓ FFI extension: ENABLED
✓ Whisper class: FOUND
✓ All checks passed!
```

## Still Having Issues?

### Check if extension is loaded

```bash
php -m | findstr ffi
```

Should output: `ffi`

### Check php.ini is correct

```bash
php --ini
```

Should NOT show any syntax errors.

### Verify ffi.enable setting

```bash
php -r "echo ini_get('ffi.enable');"
```

Should output: `preload` or `1`

## Common Mistakes

❌ **Don't do this:**
```ini
ffi.enable=true    # Wrong!
```

✅ **Do this:**
```ini
ffi.enable=preload  # Correct!
```

## Why "preload"?

- `preload` = FFI works in web requests (what we need)
- `true` = FFI only works in CLI
- `false` = FFI disabled

## Need Help?

If you're still stuck:

1. Check the exact line in php.ini:
   ```bash
   Get-Content "C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.ini" | Select-String "ffi.enable"
   ```

2. Check for syntax errors:
   ```bash
   php --ini
   ```

3. Make sure you're editing the correct php.ini (the one that's loaded)

---

**Once fixed, the speech-to-text feature will work perfectly!** 🎉
