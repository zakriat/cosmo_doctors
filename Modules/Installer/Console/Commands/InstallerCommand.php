<?php

namespace Modules\Installer\Console\Commands;

use Illuminate\Console\Command;

class InstallerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:InstallerCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installer Command description';

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
