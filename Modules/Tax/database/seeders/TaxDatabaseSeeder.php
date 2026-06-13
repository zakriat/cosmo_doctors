<?php

namespace Modules\Tax\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Tax\Models\Tax;

class TaxDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $taxes = [
            [
                'title' => 'Service Tax',
                'type' => 'fixed',
                'module_type'=>'services',
                'value' => 22,
                'tax_type' => 'exclusive',

            ],
            [
                'title' => 'GST',
                'type' => 'percent',
                'module_type'=>'services',
                'value' => 28,
                'tax_type' => 'exclusive',

            ],

            [
                'title' => 'VAT',
                'type' => 'percent',
                'value' => 10,
                'module_type'=>'services',
                'tax_type' => 'inclusive',

            ],
        ];
        if (env('IS_DUMMY_DATA')) {
            foreach ($taxes as $key => $taxes_data) {
                $tax = Tax::create($taxes_data);
            }
        }

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
