<?php

/**
 * Fix FFI configuration in php.ini
 * This script will help you enable FFI properly
 */

echo "FFI Configuration Fixer\n";
echo "======================\n\n";

// Get php.ini location
$phpIniPath = php_ini_loaded_file();

if (!$phpIniPath) {
    echo "❌ Could not find php.ini file\n";
    exit(1);
}

echo "📁 PHP.ini location: $phpIniPath\n\n";

// Check if file is writable
if (!is_writable($phpIniPath)) {
    echo "❌ php.ini is not writable. Please run as administrator.\n";
    echo "\nTo fix manually:\n";
    echo "1. Open: $phpIniPath\n";
    echo "2. Find line with: ;ffi.enable=preload\n";
    echo "3. Remove the line above it that says: \"true\"\n";
    echo "4. Change ;ffi.enable=preload to: ffi.enable=preload\n";
    echo "5. Save and restart your web server\n";
    exit(1);
}

echo "✅ php.ini is writable\n\n";

// Read php.ini
$content = file_get_contents($phpIniPath);

// Check current FFI status
echo "Current FFI configuration:\n";
if (preg_match('/^ffi\.enable\s*=\s*(.+)$/m', $content, $matches)) {
    echo "  ffi.enable = " . trim($matches[1]) . " (enabled)\n";
} elseif (preg_match('/^;ffi\.enable\s*=\s*(.+)$/m', $content, $matches)) {
    echo "  ;ffi.enable = " . trim($matches[1]) . " (commented out/disabled)\n";
} else {
    echo "  Not found\n";
}

echo "\n";

// Ask user if they want to fix
echo "Do you want to fix the FFI configuration? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$answer = trim(strtolower($line));
fclose($handle);

if ($answer !== 'yes' && $answer !== 'y') {
    echo "Aborted.\n";
    exit(0);
}

// Backup php.ini
$backupPath = $phpIniPath . '.backup.' . date('YmdHis');
if (!copy($phpIniPath, $backupPath)) {
    echo "❌ Could not create backup\n";
    exit(1);
}

echo "✅ Backup created: $backupPath\n";

// Fix the configuration
// 1. Remove the stray "true" line
$content = preg_replace('/^\s*"true"\s*$/m', '', $content);

// 2. Enable ffi.enable=preload
$content = preg_replace('/^;ffi\.enable\s*=\s*preload/m', 'ffi.enable=preload', $content);

// 3. If ffi.enable doesn't exist, add it
if (!preg_match('/^ffi\.enable/m', $content)) {
    // Find the [ffi] section
    if (preg_match('/^\[ffi\]/m', $content)) {
        $content = preg_replace('/^\[ffi\]\s*$/m', "[ffi]\nffi.enable=preload", $content);
    } else {
        // Add [ffi] section at the end
        $content .= "\n\n[ffi]\nffi.enable=preload\n";
    }
}

// Write back
if (file_put_contents($phpIniPath, $content) === false) {
    echo "❌ Could not write to php.ini\n";
    exit(1);
}

echo "✅ php.ini updated successfully\n\n";

echo "⚠️  IMPORTANT: You must restart your web server for changes to take effect!\n\n";

echo "Restart commands:\n";
echo "  Laragon: Click 'Stop All' then 'Start All'\n";
echo "  XAMPP: Restart Apache from control panel\n";
echo "  Command line: php artisan serve (restart the command)\n\n";

echo "After restarting, verify FFI is enabled:\n";
echo "  php -r \"var_dump(extension_loaded('ffi'));\"\n";
echo "  (Should output: bool(true))\n";
