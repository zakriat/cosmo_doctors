# Speech-to-Text Feature - Deployment Checklist

## Pre-Deployment

### 1. Code Review ✅
- [x] All files created/modified
- [x] No syntax errors
- [x] Code follows Laravel conventions
- [x] Comments added where needed

### 2. Testing ✅
- [ ] Run: `php test_whisper_installation.php`
- [ ] Manual testing completed (see test_speech_to_text_feature.md)
- [ ] Browser compatibility verified
- [ ] Mobile testing (optional)

### 3. Dependencies ✅
- [x] Whisper.php installed: `composer show codewithkyrian/whisper.php`
- [x] FFI extension enabled
- [x] Storage directories created

### 4. Database ✅
- [x] Migration created
- [x] Migration run: `php artisan migrate`
- [x] Table verified: `audio_transcriptions`

### 5. Configuration ✅
- [x] Config file created: `config/whisper.php`
- [x] Environment variables documented
- [x] Default values set

---

## Deployment Steps

### Step 1: Backup 🔒

```bash
# Backup database
php artisan backup:run

# Backup code (if not using Git)
cp -r . ../backup-$(date +%Y%m%d)
```

### Step 2: Pull Latest Code 📥

```bash
git pull origin main
# or upload files manually
```

### Step 3: Install Dependencies 📦

```bash
composer install --no-dev --optimize-autoloader
```

### Step 4: Run Migrations 🗄️

```bash
php artisan migrate --force
```

### Step 5: Create Storage Directories 📁

```bash
mkdir -p storage/app/whisper-models
mkdir -p storage/app/audio-recordings
mkdir -p storage/app/temp-audio
chmod -R 775 storage/app/whisper-models
chmod -R 775 storage/app/audio-recordings
chmod -R 775 storage/app/temp-audio
```

### Step 6: Clear Caches 🧹

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Step 7: Optimize 🚀

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 8: Set Permissions 🔐

```bash
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### Step 9: Verify Installation ✅

```bash
php test_whisper_installation.php
```

Expected output:
```
✓ FFI extension enabled
✓ Whisper class found
✓ Storage directories exist
✓ Config file exists
✓ All checks passed!
```

---

## Post-Deployment

### 1. Smoke Test 🔥

- [ ] Visit booking page
- [ ] Click "Record Audio"
- [ ] Record 5 seconds
- [ ] Transcribe
- [ ] Verify textarea populates

### 2. Monitor Logs 📊

```bash
tail -f storage/logs/laravel.log
```

Watch for:
- Transcription errors
- Model download progress (first time)
- Performance issues

### 3. Check Disk Space 💾

```bash
df -h
```

Ensure at least 500MB free for:
- Whisper models (~75MB)
- Audio files
- Temporary files

### 4. Performance Monitoring ⏱️

Monitor:
- Response times
- Memory usage
- CPU usage during transcription

### 5. User Feedback 👥

- [ ] Collect user feedback
- [ ] Monitor error rates
- [ ] Check transcription accuracy

---

## Environment-Specific Settings

### Development

```env
APP_ENV=local
APP_DEBUG=true
WHISPER_MODEL=tiny.en
WHISPER_THREADS=2
WHISPER_QUEUE_ENABLED=false
```

### Staging

```env
APP_ENV=staging
APP_DEBUG=true
WHISPER_MODEL=tiny.en
WHISPER_THREADS=4
WHISPER_QUEUE_ENABLED=false
```

### Production

```env
APP_ENV=production
APP_DEBUG=false
WHISPER_MODEL=tiny.en
WHISPER_THREADS=4
WHISPER_QUEUE_ENABLED=true  # Enable for better performance
QUEUE_CONNECTION=redis      # Use Redis for queues
```

---

## Rollback Plan 🔄

If something goes wrong:

### Quick Rollback

```bash
# 1. Restore database backup
php artisan backup:restore

# 2. Revert code
git revert HEAD
# or restore from backup

# 3. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Remove Feature

```bash
# 1. Drop table
php artisan migrate:rollback --step=1

# 2. Remove package
composer remove codewithkyrian/whisper.php

# 3. Revert file changes
git checkout HEAD -- Modules/Frontend/Resources/views/booking.blade.php
git checkout HEAD -- Modules/Frontend/Http/Controllers/ServiceController.php
git checkout HEAD -- Modules/Frontend/Routes/web.php
git checkout HEAD -- lang/en/frontend.php
```

---

## Monitoring & Maintenance

### Daily Checks

- [ ] Check error logs
- [ ] Monitor disk space
- [ ] Check transcription success rate

### Weekly Checks

- [ ] Review performance metrics
- [ ] Clean up old temp files
- [ ] Check audio storage size

### Monthly Checks

- [ ] Review user feedback
- [ ] Consider model upgrade (tiny → base)
- [ ] Optimize storage (compress old files)

---

## Troubleshooting

### Issue: Model download fails

**Solution:**
```bash
# Manually download model
cd storage/app/whisper-models
wget https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-tiny.en.bin
```

### Issue: FFI not enabled

**Solution:**
```bash
# Edit php.ini
sudo nano /etc/php/8.3/fpm/php.ini

# Add or uncomment:
extension=ffi

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

### Issue: Storage permission denied

**Solution:**
```bash
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
```

### Issue: High memory usage

**Solution:**
```env
# Use smaller model
WHISPER_MODEL=tiny.en

# Reduce threads
WHISPER_THREADS=2

# Enable queue
WHISPER_QUEUE_ENABLED=true
```

---

## Security Checklist

- [x] CSRF protection enabled
- [x] File type validation
- [x] File size limits
- [x] Authentication required
- [x] User-specific storage
- [x] No sensitive data in logs
- [x] HTTPS enabled (production)

---

## Performance Optimization

### Enable Queue Processing

1. Update `.env`:
```env
WHISPER_QUEUE_ENABLED=true
QUEUE_CONNECTION=redis
```

2. Start queue worker:
```bash
php artisan queue:work --queue=transcription
```

3. Use supervisor to keep worker running:
```ini
[program:laravel-worker]
command=php /path/to/artisan queue:work --queue=transcription
autostart=true
autorestart=true
```

### Use Faster Model (if needed)

```env
# Faster but less accurate
WHISPER_MODEL=tiny.en

# Slower but more accurate
WHISPER_MODEL=base.en
```

---

## Documentation

### User Documentation

Create user guide:
- How to record audio
- How to transcribe
- Tips for best results
- Troubleshooting

### Developer Documentation

- [x] Requirements document
- [x] Implementation summary
- [x] Quick start guide
- [x] Test plan
- [x] Deployment checklist

---

## Success Metrics

Track these metrics:

- **Usage Rate**: % of bookings using speech-to-text
- **Success Rate**: % of successful transcriptions
- **Accuracy**: User satisfaction with transcription quality
- **Performance**: Average processing time
- **Errors**: Error rate and types

Target Metrics:
- Usage Rate: >30%
- Success Rate: >95%
- Accuracy: >85%
- Processing Time: <15 seconds per minute
- Error Rate: <5%

---

## Sign-Off

- [ ] Code reviewed by: ___________
- [ ] Testing completed by: ___________
- [ ] Deployment approved by: ___________
- [ ] Production deployment date: ___________

---

## Contact

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Review documentation
- Check GitHub issues: https://github.com/CodeWithKyrian/whisper.php/issues

---

**Deployment Status**: ✅ READY

**Last Updated**: January 16, 2026
