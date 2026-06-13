# Booking Flow Fixes Required

## 🎯 Goal
Fix the booking flow to properly handle:
1. Service → Category → Clinic → Doctor → Date/Time progression
2. Auto-select clinic when only one exists
3. Filter doctors by category (not service)
4. Show doctors only in Step 3, not in Step 2

---

## 📋 Current Issues

### Issue 1: Doctors Showing in Step 2 (Clinic Selection)
**Problem:** When user is on Step 2 (choosing clinic), doctors dropdown appears below the clinic card.

**Location:** `/booking/59?category_id=68`

**What's wrong:**
- Doctors are rendered in the same view as clinic selection
- This is Step 2, but doctors should only appear in Step 3

**Expected behavior:**
- Step 2 shows ONLY clinic selection
- No doctor dropdown visible
- User clicks "Continue" to proceed to Step 3
- Step 3 then shows doctor selection

---

### Issue 2: Doctor Filtering by Service Instead of Category
**Problem:** Doctors are filtered by `service_id` instead of `category_id`.

**Current query (wrong):**
```php
$doctors = Doctor::whereHas('serviceMapping', function($q) use ($serviceId) {
    $q->where('service_id', $serviceId);
});
```

**Correct query (needed):**
```php
$doctors = Doctor::whereHas('categoryMappings', function($q) use ($categoryId) {
    $q->where('category_id', $categoryId)
      ->where('status', 1);
});
```

**Why this matters:**
- Different categories under same service have different doctor assignments
- "Private GP Consultation" might have Dr. A and Dr. B
- "Private Prescriptions" might have only Dr. A
- Must filter by category, not service

---

### Issue 3: Auto-Select Clinic Logic
**Problem:** When only one clinic exists, it should auto-select and auto-proceed to Step 3.

**Current behavior:**
- Shows clinic card
- User must manually click "Continue"
- Doctors appear in same view (wrong)

**Expected behavior (Option B):**
1. Show Step 2 with clinic card
2. Auto-select the clinic (checked/highlighted)
3. Show brief message: "Only one clinic available, auto-selected"
4. After 1-2 seconds, automatically proceed to Step 3
5. OR show "Continue" button that's immediately enabled
6. Smooth transition to doctor selection

---

## 🔧 Required Fixes

### Fix 1: Separate Doctor Selection into Step 3

**File:** `Modules/Frontend/Resources/views/booking.blade.php`

**What to do:**
1. Find where doctors are rendered in Step 2 (clinic selection)
2. Remove doctor dropdown from Step 2 content
3. Move doctor selection to Step 3 content only
4. Ensure doctors only render when `$currentStep >= 2` (Step 3 in 0-indexed)

**Pseudo-code:**
```blade
{{-- Step 2: Clinic Selection --}}
<div id="step-content-1" class="step-content">
    {{-- Show clinic cards --}}
    {{-- NO doctor dropdown here! --}}
    <button id="continue-to-doctors">Continue</button>
</div>

{{-- Step 3: Doctor Selection --}}
<div id="step-content-2" class="step-content">
    {{-- Show doctor cards/dropdown here --}}
    {{-- Filtered by category_id --}}
</div>
```

---

### Fix 2: Filter Doctors by Category

**File:** `Modules/Frontend/Http/Controllers/ServiceController.php`

**Method:** `booking()` or wherever doctors are loaded

**What to do:**
1. Find the doctor query
2. Change from filtering by `service_id` to `category_id`
3. Use `doctor_category_mappings` table
4. Join with `doctors` table to get doctor details

**Current code (to find and replace):**
```php
// WRONG - filters by service
$doctors = Doctor::whereHas('serviceMapping', function($q) use ($serviceId) {
    $q->where('service_id', $serviceId);
})->get();
```

**New code (correct):**
```php
// CORRECT - filters by category
$doctors = Doctor::whereHas('categoryMappings', function($q) use ($categoryId, $clinicId) {
    $q->where('category_id', $categoryId)
      ->where('clinic_id', $clinicId)
      ->where('status', 1);
})->with('user')->get();
```

**Alternative using direct query:**
```php
$doctorIds = \Modules\Clinic\Models\DoctorCategoryMapping::where('category_id', $categoryId)
    ->where('clinic_id', $clinicId)
    ->where('status', 1)
    ->pluck('doctor_id');

$doctors = Doctor::whereIn('doctor_id', $doctorIds)
    ->with('user')
    ->get();
```

---

### Fix 3: Auto-Select Single Clinic

**File:** `Modules/Frontend/Http/Controllers/ServiceController.php`

**Method:** `booking()`

**What to do:**
1. Check if only one clinic exists
2. If yes, auto-select it and set flag
3. Pass flag to view
4. View uses JavaScript to auto-proceed to Step 3

**Backend code:**
```php
// In booking() method
$clinics = Clinics::where('status', 1)->get();
$autoSelectClinic = $clinics->count() === 1;
$selectedClinicId = $autoSelectClinic ? $clinics->first()->id : null;

return view('frontend::booking', [
    'clinics' => $clinics,
    'autoSelectClinic' => $autoSelectClinic,
    'selectedClinicId' => $selectedClinicId,
    // ... other data
]);
```

