<?php

namespace Modules\Service\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Service\Models\SystemServiceCategory;

class SystemServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (env('IS_DUMMY_DATA')) {
            $data = [

                [
                    'slug' => 'specialist-clinics',
                    'name' => 'Specialist Clinics',
                    'status' => 1,
                    'file_url' => public_path('/dummy-images/system_category/clinic/general_medicine.png'),
                   
                ],
                [
                    'slug' => 'general-medicine',
                    'name' => 'General Medicine',
                    'status' => 1,
                    'file_url' => public_path('/dummy-images/system_category/clinic/dental_care.png'),
                   
                ],
                [
                    'slug' => 'dental-care',
                    'name' => 'Dental Care',
                    'status' => 1,
                    'file_url' => public_path('/dummy-images/system_category/clinic/specialist_clinics.png'),
                   
                ],
                [
                    'slug' => 'others',
                    'name' => 'Others',
                    'status' => 1,
                    'file_url' => public_path('/dummy-images/system_category/clinic/others.jpg'),
                   
                ]



            ];
            foreach ($data as $key => $val) {
                $featureImage = $val['file_url'] ?? null;
                $categoryData = Arr::except($val, [ 'file_url']);
                $category = SystemServiceCategory::create($categoryData);
                if (isset($featureImage)) {
                    $this->attachFeatureImage($category, $featureImage);
                }
                if(!empty($subCategorys)){
                    foreach ($subCategorys as $subKey => $subCategory) {
                        $subCategory['parent_id'] = $category->id;
                        $featureImage = $subCategory['file_url'] ?? null;
                        $sub_categoryData = Arr::except($subCategory, ['file_url']);
                        $subcategoryData = SystemServiceCategory::create($sub_categoryData);
                        if (isset($featureImage)) {
                            $this->attachFeatureImage($subcategoryData, $featureImage);
                        }
                    }
                }

            }
        }
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if(!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('file_url');

        return $media;

    }

}
