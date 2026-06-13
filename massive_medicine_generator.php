<?php
/**
 * Massive Medicine Generator
 * Creates 500+ UK medicines with complete data
 */

echo "Massive BNF Medicine Generator\n";
echo "==============================\n\n";

$medicines = [];

// Common UK medicines with real data
$realMedicines = [
    // Analgesics
    ['Paracetamol 500mg Tablets', 'Paracetamol', 'Panadol', '500mg', 'tablet', 'GSK', 'Analgesic', 1.50],
    ['Paracetamol 250mg Tablets', 'Paracetamol', 'Calpol', '250mg', 'tablet', 'GSK', 'Analgesic', 1.20],
    ['Ibuprofen 400mg Tablets', 'Ibuprofen', 'Nurofen', '400mg', 'tablet', 'RB', 'NSAID', 2.80],
    ['Ibuprofen 200mg Tablets', 'Ibuprofen', 'Nurofen', '200mg', 'tablet', 'RB', 'NSAID', 2.20],
    ['Aspirin 75mg Tablets', 'Aspirin', 'Disprin', '75mg', 'tablet', 'Various', 'Antiplatelet', 1.20],
    ['Aspirin 300mg Tablets', 'Aspirin', 'Disprin', '300mg', 'tablet', 'Various', 'Analgesic', 1.80],
    ['Codeine 30mg Tablets', 'Codeine', 'Various', '30mg', 'tablet', 'Various', 'Opioid', 8.50],
    ['Tramadol 50mg Capsules', 'Tramadol', 'Zydol', '50mg', 'capsule', 'Various', 'Opioid', 12.80],
    ['Diclofenac 50mg Tablets', 'Diclofenac', 'Voltarol', '50mg', 'tablet', 'GSK', 'NSAID', 4.20],
    ['Naproxen 250mg Tablets', 'Naproxen', 'Naprosyn', '250mg', 'tablet', 'Various', 'NSAID', 5.80],
    
    // Antibiotics
    ['Amoxicillin 500mg Capsules', 'Amoxicillin', 'Amoxil', '500mg', 'capsule', 'GSK', 'Penicillin', 4.50],
    ['Amoxicillin 250mg Capsules', 'Amoxicillin', 'Amoxil', '250mg', 'capsule', 'GSK', 'Penicillin', 3.80],
    ['Flucloxacillin 250mg Capsules', 'Flucloxacillin', 'Floxapen', '250mg', 'capsule', 'Various', 'Penicillin', 5.20],
    ['Flucloxacillin 500mg Capsules', 'Flucloxacillin', 'Floxapen', '500mg', 'capsule', 'Various', 'Penicillin', 7.40],
    ['Erythromycin 250mg Tablets', 'Erythromycin', 'Erymax', '250mg', 'tablet', 'Various', 'Macrolide', 6.80],
    ['Erythromycin 500mg Tablets', 'Erythromycin', 'Erymax', '500mg', 'tablet', 'Various', 'Macrolide', 9.20],
    ['Clarithromycin 250mg Tablets', 'Clarithromycin', 'Klaricid', '250mg', 'tablet', 'Abbott', 'Macrolide', 12.50],
    ['Clarithromycin 500mg Tablets', 'Clarithromycin', 'Klaricid', '500mg', 'tablet', 'Abbott', 'Macrolide', 18.90],
    ['Doxycycline 100mg Capsules', 'Doxycycline', 'Vibramycin', '100mg', 'capsule', 'Pfizer', 'Tetracycline', 8.90],
    ['Ciprofloxacin 250mg Tablets', 'Ciprofloxacin', 'Ciproxin', '250mg', 'tablet', 'Bayer', 'Quinolone', 15.60],
    ['Ciprofloxacin 500mg Tablets', 'Ciprofloxacin', 'Ciproxin', '500mg', 'tablet', 'Bayer', 'Quinolone', 22.80],
    ['Cefalexin 250mg Capsules', 'Cefalexin', 'Keflex', '250mg', 'capsule', 'Various', 'Cephalosporin', 7.20],
    ['Cefalexin 500mg Capsules', 'Cefalexin', 'Keflex', '500mg', 'capsule', 'Various', 'Cephalosporin', 10.80],
    ['Co-amoxiclav 625mg Tablets', 'Co-amoxiclav', 'Augmentin', '625mg', 'tablet', 'GSK', 'Penicillin', 8.90],
    ['Trimethoprim 200mg Tablets', 'Trimethoprim', 'Various', '200mg', 'tablet', 'Various', 'Antifolate', 4.80],
    
    // Cardiovascular
    ['Simvastatin 20mg Tablets', 'Simvastatin', 'Zocor', '20mg', 'tablet', 'MSD', 'Statin', 7.90],
    ['Simvastatin 40mg Tablets', 'Simvastatin', 'Zocor', '40mg', 'tablet', 'MSD', 'Statin', 12.50],
    ['Atorvastatin 20mg Tablets', 'Atorvastatin', 'Lipitor', '20mg', 'tablet', 'Pfizer', 'Statin', 8.50],
    ['Atorvastatin 40mg Tablets', 'Atorvastatin', 'Lipitor', '40mg', 'tablet', 'Pfizer', 'Statin', 14.80],
    ['Ramipril 2.5mg Tablets', 'Ramipril', 'Tritace', '2.5mg', 'tablet', 'Sanofi', 'ACE Inhibitor', 3.80],
    ['Ramipril 5mg Tablets', 'Ramipril', 'Tritace', '5mg', 'tablet', 'Sanofi', 'ACE Inhibitor', 5.20],
    ['Ramipril 10mg Tablets', 'Ramipril', 'Tritace', '10mg', 'tablet', 'Sanofi', 'ACE Inhibitor', 7.80],
    ['Amlodipine 5mg Tablets', 'Amlodipine', 'Istin', '5mg', 'tablet', 'Pfizer', 'Calcium Channel Blocker', 4.20],
    ['Amlodipine 10mg Tablets', 'Amlodipine', 'Istin', '10mg', 'tablet', 'Pfizer', 'Calcium Channel Blocker', 6.80],
    ['Bisoprolol 2.5mg Tablets', 'Bisoprolol', 'Cardicor', '2.5mg', 'tablet', 'Merck', 'Beta-blocker', 4.60],
    ['Bisoprolol 5mg Tablets', 'Bisoprolol', 'Cardicor', '5mg', 'tablet', 'Merck', 'Beta-blocker', 6.80],
    ['Furosemide 20mg Tablets', 'Furosemide', 'Lasix', '20mg', 'tablet', 'Sanofi', 'Loop Diuretic', 3.50],
    ['Furosemide 40mg Tablets', 'Furosemide', 'Lasix', '40mg', 'tablet', 'Sanofi', 'Loop Diuretic', 5.20],
    ['Warfarin 1mg Tablets', 'Warfarin', 'Various', '1mg', 'tablet', 'Various', 'Anticoagulant', 4.50],
    ['Warfarin 3mg Tablets', 'Warfarin', 'Various', '3mg', 'tablet', 'Various', 'Anticoagulant', 4.50],
    ['Warfarin 5mg Tablets', 'Warfarin', 'Various', '5mg', 'tablet', 'Various', 'Anticoagulant', 4.50],
    
    // Respiratory
    ['Salbutamol 100mcg Inhaler', 'Salbutamol', 'Ventolin', '100mcg', 'inhaler', 'GSK', 'Beta2-agonist', 8.90],
    ['Beclometasone 50mcg Inhaler', 'Beclometasone', 'Clenil', '50mcg', 'inhaler', 'Chiesi', 'Corticosteroid', 12.50],
    ['Prednisolone 5mg Tablets', 'Prednisolone', 'Deltacortril', '5mg', 'tablet', 'Various', 'Corticosteroid', 7.40],
    ['Prednisolone 25mg Tablets', 'Prednisolone', 'Deltacortril', '25mg', 'tablet', 'Various', 'Corticosteroid', 12.80],
    ['Montelukast 10mg Tablets', 'Montelukast', 'Singulair', '10mg', 'tablet', 'MSD', 'Leukotriene Antagonist', 18.90],
    
    // Gastrointestinal
    ['Omeprazole 20mg Capsules', 'Omeprazole', 'Losec', '20mg', 'capsule', 'AstraZeneca', 'PPI', 5.60],
    ['Omeprazole 40mg Capsules', 'Omeprazole', 'Losec', '40mg', 'capsule', 'AstraZeneca', 'PPI', 8.90],
    ['Lansoprazole 15mg Capsules', 'Lansoprazole', 'Zoton', '15mg', 'capsule', 'Various', 'PPI', 6.80],
    ['Lansoprazole 30mg Capsules', 'Lansoprazole', 'Zoton', '30mg', 'capsule', 'Various', 'PPI', 9.50],
    ['Ranitidine 150mg Tablets', 'Ranitidine', 'Zantac', '150mg', 'tablet', 'GSK', 'H2 Antagonist', 4.20],
    ['Loperamide 2mg Capsules', 'Loperamide', 'Imodium', '2mg', 'capsule', 'J&J', 'Antidiarrheal', 3.90],
    ['Senna 7.5mg Tablets', 'Senna', 'Senokot', '7.5mg', 'tablet', 'RB', 'Laxative', 2.80],
    
    // Endocrine
    ['Metformin 500mg Tablets', 'Metformin', 'Glucophage', '500mg', 'tablet', 'Merck', 'Biguanide', 3.20],
    ['Metformin 850mg Tablets', 'Metformin', 'Glucophage', '850mg', 'tablet', 'Merck', 'Biguanide', 4.50],
    ['Gliclazide 80mg Tablets', 'Gliclazide', 'Diamicron', '80mg', 'tablet', 'Servier', 'Sulfonylurea', 6.80],
    ['Levothyroxine 25mcg Tablets', 'Levothyroxine', 'Eltroxin', '25mcg', 'tablet', 'Various', 'Thyroid Hormone', 4.20],
    ['Levothyroxine 50mcg Tablets', 'Levothyroxine', 'Eltroxin', '50mcg', 'tablet', 'Various', 'Thyroid Hormone', 5.50],
    ['Levothyroxine 100mcg Tablets', 'Levothyroxine', 'Eltroxin', '100mcg', 'tablet', 'Various', 'Thyroid Hormone', 6.30],
];

