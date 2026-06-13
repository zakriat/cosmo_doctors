<?php

namespace Modules\RequestService\Console\Commands;

use Illuminate\Console\Command;

class RequestServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:RequestServiceCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RequestService Command description';

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
