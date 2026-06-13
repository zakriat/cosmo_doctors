# Service Structure Guide - KiviCare System

## 📊 System Overview

Your KiviCare system uses a **2-LEVEL HIERARCHY**:

```
System Service (clinics_services table)
    ↓
Categories (clinics_categories table)
    ↓
Patients book categories!
```

---

## 🏗️ Database Structure

### Table 1: `clinics_services`
**Purpose:** Stores system services (top-level services)

**Key Columns:**
- `id` - Service ID
- `system_service_id` - Parent service ID (NULL for top-level)
- `name` - Service name
- `charges` - Service charges
- `category_id` - Links to a category (optional)
- `subcategory_id` - Links to a subcategory (optional)
- `status` - Active/Inactive

### Table 2: `clinics_categories`
**Purpose:** Stores bookable categories under services

**Key Columns:**
- `id` - Category ID
- `name` - Category name
- `parent_id` - Links to system service ID in `clinics_services`
- `price` - Category price (what patients pay)
- `service_classification` - doctor_required | no_doctor_required | doctor_optional
- `status` - Active/Inactive

### Table 3: `doctor_category_mapping`
**Purpose:** Assigns doctors to categories

**Key Columns:**
- `doctor_id` - User ID of the doctor
- `category_id` - Category ID from `clinics_categories`
- `clinic_id` - Clinic ID
- `status` - Active/Inactive

### Table 4: `doctor_service_mapping`
**Purpose:** Assigns doctors to services (legacy/additional)

**Key Columns:**
- `doctor_id` - User ID of the doctor
- `service_id` - Service ID from `clinics_services`
- `clinic_id` - Clinic ID
- `charges` - Custom charges (optional)
- `status` - Active/Inactive

---

## 📋 Current Data Analysis

### System Services (Top Level)
You have **12 top-level services**, including:
- **Private GP Services** (IDs: 51, 55, 59) - 3 instances
- **Specialist Services** (IDs: 50, 54, 58) - 3 instances
- **Private Scans & Imaging** (IDs: 53, 57, 61) - 3 instances
- **Blood Tests & Laboratory** (IDs: 52, 56, 60) - 3 instances

**Note:** You have 3 copies of each service, likely for different clinics or purposes.

### Categories Under "Private GP Services"

**Service ID 59** has these categories:
- Category ID 68: "Private GP Services" - £80.00 (doctor_required)
- Category ID 70: "Private Prescriptions" - £30.00 (doctor_required)
- Category ID 71: "Private Contraception" - £60.00 (doctor_required)
- Category ID 72: "Hayfever Treatment" - £50.00 (doctor_required)
- Category ID 69: "Visa Medicals" - £150.00 (doctor_required)

### Doctor Assignments

**Dr. Felix Harris (User ID: 18)** is assigned to:
- **Service ID 59**: "Private GP Services"
- **Category ID 68**: "Private GP Services" (£80)
- **Category ID 59**: "Pregnancy Ultrasound" (£120)
- **Clinic ID 2**: Harmony Medical Center

---

## ✅ How to Create Services & Categories

### Method 1: Using Admin Panel

