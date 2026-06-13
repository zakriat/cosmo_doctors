<?php

namespace Modules\MultiVendor\Console\Commands;

use Illuminate\Console\Command;

class MultiVendorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:MultiVendorCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MultiVendor Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
