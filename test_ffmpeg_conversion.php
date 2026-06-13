<?php

echo "Testing FFmpeg conversion...\n\n";

// Test FFmpeg availability
$ffmpegPaths = [
    'ffmpeg',
    'C:\\ffmpeg\\bin\\ffmpeg.exe',
    'C:\\laragon\\bin\\ffmpeg\\bin\\ffmpeg.exe'
];

$workingPath = null;
foreach ($ffmpegPaths as $path) {
    $output = null;
    $returnVar = null;
    exec("\"$path\" -version 2>&1", $output, $returnVar);
    
    if ($returnVar === 0) {
        $workingPath = $path;
        echo "✅ FFmpeg found at: $path\n";
        break;
    }
}

if (!$workingPath) {
    echo "❌ FFmpeg not found in any location\n";
    exit(1);
}

// Test a simple conversion command (without actual files)
echo "\nTesting conversion command syntax...\n";

$testCommand = sprintf(
    '"%s" -f lavfi -i "sine=frequency=1000:duration=1" -acodec pcm_s16le -ar 16000 -ac 1 test_output.wav 2>&1',
    $workingPath
);

echo "Command: $testCommand\n\n";

$output = null;
$returnVar = null;
exec($testCommand, $output, $returnVar);

echo "Return code: $returnVar\n";
echo "Output:\n" . implode("\n", $output) . "\n";

if ($returnVar === 0) {
    echo "\n✅ FFmpeg conversion test successful!\n";
    if (file_exists('test_output.wav')) {
        unlink('test_output.wav');
        echo "✅ Test file created and cleaned up\n";
    }
} else {
    echo "\n❌ FFmpeg conversion test failed\n";
}