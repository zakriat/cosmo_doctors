# Draft Appointments - Resume Booking Feature

## ✅ Implementation Complete

### Date: February 18, 2026

---

## 📋 What Was Implemented

### 1. Database Layer
- ✅ Migration: `2026_02_18_110140_create_draft_appointments_table.php`
- ✅ Table: `draft_appointments` with proper foreign keys
- ✅ Fields: user_id, service_id, category_id, clinic_id, doctor_id, appointment_date, appointment_time, current_step, booking_data, expires_at

### 2. Backend (Laravel)
- ✅ Model: `Modules/Appointment/Models/DraftAppointment.php`
  - Relationships: user, service, category, clinic, doctor
  - Scopes: active(), expired(), forUser()
  - Attributes: progress_percentage, step_name, days_until_expiration
  - Auto-sets expires_at to 7 days from creation

- ✅ Controller: `Modules/Frontend/Http/Controllers/DraftAppointmentController.php`
  - `saveDraft()` - Create/update draft
  - `getDraft($id)` - Get single draft
  - `getUserDrafts()` - List user's drafts
  - `deleteDraft($id)` - Delete draft
  - `deleteDraftAfterBooking()` - Cleanup after successful booking

- ✅ Routes: Added to `Modules/Frontend/routes/web.php`
  - POST `/api/draft-appointments` - Save draft
  - GET `/api/draft-appointments/{id}` - Get draft
  - GET `/api/draft-appointments` - List drafts
  - DELETE `/api/draft-appointments/{id}` - Delete draft
  - POST `/api/draft-appointments/cleanup` - Cleanup after booking

- ✅ Console Command: `Modules/Appointment/Console/Commands/CleanupExpiredDrafts.php`
  - Command: `php artisan drafts:cleanup`
  - Scheduled: Daily at midnight
  - Deletes drafts older than 7 days

- ✅ Scheduler: Added to `app/Console/Kernel.php`
  - Runs `drafts:cleanup` daily

### 3. Frontend (JavaScript)
- ✅ Core Module: `public/js/draft-appointment.js`
  - Auto-save functionality with debouncing
  - Resume draft from URL parameter
  - Delete draft after successful booking
  - Visual indicators

- ✅ Integration: `public/js/draft-appointment-integration.js`
  - Hooks into appointment.js functions
  - Hooks into enhanced-booking.js functions
  - Auto-saves on state changes
  - Intercepts successful booking to cleanup draft

- ✅ Scripts loaded in: `Modules/Frontend/Resources/views/booking.blade.php`

### 4. UI Components
- ✅ Draft Card: `Modules/Frontend/Resources/views/components/card/draft_appointment_card.blade.php`
  - Shows draft badge and progress
  - Displays booking details
  - "Continue Booking" button
  - "Delete Draft" button
  - Progress bar
  - Expiration countdown

- ✅ Appointments List: Modified `Modules/Frontend/Resources/views/appointments.blade.php`
  - Shows drafts at the top
  - Separates drafts from confirmed appointments
  - Count of incomplete bookings

- ✅ Controller Update: Modified `Modules/Frontend/Http/Controllers/AppointmentController.php`
  - `appointmentList()` now fetches and passes drafts to view

---

## 🔄 How It Works

### Auto-Save Flow:
1. User selects category → Draft saved (Step 0, 25% complete)
2. User selects clinic → Draft updated (Step 1, 50% complete)
3. User selects doctor → Draft updated (Step 2, 75% complete)
4. User selects date/time → Draft updated (Step 3, 90% complete)
5. User closes browser → Draft remains in database

### Resume Flow:
1. User visits "My Appointments"
2. Sees incomplete booking with "Continue Booking" button
3. Clicks button → Redirected to `/booking/{service_id}?resume={draft_id}`
4. JavaScript loads draft data
5. State is restored
6. User jumps to the step they were on
7. User completes booking

### Completion Flow:
1. User completes payment
2. Appointment is created
3. Draft is automatically deleted
4. User sees success message

### Cleanup Flow:
1. Scheduler runs daily at midnight
2. Command finds drafts older than 7 days
3. Expired drafts are deleted
4. Logs cleanup activity

---

## 🎯 Features Implemented

### ✅ Auto-Save
- Saves draft after each step
- Debounced to avoid excessive API calls
- Works with both original and enhanced booking flows

