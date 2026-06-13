<?php

namespace Modules\Commission\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Commission\Models\Commission;

class CommissionDatabaseSeeder extends Seeder
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

        /*
         * Commissions Seed
         * ------------------
         */

        // DB::table('commissions')->truncate();
        // echo "Truncate: commissions \n";
        if (env('IS_DUMMY_DATA')) {
            $data = [
                [
                    
                    'title' => 'Admin Fees',
                    'commission_type' => 'fixed',
                    'commission_value' => 3,
                    'status' => 1,
                    'type' => 'admin_fees',
                ],
                // [
                   
                //     'title' => 'Product Commission',
                //     'commission_type' => 'percentage',
                //     'commission_value' => 1,
                //     'status' => 1,
                //     'type' => 'product_commission',
                // ],
                [
                    
                    'title' => 'Doctor Commission',
                    'commission_type' => 'percentage',
                    'commission_value' => 40,
                    'status' => 1,
                    'type' => 'doctor_commission',
                ]
            ];

            foreach ($data as $key => $value) {
                Commission::create($value);
            }
        }

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
