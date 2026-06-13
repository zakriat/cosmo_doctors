<?php

namespace Modules\CustomForm\Console\Commands;

use Illuminate\Console\Command;

class CustomFormCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CustomFormCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CustomForm Command description';

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
