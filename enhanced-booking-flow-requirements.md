# Enhanced Booking Flow Requirements

## Overview
Enhance the existing booking system to include a **Category Selection** step between Service and Doctor selection, creating a more intuitive and organized booking experience.

## Current vs Enhanced Flow

### Current Flow
```
Service → Clinic → Doctor → DateTime → Payment
```

### Enhanced Flow
```
Service → Clinic → Category → Doctor (conditional) → DateTime → Payment
```

## Service Structure

The system will support multiple main services, each with their own categories:

### 1. Specialist Services
**Main Service:** Specialist Services

**Categories:**
- Audiology
- Cardiology Consultations & Scans
- Dermatology
- Diabetology & Endocrinology
- Ear, Nose and Throat
- Gynaecology
- Orthopaedics
- Paediatric
- Psychology
- Sexual Health
- Sleep and Respiratory
- Urology

### 2. Private GP Services
**Main Service:** Private GP Services

**Categories:**
- Private GP Services
- Visa Medicals
- Private Prescriptions
- Private Contraception
- Hayfever Treatment
- Ear Syringing
- Vaccinations
- Medical Finance
- Medical Insurance

### 3. Blood Tests & Laboratory
**Main Service:** Blood Tests & Laboratory

**Package Categories:**
- Well Person Blood Test (£220)
  - Blood count
  - Kidney function
  - Liver function
  - Thyroid function
  - Cardiac / muscle enzymes
  - Iron and bone markers
  - Blood glucose
  - Cholesterol levels including HDL & LDL
  - Prostate cancer check (for men)
  - Ovarian cancer check (for women)

- Lifestyle Blood Test (£288)
  - Full Blood Count
  - ESR
  - C-reactive protein
  - Biochemistry (Urea & Electrolytes & Liver function tests)
  - Lipids Profile (HDL/LDL)
  - Iron studies (Iron TIBC)
  - Thyroid function (Free T3/Free T4/TSH)
  - Bone markers (calcium, phosphate, uric acid)
  - PSA for men / CA125 for women
  - Haemoglobin A1C (HbA1c) test
  - Minerals (Ferritin, Magnesium)
  - Vitamins (B12, Serum Folate, Vitamin D)

- Ultimate Health Screen (£649)
  - GP Consultation
  - Physical Examination
  - Lifestyle assessment
  - Medical history
  - Urine analysis
  - Thyroid tests (TSH, Free T4 & Free T3)
  - Blood Pressure & Pulse
  - Health Promotion Literature
  - A PLAC & Q-Risk3 cardiac assessment
  - Detailed review of liver and kidney function
  - PSA check for prostate cancer
  - CA125 check for ovarian cancer
  - Essential vitamins (Vit D and B12)
  - Advanced HbA1c diabetes test

**Individual Test Categories:**
- Allergy & Immunology Tests
  - Total IgE (Immunoglobulin E) - £84
  - Rheumatoid Factor (RF) - £68
  - Serum Protein Electrophoresis - £143

- Biochemistry Tests
  - Electrolytes (Sodium, Potassium, Chloride, Bicarbonate) - £80
  - Liver Function Tests (LFTs) - £70
  - Kidney Function Tests (KFTs) - £80
  - Lipid Profile - £83
  - Glucose - £64
  - Calcium - £64

- Cancer Risk Tests
  - Prostate-Specific Antigen (PSA) - £94
  - CA 125 (Cancer Antigen 125) - £153
  - CA 19-9 (Carbohydrate Antigen 19-9) - £164

- Cardiovascular Health
  - Lipid Profile (Cholesterol, Triglycerides) - £83
  - High-Sensitivity C-Reactive Protein (hs-CRP) - £32
  - Homocysteine - £110

- General Health Tests
  - Full Blood Count (FBC) - £69
  - Liver Function Tests (LFTs) - £70
  - Kidney Function Tests (KFTs) - £80
  - Thyroid Function Tests (TFTs) - £126

- Haematology Tests
  - Full Blood Count (FBC) - £53
  - Vitamin B12 - £144
  - C-Reactive Protein (CRP) - £100

- Hormonal & Endocrine
  - Thyroid Stimulating Hormone (TSH) - £84
  - Cortisol - £83
  - Testosterone (Total) - £83
  - Estrogen (Estradiol) - £78
  - Progesterone - £78

- Infectious Diseases
  - HIV (Human Immunodeficiency Virus) - £94
  - Hepatitis B Surface Antigen (HBsAg) - £89
  - Hepatitis C Antibody - £125
  - Syphilis (RPR, VDRL) - £70

- Neurological & Mental Health
  - Folate - £45
  - Cortisol - £83
  - Thyroid Function Tests (TFTs) - £126
  - Vitamin B12 - £144
  - Homocysteine - £110
  - Methylmalonic Acid (MMA) - £226

- Nutritional & Metabolic Health
  - Magnesium - £73
  - Zinc - £84
  - Ferritin - £123
  - Vitamin D (25-hydroxyvitamin D) - £257

