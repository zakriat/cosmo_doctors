# Speech-to-Text for Medical Records - Requirements Document

## Project Overview
Add speech-to-text functionality to the medical record upload section in the booking flow, allowing users to record voice notes that are automatically transcribed and saved.

## Current Location
- **File**: `Modules/Frontend/Resources/views/booking.blade.php`
- **Section**: Medical History & Upload Medical Report (lines ~240-250)
- **Route**: `GET booking/{id}` via `ServiceController@booking`

## System Requirements

### ✅ Prerequisites Met
- PHP 8.3.16 (Required: PHP 8.1+)
- Laravel 11.0
- FFI Extension: **ENABLED** ✅
- Spatie Media Library (already installed)

### 📦 New Dependencies Required
```bash
composer require codewithkyrian/whisper.php
```

### 🎯 Whisper Model
- **Recommended**: `tiny.en` model (~75MB)
- **Auto-downloads** on first use
- **Storage**: Create `storage/app/whisper-models/` directory
- **Alternative**: `base.en` for better accuracy (larger, slower)

## Feature Requirements

### 1. User Interface Components

#### A. Record Button
- Add microphone icon button next to "Upload Medical Report" section
- Visual states:
  - Idle: Gray microphone icon
  - Recording: Red pulsing microphone icon with timer
  - Processing: Loading spinner with "Transcribing..." text
  - Complete: Green checkmark

#### B. Recording Controls
- **Start Recording** button
- **Stop Recording** button (appears during recording)
- **Cancel** button (discard recording)
- **Timer display** showing recording duration (MM:SS)
- **Audio level indicator** (optional visual feedback)

#### C. Transcription Display ⭐ **CORE FEATURE**
- **Auto-populate** `#appointment_extra_info` textarea with transcribed text immediately after processing
- **Append mode**: If textarea already has content, append transcription with line break
- **Replace mode**: Option to replace existing content (user choice)
- Show both:
  - Transcribed text in textarea (editable)
  - Audio file name/link for playback
  - Timestamp of transcription
- Allow full editing of transcribed text before form submission
- Character count indicator (if textarea has max length)
- "Clear transcription" button to remove and start over

### 2. Frontend Functionality

#### A. Browser Audio Recording
- Use **Web Audio API** / **MediaRecorder API**
- Supported formats: WAV, MP3, or WebM
- Sample rate: 16kHz (optimal for Whisper)
- Max recording duration: 5 minutes (configurable)
- File size limit: 10MB (configurable)

#### B. Audio Playback
- Add audio player to review recording before transcription
- Play/Pause controls
- Waveform visualization (optional enhancement)

#### C. Upload Mechanism
- Use existing Uppy integration or separate AJAX upload
- Upload to temporary storage first
- Process transcription via AJAX
- Return transcribed text to populate textarea

#### D. Textarea Population Logic ⭐ **CRITICAL**
**Implementation Flow:**
```javascript
// After successful transcription
$.ajax({
    url: '/booking/transcribe-audio',
    method: 'POST',
    data: formData,
    success: function(response) {
        // Get textarea element
        const textarea = $('#appointment_extra_info');
        const currentText = textarea.val().trim();
        
        // Populate textarea with transcription
        if (currentText === '') {
            // Empty textarea - just set the value
            textarea.val(response.transcription);
        } else {
            // Has existing content - ask user or append
            if (confirm('Append to existing text or replace?')) {
                textarea.val(currentText + '\n\n' + response.transcription);
            } else {
                textarea.val(response.transcription);
            }
        }
        
        // Focus textarea and scroll to show new content
        textarea.focus();
        textarea[0].scrollTop = textarea[0].scrollHeight;
        
        // Show success message
        showToast('Transcription complete!', 'success');
    }
});
```

**Key Features:**
- Instant population after transcription completes
- Smart append/replace logic
- Smooth user experience with focus and scroll
- Visual feedback (success toast/notification)
- Preserve user's existing text if any
- Allow immediate editing after population

### 3. Backend Implementation

#### A. New Route
```
POST /booking/transcribe-audio
```

#### B. Controller Method
- **Location**: `Modules/Frontend/Http/Controllers/ServiceController.php`
- **Method**: `transcribeAudio(Request $request)`
- **Responsibilities**:
  1. Validate audio file (format, size, duration)
  2. Store audio temporarily
  3. Process with Whisper.php
  4. Return transcribed text as JSON
  5. Store audio file permanently with appointment

**Response Format:**
```json
{
    "success": true,
    "transcription": "Patient reports headache for the past three days...",
    "audio_file": "recording_1234567890.wav",
    "duration": 45,
    "processing_time": 8.5,
    "model_used": "tiny.en"
}
```

