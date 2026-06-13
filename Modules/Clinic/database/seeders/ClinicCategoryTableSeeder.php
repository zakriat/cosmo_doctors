<?php

namespace Modules\Clinic\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Clinic\Models\Clinics;
use Illuminate\Support\Arr;
use Modules\Clinic\Models\ClinicsCategory;
class ClinicCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (env('IS_DUMMY_DATA')){
            $data = [
                [
                    'name' =>'Cardiology',
                    'slug' =>'cardiology',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/cardiology.png'),
                ],
                [
                    'name' =>'Electrophysiology',
                    'slug' =>'electrophysiology',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/electrophysiology.png'),
                ],
                [
                    'name' =>'Heart Monitoring',
                    'slug' =>'heart-monitoring',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/heart_monitoring.png'),
                ],
                [
                    'name' =>'Coronary Angiography',
                    'slug' =>'coronary-angiography',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/coronary_angiography.png'),
                ],
                [
                    'name' =>'Genetic Cardiology Services',
                    'slug' =>'genetic-cardiology-services',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/genetic_cardiology_services.png'),
                ],
                [
                    'name' =>'Orthotics and Prosthetics',
                    'slug' =>'orthotics-and-prosthetics',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/orthotics_and_prosthetics.png'),
                ],
                [
                    'name' =>'Joint Replacement',
                    'slug' =>'joint-replacement',
                    'featured'=>0,
                    'status' =>1,
                    'file_url' => public_path('/dummy-images/clinic/category/joint_replacement.png'),
                ],
                [
                    'name' =>'Spine Care',
                    'slug' =>'spine-care',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/spine_care.png'),
                ],
                [
                    'name' =>'Osteoporosis',
                    'slug' =>'osteoporosis',
                    'featured'=>0,
                    'status' =>1,
                    'file_url' => public_path('/dummy-images/clinic/category/osteoporosis.png'),
                ],
                [
                    'name' =>'Minimally Invasive Surgery',
                    'slug' =>'minimally-invasive-surgery',
                    'status' =>1,
                    'featured'=>0,
                    'file_url' => public_path('/dummy-images/clinic/category/minimally_invasive_surgery.png'),
                ],
                //
                [
                    'name' =>'Primary Care',
                    'slug' =>'primary-care',
                    'status' =>1,
                    'featured'=>0,
                    'file_url' => public_path('/dummy-images/clinic/category/primary_care.png'),
                ],
                [
                    'name' =>'Integrative Health',
                    'slug' =>'integrative-health',
                    'featured'=>0,
                    'status' =>1,
                    'file_url' => public_path('/dummy-images/clinic/category/integrative_health.png'),
                ],
                [
                    'name' =>'Pediatrics',
                    'slug' =>'pediatrics',
                    'featured'=>0,
                    'status' =>1,
                    'file_url' => public_path('/dummy-images/clinic/category/pediatrics.png'),
                ],
                [
                    'name' =>'General Dentistry',
                    'slug' =>'general-dentistry',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/general_dentistry.png'),
                ],
                [
                    'name' =>'Oral Surgery',
                    'slug' =>'oral-surgery',
                    'status' =>1,
                    'featured'=>0,
                    'file_url' => public_path('/dummy-images/clinic/category/oral_surgery.png'),
                ],
                [
                    'name' =>'Cosmetic Dermatology',
                    'slug' =>'cosmetic-dermatology',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/cosmetic_dermatology.png'),
                ],
                [
                    'name' =>'Mohs Surgery',
                    'slug' =>'mohs-surgery',
                    'status' =>1,
                    'featured'=>0,
                    'file_url' => public_path('/dummy-images/clinic/category/mohs_surgery.png'),
                ],
                [
                    'name' =>'Ophthalmology',
                    'slug' =>'ophthalmology',
                    'status' =>1,
                    'featured'=>0,
                    'file_url' => public_path('/dummy-images/clinic/category/ophthalmology.png'),
                ],
                [
                    'name' =>'Optometry',
                    'slug' =>'optometry',
                    'status' =>1,
                    'featured'=>1,
                    'file_url' => public_path('/dummy-images/clinic/category/optometry.png'),
                ],
            ];
            foreach ($data as $key => $val) {
                $featureImage = $val['file_url'] ?? null;
                $clinicCategory = Arr::except($val, [ 'file_url']);
                $clinicData = ClinicsCategory::create($clinicCategory);
                
                if (isset($featureImage)) {
                    $this->attachFeatureImage($clinicData, $featureImage);
                }

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
