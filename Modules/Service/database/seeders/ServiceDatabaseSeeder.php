<?php

namespace Modules\Service\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Service\Models\Service;
class ServiceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if dummy data is enabled
        if (env('IS_DUMMY_DATA')) {
            $data = [
                [
                    'slug' => 'general-checkup',
                    'name' => 'General Checkup',
                    'description' => 'A routine general health checkup.',
                    'duration_min' => 30,
                    'default_price' => 50.00,
                    'category_id' => 1, // Assuming health category ID
                    'status' => 1,
                ],
                [
                    'slug' => 'vaccination',
                    'name' => 'Vaccination',
                    'description' => 'Administration of vaccines for disease prevention.',
                    'duration_min' => 15,
                    'default_price' => 25.00,
                    'category_id' => 1, // Assuming health category ID
                    'status' => 1,
                ],
            ];

            // Insert data into the database
            foreach ($data as $serviceData) {
                Service::create($serviceData);
            }
        }
    }
}
