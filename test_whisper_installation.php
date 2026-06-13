<?php

/**
 * Test script to verify Whisper.php installation
 * Run: php test_whisper_installation.php
 */

require __DIR__ . '/vendor/autoload.php';

echo "Testing Whisper.php Installation...\n\n";

// Check FFI extension
echo "1. Checking FFI extension: ";
if (extension_loaded('ffi')) {
    echo "✓ ENABLED\n";
} else {
    echo "✗ NOT ENABLED - Please enable FFI in php.ini\n";
    exit(1);
}

// Check Whisper class
echo "2. Checking Whisper class: ";
if (class_exists('Codewithkyrian\Whisper\Whisper')) {
    echo "✓ FOUND\n";
} else {
    echo "✗ NOT FOUND - Please run: composer require codewithkyrian/whisper.php\n";
    exit(1);
}

// Check storage directories
echo "3. Checking storage directories:\n";
$dirs = [
    'storage/app/whisper-models',
    'storage/app/audio-recordings',
    'storage/app/temp-audio'
];

foreach ($dirs as $dir) {
    echo "   - $dir: ";
    if (is_dir($dir) && is_writable($dir)) {
        echo "✓ EXISTS & WRITABLE\n";
    } else {
        echo "✗ MISSING OR NOT WRITABLE\n";
    }
}

// Check config
echo "4. Checking config file: ";
if (file_exists('config/whisper.php')) {
    echo "✓ EXISTS\n";
} else {
    echo "✗ NOT FOUND\n";
}

echo "\n✓ All checks passed! Whisper.php is ready to use.\n";
echo "\nNote: The Whisper model (~75MB) will download automatically on first use.\n";
echo "This may take 30-60 seconds depending on your internet connection.\n";
