<?php

namespace Modules\Triage\database\seeders;

use Illuminate\Database\Seeder;

class TriageDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TriageCategorySeeder::class);
    }
}
