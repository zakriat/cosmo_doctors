# Gemini PHP Client - Key Implementation Details

## Installation
```bash
composer require google-gemini-php/client
composer require guzzlehttp/guzzle
```

## Basic Usage for Medical Transcription
```php
use Gemini;
use Gemini\Data\Content;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;

$client = Gemini::client($apiKey);

// Medical text enhancement with system instructions
$response = $client->generativeModel(model: 'gemini-2.0-flash')
    ->withSystemInstruction(
        Content::parse('You are a medical AI assistant. Convert casual patient language into proper medical terminology while preserving the original meaning. Categorize medical information into: symptoms, medical_history, medications, personal_info, tests_treatments, allergies, urgent.')
    )
    ->generateContent($originalText);

$enhancedText = $response->text();
```

## Structured Output for Medical Categories
```php
use Gemini\Data\Schema;
use Gemini\Enums\DataType;

$response = $client->generativeModel(model: 'gemini-2.0-flash')
    ->withGenerationConfig(
        new GenerationConfig(
            responseMimeType: ResponseMimeType::APPLICATION_JSON,
            responseSchema: new Schema(
                type: DataType::OBJECT,
                properties: [
                    'enhanced_text' => new Schema(type: DataType::STRING),
                    'categories' => new Schema(
                        type: DataType::ARRAY,
                        items: new Schema(
                            type: DataType::OBJECT,
                            properties: [
                                'text' => new Schema(type: DataType::STRING),
                                'category' => new Schema(type: DataType::STRING),
                                'start_pos' => new Schema(type: DataType::INTEGER),
                                'end_pos' => new Schema(type: DataType::INTEGER),
                                'confidence' => new Schema(type: DataType::NUMBER)
                            ]
                        )
                    )
                ]
            )
        )
    )
    ->generateContent($prompt);

$result = $response->json();
```

## Key Features for Our Implementation
- **System Instructions**: Perfect for medical context
- **Structured Output**: JSON response for categorization
- **Error Handling**: Built-in exception handling
- **Timeout Configuration**: Configurable for medical processing
- **Testing Support**: Built-in fake client for testing