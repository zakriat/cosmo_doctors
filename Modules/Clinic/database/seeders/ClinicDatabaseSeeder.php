<?php

namespace Modules\Clinic\database\seeders;

use Illuminate\Database\Seeder;
class ClinicDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(ClinicTableSeeder::class);
        $this->call(ClinicCategoryTableSeeder::class);
        //$this->call(ClinicServiceTableSeeder::class);
        $this->call(SystemServiceTableSeeder::class);
        $this->call(DoctorDataSeeder::class);
        $this->call(ReceptionistSeeder::class);
    }
}