**Error Response:**
```json
{
    "success": false,
    "error": "Transcription failed",
    "message": "Audio file is corrupted or unsupported format"
}
```

#### C. Whisper.php Integration
```php
// Initialize Whisper
$whisper = Whisper::fromPretrained(
    'tiny.en',
    baseDir: storage_path('app/whisper-models')
);

// Transcribe audio
$audio = readAudio($audioPath);
$segments = $whisper->transcribe($audio, threads: 4);

// Extract text
$transcription = collect($segments)
    ->pluck('text')
    ->implode(' ');
```

#### D. Queue Job (Recommended)
- **Job**: `TranscribeAudioJob`
- **Queue**: `transcription`
- **Timeout**: 120 seconds
- **Reason**: Transcription takes 5-30 seconds, avoid blocking requests

### 4. Database Schema

#### A. New Table: `audio_transcriptions`
```sql
- id (bigint, primary key)
- appointment_id (bigint, nullable, foreign key)
- user_id (bigint, foreign key)
- audio_file_path (string)
- transcription_text (text)
- duration_seconds (integer)
- model_used (string, default: 'tiny.en')
- processing_time_ms (integer)
- status (enum: 'pending', 'processing', 'completed', 'failed')
- error_message (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

#### B. Relationship
- Link to `clinic_appointments` table via `appointment_id`
- Store in `media` table via Spatie Media Library (alternative approach)

### 5. File Storage Structure
```
storage/
├── app/
│   ├── whisper-models/          # Whisper AI models
│   │   └── ggml-tiny.en.bin
│   ├── audio-recordings/         # Permanent audio storage
│   │   └── {user_id}/
│   │       └── {appointment_id}/
│   │           └── recording_{timestamp}.wav
│   └── temp-audio/               # Temporary uploads
│       └── {session_id}_audio.wav
```

### 6. Configuration

#### A. New Config File: `config/whisper.php`
```php
return [
    'model' => env('WHISPER_MODEL', 'tiny.en'),
    'models_path' => storage_path('app/whisper-models'),
    'threads' => env('WHISPER_THREADS', 4),
    'language' => env('WHISPER_LANGUAGE', 'en'),
    'max_duration' => 300, // 5 minutes in seconds
    'max_file_size' => 10240, // 10MB in KB
    'queue_enabled' => env('WHISPER_QUEUE_ENABLED', true),
];
```

#### B. Environment Variables
```env
WHISPER_MODEL=tiny.en
WHISPER_THREADS=4
WHISPER_LANGUAGE=en
WHISPER_QUEUE_ENABLED=true
```

### 7. Validation Rules

#### Audio File Validation
- **Formats**: wav, mp3, ogg, m4a, webm
- **Max size**: 10MB
- **Max duration**: 5 minutes
- **Min duration**: 1 second
- **Sample rate**: 8kHz - 48kHz (auto-resampled to 16kHz)

### 8. Error Handling

#### Frontend Errors
- Microphone permission denied
- Browser doesn't support audio recording
- Recording too short (< 1 second)
- Recording too long (> 5 minutes)
- File size exceeds limit
- Network error during upload

#### Backend Errors
- Invalid audio format
- Transcription failed
- Model not found
- Insufficient server resources
- Timeout during processing

#### User Feedback
- Show error messages in toast/alert
- Fallback to manual text entry
- Option to retry transcription
- Save audio file even if transcription fails

### 9. Performance Considerations

#### Processing Time Estimates
- 1 minute audio ≈ 5-10 seconds (tiny model)
- 1 minute audio ≈ 10-20 seconds (base model)
- First-time use: +30 seconds (model download)

#### Optimization Strategies
- Use queue jobs for async processing
- Show progress indicator to user
- Cache Whisper instance (singleton pattern)
- Limit concurrent transcriptions
- Consider Redis for job queue

#### Server Resources
- Memory: 500MB - 1GB per transcription
- CPU: Multi-threaded (4 threads recommended)
- Storage: ~100MB for models + audio files

### 10. User Experience Flow

#### Happy Path ⭐ **WITH TEXTAREA AUTO-POPULATION**
1. User clicks microphone button
2. Browser requests microphone permission
3. User grants permission
4. Recording starts (red pulsing icon + timer)
5. User speaks medical history (e.g., "I have been experiencing headaches for three days")
6. User clicks stop button
7. Audio player appears for review (optional: user can play back)
8. User clicks "Transcribe" button
9. Loading indicator shows "Transcribing..." (5-15 seconds)
10. **✨ AJAX receives transcription response**
11. **✨ Textarea `#appointment_extra_info` auto-populates with: "I have been experiencing headaches for three days"**
12. **✨ Textarea scrolls to show new content**
13. **✨ Success notification: "Transcription complete!"**
14. User can immediately edit the transcribed text if needed
15. Audio file saved with appointment
16. Both audio + text stored in database on form submission