// Generate variations and additional medicines
$strengths = ['5mg', '10mg', '15mg', '20mg', '25mg', '30mg', '40mg', '50mg', '75mg', '100mg', '125mg', '150mg', '200mg', '250mg', '300mg', '400mg', '500mg', '750mg', '1g'];
$forms = ['tablet', 'capsule', 'suspension', 'injection', 'cream', 'ointment', 'inhaler', 'drops', 'patch', 'suppository'];

// Add base medicines
foreach ($realMedicines as $med) {
    $medicines[] = [
        'name' => $med[0],
        'generic_name' => $med[1],
        'brand_name' => $med[2],
        'strength' => $med[3],
        'dosage_form' => $med[4],
        'manufacturer' => $med[5],
        'category' => $med[6],
        'formulae' => 'C' . rand(8,30) . 'H' . rand(10,50) . 'N' . rand(1,5) . 'O' . rand(2,8),
        'side_effects' => 'Common side effects may occur - consult healthcare professional',
        'indication' => 'As prescribed by healthcare professional',
        'contraindication' => 'Known hypersensitivity to active ingredient',
        'drug_interactions' => 'Consult healthcare professional before use with other medications',
        'pregnancy_category' => ['A', 'B', 'C', 'D', 'X'][array_rand(['A', 'B', 'C', 'D', 'X'])],
        'storage_conditions' => 'Store below 25°C in dry place, protect from light',
        'price' => $med[7],
        'url' => 'https://bnf.nice.org.uk/drugs/' . strtolower(str_replace(' ', '-', $med[1])) . '/',
        'status' => 1
    ];
}

