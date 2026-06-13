<?php

namespace Modules\Appointment\database\seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class AppointmentPatientRecordsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('appointment_patient_records')->delete();
        
        \DB::table('appointment_patient_records')->insert(array (
            0 => 
            array (
                'id' => 1,
                'subjective' => Crypt::decrypt('sksdiahdausdyajdh'),
                'objective' => Crypt::decrypt('sksdiahdausdyajdh'),
                'assessment' => Crypt::decrypt('sksdiahdausdyajdh'),
                'plan' => Crypt::decrypt('sksdiahdausdyajdh'),
                'appointment_id' => 1,
                'patient_id' => 3,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-03-31 22:07:24',
                'updated_at' => '2024-03-31 22:07:24',
            ),
            1 => 
            array (
                'id' => 2,
                'subjective' => Crypt::decrypt('adad'),
                'objective' => Crypt::decrypt('asdasdas'),
                'assessment' => Crypt::decrypt('dasdad'),
                'plan' => Crypt::decrypt('asdasda'),
                'appointment_id' => 2,
                'patient_id' => 4,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-03-31 22:07:35',
                'updated_at' => '2024-03-31 22:07:35',
            ),
            2 => 
            array (
                'id' => 3,
                'subjective' => Crypt::decrypt('sadas'),
                'objective' => Crypt::decrypt('dasdasd'),
                'assessment' => Crypt::decrypt('asdad'),
                'plan' => Crypt::decrypt('adada'),
                'appointment_id' => 3,
                'patient_id' => 6,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-03-31 22:07:43',
                'updated_at' => '2024-03-31 22:07:43',
            ),
            3 => 
            array (
                'id' => 4,
                'subjective'=> Crypt::decrypt('dad'),
                'objective' => Crypt::decrypt('adad'),
                'assessment' => Crypt::decrypt('asdasd'),
                'plan' => Crypt::decrypt('asdasdas'),
                'appointment_id' => 4,
                'patient_id' => 5,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-03-31 22:07:53',
                'updated_at' => '2024-03-31 22:07:53',
            ),
            4 => 
            array (
                'id' => 5,
                'subjective' => Crypt::decrypt('dfff'),
                'objective' => Crypt::decrypt('fffff'),
                'assessment' => Crypt::decrypt('fff'),
                'plan' => Crypt::decrypt('fffff'),
                'appointment_id' => 5,
                'patient_id' => 6,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2024-03-31 22:08:02',
                'updated_at' => '2024-03-31 22:08:02',
            ),
        ));
        
        
    }
}