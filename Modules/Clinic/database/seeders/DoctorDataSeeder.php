<?php

namespace Modules\Clinic\database\seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Clinic\Models\Clinics;
use Modules\Commission\Models\EmployeeCommission;
use Modules\Employee\Models\BranchEmployee;
use Modules\Employee\Models\EmployeeRating;
use Modules\Service\Models\ServiceEmployee;
use Modules\Commission\Models\Commission;
use Modules\Clinic\Models\DoctorSession;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\DoctorClinicMapping;
use Modules\Clinic\Models\ClinicsService;
use Modules\Clinic\Models\DoctorServiceMapping;
use Modules\Clinic\Models\DoctorDocument;
use Modules\Clinic\Models\ClinicServiceMapping;
/**
 * Class UserTableSeeder.
 */
class DoctorDataSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $avatarPath = config('app.avatar_base_path');
        $doctor = [
            [
                'first_name' => 'Felix',
                'last_name' => 'Harris',
                'email' => 'doctor@kivicare.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+91 4578952512',
                'date_of_birth' => fake()->date,
                'file_url' => public_path('/dummy-images/clinic/doctor/felix.png'),
                'avatar' => $avatarPath.'male.webp',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'doctor',
                'about_self' => 'I am committed to delivering compassionate and comprehensive care to patients with cardiac issues.',
                'expert' => 'Cardiology & Orthopadic',
                'address' => '6 Heart Street, Cityville, USA',
                'country' => 230,
                'state' => 3812,
                'city' => 41432,
                'pincode' => '12345',
                'latitude'=>'40.7128',
                'longitude'=>'74.006',
                'experience' => '15 years',
                'degree' => 'MD in Cardiology',
                'university' => 'University of Medical Sciences',
                'year' => '2005',
                'vendor_id' => 1,
                "clinic" => [
                    'heartcare-orthocare-center',
                    'harmony-medical-center'
                ],
                "service" => [
                    "cardiac-consultation",
                    "echocardiography",
                    "angioplasty",
                    "electrophysiology-study-eps",
                    "cardiac-ablation",
                    "implantable-cardioverter-defibrillator-icd-placement",
                    "holter-monitoring",
                    "event-monitoring",
                    "implantable-loop-recorder-ilr",
                    "hypertension-screening",
                    "diagnostic-coronary-angiography",
                    "percutaneous-coronary-intervention-pci",
                    "coronary-artery-bypass-grafting-cabg",
                    "genetic-testing-for-cardiac-conditions",
                    "genetic-counseling-for-heart-diseases",
                    "family-screening-for-inherited-heart-disorders",
                    "orthopedic-sergery",
                    "prosthetic-limb-fitting",
                    "hip-replacement",
                    "knee-replacement",
                    "shoulder-replacement",
                    "spinal-fusion-surgery",
                    "discectomy",
                    "spinal-decompression-treatment",
                    "bone-density-testing",
                    "osteoporosis-medication-treatment",
                    "laparoscopic-surgery",
                    "endoscopic-surgery",
                    "robotic-surgery",
                    "routine-check-ups-and-physical-examinations",
                    "diagnosis-and-treatment-of-common-illnesses",
                    "vaccinations-and-immunizations",
                    "acupuncture",
                    "nutritional-supplements-and-dietary-counseling",
                    "pediatric-primary-care",
                    "childhood-illness-management"
                ]
            ],
            [
                'first_name' => 'Jorge',
                'last_name' => 'Perez',
                'email' => 'jorge@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+91 7485961589',
                'date_of_birth' => fake()->date, // Replace fake()->date with the actual date of birth
                'file_url' => public_path('/dummy-images/clinic/doctor/jorge.png'),
                'avatar' => $avatarPath.'male.webp',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'doctor',
                'about_self' => 'I strive to provide top-quality dental care to patients of all ages, ensuring healthy and beautiful smiles.',
                'expert' => 'Dentistry',
                'address' => '48, Tooth Street, Townsville, USA',
                'country' => 230,
                'state' => 3812,
                'city' => 41432,
                'pincode' => '12345',
                'latitude'=>'34.0522',
                'longitude'=>'-118.2437',
                'experience' => '12 years',
                'degree' => 'DDS in Dentistry',
                'university' => 'New York University',
                'year' => '2010',
                'vendor_id' => 1,
                'clinic' => ['wellness-dental-clinic'],
                'service' => [
                    'teeth-cleaning',
                    'fillings',
                    'wisdom-teeth-removal',
                    'dental-implant-surgery',
                    'tooth-extraction',
                ],
                // 'description' => 'I strive to provide top-quality dental care to patients of all ages, ensuring healthy and beautiful smiles.',
                // 'specialization' => 'Dentistry',
                // 'address' => [
                //     'address_line_1' => '48 Tooth Street',
                //     'address_line_2' => 'Townsville, USA',
                //     'latitude' => '34.0522° N',
                //     'longitude' => '-118.2437° W',
                // ],
                // 'experience_years' => 12,
                // 'qualification' => 'DDS in Dentistry',
                // 'institution' => 'New York University',
                // 'graduation_year' => 2010,
            ],
            [
                'first_name' => 'Erica',
                'last_name' => 'Mendiz',
                'email' => 'erica@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+91 4578965541',
                'date_of_birth' => fake()->date, // Replace fake()->date with the actual date of birth
                'file_url' => public_path('/dummy-images/clinic/doctor/erica.png'),
                'avatar' => $avatarPath.'female.webp',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'doctor',
                'about_self' => 'My focus is on promoting holistic well-being through personalized medical care and lifestyle interventions.',
                'expert' => 'General Medicine',
                'address' => '9, Harmony Road, Healthtown, USA',
                'country' => 230,
                'state' => 3812,
                'city' => 41432,
                'pincode' => '12345',
                'latitude'=>'41.8781',
                'longitude'=>'-87.6298',
                'experience' => '18 years',
                'degree' => 'MD in General Medicine',
                'university' => 'Harvard Medical School',
                'year' => '2004',
                'vendor_id' => 1,
                'clinic' => [
                    'wellness-dental-clinic',
                    'harmony-medical-center'
                ],
                'service' => [
                    'routine-check-ups-and-physical-examinations',
                    'diagnosis-and-treatment-of-common-illnesses',
                    'vaccinations-and-immunizations',
                    'acupuncture',
                    'nutritional-supplements-and-dietary-counseling',
                    'pediatric-primary-care',
                    'childhood-illness-management',
                    'teeth-cleaning',
                    'fillings',
                    'wisdom-teeth-removal',
                    'dental-implant-surgery',
                    'tooth-extraction',
                ],
                // 'description' => 'My focus is on promoting holistic well-being through personalized medical care and lifestyle interventions.',
                // 'specialization' => 'General Medicine',
                // 'address' => [
                //     'address_line_1' => '9 Harmony Road',
                //     'address_line_2' => 'Healthtown, USA',
                //     'latitude' => '41.8781° N',
                //     'longitude' => '-87.6298° W',
                // ],
                // 'experience_years' => 18,
                // 'qualification' => 'MD in General Medicine',
                // 'institution' => 'Harvard Medical School',
                // 'graduation_year' => 2004,
            ],
            [
                'first_name' => 'Parsa',
                'last_name' => 'Evana',
                'email' => 'parsa@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+91 5674587152',
                'date_of_birth' => fake()->date, // Replace fake()->date with the actual date of birth
                'file_url' => public_path('/dummy-images/clinic/doctor/parsa.png'),
                'avatar' => $avatarPath.'female.webp',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'doctor',
                'about_self' => 'I am dedicated to providing exceptional dental services, ensuring optimal oral health and patient satisfaction.',
                'expert' => 'Dentistry',
                'address' => '101, Dental Avenue, Smilesville, USA',
                'country' => 230,
                'state' => 3812,
                'city' => 41432,
                'pincode' => '12345',
                'latitude'=>'37.7749',
                'longitude'=>'-122.4194',
                'experience' => '10 years',
                'degree' => 'DMD in Dentistry',
                'university' => 'University of Pennsylvania',
                'year' => '2012',
                'vendor_id' => 3,
                'clinic' => [
                    'dermatology-associates',
                ],
                'service' => [
                    'skin-cancer-removal',
                    'mohs-micrographic-surgery',
                    'dermal-fillers',
                    'chemical-peels',
                   
                ],
                // // 'description' => 'I am dedicated to providing exceptional dental services, ensuring optimal oral health and patient satisfaction.',
                // // 'specialization' => 'Dentistry',
                // // 'address' => [
                // //     'address_line_1' => '101 Dental Avenue',
                // //     'address_line_2' => 'Smilesville, USA',
                // //     'latitude' => '37.7749° N',
                // //     'longitude' => '-122.4194° W',
                // // ],
                // // 'experience_years' => 10,
                // // 'qualification' => 'DMD in Dentistry',
                // // 'institution' => 'University of Pennsylvania',
                // 'graduation_year' => 2012,
            ],
            [
                'first_name' => 'Daniel',
                'last_name' => 'Williams',
                'email' => 'daniel@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+91 2563987415',
                'date_of_birth' => fake()->date, // Replace fake()->date with the actual date of birth
                'file_url' => public_path('/dummy-images/clinic/doctor/daniel.png'),
                'avatar' => $avatarPath.'male.webp',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'doctor',
                'about_self' => 'I am committed to delivering high-quality eye care, focusing on improving vision and enhancing ocular health.',
                'expert' => 'Ophthalmology',
                'address' => '23, Vision Way, Seecity, USA',
                'country' => 230,
                'state' => 3812,
                'city' => 41432,
                'pincode' => '12345',
                'latitude'=>'33.749',
                'longitude'=>'-84.388',
                'experience' => '14 years',
                'degree' => 'MD in Ophthalmology',
                'university' => 'Johns Hopkins University',
                'year' => '2008',
                'vendor_id' => 4,
                'clinic' => [
                    'visionplus-eye-clinic',
                ],
                'service' => [
                    'cataract-surgery',
                    'lasik-eye-surgery',
                    'comprehensive-eye-examinations',
                    'contact-lens-fitting',
                ],
                // 'description' => 'I am committed to delivering high-quality eye care, focusing on improving vision and enhancing ocular health.',
                // 'specialization' => 'Ophthalmology',
                // 'address' => [
                //     'address_line_1' => '6 Vision Way',
                //     'address_line_2' => 'Seecity, USA',
                //     'latitude' => '33.7490° N',
                //     'longitude' => '-84.3880° W',
                // ],
                // 'experience_years' => 14,
                // 'qualification' => 'MD in Ophthalmology',
                // 'institution' => 'Johns Hopkins University',
                // 'graduation_year' => 2008,
            ]

        ];

        if (env('IS_DUMMY_DATA')) {
            $days = [
                ['day' => 'monday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'tuesday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'wednesday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'thursday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'friday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'saturday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'sunday', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'is_holiday' => true, 'breaks' => []],
            ];
            foreach ($doctor  as $key => $employee_data) {

                $image = $employee_data['file_url'] ?? null;
                $empData = Arr::except($employee_data, [ 'file_url','about_self','expert','vendor_id','experience','degree','university','year' ,'clinic','service']);
                $emp = User::create($empData);
                $emp->assignRole($emp->user_type);

                if (isset($image)) {
                    $this->attachFeatureImage($emp, $image);
                }

                UserProfile::create([
                    'user_id' =>$emp->id,
                    'about_self' => $employee_data['about_self'],   
                    'expert' => $employee_data['expert'], 
                    'facebook_link' => $emp->facebook_link,
                    'instagram_link' => $emp->instagram_link,
                    'twitter_link' => $emp->twitter_link,
                    'dribbble_link' => $emp->dribbble_link,
                ]);

                $doctor_data = [
                    'doctor_id' => $emp->id,
                    'vendor_id' => $employee_data['vendor_id'],
                    'experience' => $employee_data['experience'],
                ];
                Doctor::create($doctor_data);

                $doctor_documents = [
                    'doctor_id' => $emp->id,
                    'degree' => $employee_data['degree'],
                    'university' => $employee_data['university'],
                    'year' => $employee_data['year'],
                ];
                DoctorDocument::create($doctor_documents);

                EmployeeCommission::create([
                    'employee_id' => $emp->id,
                    'commission_id' => 2,
                ]);

                if(!empty($employee_data['clinic'])){
                    foreach ($employee_data['clinic'] as $key => $value) {
                       $clinicData = Clinics::where('slug',$value)->first();
                     
                        $doctor_clinic = [
                            'doctor_id' => $emp->id,
                            'clinic_id' => $clinicData->id,
                        ];

                        DoctorClinicMapping::create($doctor_clinic);

                        foreach ($days as $key => $val) {

                            $val['clinic_id'] = $clinicData->id;
                            $val['doctor_id'] =  $emp->id;

                         
                            DoctorSession::updateOrCreate(
                                ['clinic_id' => $val['clinic_id'], 'doctor_id' => $val['doctor_id'], 'day' => $val['day']],
                                $val
                            );
                            
                        }
                    }
                }
                if (!empty($employee_data['clinic']) && !empty($employee_data['service'])) {
                    foreach ($employee_data['clinic'] as $clinic_slug) {
                        // Find the clinic
                        $clinicData = Clinics::where('slug', $clinic_slug)->first();
                        $service = $employee_data['service'];
                        // If clinic is found, find services offered at that clinic
                        if ($clinicData) {
                            $services_at_clinic = ClinicServiceMapping::where('clinic_id', $clinicData->id)->with('service')
                            ->whereHas('service.systemservice', function ($query) use ($service) {
                                $query->whereIn('slug', $service);
                            })
                            ->get();
                                
            
                            // Iterate through services offered at this clinic
                            foreach ($services_at_clinic as $service) {
                                $service_data = [
                                    'doctor_id' => $emp->id,
                                    'clinic_id' => $clinicData->id,
                                    'service_id' => $service->service_id,
                                    'charges' => optional($service->service)->charges,
                                ];

                                DoctorServiceMapping::updateOrCreate(
                                    ['service_id' => $service->service_id, 'clinic_id' =>$clinicData->id, 'doctor_id' => $emp->id],
                                    $service_data
                                );
                               
                            }
                        }
                    }
                }


            }

        }

          Schema::enableForeignKeyConstraints();
      }

      private function attachFeatureImage($model, $publicPath)
      {
          if(!env('IS_DUMMY_DATA_IMAGE')) return false;

          $file = new \Illuminate\Http\File($publicPath);

          $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('profile_image');

          return $media;
      }
}
