<?php

echo "Testing FFmpeg paths...\n\n";

$ffmpegPaths = [
    'ffmpeg', // System PATH
    'C:\\ffmpeg\\bin\\ffmpeg.exe', // Common Windows installation
    'C:\\laragon\\bin\\ffmpeg\\bin\\ffmpeg.exe', // Laragon installation
    '/usr/bin/ffmpeg', // Linux
    '/usr/local/bin/ffmpeg' // macOS
];

foreach ($ffmpegPaths as $path) {
    echo "Testing: $path\n";
    
    $output = null;
    $returnVar = null;
    exec("\"$path\" -version 2>&1", $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "✅ FOUND: $path\n";
        echo "Version: " . (isset($output[0]) ? $output[0] : 'Unknown') . "\n";
        break;
    } else {
        echo "❌ Not found\n";
    }
    echo "\n";
}

if ($returnVar !== 0) {
    echo "❌ FFmpeg not found in any common location\n";
    echo "Please check your FFmpeg installation\n";
}