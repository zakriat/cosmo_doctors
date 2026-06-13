# Multiple Audio Files Feature - Implementation Guide

## ✅ What's Been Done

### 1. Enhanced JavaScript (`public/js/enhanced-medical-transcription.js`)

Added the following features:

#### New Properties
- `audioQueue`: Array to store multiple audio files with metadata
- `nextAudioId`: Counter for unique audio IDs

#### New Methods

**File Upload Handling:**
- `handleFileUpload(event)` - Handles multiple file selection from input
- Validates file type (audio only) and size (25MB limit)
- Automatically adds files to queue

**Queue Management:**
- `addRecordingToQueue()` - Adds current recording to queue
- `addToQueue(blob, url, name)` - Core method to add audio to queue
- `renderQueue()` - Renders the queue UI dynamically
- `removeFromQueue(audioId)` - Removes specific audio from queue
- `clearQueue()` - Clears entire queue with confirmation
- `playAudioFromQueue(audioId)` - Plays audio from queue

**Transcription:**
- `transcribeAllInQueue()` - Processes all pending audio files
- `transcribeSingleAudio(item)` - Transcribes individual audio file
- `addAllTranscriptionsToNotes()` - Combines all transcriptions into notes field

**UI Helpers:**
- `getStatusBadge(status)` - Returns HTML badge for audio status
- Status types: pending, processing, completed, error

## 📋 What You Need to Do

### Step 1: Update the Booking Form Blade Template

Open `Modules/Frontend/Resources/views/booking.blade.php` and make these changes:

#### A. Add Upload Button (around line 330)

Replace the existing recording controls section with:

```blade
<!-- Speech-to-Text Controls -->
<div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
    <button type="button" id="record-audio-btn" class="btn btn-outline-primary btn-sm">
        <i class="ph ph-microphone"></i> {{ __('frontend.record_audio') }}
    </button>
    
    <!-- NEW: Upload Audio Files Button -->
    <button type="button" id="upload-audio-btn" class="btn btn-outline-secondary btn-sm">
        <i class="ph ph-upload"></i> {{ __('frontend.upload_audio') }}
    </button>
    <input type="file" id="audio-file-input" accept="audio/*" multiple class="d-none">
    
    <button type="button" id="stop-recording-btn" class="btn btn-danger btn-sm d-none">
        <i class="ph ph-stop"></i> {{ __('frontend.stop_recording') }}
    </button>
    <button type="button" id="cancel-recording-btn" class="btn btn-outline-secondary btn-sm d-none">
        {{ __('frontend.cancel_recording') }}
    </button>
    <span id="recording-timer" class="text-muted d-none fw-bold">00:00</span>
</div>
```

#### B. Update Audio Player (around line 345)

Replace the audio player container with:

```blade
<!-- Audio Player (hidden until recording is made) -->
<div id="audio-player-container" class="mb-3 d-none">
    <div class="audio-player-wrapper p-3 border rounded bg-light">
        <div class="mb-2">
            <small class="text-muted">{{ __('frontend.current_audio') }}:</small>
            <strong id="audio-name-display" class="d-block">Recording 1</strong>
        </div>
        <audio id="audio-player" controls class="w-100 mb-2"></audio>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" id="transcribe-btn" class="btn btn-primary btn-sm">
                <i class="ph ph-plus-circle"></i> {{ __('frontend.add_to_queue') }}
            </button>
            <button type="button" id="delete-recording-btn" class="btn btn-outline-danger btn-sm">
                <i class="ph ph-trash"></i> {{ __('frontend.delete_recording') }}
            </button>
        </div>
    </div>
</div>
```

#### C. Add Audio Queue Container (BEFORE transcription cards, around line 360)

