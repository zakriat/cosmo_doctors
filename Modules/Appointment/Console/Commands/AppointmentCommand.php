<?php

namespace Modules\Appointment\Console\Commands;

use Illuminate\Console\Command;

class AppointmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AppointmentCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Appointment Command description';

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
