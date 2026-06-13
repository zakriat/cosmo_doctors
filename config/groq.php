<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Groq API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Groq's fast speech-to-text API
    | Get your API key from: https://console.groq.com/keys
    |
    */

    'api_key' => env('GROQ_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Model Selection
    |--------------------------------------------------------------------------
    |
    | Available models:
    | - whisper-large-v3-turbo: Fastest, best price/performance, 12% WER
    | - whisper-large-v3: Most accurate, 10.3% WER, supports translation
    |
    */

    'model' => env('GROQ_MODEL', 'whisper-large-v3-turbo'),

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */

    'cache_enabled' => env('GROQ_CACHE_ENABLED', true),
    'cache_ttl' => env('GROQ_CACHE_TTL', 3600), // 1 hour

    /*
    |--------------------------------------------------------------------------
    | Audio Storage
    |--------------------------------------------------------------------------
    */

    'audio_storage_path' => env('GROQ_AUDIO_STORAGE_PATH', 'audio_transcriptions'),
    'temp_audio_path' => env('GROQ_TEMP_AUDIO_PATH', 'temp/audio'),

    /*
    |--------------------------------------------------------------------------
    | Medical Categories for Entity Extraction
    |--------------------------------------------------------------------------
    */

    'category_colors' => [
        'emergency' => '#dc3545',      // Red - Critical
        'symptoms' => '#fd7e14',       // Orange - High priority
        'conditions' => '#0d6efd',     // Blue - High priority
        'medications' => '#198754',    // Green - High priority
        'vitals' => '#6f42c1',         // Purple - Medium priority
        'anatomy' => '#20c997',        // Teal - Medium priority
        'procedures' => '#0dcaf0',     // Cyan - Medium priority
        'severity' => '#ffc107',       // Yellow - Low priority
        'duration' => '#6c757d',       // Gray - Low priority
        'allergies' => '#e83e8c',      // Pink - Critical
        'family_history' => '#795548', // Brown - Low priority
        'social_history' => '#607d8b', // Blue-gray - Low priority
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Limits
    |--------------------------------------------------------------------------
    */

    'max_file_size' => 25 * 1024 * 1024, // 25MB (free tier)
    'min_duration' => 0.01, // seconds
    'max_duration' => 3600, // 1 hour
];
