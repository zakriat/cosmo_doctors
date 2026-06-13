# Quick Start Guide - Speech-to-Text Feature

## 🚀 Ready to Use!

The speech-to-text feature is fully implemented and ready for testing.

## How to Test

### 1. Start Your Server

```bash
php artisan serve
```

### 2. Navigate to Booking Page

Visit: `http://localhost:8000/booking/1` (replace `1` with actual service ID)

### 3. Find the Feature

Scroll down to the **"Medical History"** section. You'll see:
- 🎤 **Record Audio** button
- Textarea for medical history

### 4. Record Your Voice

1. Click **"Record Audio"**
2. Allow microphone access when prompted
3. Speak clearly: *"Patient has fever and cough for two days"*
4. Click **"Stop Recording"**

### 5. Transcribe

1. Review your recording (audio player appears)
2. Click **"Transcribe"** button
3. Wait 5-15 seconds (loading indicator shows)
4. **✨ Watch the textarea auto-populate with your speech!**

### 6. Edit & Submit

- Edit the transcribed text if needed
- Continue with booking form
- Submit appointment

## First-Time Use

⏳ **Important**: The first transcription will take 30-60 seconds longer because:
- Whisper model downloads automatically (~75MB)
- This only happens once
- Subsequent transcriptions are fast (5-15 seconds)

## What to Expect

### ✅ Success Indicators
- Green border flashes on textarea
- "Transcription Complete" message
- Text appears in textarea
- Text is editable

### ⚠️ If Something Goes Wrong
- Check browser console (F12)
- Check Laravel logs: `storage/logs/laravel.log`
- Verify FFI is enabled: `php -r "var_dump(extension_loaded('ffi'));"`

## Browser Requirements

✅ **Works in:**
- Chrome/Edge (recommended)
- Firefox
- Safari 14.1+

❌ **Doesn't work in:**
- Internet Explorer
- Very old browsers

## Tips for Best Results

### 🎤 Recording Tips
- Speak clearly and at normal pace
- Minimize background noise
- Keep recordings under 2 minutes for faster processing
- Use a good microphone if available

### 📝 Transcription Tips
- Review audio before transcribing
- Edit transcription if needed (it's not 100% perfect)
- You can record multiple times and append text

## Example Use Cases

### Medical History
*"Patient reports headache for three days, accompanied by mild fever and fatigue. No known allergies."*

### Symptoms
*"Experiencing sharp pain in lower back, difficulty sleeping, and reduced mobility since last week."*

### Medications
*"Currently taking ibuprofen 400mg twice daily and vitamin D supplements."*

## Keyboard Shortcuts

- **Start Recording**: Click button (no keyboard shortcut yet)
- **Stop Recording**: Click button or wait 5 minutes (auto-stop)
- **Edit Text**: Click in textarea and type normally

## Troubleshooting

### "Microphone permission denied"
→ Click the 🔒 icon in browser address bar → Allow microphone

### "Transcription failed"
→ Check if FFI extension is enabled in php.ini

### Textarea doesn't populate
→ Check browser console for errors (F12)

### Recording is silent
→ Check microphone is working in system settings

## Advanced Features

### Append vs Replace
If textarea already has text:
- **OK** = Append new transcription
- **Cancel** = Replace existing text

### Multiple Recordings
You can:
1. Record → Transcribe
2. Record again → Transcribe
3. All text appends to textarea

### Delete Recording
Click **"Delete Recording"** to remove audio and start over

## Performance

- **Recording**: Instant, real-time
- **Upload**: 1-3 seconds
- **Transcription**: 5-15 seconds per minute
- **Textarea update**: Instant

## What Gets Saved

When you submit the booking form:
- ✅ Transcribed text (in appointment)
- ✅ Audio file (in database)
- ✅ Processing metadata (duration, model used)

## Need Help?

1. Check `SPEECH_TO_TEXT_IMPLEMENTATION_SUMMARY.md` for details
2. Check `speech-to-text-medical-records-requirements.md` for full specs
3. Run test: `php test_whisper_installation.php`
4. Check Laravel logs: `storage/logs/laravel.log`

---

## 🎉 That's It!

The feature is ready to use. Just navigate to the booking page and start recording!

**Happy Testing! 🚀**