```blade
<!-- Audio Queue Container -->
<div id="audio-queue-container" class="mb-4 d-none">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="ph ph-queue me-2"></i>
                <strong>{{ __('frontend.audio_queue') }}</strong>
                <span class="badge bg-white text-primary ms-2" id="queue-count">0</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="transcribe-all-btn" class="btn btn-sm btn-light">
                    <i class="ph ph-text-aa"></i> {{ __('frontend.transcribe_all') }}
                </button>
                <button type="button" id="clear-queue-btn" class="btn btn-sm btn-outline-light">
                    <i class="ph ph-trash"></i> {{ __('frontend.clear') }}
                </button>
            </div>
        </div>
        <div class="card-body p-3">
            <div id="audio-queue-list">
                <!-- Queue items will be dynamically inserted here -->
            </div>
            
            <!-- Add All to Notes Button -->
            <div class="mt-3">
                <button type="button" id="add-all-to-notes-btn" class="btn btn-success w-100 d-none">
                    <i class="ph ph-check-circle"></i> {{ __('frontend.add_all_to_notes') }}
                </button>
            </div>
        </div>
    </div>
</div>
```

#### D. Add CSS Styles (in the @push('styles') section or before </head>)

```blade
@push('styles')
<style>
.audio-queue-item {
    transition: all 0.3s ease;
}

.audio-queue-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.highlight-success {
    animation: highlightPulse 2s ease;
}

@keyframes highlightPulse {
    0%, 100% { background-color: transparent; }
    50% { background-color: rgba(81, 207, 102, 0.2); }
}

.fade-in {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush
```

### Step 2: Add Translation Keys

Add these keys to your language files (e.g., `lang/en/frontend.php`):

```php
'upload_audio' => 'Upload Audio Files',
'current_audio' => 'Current Audio',
'add_to_queue' => 'Add to Queue',
'audio_queue' => 'Audio Queue',
'transcribe_all' => 'Transcribe All',
'clear' => 'Clear',
'add_all_to_notes' => 'Add All Transcriptions to Notes',
```

### Step 3: Test the Feature

1. **Record Multiple Clips:**
   - Click "Record Audio"
   - Record a clip
   - Click "Add to Queue"
   - Repeat for multiple recordings

2. **Upload Files:**
   - Click "Upload Audio Files"
   - Select multiple audio files (mp3, wav, m4a, etc.)
   - Files are automatically added to queue

3. **Transcribe:**
   - Click "Transcribe All" to process all pending files
   - Watch status badges change: Pending → Processing → Completed

4. **Add to Notes:**
   - Once transcriptions are complete, click "Add All Transcriptions to Notes"
   - All transcriptions are combined with separators

## 🎯 Features Included

✅ **Record Multiple Clips** - Record as many audio clips as needed
✅ **Upload Existing Files** - Upload pre-recorded audio files (multiple selection)
✅ **Queue Management** - View, play, and remove individual audio files
✅ **Batch Transcription** - Transcribe all files at once
✅ **Status Tracking** - Visual indicators for pending/processing/completed/error
✅ **Combined Output** - All transcriptions merged into notes with separators
✅ **File Validation** - Type and size validation (25MB limit per file)
✅ **Memory Management** - Proper cleanup of object URLs

## 🔧 Technical Details

### Audio Queue Item Structure
```javascript
{
    id: 1,                          // Unique identifier
    blob: Blob,                     // Audio file blob
    url: "blob:http://...",         // Object URL for playback
    name: "Recording 1",            // Display name
    transcription: "text...",       // Transcribed text (null until completed)
    status: "pending",              // pending|processing|completed|error
    transcriptionData: {...}        // Full API response
}
```

### Status Flow
1. **pending** - Audio added to queue, waiting for transcription
2. **processing** - Currently being transcribed
3. **completed** - Transcription successful
4. **error** - Transcription failed

## 📱 User Experience

1. User can build up a queue of audio files before transcribing
2. Each file shows its status with color-coded badges
3. Play button lets users preview audio before transcribing
4. Remove button for individual file management
5. Clear queue button for starting over
6. "Add All to Notes" combines everything with clear separators

## 🚀 Next Steps (Optional Enhancements)

- Add drag-and-drop file upload
- Show file size and duration
- Add progress bar for batch transcription
- Allow reordering queue items
- Save queue to session storage for page refresh
- Export transcriptions as separate files