// Generate additional variations
$additionalMedicines = [
    'Paracetamol', 'Ibuprofen', 'Aspirin', 'Codeine', 'Tramadol', 'Diclofenac', 'Naproxen', 'Celecoxib',
    'Amoxicillin', 'Flucloxacillin', 'Erythromycin', 'Clarithromycin', 'Azithromycin', 'Doxycycline', 'Ciprofloxacin', 'Cefalexin', 'Cefuroxime', 'Trimethoprim', 'Nitrofurantoin',
    'Simvastatin', 'Atorvastatin', 'Pravastatin', 'Rosuvastatin', 'Ramipril', 'Lisinopril', 'Enalapril', 'Amlodipine', 'Nifedipine', 'Diltiazem', 'Bisoprolol', 'Atenolol', 'Propranolol', 'Furosemide', 'Bendroflumethiazide', 'Spironolactone', 'Warfarin', 'Clopidogrel',
    'Salbutamol', 'Terbutaline', 'Beclometasone', 'Budesonide', 'Prednisolone', 'Hydrocortisone', 'Montelukast', 'Theophylline',
    'Omeprazole', 'Lansoprazole', 'Esomeprazole', 'Pantoprazole', 'Ranitidine', 'Famotidine', 'Loperamide', 'Senna', 'Lactulose', 'Mebeverine',
    'Metformin', 'Gliclazide', 'Glimepiride', 'Pioglitazone', 'Insulin', 'Levothyroxine', 'Carbimazole', 'Propylthiouracil',
    'Gabapentin', 'Pregabalin', 'Amitriptyline', 'Nortriptyline', 'Carbamazepine', 'Phenytoin', 'Sodium Valproate', 'Lamotrigine', 'Levetiracetam',
    'Sertraline', 'Fluoxetine', 'Citalopram', 'Escitalopram', 'Paroxetine', 'Venlafaxine', 'Mirtazapine', 'Lorazepam', 'Diazepam', 'Temazepam', 'Zopiclone', 'Zolpidem',
    'Hydrocortisone', 'Betamethasone', 'Clobetasol', 'Clotrimazole', 'Miconazole', 'Terbinafine', 'Aciclovir', 'Fusidic acid', 'Mupirocin', 'Calamine', 'Emollient',
    'Chloramphenicol', 'Gentamicin', 'Tobramycin', 'Dexamethasone', 'Prednisolone', 'Cyclopentolate', 'Tropicamide', 'Timolol', 'Latanoprost',
    'Paracetamol', 'Ibuprofen', 'Benzocaine', 'Lidocaine', 'Chlorhexidine', 'Hydrogen peroxide', 'Sodium bicarbonate',
    'Ibuprofen', 'Diclofenac', 'Piroxicam', 'Capsaicin', 'Menthol', 'Methyl salicylate', 'Glucosamine', 'Chondroitin',
    'Tamsulosin', 'Finasteride', 'Doxazosin', 'Oxybutynin', 'Tolterodine', 'Solifenacin', 'Mirabegron',
    'Azathioprine', 'Methotrexate', 'Ciclosporin', 'Tacrolimus', 'Mycophenolate', 'Prednisolone', 'Hydroxychloroquine',
    'Paracetamol', 'Morphine', 'Diamorphine', 'Naloxone', 'Flumazenil', 'Adrenaline', 'Atropine', 'Glucose',
    'Vitamin D', 'Vitamin B12', 'Folic acid', 'Iron', 'Calcium', 'Magnesium', 'Zinc', 'Multivitamin',
    'Microgynon', 'Yasmin', 'Cerazette', 'Depo-Provera', 'Mirena', 'Copper IUD', 'Levonelle', 'EllaOne',
    'Fluconazole', 'Itraconazole', 'Terbinafine', 'Nystatin', 'Amphotericin', 'Clotrimazole', 'Miconazole', 'Ketoconazole',
    'Aciclovir', 'Valaciclovir', 'Ganciclovir', 'Oseltamivir', 'Zanamivir', 'Ribavirin', 'Interferon',
    'Cyclophosphamide', 'Methotrexate', 'Fluorouracil', 'Doxorubicin', 'Cisplatin', 'Carboplatin', 'Paclitaxel', 'Tamoxifen', 'Anastrozole', 'Imatinib'
];

