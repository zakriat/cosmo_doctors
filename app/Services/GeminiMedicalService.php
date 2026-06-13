<?php

namespace App\Services;

use Gemini;
use Gemini\Data\Content;
use Gemini\Data\GenerationConfig;
use Gemini\Data\Schema;
use Gemini\Enums\DataType;
use Gemini\Enums\ResponseMimeType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiMedicalService
{
    private $client;
    private $config;

    public function __construct()
    {
        $this->config = config('gemini');
        
        if (!$this->config['api_key']) {
            throw new \Exception('Gemini API key not configured');
        }

        $this->client = Gemini::client($this->config['api_key']);
    }

    /**
     * Enhance medical text with proper terminology and categorization
     */
    public function enhanceMedicalText(string $originalText): array
    {
        // Validate input
        if (strlen($originalText) < $this->config['min_text_length']) {
            return $this->createFallbackResponse($originalText, 'Text too short for enhancement');
        }

        if (strlen($originalText) > $this->config['max_text_length']) {
            return $this->createFallbackResponse($originalText, 'Text too long for enhancement');
        }

        // Check cache first
        $cacheKey = 'gemini_medical_' . md5($originalText);
        if ($this->config['cache_enabled'] && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $startTime = microtime(true);

        try {
            $result = $this->processWithGemini($originalText);
            
            // Validate response
            if (!$this->validateResponse($result)) {
                return $this->createFallbackResponse($originalText, 'Invalid AI response');
            }

            // Add processing metadata
            $result['processing_time_ms'] = round((microtime(true) - $startTime) * 1000);
            $result['model_used'] = $this->config['model'];
            $result['timestamp'] = now()->toISOString();

            // Cache successful result
            if ($this->config['cache_enabled']) {
                Cache::put($cacheKey, $result, $this->config['cache_ttl']);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Gemini Medical Enhancement Error', [
                'error' => $e->getMessage(),
                'original_text' => $originalText,
                'processing_time_ms' => round((microtime(true) - $startTime) * 1000)
            ]);

            return $this->createFallbackResponse($originalText, $e->getMessage());
        }
    }

    /**
     * Process text with Gemini API
     */
    private function processWithGemini(string $originalText): array
    {
        $prompt = str_replace('{original_text}', $originalText, $this->config['enhancement_prompt_template']);

        $response = $this->client->generativeModel(model: $this->config['model'])
            ->withSystemInstruction(Content::parse($this->config['medical_system_prompt']))
            ->withGenerationConfig(
                new GenerationConfig(
                    responseMimeType: ResponseMimeType::APPLICATION_JSON,
                    responseSchema: new Schema(
                        type: DataType::OBJECT,
                        properties: [
                            'enhanced_text' => new Schema(
                                type: DataType::STRING,
                                description: 'The medically enhanced version of the original text'
                            ),
                            'categories' => new Schema(
                                type: DataType::ARRAY,
                                description: 'Array of categorized medical terms',
                                items: new Schema(
                                    type: DataType::OBJECT,
                                    properties: [
                                        'text' => new Schema(type: DataType::STRING),
                                        'category' => new Schema(type: DataType::STRING),
                                        'start_pos' => new Schema(type: DataType::INTEGER),
                                        'end_pos' => new Schema(type: DataType::INTEGER),
                                        'confidence' => new Schema(type: DataType::NUMBER),
                                        'original_phrase' => new Schema(type: DataType::STRING)
                                    ],
                                    required: ['text', 'category', 'confidence']
                                )
                            ),
                            'confidence_score' => new Schema(
                                type: DataType::NUMBER,
                                description: 'Overall confidence score between 0 and 1'
                            ),
                            'original_preserved' => new Schema(
                                type: DataType::BOOLEAN,
                                description: 'Whether the original meaning was preserved'
                            )
                        ],
                        required: ['enhanced_text', 'categories', 'confidence_score']
                    ),
                    maxOutputTokens: $this->config['max_tokens'],
                    temperature: $this->config['temperature']
                )
            )
            ->generateContent($prompt);

        return $response->json();
    }

    /**
     * Validate Gemini response
     */
    private function validateResponse(array $response): bool
    {
        // Check required fields
        if (!isset($response['enhanced_text']) || !isset($response['categories']) || !isset($response['confidence_score'])) {
            return false;
        }

        // Check confidence score
        if ($response['confidence_score'] < $this->config['min_confidence_score']) {
            Log::warning('Low confidence score from Gemini', [
                'confidence' => $response['confidence_score'],
                'threshold' => $this->config['min_confidence_score']
            ]);
        }

        // Validate categories structure
        if (!is_array($response['categories'])) {
            return false;
        }

        foreach ($response['categories'] as $category) {
            if (!isset($category['text']) || !isset($category['category']) || !isset($category['confidence'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create fallback response when Gemini fails
     */
    private function createFallbackResponse(string $originalText, string $error = null): array
    {
        return [
            'enhanced_text' => $originalText,
            'categories' => [],
            'confidence_score' => 0.0,
            'original_preserved' => true,
            'fallback_used' => true,
            'error' => $error,
            'processing_time_ms' => 0,
            'model_used' => 'fallback',
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Get available medical categories with colors
     */
    public function getMedicalCategories(): array
    {
        return $this->config['category_colors'];
    }

    /**
     * Test Gemini connection and functionality
     */
    public function testConnection(): array
    {
        try {
            $testText = "I have been experiencing severe headaches for the past three days.";
            $result = $this->enhanceMedicalText($testText);
            
            return [
                'success' => true,
                'test_input' => $testText,
                'result' => $result,
                'api_connected' => !isset($result['fallback_used']),
                'categories_detected' => count($result['categories']),
                'confidence_score' => $result['confidence_score']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'api_connected' => false
            ];
        }
    }

    /**
     * Combine original and medical text for textarea display
     */
    public function combineTextsForDisplay(string $originalText, string $medicalText): string
    {
        return "Original: \"{$originalText}\"\n\nMedical: \"{$medicalText}\"";
    }

    /**
     * Extract plain text from categorized medical text
     */
    public function extractPlainText(array $categories, string $enhancedText): string
    {
        // This method can be used to create a clean version without HTML formatting
        return strip_tags($enhancedText);
    }
}