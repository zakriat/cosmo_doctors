# Enhanced Booking Flow - Implementation Tasks

## Overview
This document provides a step-by-step sequence to implement the enhanced booking flow with category selection between Service and Doctor selection.

## Current vs Enhanced Flow
- **Current:** Service → Clinic → Doctor → DateTime → Payment
- **Enhanced:** Service → Clinic → **Category** → Doctor (conditional) → DateTime → Payment

---

## Phase 1: Backend Data Setup (No Code Changes Required)

### Task 1.1: Create Main Services via Backend
**Location:** Backend → Services (`/app/services`)

**Action:** Create the following main services:

1. **Specialist Services**
   - Name: "Specialist Services"
   - Description: "Professional specialist consultations and treatments"
   - Type: "in_clinic"
   - Charges: 0 (will be set per category)
   - Status: Active

2. **Private GP Services**
   - Name: "Private GP Services" 
   - Description: "General practitioner services and consultations"
   - Type: "in_clinic"
   - Charges: 0
   - Status: Active

3. **Blood Tests & Laboratory**
   - Name: "Blood Tests & Laboratory"
   - Description: "Comprehensive blood testing and laboratory services"
   - Type: "in_clinic"
   - Charges: 0
   - Status: Active

4. **Private Scans & Imaging**
   - Name: "Private Scans & Imaging"
   - Description: "Medical imaging and diagnostic scans"
   - Type: "in_clinic" 
   - Charges: 0
   - Status: Active

**Expected Result:** 4 main services created in the system

---

### Task 1.2: Create Service Categories via Backend
**Location:** Backend → Categories (`/app/category`)

**Action:** Create categories with parent relationships:

#### For "Specialist Services" (parent_id = service_id_1):
1. Audiology - £150
2. Cardiology Consultations & Scans - £200
3. Dermatology - £120
4. Diabetology & Endocrinology - £180
5. Ear, Nose and Throat - £140
6. Gynaecology - £160
7. Orthopaedics - £180
8. Paediatric - £130
9. Psychology - £100
10. Sexual Health - £120
11. Sleep and Respiratory - £170
12. Urology - £160

#### For "Private GP Services" (parent_id = service_id_2):
1. Private GP Services - £80
2. Visa Medicals - £150
3. Private Prescriptions - £30
4. Private Contraception - £60
5. Hayfever Treatment - £50
6. Ear Syringing - £40
7. Vaccinations - £varies
8. Medical Finance - £0
9. Medical Insurance - £0

#### For "Blood Tests & Laboratory" (parent_id = service_id_3):
**Package Categories:**
1. Well Person Blood Test - £220
2. Lifestyle Blood Test - £288
3. Ultimate Health Screen - £649

**Individual Test Categories:**
1. Allergy & Immunology Tests - £varies
2. Biochemistry Tests - £varies
3. Cancer Risk Tests - £varies
4. Cardiovascular Health - £varies
5. General Health Tests - £varies
6. Haematology Tests - £varies
7. Hormonal & Endocrine - £varies
8. Infectious Diseases - £varies
9. Neurological & Mental Health - £varies
10. Nutritional & Metabolic Health - £varies
11. Reproductive Health Tests - £varies
12. Sexual Health Tests - £varies

#### For "Private Scans & Imaging" (parent_id = service_id_4):
1. CT Scans - £varies
2. Heart Scans (CT Calcium Score) - £varies
3. Hysterosalpingo Contrast Sonography (HyCoSy) - £varies
4. MRI Scans - £varies
5. Pregnancy Ultrasound - £varies
6. Medical Ultrasound - £varies
7. Non-invasive virtual colonoscopy - £varies
8. X-ray - £varies

**Expected Result:** All service categories created with proper parent relationships

---

### Task 1.3: Assign Doctors to Categories
**Location:** Backend → Services → Edit Service → Assign Doctors

**Action:** For each service category that requires doctors:

