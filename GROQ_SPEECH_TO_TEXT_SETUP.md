# Groq Speech-to-Text Integration for Medical Records

## Overview

Your medical records booking form now uses **Groq's ultra-fast speech-to-text API** instead of Whisper.php + Gemini. This provides:

- **189x faster than real-time** transcription
- **No local setup** - pure API-based solution
- **Medical terminology** built into prompts
- **Automatic entity extraction** (symptoms, medications, vitals, etc.)
- **Lower cost** - $0.04/hour vs multiple Gemini API calls

## What Changed

### Before (Whisper.php + Gemini)
1. Audio recorded in browser
2. Uploaded to server
3. FFmpeg converts to WAV
4. Whisper.php processes locally (slow)
5. Gemini API enhances text (extra API call)
6. Results combined and returned

### After (Groq)
1. Audio recorded in browser
2. Uploaded to server
3. **Groq API transcribes instantly** (supports all formats)
4. Medical entities extracted automatically
5. Text formatted for medical records
6. Results returned

## Configuration

Your `.env` file now includes:

```env
# Groq API Configuration
GROQ_API_KEY=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
GROQ_MODEL=whisper-large-v3-turbo
GROQ_CACHE_ENABLED=true
GROQ_CACHE_TTL=3600
GROQ_AUDIO_STORAGE_PATH=audio_transcriptions
GROQ_TEMP_AUDIO_PATH=temp/audio
```

## Files Created/Modified

### New Files
- `app/Services/GroqSpeechService.php` - Main Groq integration service
- `config/groq.php` - Groq configuration
- `test_groq_speech.php` - Test script

### Modified Files
- `Modules/Frontend/Http/Controllers/ServiceController.php` - Updated transcription methods
- `.env` - Added Groq configuration
- `.env.example` - Added Groq configuration template

## Testing

Run the test script to verify everything works:

```bash
php test_groq_speech.php
```

This will test:
- ✅ Configuration
- ✅ Service initialization
- ✅ API connection
- ✅ Medical entity extraction
- ✅ Text formatting
- ✅ Category colors

## API Endpoints

### POST /transcribe-audio
Transcribes audio with medical context

**Request:**
```javascript
FormData {
  audio: File (wav, mp3, ogg, m4a, webm, flac, mpeg, mpga)
}
```

**Response:**
```json
{
  "success": true,
  "transcription": "Patient reports severe headache...",
  "original_text": "patient reports severe headache...",
  "audio_id": 123,
  "duration": 15.5,
  "processing_time": 0.85,
  "model_used": "whisper-large-v3-turbo",
  "medical_entities": [
    {
      "text": "headache",
      "category": "symptoms",
      "confidence": 0.85
    }
  ],
  "quality_metrics": {
    "avg_confidence": -0.097,
    "quality_score": 0.92
  },
  "category_colors": {
    "symptoms": "#ef4444",
    "medications": "#10b981"
  }
}
```

### POST /transcribe-audio-enhanced
Same as `/transcribe-audio` (kept for backward compatibility)

## Medical Entity Categories

The system automatically detects and categorizes:

| Category | Color | Examples |
|----------|-------|----------|
| **Symptoms** | Red (#ef4444) | pain, fever, cough, nausea, dizzy |
| **Conditions** | Orange (#f59e0b) | diabetes, hypertension, asthma |
| **Medications** | Green (#10b981) | aspirin, ibuprofen, antibiotic |
| **Vitals** | Blue (#3b82f6) | blood pressure, heart rate, temperature |
| **Anatomy** | Purple (#8b5cf6) | head, chest, heart, lung |
| **Procedures** | Pink (#ec4899) | examination, x-ray, blood test |

## Supported Audio Formats

Groq accepts these formats directly (no conversion needed):
- WAV
- MP3
- MP4
- MPEG
- MPGA
- M4A
- OGG
- WEBM
- FLAC

**Max file size:** 25MB (free tier), 100MB (dev tier)

## Model Options

### whisper-large-v3-turbo (Current)
- **Speed:** 216x faster than real-time
- **Cost:** $0.04/hour
- **WER:** 12%
- **Best for:** Fast, cost-effective transcription

### whisper-large-v3 (Alternative)
- **Speed:** 189x faster than real-time
- **Cost:** $0.111/hour
- **WER:** 10.3%
- **Best for:** Maximum accuracy
- **Supports:** Translation to English

To switch models, update `.env`:
```env
GROQ_MODEL=whisper-large-v3
```

## Usage in Your Booking Form

Your existing booking form JavaScript should work without changes. The audio recording flow:

1. User clicks record button
2. Browser records audio (MediaRecorder API)
3. User stops recording
4. Audio blob sent to `/transcribe-audio`
5. Groq transcribes instantly
6. Medical entities highlighted
7. Text inserted into form field

## Benefits Over Previous Setup

| Feature | Whisper.php + Gemini | Groq |
|---------|---------------------|------|
| **Speed** | ~10-30 seconds | ~0.5-2 seconds |
| **Setup** | FFmpeg, models, PHP extension | Just API key |
| **Cost** | Free (local) + Gemini API | $0.04/hour |
| **Accuracy** | Good | Excellent (10-12% WER) |
| **Medical Context** | Via Gemini | Built-in prompt |
| **Maintenance** | High (dependencies) | Low (API) |
| **Scalability** | Limited (server CPU) | Unlimited |

## Troubleshooting

### API Key Issues
```bash
# Test API key
php test_groq_speech.php
```

### Audio Upload Fails
- Check file size (max 25MB)
- Verify supported format
- Check storage permissions

### Slow Transcription
- Groq should be <2 seconds
- Check network connection
- Verify API key tier

### Medical Entities Not Detected
- Entities are keyword-based
- Add custom keywords in `GroqSpeechService::extractMedicalEntities()`
- Or use Gemini for advanced NER (optional)

## Advanced: Combining with Gemini

If you want even better medical entity extraction, you can still use Gemini after Groq:

```php
// In ServiceController.php
$groqResult = $groqService->transcribeAudio($fullPath);
$geminiService = new \App\Services\GeminiMedicalService();
$enhanced = $geminiService->enhanceMedicalText($groqResult['text']);
```

But for most cases, Groq's built-in medical prompt is sufficient.

## Cost Comparison

**Example: 100 hours of audio per month**

| Service | Cost |
|---------|------|
| Groq (turbo) | $4.00 |
| Groq (v3) | $11.10 |
| Whisper.php | Free (but slow) |
| Gemini enhancement | ~$5-10 (depends on usage) |

**Recommended:** Use Groq turbo for speed and cost efficiency.

## Next Steps

1. ✅ Configuration added to `.env`
2. ✅ Service created
3. ✅ Controller updated
4. 🔄 Test with: `php test_groq_speech.php`
5. 🔄 Try recording audio in your booking form
6. 🔄 Verify transcription appears correctly

## Support

- **Groq Docs:** https://console.groq.com/docs/speech-text
- **API Keys:** https://console.groq.com/keys
- **Models:** https://console.groq.com/docs/models

---

**Ready to use!** Your booking form now has ultra-fast medical transcription powered by Groq. 🚀
