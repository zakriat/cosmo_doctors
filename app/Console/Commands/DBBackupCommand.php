<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DBBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database backup.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $outputDb = Artisan::call('backup:run --only-db');
            Log::info('database backup output: ' . $outputDb);
            echo 'database backup done';
        } catch (\Exception $e) {
            Log::error('backup creation failed: ' . $e->getMessage());
        }
        
    }
   
}
