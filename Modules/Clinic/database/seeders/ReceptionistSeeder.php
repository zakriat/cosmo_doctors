<?php

namespace Modules\Clinic\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Clinic\Models\Receptionist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Clinic\Models\Clinics;

class ReceptionistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $avatarPath = config('app.avatar_base_path');
        $receptionist = [
            [
                'first_name' => 'Harvey',
                'last_name' => 'Francis',
                'email' => 'receptionist@kivicare.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+1 8874547968',
                'date_of_birth' => fake()->date,
                'file_url' => public_path('/dummy-images/profile/receptionist/harvey.png'),
                'avatar' => $avatarPath.'male.webp',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'receptionist',
                'address' => '6 Heart Street, Cityville, USA',
                'country' => 230,
                'state' => 3812,
                'city' => 41432,
                'pincode' => '12345',
                'latitude'=>'40.7128',
                'longitude'=>'74.006',
                'vendor_id' => 1,
                "clinic" => 'heartcare-orthocare-center',
                
            ],
            [
                'first_name' => 'Angela',
                'last_name' => 'Perez',
                'email' => 'angela@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+1 3652547855',
                'date_of_birth' => fake()->date,
                'file_url' => public_path('/dummy-images/profile/receptionist/angela.png'),
                'avatar' => $avatarPath.'male.webp',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'receptionist',
                'address' => '6 Heart Street, Cityville, USA',
                'country' => 230,
                'state' => 3812,
                'city' => 41432,
                'pincode' => '12345',
                'latitude'=>'40.7128',
                'longitude'=>'74.006',
                'vendor_id' => 1,
                "clinic" => 'harmony-medical-center',
                
            ],
            [
                'first_name' => 'Amy',
                'last_name' => 'Ellis',
                'email' => 'amy@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => '+1 4785478627',
                'date_of_birth' => fake()->date,
                'file_url' => public_path('/dummy-images/profile/receptionist/amy.png'),
                'avatar' => $avatarPath.'male.webp',
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_type' => 'receptionist',
                'address' => '6 Heart Street, Cityville, USA',
                'country' => 230,
                'state' => 3812,
                'city' => 41432,
                'pincode' => '12345',
                'latitude'=>'57.653484',
                'longitude'=>'-3.314624',
                'vendor_id' => 1,
                "clinic" => 'wellness-dental-clinic',
                
            ],
           

        ];

        if (env('IS_DUMMY_DATA')) {
           
            foreach ($receptionist  as $key => $employee_data) {

                $image = $employee_data['file_url'] ?? null;
                $empData = Arr::except($employee_data, [ 'file_url','vendor_id','clinic']);
                $emp = User::create($empData);
                $emp->assignRole($emp->user_type);

                if (isset($image)) {
                    $this->attachFeatureImage($emp, $image);
                }

                if(!empty($employee_data['clinic'])){

                       $clinicData = Clinics::where('slug',$employee_data['clinic'])->first();
                     
                        $receptionist_clinic = [
                            'receptionist_id' => $emp->id,
                            'clinic_id' => $clinicData->id,
                            'vendor_id' => $employee_data['vendor_id'],
                        ];

                        Receptionist::create($receptionist_clinic);

                    
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
