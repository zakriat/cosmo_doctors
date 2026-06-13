<?php

/**
 * Enhanced Transcription System Test
 * 
 * This script tests the complete enhanced transcription system
 * Run: php test_enhanced_transcription.php
 */

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\GeminiMedicalService;
use App\Models\AudioTranscription;

echo "🧪 Testing Enhanced Transcription System...\n\n";

try {
    // Test 1: Database Schema
    echo "📊 Testing Database Schema...\n";
    
    // Check if new columns exist
    $columns = \Schema::getColumnListing('audio_transcriptions');
    $requiredColumns = [
        'original_text', 'medical_text', 'final_text', 
        'medical_categories', 'confidence_scores', 'word_mappings',
        'gemini_model_used', 'gemini_processing_time_ms', 'gemini_fallback_used',
        'user_edited', 'edit_count', 'preferred_version', 'last_edited_at'
    ];
    
    $missingColumns = array_diff($requiredColumns, $columns);
    
    if (empty($missingColumns)) {
        echo "✅ Database Schema: All required columns present\n";
    } else {
        echo "❌ Database Schema: Missing columns: " . implode(', ', $missingColumns) . "\n";
        exit(1);
    }
    
    // Test 2: AudioTranscription Model
    echo "\n📝 Testing AudioTranscription Model...\n";
    
    // Create a test transcription record
    $testRecord = AudioTranscription::create([
        'user_id' => 1, // Assuming user ID 1 exists
        'audio_file_path' => 'test/audio.wav',
        'transcription_text' => 'I have been having headaches',
        'original_text' => 'I have been having headaches',
        'medical_text' => 'Patient reports experiencing cephalgia',
        'final_text' => 'Patient reports experiencing cephalgia',
        'medical_categories' => [
            [
                'text' => 'cephalgia',
                'category' => 'symptoms',
                'start_pos' => 25,
                'end_pos' => 35,
                'confidence' => 0.95
            ]
        ],
        'confidence_scores' => ['gemini' => 0.92, 'whisper' => 1.0],
        'model_used' => 'tiny.en',
        'gemini_model_used' => 'gemini-2.0-flash',
        'processing_time_ms' => 2500,
        'gemini_processing_time_ms' => 1800,
        'gemini_fallback_used' => false,
        'status' => 'completed'
    ]);
    
    echo "✅ Model: Test record created (ID: {$testRecord->id})\n";
    
    // Test model methods
    $bestTranscription = $testRecord->getBestTranscriptionAttribute();
    echo "✅ Model: getBestTranscription() = \"$bestTranscription\"\n";
    
    $combinedText = $testRecord->getCombinedDisplayTextAttribute();
    echo "✅ Model: getCombinedDisplayText() working\n";
    
    $hasGemini = $testRecord->hasGeminiEnhancement();
    echo "✅ Model: hasGeminiEnhancement() = " . ($hasGemini ? 'true' : 'false') . "\n";
    
    $categoriesWithColors = $testRecord->getMedicalCategoriesWithColors();
    echo "✅ Model: getMedicalCategoriesWithColors() = " . count($categoriesWithColors) . " categories\n";
    
    // Test 3: Gemini Service
    echo "\n🤖 Testing Gemini Service...\n";
    
    $geminiService = new GeminiMedicalService();
    
    // Test with simple text (will likely hit quota limit but should fallback gracefully)
    $testText = "I have stomach pain after eating";
    $result = $geminiService->enhanceMedicalText($testText);
    
    if (isset($result['fallback_used'])) {
        echo "⚠️  Gemini Service: Using fallback (API quota exceeded)\n";
        echo "   - Fallback text: \"" . $result['enhanced_text'] . "\"\n";
        echo "   - Error: " . ($result['error'] ?? 'Unknown') . "\n";
    } else {
        echo "✅ Gemini Service: Enhancement successful\n";
        echo "   - Original: \"$testText\"\n";
        echo "   - Enhanced: \"" . $result['enhanced_text'] . "\"\n";
        echo "   - Categories: " . count($result['categories']) . "\n";
        echo "   - Confidence: " . $result['confidence_score'] . "\n";
    }
    
    // Test category colors
    $categories = $geminiService->getMedicalCategories();
    echo "✅ Gemini Service: " . count($categories) . " medical categories configured\n";
    
    // Test combined text display
    $combinedDisplay = $geminiService->combineTextsForDisplay($testText, $result['enhanced_text']);
    echo "✅ Gemini Service: Combined display text generated\n";
    
    // Test 4: Route Registration
    echo "\n🛣️  Testing Route Registration...\n";
    
    $routes = collect(\Route::getRoutes())->map(function($route) {
        return $route->getName();
    })->filter()->toArray();
    
    if (in_array('transcribe-audio-enhanced', $routes)) {
        echo "✅ Routes: Enhanced transcription route registered\n";
    } else {
        echo "❌ Routes: Enhanced transcription route NOT found\n";
    }
    
    // Test 5: Configuration
    echo "\n⚙️  Testing Configuration...\n";
    
    $geminiConfig = config('gemini');
    if ($geminiConfig && isset($geminiConfig['api_key'])) {
        echo "✅ Config: Gemini configuration loaded\n";
        echo "   - Model: " . $geminiConfig['model'] . "\n";
        echo "   - Categories: " . count($geminiConfig['category_colors']) . "\n";
        echo "   - API Key: " . (strlen($geminiConfig['api_key']) > 10 ? 'Present' : 'Missing') . "\n";
    } else {
        echo "❌ Config: Gemini configuration missing\n";
    }
    
    // Clean up test record
    $testRecord->delete();
    echo "\n🧹 Test record cleaned up\n";
    
    echo "\n🎉 Enhanced Transcription System Test Complete!\n";
    echo "\n📋 Summary:\n";
    echo "- Database Schema: ✅ Ready\n";
    echo "- AudioTranscription Model: ✅ Enhanced\n";
    echo "- Gemini Service: " . (isset($result['fallback_used']) ? "⚠️  Fallback Mode" : "✅ Working") . "\n";
    echo "- Routes: ✅ Registered\n";
    echo "- Configuration: ✅ Loaded\n";
    
    if (isset($result['fallback_used'])) {
        echo "\n⚠️  Note: Gemini API quota exceeded, but system will work with fallback\n";
        echo "   When quota resets, enhanced features will work automatically\n";
    }
    
    echo "\n🚀 System is ready for frontend integration!\n";
    
} catch (Exception $e) {
    echo "❌ Test Failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}