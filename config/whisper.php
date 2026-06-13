<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Whisper Model
    |--------------------------------------------------------------------------
    |
    | The Whisper model to use for transcription.
    | Options: tiny, tiny.en, base, base.en, small, small.en, medium, large
    | Recommended: tiny.en (fast, good accuracy for English)
    |
    */
    'model' => env('WHISPER_MODEL', 'tiny.en'),

    /*
    |--------------------------------------------------------------------------
    | Models Storage Path
    |--------------------------------------------------------------------------
    |
    | Directory where Whisper models will be stored and loaded from.
    |
    */
    'models_path' => storage_path('app/whisper-models'),

    /*
    |--------------------------------------------------------------------------
    | Processing Threads
    |--------------------------------------------------------------------------
    |
    | Number of CPU threads to use for transcription processing.
    | More threads = faster processing (if CPU supports it)
    |
    */
    'threads' => env('WHISPER_THREADS', 4),

    /*
    |--------------------------------------------------------------------------
    | Language
    |--------------------------------------------------------------------------
    |
    | Default language for transcription (ISO 639-1 code).
    | Set to 'auto' for automatic detection.
    |
    */
    'language' => env('WHISPER_LANGUAGE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Audio Constraints
    |--------------------------------------------------------------------------
    |
    | Validation constraints for uploaded audio files.
    |
    */
    'max_duration' => 300, // 5 minutes in seconds
    'max_file_size' => 10240, // 10MB in KB

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Enable queue processing for transcription jobs.
    | Recommended: true (prevents blocking requests)
    |
    */
    'queue_enabled' => env('WHISPER_QUEUE_ENABLED', false), // Start with sync for testing

    /*
    |--------------------------------------------------------------------------
    | Storage Paths
    |--------------------------------------------------------------------------
    |
    | Paths for storing audio files and transcriptions.
    |
    */
    'audio_storage_path' => 'audio-recordings',
    'temp_audio_path' => 'temp-audio',
];