#### Doctor Required Categories:
- All Specialist Services categories
- Most Private GP Services (except Medical Finance/Insurance)
- Ultimate Health Screen (includes GP consultation)
- Some scan interpretations

#### No Doctor Required Categories:
- Basic blood tests
- Standard lab work
- Simple scans without interpretation
- Medical Finance/Insurance

**Steps:**
1. Go to Services management
2. Edit each service
3. Use "Assign Doctor" functionality
4. Select appropriate doctors for each category
5. Set custom pricing if needed
6. Assign to specific clinics

**Expected Result:** Doctor assignments configured for all relevant categories

---

## Phase 2: Database Enhancements (Code Changes Required)

### Task 2.1: Add Service Type Classification Field
**File:** Create new migration
**Purpose:** Add field to distinguish service behavior

**Migration:** `add_service_classification_to_clinics_services_table.php`
```sql
ALTER TABLE clinics_services ADD COLUMN service_classification ENUM('doctor_required', 'doctor_optional', 'no_doctor_required') DEFAULT 'doctor_required';
```

**Expected Result:** New field available for conditional logic

---

### Task 2.2: Create Service Categories Seeder
**File:** `Modules/Clinic/database/seeders/ServiceCategoriesSeeder.php`
**Purpose:** Populate categories programmatically for consistency

**Expected Result:** Automated category creation for fresh installations

---

## Phase 3: Backend Controller Enhancements

### Task 3.1: Enhance ClinicsServiceController
**File:** `Modules/Clinic/Http/Controllers/ClinicsServiceController.php`

**Changes Needed:**
1. Add service classification field to forms
2. Add category filtering methods
3. Add service type detection methods
4. Enhance service listing with category relationships

**Expected Result:** Backend can manage service classifications

---

### Task 3.2: Enhance ClinicsCategoryController  
**File:** `Modules/Clinic/Http/Controllers/ClinicsCategoryController.php`

**Changes Needed:**
1. Add methods to get categories by service
2. Add pricing management for categories
3. Add service-category relationship validation

**Expected Result:** Better category management with service relationships

---

## Phase 4: Frontend Booking Flow Enhancement

### Task 4.1: Update ServiceController Booking Method
**File:** `Modules/Frontend/Http/Controllers/ServiceController.php`

**Changes Needed:**
1. Add category selection step logic
2. Implement conditional doctor selection
3. Add category-based routing
4. Update session management for new step

**Expected Result:** Booking flow supports category selection

---

### Task 4.2: Create Category Selection View
**File:** `Modules/Frontend/Resources/views/components/category_selection.blade.php`

**Purpose:** Display categories based on selected service

**Expected Result:** User can select categories within services

---

### Task 4.3: Update Booking View
**File:** `Modules/Frontend/Resources/views/booking.blade.php`

**Changes Needed:**
1. Add category selection step
2. Update progress indicators
3. Add conditional step display logic
4. Update JavaScript for step navigation

**Expected Result:** Complete booking flow with category step

---

### Task 4.4: Update Frontend JavaScript
**File:** Frontend booking JavaScript files

**Changes Needed:**
1. Add category selection handling
2. Update step navigation logic
3. Add conditional doctor selection
4. Update form validation

**Expected Result:** Smooth user experience with new flow

---

## Phase 5: API and Data Flow

### Task 5.1: Create Category API Endpoints
**Purpose:** AJAX endpoints for dynamic category loading

**Endpoints Needed:**
1. `/api/services/{service_id}/categories` - Get categories by service
2. `/api/categories/{category_id}/doctors` - Get doctors by category
3. `/api/categories/{category_id}/pricing` - Get category pricing

**Expected Result:** Dynamic category loading in frontend

---

### Task 5.2: Update Booking Session Management
**Purpose:** Handle category data in booking sessions

**Changes Needed:**
1. Add category_id to session data
2. Update booking validation
3. Add category information to booking confirmation

**Expected Result:** Category information persisted through booking flow

---

## Phase 6: UI/UX Enhancements

