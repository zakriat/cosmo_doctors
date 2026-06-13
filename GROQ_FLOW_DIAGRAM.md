# Groq Speech-to-Text Flow Diagram

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        BOOKING FORM                              │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  👤 User Interface                                        │  │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐         │  │
│  │  │  🎤 Record │  │  ⏹️ Stop   │  │  📝 Transcribe│       │  │
│  │  │   Audio    │  │  Recording │  │    Audio    │         │  │
│  │  └────────────┘  └────────────┘  └────────────┘         │  │
│  │                                                            │  │
│  │  ┌──────────────────────────────────────────────────┐    │  │
│  │  │  📄 Medical Notes Textarea                       │    │  │
│  │  │  [Transcribed text appears here...]              │    │  │
│  │  └──────────────────────────────────────────────────┘    │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ POST /transcribe-audio
                              │ FormData { audio: Blob }
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    LARAVEL BACKEND                               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  ServiceController.php                                    │  │
│  │  ┌────────────────────────────────────────────────────┐  │  │
│  │  │  transcribeAudio()                                  │  │  │
│  │  │  1. Validate audio file                            │  │  │
│  │  │  2. Store temporarily                              │  │  │
│  │  │  3. Call GroqSpeechService                         │  │  │
│  │  └────────────────────────────────────────────────────┘  │  │
│  └──────────────────────────────────────────────────────────┘  │
│                              │                                   │
│                              ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  GroqSpeechService.php                                    │  │
│  │  ┌────────────────────────────────────────────────────┐  │  │
│  │  │  transcribeAudio()                                  │  │  │
│  │  │  • Validate file size/format                       │  │  │
│  │  │  • Check cache                                      │  │  │
│  │  │  • Prepare medical prompt                          │  │  │
│  │  │  • Call Groq API                                    │  │  │
│  │  └────────────────────────────────────────────────────┘  │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ HTTPS POST
                              │ multipart/form-data
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      GROQ API                                    │
│  🌐 https://api.groq.com/openai/v1/audio/transcriptions         │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Whisper Large V3 Turbo                                   │  │
│  │  • 189x faster than real-time                            │  │
│  │  • Medical terminology support                           │  │
│  │  • Word-level timestamps                                 │  │
│  │  • Quality metrics                                       │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
│  Processing Time: 0.5-2 seconds ⚡                              │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ JSON Response
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    RESPONSE PROCESSING                           │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  GroqSpeechService.php                                    │  │
│  │  • Parse transcription                                    │  │
│  │  • Extract medical entities                              │  │
│  │  • Format for medical records                            │  │
│  │  • Calculate quality metrics                             │  │
│  │  • Cache result                                           │  │
│  └──────────────────────────────────────────────────────────┘  │
│                              │                                   │
│                              ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  ServiceController.php                                    │  │
│  │  • Save to database (audio_transcriptions)               │  │
│  │  • Store audio file permanently                          │  │
│  │  • Clean up temp files                                    │  │
│  │  • Return JSON response                                   │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ JSON Response
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    FRONTEND DISPLAY                              │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  JavaScript (booking.blade.php)                           │  │
│  │  • Receive transcription                                  │  │
│  │  • Display in textarea                                    │  │
│  │  • Highlight medical entities                            │  │
│  │  • Show quality metrics                                   │  │
│  │  • Enable form submission                                 │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow

### 1. Audio Recording
```
User clicks "Record" 
  → MediaRecorder API starts
  → Audio chunks collected
  → User clicks "Stop"
  → Blob created (audio/wav)
```

### 2. Upload & Transcription
```
FormData created with audio blob
  → POST to /transcribe-audio
  → Laravel validates request
  → File stored temporarily
  → GroqSpeechService called
  → API request to Groq
  → Response in 0.5-2 seconds ⚡
```

### 3. Processing
```
Groq returns transcription
  → Extract medical entities
  → Format text (capitalize, punctuation)
  → Calculate quality metrics
  → Cache result
  → Save to database
```

### 4. Display
```
JSON response to frontend
  → Parse transcription
  → Insert into textarea
  → Highlight entities (optional)
  → Show success message
  → User can edit/submit
```

## Medical Entity Extraction Flow

