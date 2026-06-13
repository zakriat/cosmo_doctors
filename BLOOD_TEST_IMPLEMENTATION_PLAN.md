# Blood Test Booking System - Implementation Plan

## Project Overview
Integration of Gravity Forms blood test bookings into the existing KiviCare appointment system. Blood tests will be stored as a type of appointment in the existing `appointments` table.

---

## Database Analysis

### Existing Appointments Table Structure
```
✅ user_id (bigint, nullable) - Patient link
✅ clinic_id (bigint, nullable) - Can be null for blood tests
✅ doctor_id (bigint, nullable) - Can be null for blood tests  
✅ service_id (bigint, nullable) - Can be null for blood tests
✅ status (varchar) - Reusable: pending, confirmed, completed, cancelled, checkout
✅ appointment_date (datetime, nullable)
✅ appointment_time (time, nullable)
✅ appointment_extra_info (longtext, nullable) - Store blood test details
✅ total_amount (double) - Can be 0 or set later
✅ created_at, updated_at, deleted_at
```

### New Columns to Add
```sql
type                      ENUM('appointment', 'blood_test') DEFAULT 'appointment'
gf_entry_id              VARCHAR(191) UNIQUE NULLABLE
initiated_from_dashboard BOOLEAN DEFAULT false
test_type                VARCHAR(191) NULLABLE
raw_gf_data              JSON NULLABLE
synced_at                TIMESTAMP NULLABLE
```

---

## Implementation Checklist

### Phase 1: Core Infrastructure (2-3 hours)

#### 1.1 Gravity Forms Service
**File:** `app/Services/GravityFormsService.php`

**Methods:**
- `getForms()` - Fetch all forms
- `getEntries($formId, $params)` - Fetch paginated entries
- `getEntry($entryId)` - Fetch single entry
- `getFormFields($formId)` - Get form structure (cached 1 hour)

**Config:**
```php
// config/services.php
'gravity_forms' => [
    'api_url' => env('GF_API_URL'),
    'consumer_key' => env('GF_CONSUMER_KEY'),
    'consumer_secret' => env('GF_CONSUMER_SECRET'),
    'form_id' => env('GF_BLOOD_TEST_FORM_ID', 1),
],
```

**Environment Variables:**
```env
GF_API_URL=https://cosmodoctors.com/wp-json/gf/v2
GF_CONSUMER_KEY=ck_xxx
GF_CONSUMER_SECRET=cs_xxx
GF_BLOOD_TEST_FORM_ID=1
```

#### 1.2 Database Migration
**File:** `database/migrations/2026_03_02_add_blood_test_fields_to_appointments.php`

```php
Schema::table('appointments', function (Blueprint $table) {
    $table->enum('type', ['appointment', 'blood_test'])
          ->default('appointment')
          ->after('status');
    
    $table->string('gf_entry_id')->unique()->nullable()->after('type');
    $table->boolean('initiated_from_dashboard')->default(false)->after('gf_entry_id');
    $table->string('test_type')->nullable()->after('initiated_from_dashboard');
    $table->json('raw_gf_data')->nullable()->after('test_type');
    $table->timestamp('synced_at')->nullable()->after('raw_gf_data');
});
```

#### 1.3 Update Appointment Model
**File:** `Modules/Appointment/Models/Appointment.php`

**Add to $fillable:**
```php
'type', 'gf_entry_id', 'initiated_from_dashboard', 'test_type', 'raw_gf_data', 'synced_at'
```

**Add to $casts:**
```php
'initiated_from_dashboard' => 'boolean',
'raw_gf_data' => 'array',
'synced_at' => 'datetime',
```

**Add scopes:**
```php
public function scopeBloodTests($query) {
    return $query->where('type', 'blood_test');
}

public function scopeRegularAppointments($query) {
    return $query->where('type', 'appointment');
}

public function scopeFromDashboard($query) {
    return $query->where('initiated_from_dashboard', true);
}
```

---

### Phase 2: Sync Command (2 hours)

#### 2.1 Artisan Command
**File:** `app/Console/Commands/SyncBloodTestAppointments.php`

**Signature:** `gf:sync-blood-tests`

**Logic Flow:**
1. Fetch GF form structure (cached)
2. Map field IDs to column names
3. Fetch all entries (paginated)
4. For each entry:
   - Check if dashboard-initiated booking exists (by email, no gf_entry_id)
   - If yes: Update existing record with GF data
   - If no: Create new appointment record
5. Output summary

**Key Features:**
- Pagination handling
- Email-based patient matching
- Dashboard booking linking
- Error handling with try/catch
- Detailed logging

#### 2.2 Schedule Command
**File:** `app/Console/Kernel.php`

```php
$schedule->command('gf:sync-blood-tests')
         ->everyFifteenMinutes()
         ->withoutOverlapping();
```

---

### Phase 3: Patient Dashboard Integration (1-2 hours)

#### 3.1 Add "Book Blood Test" Button
**File:** `Modules/Frontend/Resources/views/appointments.blade.php`