### ✅ Visual Indicators
- Draft badge (yellow/warning color)
- Progress percentage (25%, 50%, 75%, 90%)
- Progress bar
- Step name ("Stopped at: Doctor Selection")
- Expiration countdown ("Expires in: 5 days")
- Last updated timestamp

### ✅ Resume Functionality
- URL parameter: `?resume={draft_id}`
- Restores all selections
- Jumps to correct step
- Pre-fills form data

### ✅ Draft Management
- List all user's drafts
- Delete individual drafts
- Auto-delete after successful booking
- Auto-delete expired drafts (7 days)

### ✅ User Experience
- Subtle "Progress saved" indicator
- Smooth transitions
- No interruption to booking flow
- Works seamlessly with existing code

---

## 📁 Files Created/Modified

### Created:
1. `database/migrations/2026_02_18_110140_create_draft_appointments_table.php`
2. `Modules/Appointment/Models/DraftAppointment.php`
3. `Modules/Frontend/Http/Controllers/DraftAppointmentController.php`
4. `Modules/Appointment/Console/Commands/CleanupExpiredDrafts.php`
5. `public/js/draft-appointment.js`
6. `public/js/draft-appointment-integration.js`
7. `Modules/Frontend/Resources/views/components/card/draft_appointment_card.blade.php`

### Modified:
1. `Modules/Frontend/routes/web.php` - Added draft routes
2. `app/Console/Kernel.php` - Added scheduled cleanup
3. `Modules/Frontend/Http/Controllers/AppointmentController.php` - Added drafts to list
4. `Modules/Frontend/Resources/views/appointments.blade.php` - Display drafts
5. `Modules/Frontend/Resources/views/booking.blade.php` - Load draft scripts

---

## 🧪 Testing Checklist

### Manual Testing:
- [ ] Start booking, select category, close browser
- [ ] Go to "My Appointments", see draft
- [ ] Click "Continue Booking", verify state restored
- [ ] Complete booking, verify draft deleted
- [ ] Start booking, select clinic, close browser
- [ ] Verify draft shows 50% progress
- [ ] Delete draft manually, verify it's removed
- [ ] Run `php artisan drafts:cleanup`, verify old drafts deleted

### API Testing:
```bash
# Save draft
curl -X POST http://localhost/api/draft-appointments \
  -H "Content-Type: application/json" \
  -d '{"service_id": 59, "category_id": 1, "current_step": 0}'

# Get drafts
curl http://localhost/api/draft-appointments

# Delete draft
curl -X DELETE http://localhost/api/draft-appointments/1
```

---

## 🚀 Next Steps (Optional Enhancements)

### Not Implemented (As Per Requirements):
- ❌ Edge case handling (expired slots, unavailable doctors)
- ❌ Price change validation
- ❌ Slot availability re-check

### Future Enhancements (If Needed):
1. Email notifications for abandoned bookings
2. Dashboard notification banner
3. SMS reminders
4. Draft analytics (conversion rate)
5. A/B testing for reminder timing

---

## 📊 Database Schema

```sql
CREATE TABLE `draft_appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `service_id` bigint unsigned NULL,
  `category_id` bigint unsigned NULL,
  `clinic_id` bigint unsigned NULL,
  `doctor_id` bigint unsigned NULL,
  `appointment_date` date NULL,
  `appointment_time` time NULL,
  `current_step` tinyint NOT NULL DEFAULT 0 COMMENT '0=category, 1=clinic, 2=doctor, 3=datetime',
  `booking_data` json NULL COMMENT 'Stores complete state object',
  `expires_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  `deleted_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `draft_appointments_user_id_index` (`user_id`),
  KEY `draft_appointments_expires_at_index` (`expires_at`),
  CONSTRAINT `draft_appointments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `draft_appointments_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `clinics_services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `draft_appointments_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `clinics_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `draft_appointments_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinic` (`id`) ON DELETE CASCADE,
  CONSTRAINT `draft_appointments_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

---

## 🎉 Summary

The Resume Appointment functionality is now fully implemented! Users can:
- Start booking and abandon it at any step
- See incomplete bookings in their appointment list
- Resume from where they left off
- Have drafts automatically cleaned up after 7 days

The implementation is modular, follows Laravel best practices, and integrates seamlessly with your existing booking flow.
