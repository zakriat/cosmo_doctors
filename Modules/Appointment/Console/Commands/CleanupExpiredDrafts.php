<?php

namespace Modules\Appointment\Console\Commands;

use Illuminate\Console\Command;
use Modules\Appointment\Models\DraftAppointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupExpiredDrafts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drafts:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete draft appointments older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired draft appointments...');

        try {
            // Get expired drafts
            $expiredDrafts = DraftAppointment::expired()->get();
            $count = $expiredDrafts->count();

            if ($count === 0) {
                $this->info('No expired drafts found.');
                Log::info('Draft cleanup completed: No expired drafts found');
                return 0;
            }

            // Delete expired drafts
            DraftAppointment::expired()->delete();

            $this->info("Successfully deleted {$count} expired draft(s).");
            Log::info("Draft cleanup completed: {$count} expired drafts deleted");

            return 0;

        } catch (\Exception $e) {
            $this->error('Error during cleanup: ' . $e->getMessage());
            Log::error('Draft cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }
}