**Location:** Below existing "Book Appointment" button

```blade
<a href="{{ route('frontend.blood-test.book') }}" 
   class="btn btn-outline-danger w-100 mt-2">
    🩸 Book a Blood Test
</a>
```

#### 3.2 Blood Test Booking Controller
**File:** `Modules/Frontend/Http/Controllers/AppointmentController.php`

**New Method:** `bookBloodTest()`

**Logic:**
1. Pre-create appointment record:
   ```php
   Appointment::create([
       'type' => 'blood_test',
       'user_id' => auth()->id(),
       'initiated_from_dashboard' => true,
       'status' => 'pending',
       // ... patient details
   ]);
   ```
2. Show GF form (iframe or native)
3. Pass booking ID for reference

#### 3.3 Booking View
**File:** `Modules/Frontend/Resources/views/blood_test_booking.blade.php`

**Options:**
- **Option A (Preferred):** Iframe embed
- **Option B (Fallback):** Native form with GF API submission

---

### Phase 4: Admin Panel Integration (2-3 hours)

#### 4.1 Backend Controller Updates
**File:** `Modules/Appointment/Http/Controllers/Backend/ClinicAppointmentController.php`

**Changes:**
- Add type filter to index query
- Add manual sync button handler
- Support blood test status updates

**Filter Logic:**
```php
if ($request->has('type')) {
    if ($request->type === 'blood_test') {
        $query->bloodTests();
    } elseif ($request->type === 'appointment') {
        $query->regularAppointments();
    }
    // 'all' = no filter
}
```

#### 4.2 Admin View Updates
**File:** `Modules/Appointment/Resources/views/backend/appointment/index_datatable.blade.php`

**Changes:**
1. Add "Type" column with badges:
   - Appointment: Blue badge
   - Blood Test: Red badge with 🩸 icon

2. Add filter tabs:
   ```blade
   <ul class="nav nav-tabs">
       <li><a href="?type=all">All</a></li>
       <li><a href="?type=appointment">Appointments</a></li>
       <li><a href="?type=blood_test">Blood Tests</a></li>
   </ul>
   ```

3. Add manual sync button:
   ```blade
   <button onclick="syncBloodTests()">
       <i class="fa fa-sync"></i> Sync Blood Tests
   </button>
   ```

4. Show patient link for blood tests (same as appointments)

---

### Phase 5: Routes (30 minutes)

#### Frontend Routes
**File:** `Modules/Frontend/routes/web.php`

```php
Route::middleware(['auth', 'user_check'])->group(function () {
    Route::get('/blood-test/book', [AppointmentController::class, 'bookBloodTest'])
         ->name('frontend.blood-test.book');
});
```

#### Backend Routes
**File:** `Modules/Appointment/routes/web.php`

```php
Route::middleware(['auth', 'auth_check'])->group(function () {
    Route::post('/admin/blood-tests/sync', [ClinicAppointmentController::class, 'syncBloodTests'])
         ->name('backend.blood-tests.sync');
});
```

---

## Data Flow Diagrams

### Flow 1: Dashboard-Initiated Blood Test
```
Patient Dashboard
    ↓ (clicks "Book Blood Test")
Pre-create Appointment
    - type: blood_test
    - user_id: [patient]
    - initiated_from_dashboard: true
    - status: pending
    ↓
Show GF Form (iframe/native)
    ↓ (patient submits)
GF Entry Created
    ↓ (sync command runs)
Match by Email
    ↓
Update Existing Appointment
    - gf_entry_id: [entry ID]
    - raw_gf_data: [full entry]
    - synced_at: now()
```

### Flow 2: External GF Submission
```
WordPress GF Form
    ↓ (visitor submits)
GF Entry Created
    ↓ (sync command runs)
Check for Dashboard Booking
    ↓ (not found)
Create New Appointment
    - type: blood_test
    - gf_entry_id: [entry ID]
    - user_id: [matched by email or null]
    - initiated_from_dashboard: false
    - status: pending
```

---

## Field Mapping Strategy

### GF Form Fields → Appointment Columns
```php
// Example mapping (adjust based on actual GF form)
$fieldMap = [
    '1' => 'patient_name',      // Name field
    '2' => 'patient_email',     // Email field
    '3' => 'patient_phone',     // Phone field
    '4' => 'test_type',         // Blood test type
    '5' => 'appointment_date',  // Preferred date
    '6' => 'appointment_time',  // Preferred time
    '7' => 'appointment_extra_info', // Notes
];
```

**Dynamic Mapping:**
1. Fetch form structure via `getFormFields(1)`
2. Build map by matching field labels
3. Cache for 1 hour
4. Use map to transform entries

---

## Patient Linking Logic

### Priority Order:
1. **Direct FK** (dashboard-initiated): `user_id` is set
2. **Email match**: Find user by email
3. **Phone match**: Secondary fallback
4. **Unlinked**: External visitor (user_id = null)

