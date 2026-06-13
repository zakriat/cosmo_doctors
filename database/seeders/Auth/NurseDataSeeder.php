<?php

namespace Database\Seeders\Auth;

use App\Models\User;
use App\Models\Nurse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

/**
 * Class NurseDataSeeder
 * Creates sample nurse users and data for testing
 */
class NurseDataSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // Sample nurse data
        $nurses = [
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@clinic.com',
                'mobile' => '+1234567890',
                'license_number' => 'RN123456',
                'specialization' => 'Emergency Care',
                'shift_type' => 'day',
                'is_head_nurse' => true,
                'certifications' => ['BLS', 'ACLS', 'PALS'],
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'email' => 'maria.garcia@clinic.com',
                'mobile' => '+1234567891',
                'license_number' => 'RN123457',
                'specialization' => 'Pediatric Care',
                'shift_type' => 'day',
                'is_head_nurse' => false,
                'certifications' => ['BLS', 'PALS'],
            ],
            [
                'first_name' => 'Jennifer',
                'last_name' => 'Smith',
                'email' => 'jennifer.smith@clinic.com',
                'mobile' => '+1234567892',
                'license_number' => 'RN123458',
                'specialization' => 'Critical Care',
                'shift_type' => 'night',
                'is_head_nurse' => false,
                'certifications' => ['BLS', 'ACLS', 'CCRN'],
            ],
            [
                'first_name' => 'Amanda',
                'last_name' => 'Wilson',
                'email' => 'amanda.wilson@clinic.com',
                'mobile' => '+1234567893',
                'license_number' => 'RN123459',
                'specialization' => 'Medical-Surgical',
                'shift_type' => 'rotating',
                'is_head_nurse' => false,
                'certifications' => ['BLS', 'Med-Surg Certified'],
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Brown',
                'email' => 'lisa.brown@clinic.com',
                'mobile' => '+1234567894',
                'license_number' => 'RN123460',
                'specialization' => 'Geriatric Care',
                'shift_type' => 'day',
                'is_head_nurse' => false,
                'certifications' => ['BLS', 'Gerontology Certified'],
            ],
        ];

        foreach ($nurses as $nurseData) {
            // Create user account
            $user = User::create([
                'first_name' => $nurseData['first_name'],
                'last_name' => $nurseData['last_name'],
                'email' => $nurseData['email'],
                'mobile' => $nurseData['mobile'],
                'password' => Hash::make('password123'),
                'email_verified_at' => Carbon::now(),
                'user_type' => 'nurse',
                'status' => 1,
                'gender' => 'female', // Default for sample data
                'date_of_birth' => Carbon::now()->subYears(rand(25, 45))->format('Y-m-d'),
            ]);

            // Assign nurse role
            $user->assignRole('nurse');

            // Create nurse profile
            Nurse::create([
                'nurse_id' => $user->id,
                'clinic_id' => 1, // Assuming clinic ID 1 exists
                'vendor_id' => 1, // Assuming vendor ID 1 exists
                'license_number' => $nurseData['license_number'],
                'specialization' => $nurseData['specialization'],
                'license_expiry' => Carbon::now()->addYears(2), // License expires in 2 years
                'certifications' => $nurseData['certifications'],
                'shift_type' => $nurseData['shift_type'],
                'is_head_nurse' => $nurseData['is_head_nurse'],
                'notes' => 'Sample nurse created by seeder',
            ]);

            $this->command->info("✅ Created nurse: {$nurseData['first_name']} {$nurseData['last_name']} ({$nurseData['email']})");
        }

        Schema::enableForeignKeyConstraints();

        $this->command->info('🏥 Nurse data seeding completed!');
        $this->command->info('📋 Created ' . count($nurses) . ' sample nurses');
        $this->command->info('🔑 Default password for all nurses: password123');
        $this->command->info('👩‍⚕️ Head Nurse: Sarah Johnson');
    }
}