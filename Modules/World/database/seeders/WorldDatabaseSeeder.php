<?php

namespace Modules\World\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\MenuBuilder\Models\MenuBuilder;

class WorldDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(StatesTableSeeder::class);

     
    }

  
}
