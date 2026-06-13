# Triage Module — Implementation Spec

## Overview

A standalone `Triage` module that sits upstream of the existing `PatientEncounter` (clinician) flow.
The nurse uses this module exclusively. The encounter is untouched.

```
Booking Request → [Nurse Triage] → Appointment Confirmed → [Doctor Encounter]
```

---

## Stage 1 — Core Triage Module (this spec)
## Stage 2 — In-app / SMS messaging to patient (deferred)

---

## 1. Module Location

```
Modules/Triage/
├── Config/
├── database/
│   ├── migrations/
│   └── seeders/
├── Http/
│   └── Controllers/
│       ├── TriageCategoryController.php
│       ├── TriageItemController.php
│       └── TriageController.php
├── lang/
│   └── en/
│       └── triage.php
├── Models/
│   ├── TriageCategory.php
│   ├── TriageItem.php
│   ├── PatientTriage.php
│   └── TriagePreCheck.php
├── Providers/
│   ├── TriageServiceProvider.php
│   └── RouteServiceProvider.php
├── Resources/
│   └── views/
│       └── backend/
│           ├── triage/
│           │   ├── index.blade.php          ← Triage Queue
│           │   ├── detail.blade.php         ← Triage intake form
│           │   └── action_column.blade.php
│           └── triage_category/
│               ├── index.blade.php
│               └── action_column.blade.php
├── routes/
│   └── web.php
├── composer.json
└── module.json
```

---

## 2. Database Migrations

### 2.1 `triage_categories`
```
id
name                string
display_order       integer  default 0
is_active           boolean  default true
timestamps
```

### 2.2 `triage_items`
```
id
category_id         FK → triage_categories.id
label               string
is_red_flag         boolean  default false
display_order       integer  default 0
is_active           boolean  default true
timestamps
```

### 2.3 `patient_triages`
```
id
patient_id          FK → users.id
appointment_id      FK → appointments.id  nullable
nurse_id            FK → users.id
category_id         FK → triage_categories.id  nullable
item_id             FK → triage_items.id  nullable
urgency_level       enum('E1','U2','S3','R4')  nullable
outcome             enum('emergency','urgent','soon','routine','redirect','home_visit')  nullable
nurse_notes         text  nullable  (encrypted at rest)
clinician_escalated_to  FK → users.id  nullable
handover_summary    text  nullable
status              enum('new','in_progress','escalated','closed')  default 'new'
-- Q10 intake fields (nurse internal form)
onset_bucket        string  nullable   (Today / 1-2 days / 3-7 days / 1-4 weeks / 1-3 months / >3 months)
trend               enum('worse','same','improving')  nullable
fever_flag          boolean  nullable
severity_score      tinyint  nullable  (0-10)
function_impacted   boolean  nullable
hydration_concern   boolean  nullable
risk_flags          json  nullable    (array of selected risk factors)
meds_text           text  nullable
allergy_text        text  nullable
recent_antibiotics  boolean  nullable
identity_confirmed  boolean  default false
timestamps
softDeletes
```

### 2.4 `triage_pre_checks`
```
id
appointment_id      FK → appointments.id  nullable
user_id             FK → users.id  nullable
answers             json   (keyed by Q1–Q14)
blocker_triggered   boolean  default false
blocker_question    string  nullable
recommended_urgency string  nullable
recommended_path    string  nullable
timestamps
```

### 2.5 Alter `appointments` table
```
Add: triage_id  unsignedBigInteger  nullable  FK → patient_triages.id
```

---

## 3. Seeders

Seed all triage categories and items from the spec below.
`TriageCategorySeeder` + `TriageItemSeeder` — run via `DatabaseSeeder`.

### Categories + Items

**A — Breathing / Chest / Respiratory** (is_red_flag: false unless noted)
- Shortness of breath
- Wheeze / asthma flare-up
- Chest tightness
- Chest pain (non-emergency screening)
- Cough (dry)
- Cough (chesty / productive)
- Fever + cough
- Sore throat
- Tonsillitis symptoms
- Sinus pain / facial pressure
- Blocked nose / congestion
- Ear pain with cold symptoms
- Suspected chest infection
- Persistent cough (over 3 weeks)

**B — Fever / Infection / Unwell**
- Fever / high temperature
- Chills / shivering
- Flu-like symptoms
- General unwell / body aches
- Swollen glands
- Suspected viral infection
- Suspected bacterial infection
- Recurrent infections
- Post-travel illness
- Infection follow-up (after antibiotics)

