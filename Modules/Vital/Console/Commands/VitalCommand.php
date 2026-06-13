<?php

namespace Modules\Vital\Console\Commands;

use Illuminate\Console\Command;

class VitalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:VitalCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vital Command description';

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