#### Alternative Flows
- **User has existing text in textarea:**
  - Show modal: "Append to existing text or replace?"
  - User chooses → textarea updates accordingly
  
- **User cancels recording:**
  - Discard audio, no textarea change
  
- **User re-records:**
  - Replace previous audio
  - Ask to replace or append transcription
  
- **Transcription fails:**
  - Show error notification
  - Keep audio file saved
  - Textarea remains unchanged
  - Allow manual text entry
  
- **User edits transcription:**
  - Save edited version (not original)
  - Mark as "manually edited" in database

### 11. Security Considerations

- Validate file MIME types (not just extensions)
- Sanitize transcribed text before display
- Rate limit transcription requests (max 5 per user per hour)
- Authenticate all API requests
- Store audio files with secure permissions
- Delete temporary files after processing
- GDPR compliance: Allow users to delete recordings

### 12. Testing Requirements

#### Unit Tests
- Audio file validation
- Whisper.php integration
- Text extraction from segments
- Error handling

#### Integration Tests
- Full transcription flow
- Queue job processing
- File storage and retrieval
- Database relationships

#### Browser Tests
- Microphone permission handling
- Audio recording functionality
- AJAX upload and response handling
- UI state transitions

### 13. Accessibility

- Keyboard navigation for all controls
- ARIA labels for screen readers
- Visual feedback for recording state
- Alternative text entry always available
- Clear error messages

### 14. Localization

#### Translation Keys Needed
```php
'frontend.record_audio' => 'Record Audio'
'frontend.start_recording' => 'Start Recording'
'frontend.stop_recording' => 'Stop Recording'
'frontend.cancel_recording' => 'Cancel'
'frontend.transcribing' => 'Transcribing...'
'frontend.transcription_complete' => 'Transcription Complete'
'frontend.transcription_failed' => 'Transcription Failed'
'frontend.microphone_permission_denied' => 'Microphone access denied'
'frontend.recording_too_short' => 'Recording too short'
'frontend.recording_too_long' => 'Recording exceeds 5 minutes'
```

### 15. Future Enhancements (Optional)

- Real-time transcription (streaming)
- Multiple language support
- Speaker diarization (identify different speakers)
- Medical terminology dictionary
- Voice commands for form filling
- Integration with patient history
- Audio search functionality
- Transcription confidence scores

## Implementation Priority

### Phase 1: Core Functionality (MVP)
1. Install Whisper.php package
2. Create backend transcription endpoint
3. Add basic record button UI
4. Implement browser audio recording
5. Upload and transcribe audio
6. **⭐ Populate textarea with transcription (CRITICAL)**
7. Store audio file

### Phase 2: Enhanced UX
1. Add queue job for async processing
2. Implement progress indicators
3. Add audio playback controls
4. Error handling and user feedback
5. Database schema and relationships
6. Smart append/replace logic for textarea

### Phase 3: Polish & Optimization
1. Performance optimization
2. Comprehensive testing
3. Security hardening
4. Accessibility improvements
5. Documentation

---

## Textarea Auto-Population - Technical Deep Dive

### Why This is Feasible ✅

**1. Simple DOM Manipulation**
- Target: `<textarea id="appointment_extra_info">` (already exists in booking.blade.php)
- Method: Standard jQuery `.val()` or vanilla JS `.value`
- No complex state management needed

**2. AJAX Response Handling**
```javascript
// Whisper.php returns plain text
response.transcription = "Patient reports severe headache..."

// Direct assignment to textarea
$('#appointment_extra_info').val(response.transcription);
```

**3. Real-World Example Flow**
```
User speaks: "I have diabetes and high blood pressure"
      ↓
Audio recorded: recording.wav (3 seconds)
      ↓
Uploaded to: /booking/transcribe-audio
      ↓
Whisper processes: 2 seconds
      ↓
Returns JSON: {"transcription": "I have diabetes and high blood pressure"}
      ↓
JavaScript: $('#appointment_extra_info').val(response.transcription)
      ↓
Textarea shows: "I have diabetes and high blood pressure"
      ↓
User can edit: "I have diabetes, high blood pressure, and take metformin"
```

### Implementation Code Snippet