#### Step 1: Create a System Service
1. Go to: `http://127.0.0.1:8000/app/service`
2. Click "Add New Service"
3. Fill in:
   - **system service**: Leave EMPTY (or don't select anything)
   - **Name**: "Private GP Services"
   - **Description**: "Private GP consultation services"
   - **Charges**: 0 (parent services usually have no charge)
   - **Status**: Active
4. Save

#### Step 2: Create Categories Under the Service
Categories are created in the `clinics_categories` table. You need to:

**Option A: Through Database/Tinker**
```bash
php artisan tinker --execute="
\Modules\Clinic\Models\ClinicsCategory::create([
    'name' => 'Private GP Consultation',
    'parent_id' => 59,  # Your system service ID
    'price' => 80.00,
    'service_classification' => 'doctor_required',
    'status' => 1
]);
echo 'Category created';
"
```

**Option B: Check if Admin Panel Has Category Creation**
- Look for a "Categories" or "Service Categories" menu
- Or check if there's an "Add Category" button on the service edit page

#### Step 3: Assign Doctor to Category
```bash
php artisan tinker --execute="
\Modules\Clinic\Models\DoctorCategoryMapping::create([
    'doctor_id' => 18,  # Dr. Felix Harris
    'category_id' => 68,  # Private GP Services category
    'clinic_id' => 2,  # Harmony Medical Center
    'status' => 1
]);
echo 'Doctor assigned to category';
"
```

#### Step 4: Also Assign Doctor to Parent Service (Optional but Recommended)
```bash
php artisan tinker --execute="
\Modules\Clinic\Models\DoctorServiceMapping::create([
    'doctor_id' => 18,
    'service_id' => 59,  # Private GP Services system service
    'clinic_id' => 2,
    'charges' => 0,
    'status' => 1
]);
echo 'Doctor assigned to service';
"
```

---

## 🎯 How Booking Works

### Booking Flow:
1. Patient goes to: `http://127.0.0.1:8000/booking/59`
   - `59` = System Service ID ("Private GP Services")

2. System loads categories under service 59:
   - "Private GP Services" (£80)
   - "Private Prescriptions" (£30)
   - "Hayfever Treatment" (£50)
   - etc.

3. Patient selects a category (e.g., "Private GP Services")

4. If category has `service_classification = 'doctor_required'`:
   - System loads doctors assigned to that category
   - Patient selects a doctor

5. Patient selects date & time

6. Booking is created with:
   - Service ID: 59
   - Category ID: 68
   - Doctor ID: 18
   - Clinic ID: 2 (auto-selected)

---

## 🔧 Common Tasks

### Task 1: Add a New Category to Existing Service

**Example: Add "Telephone Consultation" to "Private GP Services" (ID 59)**

```bash
php artisan tinker --execute="
\Modules\Clinic\Models\ClinicsCategory::create([
    'name' => 'Telephone Consultation',
    'parent_id' => 59,
    'price' => 50.00,
    'service_classification' => 'doctor_required',
    'description' => 'Phone consultation with GP',
    'status' => 1
]);
echo 'Category created';
"
```

### Task 2: Assign Doctor to New Category

```bash
# First, get the category ID
php artisan tinker --execute="
echo \Modules\Clinic\Models\ClinicsCategory::where('name', 'Telephone Consultation')->first()->id;
"

# Then assign doctor (replace 84 with actual category ID)
php artisan tinker --execute="
\Modules\Clinic\Models\DoctorCategoryMapping::create([
    'doctor_id' => 18,
    'category_id' => 84,
    'clinic_id' => 2,
    'status' => 1
]);
echo 'Doctor assigned';
"
```

### Task 3: Create a Completely New Service with Categories

```bash
# Step 1: Create system service
php artisan tinker --execute="
\$service = \Modules\Clinic\Models\ClinicsService::create([
    'name' => 'Mental Health Services',
    'description' => 'Mental health and wellbeing services',
    'charges' => 0,
    'status' => 1,
    'system_service_id' => null
]);
echo 'Service created with ID: ' . \$service->id;
"

# Step 2: Create categories (replace 62 with your service ID)
php artisan tinker --execute="
\Modules\Clinic\Models\ClinicsCategory::create([
    'name' => 'Counselling Session',
    'parent_id' => 62,
    'price' => 90.00,
    'service_classification' => 'doctor_required',
    'status' => 1
]);
echo 'Category created';
"

# Step 3: Assign doctor
php artisan tinker --execute="
\$categoryId = \Modules\Clinic\Models\ClinicsCategory::where('name', 'Counselling Session')->first()->id;
\Modules\Clinic\Models\DoctorCategoryMapping::create([
    'doctor_id' => 18,
    'category_id' => \$categoryId,
    'clinic_id' => 2,
    'status' => 1
]);
echo 'Doctor assigned';
"
```

---

## 🐛 Troubleshooting

### Issue 1: "No doctors available"
**Cause:** Doctor not assigned to the category

**Solution:**
```bash
# Check if doctor is assigned
php artisan tinker --execute="
echo \Modules\Clinic\Models\DoctorCategoryMapping::where('category_id', 68)->where('doctor_id', 18)->exists() ? 'Assigned' : 'Not assigned';
"

# If not assigned, assign them
php artisan tinker --execute="
\Modules\Clinic\Models\DoctorCategoryMapping::create([
    'doctor_id' => 18,
    'category_id' => 68,
    'clinic_id' => 2,
    'status' => 1
]);
"
```

### Issue 2: "No time slots available"
**Cause:** Doctor doesn't have sessions configured

**Solution:**
```bash
# Check if doctor has sessions
php artisan tinker --execute="
echo \Modules\Clinic\Models\DoctorSession::where('doctor_id', 18)->where('clinic_id', 2)->count() . ' sessions found';
"
```

### Issue 3: Price not showing
**Cause:** Category price is 0 or NULL

**Solution:**
```bash
# Update category price
php artisan tinker --execute="
\Modules\Clinic\Models\ClinicsCategory::where('id', 68)->update(['price' => 80.00]);
echo 'Price updated';
"
```

---

## 📝 Quick Reference Commands

### View all services
```bash
php artisan tinker --execute="
\Modules\Clinic\Models\ClinicsService::whereNull('system_service_id')->get(['id', 'name'])->each(function(\$s) {
    echo \$s->id . ': ' . \$s->name . PHP_EOL;
});
"
```

### View categories for a service
```bash
php artisan tinker --execute="
\Modules\Clinic\Models\ClinicsCategory::where('parent_id', 59)->get(['id', 'name', 'price'])->each(function(\$c) {
    echo \$c->id . ': ' . \$c->name . ' (£' . \$c->price . ')' . PHP_EOL;
});
"
```

### View doctor assignments
```bash
php artisan tinker --execute="
\Modules\Clinic\Models\DoctorCategoryMapping::where('doctor_id', 18)->with('category:id,name')->get()->each(function(\$m) {
    echo 'Category: ' . (\$m->category ? \$m->category->name : 'Unknown') . PHP_EOL;
});
"
```

---

## 🎓 Summary

**Your system structure:**
- **System Services** = Top-level groupings (e.g., "Private GP Services")
- **Categories** = Bookable items under services (e.g., "Private GP Consultation" £80)
- **Doctors** = Assigned to categories via `doctor_category_mapping`

**To create a new bookable service:**
1. Create system service (if doesn't exist)
2. Create category under that service with price
3. Assign doctor to category
4. Patients can now book it!

**Current working example:**
- Service: "Private GP Services" (ID 59)
- Category: "Private GP Services" (ID 68, £80)
- Doctor: Dr. Felix Harris (ID 18)
- Booking URL: `http://127.0.0.1:8000/booking/59`
