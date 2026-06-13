<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Auth\NurseRoleSeeder;
use Database\Seeders\Auth\NurseDataSeeder;

/**
 * Class NurseSeeder
 * Master seeder for all nurse-related data
 */
class NurseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🚀 Starting Nurse System Setup...');
        $this->command->newLine();

        // Step 1: Create Nurse Role and Permissions
        $this->command->info('📝 Creating Nurse Role and Permissions...');
        $this->call(NurseRoleSeeder::class);
        $this->command->newLine();

        // Step 2: Create Sample Nurse Data
        $this->command->info('👩‍⚕️ Creating Sample Nurse Users...');
        $this->call(NurseDataSeeder::class);
        $this->command->newLine();

        $this->command->info('🎉 Nurse System Setup Complete!');
        $this->command->newLine();
        
        $this->command->info('📋 Summary:');
        $this->command->info('   ✅ Nurse role created with comprehensive permissions');
        $this->command->info('   ✅ 5 sample nurses created');
        $this->command->info('   ✅ Nurse database table ready');
        $this->command->info('   ✅ User relationships established');
        $this->command->newLine();
        
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('   Email: sarah.johnson@clinic.com (Head Nurse)');
        $this->command->info('   Email: maria.garcia@clinic.com');
        $this->command->info('   Email: jennifer.smith@clinic.com');
        $this->command->info('   Email: amanda.wilson@clinic.com');
        $this->command->info('   Email: lisa.brown@clinic.com');
        $this->command->info('   Password: password123 (for all)');
        $this->command->newLine();
        
        $this->command->info('🏥 Next Steps:');
        $this->command->info('   1. Run: php artisan migrate (if not done)');
        $this->command->info('   2. Create Nurse management controllers');
        $this->command->info('   3. Add Nurse menu items');
        $this->command->info('   4. Implement Nurse-specific features');
    }
}