**Frontend (JavaScript):**
```javascript
function transcribeAudio(audioBlob) {
    const formData = new FormData();
    formData.append('audio', audioBlob, 'recording.wav');
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    
    // Show loading state
    $('#transcribe-btn').prop('disabled', true).text('Transcribing...');
    
    $.ajax({
        url: '/booking/transcribe-audio',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // ⭐ POPULATE TEXTAREA - THIS IS THE MAIN FEATURE
                const textarea = $('#appointment_extra_info');
                const existingText = textarea.val().trim();
                
                if (existingText) {
                    // Append with line break
                    textarea.val(existingText + '\n\n' + response.transcription);
                } else {
                    // Set new value
                    textarea.val(response.transcription);
                }
                
                // Visual feedback
                textarea.addClass('highlight-success');
                setTimeout(() => textarea.removeClass('highlight-success'), 2000);
                
                // Scroll to show content
                textarea.focus();
                textarea[0].scrollTop = textarea[0].scrollHeight;
                
                // Success notification
                toastr.success('Transcription complete!');
            }
        },
        error: function(xhr) {
            toastr.error('Transcription failed. Please try again.');
        },
        complete: function() {
            $('#transcribe-btn').prop('disabled', false).text('Transcribe');
        }
    });
}
```

**Backend (Laravel Controller):**
```php
public function transcribeAudio(Request $request)
{
    $request->validate([
        'audio' => 'required|file|mimes:wav,mp3,ogg,m4a|max:10240'
    ]);
    
    try {
        // Store audio temporarily
        $audioPath = $request->file('audio')->store('temp-audio');
        $fullPath = storage_path('app/' . $audioPath);
        
        // Initialize Whisper
        $whisper = Whisper::fromPretrained(
            'tiny.en',
            baseDir: storage_path('app/whisper-models')
        );
        
        // Transcribe
        $audio = readAudio($fullPath);
        $segments = $whisper->transcribe($audio, threads: 4);
        
        // Extract text
        $transcription = collect($segments)
            ->pluck('text')
            ->implode(' ')
            ->trim();
        
        // Clean up temp file
        Storage::delete($audioPath);
        
        // Return transcription for textarea population
        return response()->json([
            'success' => true,
            'transcription' => $transcription, // ⭐ THIS POPULATES THE TEXTAREA
            'duration' => count($segments),
            'model_used' => 'tiny.en'
        ]);
        
    } catch (\Exception $e) {
        Log::error('Transcription failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'Transcription failed',
            'message' => $e->getMessage()
        ], 500);
    }
}
```

### CSS for Visual Feedback
```css
/* Highlight textarea when transcription populates */
.highlight-success {
    border: 2px solid #28a745 !important;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
}

/* Smooth transition back to normal */
#appointment_extra_info {
    transition: border 0.3s ease, box-shadow 0.3s ease;
}
```

### Testing the Feature

**Manual Test:**
1. Open booking page
2. Click microphone button
3. Say: "Patient has fever and cough for two days"
4. Stop recording
5. Click "Transcribe"
6. **Expected**: Textarea shows "Patient has fever and cough for two days"
7. **Verify**: Text is editable
8. **Verify**: Form submission includes the text

**Automated Test:**
```php
public function test_transcription_populates_textarea()
{
    $audioFile = UploadedFile::fake()->create('test.wav', 1024);
    
    $response = $this->post('/booking/transcribe-audio', [
        'audio' => $audioFile
    ]);
    
    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'transcription' => 'Expected transcription text'
             ]);
}
```

### Fallback Scenarios

**If transcription fails:**
- Textarea remains unchanged
- User can still type manually
- Audio file is saved for later review
- Error message shown to user

**If JavaScript fails:**
- Graceful degradation
- Manual text entry still works
- Form submission works normally

### Performance Metrics

- **Transcription time**: 5-15 seconds for 1-minute audio
- **Textarea population**: Instant (<100ms after AJAX response)
- **User perception**: Smooth, no lag
- **Success rate target**: >95%

---

## Success Metrics

- Transcription accuracy: >85%
- Processing time: <15 seconds for 1-minute audio
- User adoption: >30% of bookings use voice input
- Error rate: <5%
- User satisfaction: Positive feedback

## Technical Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| High server load | High | Use queue jobs, limit concurrent processing |
| Transcription inaccuracy | Medium | Use base model, allow manual editing |
| Browser compatibility | Medium | Feature detection, fallback to manual entry |
| Model download delay | Low | Pre-download models during deployment |
| Storage costs | Low | Compress audio files, set retention policy |

## Dependencies

- Whisper.php library
- Browser MediaRecorder API support
- Server FFI extension enabled
- Queue worker running (if using queues)
- Sufficient server storage

## Deployment Checklist

- [ ] Install Whisper.php via Composer
- [ ] Enable FFI extension in php.ini
- [ ] Create storage directories with proper permissions
- [ ] Add configuration file
- [ ] Set environment variables
- [ ] Run migrations
- [ ] Pre-download Whisper model
- [ ] Configure queue worker (if using queues)
- [ ] Test on staging environment
- [ ] Update user documentation

---

**Document Version**: 1.0  
**Last Updated**: January 16, 2026  
**Status**: Ready for Implementation