### Sync Command Logic:
```php
// Try to find dashboard-initiated booking
$existing = Appointment::where('type', 'blood_test')
    ->where('user_id', $userId) // or email match
    ->where('initiated_from_dashboard', true)
    ->whereNull('gf_entry_id')
    ->latest()
    ->first();

if ($existing) {
    // Link GF entry to existing booking
    $existing->update([
        'gf_entry_id' => $entryId,
        'raw_gf_data' => $entry,
        'synced_at' => now(),
    ]);
} else {
    // Create new appointment from GF entry
    Appointment::create([...]);
}
```

---

## Testing Checklist

### Unit Tests
- [ ] GravityFormsService API calls
- [ ] Field mapping logic
- [ ] Patient matching logic
- [ ] Sync command execution

### Integration Tests
- [ ] Dashboard booking flow
- [ ] GF form submission
- [ ] Sync command with real data
- [ ] Admin filtering
- [ ] Status updates

### Manual Testing
- [ ] Book blood test from dashboard
- [ ] Submit GF form externally
- [ ] Run sync command manually
- [ ] View in admin panel
- [ ] Filter by type
- [ ] Update status
- [ ] Check patient links

---

## Error Handling

### GF API Failures
```php
try {
    $response = Http::withBasicAuth($key, $secret)->get($url);
} catch (\Exception $e) {
    Log::error('GF API failed', ['error' => $e->getMessage()]);
    return []; // Return empty array, don't break app
}
```

### Sync Command Failures
- Log all errors
- Continue processing other entries
- Output summary with error count
- Send notification to admin (optional)

### Missing Patient Match
- Store appointment with user_id = null
- Admin can manually link later
- Show in "Unlinked Blood Tests" filter

---

## Performance Considerations

### Caching Strategy
- Form structure: 1 hour cache
- Field mapping: 1 hour cache
- Clear cache on manual sync

### Pagination
- Fetch 100 entries per page
- Loop through all pages
- Use `paging[page_size]` and `paging[current_page]`

### Database Queries
- Use `updateOrCreate` to avoid duplicates
- Eager load relationships in admin view
- Index on `gf_entry_id` (unique)
- Index on `type` for filtering

---

## Security Considerations

### API Credentials
- Store in `.env` (never commit)
- Use Basic Auth with consumer key/secret
- Validate SSL certificates

### Data Sanitization
- Sanitize all GF entry data before storing
- Validate email format
- Validate phone format
- Escape HTML in notes

### Authorization
- Protect admin routes with middleware
- Only authenticated patients can book
- Only admins can sync/view all

---

## Deployment Steps

1. **Backup database**
2. **Add environment variables** to `.env`
3. **Run migration:** `php artisan migrate`
4. **Clear cache:** `php artisan cache:clear`
5. **Test GF API connection:** `php artisan tinker` → test service
6. **Run initial sync:** `php artisan gf:sync-blood-tests`
7. **Verify data** in admin panel
8. **Enable scheduled sync** (cron job)
9. **Test booking flow** from dashboard
10. **Monitor logs** for errors

---

## Rollback Plan

If issues occur:
1. **Revert migration:** `php artisan migrate:rollback`
2. **Remove environment variables**
3. **Clear cache**
4. **Restore database backup** (if needed)

---

## Future Enhancements

### Phase 2 Features (Optional)
- [ ] Webhook integration for instant sync
- [ ] Email notifications for new blood tests
- [ ] SMS reminders
- [ ] Blood test result upload
- [ ] Patient blood test history view
- [ ] Export blood test data
- [ ] Analytics dashboard

---

## Support & Maintenance

### Monitoring
- Check sync command logs daily
- Monitor GF API response times
- Track unlinked blood tests
- Review error logs weekly

### Updates
- Update GF API credentials if changed
- Adjust field mapping if form changes
- Update sync frequency based on volume

---

## Estimated Timeline

| Phase | Task | Time |
|-------|------|------|
| 1 | Core Infrastructure | 2-3 hours |
| 2 | Sync Command | 2 hours |
| 3 | Patient Dashboard | 1-2 hours |
| 4 | Admin Panel | 2-3 hours |
| 5 | Routes & Config | 30 min |
| 6 | Testing | 1-2 hours |
| **Total** | | **8-12 hours** |

---

## Success Criteria

✅ Blood tests appear in admin appointments list
✅ Type filter works (All/Appointments/Blood Tests)
✅ Dashboard booking creates pre-linked appointment
✅ GF sync links entries to correct patients
✅ External submissions create new appointments
✅ Patient links work correctly
✅ Status updates work for blood tests
✅ No breaking changes to existing appointments
✅ All tests pass
✅ Documentation complete

---

## Notes

- **No separate table needed** - Reuses existing appointments infrastructure
- **Backward compatible** - Existing appointments unaffected
- **Modular design** - Easy to extend or remove
- **Production ready** - Error handling, logging, caching included

---

**Document Version:** 1.0
**Last Updated:** 2026-03-02
**Author:** Kiro AI Assistant
