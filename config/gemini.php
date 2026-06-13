<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Configuration
    |--------------------------------------------------------------------------
    */
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
    'timeout' => env('GEMINI_TIMEOUT', 30),
    'max_tokens' => env('GEMINI_MAX_TOKENS', 1000),
    'temperature' => env('GEMINI_TEMPERATURE', 0.3),

    /*
    |--------------------------------------------------------------------------
    | Medical Enhancement Prompts
    |--------------------------------------------------------------------------
    */
    'medical_system_prompt' => 'You are a medical AI assistant specializing in converting casual patient language into proper medical terminology. Your task is to:

1. Convert everyday language to appropriate medical terms
2. Preserve the original meaning and context
3. Maintain patient-friendly tone while using medical accuracy
4. Categorize each medical term into specific categories
5. Provide confidence scores for your interpretations

Categories to use:
- symptoms: Physical complaints, pain, discomfort, sensations
- medical_history: Past conditions, surgeries, chronic diseases
- medications: Drugs, prescriptions, treatments, dosages
- personal_info: Age, lifestyle, family history, demographics
- tests_treatments: Procedures, examinations, diagnostics
- allergies: Allergic reactions, intolerances, sensitivities
- urgent: Critical symptoms requiring immediate attention

Always maintain medical accuracy while being respectful of patient language.',

    'enhancement_prompt_template' => 'Original patient statement: "{original_text}"

Please enhance this text with proper medical terminology and provide detailed categorization. Return a JSON response with:
1. enhanced_text: The medically enhanced version
2. categories: Array of medical terms with their positions and categories
3. confidence_score: Overall confidence in the enhancement (0-1)
4. original_preserved: Whether original meaning was preserved

Focus on accuracy and clarity while maintaining the patient\'s original intent.',

    /*
    |--------------------------------------------------------------------------
    | Medical Category Colors
    |--------------------------------------------------------------------------
    */
    'category_colors' => [
        'symptoms' => '#ff6b6b',           // Red - for symptoms and pain
        'medical_history' => '#51cf66',    // Green - for medical history
        'medications' => '#ffd43b',        // Yellow - for medications
        'personal_info' => '#339af0',      // Blue - for personal information
        'tests_treatments' => '#9775fa',   // Purple - for tests and treatments
        'allergies' => '#ff922b',          // Orange - for allergies
        'urgent' => '#c92a2a'              // Dark red - for urgent items
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Settings
    |--------------------------------------------------------------------------
    */
    'retry_attempts' => 3,
    'retry_delay' => 1000, // milliseconds
    'cache_enabled' => true,
    'cache_ttl' => 3600, // 1 hour

    /*
    |--------------------------------------------------------------------------
    | Quality Thresholds
    |--------------------------------------------------------------------------
    */
    'min_confidence_score' => 0.7,
    'max_text_length' => 2000,
    'min_text_length' => 10,

    /*
    |--------------------------------------------------------------------------
    | Fallback Settings
    |--------------------------------------------------------------------------
    */
    'fallback_enabled' => true,
    'fallback_message' => 'AI enhancement temporarily unavailable. Using original transcription.',
];