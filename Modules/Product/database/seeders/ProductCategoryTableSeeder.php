<?php

namespace Modules\Product\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Product\Models\ProductCategory;

class ProductCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Haircare Products',
                'file_url' => public_path('/dummy-images/category-images/haircare_products.png'),
            ],
            [
                'name' => 'Skincare Products',
                'file_url' => public_path('/dummy-images/category-images/skincare_products.png'),
            ],
            [
                'name' => 'Nail Care Products',
                'file_url' => public_path('/dummy-images/category-images/nail_care_products.png'),
            ],
            [
                'name' => 'Beauty and Makeup Products',
                'file_url' => public_path('/dummy-images/category-images/beauty_and_makeup_products.png'),
            ],
            [
                'name' => 'Personal Care Products',
                'file_url' => public_path('/dummy-images/category-images/personal_care_products.png'),
            ],
            [
                'name' => 'Salon Equipment and Tools',
                'file_url' => public_path('/dummy-images/category-images/salon_equipment_and_tools.png'),
            ],
            [
                'name' => 'Accessories and Fashion Items',
                'file_url' => public_path('/dummy-images/category-images/accessories_and_fashion_items.png'),
            ],
            [
                'name' => 'Home Spa and Self-Care Kits',
                'file_url' => public_path('/dummy-images/category-images/home_spa_and_self_care_kits.png'),
            ],
            [
                'name' => 'Gift Sets and Bundles',
                'file_url' => public_path('/dummy-images/category-images/gift_sets_and_bundles.png'),
            ],

        ];
        if (env('IS_DUMMY_DATA')) {
            foreach ($data as $key => $value) {
                $featureimage = $value['file_url'] ?? null;
                $product_category = Arr::except($value, ['file_url']);
                $product_category = [
                    'name' => $value['name'],
                ];
                $product_category = ProductCategory::create($product_category);
                if (isset($featureimage)) {
                    $this->attachFeatureImage($product_category, $featureimage);
                }

                $product_category->brands()->sync([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17]);
            }
        }
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
