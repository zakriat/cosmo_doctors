<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Incidence;
use Carbon\Carbon;

class IncidenceSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Incidence::truncate();


        if (env('IS_DUMMY_DATA') == false) {

            Schema::enableForeignKeyConstraints();
            return;
        }

        $now = Carbon::now();

        $incidences = [
            // ✅ With Images
            [
                'user_id' => 9,
                'title' => 'Broken Medical Equipment',
                'description' => 'The ECG monitor is not functioning properly.',
                'phone' => '+91 4578952512',
                'email' => 'john@gmail.com',
                'status' => 1,
                'incident_type' => 1,
                'reply' => 'Technician will replace the ECG monitor tomorrow.',
                'image_path' => public_path('dummy-images/incidence/incidence_1.png'),
                'created_by' => 1,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => $now->copy()->subDays(0),
                'updated_at' => $now->copy()->subDays(0),
                'incident_closed_date' => null,
            ],
            [
                'user_id' => 9,
                'title' => 'Water Leakage in ICU',
                'description' => 'Water Leakage in ICU near the oxygen supply line.',
                'phone' => '+91 4578952512',
                'email' => 'john@gmail.com',
                'status' => 2,
                'incident_type' => 2,
                'reply' => 'Maintenance resolved the leakage.',
                'image_path' => null,
                'created_by' => 2,
                'updated_by' => 2,
                'deleted_by' => null,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
                'incident_closed_date' => $now->copy()->subDays(0),
            ],
            [
                'user_id' => 9,
                'title' => 'Delay in Pharmacy Services',
                'description' => 'Patients waiting over 30 minutes.',
                'phone' => '+91 4578952512',
                'email' => 'john@gmail.com',
                'status' => 3,
                'incident_type' => 3,
                'reply' => 'We are adding more staff.',
                'image_path' => public_path('dummy-images/incidence/incidenec_3.jpg'),
                'created_by' => 3,
                'updated_by' => 3,
                'deleted_by' => null,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
                'incident_closed_date' => null,
            ],
            [
                'user_id' => 10,
                'title' => 'Insufficient Wheelchairs',
                'description' => 'Only 2 wheelchairs available.',
                'phone' => '+91 7485961545',
                'email' => 'robert@gmail.com',
                'status' => 2,
                'incident_type' => 2,
                'reply' => 'New ones arriving next week.',
                'image_path' => null,
                'created_by' => 5,
                'updated_by' => 5,
                'deleted_by' => null,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(4),
                'incident_closed_date' => $now->copy()->subDays(3),
            ],

            [
                'user_id' => 11,
                'title' => 'Lift Malfunctioning',
                'description' => 'Lift near reception not working.',
                'phone' => '+91 2563987448',
                'email' => 'bentley@gmail.com',
                'status' => 1,
                'incident_type' => 1,
                'reply' => '',
                'image_path' => public_path('dummy-images/incidence/incidence_5.jpeg'),
                'created_by' => 6,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
                'incident_closed_date' => null,
            ],
            [
                'user_id' => 12,
                'title' => 'Complaint About Security Staff',
                'description' => 'Family was not treated politely.',
                'phone' => '+91 3565478912',
                'email' => 'brian@gmail.com',
                'status' => 3,
                'incident_type' => 3,
                'reply' => '',
                'image_path' => null,
                'created_by' => 7,
                'updated_by' => 7,
                'deleted_by' => null,
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(6),
                'incident_closed_date' => null,
            ],
            [
                'user_id' => 13,
                'title' => 'No Soap in Washrooms',
                'description' => 'Washrooms on 2nd floor lack soap.',
                'phone' => '+91 5674587110',
                'email' => 'gilbert@gmail.com',
                'status' => 2,
                'incident_type' => 2,
                'reply' => 'Soaps restocked.',
                'image_path' => null,
                'created_by' => 8,
                'updated_by' => 8,
                'deleted_by' => null,
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(7),
                'incident_closed_date' => $now->copy()->subDays(6),
            ],
            [
                'user_id' => 14,
                'title' => 'AC Not Working in Waiting Room',
                'description' => 'AC stopped working in the outpatient lobby.',
                'phone' => '+91 7418529630',
                'email' => 'diana@gmail.com',
                'status' => 1,
                'incident_type' => 1,
                'reply' => 'Technician assigned for inspection.',
                'image_path' => null,
                'created_by' => 9,
                'updated_by' => null,
                'deleted_by' => null,
                'created_at' => $now->copy()->subDays(8),
                'updated_at' => $now->copy()->subDays(8),
                'incident_closed_date' => null,
            ],
        ];

        foreach ($incidences as $data) {
            $imagePath = $data['image_path'];
            unset($data['image_path']);

            $incidence = Incidence::create($data);

            if ($imagePath) {
                $this->attachIncidentImage($incidence, $imagePath);
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    private function attachIncidentImage($model, $publicPath)
    {
        if (!file_exists($publicPath)) return false;

        $file = new \Illuminate\Http\File($publicPath);

        return $model->addMedia($file)
            ->preservingOriginal()
            ->toMediaCollection('file_url');
    }
}
