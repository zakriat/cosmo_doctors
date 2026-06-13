<?php

/**
 * Service Structure Analysis Script
 * This script shows how services, categories, and doctors are connected
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=====================================\n";
echo "SERVICE STRUCTURE ANALYSIS\n";
echo "=====================================\n\n";

// 1. Show all top-level services (system_service_id = NULL)
echo "1. TOP-LEVEL SERVICES (System Services)\n";
echo "----------------------------------------\n";
$topServices = \Modules\Clinic\Models\ClinicsService::whereNull('system_service_id')
    ->orderBy('name')
    ->get(['id', 'name', 'charges', 'status', 'category_id', 'subcategory_id']);

if ($topServices->count() > 0) {
    foreach ($topServices as $service) {
        echo "ID: {$service->id}\n";
        echo "Name: {$service->name}\n";
        echo "Charges: £{$service->charges}\n";
        echo "Status: " . ($service->status ? 'Active' : 'Inactive') . "\n";
        echo "Category ID: " . ($service->category_id ?? 'NULL') . "\n";
        echo "Subcategory ID: " . ($service->subcategory_id ?? 'NULL') . "\n";
        
        // Count children
        $childCount = \Modules\Clinic\Models\ClinicsService::where('system_service_id', $service->id)->count();
        echo "Child Services: {$childCount}\n";
        echo "---\n";
    }
} else {
    echo "No top-level services found.\n";
}

echo "\n\n";

// 2. Show all categories
echo "2. CATEGORIES (Medical Specialties)\n";
echo "----------------------------------------\n";
$categories = \Modules\Clinic\Models\ClinicsCategory::orderBy('name')->get(['id', 'name', 'parent_id', 'price', 'service_classification', 'status']);

if ($categories->count() > 0) {
    foreach ($categories as $category) {
        echo "ID: {$category->id}\n";
        echo "Name: {$category->name}\n";
        echo "Parent Service ID: " . ($category->parent_id ?? 'NULL') . "\n";
        echo "Price: £" . ($category->price ?? '0.00') . "\n";
        echo "Classification: " . ($category->service_classification ?? 'N/A') . "\n";
        echo "Status: " . ($category->status ? 'Active' : 'Inactive') . "\n";
        echo "---\n";
    }
} else {
    echo "No categories found.\n";
}

echo "\n\n";

// 3. Show child services (services with system_service_id)
echo "3. CHILD SERVICES (Bookable Services)\n";
echo "----------------------------------------\n";
$childServices = \Modules\Clinic\Models\ClinicsService::whereNotNull('system_service_id')
    ->orderBy('system_service_id')
    ->orderBy('name')
    ->get(['id', 'name', 'system_service_id', 'charges', 'category_id', 'subcategory_id', 'status']);

if ($childServices->count() > 0) {
    $currentParent = null;
    foreach ($childServices as $service) {
        if ($currentParent !== $service->system_service_id) {
            $currentParent = $service->system_service_id;
            $parent = \Modules\Clinic\Models\ClinicsService::find($service->system_service_id);
            $parentName = $parent ? $parent->name : 'Unknown';
            echo "\n>>> PARENT: {$parentName} (ID: {$service->system_service_id})\n\n";
        }
        
        echo "  Child ID: {$service->id}\n";
        echo "  Name: {$service->name}\n";
        echo "  Charges: £{$service->charges}\n";
        echo "  Category ID: " . ($service->category_id ?? 'NULL') . "\n";
        echo "  Subcategory ID: " . ($service->subcategory_id ?? 'NULL') . "\n";
        echo "  Status: " . ($service->status ? 'Active' : 'Inactive') . "\n";
        echo "  ---\n";
    }
} else {
    echo "No child services found.\n";
}

echo "\n\n";

// 4. Show "Private GP" related services
echo "4. 'PRIVATE GP' SERVICES ANALYSIS\n";
echo "----------------------------------------\n";
$privateGPServices = \Modules\Clinic\Models\ClinicsService::where('name', 'LIKE', '%Private GP%')
    ->orWhere('name', 'LIKE', '%private gp%')
    ->get();

if ($privateGPServices->count() > 0) {
    foreach ($privateGPServices as $service) {
        echo "ID: {$service->id}\n";
        echo "Name: {$service->name}\n";
        echo "System Service ID: " . ($service->system_service_id ?? 'NULL (Top-level service)') . "\n";
        echo "Charges: £{$service->charges}\n";
        echo "Category ID: " . ($service->category_id ?? 'NULL') . "\n";
        echo "Subcategory ID: " . ($service->subcategory_id ?? 'NULL') . "\n";
        
        if ($service->system_service_id) {
            $parent = \Modules\Clinic\Models\ClinicsService::find($service->system_service_id);
            echo "Parent Name: " . ($parent ? $parent->name : 'Not found') . "\n";
        }
        
        // Check if it has children
        $children = \Modules\Clinic\Models\ClinicsService::where('system_service_id', $service->id)->get(['id', 'name']);
        if ($children->count() > 0) {
            echo "Children:\n";
            foreach ($children as $child) {
                echo "  - {$child->name} (ID: {$child->id})\n";
            }
        }
        
        echo "---\n";
    }
} else {
    echo "No 'Private GP' services found in clinics_services table.\n";
}

// Check in categories table
$privateGPCategories = \Modules\Clinic\Models\ClinicsCategory::where('name', 'LIKE', '%Private GP%')
    ->orWhere('name', 'LIKE', '%private gp%')
    ->get();

if ($privateGPCategories->count() > 0) {
    echo "\nFound in clinics_categories table:\n";
    foreach ($privateGPCategories as $category) {
        echo "Category ID: {$category->id}\n";
        echo "Name: {$category->name}\n";
        echo "Parent Service ID: " . ($category->parent_id ?? 'NULL') . "\n";
        echo "Price: £" . ($category->price ?? '0.00') . "\n";
        echo "Classification: " . ($category->service_classification ?? 'N/A') . "\n";
        
        if ($category->parent_id) {
            $parent = \Modules\Clinic\Models\ClinicsService::find($category->parent_id);
            echo "Parent Service Name: " . ($parent ? $parent->name : 'Not found') . "\n";
        }
        echo "---\n";
    }
}

echo "\n\n";

// 5. Show doctor assignments
echo "5. DOCTOR ASSIGNMENTS\n";
echo "----------------------------------------\n";

// Get all doctors
$doctors = \Modules\Clinic\Models\Doctor::with('user:id,first_name,last_name')->get();

if ($doctors->count() > 0) {
    foreach ($doctors as $doctor) {
        $userName = $doctor->user ? $doctor->user->first_name . ' ' . $doctor->user->last_name : 'Unknown';
        echo "Doctor: {$userName} (ID: {$doctor->id}, User ID: {$doctor->doctor_id})\n";
        
        // Service mappings
        $serviceMappings = \Modules\Clinic\Models\DoctorServiceMapping::where('doctor_id', $doctor->doctor_id)
            ->get();
        
        if ($serviceMappings->count() > 0) {
            echo "  Assigned Services:\n";
            foreach ($serviceMappings as $mapping) {
                $service = \Modules\Clinic\Models\ClinicsService::find($mapping->service_id);
                $serviceName = $service ? $service->name : 'Unknown';
                echo "    - {$serviceName} (Service ID: {$mapping->service_id}, Clinic ID: {$mapping->clinic_id})\n";
            }
        } else {
            echo "  No service assignments\n";
        }
        
        // Category mappings
        $categoryMappings = \Modules\Clinic\Models\DoctorCategoryMapping::where('doctor_id', $doctor->doctor_id)
            ->get();
        
        if ($categoryMappings->count() > 0) {
            echo "  Assigned Categories:\n";
            foreach ($categoryMappings as $mapping) {
                $category = \Modules\Clinic\Models\ClinicsCategory::find($mapping->category_id);
                $categoryName = $category ? $category->name : 'Unknown';
                echo "    - {$categoryName} (Category ID: {$mapping->category_id}, Clinic ID: {$mapping->clinic_id})\n";
            }
        } else {
            echo "  No category assignments\n";
        }
        
        echo "---\n";
    }
} else {
    echo "No doctors found.\n";
}

echo "\n\n";

// 6. Show complete hierarchy for one example
echo "6. COMPLETE HIERARCHY EXAMPLE\n";
echo "----------------------------------------\n";

// Find a service with children
$exampleParent = \Modules\Clinic\Models\ClinicsService::whereNull('system_service_id')->first();

if ($exampleParent) {
    echo "PARENT SERVICE: {$exampleParent->name} (ID: {$exampleParent->id})\n";
    echo "├── Charges: £{$exampleParent->charges}\n";
    echo "├── Status: " . ($exampleParent->status ? 'Active' : 'Inactive') . "\n";
    echo "├── Category ID: " . ($exampleParent->category_id ?? 'NULL') . "\n";
    echo "│\n";
    
    // Get categories under this service
    $categories = \Modules\Clinic\Models\ClinicsCategory::where('parent_id', $exampleParent->id)->get();
    
    if ($categories->count() > 0) {
        echo "└── CATEGORIES:\n";
        foreach ($categories as $index => $category) {
            $isLast = ($index === $categories->count() - 1);
            $prefix = $isLast ? '    └──' : '    ├──';
            
            echo "{$prefix} {$category->name} (ID: {$category->id})\n";
            echo "        ├── Price: £{$category->price}\n";
            echo "        ├── Classification: {$category->service_classification}\n";
            
            // Check doctor assignments to this category
            $doctorMappings = \Modules\Clinic\Models\DoctorCategoryMapping::where('category_id', $category->id)->get();
            
            if ($doctorMappings->count() > 0) {
                echo "        └── Assigned Doctors:\n";
                foreach ($doctorMappings as $dm) {
                    $doctor = \Modules\Clinic\Models\Doctor::where('doctor_id', $dm->doctor_id)->first();
                    $user = $doctor ? \App\Models\User::find($doctor->doctor_id) : null;
                    $doctorName = $user ? $user->first_name . ' ' . $user->last_name : 'Unknown';
                    echo "            - {$doctorName}\n";
                }
            } else {
                echo "        └── No doctors assigned\n";
            }
            
            if (!$isLast) {
                echo "    │\n";
            }
        }
    } else {
        echo "└── No categories found\n";
    }
} else {
    echo "No parent services found.\n";
}

echo "\n\n";

// 7. Summary
echo "7. SUMMARY\n";
echo "----------------------------------------\n";
$totalTopServices = \Modules\Clinic\Models\ClinicsService::whereNull('system_service_id')->count();
$totalChildServices = \Modules\Clinic\Models\ClinicsService::whereNotNull('system_service_id')->count();
$totalCategories = \Modules\Clinic\Models\ClinicsCategory::count();
$totalDoctors = \Modules\Clinic\Models\Doctor::count();
$totalClinics = \Modules\Clinic\Models\Clinics::count();

echo "Total Top-Level Services: {$totalTopServices}\n";
echo "Total Child Services: {$totalChildServices}\n";
echo "Total Categories: {$totalCategories}\n";
echo "Total Doctors: {$totalDoctors}\n";
echo "Total Clinics: {$totalClinics}\n";

echo "\n=====================================\n";
echo "ANALYSIS COMPLETE\n";
echo "=====================================\n";