### Task 6.1: Create Category Cards Component
**Purpose:** Display categories with pricing and descriptions

**Features:**
- Category name and description
- Pricing display
- "More info" links
- Selection state management

**Expected Result:** Attractive category selection interface

---

### Task 6.2: Update Progress Indicator
**Purpose:** Show new step in booking progress

**Changes Needed:**
1. Add category step to progress bar
2. Update step labels
3. Add step validation indicators

**Expected Result:** Clear booking progress indication

---

### Task 6.3: Add Conditional Step Display
**Purpose:** Show/hide steps based on service type

**Logic:**
- Show doctor step only if category requires doctor
- Skip doctor step for lab-only services
- Show appropriate messaging for each flow

**Expected Result:** Streamlined booking experience

---

## Phase 7: Testing and Validation

### Task 7.1: Backend Testing
**Tests:**
1. Service creation with categories
2. Doctor assignment functionality
3. Category management operations
4. Data validation and relationships

**Expected Result:** Backend functionality works correctly

---

### Task 7.2: Frontend Flow Testing
**Tests:**
1. Complete booking flow for each service type
2. Conditional doctor selection
3. Category pricing display
4. Session management
5. Error handling

**Expected Result:** Smooth user booking experience

---

### Task 7.3: Integration Testing
**Tests:**
1. End-to-end booking process
2. Payment integration with categories
3. Appointment creation with category data
4. Email confirmations with category information

**Expected Result:** Complete system integration

---

## Phase 8: Documentation and Training

### Task 8.1: Update Admin Documentation
**Purpose:** Document new backend features

**Content:**
- How to create services and categories
- Doctor assignment process
- Service classification options
- Troubleshooting guide

**Expected Result:** Clear admin guidance

---

### Task 8.2: Create User Guide
**Purpose:** Document new booking flow for users

**Content:**
- New booking process explanation
- Category selection guidance
- Service type differences
- FAQ section

**Expected Result:** User-friendly documentation

---

## Implementation Sequence

### Week 1: Backend Setup (No Code)
- Tasks 1.1, 1.2, 1.3
- Set up all services and categories via existing backend

### Week 2: Database and Backend Code
- Tasks 2.1, 2.2, 3.1, 3.2
- Database enhancements and backend controller updates

### Week 3: Frontend Implementation
- Tasks 4.1, 4.2, 4.3, 4.4
- Frontend booking flow implementation

### Week 4: API and Integration
- Tasks 5.1, 5.2, 6.1, 6.2, 6.3
- API endpoints and UI enhancements

### Week 5: Testing and Polish
- Tasks 7.1, 7.2, 7.3, 8.1, 8.2
- Testing, documentation, and final polish

---

## Success Criteria

### Backend Success:
- ✅ All services and categories created
- ✅ Doctor assignments working
- ✅ Service classification functional
- ✅ Category management operational

### Frontend Success:
- ✅ Category selection step working
- ✅ Conditional doctor selection
- ✅ Smooth booking flow
- ✅ Proper pricing display

### Integration Success:
- ✅ Complete booking process
- ✅ Payment integration
- ✅ Appointment creation
- ✅ Email confirmations

---

## Risk Mitigation

### Potential Issues:
1. **Data Migration:** Existing bookings compatibility
2. **Performance:** Category loading speed
3. **User Confusion:** New booking flow adoption
4. **Backend Complexity:** Service-category relationships

### Mitigation Strategies:
1. **Backward Compatibility:** Maintain existing booking URLs
2. **Caching:** Implement category caching
3. **User Education:** Clear UI and help text
4. **Testing:** Comprehensive testing at each phase

---

## Notes

- Start with Phase 1 using existing backend - no code changes needed
- Each phase builds on the previous one
- Test thoroughly at each phase before proceeding
- Maintain backward compatibility throughout
- Document all changes for future maintenance

This implementation approach ensures a smooth, error-free development process with clear milestones and success criteria.