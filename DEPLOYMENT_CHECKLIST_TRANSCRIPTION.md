# Speech-to-Text Transcription - Deployment Checklist

## 📦 Files to Upload to Live Server

### 1. Core JavaScript Files
```
✅ public/js/enhanced-medical-transcription.js
```
**Purpose**: Main client-side logic for recording, uploading, queue management, and transcription

### 2. Backend Services
```
✅ app/Services/GroqSpeechService.php
✅ app/Services/GeminiMedicalService.php (optional - for medical enhancement)
```
**Purpose**: 
- GroqSpeechService: Handles audio transcription via Groq API
- GeminiMedicalService: Enhances transcription with medical terminology (optional)

### 3. Controllers
```
✅ Modules/Frontend/Http/Controllers/ServiceController.php
```
**Purpose**: Contains `transcribeAudio()` and `transcribeAudioEnhanced()` methods

### 4. Routes
```
✅ Modules/Frontend/Routes/web.php
```
**Purpose**: Defines `/transcribe-audio` and `/transcribe-audio-enhanced` endpoints

### 5. Views (Blade Templates)
```
✅ Modules/Frontend/Resources/views/booking.blade.php
✅ Modules/Frontend/Resources/views/appointment_detail.blade.php (if using on detail page)
```
**Purpose**: Contains UI elements for recording, uploading, and queue management

### 6. Language Files
```
✅ lang/en/frontend.php
✅ lang/fr/frontend.php (if you have French translations)
✅ lang/[other-languages]/frontend.php (add translations for other languages)
```
**Purpose**: Translation keys for UI labels

### 7. Configuration Files
```
✅ config/groq.php
✅ config/gemini.php (optional - if using Gemini enhancement)
```
**Purpose**: API configuration and settings

### 8. Database Migrations
```
✅ database/migrations/2026_01_29_122850_add_gemini_fields_to_audio_transcriptions_table.php
✅ database/migrations/[date]_create_audio_transcriptions_table.php (if exists)
```
**Purpose**: Database schema for storing transcriptions

### 9. Environment Variables
```
✅ .env (update on server)
```
**Required variables:**
```env
GROQ_API_KEY=your_groq_api_key_here
GROQ_MODEL=whisper-large-v3-turbo
GROQ_TIMEOUT=30
GROQ_MAX_FILE_SIZE=25

# Optional - for Gemini enhancement
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-1.5-flash
```

---

## 🚀 Deployment Steps

### Step 1: Backup Current Files
```bash
# On live server, backup existing files
cp public/js/enhanced-medical-transcription.js public/js/enhanced-medical-transcription.js.backup
cp app/Services/GroqSpeechService.php app/Services/GroqSpeechService.php.backup
# ... backup other files
```

### Step 2: Upload Files via FTP/SFTP
Upload all files listed above to their respective directories on the live server.

### Step 3: Update Environment Variables
```bash
# On live server
nano .env

# Add/update these lines:
GROQ_API_KEY=your_actual_groq_api_key
GROQ_MODEL=whisper-large-v3-turbo
GROQ_TIMEOUT=30
GROQ_MAX_FILE_SIZE=25
```

### Step 4: Run Database Migrations
```bash
# On live server
php artisan migrate

# If migration already ran, you can skip or use:
php artisan migrate:status
```

### Step 5: Clear Caches
```bash
# On live server
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### Step 6: Set Permissions
```bash
# Ensure storage directories are writable
chmod -R 775 storage/app/audio_transcriptions
chown -R www-data:www-data storage/app/audio_transcriptions

# If using public storage
chmod -R 775 public/storage
```

### Step 7: Test the Feature
1. Go to booking form on live site
2. Try recording audio
3. Try uploading audio files
4. Test transcription
5. Check browser console for errors
6. Check Laravel logs: `storage/logs/laravel.log`

---

## 🔍 File Dependencies Map

### Frontend Flow
```
booking.blade.php
    ↓ (includes)
enhanced-medical-transcription.js
    ↓ (AJAX POST)
