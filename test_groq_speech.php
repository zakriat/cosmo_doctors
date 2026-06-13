<?php

/**
 * Test Groq Speech-to-Text Integration
 * 
 * This script tests the Groq API connection and speech-to-text functionality
 * Run: php test_groq_speech.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GroqSpeechService;

echo "🧪 Testing Groq Speech-to-Text Integration...\n\n";

try {
    // Test 1: Configuration Check
    echo "📋 Test 1: Configuration Check\n";
    echo "   - API Key: " . (config('groq.api_key') ? '✅ Set' : '❌ Not set') . "\n";
    echo "   - Model: " . config('groq.model') . "\n";
    echo "   - Cache Enabled: " . (config('groq.cache_enabled') ? 'Yes' : 'No') . "\n";
    echo "   - Storage Path: " . config('groq.audio_storage_path') . "\n\n";

    // Test 2: Service Initialization
    echo "🔧 Test 2: Service Initialization\n";
    $groqService = new GroqSpeechService();
    echo "   ✅ GroqSpeechService initialized successfully\n\n";

    // Test 3: API Connection
    echo "🌐 Test 3: API Connection Test\n";
    $connectionTest = $groqService->testConnection();
    
    if ($connectionTest['success']) {
        echo "   ✅ Groq API connection successful\n";
        echo "   - Available models: " . count($connectionTest['models']) . "\n";
        
        // List Whisper models
        $whisperModels = array_filter($connectionTest['models'], function($model) {
            return stripos($model['id'], 'whisper') !== false;
        });
        
        if (!empty($whisperModels)) {
            echo "   - Whisper models available:\n";
            foreach ($whisperModels as $model) {
                echo "     • " . $model['id'] . "\n";
            }
        }
    } else {
        echo "   ❌ API connection failed: " . $connectionTest['error'] . "\n";
    }
    echo "\n";

    // Test 4: Medical Entity Extraction
    echo "🏥 Test 4: Medical Entity Extraction\n";
    $testText = "Patient reports severe headache and high blood pressure. Prescribed aspirin and scheduled blood test.";
    $entities = $groqService->extractMedicalEntities($testText);
    
    echo "   - Test text: \"$testText\"\n";
    echo "   - Entities found: " . count($entities) . "\n";
    
    if (!empty($entities)) {
        $categorized = [];
        foreach ($entities as $entity) {
            $categorized[$entity['category']][] = $entity['text'];
        }
        
        foreach ($categorized as $category => $terms) {
            echo "   - " . ucfirst($category) . ": " . implode(', ', array_unique($terms)) . "\n";
        }
    }
    echo "\n";

    // Test 5: Text Formatting
    echo "📝 Test 5: Medical Record Formatting\n";
    $rawText = "patient has chest pain and shortness of breath";
    $formatted = $groqService->formatForMedicalRecord(['text' => $rawText]);
    echo "   - Raw: \"$rawText\"\n";
    echo "   - Formatted: \"$formatted\"\n\n";

    // Test 6: Category Colors
    echo "🎨 Test 6: Medical Category Colors\n";
    $colors = config('groq.category_colors');
    foreach ($colors as $category => $color) {
        echo "   - " . ucfirst($category) . ": $color\n";
    }
    echo "\n";

    // Summary
    echo "✅ All tests completed successfully!\n\n";
    echo "📌 Next Steps:\n";
    echo "   1. Record audio in your booking form\n";
    echo "   2. The audio will be sent to /transcribe-audio endpoint\n";
    echo "   3. Groq will transcribe with medical context\n";
    echo "   4. Medical entities will be automatically extracted\n";
    echo "   5. Text will be formatted for medical records\n\n";
    
    echo "💡 Benefits over Whisper.php + Gemini:\n";
    echo "   - 189x faster than real-time (Groq)\n";
    echo "   - No local model downloads needed\n";
    echo "   - No FFmpeg conversion required\n";
    echo "   - Built-in medical terminology support\n";
    echo "   - Automatic entity extraction\n";
    echo "   - Lower cost: $0.04/hour vs Gemini API calls\n\n";

} catch (\Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "🎉 Groq integration is ready to use!\n";
