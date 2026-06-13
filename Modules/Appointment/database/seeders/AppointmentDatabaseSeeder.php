<?php

namespace Modules\Appointment\database\seeders;

use Illuminate\Database\Seeder;

class AppointmentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('IS_DUMMY_DATA')){
            $this->call(AppointmentsTableSeeder::class);
            $this->call(AppointmentTransactionsTableSeeder::class);
            $this->call(PatientEncounterSeeder::class);
            // $this->call(AppointmentPatientRecordsTableSeeder::class);
            $this->call(AppointmentPatientBodychartTableSeeder::class);
        }
    }
}
