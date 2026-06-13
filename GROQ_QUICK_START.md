# Groq Speech-to-Text - Quick Start

## ✅ What's Done

Your medical records booking form now uses **Groq API** for ultra-fast speech-to-text transcription.

## 🔑 Your API Key

```
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
```

Already configured in `.env` file.

## 🚀 Test It

```bash
php test_groq_speech.php
```

## 📝 How It Works

1. User records audio in booking form
2. Audio sent to `/transcribe-audio` endpoint
3. Groq transcribes in <2 seconds
4. Medical entities extracted automatically
5. Formatted text returned to form

## 🎯 Key Features

- **189x faster** than real-time
- **No FFmpeg** or local models needed
- **Medical terminology** built-in
- **Auto-detects:** symptoms, medications, vitals, conditions
- **Supports:** wav, mp3, ogg, m4a, webm, flac

## 💰 Cost

- **$0.04 per hour** of audio
- Much cheaper than Gemini API calls
- Free tier: 25MB max file size

## 📊 Response Example

```json
{
  "success": true,
  "transcription": "Patient reports severe headache and high blood pressure.",
  "medical_entities": [
    {"text": "headache", "category": "symptoms"},
    {"text": "blood pressure", "category": "vitals"}
  ],
  "processing_time": 0.85,
  "quality_score": 0.92
}
```

## 🔧 Configuration

All set in `.env`:
- ✅ API Key configured
- ✅ Model: whisper-large-v3-turbo (fastest)
- ✅ Cache enabled
- ✅ Storage paths configured

## 📚 Full Documentation

See `GROQ_SPEECH_TO_TEXT_SETUP.md` for complete details.

---

**Ready to use!** Just record audio in your booking form. 🎤
