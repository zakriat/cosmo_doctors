<?php
namespace Modules\Appointment\database\seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Appointment\Models\AppointmentPatientBodychart;

class AppointmentPatientBodychartTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('appointment_patient_bodychart')->delete();
        
        $data = (array (
            0 => 
            array (
                'id' => 1,
            'name' => 'Gout (Inflammatory Arthritis)',
                'description' => 'Gout is a form of inflammatory arthritis characterized by sudden, severe attacks of pain, redness, and swelling in the joints, often the big toe.',
                'encounter_id' => 3,
                'appointment_id' => 2,
                'patient_id' => 10,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-31 11:09:38',
                'updated_at' => '2024-05-31 11:09:38',
                'deleted_at' => NULL,
                'file_url' => public_path('/dummy-images/body-chart/body_chart_4.png'),
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Achilles Tendon',
                'description' => 'The Achilles tendon connects the calf muscles to the heel bone, enabling walking, running, and jumping by facilitating foot movement. It is prone to injuries such as tendinitis and ruptures.',
                'encounter_id' => 3,
                'appointment_id' => 2,
                'patient_id' => 10,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-31 11:10:24',
                'updated_at' => '2024-05-31 11:10:24',
                'deleted_at' => NULL,
                'file_url' => public_path('/dummy-images/body-chart/body_chart_7.png'),
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Human Foot Bones',
                'description' => 'The human foot contains 26 bones, including the tarsals, metatarsals, and phalanges, providing structure and support for standing, walking, and balancing.',
                'encounter_id' => 8,
                'appointment_id' => 5,
                'patient_id' => 13,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-31 11:17:58',
                'updated_at' => '2024-05-31 11:17:58',
                'deleted_at' => NULL,
                'file_url' => public_path('/dummy-images/body-chart/body_chart_8.png'),
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Hand Bone',
                'description' => 'The human hand consists of 27 bones, including the carpals, metacarpals, and phalanges, enabling a wide range of movements and dexterity for gripping and manipulating objects.',
                'encounter_id' => 8,
                'appointment_id' => 5,
                'patient_id' => 13,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-31 11:19:43',
                'updated_at' => '2024-05-31 11:19:43',
                'deleted_at' => NULL,
                'file_url' => public_path('/dummy-images/body-chart/body_chart_12.png'),
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Muscular System',
                'description' => 'The muscular system comprises over 600 muscles responsible for movement, posture, and vital functions like circulation and digestion, working through contraction and relaxation.',
                'encounter_id' => 7,
                'appointment_id' => 4,
                'patient_id' => 12,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-31 11:22:28',
                'updated_at' => '2024-05-31 11:22:28',
                'deleted_at' => NULL,
                'file_url' => public_path('/dummy-images/body-chart/body_chart_13.png'),
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Human Nervous System',
                'description' => 'The human nervous system consists of the central nervous system and the peripheral nervous system, coordinating body functions, responses, and sensations through a complex network of nerves.',
                'encounter_id' => 7,
                'appointment_id' => 4,
                'patient_id' => 12,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-31 11:24:39',
                'updated_at' => '2024-05-31 11:24:39',
                'deleted_at' => NULL,
                'file_url' => public_path('/dummy-images/body-chart/body_chart_18.png'),
            ),
        ));
        
        
        foreach ($data as $bodychart) {
            $featureImage = $bodychart['file_url'] ?? null;
            $bodychart = Arr::except($bodychart, [ 'file_url']);
            // $bodychartdata = \DB::table('appointment_patient_bodychart')->insert($bodychart);
            $bodychartdata =  AppointmentPatientBodychart::create($bodychart);
            if (isset($featureImage)) {
                $this->attachFeatureImage($bodychartdata, $featureImage);
            }
          
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');  
    }
    private function attachFeatureImage($model, $publicPath)
    {
        if(!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('file_url');

        return $media;

    }
}