**Frontend JavaScript:**
```javascript
// In booking.blade.php
@if($autoSelectClinic)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-select the clinic
        const clinicId = {{ $selectedClinicId }};
        
        // Show message
        console.log('Auto-selecting clinic:', clinicId);
        
        // Wait 1.5 seconds, then proceed to next step
        setTimeout(function() {
            // Trigger next step
            window.location.href = '{{ url()->current() }}?category_id={{ $categoryId }}&clinic_id=' + clinicId;
        }, 1500);
    });
</script>
@endif
```

---

## 📊 Step-by-Step Implementation Plan

### Step 1: Fix Doctor Filtering
1. Open `ServiceController.php`
2. Find where doctors are queried
3. Change from `service_id` to `category_id` filtering
4. Use `doctor_category_mappings` table
5. Test: Verify doctors are filtered by category

### Step 2: Separate Doctor Selection
1. Open `booking.blade.php`
2. Find Step 2 content (clinic selection)
3. Remove any doctor dropdown/selection from Step 2
4. Ensure doctors only appear in Step 3 content
5. Test: Verify doctors don't show in Step 2

### Step 3: Implement Auto-Select
1. In `ServiceController.php`, detect single clinic
2. Pass `autoSelectClinic` flag to view
3. In `booking.blade.php`, add JavaScript for auto-proceed
4. Show brief message to user
5. Auto-redirect to Step 3 after 1-2 seconds
6. Test: Verify smooth transition

### Step 4: Update Step Labels
1. Ensure step indicators show correct labels:
   - Step 1: "Select Category"
   - Step 2: "Choose Clinic"
   - Step 3: "Choose Doctor"
   - Step 4: "Choose Date, Time & Payment"

---

## 🧪 Testing Checklist

### Test 1: Category Selection
- [ ] Go to `/services`
- [ ] Click "Book Now" on a service
- [ ] Verify redirects to `/booking/{serviceId}`
- [ ] Verify shows category selection
- [ ] Select a category
- [ ] Verify URL becomes `/booking/{serviceId}?category_id={categoryId}`

### Test 2: Clinic Selection (Single Clinic)
- [ ] After selecting category, verify shows clinic card
- [ ] Verify clinic is auto-selected (highlighted)
- [ ] Verify NO doctor dropdown appears
- [ ] Verify auto-proceeds to Step 3 after 1-2 seconds
- [ ] OR verify "Continue" button works
- [ ] Verify URL becomes `/booking/{serviceId}?category_id={categoryId}&clinic_id={clinicId}`

### Test 3: Doctor Selection
- [ ] Verify Step 3 shows doctor selection
- [ ] Verify doctors are filtered by selected category
- [ ] Verify only assigned doctors appear
- [ ] Select a doctor
- [ ] Verify URL becomes `/booking/{serviceId}?category_id={categoryId}&clinic_id={clinicId}&doctor_id={doctorId}`

### Test 4: Date/Time Selection
- [ ] Verify Step 4 shows calendar and time slots
- [ ] Select date and time
- [ ] Complete booking
- [ ] Verify appointment is created with correct category, clinic, and doctor

### Test 5: Multiple Clinics (If Applicable)
- [ ] Add second clinic to database
- [ ] Repeat booking flow
- [ ] Verify Step 2 shows both clinics
- [ ] Verify user must manually select clinic
- [ ] Verify no auto-proceed happens
- [ ] Verify doctors still filtered by category

---

## 📁 Files to Modify

### 1. ServiceController.php
**Path:** `Modules/Frontend/Http/Controllers/ServiceController.php`

**Changes:**
- Update doctor query to filter by `category_id`
- Add auto-select clinic logic
- Pass flags to view

### 2. booking.blade.php
**Path:** `Modules/Frontend/Resources/views/booking.blade.php`

**Changes:**
- Remove doctor dropdown from Step 2
- Move doctor selection to Step 3 only
- Add auto-select JavaScript
- Update step visibility logic

### 3. booking.js (if exists)
**Path:** `public/js/booking.js` or inline in blade

**Changes:**
- Add auto-proceed logic
- Handle step transitions
- Update URL parameters

---

## 🎯 Expected Final Flow

```
User Journey:
1. Browse services at /services
2. Click "Book Now" on "Private GP Services"
3. See category selection (Step 1)
4. Select "Private GP Consultation - £80"
5. See clinic card "Harmony Medical Center" (Step 2)
   - Auto-selected (only one clinic)
   - Brief pause (1-2 seconds)
   - Auto-proceeds to Step 3
6. See doctor selection (Step 3)
   - Shows only doctors assigned to "Private GP Consultation"
   - Dr. Felix Harris, Dr. Jorge Perez
7. Select doctor
8. Choose date/time (Step 4)
9. Complete booking
```

**URL progression:**
```
/services
  ↓
/booking/59
  ↓
/booking/59?category_id=68
  ↓ (auto-proceed)
/booking/59?category_id=68&clinic_id=2
  ↓
/booking/59?category_id=68&clinic_id=2&doctor_id=12
  ↓
/appointment-confirmation
```

---

## 🚀 Ready to Implement

All requirements are clear:
1. ✅ Separate doctor selection into Step 3
2. ✅ Filter doctors by category (not service)
3. ✅ Auto-select single clinic with smooth transition
4. ✅ Proper step progression

Should I proceed with implementing these fixes?
