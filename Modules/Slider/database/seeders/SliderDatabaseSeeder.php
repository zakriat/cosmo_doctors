<?php

namespace Modules\Slider\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Service\Models\SystemServiceCategory;
use Modules\Slider\Models\Slider;

class SliderDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        $categoryIds = SystemServiceCategory::whereNull('parent_id')->pluck('id')->toArray();

        $sliders = [
            [
                'name' => 'Banner1',
                'type' => 'category',
                'link_id' => 1,
                'file_url' => public_path('/dummy-images/sliders/Offer.png'),
            ],
            [
                'name' => 'Banner2',
                'type' => 'category',
                'link_id' => 1,
                'file_url' => public_path('/dummy-images/sliders/Offer1.png'),
            ],
            [
                'name' => 'Banner3',
                'type' => 'category',
                'link_id' => 1,
                'file_url' => public_path('/dummy-images/sliders/Offer2.png'),
            ],
            [
                'name' => 'Banner4',
                'type' => 'category',
                'link_id' => 4,
                'file_url' => public_path('/dummy-images/sliders/Offer3.png'),
            ],
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($sliders as $key => $sliders_data) {
                $featureImage = $sliders_data['file_url'] ?? null;
                $slidersData = Arr::except($sliders_data, ['file_url']);
                $slider = Slider::create($slidersData);

                $this->attachFeatureImage($slider, $featureImage);
            }
        }

        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if (! env('IS_DUMMY_DATA_IMAGE')) {
            return false;
        }

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('file_url');

        return $media;
    }
}
