<?php

namespace Modules\Product\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\MenuBuilder\Models\MenuBuilder;

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      

        $this->call(ProductCategoryTableSeeder::class);
        $this->call(BrandsTableSeeder::class);
        $this->call(UnitsTableSeeder::class);
        $this->call(VariationsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(ProductCategoryMappingsTableSeeder::class);
        $this->call(ProductTagsTableSeeder::class);
        $this->call(ProductVariationsTableSeeder::class);
        $this->call(ProductVariationCombinationsTableSeeder::class);
        $this->call(ProductVariationStocksTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(CartTableSeeder::class);
        $this->call(OrderTableSeeder::class);
    }

   
}