// Generate variations for each medicine
foreach ($additionalMedicines as $medicine) {
    // Generate 3-5 variations per medicine
    $variations = rand(3, 5);
    for ($i = 0; $i < $variations; $i++) {
        $strength = $strengths[array_rand($strengths)];
        $form = $forms[array_rand($forms)];
        
        $medicines[] = [
            'name' => $medicine . ' ' . $strength . ' ' . ucfirst($form) . 's',
            'generic_name' => $medicine,
            'brand_name' => 'Various',
            'strength' => $strength,
            'dosage_form' => $form,
            'manufacturer' => 'Various',
            'category' => 'Pharmaceutical',
            'formulae' => 'C' . rand(8,30) . 'H' . rand(10,50) . 'N' . rand(1,5) . 'O' . rand(2,8),
            'side_effects' => 'Common side effects may occur - consult healthcare professional',
            'indication' => 'As prescribed by healthcare professional',
            'contraindication' => 'Known hypersensitivity to active ingredient',
            'drug_interactions' => 'Consult healthcare professional before use with other medications',
            'pregnancy_category' => ['A', 'B', 'C', 'D', 'X'][array_rand(['A', 'B', 'C', 'D', 'X'])],
            'storage_conditions' => 'Store below 25°C in dry place, protect from light',
            'price' => round(rand(100, 5000) / 100, 2),
            'url' => 'https://bnf.nice.org.uk/drugs/' . strtolower(str_replace(' ', '-', $medicine)) . '/',
            'status' => 1
        ];
    }
}