- Reproductive Health Tests
  - Estradiol - £78
  - Progesterone - £78
  - Follicle Stimulating Hormone (FSH) - £83
  - Testosterone - £83

- Sexual Health Tests
  - Chlamydia - £104
  - Gonorrhea - £104
  - Chlamydia & Gonorrhea (combined) - £104
  - HIV - £94
  - Syphilis (RPR, VDRL) - £70
  - Hepatitis B and/or C - £89 / £125

### 4. Private Scans & Imaging
**Main Service:** Private Scans & Imaging

**Categories:**
- CT Scans
- Heart Scans (CT Calcium Score)
- Hysterosalpingo Contrast Sonography (HyCoSy)
- MRI Scans
- Pregnancy Ultrasound
- Medical Ultrasound
- Non-invasive virtual colonoscopy
- X-ray

## Booking Flow Logic

### Step-by-Step Process

1. **Service Selection**
   - User selects main service (e.g., "Specialist Services", "Blood Tests & Laboratory")

2. **Clinic Selection**
   - User selects preferred clinic
   - Auto-select if coming from clinic page

3. **Category Selection** (NEW STEP)
   - Display categories based on selected service
   - Show pricing where applicable
   - Show "More info" links for detailed descriptions

4. **Doctor Selection** (Conditional)
   - **Required for:** Most specialist services, GP consultations
   - **Optional for:** Some blood tests with interpretation
   - **Skip for:** Basic lab tests, some scans

5. **Date & Time Selection**
   - Show available slots based on service type
   - Some services may not require specific time slots

6. **Payment**
   - Process payment based on selected service and category

### Conditional Logic Rules

#### Doctor Required Services:
- All Specialist Services categories
- Most Private GP Services
- Blood test packages with consultation (Ultimate Health Screen)
- Scan interpretations

#### No Doctor Required Services:
- Basic blood tests
- Standard lab work
- Simple scans without interpretation
- Report processing

#### Hybrid Services (Optional Doctor):
- Blood test result interpretation
- Scan result review
- Second opinion consultations

## Database Structure

### Existing Tables (Leverage Current Structure)

#### clinics_services
- Stores main services (Specialist Services, Private GP Services, etc.)
- Fields: id, name, description, type, charges, category_id, etc.

#### clinics_categories
- Stores service categories with parent-child relationships
- Fields: id, name, description, parent_id, status, etc.
- parent_id links categories to main services

### Data Relationships

```
clinics_services (Main Services)
├── "Specialist Services" (id: 1)
├── "Private GP Services" (id: 2)
├── "Blood Tests & Laboratory" (id: 3)
└── "Private Scans & Imaging" (id: 4)

clinics_categories (Categories)
├── Audiology (parent_id: 1, service: Specialist Services)
├── Cardiology (parent_id: 1, service: Specialist Services)
├── Well Person Blood Test (parent_id: 3, service: Blood Tests)
├── CT Scans (parent_id: 4, service: Private Scans)
└── etc...
```

## Technical Implementation Requirements

### Frontend Changes

1. **Add Category Selection Step**
   - Insert between clinic and doctor selection
   - Dynamic category loading based on selected service
   - Category cards with pricing and descriptions

2. **Update Booking Controller**
   - Modify ServiceController to handle category step
   - Add category validation and session management
   - Implement conditional doctor selection logic

3. **Enhanced UI Components**
   - Category selection cards
   - Conditional step display
   - Progress indicator updates

### Backend Changes

1. **Controller Updates**
   - Extend booking flow logic
   - Add category-based routing
   - Implement service type detection

2. **Database Seeders**
   - Create seeders for main services
   - Create seeders for service categories
   - Establish parent-child relationships

3. **Validation Rules**
   - Category selection validation
   - Service-category compatibility checks
   - Conditional field requirements

## User Experience Enhancements

### Benefits of Enhanced Flow

1. **Better Organization**
   - Clear service categorization
   - Logical booking progression
   - Reduced confusion

2. **Improved Discovery**
   - Users can browse categories within services
   - Better understanding of available options
   - Clear pricing visibility

3. **Smart Routing**
   - Skip unnecessary steps based on service type
   - Conditional doctor selection
   - Streamlined lab/scan bookings

4. **Scalability**
   - Easy to add new services and categories
   - Flexible pricing structure
   - Maintainable service hierarchy

## Implementation Priority

### Phase 1: Core Structure
- Database setup with service-category relationships
- Basic category selection step
- Simple conditional logic

### Phase 2: Enhanced Features
- Advanced conditional routing
- Pricing integration
- UI/UX improvements

### Phase 3: Advanced Features
- Package bundling
- Dynamic pricing
- Advanced filtering and search

## Notes

- Existing booking system structure supports this enhancement
- Current database schema with parent_id relationships is perfect
- Implementation should maintain backward compatibility
- Test data may differ from examples provided
- Focus on scalable, maintainable solution