```
Transcription Text
       │
       ▼
┌─────────────────────────────────────┐
│  extractMedicalEntities()           │
│                                     │
│  Categories:                        │
│  • Symptoms    → 🔴 Red            │
│  • Conditions  → 🟠 Orange         │
│  • Medications → 🟢 Green          │
│  • Vitals      → 🔵 Blue           │
│  • Anatomy     → 🟣 Purple         │
│  • Procedures  → 🩷 Pink           │
└─────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────┐
│  Keyword Matching                   │
│  • "headache" → symptoms            │
│  • "blood pressure" → vitals        │
│  • "aspirin" → medications          │
└─────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────┐
│  Return Array of Entities           │
│  [                                  │
│    {                                │
│      text: "headache",              │
│      category: "symptoms",          │
│      confidence: 0.85               │
│    }                                │
│  ]                                  │
└─────────────────────────────────────┘
```

## Comparison: Before vs After

### Before (Whisper.php + Gemini)
```
Audio Upload (1s)
  ↓
FFmpeg Conversion (2-5s)
  ↓
Whisper.php Processing (10-20s)
  ↓
Gemini Enhancement (2-5s)
  ↓
Total: 15-31 seconds ⏱️
```

### After (Groq)
```
Audio Upload (1s)
  ↓
Groq API Processing (0.5-2s)
  ↓
Entity Extraction (0.1s)
  ↓
Total: 1.6-3.1 seconds ⚡
```

**Speed Improvement: 5-10x faster!**

## Error Handling Flow

```
┌─────────────────────────────────────┐
│  Audio Upload                       │
└─────────────────────────────────────┘
       │
       ▼
┌─────────────────────────────────────┐
│  Validation                         │
│  • File size < 25MB?                │
│  • Valid format?                    │
│  • User authenticated?              │
└─────────────────────────────────────┘
       │
       ├─ ❌ Fail → Return error
       │
       ▼ ✅ Pass
┌─────────────────────────────────────┐
│  Groq API Call                      │
└─────────────────────────────────────┘
       │
       ├─ ❌ API Error → Log & return error
       │
       ▼ ✅ Success
┌─────────────────────────────────────┐
│  Process Response                   │
└─────────────────────────────────────┘
       │
       ├─ ❌ Invalid response → Fallback
       │
       ▼ ✅ Valid
┌─────────────────────────────────────┐
│  Save to Database                   │
└─────────────────────────────────────┘
       │
       ▼ ✅ Success
┌─────────────────────────────────────┐
│  Return to Frontend                 │
└─────────────────────────────────────┘
```

## Caching Strategy

```
Request arrives
  ↓
Generate cache key: md5(audio_file)
  ↓
Check cache
  ├─ ✅ Hit → Return cached result (instant!)
  │
  ▼ ❌ Miss
Call Groq API
  ↓
Process response
  ↓
Store in cache (1 hour TTL)
  ↓
Return result
```

**Cache Benefits:**
- Instant response for duplicate audio
- Reduced API costs
- Lower latency

## Database Schema

```
audio_transcriptions
├── id (primary key)
├── user_id (foreign key)
├── audio_file_path (string)
├── transcription_text (text)
├── original_text (text)
├── final_text (text)
├── medical_categories (json)
│   └── [
│         {text: "...", category: "...", confidence: 0.85}
│       ]
├── confidence_scores (json)
│   └── {groq: 0.92, avg_logprob: -0.097}
├── duration_seconds (float)
├── model_used (string)
├── processing_time_ms (integer)
├── status (enum: pending, completed, failed)
├── created_at (timestamp)
└── updated_at (timestamp)
```

## API Request/Response

### Request
```http
POST /transcribe-audio HTTP/1.1
Content-Type: multipart/form-data

------WebKitFormBoundary
Content-Disposition: form-data; name="audio"; filename="recording.wav"
Content-Type: audio/wav

[binary audio data]
------WebKitFormBoundary
Content-Disposition: form-data; name="_token"

[csrf_token]
------WebKitFormBoundary--
```

### Response
```json
{
  "success": true,
  "transcription": "Patient reports severe headache and high blood pressure.",
  "original_text": "patient reports severe headache and high blood pressure",
  "audio_id": 123,
  "audio_file": "audio_transcriptions/1/abc123.wav",
  "duration": 15.5,
  "processing_time": 0.85,
  "model_used": "whisper-large-v3-turbo",
  "medical_entities": [
    {
      "text": "headache",
      "category": "symptoms",
      "confidence": 0.85
    },
    {
      "text": "blood pressure",
      "category": "vitals",
      "confidence": 0.85
    }
  ],
  "quality_metrics": {
    "avg_confidence": -0.097,
    "low_confidence_segments": 0,
    "total_segments": 3,
    "quality_score": 0.92
  },
  "category_colors": {
    "symptoms": "#ef4444",
    "vitals": "#3b82f6"
  }
}
```

---

**Visual flow complete!** This shows exactly how audio goes from recording to transcription. 🎯
