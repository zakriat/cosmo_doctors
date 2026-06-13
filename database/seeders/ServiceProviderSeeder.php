<?php

namespace Database\Seeders;

use App\Models\Address;
use Modules\ServiceProvider\Models\ServiceProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\ClinicHour\Models\ClinicHour;

class ServiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('IS_DUMMY_DATA')) {
            $service_providers = [];

            foreach ($service_providers as $serviceProvider) {
                $address = $serviceProvider['address'];
                $featureImage = $serviceProvider['file_url'] ?? null;
                $serviceProviderData = Arr::except($serviceProvider, ['file_url', 'address']);
                $br = ServiceProvider::create($serviceProviderData);
                $this->attachFeatureImage($br, $featureImage);
                $br->address()->save(new Address($address));
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