/transcribe-audio-enhanced
    ↓ (routes to)
ServiceController@transcribeAudioEnhanced
    ↓ (uses)
GroqSpeechService@transcribeAudio
    ↓ (optional)
GeminiMedicalService@enhanceMedicalText
```

### Backend Dependencies
```
ServiceController.php
├── GroqSpeechService.php
│   ├── config/groq.php
│   ├── .env (GROQ_API_KEY)
│   └── storage/app/audio_transcriptions/
│
└── GeminiMedicalService.php (optional)
    ├── config/gemini.php
    └── .env (GEMINI_API_KEY)
```

---

## ✅ Verification Checklist

After deployment, verify:

- [ ] Groq API key is set in `.env`
- [ ] Config cache is cleared
- [ ] Routes are registered (`php artisan route:list | grep transcribe`)
- [ ] Storage directory exists and is writable
- [ ] JavaScript file is loaded (check browser Network tab)
- [ ] Recording button appears on booking form
- [ ] Upload button appears on booking form
- [ ] Audio queue container appears when files are added
- [ ] Transcription works (check Laravel logs for errors)
- [ ] Transcribed text appears in notes field
- [ ] No JavaScript errors in browser console

---

## 🐛 Troubleshooting

### Issue: "Groq API key not configured"
**Solution**: 
```bash
# Check .env file
cat .env | grep GROQ_API_KEY

# If missing, add it
echo "GROQ_API_KEY=your_key_here" >> .env

# Clear config cache
php artisan config:clear
```

### Issue: "Storage directory not writable"
**Solution**:
```bash
chmod -R 775 storage/app/audio_transcriptions
chown -R www-data:www-data storage/app/audio_transcriptions
```

### Issue: "Route not found"
**Solution**:
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list | grep transcribe
```

### Issue: JavaScript not loading
**Solution**:
- Clear browser cache (Ctrl+Shift+R)
- Check file exists: `ls -la public/js/enhanced-medical-transcription.js`
- Check file permissions: `chmod 644 public/js/enhanced-medical-transcription.js`

### Issue: CORS errors
**Solution**: Ensure your server allows audio file uploads
```php
// In config/cors.php
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

---

## 📊 File Size Reference

Approximate file sizes for upload planning:

| File | Size |
|------|------|
| enhanced-medical-transcription.js | ~35 KB |
| GroqSpeechService.php | ~8 KB |
| GeminiMedicalService.php | ~12 KB |
| ServiceController.php | ~25 KB |
| booking.blade.php | ~50 KB |
| config/groq.php | ~2 KB |
| config/gemini.php | ~3 KB |

**Total**: ~135 KB (excluding dependencies)

---

## 🔐 Security Notes

1. **API Keys**: Never commit API keys to version control
2. **File Upload**: Validate file types and sizes server-side
3. **Storage**: Store audio files outside public directory
4. **HTTPS**: Use HTTPS in production (required for microphone access)
5. **Rate Limiting**: Consider adding rate limits to transcription endpoint

---

## 📝 Post-Deployment Tasks

1. Monitor Laravel logs for errors
2. Check Groq API usage/quota
3. Test with different audio formats (mp3, wav, m4a)
4. Test with different file sizes
5. Verify mobile compatibility
6. Test with slow internet connection
7. Monitor server storage usage

---

## 🆘 Support Resources

- **Groq API Docs**: https://console.groq.com/docs
- **Laravel Logs**: `storage/logs/laravel.log`
- **Browser Console**: F12 → Console tab
- **Network Tab**: F12 → Network tab (check AJAX requests)

---

## 📞 Quick Commands Reference

```bash
# Check if routes exist
php artisan route:list | grep transcribe

# Check config values
php artisan tinker
>>> config('groq.api_key')
>>> config('groq.model')

# Test Groq service
php artisan tinker
>>> $service = new \App\Services\GroqSpeechService();
>>> $service->testConnection();

# View recent logs
tail -f storage/logs/laravel.log

# Check storage permissions
ls -la storage/app/audio_transcriptions/
```
