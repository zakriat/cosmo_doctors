<?php

namespace Database\Seeders\Auth;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Class NurseRoleSeeder
 * Creates Nurse role with appropriate permissions for clinic management system
 */
class NurseRoleSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // Create Nurse Role
        $nurse = Role::firstOrCreate([
            'name' => 'nurse',
            'title' => 'Nurse',
            'is_fixed' => true
        ]);

        // Create Nurse-specific permissions
        $nursePermissions = [
            // Clinic Nurse Management
            'view_clinic_nurse_list',
            'add_clinic_nurse_list',
            'edit_clinic_nurse_list',
            'delete_clinic_nurse_list',
            
            // Patient Care permissions
            'view_patient_vitals',
            'add_patient_vitals',
            'edit_patient_vitals',
            'delete_patient_vitals',
            
            // Nursing Notes
            'view_nursing_notes',
            'add_nursing_notes',
            'edit_nursing_notes',
            'delete_nursing_notes',
            
            // Medication Administration
            'view_medication_administration',
            'add_medication_administration',
            'edit_medication_administration',
            
            // Care Plans
            'view_care_plans',
            'add_care_plans',
            'edit_care_plans',
            
            // Nursing Assessments
            'view_nursing_assessments',
            'add_nursing_assessments',
            'edit_nursing_assessments',
        ];

        // Create permissions if they don't exist
        foreach ($nursePermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'is_fixed' => true
            ]);
        }

        // Assign comprehensive permissions to Nurse role
        $nurse->givePermissionTo([
            // Clinic and Service Access
            'view_clinics_center',
            'view_clinics_service',
            
            // Appointment Management (View and limited add)
            'view_clinic_appointment_list',
            'add_clinic_appointment_list',
            
            // Patient Management
            'view_customer',
            'edit_customer',
            'add_customer',
            
            // Doctor Information (View only)
            'view_doctors',
            'view_doctors_session',
            
            // Encounter Management (Full access for nursing care)
            'view_encounter',
            'add_encounter',
            'edit_encounter',
            
            // Billing Records (View only)
            'view_billing_record',
            
            // Settings (Limited)
            'view_setting',
            
            // Nurse-specific permissions
            'view_clinic_nurse_list',
            'add_clinic_nurse_list',
            'edit_clinic_nurse_list',
            'delete_clinic_nurse_list',
            
            // Patient Care
            'view_patient_vitals',
            'add_patient_vitals',
            'edit_patient_vitals',
            'delete_patient_vitals',
            
            // Nursing Documentation
            'view_nursing_notes',
            'add_nursing_notes',
            'edit_nursing_notes',
            'delete_nursing_notes',
            
            // Medication Management
            'view_medication_administration',
            'add_medication_administration',
            'edit_medication_administration',
            
            // Care Planning
            'view_care_plans',
            'add_care_plans',
            'edit_care_plans',
            
            // Assessments
            'view_nursing_assessments',
            'add_nursing_assessments',
            'edit_nursing_assessments',
        ]);

        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Nurse role created successfully with ' . count($nurse->permissions) . ' permissions');
        $this->command->info('📋 Nurse can now:');
        $this->command->info('   • Manage patient care and vitals');
        $this->command->info('   • Create nursing notes and assessments');
        $this->command->info('   • Administer medications');
        $this->command->info('   • View appointments and patient records');
        $this->command->info('   • Access clinic information');
    }
}