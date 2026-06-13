<?php

namespace Modules\Clinic\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\ClinicsCategory;

class EnhancedBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main services
        $services = [
            [
                'name' => 'Specialist Services',
                'description' => 'Professional specialist consultations and treatments',
                'type' => 'in_clinic',
                'charges' => 0,
                'service_classification' => 'doctor_required',
                'status' => 1,
            ],
            [
                'name' => 'Private GP Services',
                'description' => 'General practitioner services and consultations',
                'type' => 'in_clinic',
                'charges' => 0,
                'service_classification' => 'doctor_required',
                'status' => 1,
            ],
            [
                'name' => 'Blood Tests & Laboratory',
                'description' => 'Comprehensive blood testing and laboratory services',
                'type' => 'in_clinic',
                'charges' => 0,
                'service_classification' => 'doctor_optional',
                'status' => 1,
            ],
            [
                'name' => 'Private Scans & Imaging',
                'description' => 'Medical imaging and diagnostic scans',
                'type' => 'in_clinic',
                'charges' => 0,
                'service_classification' => 'doctor_optional',
                'status' => 1,
            ],
        ];

        foreach ($services as $serviceData) {
            $service = ClinicsService::create($serviceData);
            
            // Create categories for each service
            $this->createCategoriesForService($service);
        }
    }

    private function createCategoriesForService($service)
    {
        $categoriesData = [];

        switch ($service->name) {
            case 'Specialist Services':
                $categoriesData = [
                    ['name' => 'Audiology', 'price' => 150, 'classification' => 'doctor_required'],
                    ['name' => 'Cardiology Consultations & Scans', 'price' => 200, 'classification' => 'doctor_required'],
                    ['name' => 'Dermatology', 'price' => 120, 'classification' => 'doctor_required'],
                    ['name' => 'Diabetology & Endocrinology', 'price' => 180, 'classification' => 'doctor_required'],
                    ['name' => 'Ear, Nose and Throat', 'price' => 140, 'classification' => 'doctor_required'],
                    ['name' => 'Gynaecology', 'price' => 160, 'classification' => 'doctor_required'],
                ];
                break;

            case 'Private GP Services':
                $categoriesData = [
                    ['name' => 'Private GP Services', 'price' => 80, 'classification' => 'doctor_required'],
                    ['name' => 'Visa Medicals', 'price' => 150, 'classification' => 'doctor_required'],
                    ['name' => 'Private Prescriptions', 'price' => 30, 'classification' => 'doctor_required'],
                    ['name' => 'Private Contraception', 'price' => 60, 'classification' => 'doctor_required'],
                    ['name' => 'Hayfever Treatment', 'price' => 50, 'classification' => 'doctor_required'],
                ];
                break;

            case 'Blood Tests & Laboratory':
                $categoriesData = [
                    ['name' => 'Well Person Blood Test', 'price' => 220, 'classification' => 'no_doctor_required'],
                    ['name' => 'Lifestyle Blood Test', 'price' => 288, 'classification' => 'no_doctor_required'],
                    ['name' => 'Ultimate Health Screen', 'price' => 649, 'classification' => 'doctor_required'],
                    ['name' => 'Allergy & Immunology Tests', 'price' => 150, 'classification' => 'no_doctor_required'],
                    ['name' => 'Cardiovascular Health', 'price' => 180, 'classification' => 'no_doctor_required'],
                ];
                break;

            case 'Private Scans & Imaging':
                $categoriesData = [
                    ['name' => 'CT Scans', 'price' => 400, 'classification' => 'doctor_optional'],
                    ['name' => 'MRI Scans', 'price' => 500, 'classification' => 'doctor_optional'],
                    ['name' => 'Pregnancy Ultrasound', 'price' => 120, 'classification' => 'doctor_required'],
                    ['name' => 'Medical Ultrasound', 'price' => 150, 'classification' => 'doctor_optional'],
                    ['name' => 'X-ray', 'price' => 80, 'classification' => 'no_doctor_required'],
                ];
                break;
        }

        foreach ($categoriesData as $categoryData) {
            ClinicsCategory::create([
                'slug' => \Str::slug($categoryData['name']),
                'name' => $categoryData['name'],
                'description' => "Professional {$categoryData['name']} services",
                'parent_id' => $service->id, // Link category to service
                'price' => $categoryData['price'],
                'service_classification' => $categoryData['classification'],
                'status' => 1,
                'featured' => 1,
            ]);
        }
    }
}