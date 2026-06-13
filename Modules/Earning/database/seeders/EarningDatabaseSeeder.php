<?php

namespace Modules\Earning\database\seeders;

use Illuminate\Database\Seeder;


class EarningDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(CommissionEarningsTableSeeder::class);
    }
}
