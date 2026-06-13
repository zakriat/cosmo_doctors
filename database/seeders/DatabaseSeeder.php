<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        Schema::disableForeignKeyConstraints();
        $file = new Filesystem;
        $file->cleanDirectory('storage/app/public');
        $this->call(AuthTableSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(BodychartSettingSeeder::class);
        $this->call(IncidenceSeeder::class);
        $this->call(\Modules\Tax\database\seeders\TaxDatabaseSeeder::class);
        $this->call(\Modules\Constant\database\seeders\ConstantDatabaseSeeder::class);
        $this->call(\Modules\Service\database\seeders\SystemServiceCategorySeeder::class);
        $this->call(\Modules\Clinic\database\seeders\ClinicDatabaseSeeder::class);

        $this->call(\Modules\Commission\database\seeders\CommissionDatabaseSeeder::class);
        $this->call(\Modules\Currency\database\seeders\CurrencyDatabaseSeeder::class);
        $this->call(\Modules\NotificationTemplate\database\seeders\NotificationTemplateSeeder::class);
        $this->call(\Modules\Slider\database\seeders\SliderDatabaseSeeder::class);
        $this->call(\Modules\Page\database\seeders\PageDatabaseSeeder::class);
        $this->call(\Modules\Tag\database\seeders\TagDatabaseSeeder::class);
        $this->call(\Modules\World\database\seeders\WorldDatabaseSeeder::class);
        $this->call(\Modules\Logistic\database\seeders\LogisticZoneTableSeeder::class);
        $this->call(\Modules\Location\database\seeders\LocationDatabaseSeeder::class);
        $this->call(\Modules\Product\database\seeders\ProductDatabaseSeeder::class);
        $this->call(\Modules\Logistic\database\seeders\LogisticDatabaseSeeder::class);
        // $this->call(\Modules\Service\database\seeders\SystemServiceSeeder::class);
        $this->call(\Modules\Appointment\database\seeders\AppointmentDatabaseSeeder::class);
        $this->call(\Modules\Earning\database\seeders\EarningDatabaseSeeder::class);
        $this->call(\Modules\Wallet\database\seeders\WalletDatabaseSeeder::class);
        $this->call(\Modules\FAQ\database\seeders\FAQDatabaseSeeder::class);
        $this->call(\Modules\FrontendSetting\database\seeders\FrontendSettingDatabaseSeeder::class);


        Schema::enableForeignKeyConstraints();

        \Artisan::call('cache:clear');
        \Artisan::call('storage:link');
    }
}
