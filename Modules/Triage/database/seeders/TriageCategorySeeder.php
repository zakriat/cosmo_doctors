<?php

namespace Modules\Triage\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Triage\Models\TriageCategory;
use Modules\Triage\Models\TriageItem;

class TriageCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Breathing / Chest / Respiratory',
                'display_order' => 1,
                'items' => [
                    ['label' => 'Shortness of breath'],
                    ['label' => 'Wheeze / asthma flare-up'],
                    ['label' => 'Chest tightness'],
                    ['label' => 'Chest pain (non-emergency screening)'],
                    ['label' => 'Cough (dry)'],
                    ['label' => 'Cough (chesty / productive)'],
                    ['label' => 'Fever + cough'],
                    ['label' => 'Sore throat'],
                    ['label' => 'Tonsillitis symptoms'],
                    ['label' => 'Sinus pain / facial pressure'],
                    ['label' => 'Blocked nose / congestion'],
                    ['label' => 'Ear pain with cold symptoms'],
                    ['label' => 'Suspected chest infection'],
                    ['label' => 'Persistent cough (over 3 weeks)'],
                ],
            ],
            [
                'name' => 'Fever / Infection / Unwell',
                'display_order' => 2,
                'items' => [
                    ['label' => 'Fever / high temperature'],
                    ['label' => 'Chills / shivering'],
                    ['label' => 'Flu-like symptoms'],
                    ['label' => 'General unwell / body aches'],
                    ['label' => 'Swollen glands'],
                    ['label' => 'Suspected viral infection'],
                    ['label' => 'Suspected bacterial infection'],
                    ['label' => 'Recurrent infections'],
                    ['label' => 'Post-travel illness'],
                    ['label' => 'Infection follow-up (after antibiotics)'],
                ],
            ],
            [
                'name' => 'Urinary / Kidney',
                'display_order' => 3,
                'items' => [
                    ['label' => 'Burning when passing urine'],
                    ['label' => 'Frequent urination'],
                    ['label' => 'Urgency / can\'t hold urine'],
                    ['label' => 'Lower abdominal pain (possible UTI)'],
                    ['label' => 'Blood in urine'],
                    ['label' => 'Flank pain (side/back)'],
                    ['label' => 'Recurrent UTIs'],
                    ['label' => 'Urine test request'],
                    ['label' => 'Catheter-related problem'],
                    ['label' => 'Urinary incontinence concerns'],
                ],
            ],
            [
                'name' => 'Stomach / Gastrointestinal',
                'display_order' => 4,
                'items' => [
                    ['label' => 'Abdominal pain'],
                    ['label' => 'Nausea'],
                    ['label' => 'Vomiting'],
                    ['label' => 'Diarrhoea'],
                    ['label' => 'Constipation'],
                    ['label' => 'Heartburn / reflux'],
                    ['label' => 'Bloating'],
                    ['label' => 'Loss of appetite'],
                    ['label' => 'Suspected food poisoning'],
                    ['label' => 'Blood in stool'],
                    ['label' => 'Piles / haemorrhoids symptoms'],
                    ['label' => 'Ongoing stomach symptoms (2+ weeks)'],
                ],
            ],
            [
                'name' => 'Skin / Allergy',
                'display_order' => 5,
                'items' => [
                    ['label' => 'Rash (new)'],
                    ['label' => 'Rash (recurring)'],
                    ['label' => 'Eczema flare-up'],
                    ['label' => 'Hives / urticaria'],
                    ['label' => 'Itching (generalised)'],
                    ['label' => 'Suspected allergic reaction (mild)'],
                    ['label' => 'Insect bite reaction'],
                    ['label' => 'Skin infection / cellulitis concern'],
                    ['label' => 'Acne flare-up'],
                    ['label' => 'Mole/lesion concern'],
                    ['label' => 'Psoriasis flare-up'],
                    ['label' => 'Fungal rash (athlete\'s foot / ringworm)'],
                    ['label' => 'Hay fever symptoms'],
                ],
            ],
            [
                'name' => 'Pain / Musculoskeletal / Injury',
                'display_order' => 6,
                'items' => [
                    ['label' => 'Back pain'],
                    ['label' => 'Neck pain'],
                    ['label' => 'Shoulder pain'],
                    ['label' => 'Knee pain'],
                    ['label' => 'Ankle/foot pain'],
                    ['label' => 'Wrist/hand pain'],
                    ['label' => 'Muscle strain'],
                    ['label' => 'Sports injury'],
                    ['label' => 'Joint swelling'],
                    ['label' => 'Sciatica symptoms'],
                    ['label' => 'Reduced mobility / stiffness'],
                    ['label' => 'Follow-up after physiotherapy'],
                    ['label' => 'Injection enquiry (where offered)'],
                ],
            ],
            [
                'name' => 'Headache / Neurology',
                'display_order' => 7,
                'items' => [
                    ['label' => 'Headache (new)'],
                    ['label' => 'Migraine symptoms'],
                    ['label' => 'Dizziness / vertigo'],
                    ['label' => 'Fainting / blackouts'],
                    ['label' => 'Numbness / tingling'],
                    ['label' => 'Weakness (non-emergency screening)'],
                    ['label' => 'Tremor'],
                    ['label' => 'Sleep disturbance'],
                    ['label' => 'Persistent fatigue'],
                    ['label' => 'Memory/concentration concerns'],
                    ['label' => 'Neurology referral enquiry'],
                ],
            ],
            [
                'name' => 'Women\'s Health',
                'display_order' => 8,
                'items' => [
                    ['label' => 'Period pain'],
                    ['label' => 'Heavy bleeding'],
                    ['label' => 'Irregular periods'],
                    ['label' => 'Missed period / pregnancy concern'],
                    ['label' => 'Vaginal discharge'],
                    ['label' => 'Pelvic pain'],
                    ['label' => 'Menopause/perimenopause symptoms'],
                    ['label' => 'Contraception advice'],
                    ['label' => 'Coil/implant query (if offered)'],
                    ['label' => 'Smear / cervical screening query'],
                    ['label' => 'Thrush symptoms'],
                    ['label' => 'UTI symptoms (women-specific routing option)'],
                ],
            ],
            [
                'name' => 'Men\'s Health',
                'display_order' => 9,
                'items' => [
                    ['label' => 'Prostate/urinary symptoms'],
                    ['label' => 'Erectile dysfunction concerns'],
                    ['label' => 'Testosterone/hormone concerns'],
                    ['label' => 'Fertility concerns'],
                    ['label' => 'Testicular pain/lumps (non-emergency screening)'],
                    ['label' => 'Sexual health advice'],
                    ['label' => 'General wellbeing check request'],
                ],
            ],
            [
                'name' => 'Mental Health / Wellbeing',
                'display_order' => 10,
                'items' => [
                    ['label' => 'Anxiety symptoms'],
                    ['label' => 'Panic symptoms'],
                    ['label' => 'Low mood'],
                    ['label' => 'Stress/burnout'],
                    ['label' => 'Sleep problems'],
                    ['label' => 'Work-related stress'],
                    ['label' => 'Grief / bereavement support'],
                    ['label' => 'Medication review (mental health)'],
                    ['label' => 'Talking therapy enquiry'],
                    ['label' => 'ADHD enquiry (if offered)'],
                    ['label' => 'Crisis support needed', 'is_red_flag' => true],
                ],
            ],
            [
                'name' => 'Diabetes / Blood Pressure / Long-term Conditions',
                'display_order' => 11,
                'items' => [
                    ['label' => 'High blood sugar symptoms'],
                    ['label' => 'Low blood sugar episodes'],
                    ['label' => 'HbA1c test request'],
                    ['label' => 'Blood pressure check request'],
                    ['label' => 'Medication side effects'],
                    ['label' => 'Medication review (diabetes/BP)'],
                    ['label' => 'Ongoing monitoring plan'],
                    ['label' => 'General chronic condition review'],
                ],
            ],
            [
                'name' => 'Travel Clinic',
                'display_order' => 12,
                'items' => [
                    ['label' => 'Travel advice consultation'],
                    ['label' => 'Vaccine enquiry'],
                    ['label' => 'Malaria prevention advice'],
                    ['label' => 'Traveller\'s diarrhoea prevention'],
                    ['label' => 'Fit-to-fly concern'],
                    ['label' => 'Post-travel illness triage'],
                ],
            ],
            [
                'name' => 'Medical Letters / Fit Notes / Reports',
                'display_order' => 13,
                'items' => [
                    ['label' => 'Fit note request'],
                    ['label' => 'Return to work letter'],
                    ['label' => 'Medical summary letter'],
                    ['label' => 'Referral letter request'],
                    ['label' => 'Insurance form completion enquiry'],
                    ['label' => 'Work medical / occupational request'],
                ],
            ],
            [
                'name' => 'Home Visit Requests',
                'display_order' => 14,
                'items' => [
                    ['label' => 'Elderly patient / mobility issue'],
                    ['label' => 'Post-operative / limited movement'],
                    ['label' => 'Severe symptoms but stable for home assessment'],
                    ['label' => 'Childcare constraints'],
                    ['label' => 'No transport / access issue'],
                    ['label' => 'Too unwell to attend clinic (requires clinician review)'],
                ],
            ],
            [
                'name' => 'Red Flag / Safety Screening',
                'display_order' => 15,
                'items' => [
                    ['label' => 'Severe chest pain', 'is_red_flag' => true],
                    ['label' => 'Severe difficulty breathing', 'is_red_flag' => true],
                    ['label' => 'Stroke symptoms concern', 'is_red_flag' => true],
                    ['label' => 'Collapse / loss of consciousness', 'is_red_flag' => true],
                    ['label' => 'Severe allergic reaction concern', 'is_red_flag' => true],
                    ['label' => 'Heavy bleeding', 'is_red_flag' => true],
                    ['label' => 'Severe abdominal pain', 'is_red_flag' => true],
                    ['label' => 'Suicidal thoughts / immediate risk', 'is_red_flag' => true],
                ],
            ],
        ];

        foreach ($categories as $order => $catData) {
            // Skip if already seeded
            if (TriageCategory::where('name', $catData['name'])->exists()) {
                continue;
            }

            $category = TriageCategory::create([
                'name'          => $catData['name'],
                'display_order' => $catData['display_order'],
                'is_active'     => true,
            ]);

            foreach ($catData['items'] as $itemOrder => $itemData) {
                TriageItem::create([
                    'category_id'   => $category->id,
                    'label'         => $itemData['label'],
                    'is_red_flag'   => $itemData['is_red_flag'] ?? false,
                    'display_order' => $itemOrder + 1,
                    'is_active'     => true,
                ]);
            }
        }
    }
}