**C — Urinary / Kidney**
- Burning when passing urine
- Frequent urination
- Urgency / can't hold urine
- Lower abdominal pain (possible UTI)
- Blood in urine
- Flank pain (side/back)
- Recurrent UTIs
- Urine test request
- Catheter-related problem
- Urinary incontinence concerns

**D — Stomach / Gastrointestinal**
- Abdominal pain
- Nausea
- Vomiting
- Diarrhoea
- Constipation
- Heartburn / reflux
- Bloating
- Loss of appetite
- Suspected food poisoning
- Blood in stool
- Piles / haemorrhoids symptoms
- Ongoing stomach symptoms (2+ weeks)

**E — Skin / Allergy**
- Rash (new)
- Rash (recurring)
- Eczema flare-up
- Hives / urticaria
- Itching (generalised)
- Suspected allergic reaction (mild)
- Insect bite reaction
- Skin infection / cellulitis concern
- Acne flare-up
- Mole/lesion concern
- Psoriasis flare-up
- Fungal rash (athlete's foot / ringworm)
- Hay fever symptoms

**F — Pain / Musculoskeletal / Injury**
- Back pain
- Neck pain
- Shoulder pain
- Knee pain
- Ankle/foot pain
- Wrist/hand pain
- Muscle strain
- Sports injury
- Joint swelling
- Sciatica symptoms
- Reduced mobility / stiffness
- Follow-up after physiotherapy
- Injection enquiry (where offered)

**G — Headache / Neurology**
- Headache (new)
- Migraine symptoms
- Dizziness / vertigo
- Fainting / blackouts
- Numbness / tingling
- Weakness (non-emergency screening)
- Tremor
- Sleep disturbance
- Persistent fatigue
- Memory/concentration concerns
- Neurology referral enquiry

**H — Women's Health**
- Period pain
- Heavy bleeding
- Irregular periods
- Missed period / pregnancy concern
- Vaginal discharge
- Pelvic pain
- Menopause/perimenopause symptoms
- Contraception advice
- Coil/implant query (if offered)
- Smear / cervical screening query
- Thrush symptoms
- UTI symptoms (women-specific routing option)

**I — Men's Health**
- Prostate/urinary symptoms
- Erectile dysfunction concerns
- Testosterone/hormone concerns
- Fertility concerns
- Testicular pain/lumps (non-emergency screening)
- Sexual health advice
- General wellbeing check request

**J — Mental Health / Wellbeing**
- Anxiety symptoms
- Panic symptoms
- Low mood
- Stress/burnout
- Sleep problems
- Work-related stress
- Grief / bereavement support
- Medication review (mental health)
- Talking therapy enquiry
- ADHD enquiry (if offered)
- Crisis support needed  ← `is_red_flag: true`

**K — Diabetes / Blood Pressure / Long-term Conditions**
- High blood sugar symptoms
- Low blood sugar episodes
- HbA1c test request
- Blood pressure check request
- Medication side effects
- Medication review (diabetes/BP)
- Ongoing monitoring plan
- General chronic condition review

**L — Travel Clinic**
- Travel advice consultation
- Vaccine enquiry
- Malaria prevention advice
- Traveller's diarrhoea prevention
- Fit-to-fly concern
- Post-travel illness triage

**M — Medical Letters / Fit Notes / Reports**
- Fit note request
- Return to work letter
- Medical summary letter
- Referral letter request
- Insurance form completion enquiry
- Work medical / occupational request

**N — Home Visit Requests (Triage Gate)**
- Elderly patient / mobility issue
- Post-operative / limited movement
- Severe symptoms but stable for home assessment
- Childcare constraints
- No transport / access issue
- "Too unwell to attend clinic" (requires clinician review)

**O — Red Flag / Safety Screening** (all items `is_red_flag: true`)
- Severe chest pain  ← red flag
- Severe difficulty breathing  ← red flag
- Stroke symptoms concern  ← red flag
- Collapse / loss of consciousness  ← red flag
- Severe allergic reaction concern  ← red flag
- Heavy bleeding  ← red flag
- Severe abdominal pain  ← red flag
- Suicidal thoughts / immediate risk  ← red flag

---

## 4. Models

### PatientTriage
- `fillable`: all columns above
- `casts`: `risk_flags` → array, `fever_flag`/`function_impacted`/`hydration_concern`/`identity_confirmed`/`recent_antibiotics` → boolean
- `nurse_notes` + `handover_summary` encrypted via `Attribute` (same pattern as `PatientEncounter::description`)
- Relationships: `patient()`, `nurse()`, `appointment()`, `category()`, `item()`, `clinicianEscalatedTo()`
- `scopeForNurse($query, $user)` — filters to nurse's own clinic

### TriageCategory / TriageItem
- Simple models, `fillable`, `is_active` scope

### TriagePreCheck
- `answers` cast to array

---

## 5. Controllers

### TriageController
Methods:
- `index()` — Triage Queue view (tabs: New / In Progress / Escalated / Closed)
- `index_data()` — DataTables JSON for queue
- `show($id)` — Triage detail/intake form
- `store(Request $request)` — Create new triage record from appointment request
- `update(Request $request, $id)` — Save intake form (category, item, urgency, outcome, notes, risk flags, etc.)
- `escalate(Request $request, $id)` — Set escalated_to, generate handover summary, set status = escalated
- `close($id)` — Set status = closed
- `getItems(Request $request)` — AJAX: return items filtered by category_id (for dynamic dropdown)
- `preCheckStore(Request $request)` — Save pre-booking questionnaire answers

### TriageCategoryController (admin only)
- `index()`, `index_data()`, `store()`, `update()`, `destroy()`, `bulk_action()`, `update_status()`

---

## 6. Routes (`Modules/Triage/routes/web.php`)

```php
// Nurse triage queue + intake
Route::resource('triage', TriageController::class)->only(['index','show','store','update']);
Route::get('triage/index_data', [TriageController::class, 'index_data'])->name('triage.index_data');
Route::post('triage/{id}/escalate', [TriageController::class, 'escalate'])->name('triage.escalate');
Route::post('triage/{id}/close', [TriageController::class, 'close'])->name('triage.close');
Route::get('triage/get-items', [TriageController::class, 'getItems'])->name('triage.get_items');
Route::post('triage/pre-check', [TriageController::class, 'preCheckStore'])->name('triage.pre_check');

// Admin: category management
Route::resource('triage-category', TriageCategoryController::class)->only(['index','store','update','destroy']);
Route::get('triage-category/index_data', [TriageCategoryController::class, 'index_data'])->name('triage-category.index_data');
Route::post('triage-category/bulk-action', [TriageCategoryController::class, 'bulk_action'])->name('triage-category.bulk_action');
Route::post('triage-category/update-status/{id}', [TriageCategoryController::class, 'update_status'])->name('triage-category.update_status');
```

---

## 7. Permissions (add to existing permission seeder)

```
view_triage_queue
add_triage
edit_triage
delete_triage
escalate_triage
view_triage_category
add_triage_category
edit_triage_category
delete_triage_category
```

Nurse role gets: `view_triage_queue`, `add_triage`, `edit_triage`, `escalate_triage`
Admin/vendor gets all.

---

## 8. Views

All views extend `backend.layouts.app` and use the same component patterns as the rest of the project.

### 8.1 Triage Queue — `triage/index.blade.php`

- `x-backend.section-header` with toolbar (search input, status filter)
- Status tab pills: **New** | **In Progress** | **Escalated** | **Closed**
- DataTable with columns:
  - Patient name (linked to patient profile)
  - DOB / Age
  - Appointment type (Acute / Home Visit / etc.)
  - Presenting complaint (from booking notes)
  - Triage status badge (colour-coded)
  - Urgency badge (E1 red / U2 orange / S3 yellow / R4 green)
  - Nurse assigned
  - Created at
  - Action column: **Open** button → `triage/detail`
- Safety banner pinned at top of page:
  > ⚠️ If a patient reports severe chest pain, breathing difficulty, stroke symptoms, collapse, or severe allergic reaction — advise 999 immediately.

### 8.2 Triage Detail / Intake — `triage/detail.blade.php`

Two-column layout (left: patient summary card, right: intake form).

**Left panel — Patient Summary**
- Name, DOB, age, contact number, postcode
- Key notes / allergies (read-only from patient profile)
- Appointment request details (type, symptom notes from booking)
- Link to full patient profile (read-only)

**Right panel — Nurse Intake Form (10 steps, single page)**

Q1 — Identity Confirmation
- Patient name (auto-filled, confirm checkbox)
- DOB (confirm)
- Contact number (confirm)
- Current location/postcode

Q2 — Age / Safeguarding Gate
- Auto-calculated from DOB
- If age < 16: show policy block message (configurable toggle in admin settings)
- If under-16 allowed: guardian name, relationship, phone, consent checkbox

Q3 — Red Flag Safety Screen
- Checkbox list (any checked → auto-set E1, lock outcome to Emergency, show 999 script)
  - Severe chest pain/pressure
  - Severe difficulty breathing / blue lips / cannot speak
  - Stroke symptoms concern (face/arm/speech)
  - Collapse / loss of consciousness / seizure not stopping
  - Severe allergic reaction (face/tongue swelling, breathing difficulty, fainting)
  - Heavy uncontrolled bleeding
  - Immediate self-harm risk / not safe right now
- If any selected: red alert banner + "Advised 999/A&E" action required checkbox + free text "Action taken"

Q4 — Triage Category
- `<select>` dropdown populated from `triage_categories` (active, ordered)

Q5 — Reason within Category
- `<select>` dropdown dynamically populated via AJAX on Q4 change (`/triage/get-items?category_id=X`)
- Red flag items shown with ⚠️ indicator

Q6 — Onset & Pattern
- Onset: `<select>` (Today / 1–2 days / 3–7 days / 1–4 weeks / 1–3 months / >3 months)
- Trend: button group (Getting worse / Same / Improving)
- Fever: Yes / No toggle

Q7 — Severity & Function
- Severity: range slider 0–10 with live label
- Can patient do normal daily tasks? Yes / No
- Able to keep fluids down? Yes / No

Q8 — High-Risk Factors
- Checkbox list (Age 65+ / Pregnancy / Diabetes / Heart disease / Asthma-COPD / Immunosuppressed / Kidney disease / Blood thinners / Recent surgery / None)

Q9 — Medications & Allergies
- Medications: free text
- Allergies: None / Yes → free text
- Recent antibiotics: Yes / No

Q10 — Nurse Decision
- Urgency level: 4 button group (E1 Emergency / U2 Urgent / S3 Soon / R4 Routine) — colour coded
- Outcome: 6 button group (Emergency / Urgent Same-Day / Soon 24–72h / Routine / Redirect / Home Visit Consideration)
  - If Redirect: show service dropdown
  - If Home Visit: show note "Request sent for clinician approval"
- Decision support suggestion (read-only, auto-calculated, labelled "Suggested — nurse can override"):
  - Any red flag → suggest E1 Emergency
  - Severity ≥ 7 OR function impacted OR high-risk + worsening → suggest U2 Urgent
  - Severity 4–6 stable → suggest S3 Soon
  - Severity ≤ 3 long-standing → suggest R4 Routine
  - Home visit + high-risk → suggest Home Visit + auto-escalate
- Nurse notes: free text textarea
- Escalate to clinician: Yes / No toggle → if Yes, show clinician select dropdown + auto-populated handover summary (editable)
- Submit button: "Save Triage"

**Handover Summary auto-template** (shown when escalate = Yes):
```
Main issue: {Category} → {Item}
Onset/trend: {onset_bucket}, {trend}
Severity: {score}/10, Function impacted: {Y/N}
Risk factors: {list}
Meds/allergies: {text}
Nurse recommendation: {urgency} — {outcome}
Notes: {nurse_notes}
```

### 8.3 Triage Category Admin — `triage_category/index.blade.php`

- Standard DataTable (same pattern as nurse/index.blade.php)
- Columns: Name, Item Count, Display Order, Status toggle, Action (edit/delete)
- Offcanvas form for create/edit (name, display_order, is_active)
- No separate items management page — items are seeded; admin can toggle active/inactive via a nested expandable row or a simple items sub-list offcanvas

---

## 9. Sidebar Menu (`GenerateMenus.php` additions)

**Nurse role** — add after dashboard:
```php
$this->mainRoute($menu, [
    'icon' => 'ph ph-clipboard-text',
    'title' => __('triage.menu_title'),
    'route' => 'backend.triage.index',
    'active' => 'app/triage',
    'permission' => ['view_triage_queue'],
    'order' => 0,
]);
```

**Admin role** — add under clinic section as parent menu:
```php
$triage = $this->parentMenu($menu, [
    'icon' => 'ph ph-clipboard-text',
    'title' => __('triage.menu_title'),
    'nickname' => 'triage',
    'permission' => ['view_triage_queue'],
    'order' => 0,
]);
$this->childMain($triage, [
    'icon' => 'ph ph-list-bullets',
    'title' => __('triage.queue'),
    'route' => 'backend.triage.index',
    'active' => 'app/triage',
    'permission' => ['view_triage_queue'],
    'order' => 0,
]);
$this->childMain($triage, [
    'icon' => 'ph ph-tag',
    'title' => __('triage.categories'),
    'route' => 'backend.triage-category.index',
    'active' => 'app/triage-category',
    'permission' => ['view_triage_category'],
    'order' => 0,
]);
```

---

## 10. Lang Keys (`lang/en/triage.php` + module lang)

```php
'menu_title'         => 'Triage',
'queue'              => 'Triage Queue',
'categories'         => 'Triage Categories',
'singular_title'     => 'Triage',
'lbl_patient'        => 'Patient',
'lbl_urgency'        => 'Urgency',
'lbl_outcome'        => 'Outcome',
'lbl_category'       => 'Category',
'lbl_item'           => 'Reason',
'lbl_nurse_notes'    => 'Nurse Notes',
'lbl_handover'       => 'Handover Summary',
'lbl_escalate'       => 'Escalate to Clinician',
'lbl_status'         => 'Status',
'status_new'         => 'New',
'status_in_progress' => 'In Progress',
'status_escalated'   => 'Escalated',
'status_closed'      => 'Closed',
'urgency_e1'         => 'E1 Emergency',
'urgency_u2'         => 'U2 Urgent',
'urgency_s3'         => 'S3 Soon',
'urgency_r4'         => 'R4 Routine',
'outcome_emergency'  => 'Emergency — Call 999 / A&E',
'outcome_urgent'     => 'Urgent — Same Day',
'outcome_soon'       => 'Soon — 24–72 Hours',
'outcome_routine'    => 'Routine — Next Available',
'outcome_redirect'   => 'Redirect — Different Service',
'outcome_home_visit' => 'Home Visit Consideration',
'safety_banner'      => 'If a patient reports severe chest pain, breathing difficulty, stroke symptoms, collapse, or severe allergic reaction — advise 999 immediately.',
'red_flag_warning'   => 'Red flag selected. Outcome locked to Emergency. Record action taken below.',
'suggested_label'    => 'Suggested (nurse can override)',
'minor_policy_block' => 'Cosmo Doctors appointments are for patients aged 16+. Please contact NHS 111 for urgent advice, or attend appropriate services.',
'bulk_update'        => 'Triage records updated.',
'bulk_delete'        => 'Triage records deleted.',
```

---

## 11. Pre-Booking Questionnaire (Patient-Facing — Stage 1 backend only)

The `TriagePreCheck` model and `preCheckStore` endpoint are built in Stage 1 so the data is captured and stored. The patient-facing UI flow (blocking/routing logic on the booking page) is wired up in Stage 1 as well.

Logic rules (enforced server-side + JS):
- Q1–Q6 any YES → `blocker_triggered = true`, return `{ block: true, guidance: '999' }`
- Q7–Q12 YES → `recommended_urgency = 'urgent'`, `recommended_path = 'acute_same_day'`, return allow + recommendation
- Q13 YES → `recommended_path = 'home_visit_request'`
- Q14 YES → `recommended_path = 'medical_reports'`

All answers stored in `triage_pre_checks.answers` JSON for audit.

---

## 12. Appointment Model — Relationship Addition

Add to `Modules/Appointment/Models/Appointment.php`:
```php
public function triage()
{
    return $this->belongsTo(\Modules\Triage\Models\PatientTriage::class, 'triage_id');
}
```

---

## Stage 2 (deferred — do not implement yet)

- In-app message sending to patient (templates + free text)
- SMS dispatch via existing notification system
- Nurse message template management UI
- Patient-facing triage status updates
- Pre-booking questionnaire patient UI on `booking.blade.php`

---

## Implementation Order

1. Migrations (categories, items, patient_triages, triage_pre_checks, alter appointments)
2. Seeders (all categories + items)
3. Models
4. Controllers (TriageController + TriageCategoryController)
5. Routes + ServiceProvider registration
6. Views (queue index → detail → category admin)
7. Sidebar menu additions
8. Lang file
9. Permissions seeder
10. Appointment model relationship
