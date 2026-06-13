<?php

namespace Modules\Product\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Product\Models\Brands;

class BrandsTableSeeder extends Seeder
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
                'name' => 'Lotuus',
                'file_url' => public_path('/dummy-images/Brand/lotuus.png'),
            ],
            [
                'name' => 'Bela Veeta',
                'file_url' => public_path('/dummy-images/Brand/bela_veeta.png'),
            ],
            [
                'name' => 'Goodrej',
                'file_url' => public_path('/dummy-images/Brand/goodrej.png'),
            ],
            [
                'name' => 'mCaaffeine',
                'file_url' => public_path('/dummy-images/Brand/mCaaffeine.png'),
            ],
            [
                'name' => 'Dott & Keey',
                'file_url' => public_path('/dummy-images/Brand/dott_keey.png'),
            ],
            [
                'name' => 'Nykaaa',
                'file_url' => public_path('/dummy-images/Brand/nykaaa.png'),
            ],
            [
                'name' => 'Pluum',
                'file_url' => public_path('/dummy-images/Brand/pluum.png'),
            ],
            [
                'name' => 'Sweess Beauty',
                'file_url' => public_path('/dummy-images/Brand/sweess_beauty.png'),
            ],
            [
                'name' => 'Lekme',
                'file_url' => public_path('/dummy-images/Brand/lekme.png'),
            ],
            [
                'name' => 'Feces Caanada',
                'file_url' => public_path('/dummy-images/Brand/feces_caanada.png'),
            ],
            [
                'name' => 'MyyGlamm',
                'file_url' => public_path('/dummy-images/Brand/myy_glamm.png'),
            ],
            [
                'name' => 'Majjestique',
                'file_url' => public_path('/dummy-images/Brand/majjestique.png'),
            ],
            [
                'name' => 'Feeama',
                'file_url' => public_path('/dummy-images/Brand/feeama.png'),
            ],
            [
                'name' => 'Adiddas',
                'file_url' => public_path('/dummy-images/Brand/adiddas.png'),
            ],
            [
                'name' => 'Ikoniq',
                'file_url' => public_path('/dummy-images/Brand/ikoniq.png'),
            ],
            [
                'name' => 'Havellss',
                'file_url' => public_path('/dummy-images/Brand/havellss.png'),
            ],
            [
                'name' => 'Pheelips',
                'file_url' => public_path('/dummy-images/Brand/pheelips.png'),
            ],
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($data as $key => $value) {
                $featureImage = $value['file_url'] ?? null;
                $branddata = Arr::except($value, ['file_url']);
                $brand = Brands::create($branddata);
                if (isset($featureImage)) {
                    $this->attachFeatureImage($brand, $featureImage);
                }
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
