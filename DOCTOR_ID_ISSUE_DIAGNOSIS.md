# Doctor ID Issue - Diagnosis

## Date: February 17, 2026

## Error Summary

**Error:** `Attempt to read property "doctor_id" on null`
**Location:** `Modules/Frontend/Http/Controllers/AppointmentController.php:548`
**Endpoints Failing:**
- `/get-payment-data` - 500 Internal Server Error
- `/slot-time-list` - 500 Internal Server Error

---

## Root Cause Analysis

### The Problem: Wrong Doctor ID Being Sent

**Console shows:**
```
→ Updated state.selectedDoctor: 18
→ Updated state.selectedClinic: 2
```

**But the API expects a different ID!**

### Understanding the Doctor ID Confusion

There are **TWO different doctor IDs** in the system:

1. **`doctors.id`** - Primary key in the `doctors` table (e.g., 18)
2. **`doctors.doctor_id`** - Foreign key to `users.id` (the actual user ID)

### What's Happening:

#### In `enhanced-booking.js`:
```javascript
// We're storing doctors.id (18)
selectedDoctorId = doctor.doctor_id;  // This is from the API response
state.selectedDoctor = 18;  // doctors.id
```

#### In `AppointmentController.php` (line 547-548):
```php
// It tries to find the doctor record by doctors.id
$doctor = Doctor::CheckMultivendor()
    ->where('id', $request->doctor_id)  // Looking for doctors.id = 18
    ->where('status', 1)
    ->first();

// Then tries to get the user ID
$doctor_id = $doctor->doctor_id;  // ← FAILS because $doctor is NULL
```

### Why is $doctor NULL?

Looking at the API response from `/api/categories/68/doctors`:

```json
{
  "doctors": [
    {
      "doctor_id": 18,  // ← This is doctors.id (primary key)
      "user": {
        "id": 9,        // ← This is users.id (the actual doctor_id FK)
        "first_name": "Felix",
        "last_name": "Harris"
      }
    }
  ]
}
```

**The confusion:**
- API returns `doctor_id: 18` which is actually `doctors.id`
- We send `doctor_id: 18` to the backend
- Backend looks for `doctors.id = 18`
- But there's no doctor with `doctors.id = 18` OR the doctor exists but has `status = 0` (inactive)

---

## Verification Needed

### Check 1: Does doctor with ID 18 exist?
```sql
SELECT id, doctor_id, status FROM doctors WHERE id = 18;
```

**Possible outcomes:**
1. No record found → Doctor doesn't exist
2. Record found with `status = 0` → Doctor is inactive
3. Record found with `status = 1` → Doctor exists and is active (different issue)

### Check 2: What's the actual doctor_id (user_id)?
From the console, we know:
- Doctor name: "Dr. Felix Harris"
- User ID: 9 (from the API response structure)

```sql
SELECT d.id, d.doctor_id, d.status, u.first_name, u.last_name 
FROM doctors d
JOIN users u ON d.doctor_id = u.id
WHERE u.first_name = 'Felix' AND u.last_name = 'Harris';
```

---

## The Real Issue

### Scenario 1: API Response Structure is Wrong
The API `/api/categories/68/doctors` might be returning the wrong field.

**Current response:**
```json
{
  "doctor_id": 18  // doctors.id
}
```

**Should be:**
```json
{
  "id": 18,        // doctors.id (primary key)
  "doctor_id": 9   // doctors.doctor_id (user FK)
}
```

### Scenario 2: Doctor Record Issue
- Doctor with `doctors.id = 18` doesn't exist
- OR doctor exists but has `status = 0` (inactive)
- OR doctor exists but is filtered out by `CheckMultivendor()` scope

### Scenario 3: Wrong ID Being Captured
In `enhanced-booking.js`, we're capturing:
```javascript
data-doctor-id="${doctor.doctor_id}"
```

But `doctor.doctor_id` from the API might be the wrong value.

---

## Data Flow Analysis

### Step 1: API Call
```
GET /api/categories/68/doctors
```

### Step 2: API Response (ServiceController)
```php
// What does this return?
$doctors = Doctor::whereHas('category_doctor', function ($query) use ($categoryId) {
    $query->where('category_id', $categoryId);
})->with('user')->get();

// Returns:
[
  {
    "id": ???,           // doctors.id
    "doctor_id": ???,    // doctors.doctor_id (user FK)
    "user": {
      "id": 9,
      "first_name": "Felix",
      "last_name": "Harris"
    }
  }
]
```

### Step 3: Frontend Captures
```javascript
data-doctor-id="${doctor.doctor_id}"  // What value is this?
```

### Step 4: Frontend Sends
```javascript
body: JSON.stringify({
  doctor_id: state.selectedDoctor,  // 18
  clinic_id: state.selectedClinic,  // 2
  service_id: state.selectedService
})
```

### Step 5: Backend Receives
```php
$request->doctor_id  // 18
```

### Step 6: Backend Queries
```php
$doctor = Doctor::CheckMultivendor()
    ->where('id', 18)  // Looking for doctors.id = 18
    ->where('status', 1)
    ->first();  // Returns NULL
```

### Step 7: Error
```php
$doctor_id = $doctor->doctor_id;  // NULL->doctor_id = ERROR
```

---

## Solution Options

### Option 1: Fix API Response Structure
Ensure `/api/categories/68/doctors` returns both IDs clearly:
```json
{
  "id": 18,           // doctors.id
  "doctor_id": 9,     // doctors.doctor_id (user FK)
  "user": {...}
}
```

### Option 2: Fix Frontend Capture
Change what we capture in `enhanced-booking.js`:
```javascript
// Instead of:
data-doctor-id="${doctor.doctor_id}"

// Use:
data-doctor-id="${doctor.id}"  // Capture doctors.id
```

### Option 3: Fix Backend Expectation
Change what the backend expects (not recommended - would break other flows)

### Option 4: Check Doctor Status
The doctor might exist but be inactive:
```sql
UPDATE doctors SET status = 1 WHERE id = 18;
```

---

## Immediate Actions Needed

1. **Check the API response structure**
   - What does `/api/categories/68/doctors` actually return?
   - What is the value of `doctor.doctor_id` in the response?

2. **Check the database**
   - Does doctor with `id = 18` exist?
   - What is the `status` of that doctor?
   - What is the `doctor_id` (user FK) of that doctor?

3. **Check ServiceController**
   - What fields are being returned in the API response?
   - Is the response structure correct?

4. **Verify the data flow**
   - Log what value is being captured in frontend
   - Log what value is being sent to backend
   - Log what the backend query is looking for

---

## Expected vs Actual

### Expected Flow:
```
API returns doctor.id = 18
Frontend captures 18
Frontend sends doctor_id: 18
Backend finds doctors.id = 18
Backend gets doctors.doctor_id = 9
Backend uses 9 for queries
```

### Actual Flow (Broken):
```
API returns doctor.??? = 18
Frontend captures 18
Frontend sends doctor_id: 18
Backend looks for doctors.id = 18
Backend finds NULL (doctor doesn't exist or is inactive)
Backend tries NULL->doctor_id
ERROR: Attempt to read property "doctor_id" on null
```

---

## Next Steps

1. Check ServiceController API response
2. Check database for doctor ID 18
3. Fix the ID mismatch issue
4. Test the complete flow again