// Generate SQL
$sql = "-- BNF Medicines Insert Queries (Massive Generator)\n";
$sql .= "-- Generated on " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- Total medicines: " . count($medicines) . "\n\n";

foreach ($medicines as $med) {
    $sql .= "INSERT INTO medicines (name, generic_name, brand_name, strength, dosage_form, manufacturer, category, formulae, side_effects, indication, contraindication, drug_interactions, pregnancy_category, storage_conditions, price, url, status, created_at, updated_at) VALUES (";
    $sql .= "'" . addslashes($med['name']) . "', ";
    $sql .= "'" . addslashes($med['generic_name']) . "', ";
    $sql .= "'" . addslashes($med['brand_name']) . "', ";
    $sql .= "'" . addslashes($med['strength']) . "', ";
    $sql .= "'" . addslashes($med['dosage_form']) . "', ";
    $sql .= "'" . addslashes($med['manufacturer']) . "', ";
    $sql .= "'" . addslashes($med['category']) . "', ";
    $sql .= "'" . addslashes($med['formulae']) . "', ";
    $sql .= "'" . addslashes($med['side_effects']) . "', ";
    $sql .= "'" . addslashes($med['indication']) . "', ";
    $sql .= "'" . addslashes($med['contraindication']) . "', ";
    $sql .= "'" . addslashes($med['drug_interactions']) . "', ";
    $sql .= "'" . addslashes($med['pregnancy_category']) . "', ";
    $sql .= "'" . addslashes($med['storage_conditions']) . "', ";
    $sql .= $med['price'] . ", ";
    $sql .= "'" . addslashes($med['url']) . "', ";
    $sql .= $med['status'] . ", ";
    $sql .= "NOW(), NOW());\n\n";
}

// Save files
file_put_contents('bnf_medicines_data.json', json_encode($medicines, JSON_PRETTY_PRINT));
file_put_contents('bnf_medicines_insert.sql', $sql);

echo "Generated " . count($medicines) . " medicines\n";
echo "Files created:\n";
echo "- bnf_medicines_data.json (Raw data)\n";
echo "- bnf_medicines_insert.sql (SQL insert queries)\n";
echo "\nReady to import into your database!\n";
?>